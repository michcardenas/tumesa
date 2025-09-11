<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cena;
use App\Models\Pagina;

class ExperienciasController extends Controller
{
    /**
     * Muestra la página de experiencias.
     * Por ahora solo retorna la vista (que crearás luego).
     */
public function index(Request $request)
{
    // Base: solo experiencias activas, publicadas y futuras
    $base = Cena::query()->active()->published()->upcoming()->with('chef');

    // Estadísticas base para la UI
    $minPrice = (int) floor((clone $base)->min('price') ?? 0);
    $maxPrice = (int) ceil((clone $base)->max('price') ?? 0);

    // Filtros
    $q     = trim((string) $request->input('q', ''));
    $city  = trim((string) $request->input('city', ''));      // será "Ciudad" o "Ciudad / Barrio"
    $gMin  = $request->integer('guests', 0);
    $pMin  = $request->integer('price_min', $minPrice);
    $pMax  = $request->integer('price_max', $maxPrice);
    $sort  = $request->input('sort', 'date'); // date|price_asc|price_desc

    // Geo (para seleccionar cercanía si no se elige city)
    // Puedes enviar lat/lng desde la vista como inputs hidden, o desde un autocomplete
    $latReq = $request->has('lat') ? (float) $request->input('lat') : null;
    $lngReq = $request->has('lng') ? (float) $request->input('lng') : null;

    // ===== Helpers de parsing de location =====
    $parseLocation = function (?string $loc): array {
        // Esperamos algo tipo "calle, barrio, ciudad, provincia, país"
        // Robustecemos: quitamos Provincia/País comunes de AR
        $loc = (string) $loc;
        $parts = array_map('trim', explode(',', $loc));

        // Limpiar “Provincia de Buenos Aires”, “Ciudad Autónoma de Buenos Aires”, “CABA”, “Argentina”, CPs
        $cleanPart = function ($s) {
            $s = preg_replace('/\b[A-Z0-9]{4,}\b/u', '', $s); // quita códigos postales
            $s = str_ireplace([
                'Provincia de Buenos Aires',
                'Ciudad Autónoma de Buenos Aires',
                'CABA',
                'Argentina',
            ], '', $s);
            return trim(preg_replace('/\s+/', ' ', $s));
        };

        $parts = array_values(array_filter(array_map($cleanPart, $parts), fn($v) => $v !== ''));

        // Heurística: ciudad suele venir en penúltimo o antepenúltimo lugar; barrio antes de ciudad
        $count = count($parts);
        $city  = $count >= 2 ? $parts[$count-2] : ($parts[$count-1] ?? '');
        $barrio = $count >= 3 ? $parts[$count-3] : '';

        // Si “ciudad” quedó vacía o repetida, reintenta con posiciones previas
        if ($city === '' && $count >= 1) $city = $parts[$count-1];

        // Evitar ciudad=barrio idénticos
        if ($barrio === $city) $barrio = '';

        return [
            'city'   => $city,
            'barrio' => $barrio,
        ];
    };

    // ===== Construir catálogo de ubicaciones únicas (para el select) =====
    // Traemos location (+ lat/lng si existen) y armamos opciones "Ciudad / Barrio"
    $raw = (clone $base)
        ->select(['location', 'lat', 'lng'])
        ->whereNotNull('location')
        ->distinct()
        ->get();

    $options = $raw->map(function ($row) use ($parseLocation) {
        $parsed = $parseLocation($row->location);
        $city   = $parsed['city'];
        $barrio = $parsed['barrio'];

        $label = $city;
        if ($barrio !== '') {
            $label = $city.' / '.$barrio;
        }

        return [
            'label' => $label,                // lo que mostramos en el <option>
            'city'  => $city,                 // para filtrar por ciudad
            'barrio'=> $barrio,               // para filtrar por barrio
            'lat'   => $row->lat ?? null,
            'lng'   => $row->lng ?? null,
        ];
    })
    // Normalizar y deduplicar por label
    ->filter(fn($it) => $it['label'] !== '')
    ->unique('label')
    ->values();

    // ===== Si no se seleccionó city pero viene lng/lat, sugerir la opción más cercana =====
    $selectedLabel = $city; // lo que llegará al select como value
    if ($selectedLabel === '' && ($lngReq !== null || ($latReq !== null && $lngReq !== null)) && $options->count() > 0) {
        // Si tenemos lat+lng: Haversine; si solo lng: mínima diferencia abs(lng - lngReq)
        $deg2rad = fn($deg) => $deg * M_PI / 180;

        $nearest = $options->map(function ($op) use ($latReq, $lngReq, $deg2rad) {
            $dist = INF;
            if ($latReq !== null && $lngReq !== null && $op['lat'] !== null && $op['lng'] !== null) {
                // Haversine en km
                $R = 6371;
                $dLat = $deg2rad(($op['lat'] ?? 0) - $latReq);
                $dLon = $deg2rad(($op['lng'] ?? 0) - $lngReq);
                $a = sin($dLat/2) ** 2
                   + cos($deg2rad($latReq)) * cos($deg2rad($op['lat'] ?? 0)) * sin($dLon/2) ** 2;
                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                $dist = $R * $c;
            } elseif ($lngReq !== null && $op['lng'] !== null) {
                // Distancia 1D por longitud como fallback
                $dist = abs($op['lng'] - $lngReq);
            }
            return [$dist, $op];
        })
        ->sortBy(fn($x) => $x[0])
        ->first();

        if ($nearest) {
            $selectedLabel = $nearest[1]['label'];
        }
    }

    // ===== Aplicar filtros a la consulta =====
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

    // Filtro por city/barrio desde el label "Ciudad / Barrio"
    if ($selectedLabel !== '') {
        // Reconstruimos city y barrio desde el label
        [$labelCity, $labelBarrio] = array_map('trim', array_pad(explode('/', $selectedLabel, 2), 2, ''));
        // Coincidencia flexible sobre location
        $query->where(function ($w) use ($labelCity, $labelBarrio) {
            if ($labelCity !== '') {
                $w->where('location', 'like', "%{$labelCity}%");
            }
            if ($labelBarrio !== '') {
                $w->where('location', 'like', "%{$labelBarrio}%");
            }
        });
    }

    if ($gMin > 0) {
        $query->where('guests_max', '>=', $gMin);
    }

    // Precio
    if ($pMin > $pMax) { [$pMin, $pMax] = [$pMax, $pMin]; }
    $query->whereBetween('price', [$pMin, $pMax]);

    // Orden
    switch ($sort) {
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        default: // 'date'
            $query->orderBy('datetime', 'asc');
            break;
    }

    // Paginación
    $cenas = $query->paginate(12)->appends($request->query());

    // Para el select en la vista
    $cities = $options->pluck('label')->sort()->values();

    return view('experiencias.index', [
        'cenas'     => $cenas,
        'minPrice'  => $minPrice,
        'maxPrice'  => $maxPrice,
        'cities'    => $cities,
        'filters'   => [
            'q'         => $q,
            'city'      => $selectedLabel,   // 👈 usamos la elegida o la más cercana
            'guests'    => $gMin,
            'price_min' => $pMin,
            'price_max' => $pMax,
            'sort'      => $sort,
            // Pasamos lat/lng de vuelta por si quieres persistirlos en el form
            'lat'       => $latReq,
            'lng'       => $lngReq,
        ],
    ]);
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
