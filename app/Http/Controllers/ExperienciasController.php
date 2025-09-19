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
    // Descomponer el filtro seleccionado (ej: "Palermo, Buenos Aires")
    $cityParts = array_map('trim', explode(',', $city));
    
    // Crear una consulta flexible que busque por todas las partes
    $query->where(function ($locationQuery) use ($cityParts) {
        // Para cada parte de la ubicación, buscar si está presente en el campo location
        foreach ($cityParts as $index => $part) {
            if (!empty($part)) {
                if ($index === 0) {
                    // Primera parte: usar WHERE
                    $locationQuery->where('location', 'like', "%{$part}%");
                } else {
                    // Partes adicionales: usar AND para ser más restrictivo
                    $locationQuery->where('location', 'like', "%{$part}%");
                }
            }
        }
    });
    
    // Filtrar adicionalmente por proximidad si hay coordenadas
    if ($userLat && $userLng) {
        $query = $this->addProximityFilter($query, $userLat, $userLng, $radius);
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

  private function cleanLocationString($location)
{
    if (empty($location)) return '';

    // Normalizar el string
    $location = str_replace(['  ', '   '], ' ', trim($location));

    // DEBUG TEMPORAL - remover después
    $debug = $location === "Domingo Repetto 461, Martínez, Buenos Aires Province, Argentina";
    
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
        
        // NO limpiar números aquí - los necesitamos para detectar direcciones después
        // Solo limpiar códigos postales y normalizar espacios
        $cleanPart = preg_replace('/\b[A-Z]\d{4}[A-Z]{3}\b/', '', $part);
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
        // Estrategia nueva: buscar el barrio/localidad desde el final hacia atrás
        // omitiendo las direcciones que normalmente están al principio

        if ($numParts == 1) {
            // Solo una parte
            $part = $cleanedParts[0];
            if ($this->looksLikeBarrio($part)) {
                $barrio = $part;
                $localidad = 'Buenos Aires';
            } else {
                $localidad = $part;
            }
        } elseif ($numParts >= 2) {
            // Para múltiples partes, analizar desde el final
            // La estrategia es: la penúltima parte suele ser el barrio y la última la ciudad
            // pero si hay direcciones, las saltamos

            $potentialParts = [];

            // Filtrar partes que NO son direcciones para encontrar barrio/ciudad
            foreach ($cleanedParts as $part) {
                $isAddress = false;

                // Patrones para detectar direcciones
                $addressPatterns = [
                    '/^\d+\s*-?\s*/', // Empieza con número (ej: "461", "123-125")
                    '/\b\d{1,5}\s*$/', // Termina con número de 1-5 dígitos
                    '/\s+\d{1,5}(\s*[a-z])?$/', // Termina con número y posible letra (ej: "Repetto 461", "Sarmiento 123A")
                    '/^(calle|avenida|av\.?|boulevard|blvd|pasaje|paseo|ruta|camino)\s+/i', // Empieza con indicador de vía
                    '/\s+(calle|avenida|av\.?|boulevard|blvd|pasaje|paseo|ruta|camino)(\s|$)/i', // Contiene indicador de vía
                    '/^[A-Za-záéíóúñü\s]+\s+\d{1,5}(\s*[a-z])?$/', // Patrón "Nombre de Calle + Número" (ej: "Domingo Repetto 461")
                ];

                foreach ($addressPatterns as $pattern) {
                    if (preg_match($pattern, $part)) {
                        $isAddress = true;
                        if ($debug) {
                            \Log::info("Detectado como dirección: '$part' con patrón: $pattern");
                        }
                        break;
                    }
                }

                // Solo agregar si NO es una dirección
                if (!$isAddress) {
                    $potentialParts[] = $part;
                    if ($debug) {
                        \Log::info("Agregado como potencial barrio/ciudad: '$part'");
                    }
                }
            }

            // Ahora trabajar con las partes que no son direcciones
            $numPotential = count($potentialParts);

            if ($numPotential == 0) {
                // Todas las partes parecían direcciones, usar las dos últimas partes originales
                if ($numParts >= 2) {
                    $barrio = $cleanedParts[$numParts - 2];
                    $localidad = $cleanedParts[$numParts - 1];
                }
            } elseif ($numPotential == 1) {
                // Solo una parte válida
                $part = $potentialParts[0];
                if ($this->looksLikeBarrio($part)) {
                    $barrio = $part;
                    $localidad = 'Buenos Aires';
                } else {
                    $localidad = $part;
                }
            } elseif ($numPotential >= 2) {
                // Múltiples partes válidas: tomar las dos últimas
                $barrio = $potentialParts[$numPotential - 2];
                $localidad = $potentialParts[$numPotential - 1];
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

    $finalResult = implode(', ', $result);

    // DEBUG TEMPORAL
    if ($debug) {
        \Log::info("Resultado final para '$location': '$finalResult'");
        \Log::info("Partes procesadas: " . json_encode($cleanedParts));
        \Log::info("Partes potenciales: " . json_encode($potentialParts ?? []));
    }

    return $finalResult;
}

/**
 * Método auxiliar para determinar si una cadena parece ser un barrio
 */
private function looksLikeBarrio($string)
{
    // Barrios conocidos de Buenos Aires y otras ciudades (incluyendo barrios de GBA)
    $barriosConocidos = [
        // Capital Federal
        'Palermo', 'Recoleta', 'Belgrano', 'Caballito', 'Flores',
        'Villa Crespo', 'San Telmo', 'La Boca', 'Puerto Madero',
        'Núñez', 'Saavedra', 'Colegiales', 'Almagro', 'Boedo',
        'Barracas', 'Constitución', 'Monserrat', 'Retiro', 'San Nicolás',
        'Balvanera', 'San Cristóbal', 'Parque Patricios', 'Nueva Pompeya',
        'Villa Urquiza', 'Villa Devoto', 'Villa del Parque', 'Villa Lugano',
        'Mataderos', 'Liniers', 'Villa Luro', 'Vélez Sarsfield',
        'Villa Soldati', 'Parque Avellaneda', 'Parque Chacabuco',
        'Chacarita', 'Paternal', 'Villa Ortúzar',
        'Agronomía', 'Monte Castro', 'Villa Real', 'Versalles',

        // GBA Norte
        'Martínez', 'San Isidro', 'Vicente López', 'Olivos', 'La Lucila',
        'Acassuso', 'Beccar', 'Florida', 'Munro', 'Villa Adelina',
        'Boulogne', 'San Fernando', 'Tigre', 'Victoria',

        // GBA Sur
        'Lomas de Zamora', 'Temperley', 'Banfield', 'Llavallol',
        'Adrogué', 'Burzaco', 'Longchamps', 'Almirante Brown',

        // GBA Oeste
        'Morón', 'Castelar', 'Ituzaingó', 'Ramos Mejía', 'La Matanza',
        'San Justo', 'Ciudad Madero', 'Villa Luzuriaga'
    ];

    // Normalizar el string de entrada
    $normalizedInput = trim($string);

    // Buscar coincidencia exacta o parcial con barrios conocidos
    foreach ($barriosConocidos as $barrio) {
        if (strcasecmp($normalizedInput, $barrio) === 0 ||
            stripos($normalizedInput, $barrio) !== false) {
            return true;
        }
    }

    // Patrones que sugieren que es un barrio
    if (preg_match('/\b(Villa|Barrio|Parque)\s+/i', $string)) {
        return true;
    }

    // Si no contiene números y no parece dirección, probablemente sea barrio
    if (!preg_match('/\d/', $string) &&
        !preg_match('/\b(calle|avenida|av\.?|boulevard|blvd|pasaje|paseo|ruta|camino)/i', $string)) {
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

/**
 * Método auxiliar para obtener ubicaciones limpias únicas
 */
private function getCleanLocations($baseQuery)
{
    $rawLocations = (clone $baseQuery)
        ->select('location', 'latitude', 'longitude')
        ->distinct()
        ->whereNotNull('location')
        ->where('location', '!=', '')
        ->get();

    $locations = collect();
    $addedLocations = []; // Para evitar duplicados

    foreach ($rawLocations as $loc) {
        $cleanedLocation = $this->cleanLocationString($loc->location);
        
        if (!empty(trim($cleanedLocation)) && !in_array($cleanedLocation, $addedLocations)) {
            $locations->push([
                'value' => $cleanedLocation,
                'display' => $cleanedLocation,
                'lat' => $loc->latitude,
                'lng' => $loc->longitude,
            ]);
            $addedLocations[] = $cleanedLocation;
        }
    }

    return $locations->sortBy('display')->values();
}
/**
 * Método auxiliar para obtener ubicaciones limpias únicas
 */


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