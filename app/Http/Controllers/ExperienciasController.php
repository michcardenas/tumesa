<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cena;
use App\Models\Pagina;

class ExperienciasController extends Controller
{
  public function index(Request $request)
{
    // Base: solo experiencias activas, publicadas y futuras
    $base = Cena::query()->active()->published()->upcoming()->with('chef');

    // Estadísticas base para la UI
    $minPrice = (int) floor((clone $base)->min('price') ?? 0);
    $maxPrice = (int) ceil((clone $base)->max('price') ?? 0);

    // Valores de filtros desde la URL
    $q = trim((string) $request->input('q', ''));
    $city = trim((string) $request->input('city', ''));
    $gMin = $request->integer('guests', 0);
    $pMin = $request->integer('price_min', $minPrice);
    $pMax = $request->integer('price_max', $maxPrice);
    $sort = $request->input('sort', 'date');
    
    // Nuevos parámetros para búsqueda por proximidad
    $userLat = $request->input('lat');
    $userLng = $request->input('lng');
    $radius = $request->input('radius', 10); // km por defecto

    // Aplicar filtros
    $query = clone $base;

    if ($q !== '') {
        $query->where(function ($qq) use ($q) {
            $qq->where('title', 'like', "%{$q}%")
                ->orWhere('menu', 'like', "%{$q}%")
                ->orWhere('location', 'like', "%{$q}%")
                ->orWhereHas('chef', function ($qc) use ($q) {
                    $qc->where('name', 'like', "%{$q}%");
                });
        });
    }

    // Filtro de ubicación corregido - solo usar el campo 'location' que existe
    if ($city !== '') {
        // Primero intentar búsqueda exacta por ubicación
        $exactMatch = clone $query;
        $exactMatch->where('location', 'like', "%{$city}%");

        // Si no hay resultados exactos y tenemos coordenadas, buscar por proximidad
        if ($exactMatch->count() === 0 && $userLat && $userLng) {
            $query = $this->addProximityFilter($query, $userLat, $userLng, $radius);
        } else {
            $query = $exactMatch;
        }
    } elseif ($userLat && $userLng) {
        // Solo filtro por proximidad si no hay filtro de ciudad
        $query = $this->addProximityFilter($query, $userLat, $userLng, $radius);
    }

    if ($gMin > 0) {
        $query->where('guests_max', '>=', $gMin);
    }

    // Precio
    if ($pMin > $pMax) {
        [$pMin, $pMax] = [$pMax, $pMin];
    }
    $query->whereBetween('price', [$pMin, $pMax]);

    // Orden
    switch ($sort) {
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        case 'distance':
            if ($userLat && $userLng) {
                // Ordenar por distancia si tenemos coordenadas
                $query = $this->addDistanceCalculation($query, $userLat, $userLng);
                $query->orderBy('distance', 'asc');
            } else {
                $query->orderBy('datetime', 'asc');
            }
            break;
        default: // 'date'
            $query->orderBy('datetime', 'asc');
            break;
    }

    // Paginación
    $cenas = $query->paginate(12)->appends($request->query());

    // NUEVO: Agregar ubicaciones limpias a cada cena
    // Esto usa exactamente la misma lógica que el filtro
    foreach ($cenas as $cena) {
        $cena->clean_location = $this->cleanLocationString($cena->location);
    }

    // Opciones para selects (ciudades y barrios limpios)
    $locations = $this->getCleanLocations($base);

    return view('experiencias.index', [
        'cenas' => $cenas,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'locations' => $locations,
        'filters' => [
            'q' => $q,
            'city' => $city,
            'guests' => $gMin,
            'price_min' => $pMin,
            'price_max' => $pMax,
            'sort' => $sort,
            'lat' => $userLat,
            'lng' => $userLng,
            'radius' => $radius,
        ],
    ]);
}
    /**
     * Agregar filtro de proximidad geográfica
     */
    private function addProximityFilter($query, $lat, $lng, $radiusKm)
    {
        // Fórmula Haversine para calcular distancia
        // 6371 es el radio de la Tierra en km
        $earthRadius = 6371;
        
        return $query->whereRaw("
            (
                {$earthRadius} * acos(
                    cos(radians(?)) 
                    * cos(radians(latitude)) 
                    * cos(radians(longitude) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(latitude))
                )
            ) <= ?
        ", [$lat, $lng, $lat, $radiusKm]);
    }

    /**
     * Agregar cálculo de distancia para ordenamiento
     */
    private function addDistanceCalculation($query, $lat, $lng)
    {
        $earthRadius = 6371;
        
        return $query->selectRaw("
            *,
            (
                {$earthRadius} * acos(
                    cos(radians(?)) 
                    * cos(radians(latitude)) 
                    * cos(radians(longitude) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(latitude))
                )
            ) AS distance
        ", [$lat, $lng, $lat]);
    }

    private function getCleanLocations($baseQuery)
    {
        $rawLocations = (clone $baseQuery)
            ->select('location', 'latitude', 'longitude')
            ->distinct()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->orderBy('location')
            ->get();

        $locations = collect();

        foreach ($rawLocations as $loc) {
            // Limpiar la ubicación raw
            $cleanedLocation = $this->cleanLocationString($loc->location);
            
            if (!empty(trim($cleanedLocation))) {
                $locations->push([
                    'value' => $cleanedLocation,
                    'display' => $cleanedLocation,
                    'lat' => $loc->latitude,
                    'lng' => $loc->longitude,
                ]);
            }
        }

        return $locations->unique('value')->values();
    }

   private function cleanLocationString($location)
{
    if (empty($location)) return '';
    
    // Normalizar el string
    $location = str_replace(['  ', '   '], ' ', trim($location));
    
    // Dividir por comas
    $parts = array_map('trim', explode(',', $location));
    
    // Listas expandidas de barrios y zonas de Buenos Aires
    $knownNeighborhoods = [
        // CABA
        'Palermo', 'Palermo Hollywood', 'Palermo Soho', 'Palermo Chico',
        'Recoleta', 'Puerto Madero', 'San Telmo', 'La Boca',
        'Belgrano', 'Villa Crespo', 'Colegiales', 'Núñez',
        'Barracas', 'Caballito', 'Flores', 'Villa Urquiza',
        'Villa Devoto', 'Retiro', 'Constitución', 'Monserrat',
        'San Nicolás', 'Microcentro', 'Almagro', 'Boedo',
        'Parque Patricios', 'Nueva Pompeya', 'Villa Lugano',
        'Villa Riachuelo', 'Mataderos', 'Liniers', 'Villa Luro',
        'Vélez Sársfield', 'Villa Real', 'Versalles', 'Villa del Parque',
        'Villa Santa Rita', 'Villa Pueyrredón', 'Saavedra', 'Chacarita',
        'Paternal', 'Villa Ortúzar', 'Agronomía', 'Parque Chas',
        'Villa General Mitre', 'Balvanera', 'San Cristóbal',
        
        // Gran Buenos Aires - Zona Norte
        'Olivos', 'Vicente López', 'La Lucila', 'Martínez',
        'Acassuso', 'San Isidro', 'Beccar', 'Victoria',
        'San Fernando', 'Tigre', 'Don Torcuato', 'El Talar',
        'General Pacheco', 'Nordelta', 'Benavídez',
        'Florida', 'Florida Oeste', 'Munro', 'Carapachay',
        'Villa Adelina', 'Boulogne', 'Villa Martelli',
        
        // Gran Buenos Aires - Zona Oeste
        'Ciudad Jardín Lomas del Palomar', 'Lomas del Palomar',
        'El Palomar', 'Caseros', 'Ciudadela', 'Ramos Mejía',
        'Haedo', 'Morón', 'Castelar', 'Ituzaingó', 'Hurlingham',
        'Villa Tesei', 'Santos Lugares', 'Sáenz Peña',
        'Tres de Febrero', 'San Martín', 'Villa Ballester',
        'San Andrés', 'Villa Maipú', 'Villa Lynch',
        
        // Gran Buenos Aires - Zona Sur
        'Avellaneda', 'Lanús', 'Banfield', 'Lomas de Zamora',
        'Temperley', 'Adrogué', 'Burzaco', 'Quilmes',
        'Bernal', 'Don Bosco', 'Wilde', 'Sarandí',
        'Villa Domínico', 'Gerli', 'Remedios de Escalada',
        'Valentín Alsina', 'Monte Grande', 'Luis Guillón',
        'Ezeiza', 'Tristán Suárez', 'Canning',
        
        // Otros
        'La Plata', 'City Bell', 'Gonnet', 'Villa Elisa',
        'Berisso', 'Ensenada', 'Pilar', 'Del Viso',
        'Tortuguitas', 'Grand Bourg', 'Los Polvorines',
        'Pablo Nogués', 'Malvinas Argentinas', 'Escobar',
        'Garín', 'Maschwitz', 'Belén de Escobar',
        'El Paraíso', 'La Reja', 'Moreno', 'Paso del Rey',
        'Merlo', 'Padua', 'Pontevedra', 'González Catán',
        'Isidro Casanova', 'Rafael Castillo', 'Laferrere'
    ];
    
    // Ciudades y municipios principales
    $knownCities = [
        'Buenos Aires', 'CABA', 'Ciudad de Buenos Aires',
        'Vicente López', 'San Isidro', 'San Fernando', 'Tigre',
        'La Plata', 'Quilmes', 'Avellaneda', 'Lanús',
        'Lomas de Zamora', 'Almirante Brown', 'Berazategui',
        'Florencio Varela', 'Esteban Echeverría', 'Ezeiza',
        'Morón', 'Tres de Febrero', 'La Matanza', 'Merlo',
        'Moreno', 'San Martín', 'San Miguel', 'José C. Paz',
        'Malvinas Argentinas', 'Hurlingham', 'Ituzaingó',
        'Pilar', 'Escobar', 'General Rodríguez', 'Luján',
        'Mercedes', 'Campana', 'Zárate', 'Exaltación de la Cruz',
        'San Antonio de Areco', 'Partido de Ramallo', 'Ramallo'
    ];
    
    // Términos a ignorar o filtrar
    $ignoreTerms = [
        'Argentina', 'Provincia de Buenos Aires', 'Buenos Aires Province',
        'AR', 'ARG', 'América del Sur', 'South America', 'América'
    ];
    
    // Limpiar y procesar cada parte
    $cleanedParts = [];
    $foundNeighborhood = null;
    $foundCity = null;
    $streetName = null;
    
    foreach ($parts as $part) {
        // Limpiar números de calle y códigos postales
        $originalPart = $part;
        $part = preg_replace('/^\d+\s*-?\s*/', '', $part);
        $part = preg_replace('/\b[A-Z0-9]{4,8}\b/', '', $part);
        $part = preg_replace('/\s+/', ' ', trim($part));
        
        if (empty($part)) continue;
        
        // Verificar si debe ser ignorado
        $shouldIgnore = false;
        foreach ($ignoreTerms as $ignore) {
            if (stripos($part, $ignore) !== false || 
                strcasecmp($part, $ignore) === 0) {
                $shouldIgnore = true;
                break;
            }
        }
        if ($shouldIgnore) continue;
        
        // Normalizar nombres especiales
        if (stripos($part, 'CABA') !== false || 
            stripos($part, 'Ciudad Autónoma') !== false ||
            stripos($part, 'C.A.B.A') !== false) {
            $part = 'Buenos Aires';
        }
        
        // Manejar "Partido de X" o "Municipality of X"
        $part = preg_replace('/^(Partido de |Municipality of |Municipalidad de )/i', '', $part);
        
        // Buscar barrio conocido
        if (!$foundNeighborhood) {
            foreach ($knownNeighborhoods as $neighborhood) {
                if (stripos($part, $neighborhood) !== false) {
                    $foundNeighborhood = $neighborhood;
                    break;
                }
            }
        }
        
        // Buscar ciudad conocida
        if (!$foundCity) {
            foreach ($knownCities as $city) {
                if (stripos($part, $city) !== false) {
                    $foundCity = $city;
                    break;
                }
            }
        }
        
        // Si no es ni barrio ni ciudad conocida, podría ser un nombre de calle
        if (!$foundNeighborhood && !$foundCity && !$streetName && !empty($part)) {
            // Verificar si parece ser un nombre de calle (contiene palabras típicas)
            if (preg_match('/\b(calle|avenida|av\.|boulevard|blvd|pasaje|paseo)/i', $originalPart) ||
                preg_match('/^[A-Z][a-záéíóú]+(\s+[A-Z][a-záéíóú]+)*$/', $part)) {
                $streetName = $part;
            }
        }
        
        $cleanedParts[] = $part;
    }
    
    // Si no encontramos barrio o ciudad, intentar con heurística mejorada
    if (!$foundNeighborhood && !$foundCity && count($cleanedParts) >= 2) {
        // Buscar de atrás hacia adelante (usualmente la estructura es: calle, barrio, ciudad, país)
        for ($i = count($cleanedParts) - 1; $i >= 0; $i--) {
            $part = $cleanedParts[$i];
            
            // Si ya encontramos ciudad, el anterior podría ser el barrio
            if ($foundCity && !$foundNeighborhood && $i > 0) {
                $foundNeighborhood = $cleanedParts[$i - 1];
                break;
            }
            
            // Buscar patrones comunes de ciudades
            if (!$foundCity) {
                if (preg_match('/\b(Buenos Aires|Vicente López|San Isidro|Tigre|La Plata|Quilmes|Avellaneda|Lanús|Lomas de Zamora|Morón|San Martín)\b/i', $part, $matches)) {
                    $foundCity = $matches[1];
                }
            }
        }
    }
    
    // Si aún no tenemos resultados claros, usar las últimas partes limpias
    if (!$foundNeighborhood && !$foundCity && count($cleanedParts) > 0) {
        if (count($cleanedParts) >= 2) {
            // Tomar las dos últimas partes relevantes
            $foundNeighborhood = $cleanedParts[count($cleanedParts) - 2];
            $foundCity = $cleanedParts[count($cleanedParts) - 1];
        } else {
            // Solo una parte, asumirla como barrio
            $foundNeighborhood = $cleanedParts[0];
            $foundCity = 'Buenos Aires'; // Asumir Buenos Aires por defecto
        }
    }
    
    // Normalizar ciudad si es necesario
    if ($foundCity) {
        // Remover "Partido de" si quedó
        $foundCity = preg_replace('/^(Partido de |Municipality of )/i', '', $foundCity);
        
        // Si la ciudad es muy genérica o es igual al barrio, usar Buenos Aires
        if (strcasecmp($foundCity, $foundNeighborhood) === 0) {
            $foundCity = 'Buenos Aires';
        }
    }
    
    // Construir el resultado final
    if ($foundNeighborhood && $foundCity) {
        // Evitar duplicación si el barrio y ciudad son iguales
        if (strcasecmp($foundNeighborhood, $foundCity) === 0) {
            return ucwords(strtolower($foundNeighborhood));
        }
        return ucwords(strtolower($foundNeighborhood)) . ', ' . ucwords(strtolower($foundCity));
    } elseif ($foundNeighborhood) {
        return ucwords(strtolower($foundNeighborhood)) . ', Buenos Aires';
    } elseif ($foundCity) {
        return ucwords(strtolower($foundCity));
    }
    
    // Si no pudimos determinar nada, devolver vacío
    return '';
}

    public function serChef()
    {
        // Obtener contenidos específicos de la página 'ser-chef'
        $contenidosSerChef = \App\Models\Pagina::porPagina('ser-chef')->get()->keyBy('clave');
        
        return view('experiencias.ser-chef', ['contenidos' => $contenidosSerChef]);
    }

    public function comoFunciona()
    {
        // Obtener contenidos específicos de la página 'como-funciona'
        $contenidosComoFunciona = \App\Models\Pagina::porPagina('como-funciona')->get()->keyBy('clave');
        
        return view('experiencias.como-funciona', ['contenidos' => $contenidosComoFunciona]);
    }
}