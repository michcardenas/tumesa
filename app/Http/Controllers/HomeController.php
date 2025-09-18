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
            'clean_location' => $this->cleanLocationString($cena->location), // ✅ AGREGAR ESTA LÍNEA

        
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
private function cleanLocationString($location)
{
    if (empty($location)) return '';
    
    // Normalizar el string
    $location = str_replace(['  ', '   '], ' ', trim($location));
    
    // Dividir por comas
    $parts = array_map('trim', explode(',', $location));
    
    // Provincias de Argentina
    $provincias = [
        'Buenos Aires', 'CABA', 'Ciudad Autónoma de Buenos Aires',
        'Córdoba', 'Santa Fe', 'Mendoza', 'Tucumán', 'Salta',
        'Entre Ríos', 'Corrientes', 'Misiones', 'Chaco', 'Formosa',
        'Santiago del Estero', 'San Juan', 'San Luis', 'La Rioja',
        'Catamarca', 'Jujuy', 'La Pampa', 'Neuquén', 'Río Negro',
        'Chubut', 'Santa Cruz', 'Tierra del Fuego'
    ];
    
    // Términos a ignorar
    $ignoreTerms = [
        'Argentina', 'AR', 'ARG', 'América del Sur', 'South America',
        'República Argentina', 'Argentine Republic'
    ];
    
    // Variables para almacenar resultados
    $direccion = null;
    $barrio = null;
    $localidad = null;
    $partido = null;
    $provincia = null;
    $cleanedParts = [];
    
    // Procesar cada parte
    foreach ($parts as $index => $part) {
        $originalPart = $part;
        
        // Detectar si es "Partido de X"
        if (preg_match('/^Partido de (.+)$/i', $originalPart, $matches)) {
            $partido = trim($matches[1]);
            continue;
        }
        
        // Detectar si es "Provincia de X" o "X Province"
        if (preg_match('/^(Provincia de |Province of |)(.+Province)$/i', $originalPart, $matches)) {
            $provinciaTemp = trim(str_replace('Province', '', $matches[2]));
            if (strcasecmp($provinciaTemp, 'Buenos Aires') === 0) {
                $provincia = 'Buenos Aires';
            } else {
                $provincia = $provinciaTemp;
            }
            continue;
        }
        
        // Verificar si debe ser ignorado
        $shouldIgnore = false;
        foreach ($ignoreTerms as $ignore) {
            if (strcasecmp($part, $ignore) === 0 || 
                stripos($part, $ignore) !== false) {
                $shouldIgnore = true;
                break;
            }
        }
        if ($shouldIgnore) continue;
        
        // Limpiar números al inicio (direcciones) y códigos postales
        $cleanPart = preg_replace('/^\d+\s*-?\s*/', '', $part);
        $cleanPart = preg_replace('/\b[A-Z]\d{4}[A-Z]{3}\b/', '', $cleanPart);
        $cleanPart = preg_replace('/\b[A-Z0-9]{4,8}\b/', '', $cleanPart);
        $cleanPart = preg_replace('/\s+/', ' ', trim($cleanPart));
        
        if (empty($cleanPart)) continue;
        
        // Normalizar CABA
        if (stripos($cleanPart, 'CABA') !== false || 
            stripos($cleanPart, 'Ciudad Autónoma') !== false ||
            stripos($cleanPart, 'C.A.B.A') !== false) {
            $cleanPart = 'Buenos Aires';
            $provincia = 'CABA';
        }
        
        // Verificar si es una provincia conocida
        $isProvince = false;
        foreach ($provincias as $prov) {
            if (strcasecmp($cleanPart, $prov) === 0) {
                if (!$provincia) {
                    $provincia = $prov;
                }
                $isProvince = true;
                break;
            }
        }
        
        // Si no es provincia y no está vacío, agregarlo a partes limpias
        if (!$isProvince && !empty($cleanPart)) {
            $cleanedParts[] = $cleanPart;
        }
    }
    
    // LÓGICA MEJORADA PARA IDENTIFICAR BARRIO VS CALLE
    $numParts = count($cleanedParts);
    
    if ($numParts > 0) {
        // Detectar si la primera parte es una dirección/calle
        $firstPartIsAddress = false;
        if ($numParts >= 2) {
            $firstPart = $cleanedParts[0];
            
            // Patrones que indican que es una calle/dirección
            $addressPatterns = [
                '/^\d+/', // Empieza con número
                '/\b\d+\b/', // Contiene números (ej: "Av. Corrientes 1234")
                '/\b(calle|avenida|av\.?|boulevard|blvd|pasaje|paseo|ruta|camino)/i',
                '/^[A-Z][a-záéíóú]+\s+\d+/i', // Formato "Nombre Número"
            ];
            
            foreach ($addressPatterns as $pattern) {
                if (preg_match($pattern, $firstPart)) {
                    $firstPartIsAddress = true;
                    $direccion = $firstPart;
                    break;
                }
            }
        }
        
        // Asignar barrio y localidad basado en si detectamos una dirección
        if ($firstPartIsAddress) {
            // Ignorar la primera parte (es la calle)
            $remainingParts = array_slice($cleanedParts, 1);
            
            if (count($remainingParts) == 1) {
                // Solo queda una parte: es barrio o localidad
                // Decidir basado en si parece un barrio conocido
                $part = $remainingParts[0];
                if ($this->looksLikeBarrio($part)) {
                    $barrio = $part;
                    $localidad = 'Buenos Aires'; // Asumimos Buenos Aires por defecto
                } else {
                    $localidad = $part;
                }
            } elseif (count($remainingParts) == 2) {
                // Dos partes: barrio y localidad
                $barrio = $remainingParts[0];
                $localidad = $remainingParts[1];
            } elseif (count($remainingParts) >= 3) {
                // Tres o más: tomar el segundo como barrio
                $barrio = $remainingParts[0];
                $localidad = $remainingParts[1];
            }
        } else {
            // No detectamos dirección, trabajar con todas las partes
            if ($numParts == 1) {
                // Solo una parte
                $part = $cleanedParts[0];
                if ($this->looksLikeBarrio($part)) {
                    $barrio = $part;
                    $localidad = 'Buenos Aires';
                } else {
                    $localidad = $part;
                }
            } elseif ($numParts == 2) {
                // Dos partes: probablemente barrio y localidad
                $barrio = $cleanedParts[0];
                $localidad = $cleanedParts[1];
            } elseif ($numParts >= 3) {
                // Tres o más partes sin dirección detectada
                // Tomar las dos del medio como más probables para barrio/localidad
                $barrio = $cleanedParts[0];
                $localidad = $cleanedParts[1];
            }
        }
    }
    
    // Si tenemos partido pero no localidad, usar el partido como localidad
    if ($partido && !$localidad) {
        $localidad = $partido;
    } elseif ($partido && $localidad && strcasecmp($partido, $localidad) !== 0) {
        if (!$barrio) {
            $barrio = $localidad;
            $localidad = $partido;
        }
    }
    
    // CONSTRUCCIÓN DEL RESULTADO FINAL: Barrio, Ciudad
    $result = [];
    
    // PRIORIDAD 1: Si tenemos barrio, usarlo
    if ($barrio && !empty(trim($barrio))) {
        $result[] = $this->formatLocationName($barrio);
    } 
    // PRIORIDAD 2: Si no hay barrio pero hay localidad, usarla
    elseif ($localidad && !empty(trim($localidad))) {
        // Si la localidad es Buenos Aires, intentar encontrar algo más específico
        if (strcasecmp($localidad, 'Buenos Aires') === 0 && $partido) {
            $result[] = $this->formatLocationName($partido);
        } else {
            $result[] = $this->formatLocationName($localidad);
        }
    }
    // PRIORIDAD 3: Si hay partido, usarlo
    elseif ($partido && !empty(trim($partido))) {
        $result[] = $this->formatLocationName($partido);
    }
    
    // Agregar la ciudad/provincia como segunda parte
    if (count($result) > 0) {
        $ciudadFinal = null;
        
        // Determinar la ciudad basado en el contexto
        if ($localidad && strcasecmp($localidad, $result[0]) !== 0) {
            $ciudadFinal = $this->formatLocationName($localidad);
        } elseif ($provincia) {
            if ($provincia === 'CABA' || stripos($provincia, 'Ciudad Autónoma') !== false) {
                $ciudadFinal = 'Buenos Aires';
            } else {
                $ciudadFinal = $this->formatLocationName($provincia);
            }
        } else {
            // Por defecto Buenos Aires
            $ciudadFinal = 'Buenos Aires';
        }
        
        // Evitar duplicación
        if ($ciudadFinal && strcasecmp($result[0], $ciudadFinal) !== 0) {
            $result[] = $ciudadFinal;
        } else if (count($result) === 1) {
            // Si solo tenemos una parte y es igual a la ciudad, agregar Buenos Aires
            $result[] = 'Buenos Aires';
        }
    }
    
    // Si no pudimos determinar nada, devolver vacío
    if (count($result) === 0) {
        return '';
    }
    
    // Asegurar que tengamos máximo 2 partes
    if (count($result) > 2) {
        $result = array_slice($result, 0, 2);
    }
    
    return implode(', ', $result);
}

/**
 * Método auxiliar para determinar si una cadena parece ser un barrio
 */
private function looksLikeBarrio($string)
{
    // Barrios conocidos de Buenos Aires y otras ciudades
    $barriosConocidos = [
        'Palermo', 'Recoleta', 'Belgrano', 'Caballito', 'Flores', 
        'Villa Crespo', 'San Telmo', 'La Boca', 'Puerto Madero',
        'Núñez', 'Saavedra', 'Colegiales', 'Almagro', 'Boedo',
        'Barracas', 'Constitución', 'Monserrat', 'Retiro', 'San Nicolás',
        'Balvanera', 'San Cristóbal', 'Parque Patricios', 'Nueva Pompeya',
        'Villa Urquiza', 'Villa Devoto', 'Villa del Parque', 'Villa Lugano',
        'Mataderos', 'Liniers', 'Villa Luro', 'Vélez Sarsfield',
        'Villa Soldati', 'Parque Avellaneda', 'Parque Chacabuco',
        'Puerto Madero', 'Chacarita', 'Paternal', 'Villa Ortúzar',
        'Agronomía', 'Monte Castro', 'Villa Real', 'Versalles'
    ];
    
    foreach ($barriosConocidos as $barrio) {
        if (stripos($string, $barrio) !== false) {
            return true;
        }
    }
    
    // Patrones que sugieren que es un barrio
    if (preg_match('/\b(Villa|Barrio|Parque)\s+/i', $string)) {
        return true;
    }
    
    return false;
}

/**
 * Método auxiliar para formatear nombres de ubicación
 */
private function formatLocationName($name)
{
    // Capitalizar correctamente
    $name = ucwords(strtolower(trim($name)));
    
    // Corregir casos especiales
    $replacements = [
        'Caba' => 'CABA',
        'Ii' => 'II',
        'Iii' => 'III',
        'De' => 'de',
        'Del' => 'del',
        'La' => 'la',
        'Las' => 'las',
        'Los' => 'los',
        'Y' => 'y'
    ];
    
    foreach ($replacements as $search => $replace) {
        // Solo reemplazar si no es la primera palabra
        $name = preg_replace('/\s+' . $search . '\s+/i', ' ' . $replace . ' ', $name);
    }
    
    // Asegurar que la primera letra siempre sea mayúscula
    return ucfirst($name);
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
    try {
        // Validación
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'role' => 'required|in:admin,chef_anfitrion,comensal',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Reglas adicionales para chefs
        if ($request->role === 'chef_anfitrion') {
            $rules = array_merge($rules, [
                'especialidad' => 'nullable|string|max:255',
                'experiencia_anos' => 'nullable|integer|min:0|max:50',
                'website' => 'nullable|url|max:255',
                'instagram' => 'nullable|string|max:255',
                'facebook' => 'nullable|string|max:255',
            ]);
        }

        $validatedData = $request->validate($rules);

        // Manejar subida del avatar
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe y no es de proveedor externo
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                $oldAvatarPath = storage_path('app/public/' . $user->avatar);
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
            }

            // Subir nuevo avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;
        }

        // Si no es chef, limpiar campos de chef
        if ($validatedData['role'] !== 'chef_anfitrion') {
            $validatedData['especialidad'] = null;
            $validatedData['experiencia_anos'] = null;
            $validatedData['website'] = null;
            $validatedData['instagram'] = null;
            $validatedData['facebook'] = null;
        }

        // Actualizar usuario
        $user->update($validatedData);

        // Actualizar roles de Spatie si es necesario
        if ($user->role !== $validatedData['role']) {
            $user->syncRoles([$validatedData['role']]);
        }

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'Usuario actualizado correctamente.');

    } catch (\Exception $e) {
        \Log::error('Error actualizando usuario: ' . $e->getMessage());
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Ocurrió un error al actualizar el usuario. Por favor, intenta de nuevo.');
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
public function editUser(User $user)
{
    return view('admin.usuarios.edit', compact('user'));
}
    
}