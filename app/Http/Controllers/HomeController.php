<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cena;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;

class HomeController extends Controller
{
    /**
     * Página principal de la aplicación
     */
public function index()
{
    $data = $this->getWelcomeData();
    
    // Obtener contenidos específicos de la página 'experiencias'
    $contenidosExperiencias = \App\Models\Pagina::porPagina('experiencias')->get()->keyBy('clave');
    
    // DEBUG TEMPORAL
    if(isset($contenidosExperiencias['hero_imagen'])) {
        $valor = $contenidosExperiencias['hero_imagen']->valor;
        $urlConstruida = asset('storage/' . $valor);
        
       
    }
    
    $data['contenidos'] = $contenidosExperiencias;
    
    return view('welcome', $data);
}

    /**
     * Obtener datos para la página de bienvenida
     */
    private function getWelcomeData()
    {
        // Verificar si el modelo Cena existe
        if (!class_exists('App\Models\Cena')) {
            return $this->getEmptyWelcomeData();
        }

        try {
            // Cenas destacadas (próximas, activas, con espacios disponibles)
            $cenasDestacadas = Cena::with('user:id,name') // Cambié de 'chef' a 'user'
                ->where('status', 'published')
                ->where('is_active', true)
                ->where('datetime', '>', now())
                ->whereRaw('guests_current < guests_max') // Con espacios disponibles
                ->orderBy('datetime', 'asc')
                ->take(8)
                ->get()
                ->map(function($cena) {
                    return [
                        'id' => $cena->id,
                        'title' => $cena->title,
                        'chef_name' => $cena->user->name ?? 'Chef Anónimo', // Cambié de chef a user
                        'datetime' => $cena->datetime,
                        'formatted_date' => $cena->datetime->format('d M, Y'),
                        'formatted_time' => $cena->datetime->format('H:i'),
                        'location' => $cena->location,
                        'price' => $cena->price,
                        'formatted_price' => '$' . number_format($cena->price, 0, ',', '.'),
                        'guests_max' => $cena->guests_max,
                        'guests_current' => $cena->guests_current,
                        'available_spots' => $cena->guests_max - $cena->guests_current,
                        'cover_image_url' => $cena->cover_image_url,
                        'menu_preview' => strlen($cena->menu) > 100 ? substr($cena->menu, 0, 100) . '...' : $cena->menu,
                    ];
                });

            // Estadísticas corregidas para usar solo Spatie
            $estadisticas = [
                'total_cenas' => Cena::where('status', 'published')->count(),
                'total_chefs' => $this->countChefs(), // Método separado para contar chefs
                'cenas_este_mes' => Cena::where('status', 'published')
                    ->whereBetween('datetime', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'comensales_satisfechos' => Cena::where('status', 'completed')
                    ->sum('guests_current'),
            ];

            // Próximos eventos destacados (los 3 más próximos)
            $proximosEventos = Cena::with('user:id,name') // Cambié de 'chef' a 'user'
                ->where('status', 'published')
                ->where('is_active', true)
                ->where('datetime', '>', now())
                ->whereRaw('guests_current < guests_max')
                ->orderBy('datetime', 'asc')
                ->take(3)
                ->get()
                ->map(function($cena) {
                    return [
                        'id' => $cena->id,
                        'title' => $cena->title,
                        'chef_name' => $cena->user->name ?? 'Chef Anónimo', // Cambié de chef a user
                        'datetime' => $cena->datetime,
                        'formatted_date' => $cena->datetime->format('d M'),
                        'formatted_datetime' => $cena->datetime->format('d M, Y - H:i'),
                        'location' => $cena->location,
                        'price' => $cena->price,
                        'formatted_price' => '$' . number_format($cena->price, 0, ',', '.'),
                        'available_spots' => $cena->guests_max - $cena->guests_current,
                        'cover_image_url' => $cena->cover_image_url,
                        'days_until' => now()->diffInDays($cena->datetime, false),
                    ];
                });

        } catch (\Exception $e) {
            // Si hay error con la base de datos, usar datos vacíos
            \Log::error('Error en getWelcomeData: ' . $e->getMessage());
            return $this->getEmptyWelcomeData();
        }

        // Categorías de cocina simuladas
        $categorias = $this->getCategoriasCocina();

        // Testimonios simulados
        $testimonios = [
            [
                'nombre' => 'María González',
                'comentario' => 'Una experiencia culinaria increíble. El chef nos sorprendió con cada plato.',
                'rating' => 5,
                'cena' => 'Cena Italiana Tradicional'
            ],
            [
                'nombre' => 'Carlos Ruiz', 
                'comentario' => 'Ambiente perfecto y comida deliciosa. Volveré sin duda.',
                'rating' => 5,
                'cena' => 'Fusión Asiática Moderna'
            ],
            [
                'nombre' => 'Ana Martínez',
                'comentario' => 'Me encantó conocer gente nueva mientras disfrutaba de una cena espectacular.',
                'rating' => 4,
                'cena' => 'Cocina Mediterránea'
            ]
        ];

        return [
            'cenas_destacadas' => $cenasDestacadas,
            'estadisticas' => $estadisticas,
            'categorias' => $categorias,
            'proximos_eventos' => $proximosEventos,
            'testimonios' => $testimonios,
            'meta_title' => 'TuMesa - Experiencias Culinarias Únicas',
            'meta_description' => 'Descubre cenas íntimas preparadas por chefs locales. Conecta, disfruta y vive experiencias gastronómicas inolvidables.',
        ];
    }

    /**
     * Contar chefs usando solo Spatie roles
     */
    private function countChefs()
    {
        try {
            // Contar usuarios que tienen rol de chef usando Spatie
            return User::whereHas('roles', function($query) {
                $query->whereIn('name', ['chef', 'chef_anfitrion']);
            })->count();
        } catch (\Exception $e) {
            // Si hay error, retornar 0
            return 0;
        }
    }

    /**
     * Datos vacíos para cuando no hay cenas o hay errores
     */
    private function getEmptyWelcomeData()
    {
        return [
            'cenas_destacadas' => collect([]),
            'estadisticas' => [
                'total_cenas' => 0,
                'total_chefs' => 0,
                'cenas_este_mes' => 0,
                'comensales_satisfechos' => 0,
            ],
            'categorias' => [],
            'proximos_eventos' => collect([]),
            'testimonios' => [
                [
                    'nombre' => 'María González',
                    'comentario' => 'Una experiencia culinaria increíble. El chef nos sorprendió con cada plato.',
                    'rating' => 5,
                    'cena' => 'Cena Italiana Tradicional'
                ],
                [
                    'nombre' => 'Carlos Ruiz', 
                    'comentario' => 'Ambiente perfecto y comida deliciosa. Volveré sin duda.',
                    'rating' => 5,
                    'cena' => 'Fusión Asiática Moderna'
                ]
            ],
            'meta_title' => 'TuMesa - Experiencias Culinarias Únicas',
            'meta_description' => 'Descubre cenas íntimas preparadas por chefs locales. Conecta, disfruta y vive experiencias gastronómicas inolvidables.',
        ];
    }

    /**
     * Obtener categorías de cocina básicas
     */
    private function getCategoriasCocina()
    {
        return [
            'italiana' => [
                'nombre' => 'Cocina Italiana',
                'icono' => 'fas fa-pizza-slice',
                'count' => 3
            ],
            'asiatica' => [
                'nombre' => 'Cocina Asiática', 
                'icono' => 'fas fa-pepper-hot',
                'count' => 2
            ],
            'mediterranea' => [
                'nombre' => 'Mediterránea',
                'icono' => 'fas fa-fish',
                'count' => 4
            ]
        ];
    }

    /**
     * Método para búsqueda de cenas (para después)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        // Por ahora, retornar vista básica
        return view('cenas.search', [
            'cenas' => collect([]),
            'query' => $query
        ]);
    }
public function showCena(Cena $cena)
{
    // Verificar que la cena esté disponible para el público
    if ($cena->status !== 'published' || !$cena->is_active) {
        abort(404, 'Cena no disponible');
    }

    // Cargar la relación del usuario/chef
    $cena->load('user');

    // Verificar si el usuario actual tiene una reserva confirmada para esta cena
    $userHasReservation = false;
    $userReservation = null;
    
    if (Auth::check()) {
        $userReservation = Reserva::where('cena_id', $cena->id)
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['confirmada', 'pagada']) // Solo reservas confirmadas/pagadas
            ->where('estado_pago', '!=', 'cancelada')
            ->first();
            
        $userHasReservation = $userReservation !== null;
    }

    // Calcular si debe mostrar ubicación exacta
    $hoursUntilCena = now()->diffInHours($cena->datetime, false);
    $canSeeExactLocation = $userHasReservation && $hoursUntilCena <= 24;

    // Preparar datos de la cena para la vista pública
    $cenaData = [
        'id' => $cena->id,
        'title' => $cena->title,
        'chef_name' => $cena->user->name ?? 'Chef Anónimo',
        'chef_specialty' => $cena->user->especialidad ?? null,
        'chef_rating' => $cena->user->formatted_rating ?? '0.0',
        'chef_experience' => $cena->user->experience_text ?? null,
        'chef_bio' => $cena->user->bio ?? null,
        'chef_avatar' => $cena->user->avatar_url ?? null,
        'chef_instagram' => $cena->user->instagram_url ?? null,
        'chef_facebook' => $cena->user->facebook_url ?? null,
        'chef_website' => $cena->user->website ?? null,
        'datetime' => $cena->datetime,
        'formatted_date' => $cena->datetime->format('l, j \d\e F \d\e Y'),
        'formatted_time' => $cena->datetime->format('H:i'),
        'formatted_datetime' => $cena->datetime->format('d/m/Y H:i'),
        'location' => $cena->location,
        'latitude' => $cena->latitude,
        'longitude' => $cena->longitude,
        'menu' => $cena->menu,
        'price' => $cena->price,
        'formatted_price' => '$' . number_format($cena->price, 0, ',', '.'),
        'guests_max' => $cena->guests_max,
        'guests_current' => $cena->guests_current,
        'available_spots' => $cena->guests_max - $cena->guests_current,
        'is_available' => ($cena->guests_max - $cena->guests_current) > 0,
        'cover_image_url' => $cena->cover_image_url,
        'gallery_image_urls' => $cena->gallery_image_urls ?? collect(),
        'days_until' => now()->diffInDays($cena->datetime, false),
        'is_past' => $cena->datetime->isPast(),
        'can_book' => !$cena->datetime->isPast() && ($cena->guests_max - $cena->guests_current) > 0,
        
        // NUEVOS CAMPOS para control de ubicación
        'user_has_reservation' => $userHasReservation,
        'can_see_exact_location' => $canSeeExactLocation,
        'hours_until_cena' => $hoursUntilCena,
        'reservation_code' => $userReservation?->codigo_reserva ?? null,
    ];

    return view('cenas.show', [
        'cena' => $cena,
        'cenaData' => $cenaData,
        'userReservation' => $userReservation,
        'meta_title' => $cena->title . ' - TuMesa',
        'meta_description' => 'Reserva tu lugar en "' . $cena->title . '" con "' . ($cena->user->name ?? 'nuestro chef'). '" ' . substr($cena->menu, 0, 100) . '...'
    ]);
}

public function users()
{
    $usuarios = User::with('roles')->paginate(15);
    $roles = \Spatie\Permission\Models\Role::all();
    
    return view('admin.usuarios.index', compact('usuarios', 'roles'));
}

/**
 * Actualizar información del usuario
 */
public function updateUser(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:500',
        'bio' => 'nullable|string|max:1000',
        'especialidad' => 'nullable|string|max:255',
        'experiencia_anos' => 'nullable|integer|min:0|max:50',
        'website' => 'nullable|url|max:255',
        'instagram' => 'nullable|string|max:255',
        'facebook' => 'nullable|string|max:255',
    ]);

    try {
        $user->update($request->only([
            'name', 'email', 'telefono', 'direccion', 'bio', 
            'especialidad', 'experiencia_anos', 'website', 
            'instagram', 'facebook'
        ]));

        return redirect()->route('admin.usuarios.index')
                        ->with('success', 'Usuario actualizado correctamente.');
                        
    } catch (\Exception $e) {
        return redirect()->route('admin.usuarios.index')
                        ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
    }
}

/**
 * Actualizar rol del usuario
 */
public function updateUserRole(Request $request, User $user)
{
    $request->validate([
        'role' => 'required|string|exists:roles,name'
    ]);

    try {
        // Actualizar el campo role en la tabla users
        $user->update(['role' => $request->role]);
        
        // Sincronizar roles de Spatie
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.usuarios.index')
                        ->with('success', "Rol actualizado correctamente para {$user->name}.");
                        
    } catch (\Exception $e) {
        return redirect()->route('admin.usuarios.index')
                        ->with('error', 'Error al actualizar el rol: ' . $e->getMessage());
    }
}

/**
 * Eliminar usuario
 */
public function deleteUser(User $user)
{
    // Verificar que no sea el usuario actual
    if ($user->id === auth()->id()) {
        return redirect()->route('admin.usuarios.index')
                        ->with('error', 'No puedes eliminar tu propia cuenta.');
    }

    try {
        $userName = $user->name;
        
        // Eliminar roles de Spatie
        $user->syncRoles([]);
        
        // Eliminar el usuario
        $user->delete();

        return redirect()->route('admin.usuarios.index')
                        ->with('success', "Usuario {$userName} eliminado correctamente.");
                        
    } catch (\Exception $e) {
        return redirect()->route('admin.usuarios.index')
                        ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
    }
}
    
}