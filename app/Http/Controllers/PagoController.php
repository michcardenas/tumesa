<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PagoController extends Controller
{
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

            // Crear nuevo registro de pago
            $pago = Pago::create([
                'reserva_id' => $reserva->id,
                'cena_id' => $reserva->cena_id,
                'user_id' => $reserva->user_id,
                'payment_id' => $request->payment_id,
                'collection_id' => $request->collection_id,
                'preference_id' => $request->preference_id,
                'merchant_order_id' => $request->merchant_order_id,
                'external_reference' => $request->external_reference,
                'status' => $request->status,
                'collection_status' => $request->collection_status,
                'payment_type' => $request->payment_type,
                'payment_method' => $request->payment_method ?? null,
                'monto_total' => $reserva->precio_total,
                'currency_id' => 'ARS',
                'datos_completos' => $request->all(),
                'processing_mode' => $request->processing_mode,
                'site_id' => $request->site_id,
                'fecha_pago' => now()
            ]);

            // Si el pago fue aprobado, actualizar la reserva
            if ($request->status === 'approved') {
                $pago->update(['fecha_aprobacion' => now()]);
                
                // Actualizar estado de la reserva
                $reserva->update([
                    'estado' => 'pagada',
                    'estado_pago' => 'pagado',
                    'fecha_confirmacion' => now()
                ]);

                // Actualizar contador de guests en la cena
                $reserva->cena->increment('guests_current', $reserva->cantidad_comensales);

                Log::info('Pago aprobado y reserva actualizada:', [
                    'pago_id' => $pago->id,
                    'reserva_estado' => 'pagada',
                    'guests_added' => $reserva->cantidad_comensales
                ]);
            }

            Log::info('Pago registrado exitosamente:', [
                'pago_id' => $pago->id,
                'status' => $pago->status,
                'monto' => $pago->monto_total
            ]);

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
}