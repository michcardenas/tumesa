<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'reserva_id',
        'cena_id', 
        'user_id',
        'payment_id',
        'collection_id',
        'preference_id',
        'merchant_order_id',
        'external_reference',
        'status',
        'collection_status',
        'payment_type',
        'payment_method',
        'monto_total',
        'currency_id',
        'datos_completos',
        'processing_mode',
        'site_id',
        'fecha_pago',
        'fecha_aprobacion',
        'fecha_rechazo'
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'datos_completos' => 'array',
        'fecha_pago' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_rechazo' => 'datetime'
    ];

    // Relaciones
    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function cena(): BelongsTo
    {
        return $this->belongsTo(Cena::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeAprobados($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePendientes($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRechazados($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getMontoFormateadoAttribute()
    {
        return '$' . number_format($this->monto_total, 0, ',', '.');
    }

    public function getEsAprobadoAttribute()
    {
        return $this->status === 'approved';
    }

    public function getEsPendienteAttribute()
    {
        return $this->status === 'pending';
    }

    public function getEsRechazadoAttribute()
    {
        return $this->status === 'rejected';
    }

    // Método estático para crear desde datos de MercadoPago
    public static function crearDesdeMercadoPago($reserva, $datosMercadoPago)
    {
        return static::create([
            'reserva_id' => $reserva->id,
            'cena_id' => $reserva->cena_id,
            'user_id' => $reserva->user_id,
            'payment_id' => $datosMercadoPago['payment_id'] ?? null,
            'collection_id' => $datosMercadoPago['collection_id'] ?? null,
            'preference_id' => $datosMercadoPago['preference_id'] ?? null,
            'merchant_order_id' => $datosMercadoPago['merchant_order_id'] ?? null,
            'external_reference' => $datosMercadoPago['external_reference'] ?? null,
            'status' => $datosMercadoPago['status'] ?? null,
            'collection_status' => $datosMercadoPago['collection_status'] ?? null,
            'payment_type' => $datosMercadoPago['payment_type'] ?? null,
            'payment_method' => $datosMercadoPago['payment_method'] ?? null,
            'monto_total' => $reserva->precio_total,
            'currency_id' => 'ARS',
            'datos_completos' => $datosMercadoPago,
            'processing_mode' => $datosMercadoPago['processing_mode'] ?? null,
            'site_id' => $datosMercadoPago['site_id'] ?? null,
            'fecha_pago' => now(),
            'fecha_aprobacion' => in_array($datosMercadoPago['status'] ?? '', ['approved']) ? now() : null,
            'fecha_rechazo' => in_array($datosMercadoPago['status'] ?? '', ['rejected', 'cancelled']) ? now() : null
        ]);
    }

    // Método para marcar como aprobado
    public function aprobar()
    {
        $this->update([
            'status' => 'approved',
            'collection_status' => 'approved', 
            'fecha_aprobacion' => now()
        ]);

        // Actualizar también la reserva
        $this->reserva->marcarComoPagada($this->payment_id, $this->datos_completos);
    }

    // Método para marcar como rechazado
    public function rechazar($motivo = null)
    {
        $this->update([
            'status' => 'rejected',
            'fecha_rechazo' => now()
        ]);

        // Si hay motivo, agregarlo a datos completos
        if ($motivo) {
            $datos = $this->datos_completos ?? [];
            $datos['motivo_rechazo'] = $motivo;
            $this->update(['datos_completos' => $datos]);
        }
    }
}