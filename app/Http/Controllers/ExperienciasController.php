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

    // ✅ FILTRO DE UBICACIÓN CORREGIDO
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

    // ✅ DEBUG: Agregar logs temporales para verificar filtros
    if ($city !== '') {
        \Log::info("Filtro de ubicación aplicado", [
            'city_filter' => $city,
            'city_parts' => array_map('trim', explode(',', $city)),
            'total_before_filter' => (clone $base)->count(),
        ]);
    }

    // Paginación
    $cenas = $query->paginate(12)->appends($request->query());

    // ✅ DEBUG: Log final results
    \Log::info("Resultados de filtro", [
        'total_results' => $cenas->total(),
        'filters_applied' => array_filter([
            'q' => $q ?: null,
            'city' => $city ?: null,
            'guests' => $gMin ?: null,
            'price_range' => ($pMin != $minPrice || $pMax != $maxPrice) ? "{$pMin}-{$pMax}" : null,
        ]),
    ]);

    // Agregar ubicaciones limpias a cada cena
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



// ✅ Agregar filtro de proximidad (sin cambios)
private function addProximityFilter($query, $lat, $lng, $radiusKm)
{
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

// ✅ Agregar cálculo de distancia (sin cambios)
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