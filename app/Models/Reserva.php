<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\Cena;
use App\Models\User;
use App\Models\Reseña;


class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'cena_id',
        'user_id',
        'cantidad_comensales',
        'precio_total',
        'precio_por_persona',
        'estado',
        'estado_pago',
        'nombre_contacto',
        'telefono_contacto',
        'email_contacto',
        'comentarios_especiales',
        'restricciones_alimentarias',
        'solicitudes_especiales',
        'metodo_pago',
        'transaccion_id',
        'datos_pago',
        'fecha_pago',
        'fecha_confirmacion',
        'fecha_cancelacion',
        'acepta_terminos',
        'acepta_politica_cancelacion',
        'codigo_reserva',
        'codigo_qr',
        'calificacion',
        'resena',
        'fecha_resena',
        // Nuevos campos de asistencia
        'asistencia_marcada',
        'estado_asistencia',
        'fecha_asistencia_marcada',
        'comentarios_asistencia'
    ];

    protected $casts = [
        'precio_total' => 'decimal:2',
        'precio_por_persona' => 'decimal:2',
        'datos_pago' => 'array',
        'fecha_pago' => 'datetime',
        'fecha_reserva' => 'datetime', 
        'fecha_confirmacion' => 'datetime',
        'fecha_cancelacion' => 'datetime',
        'fecha_resena' => 'datetime',
        'acepta_terminos' => 'boolean',
        'acepta_politica_cancelacion' => 'boolean',
        'cantidad_comensales' => 'integer',
        'calificacion' => 'integer',
        // Nuevos casts de asistencia
        'asistencia_marcada' => 'boolean',
        'fecha_asistencia_marcada' => 'datetime'
    ];

    // Eventos del modelo
    protected static function boot()
    {
        parent::boot();
        
        // Generar código único al crear
        static::creating(function ($reserva) {
            if (empty($reserva->codigo_reserva)) {
                $reserva->codigo_reserva = static::generarCodigoUnico();
            }
        });
    }

    // Relaciones
    public function cena(): BelongsTo
    {
        return $this->belongsTo(Cena::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comensal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
        public function reseña()
    {
        return $this->hasOne(Reseña::class, 'id_reserva');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
    // En el modelo Reserva
public function resena()
{
    return $this->hasOne(Resena::class);
}
    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopePagadas($query)
    {
        return $query->where('estado_pago', 'pagado');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    // Nuevos scopes de asistencia
    public function scopeAsistenciaPresente($query)
    {
        return $query->where('estado_asistencia', 'presente');
    }

    public function scopeAsistenciaAusente($query)
    {
        return $query->where('estado_asistencia', 'ausente');
    }

    public function scopeAsistenciaMarcada($query)
    {
        return $query->where('asistencia_marcada', true);
    }

    // Accessors
    public function getPrecioTotalFormateadoAttribute()
    {
        return '$' . number_format($this->precio_total, 0, ',', '.');
    }

    public function getPrecioPorPersonaFormateadoAttribute()
    {
        return '$' . number_format($this->precio_por_persona, 0, ',', '.');
    }

    public function getFechaReservaFormateadaAttribute()
    {
        return $this->fecha_reserva->format('d/m/Y H:i');
    }

    public function getEstadoBadgeAttribute()
    {
        $estados = [
            'pendiente' => ['class' => 'bg-warning', 'texto' => 'Pendiente'],
            'confirmada' => ['class' => 'bg-success', 'texto' => 'Confirmada'],
            'pagada' => ['class' => 'bg-primary', 'texto' => 'Pagada'],
            'cancelada' => ['class' => 'bg-danger', 'texto' => 'Cancelada'],
            'completada' => ['class' => 'bg-secondary', 'texto' => 'Completada'],
        ];

        return $estados[$this->estado] ?? ['class' => 'bg-secondary', 'texto' => 'Desconocido'];
    }

    public function getEstadoPagoBadgeAttribute()
    {
        $estados = [
            'pendiente' => ['class' => 'bg-warning', 'texto' => 'Pago Pendiente'],
            'pagado' => ['class' => 'bg-success', 'texto' => 'Pagado'],
            'reembolsado' => ['class' => 'bg-info', 'texto' => 'Reembolsado'],
            'fallido' => ['class' => 'bg-danger', 'texto' => 'Pago Fallido'],
        ];

        return $estados[$this->estado_pago] ?? ['class' => 'bg-secondary', 'texto' => 'Desconocido'];
    }

    // Nuevo accessor para estado de asistencia
    public function getEstadoAsistenciaBadgeAttribute()
    {
        $estados = [
            'pendiente' => ['class' => 'bg-secondary', 'texto' => 'Sin marcar'],
            'presente' => ['class' => 'bg-success', 'texto' => 'Presente'],
            'ausente' => ['class' => 'bg-danger', 'texto' => 'Ausente'],
            'parcial' => ['class' => 'bg-warning', 'texto' => 'Asistencia parcial'],
        ];

        return $estados[$this->estado_asistencia] ?? ['class' => 'bg-secondary', 'texto' => 'Sin marcar'];
    }

    public function getPuedeCancelarAttribute()
    {
        // Puede cancelar si está pendiente o confirmada y la cena es en más de 24 horas
        if (!in_array($this->estado, ['pendiente', 'confirmada'])) {
            return false;
        }

        return $this->cena->datetime->diffInHours(now()) > 24;
    }

    public function getPuedeCalificarAttribute()
    {
        // Puede calificar si la cena ya pasó y está completada
        return $this->estado === 'completada' && 
               $this->cena->datetime < now() && 
               is_null($this->calificacion);
    }

    // Métodos estáticos
    public static function generarCodigoUnico()
    {
        do {
            $codigo = 'RSV-' . strtoupper(Str::random(8));
        } while (static::where('codigo_reserva', $codigo)->exists());
        
        return $codigo;
    }

    // Métodos de instancia
    public function confirmar()
    {
        $this->update([
            'estado' => 'confirmada',
            'fecha_confirmacion' => now()
        ]);
    }

    public function cancelar($motivo = null)
    {
        $this->update([
            'estado' => 'cancelada',
            'fecha_cancelacion' => now(),
            'comentarios_especiales' => $this->comentarios_especiales . 
                                      ($motivo ? "\n\nMotivo cancelación: " . $motivo : '')
        ]);
    }

    public function marcarComoPagada($transaccionId = null, $datosPago = null)
    {
        $this->update([
            'estado' => 'pagada',
            'estado_pago' => 'pagado',
            'fecha_pago' => now(),
            'transaccion_id' => $transaccionId,
            'datos_pago' => $datosPago
        ]);
    }

    public function completar()
    {
        $this->update([
            'estado' => 'completada'
        ]);
    }

    public function calificar($puntuacion, $resena = null)
    {
        if ($puntuacion >= 1 && $puntuacion <= 5) {
            $this->update([
                'calificacion' => $puntuacion,
                'resena' => $resena,
                'fecha_resena' => now()
            ]);
        }
    }

    // Nuevos métodos de asistencia
    public function marcarPresente($comentarios = null)
    {
        $this->update([
            'asistencia_marcada' => true,
            'estado_asistencia' => 'presente',
            'fecha_asistencia_marcada' => now(),
            'comentarios_asistencia' => $comentarios
        ]);
    }

    public function marcarAusente($comentarios = null)
    {
        $this->update([
            'asistencia_marcada' => true,
            'estado_asistencia' => 'ausente',
            'fecha_asistencia_marcada' => now(),
            'comentarios_asistencia' => $comentarios
        ]);
    }

    public function marcarParcial($comentarios = null)
    {
        $this->update([
            'asistencia_marcada' => true,
            'estado_asistencia' => 'parcial',
            'fecha_asistencia_marcada' => now(),
            'comentarios_asistencia' => $comentarios
        ]);
    }
}