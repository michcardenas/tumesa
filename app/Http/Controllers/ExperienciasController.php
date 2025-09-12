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

        // FILTRO DE UBICACIÓN INTELIGENTE
        if ($city !== '') {
            \Log::info('Buscando ciudad: ' . $city);
            
            // Búsqueda inteligente por partes de la ubicación
            $query->where(function($q) use ($city) {
                // Buscar el texto completo
                $q->where('location', 'like', "%{$city}%");
                
                // También buscar por partes separadas por comas
                $parts = explode(',', $city);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if (!empty($part)) {
                        $q->orWhere('location', 'like', "%{$part}%");
                    }
                }
                
                // Búsquedas específicas para casos comunes
                if (stripos($city, 'Ciudad Jardín Lomas del Palomar') !== false) {
                    $q->orWhere('location', 'like', "%Lomas del Palomar%")
                      ->orWhere('location', 'like', "%Ciudad Jardín%");
                }
                
                if (stripos($city, 'Buenos Aires') !== false) {
                    $q->orWhere('location', 'like', "%Buenos Aires%")
                      ->orWhere('location', 'like', "%CABA%");
                }
            });
            
            \Log::info('Resultados encontrados: ' . $query->count());
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

        // UBICACIONES PARA EL FILTRO - VERSIÓN CORREGIDA
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
     * VERSIÓN CORREGIDA - Obtener ubicaciones para el filtro
     * Problema: estaba limpiando las ubicaciones para mostrar, 
     * pero debe mostrar exactamente lo que está en la DB para que coincida la búsqueda
     */
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
            // OPCIÓN 1: Usar la ubicación tal como está en la DB (más confiable)
            $locations->push([
                'value' => $loc->location, // Usar el valor original
                'display' => $this->cleanLocationForDisplay($loc->location), // Solo limpiar para mostrar
                'lat' => $loc->latitude,
                'lng' => $loc->longitude,
            ]);
        }

        return $locations->unique('value')->values();
    }

    /**
     * Nueva función: limpiar SOLO para mostrar, no para filtrar
     */
    private function cleanLocationForDisplay($location)
    {
        if (empty($location)) return '';
        
        // Limpiezas básicas para mostrar mejor
        $location = preg_replace('/^\d+\s*-?\s*/', '', $location); // Quitar números de dirección
        $location = preg_replace('/,\s*Argentina$/', '', $location); // Quitar ", Argentina" al final
        
        return trim($location);
    }

    /**
     * Agregar filtro de proximidad geográfica
     */
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

    public function serChef()
    {
        $contenidosSerChef = \App\Models\Pagina::porPagina('ser-chef')->get()->keyBy('clave');
        return view('experiencias.ser-chef', ['contenidos' => $contenidosSerChef]);
    }

    public function comoFunciona()
    {
        $contenidosComoFunciona = \App\Models\Pagina::porPagina('como-funciona')->get()->keyBy('clave');
        return view('experiencias.como-funciona', ['contenidos' => $contenidosComoFunciona]);
    }
}