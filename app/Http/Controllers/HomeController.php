<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cena;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Página principal de la aplicación
     */
    public function index()
    {
        // Si está autenticado, redirigir al dashboard correspondiente
        if (auth()->check()) {
            $user = auth()->user();
            
            // Redirigir según el rol del usuario (solo Spatie)
            if (method_exists($user, 'hasRole') && ($user->hasRole('chef') || $user->hasRole('chef_anfitrion'))) {
                return redirect()->route('chef.dashboard');
            }
            
            // Para otros usuarios, redirigir a un dashboard general
            return redirect()->route('dashboard');
        }

        // Si no está autenticado, mostrar página de bienvenida con datos
        $data = $this->getWelcomeData();
        
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

        // Preparar datos de la cena para la vista pública
        $cenaData = [
            'id' => $cena->id,
            'title' => $cena->title,
            'chef_name' => $cena->user->name ?? 'Chef Anónimo',
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
        ];

        return view('cenas.show', [
            'cena' => $cena,
            'cenaData' => $cenaData,
            'meta_title' => $cena->title . ' - TuMesa',
            'meta_description' => 'Reserva tu lugar en "' . $cena->title . '" con "' . ($cena->user->name ?? 'nuestro chef'). '" ' . substr($cena->menu, 0, 100) . '...'
        ]);
    }
    
}