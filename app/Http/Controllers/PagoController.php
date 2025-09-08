<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Cena;
use Carbon\Carbon;

class PagoController extends Controller
{
    /**
     * Handle successful payment callback from MercadoPago
     */
   /**
 * Handle successful payment callback from MercadoPago
 */
public function pagoExito(Request $request, $codigoReserva): View|RedirectResponse
{
    Log::info('=== CALLBACK PAGO EXITOSO ===');
    Log::info('Código de reserva:', ['codigo' => $codigoReserva]);
    Log::info('Datos recibidos de MercadoPago:', $request->all());

    try {
        // Buscar la reserva
        $reserva = Reserva::with(['cena', 'user', 'cena.chef'])
            ->where('codigo_reserva', $codigoReserva)
            ->firstOrFail();

        Log::info('Reserva encontrada:', [
            'reserva_id' => $reserva->id,
            'estado_actual' => $reserva->estado,
            'estado_pago_actual' => $reserva->estado_pago
        ]);

        // Verificar si el pago ya fue procesado
        $pagoExistente = Pago::where('external_reference', $codigoReserva)
            ->where('payment_id', $request->payment_id)
            ->first();

        if ($pagoExistente) {
            Log::info('Pago ya procesado anteriormente:', ['pago_id' => $pagoExistente->id]);
            
            return view('pago.exito', [
                'reserva' => $reserva,
                'pago' => $pagoExistente,
                'yaProcessed' => true
            ]);
        }

        // Crear nuevo registro de pago usando el método del modelo
        $pago = Pago::crearDesdeMercadoPago($reserva, $request->all());

        // Si el pago fue aprobado, usar el método del modelo Reserva
        if ($request->status === 'approved') {
            $pago->update(['fecha_aprobacion' => now()]);
            
            // USAR EL MÉTODO DEL MODELO RESERVA
            $reserva->marcarComoPagada($request->payment_id, $request->all());
            
            // También confirmar la reserva
            $reserva->confirmar();

            // Actualizar contador de guests en la cena
            $reserva->cena->increment('guests_current', $reserva->cantidad_comensales);

            Log::info('Pago aprobado usando métodos del modelo:', [
                'pago_id' => $pago->id,
                'reserva_estado' => $reserva->fresh()->estado,
                'guests_added' => $reserva->cantidad_comensales
            ]);
        }

        return view('pago.exito', [
            'reserva' => $reserva,
            'pago' => $pago,
            'yaProcessed' => false
        ]);

    } catch (\Exception $e) {
        Log::error('Error procesando callback de pago exitoso:', [
            'codigo_reserva' => $codigoReserva,
            'error' => $e->getMessage(),
            'datos_request' => $request->all()
        ]);

        return redirect()->route('comensal.dashboard')
            ->with('error', 'Error procesando la confirmación de pago. Contacta al soporte.');
    }
}

    /**
     * Handle error payment callback from MercadoPago
     */
    public function pagoError(Request $request, $codigoReserva): RedirectResponse
    {
        Log::error('=== CALLBACK PAGO ERROR ===');
        Log::error('Código de reserva:', ['codigo' => $codigoReserva]);
        Log::error('Datos de error de MercadoPago:', $request->all());

        return redirect()->route('comensal.dashboard')
            ->with('error', 'El pago no pudo ser procesado. Tu reserva permanece pendiente.');
    }

    /**
     * Handle pending payment callback from MercadoPago
     */
    public function pagoPendiente(Request $request, $codigoReserva): RedirectResponse
    {
        Log::info('=== CALLBACK PAGO PENDIENTE ===');
        Log::info('Código de reserva:', ['codigo' => $codigoReserva]);
        Log::info('Datos de pago pendiente:', $request->all());

        return redirect()->route('comensal.dashboard')
            ->with('warning', 'Tu pago está siendo procesado. Te notificaremos cuando sea confirmado.');
    }


    public function dashboardNegocio()
{
    // Estadísticas generales
    $totalCenas = Cena::count();
    $totalReservas = Reserva::count();
    $reservasPagadas = Reserva::where('estado_pago', 'pagado')->count();
    $totalIngresos = Reserva::where('estado_pago', 'pagado')->sum('precio_total');
    
    // Ingresos por mes (últimos 6 meses)
    $ingresosPorMes = Reserva::where('estado_pago', 'pagado')
        ->whereDate('created_at', '>=', Carbon::now()->subMonths(6))
        ->selectRaw('MONTH(created_at) as mes, YEAR(created_at) as año, SUM(precio_total) as total')
        ->groupBy('año', 'mes')
        ->orderBy('año', 'desc')
        ->orderBy('mes', 'desc')
        ->get();
    
    // Cenas más populares (con más reservas)
    $cenasPopulares = Cena::withCount('reservas')
        ->orderBy('reservas_count', 'desc')
        ->limit(5)
        ->get();
    
    // Reservas recientes
    $reservasRecientes = Reserva::with(['cena', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    return view('admin.negocio.dashboard', compact(
        'totalCenas',
        'totalReservas', 
        'reservasPagadas',
        'totalIngresos',
        'ingresosPorMes',
        'cenasPopulares',
        'reservasRecientes'
    ));
}
}