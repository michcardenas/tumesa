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

    // Valores de filtros desde la URL
    $q        = trim((string) $request->input('q', ''));
    $city     = trim((string) $request->input('city', ''));
    $gMin     = $request->integer('guests', 0);
    $pMin     = $request->integer('price_min', $minPrice);
    $pMax     = $request->integer('price_max', $maxPrice);
    $sort     = $request->input('sort', 'date'); // date|price_asc|price_desc

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

    if ($city !== '') {
        $query->where('location', 'like', "%{$city}%");
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

    // 🔥 Opciones para selects (ciudades limpias)
    $rawCities = (clone $base)
        ->select('location')
        ->distinct()
        ->orderBy('location')
        ->pluck('location')
        ->take(100);

    // Limpiar direcciones → solo barrio/localidad
    $cities = $rawCities->map(function ($loc) {
        $parts = explode(',', $loc);

        // Normalmente Google entrega: [calle, barrio, ciudad, provincia, país]
        $candidate = $parts[1] ?? $parts[0]; 
        $candidate = $parts[2] ?? $candidate; // fallback a posición 2 si existe

        // Quitar códigos postales (ej: B1636AAF)
        $clean = preg_replace('/\b[A-Z0-9]{4,}\b/', '', $candidate);

        return trim($clean);
    })->unique()->values();

    return view('experiencias.index', [
        'cenas'     => $cenas,
        'minPrice'  => $minPrice,
        'maxPrice'  => $maxPrice,
        'cities'    => $cities,
        'filters'   => [
            'q'         => $q,
            'city'      => $city,
            'guests'    => $gMin,
            'price_min' => $pMin,
            'price_max' => $pMax,
            'sort'      => $sort,
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
