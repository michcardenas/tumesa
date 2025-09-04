<?php

namespace App\Http\Controllers;

use App\Models\Cena;
use App\Models\Reserva;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{public function show(Cena $cena)
{
    // Verificar que la cena pertenece al chef autenticado
    if ($cena->user_id !== auth()->id()) {
        abort(403, 'No tienes permisos para acceder a esta cena.');
    }

    // Obtener todas las reservas confirmadas de esta cena
    $reservas = Reserva::where('cena_id', $cena->id)
        ->whereIn('estado', ['confirmada', 'pagada'])
        ->with('comensal')
        ->orderBy('created_at')
        ->get();

    // Calcular estadísticas de asistencia
    $stats = [
        'total_reservas' => $reservas->count(),
        'total_comensales' => $reservas->sum('cantidad_comensales'),
        'asistencia_marcada' => $reservas->where('asistencia_marcada', true)->count(),
        'presentes' => $reservas->where('estado_asistencia', 'presente')->count(),
        'ausentes' => $reservas->where('estado_asistencia', 'ausente')->count(),
        'pendientes' => $reservas->where('estado_asistencia', 'pendiente')->count(),
        'comensales_presentes' => $reservas->where('estado_asistencia', 'presente')->sum('cantidad_comensales'),
        'comensales_ausentes' => $reservas->where('estado_asistencia', 'ausente')->sum('cantidad_comensales')
    ];

    return view('chef.asistencia', compact('cena', 'reservas', 'stats'));
}
}