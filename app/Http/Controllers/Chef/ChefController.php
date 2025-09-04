<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cena;
use App\Models\Reserva;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;




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
// En tu método dashboard(), actualiza el map de proximas_cenas:
$proximas_cenas = Cena::where('user_id', $userId)
    ->where('datetime', '>', now()->subHours(24)) // Mostrar cenas de las últimas 24 horas también
    ->orderBy('datetime', 'asc')
    ->take(15) // Aumentar el límite
    ->get()
    ->map(function($cena) {
        $ahora = now();
        $esPasada = $cena->datetime < $ahora;
        $minutosParaCena = $ahora->diffInMinutes($cena->datetime, false);
        
        return [
            'id' => $cena->id,
            'datetime' => $cena->datetime,
            'fecha_formatted' => $cena->datetime->format('d/m/Y H:i'),
            'titulo' => $cena->title,
            'comensales_actuales' => $cena->guests_current,
            'comensales_max' => $cena->guests_max,
            'precio' => $cena->price,
            'estado' => $cena->status,
            'location' => $cena->location,
            'es_pasada' => $esPasada,
            'minutos_para_cena' => $minutosParaCena
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
public function editDinner(Cena $cena)
{
    $user = Auth::user();
    
    Log::info('=== ACCESO A EDITAR CENA ===');
    Log::info('Chef editando cena:', [
        'chef_id' => $user->id,
        'cena_id' => $cena->id,
        'cena_titulo' => $cena->title
    ]);

    // Verificar que la cena pertenece al chef autenticado
    if ($cena->user_id !== $user->id) {
        Log::warning('Chef intenta editar cena que no le pertenece:', [
            'chef_id' => $user->id,
            'cena_owner_id' => $cena->user_id,
            'cena_id' => $cena->id
        ]);
        
        return redirect()->route('chef.dashboard')
            ->with('error', 'No tienes permisos para editar esta cena.');
    }

    // Verificar que la cena no tenga reservas confirmadas
    $reservasConfirmadas = Reserva::where('cena_id', $cena->id)
        ->whereIn('estado', ['confirmada', 'pagada'])
        ->count();

    if ($reservasConfirmadas > 0) {
        Log::warning('Intento de editar cena con reservas confirmadas:', [
            'cena_id' => $cena->id,
            'reservas_confirmadas' => $reservasConfirmadas
        ]);
        
        return redirect()->route('chef.dashboard')
            ->with('error', 'No puedes editar una cena que ya tiene reservas confirmadas.');
    }

    Log::info('Mostrando formulario de edición para cena: ' . $cena->id);

    return view('chef.edit-dinner', compact('cena', 'user'));
}

public function updateDinner(Request $request, Cena $cena)
{
    $user = Auth::user();

    Log::info('=== ACTUALIZAR CENA ===', [
        'chef_id' => $user->id,
        'cena_id' => $cena->id,
    ]);

    // 1) Verifica que la cena pertenece al chef
    if ($cena->user_id !== $user->id) {
        Log::warning('Chef intenta actualizar cena que no le pertenece', [
            'chef_id' => $user->id,
            'cena_owner_id' => $cena->user_id,
            'cena_id' => $cena->id,
        ]);
        return redirect()->route('chef.dashboard')
            ->with('error', 'No tienes permisos para editar esta cena.');
    }

    // 2) Verifica que no existan reservas confirmadas/pagadas
    $reservasConfirmadas = Reserva::where('cena_id', $cena->id)
        ->whereIn('estado', ['confirmada', 'pagada'])
        ->count();

    if ($reservasConfirmadas > 0) {
        Log::warning('Intento de actualizar cena con reservas confirmadas', [
            'cena_id' => $cena->id,
            'reservas_confirmadas' => $reservasConfirmadas,
        ]);
        return redirect()->route('chef.dashboard')
            ->with('error', 'No puedes editar una cena que ya tiene reservas confirmadas.');
    }

    // 3) Validación
    // Nota: se exige futura con after:now (igual que storeDinner).
    // Si quieres permitir mismo día con margen, cambia a after:now->addMinutes(5).
    $validated = $request->validate([
        'title' => ['required','string','max:255'],
        'datetime' => ['required','date','after:now'],
        'guests' => ['required','integer','min:1','max:50'],
        'price' => ['required','numeric','min:0'],
        'menu' => ['required','string','max:2000'],
        'location' => ['required','string','max:500'],
        'latitude' => ['required','numeric','between:-90,90'],
        'longitude' => ['required','numeric','between:-180,180'],
        'is_active' => ['nullable'], // boolean luego

        'special_requirements' => ['nullable','string','max:2000'],
        'cancellation_policy' => ['nullable','string','max:2000'],

        'cover_image' => ['nullable','image','mimes:jpeg,png,jpg,webp','max:5120'],
        'gallery_images' => ['nullable','array'],
        'gallery_images.*' => ['image','mimes:jpeg,png,jpg,webp','max:5120'],

        'remove_cover' => ['nullable','boolean'],
        'remove_gallery' => ['nullable','array'],
        'remove_gallery.*' => ['integer','min:0'],
    ]);

    // 3.1) Regla de negocio: no permitir reducir cupos por debajo de los ocupados
    if ((int)$validated['guests'] < (int)$cena->guests_current) {
        return back()->withErrors([
            'guests' => 'No puedes establecer menos de '.$cena->guests_current.' cupos porque ya hay reservas ocupando esos lugares.'
        ])->withInput();
    }

    // 4) Preparar galería existente y espacio disponible
    $existingGallery = $cena->gallery_images ?: []; // array de rutas
    $removeIdx = collect($request->input('remove_gallery', []))
        ->filter(fn($i) => is_numeric($i))
        ->map(fn($i) => (int)$i)
        ->unique()
        ->values()
        ->all();

    // 4.1) Eliminar imágenes marcadas de la galería
    foreach ($removeIdx as $idx) {
        if (isset($existingGallery[$idx])) {
            try {
                Storage::disk('public')->delete($existingGallery[$idx]);
            } catch (\Throwable $t) {
                Log::warning('No se pudo eliminar imagen de galería del storage', [
                    'path' => $existingGallery[$idx],
                    'error' => $t->getMessage(),
                ]);
            }
            unset($existingGallery[$idx]);
        }
    }
    // Reindexar
    $existingGallery = array_values($existingGallery);

    // 4.2) Comprobar límite de 5 con nuevas imágenes
    $newGalleryFiles = $request->file('gallery_images', []);
    $spaceLeft = 5 - count($existingGallery);
    if (is_array($newGalleryFiles) && count($newGalleryFiles) > $spaceLeft) {
        return back()->withErrors([
            'gallery_images' => 'Solo puedes agregar '.max($spaceLeft,0).' imágenes más (límite total 5).'
        ])->withInput();
    }

    // 5) Manejo de portada
    $newCoverPath = $cena->cover_image; // por defecto mantener
    $removeCover = (bool)$request->boolean('remove_cover');

    if ($removeCover && $cena->cover_image) {
        try {
            Storage::disk('public')->delete($cena->cover_image);
        } catch (\Throwable $t) {
            Log::warning('No se pudo eliminar portada anterior', [
                'path' => $cena->cover_image,
                'error' => $t->getMessage(),
            ]);
        }
        $newCoverPath = null;
    }

    if ($request->hasFile('cover_image')) {
        // Si sube nueva portada, opcionalmente borra la anterior (si no fue marcada para eliminar ya)
        if ($cena->cover_image && !$removeCover) {
            try {
                Storage::disk('public')->delete($cena->cover_image);
            } catch (\Throwable $t) {
                Log::warning('No se pudo eliminar portada anterior (reemplazo)', [
                    'path' => $cena->cover_image,
                    'error' => $t->getMessage(),
                ]);
            }
        }
        $newCoverPath = $request->file('cover_image')->store('cenas/covers', 'public');
    }

    // 6) Agregar nuevas imágenes a la galería
    if (is_array($newGalleryFiles)) {
        foreach ($newGalleryFiles as $img) {
            $stored = $img->store('cenas/gallery', 'public');
            $existingGallery[] = $stored;
        }
    }

    // Si la galería quedó vacía, guardamos null
    $finalGallery = count($existingGallery) ? $existingGallery : null;

    // 7) Actualizar modelo
    $cena->title = $validated['title'];
    $cena->datetime = $validated['datetime'];
    $cena->guests_max = (int)$validated['guests'];
    // guests_current se mantiene
    $cena->price = $validated['price'];
    $cena->menu = $validated['menu'];
    $cena->location = $validated['location'];
    $cena->latitude = $validated['latitude'];
    $cena->longitude = $validated['longitude'];

    // Opcionales
    $cena->special_requirements = $request->input('special_requirements');
    $cena->cancellation_policy = $request->input('cancellation_policy');

    // Imágenes
    $cena->cover_image = $newCoverPath;
    $cena->gallery_images = $finalGallery;

    $cena->save();

    Log::info('Cena actualizada correctamente', [
        'cena_id' => $cena->id,
        'chef_id' => $user->id,
    ]);

    return redirect()
        ->route('chef.dinners.edit', $cena->id)
        ->with('success', '¡La cena se actualizó correctamente!');
}

public function showDinner(Cena $cena)
{
    // Verificar que la cena pertenezca al chef autenticado
    if ($cena->user_id !== auth()->id()) {
        abort(403, 'No tienes permisos para ver esta cena.');
    }

    // Cargar la relación del chef
    $cena->load('user');

    // Calcular información adicional
    $cenaData = [
        // Información básica de la cena
        'id' => $cena->id,
        'title' => $cena->title,
        'menu' => $cena->menu,
        'location' => $cena->location,
        'latitude' => $cena->latitude,
        'longitude' => $cena->longitude,
        
        // Fecha y tiempo
        'datetime' => $cena->datetime,
        'formatted_date' => $cena->datetime->format('l, j \d\e F \d\e Y'),
        'formatted_time' => $cena->datetime->format('H:i'),
        'formatted_datetime' => $cena->datetime->format('d/m/Y H:i'),
        'days_until' => now()->diffInDays($cena->datetime, false),
        'is_past' => $cena->datetime->isPast(),
        
        // Información de invitados
        'guests_max' => $cena->guests_max,
        'guests_current' => $cena->guests_current,
        'available_spots' => $cena->available_spots,
        'is_full' => $cena->is_full,
        'occupancy_percentage' => $cena->guests_max > 0 ? round(($cena->guests_current / $cena->guests_max) * 100, 1) : 0,
        
        // Información financiera
        'price' => $cena->price,
        'formatted_price' => $cena->formatted_price,
        'total_revenue_potential' => $cena->price * $cena->guests_max,
        'current_revenue' => $cena->price * $cena->guests_current,
        'formatted_total_revenue' => '$' . number_format($cena->price * $cena->guests_max, 0, ',', '.'),
        'formatted_current_revenue' => '$' . number_format($cena->price * $cena->guests_current, 0, ',', '.'),
        
        // Estado y permisos
        'status' => $cena->status,
        'status_label' => $this->getStatusLabel($cena->status),
        'status_color' => $this->getStatusColor($cena->status),
        'is_active' => $cena->is_active,
        'can_edit' => !$cena->datetime->isPast() && $cena->status !== 'cancelled',
        'can_cancel' => !$cena->datetime->isPast() && $cena->status === 'published',
        'can_publish' => $cena->status === 'draft',
        
        // Imágenes
        'cover_image_url' => $cena->cover_image_url,
        'gallery_image_urls' => $cena->gallery_image_urls ?? collect(),
        
        // Fechas de auditoría
        'created_at' => $cena->created_at,
        'updated_at' => $cena->updated_at,
        'created_ago' => $cena->created_at->diffForHumans(),
        'updated_ago' => $cena->updated_at->diffForHumans(),
    ];

    // Obtener reservas directamente de la tabla
    $reservas = \App\Models\Reserva::where('cena_id', $cena->id)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();
    
    $reservasData = [
        'total_reservas' => $reservas->count(),
        'reservas_confirmadas' => $reservas->where('estado', 'confirmada')->count(),
        'reservas_pagadas' => $reservas->where('estado_pago', 'pagado')->count(),
        'reservas_pendientes' => $reservas->where('estado', 'pendiente')->count(),
        'reservas_canceladas' => $reservas->where('estado', 'cancelada')->count(),
        'total_comensales_reservados' => $reservas->whereIn('estado', ['pendiente', 'confirmada', 'pagada', 'completada'])->sum('cantidad_comensales'),
        'ingresos_confirmados' => $reservas->where('estado_pago', 'pagado')->sum('precio_total'),
        'ingresos_potenciales' => $reservas->whereIn('estado', ['pendiente', 'confirmada'])->sum('precio_total'),
        'promedio_comensales_por_reserva' => $reservas->count() > 0 ? round($reservas->avg('cantidad_comensales'), 1) : 0,
        'lista_reservas' => $reservas
    ];

    return view('chef.dinners.show', compact('cenaData', 'cena', 'reservasData'));
}

private function getStatusLabel($status)
{
    return match($status) {
        'draft' => 'Borrador',
        'published' => 'Publicada',
        'cancelled' => 'Cancelada',
        'completed' => 'Completada',
        default => 'Desconocido',
    };
}

private function getStatusColor($status)
{
    return match($status) {
        'draft' => 'warning',
        'published' => 'success',
        'cancelled' => 'danger',
        'completed' => 'primary',
        default => 'secondary',
    };
}
// Métodos auxiliares para el estado

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
