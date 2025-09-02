<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;  // Agregado esta línea
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
   

    public function historial(Request $request)
    {
        // Obtener las reservas del usuario autenticado
        $reservas = Reserva::where('user_id', Auth::id())
            ->with(['cena.user']) // Cargar la cena y el chef
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Estadísticas rápidas
        $estadisticas = [
            'total' => Reserva::where('user_id', Auth::id())->count(),
            'confirmadas' => Reserva::where('user_id', Auth::id())->confirmadas()->count(),
            'pagadas' => Reserva::where('user_id', Auth::id())->pagadas()->count(),
            'canceladas' => Reserva::where('user_id', Auth::id())->canceladas()->count(),
            'completadas' => Reserva::where('user_id', Auth::id())->completadas()->count(),
        ];

        return view('reservas.historial', compact('reservas', 'estadisticas'));
    }
}