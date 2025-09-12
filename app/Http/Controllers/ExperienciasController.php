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
        
        $parts = explode(',', $location);
        
        // Limpiar cada parte
        $cleanedParts = [];
        foreach ($parts as $part) {
            $part = trim($part);
            
            // Saltar partes vacías
            if (empty($part)) continue;
            
            // Saltar números de dirección al inicio (ej: "700 - Aviador Plüschow")
            $part = preg_replace('/^\d+\s*-?\s*/', '', $part);
            
            // Saltar códigos postales
            $part = preg_replace('/\b[A-Z0-9]{4,}\b/', '', $part);
            
            // Normalizar nombres de ciudades
            if (in_array(strtolower($part), ['caba', 'ciudad autónoma de buenos aires'])) {
                $part = 'Buenos Aires';
            }
            
            // Saltar términos que no queremos
            if (in_array($part, ['Argentina', 'Provincia de Buenos Aires'])) {
                continue;
            }
            
            $part = trim($part);
            if (!empty($part)) {
                $cleanedParts[] = $part;
            }
        }
        
        // Definir barrios/zonas conocidas
        $knownNeighborhoods = [
            'Palermo Hollywood', 'Palermo', 'Recoleta', 'Puerto Madero', 'San Telmo',
            'La Boca', 'Belgrano', 'Villa Crespo', 'Colegiales', 'Núñez', 'Barracas',
            'Caballito', 'Flores', 'Villa Urquiza', 'Villa Devoto', 'Retiro',
            'Ciudad Jardín Lomas del Palomar', 'Lomas del Palomar', 'El Paraíso'
        ];
        
        // Definir ciudades conocidas
        $knownCities = [
            'Buenos Aires', 'Vicente López', 'San Isidro', 'Tigre', 'La Plata',
            'Partido de Ramallo', 'Quilmes', 'Avellaneda', 'Lanús', 'Lomas de Zamora'
        ];
        
        $neighborhood = null;
        $city = null;
        
        // Buscar barrio y ciudad en las partes
        foreach ($cleanedParts as $part) {
            // Verificar si es un barrio conocido
            foreach ($knownNeighborhoods as $knownNeighborhood) {
                if (stripos($part, $knownNeighborhood) !== false) {
                    $neighborhood = $knownNeighborhood;
                    break;
                }
            }
            
            // Verificar si es una ciudad conocida
            foreach ($knownCities as $knownCity) {
                if (stripos($part, $knownCity) !== false) {
                    $city = $knownCity;
                    break;
                }
            }
        }
        
        // Si no encontramos barrio/ciudad específicos, usar heurística
        if (!$neighborhood && !$city && count($cleanedParts) >= 2) {
            // La penúltima parte suele ser el barrio, la última la ciudad
            $neighborhood = $cleanedParts[count($cleanedParts) - 2];
            $city = $cleanedParts[count($cleanedParts) - 1];
        } elseif (!$neighborhood && !$city && count($cleanedParts) == 1) {
            // Solo una parte, asumir que es barrio
            $neighborhood = $cleanedParts[0];
        }
        
        // Construir resultado
        if ($neighborhood && $city) {
            return "{$neighborhood}, {$city}";
        } elseif ($neighborhood) {
            return $neighborhood;
        } elseif ($city) {
            return $city;
        }
        
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