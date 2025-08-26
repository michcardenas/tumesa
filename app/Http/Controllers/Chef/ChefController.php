<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cena;

class ChefController extends Controller
{
   
  public function dashboard() // ✅ Cambié el nombre del método
{
    $user = Auth::user();
    
    // 🔍 LOG: Debug del acceso al dashboard
    \Log::info('=== ACCESO AL DASHBOARD DEL CHEF ===');
    \Log::info('Usuario accediendo:', [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role_campo' => $user->role,
    ]);

    // Verificar que el usuario tenga el rol correcto
    // Verificar tanto el campo role como los roles de Spatie
    $hasChefRole = false;
    $roleSource = '';
    
    // Verificar campo role de la tabla users
    if ($user->role === 'chef_anfitrion') {
        $hasChefRole = true;
        $roleSource = 'campo users.role';
        \Log::info('✅ Acceso autorizado via campo users.role: chef_anfitrion');
    } 
    // Verificar roles de Spatie como fallback
    elseif (method_exists($user, 'hasRole')) {
        try {
            $spatieRoles = $user->getRoleNames()->toArray();
            \Log::info('Roles de Spatie en dashboard:', $spatieRoles);
            
            if ($user->hasRole('chef') || $user->hasRole('chef_anfitrion')) {
                $hasChefRole = true;
                $roleSource = 'roles Spatie';
                \Log::info('✅ Acceso autorizado via Spatie: ' . implode(', ', $spatieRoles));
            } else {
                \Log::warning('❌ Usuario no tiene roles de chef en Spatie: ' . implode(', ', $spatieRoles));
            }
        } catch (\Exception $e) {
            \Log::error('Error verificando roles de Spatie: ' . $e->getMessage());
        }
    } else {
        \Log::warning('❌ Método getRoleNames no existe y campo role es: ' . ($user->role ?? 'null'));
    }
    
    // Denegar acceso si no tiene rol correcto
    if (!$hasChefRole) {
        \Log::warning('🚫 ACCESO DENEGADO - Usuario sin permisos de chef');
        \Log::warning('Campo role: ' . ($user->role ?? 'null'));
        
        abort(403, 'No tienes acceso a esta sección. Contacta al administrador.');
    }

    \Log::info('🎉 Acceso autorizado al dashboard del chef via: ' . $roleSource);

    // Obtener cenas del chef actual
    $userId = $user->id;
    $currentMonth = now()->startOfMonth();
    $nextMonth = now()->addMonth()->startOfMonth();

    // Estadísticas
    $stats = [
        'cenas_mes' => Cena::where('user_id', $userId)
            ->whereBetween('datetime', [$currentMonth, $nextMonth])
            ->count(),
        
        'comensales_totales' => Cena::where('user_id', $userId)
            ->where('datetime', '>=', $currentMonth)
            ->sum('guests_current'),
            
        'cenas_pendientes' => Cena::where('user_id', $userId)
            ->where('datetime', '>', now())
            ->where('status', 'published')
            ->count(),
            
        'ingresos_mes' => Cena::where('user_id', $userId)
            ->whereBetween('datetime', [$currentMonth, $nextMonth])
            ->where('status', 'completed')
            ->sum(\DB::raw('price * guests_current'))
    ];

    // Próximas cenas
    $proximas_cenas = Cena::where('user_id', $userId)
        ->where('datetime', '>', now())
        ->orderBy('datetime', 'asc')
        ->take(10)
        ->get()
        ->map(function($cena) {
            return [
                'id' => $cena->id,
                'fecha_formatted' => $cena->datetime->format('d/m/Y'),
                'titulo' => $cena->title,
                'comensales_actuales' => $cena->guests_current,
                'comensales_max' => $cena->guests_max,
                'precio' => $cena->price,
                'estado' => $cena->status,
                'location' => $cena->location
            ];
        });

    // Información de ingresos
    $ingresos = [
        'mes' => Cena::where('user_id', $userId)
            ->whereBetween('datetime', [$currentMonth, $nextMonth])
            ->where('status', 'completed')
            ->sum(\DB::raw('price * guests_current')),
            
        'pendientes' => Cena::where('user_id', $userId)
            ->where('datetime', '>', now())
            ->where('status', 'published')
            ->sum(\DB::raw('price * guests_max')),
            
        'total' => Cena::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum(\DB::raw('price * guests_current')),
            
        'cenas_mes' => $stats['cenas_mes'],
        'cenas_pendientes' => $stats['cenas_pendientes'],
        'crecimiento' => 15 // Puedes calcular esto comparando con el mes anterior
    ];

    $data = [
        'user' => $user,
        'welcome_message' => '¡Bienvenido a tu Dashboard de Chef!',
        'stats' => $stats,
        'proximas_cenas' => $proximas_cenas,
        'ingresos' => $ingresos
    ];

    return view('chef.dashboard', $data);
}

   public function storeDinner(Request $request)
{
    // Validación de datos
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'datetime' => 'required|date|after:now',
        'guests' => 'required|integer|min:1|max:50',
        'price' => 'required|numeric|min:0',
        'menu' => 'required|string|max:2000',
        'location' => 'required|string|max:500',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
        'gallery_images' => 'nullable|array|max:5',
        'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
    ]);

    try {
        // Procesar imagen de portada
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('cenas/covers', 'public');
        }

        // Procesar imágenes de galería
        $galleryImagePaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                $path = $image->store('cenas/gallery', 'public');
                $galleryImagePaths[] = $path;
            }
        }

        // Crear la cena
        $cena = Cena::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'datetime' => $validated['datetime'],
            'guests_max' => $validated['guests'],
            'guests_current' => 0,
            'price' => $validated['price'],
            'menu' => $validated['menu'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'cover_image' => $coverImagePath,
            'gallery_images' => empty($galleryImagePaths) ? null : $galleryImagePaths,
            'status' => 'published',
            'is_active' => true
        ]);

        // Respuesta de éxito
        return response()->json([
            'success' => true,
            'message' => 'Cena creada exitosamente',
            'cena' => [
                'id' => $cena->id,
                'title' => $cena->title,
                'formatted_date' => $cena->formatted_date,
                'formatted_price' => $cena->formatted_price,
                'location' => $cena->location
            ]
        ], 201);

    } catch (\Exception $e) {
        // Log del error
        \Log::error('Error creando cena: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'request_data' => $request->except(['cover_image', 'gallery_images'])
        ]);

        // Respuesta de error
        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor. Inténtalo de nuevo.'
        ], 500);
    }
}

public function showDinner(Cena $cena)
{
    // Verificar que la cena pertenezca al chef autenticado
    if ($cena->user_id !== auth()->id()) {
        abort(403, 'No tienes permisos para ver esta cena.');
    }

    // Calcular información adicional
    $cenaData = [
        'id' => $cena->id,
        'title' => $cena->title,
        'datetime' => $cena->datetime,
        'formatted_date' => $cena->datetime->format('l, j \d\e F \d\e Y'),
        'formatted_time' => $cena->datetime->format('H:i'),
        'formatted_datetime' => $cena->datetime->format('d/m/Y H:i'),
        'guests_max' => $cena->guests_max,
        'guests_current' => $cena->guests_current,
        'available_spots' => $cena->available_spots,
        'is_full' => $cena->is_full,
        'price' => $cena->price,
        'formatted_price' => $cena->formatted_price,
        'total_revenue_potential' => $cena->price * $cena->guests_max,
        'current_revenue' => $cena->price * $cena->guests_current,
        'menu' => $cena->menu,
        'location' => $cena->location,
        'latitude' => $cena->latitude,
        'longitude' => $cena->longitude,
        'status' => $cena->status,
        'status_label' => $this->getStatusLabel($cena->status),
        'status_color' => $this->getStatusColor($cena->status),
        'is_active' => $cena->is_active,
        'cover_image_url' => $cena->cover_image_url,
        'gallery_image_urls' => $cena->gallery_image_urls,
        'created_at' => $cena->created_at,
        'updated_at' => $cena->updated_at,
        'days_until' => now()->diffInDays($cena->datetime, false),
        'is_past' => $cena->datetime->isPast(),
        'can_edit' => !$cena->datetime->isPast() && $cena->status !== 'cancelled'
    ];

    return view('chef.dinners.show', compact('cenaData', 'cena'));
}

// Métodos auxiliares para el estado
private function getStatusLabel($status)
{
    return match($status) {
        'draft' => 'Borrador',
        'published' => 'Publicada',
        'cancelled' => 'Cancelada',
        'completed' => 'Completada',
        default => ucfirst($status)
    };
}

private function getStatusColor($status)
{
    return match($status) {
        'draft' => 'warning',
        'published' => 'success',
        'cancelled' => 'danger',
        'completed' => 'primary',
        default => 'secondary'
    };
}
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
