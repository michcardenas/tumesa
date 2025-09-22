<?php

namespace App\Http\Controllers;

use App\Models\Cena;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use App\Models\Reseña;

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
        ->whereIn('status', ['published', 'in_progress']) // CAMBIO: Incluir en curso
        ->where('datetime', '>', now()->subHours(2)) // Mostrar hasta 2 horas después de iniciadas
        ->orderBy('datetime')
        ->limit(6)
        ->get();
    // Cenas recomendadas (las más populares o recientes)
    $cenasRecomendadas = Cena::with('chef')
        ->active()
        ->published() 
        ->upcoming()
        ->where('guests_current', '>', 0)
        ->orderBy('guests_current', 'desc')
        ->limit(2)
        ->get();

    if ($cenasRecomendadas->isEmpty()) {
        $cenasRecomendadas = Cena::with('chef')
            ->active()
            ->published()
            ->upcoming()
            ->latest('created_at')
            ->limit(2)
            ->get();
    }

    // NUEVO: Traer reservas del usuario
       $proximasReservas = Reserva::with(['cena', 'cena.chef', 'reseña'])
    ->where('user_id', $user->id)
    ->whereIn('estado', ['pendiente', 'confirmada', 'pagada', 'completada'])
    ->whereHas('cena', function($query) {
        $query->where(function($q) {
            $q->where('datetime', '>', now())
               ->orWhere(function($sub) {
                   $sub->where('datetime', '<=', now())
                       ->where('datetime', '>=', now()->subHours(3))
                       ->whereIn('status', ['published', 'in_progress', 'completed']);
               });
        });
    })
    ->orderBy('created_at', 'desc')
    ->get();


    // NUEVO: Calcular estadísticas reales
    $stats = [
        'reservas_activas' => $proximasReservas->count(),
        'cenas_disfrutadas' => Reserva::where('user_id', $user->id)
            ->where('estado', 'completada')
            ->count(),
        'chefs_favoritos' => 0, // Implementar después con tabla de favoritos
        'gastado_mes' => Reserva::where('user_id', $user->id)
            ->where('estado_pago', 'pagado')
            ->whereMonth('created_at', now()->month)
            ->sum('precio_total')
    ];

    Log::info('Estadísticas calculadas:', $stats);
    Log::info('Próximas reservas encontradas: ' . $proximasReservas->count());

    return view('comensal.dashboard', compact('user', 'cenasDisponibles', 'cenasRecomendadas', 'proximasReservas', 'stats'));
}

public function verDetalleReserva(Reserva $reserva)
{
    // Verificar que la reserva pertenece al usuario autenticado
    if ($reserva->user_id !== auth()->id()) {
        abort(403, 'No tienes permiso para ver esta reserva');
    }

    // Cargar relaciones necesarias
    $reserva->load(['cena', 'cena.chef', 'reseña']); // Usar 'reseña' con ñ

    // Calcular información adicional
    $puedeCalificar = $reserva->puede_calificar && !$reserva->reseña; // Usar 'reseña' con ñ
    $puedeCancelar = $reserva->puede_cancelar;

    // Estado de la cena
    $ahora = now();
    $fechaCena = $reserva->cena->datetime;
    $minutosParaCena = $ahora->diffInMinutes($fechaCena, false);
    $cenaPasada = $fechaCena < $ahora;
    $cenaEnCurso = $reserva->cena->status === 'in_progress';

    return view('comensal.reserva-detalle', compact(
        'reserva',
        'puedeCalificar',
        'puedeCancelar',
        'minutosParaCena',
        'cenaPasada',
        'cenaEnCurso'
    ));
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
        // Solo bloquear si tiene una reserva con pago exitoso o pendiente válido
        $reservaExistente = Reserva::where('user_id', $user->id)
            ->where('cena_id', $cena->id)
            ->where(function($query) {
                $query->where(function($subQuery) {
                    // Reservas confirmadas o pagadas (sin importar estado_pago)
                    $subQuery->whereIn('estado', ['confirmada', 'pagada', 'completada']);
                })->orWhere(function($subQuery) {
                    // Reservas pendientes pero con pago exitoso o pendiente (no fallido)
                    $subQuery->where('estado', 'pendiente')
                             ->whereIn('estado_pago', ['pagado', 'pendiente']);
                });
            })
            ->first();

        if ($reservaExistente) {
            Log::warning('Usuario intenta reservar cena que ya tiene reservada:', [
                'user_id' => $user->id,
                'cena_id' => $cena->id,
                'reserva_id' => $reservaExistente->id,
                'estado_actual' => $reservaExistente->estado,
                'estado_pago' => $reservaExistente->estado_pago
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
/**
 * Process dinner reservation and redirect to payment gateway.
 */
/**
 * Completar pago de una reserva existente.
 */
public function completarPago(Reserva $reserva): View|RedirectResponse
{
    $user = Auth::user();

    Log::info('=== ACCESO A COMPLETAR PAGO ===');
    Log::info('Completando pago de reserva:', [
        'comensal_id' => $user->id,
        'reserva_id' => $reserva->id,
        'reserva_codigo' => $reserva->codigo_reserva,
        'estado_actual' => $reserva->estado,
        'estado_pago_actual' => $reserva->estado_pago
    ]);

    // Verificar que la reserva pertenece al usuario autenticado
    if ($reserva->user_id !== $user->id) {
        Log::warning('Usuario intenta completar pago de reserva que no le pertenece:', [
            'user_id' => $user->id,
            'reserva_id' => $reserva->id,
            'reserva_user_id' => $reserva->user_id
        ]);

        return redirect()->route('comensal.dashboard')
            ->with('error', 'No tienes permiso para acceder a esta reserva.');
    }

    // Verificar que la reserva requiera pago
    if ($reserva->estado_pago === 'pagado') {
        Log::info('Reserva ya está pagada, redirigiendo al dashboard:', [
            'reserva_id' => $reserva->id,
            'estado_pago' => $reserva->estado_pago
        ]);

        return redirect()->route('comensal.dashboard')
            ->with('success', 'Esta reserva ya está pagada.');
    }

    if (!in_array($reserva->estado_pago, ['pendiente', 'fallido'])) {
        Log::warning('Intento de completar pago en reserva con estado incorrecto:', [
            'reserva_id' => $reserva->id,
            'estado_pago' => $reserva->estado_pago
        ]);

        return redirect()->route('comensal.dashboard')
            ->with('error', 'Esta reserva no puede ser pagada en su estado actual.');
    }

    // Cargar la cena relacionada
    $cena = $reserva->cena;

    // Verificar que la cena siga disponible
    if (!$cena->is_active || $cena->status !== 'published') {
        Log::warning('Intento de pagar reserva para cena no disponible:', [
            'cena_id' => $cena->id,
            'is_active' => $cena->is_active,
            'status' => $cena->status
        ]);

        return redirect()->route('comensal.dashboard')
            ->with('error', 'Esta cena ya no está disponible.');
    }

    // Verificar que la cena no haya pasado
    if ($cena->datetime <= now()) {
        Log::warning('Intento de pagar reserva para cena que ya pasó:', [
            'cena_id' => $cena->id,
            'datetime' => $cena->datetime,
            'now' => now()
        ]);

        return redirect()->route('comensal.dashboard')
            ->with('error', 'Esta cena ya pasó. No se puede completar el pago.');
    }

    Log::info('Completar pago validado correctamente - Mostrando formulario de pago');

    // Retornar la vista de checkout pero con la información de la reserva existente
    return view('comensal.checkout', compact('cena', 'user', 'reserva'));
}

public function procesarReserva(Request $request): RedirectResponse
{
    $user = Auth::user();
    
    Log::info('=== PROCESANDO RESERVA ===');
    Log::info('Datos recibidos:', $request->all());

    // Validar datos del formulario
    $validatedData = $request->validate([
        'cena_id' => 'required|exists:cenas,id',
        'reserva_id' => 'nullable|exists:reservas,id', // Para completar pago de reserva existente
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

        // Convertir checkboxes a booleanos
        $aceptaTerminos = $request->has('acepta_terminos') && $request->acepta_terminos == 'on';
        $aceptaPolitica = $request->has('acepta_politica_cancelacion') && $request->acepta_politica_cancelacion == 'on';

        // Determinar si es completar pago de reserva existente o crear nueva
        if (isset($validatedData['reserva_id']) && !empty($validatedData['reserva_id'])) {
            // Completar pago de reserva existente
            $reserva = Reserva::findOrFail($validatedData['reserva_id']);

            // Verificar que la reserva pertenece al usuario
            if ($reserva->user_id !== $user->id) {
                throw new \Exception('No tienes permiso para modificar esta reserva.');
            }

            // Actualizar la reserva con los nuevos datos
            $reserva->update([
                'cantidad_comensales' => $validatedData['cantidad_comensales'],
                'precio_por_persona' => $precioPorPersona,
                'precio_total' => $precioTotal,
                'nombre_contacto' => $validatedData['nombre_contacto'],
                'telefono_contacto' => $validatedData['telefono_contacto'],
                'email_contacto' => $validatedData['email_contacto'],
                'restricciones_alimentarias' => $validatedData['restricciones_alimentarias'],
                'solicitudes_especiales' => $validatedData['solicitudes_especiales'],
                'comentarios_especiales' => $validatedData['comentarios_especiales'],
                'acepta_terminos' => $aceptaTerminos,
                'acepta_politica_cancelacion' => $aceptaPolitica,
                'estado_pago' => 'pendiente' // Resetear a pendiente para el nuevo intento de pago
            ]);

            Log::info('Reserva existente actualizada para completar pago:', [
                'reserva_id' => $reserva->id,
                'codigo_reserva' => $reserva->codigo_reserva
            ]);
        } else {
            // Crear nueva reserva
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
                'acepta_terminos' => $aceptaTerminos,
                'acepta_politica_cancelacion' => $aceptaPolitica,
                'fecha_reserva' => now()
            ]);

            Log::info('Nueva reserva creada:', [
                'reserva_id' => $reserva->id,
                'codigo_reserva' => $reserva->codigo_reserva
            ]);
        }

        Log::info('Reserva creada exitosamente:', [
            'reserva_id' => $reserva->id,
            'codigo_reserva' => $reserva->codigo_reserva,
            'precio_total' => $precioTotal
        ]);

        // Configurar MercadoPago
\MercadoPago\MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        
        $client = new \MercadoPago\Client\Preference\PreferenceClient();
        
        // CORRECCIÓN: Usar ARS como en tu ejemplo que funciona
        $preference = $client->create([
            "items" => [
                [
                    "id" => "reserva_" . $reserva->id,
                    "title" => "Reserva: " . $cena->title,
                    "description" => "Chef: " . $cena->chef->name . " | " . $validatedData['cantidad_comensales'] . " comensales",
                    "quantity" => 1,
                    "unit_price" => floatval($precioTotal),
                    "currency_id" => "ARS"  // CAMBIADO DE COP A ARS
                ]
            ],
            "payer" => [
                "name" => $validatedData['nombre_contacto'],
                "email" => $validatedData['email_contacto']
            ],
            "external_reference" => $reserva->codigo_reserva,
            "back_urls" => [
                "success" => route('pago.exito', $reserva->codigo_reserva),
                "failure" => route('pago.error', $reserva->codigo_reserva), 
                "pending" => route('pago.pendiente', $reserva->codigo_reserva)
            ],
            "auto_return" => "approved"
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