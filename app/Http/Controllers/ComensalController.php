<?php

namespace App\Http\Controllers;

use App\Models\Cena;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ComensalController extends Controller
{
    /**
     * Display the comensal dashboard.
     */
  public function dashboard(): View
{
    $user = Auth::user();
    
    Log::info('=== ACCESO A DASHBOARD COMENSAL ===');
    Log::info('Comensal accediendo a dashboard:', [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ]);

    // Traer cenas disponibles (activas, publicadas y próximas)
    $cenasDisponibles = Cena::with('chef')
        ->active()
        ->published()
        ->upcoming()
        ->orderBy('datetime')
        ->limit(6)
        ->get();

    // Cenas recomendadas (las más populares o recientes)
    $cenasRecomendadas = Cena::with('chef')
        ->active()
        ->published() 
        ->upcoming()
        ->where('guests_current', '>', 0) // Que tengan al menos una reserva
        ->orderBy('guests_current', 'desc') // Las más populares primero
        ->limit(2)
        ->get();

    // Si no hay cenas populares, traer las más recientes
    if ($cenasRecomendadas->isEmpty()) {
        $cenasRecomendadas = Cena::with('chef')
            ->active()
            ->published()
            ->upcoming()
            ->latest('created_at')
            ->limit(2)
            ->get();
    }

    Log::info('Cenas disponibles encontradas: ' . $cenasDisponibles->count());
    Log::info('Cenas recomendadas encontradas: ' . $cenasRecomendadas->count());

    return view('comensal.dashboard', compact('user', 'cenasDisponibles', 'cenasRecomendadas'));
}

public function checkout(Cena $cena): View|RedirectResponse
    {
        $user = Auth::user();
        
        Log::info('=== ACCESO A CHECKOUT COMENSAL ===');
        Log::info('Iniciando proceso de reserva:', [
            'comensal_id' => $user->id,
            'cena_id' => $cena->id,
            'cena_titulo' => $cena->title,
            'chef' => $cena->chef->name,
        ]);

        // Verificar que la cena esté disponible
        if (!$cena->is_active || $cena->status !== 'published') {
            Log::warning('Intento de reserva en cena no disponible:', [
                'cena_id' => $cena->id,
                'is_active' => $cena->is_active,
                'status' => $cena->status
            ]);
            
            return redirect()->route('comensal.dashboard')
                ->with('error', 'Esta cena ya no está disponible para reservas.');
        }

        // Verificar que no esté llena
        if ($cena->is_full) {
            Log::warning('Intento de reserva en cena completa:', [
                'cena_id' => $cena->id,
                'guests_current' => $cena->guests_current,
                'guests_max' => $cena->guests_max
            ]);
            
            return redirect()->route('comensal.dashboard')
                ->with('error', 'Esta cena ya está completa. No hay lugares disponibles.');
        }

        // Verificar que la cena sea en el futuro
        if ($cena->datetime <= now()) {
            Log::warning('Intento de reserva en cena pasada:', [
                'cena_id' => $cena->id,
                'datetime' => $cena->datetime,
                'now' => now()
            ]);
            
            return redirect()->route('comensal.dashboard')
                ->with('error', 'Esta cena ya pasó. No se pueden hacer más reservas.');
        }

        // Verificar que el usuario no sea el chef de esta cena
        if ($cena->user_id === $user->id) {
            Log::warning('Chef intenta reservar su propia cena:', [
                'chef_id' => $user->id,
                'cena_id' => $cena->id
            ]);
            
            return redirect()->route('comensal.dashboard')
                ->with('error', 'No puedes reservar tu propia cena.');
        }

        // Verificar que no tenga ya una reserva activa para esta cena
        $reservaExistente = Reserva::where('user_id', $user->id)
            ->where('cena_id', $cena->id)
            ->whereIn('estado', ['pendiente', 'confirmada', 'pagada'])
            ->first();

        if ($reservaExistente) {
            Log::warning('Usuario intenta reservar cena que ya tiene reservada:', [
                'user_id' => $user->id,
                'cena_id' => $cena->id,
                'reserva_id' => $reservaExistente->id,
                'estado_actual' => $reservaExistente->estado
            ]);
            
            return redirect()->route('comensal.dashboard')
                ->with('error', 'Ya tienes una reserva activa para esta cena.');
        }

        Log::info('Checkout validado correctamente - Mostrando formulario de reserva');

        return view('comensal.checkout', compact('cena', 'user'));
    }

/**
 * Process dinner reservation and redirect to payment gateway.
 */
public function procesarReserva(Request $request): RedirectResponse
{
    $user = Auth::user();
    
    Log::info('=== PROCESANDO RESERVA ===');
    Log::info('Datos recibidos:', $request->all());

    // Validar datos del formulario
    $validatedData = $request->validate([
        'cena_id' => 'required|exists:cenas,id',
        'cantidad_comensales' => 'required|integer|min:1|max:20',
        'nombre_contacto' => 'required|string|max:255',
        'telefono_contacto' => 'required|string|max:20',
        'email_contacto' => 'required|email|max:255',
        'restricciones_alimentarias' => 'nullable|string|max:1000',
        'solicitudes_especiales' => 'nullable|string|max:1000', 
        'comentarios_especiales' => 'nullable|string|max:1000',
        'acepta_terminos' => 'required',
        'acepta_politica_cancelacion' => 'required'
    ]);

    try {
        // Obtener la cena
        $cena = Cena::with('chef')->findOrFail($validatedData['cena_id']);
        
        // Validaciones adicionales
        if (!$cena->is_active || $cena->status !== 'published') {
            return redirect()->route('comensal.dashboard')
                ->with('error', 'Esta cena ya no está disponible.');
        }

        if ($cena->available_spots < $validatedData['cantidad_comensales']) {
            return redirect()->route('comensal.dashboard')
                ->with('error', 'No hay suficientes lugares disponibles.');
        }

        // Calcular precios
        $precioPorPersona = $cena->price;
        $precioTotal = $precioPorPersona * $validatedData['cantidad_comensales'];

        // CORRECCIÓN: Convertir checkboxes a booleanos
        $aceptaTerminos = $request->has('acepta_terminos') && $request->acepta_terminos == 'on';
        $aceptaPolitica = $request->has('acepta_politica_cancelacion') && $request->acepta_politica_cancelacion == 'on';

        // Crear la reserva en la base de datos
        $reserva = Reserva::create([
            'cena_id' => $cena->id,
            'user_id' => $user->id,
            'cantidad_comensales' => $validatedData['cantidad_comensales'],
            'precio_por_persona' => $precioPorPersona,
            'precio_total' => $precioTotal,
            'estado' => 'pendiente',
            'estado_pago' => 'pendiente',
            'nombre_contacto' => $validatedData['nombre_contacto'],
            'telefono_contacto' => $validatedData['telefono_contacto'],
            'email_contacto' => $validatedData['email_contacto'],
            'restricciones_alimentarias' => $validatedData['restricciones_alimentarias'],
            'solicitudes_especiales' => $validatedData['solicitudes_especiales'],
            'comentarios_especiales' => $validatedData['comentarios_especiales'],
            'acepta_terminos' => $aceptaTerminos,  // CORREGIDO
            'acepta_politica_cancelacion' => $aceptaPolitica,  // CORREGIDO
            'fecha_reserva' => now()
        ]);

        Log::info('Reserva creada exitosamente:', [
            'reserva_id' => $reserva->id,
            'codigo_reserva' => $reserva->codigo_reserva,
            'precio_total' => $precioTotal
        ]);

        // Configurar MercadoPago
        \MercadoPago\MercadoPagoConfig::setAccessToken("TEST-7710589893885438-080817-3174e23bf25e4fa03ab7383053c5b49c-90445855");
        
        $client = new \MercadoPago\Client\Preference\PreferenceClient();
        
        // Crear preferencia de pago
        $preference = $client->create([
            "items" => [
                [
                    "id" => "reserva_" . $reserva->id,
                    "title" => "Reserva: " . $cena->title,
                    "description" => "Chef: " . $cena->chef->name . " | " . $validatedData['cantidad_comensales'] . " comensales",
                    "quantity" => 1,
                    "unit_price" => floatval($precioTotal),
                    "currency_id" => "COP"
                ]
            ],
            "payer" => [
                "name" => $validatedData['nombre_contacto'],
                "email" => $validatedData['email_contacto'],
                "phone" => [
                    "number" => $validatedData['telefono_contacto']
                ]
            ],
            "external_reference" => $reserva->codigo_reserva,
            "back_urls" => [
                "success" => route('pago.exito', $reserva->codigo_reserva),
                "failure" => route('pago.error', $reserva->codigo_reserva), 
                "pending" => route('pago.pendiente', $reserva->codigo_reserva)
            ],
            "auto_return" => "approved",
            "statement_descriptor" => "TuMesa - Reserva Cena"
        ]);

        // Actualizar reserva con ID de preferencia
        $reserva->update([
            'transaccion_id' => $preference->id,
            'datos_pago' => [
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'cantidad_comensales' => $validatedData['cantidad_comensales'],
                'precio_unitario' => $precioPorPersona
            ]
        ]);

        Log::info('Preferencia de MercadoPago creada:', [
            'preference_id' => $preference->id,
            'reserva_codigo' => $reserva->codigo_reserva,
            'init_point' => $preference->init_point
        ]);

        // Redirigir a MercadoPago
        return redirect()->away($preference->init_point);

    } catch (\MercadoPago\Exceptions\MPApiException $e) {
        Log::error('Error de MercadoPago API:', [
            'error' => $e->getMessage(),
            'body' => method_exists($e, 'getApiResponse') ? $e->getApiResponse()->getContent() : null
        ]);
        
        return redirect()->route('comensal.dashboard')
            ->with('error', 'Error al procesar el pago. Inténtalo de nuevo.');
            
    } catch (\Exception $e) {
        Log::error('Error general al procesar reserva:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->route('comensal.dashboard')
            ->with('error', 'Error al procesar la reserva. Contacta al soporte.');
    }
}
}