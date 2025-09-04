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

    public function marcarAsistencia(Request $request, Reserva $reserva)
    {
        // Verificar que la reserva pertenece a una cena del chef autenticado
        if ($reserva->cena->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        // Validar entrada
        $request->validate([
            'estado' => 'required|in:presente,ausente',
            'comentarios' => 'nullable|string|max:500'
        ]);

        $estado = $request->input('estado');
        $comentarios = $request->input('comentarios');

        try {
            // Marcar asistencia según el estado usando los métodos del modelo
            if ($estado === 'presente') {
                $reserva->marcarPresente($comentarios);
            } else {
                $reserva->marcarAusente($comentarios);
            }

            // Recargar el modelo para obtener los datos actualizados
            $reserva->refresh();

            return response()->json([
                'success' => true, 
                'message' => 'Asistencia marcada correctamente',
                'estado' => $reserva->estado_asistencia,
                'badge' => $reserva->estado_asistencia_badge,
                'fecha_marcada' => $reserva->fecha_asistencia_marcada->format('H:i'),
                'comentarios' => $reserva->comentarios_asistencia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al marcar asistencia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetearAsistencia(Request $request, Reserva $reserva)
    {
        // Verificar que la reserva pertenece a una cena del chef autenticado
        if ($reserva->cena->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        try {
            // Resetear asistencia
            $reserva->update([
                'asistencia_marcada' => false,
                'estado_asistencia' => 'pendiente',
                'fecha_asistencia_marcada' => null,
                'comentarios_asistencia' => null
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Asistencia reseteada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al resetear asistencia: ' . $e->getMessage()
            ], 500);
        }
    }
}