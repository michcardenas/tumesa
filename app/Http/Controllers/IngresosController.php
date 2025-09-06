<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cena;
use App\Models\Reserva;
use App\Models\Pago;


class IngresosController extends Controller
{
   public function index()
{
    $user = Auth::user();
    
    // Verificación de permisos (similar al ChefController)
    $hasChefRole = false;
    
    if ($user->role === 'chef_anfitrion') {
        $hasChefRole = true;
    } elseif (method_exists($user, 'hasRole')) {
        if ($user->hasRole('chef') || $user->hasRole('chef_anfitrion')) {
            $hasChefRole = true;
        }
    }
    
    if (!$hasChefRole) {
        abort(403, 'No tienes acceso a esta sección.');
    }

    // Obtener datos de ingresos
    $userId = $user->id;
    $currentMonth = now()->startOfMonth();
    $nextMonth = now()->addMonth()->startOfMonth();
    $previousMonth = now()->subMonth()->startOfMonth();
    $currentMonthEnd = now()->endOfMonth();

    // Ingresos del mes actual (basado en reservas pagadas)
    $ingresosMesActual = Reserva::whereHas('cena', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereHas('cena', function($query) use ($currentMonth, $nextMonth) {
            $query->whereBetween('datetime', [$currentMonth, $nextMonth]);
        })
        ->where('estado_pago', 'pagado')
        ->sum('precio_total');

    // Ingresos del mes anterior para cálculo de crecimiento
    $ingresosMesAnterior = Reserva::whereHas('cena', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereHas('cena', function($query) use ($previousMonth, $currentMonth) {
            $query->whereBetween('datetime', [$previousMonth, $currentMonth]);
        })
        ->where('estado_pago', 'pagado')
        ->sum('precio_total');

    // Calcular crecimiento
    $crecimiento = 0;
    if ($ingresosMesAnterior > 0) {
        $crecimiento = (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100;
    } elseif ($ingresosMesActual > 0) {
        $crecimiento = 100; // Si no había ingresos antes y ahora sí
    }

    // Ingresos pendientes (cenas futuras con reservas confirmadas/pagadas)
    $ingresosPendientes = Reserva::whereHas('cena', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('datetime', '>', now());
        })
        ->whereIn('estado', ['confirmada', 'pagada'])
        ->sum('precio_total');

    // Total acumulado histórico
    $totalAcumulado = Reserva::whereHas('cena', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('estado_pago', 'pagado')
        ->sum('precio_total');

    // Contadores
    $cenasMesActual = Cena::where('user_id', $userId)
        ->whereBetween('datetime', [$currentMonth, $nextMonth])
        ->whereHas('reservas', function($query) {
            $query->where('estado_pago', 'pagado');
        })
        ->count();

    $cenasPendientes = Cena::where('user_id', $userId)
        ->where('datetime', '>', now())
        ->where('status', 'published')
        ->whereHas('reservas', function($query) {
            $query->whereIn('estado', ['confirmada', 'pagada']);
        })
        ->count();

    // Historial de reservas por mes (últimos 6 meses)
    $historialMeses = [];
    for ($i = 5; $i >= 0; $i--) {
        $mesInicio = now()->subMonths($i)->startOfMonth();
        $mesFin = now()->subMonths($i)->endOfMonth();
        
        $ingresosMes = Reserva::whereHas('cena', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('cena', function($query) use ($mesInicio, $mesFin) {
                $query->whereBetween('datetime', [$mesInicio, $mesFin]);
            })
            ->where('estado_pago', 'pagado')
            ->sum('precio_total');
            
        $reservasMes = Reserva::whereHas('cena', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('cena', function($query) use ($mesInicio, $mesFin) {
                $query->whereBetween('datetime', [$mesInicio, $mesFin]);
            })
            ->where('estado_pago', 'pagado')
            ->count();

        $historialMeses[] = [
            'mes' => $mesInicio->format('M Y'),
            'ingresos' => $ingresosMes,
            'reservas' => $reservasMes
        ];
    }

    // Últimas reservas (últimas 10 reservas pagadas)
    $ultimasReservas = Reserva::with(['cena', 'user'])
        ->whereHas('cena', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('estado_pago', 'pagado')
        ->orderBy('fecha_pago', 'desc')
        ->limit(10)
        ->get();

    // Resumen de reservas por estado
    $resumenReservas = [
        'pagadas' => Reserva::whereHas('cena', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('estado_pago', 'pagado')
            ->count(),
            
        'pendientes' => Reserva::whereHas('cena', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('estado', 'pendiente')
            ->count(),
            
        'canceladas' => Reserva::whereHas('cena', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('estado', 'cancelada')
            ->count(),
    ];

    $ingresos = [
        'mes' => $ingresosMesActual,
        'pendientes' => $ingresosPendientes,
        'total' => $totalAcumulado,
        'cenas_mes' => $cenasMesActual,
        'cenas_pendientes' => $cenasPendientes,
        'crecimiento' => round($crecimiento, 1)
    ];

    return view('chef.ingresos', compact(
        'ingresos', 
        'user', 
        'historialMeses', 
        'ultimasReservas',
        'resumenReservas'
    ));
}
}