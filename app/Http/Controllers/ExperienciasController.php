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
            // Normalizar nombres de provincia en inglés
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
        $cleanPart = preg_replace('/\b[A-Z]\d{4}[A-Z]{3}\b/', '', $cleanPart); // Códigos postales argentinos
        $cleanPart = preg_replace('/\b[A-Z0-9]{4,8}\b/', '', $cleanPart); // Otros códigos
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
    
    // Analizar las partes limpias para determinar qué es cada cosa
    // Generalmente el orden es: Calle, Barrio, Localidad/Ciudad, Provincia, País
    $numParts = count($cleanedParts);
    
    if ($numParts > 0) {
        // La lógica varía según la cantidad de partes
        if ($numParts == 1) {
            // Solo una parte: probablemente sea barrio o localidad
            $localidad = $cleanedParts[0];
        } elseif ($numParts == 2) {
            // Dos partes: probablemente barrio y localidad
            $barrio = $cleanedParts[0];
            $localidad = $cleanedParts[1];
        } elseif ($numParts >= 3) {
            // Tres o más partes
            // La primera puede ser calle (la ignoramos si tiene formato de dirección)
            $startIndex = 0;
            
            // Verificar si la primera parte parece una calle
            if (preg_match('/^[A-Z][a-záéíóú]+(\s+[A-Z][a-záéíóú]+)*$/', $cleanedParts[0]) ||
                preg_match('/\b(calle|avenida|av\.|boulevard|blvd|pasaje|paseo)/i', $cleanedParts[0])) {
                $direccion = $cleanedParts[0];
                $startIndex = 1;
            }
            
            // Asignar el resto
            $remainingParts = array_slice($cleanedParts, $startIndex);
            if (count($remainingParts) == 1) {
                $localidad = $remainingParts[0];
            } elseif (count($remainingParts) == 2) {
                $barrio = $remainingParts[0];
                $localidad = $remainingParts[1];
            } else {
                // Tomar los dos últimos elementos más relevantes
                $barrio = $remainingParts[count($remainingParts) - 2];
                $localidad = $remainingParts[count($remainingParts) - 1];
            }
        }
    }
    
    // Si tenemos partido pero no localidad, usar el partido como localidad
    if ($partido && !$localidad) {
        $localidad = $partido;
    } elseif ($partido && $localidad && strcasecmp($partido, $localidad) !== 0) {
        // Si el partido es diferente a la localidad, puede ser más específico
        if (!$barrio) {
            $barrio = $localidad;
            $localidad = $partido;
        }
    }
    
    // Construir el resultado final - SIEMPRE devolver dos partes
    $result = [];
    
    // Determinar la primera parte (barrio/localidad)
    if ($barrio) {
        $result[] = ucwords(strtolower($barrio));
    } elseif ($localidad) {
        $result[] = ucwords(strtolower($localidad));
    } elseif ($partido) {
        $result[] = ucwords(strtolower($partido));
    }
    
    // Determinar la segunda parte (ciudad/provincia)
    // SIEMPRE agregar una segunda parte
    if (count($result) > 0) {
        $secondPart = null;
        
        if ($barrio && $localidad && strcasecmp($barrio, $localidad) !== 0) {
            // Si tenemos barrio Y localidad diferentes, usar localidad como segunda parte
            $secondPart = ucwords(strtolower($localidad));
        } elseif ($partido && (!$localidad || strcasecmp($partido, $localidad) !== 0)) {
            // Si tenemos partido diferente a localidad
            $secondPart = ucwords(strtolower($partido));
        } elseif ($provincia) {
            // Si tenemos provincia
            if ($provincia === 'CABA' || stripos($provincia, 'Ciudad Autónoma') !== false) {
                $secondPart = 'Buenos Aires';
            } else {
                $secondPart = ucwords(strtolower($provincia));
            }
        } else {
            // Por defecto, asumir Buenos Aires si no hay más contexto
            $secondPart = 'Buenos Aires';
        }
        
        // Agregar segunda parte solo si es diferente a la primera
        if ($secondPart && strcasecmp($result[0], $secondPart) !== 0) {
            $result[] = $secondPart;
        } else {
            // Si son iguales o no hay segunda parte, agregar Buenos Aires por defecto
            $result[] = 'Buenos Aires';
        }
    }
    
    // Si no pudimos determinar nada, devolver vacío
    if (count($result) === 0) {
        return '';
    }
    
    // Asegurar que siempre tengamos exactamente 2 partes
    if (count($result) === 1) {
        $result[] = 'Buenos Aires';
    }
    
    return implode(', ', $result);
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