<?php

namespace App\Http\Controllers;

use App\Models\Cena;
use App\Models\Reserva;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function show(Cena $cena)
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

        return view('chef.asistencia', compact('cena', 'reservas'));
    }
}