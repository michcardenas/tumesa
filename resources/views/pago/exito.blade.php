{{-- resources/views/pago/exito.blade.php --}}
@extends('layouts.app_comensal')

@section('content')
<div class="payment-success-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Header -->
                <div class="success-header text-center">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1 class="success-title">¡Pago Exitoso!</h1>
                    <p class="success-subtitle">Tu reserva ha sido confirmada correctamente</p>
                </div>

                <!-- Reservation Details Card -->
                <div class="reservation-confirmed-card">
                    <div class="card-header">
                        <h4>
                            <i class="fas fa-ticket-alt me-2"></i>
                            Detalles de tu Reserva
                        </h4>
                        <span class="confirmation-badge">
                            <i class="fas fa-shield-check"></i> Confirmada
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Información de la Cena -->
                            <div class="col-md-8">
                                <div class="reservation-info">
                                    <h5 class="cena-title">{{ $reserva->cena->title }}</h5>
                                    
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Chef Anfitrión</span>
                                                <span class="info-value">{{ $reserva->cena->chef->name }}</span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Fecha y Hora</span>
                                                <span class="info-value">{{ $reserva->cena->datetime->format('l, j \d\e F \d\e Y \a \l\a\s g:i A') }}</span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                          
                                        </div>

                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Comensales</span>
                                                <span class="info-value">{{ $reserva->cantidad_comensales }} {{ Str::plural('persona', $reserva->cantidad_comensales) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- QR y Código -->
                            <div class="col-md-4">
                                <div class="reservation-code-section">
                                    <div class="code-display">
                                        <h6>Código de Reserva</h6>
                                        <div class="reservation-code">{{ $reserva->codigo_reserva }}</div>
                                        <small class="text-muted">Presenta este código en la cena</small>
                                    </div>
                                    
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="payment-details-card">
                    <h5>
                        <i class="fas fa-credit-card me-2"></i>
                        Detalles del Pago
                    </h5>
                    
                    <div class="payment-info">
                        <div class="payment-row">
                            <span>Método de pago:</span>
                            <span class="fw-bold text-capitalize">{{ $pago->payment_type ?? 'Tarjeta de crédito' }}</span>
                        </div>
                        <div class="payment-row">
                            <span>ID de transacción:</span>
                            <span class="fw-bold">{{ $pago->payment_id }}</span>
                        </div>
                        <div class="payment-row">
                            <span>Estado:</span>
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> {{ ucfirst($pago->status) }}
                            </span>
                        </div>
                        <div class="payment-row">
                            <span>Fecha de pago:</span>
                            <span class="fw-bold">{{ $pago->fecha_pago->format('d/m/Y H:i') }}</span>
                        </div>
                        <hr>
                        <div class="payment-row total">
                            <span>Total pagado:</span>
                            <span class="fw-bold text-success fs-5">{{ $pago->monto_formateado }}</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                @if($reserva->restricciones_alimentarias || $reserva->solicitudes_especiales)
                <div class="special-requests-card">
                    <h5>
                        <i class="fas fa-info-circle me-2"></i>
                        Información Especial Compartida con el Chef
                    </h5>
                    
                    @if($reserva->restricciones_alimentarias)
                    <div class="request-item">
                        <strong>Restricciones alimentarias:</strong>
                        <p>{{ $reserva->restricciones_alimentarias }}</p>
                    </div>
                    @endif
                    
                    @if($reserva->solicitudes_especiales)
                    <div class="request-item">
                        <strong>Solicitudes especiales:</strong>
                        <p>{{ $reserva->solicitudes_especiales }}</p>
                    </div>
                    @endif
                    
                    @if($reserva->comentarios_especiales)
                    <div class="request-item">
                        <strong>Comentarios para el chef:</strong>
                        <p>{{ $reserva->comentarios_especiales }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Next Steps -->
                <div class="next-steps-card">
                    <h5>
                        <i class="fas fa-list-check me-2"></i>
                        Próximos Pasos
                    </h5>
                    
                    <div class="steps-list">
                        <div class="step-item completed">
                            <div class="step-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="step-content">
                                <strong>Reserva confirmada</strong>
                                <p>Tu lugar está garantizado</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="step-content">
                                <strong>Confirmación por email</strong>
                                <p>Recibirás todos los detalles en {{ $reserva->email_contacto }}</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="step-content">
                                <strong>Día de la cena</strong>
                                <p>Presenta tu código de reserva {{ $reserva->codigo_reserva }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons text-center">
                    <a href="{{ route('comensal.dashboard') }}" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Ir a Mi Dashboard
                    </a>
                    <button class="btn btn-outline-secondary btn-lg" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>
                        Imprimir Confirmación
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-success-container {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    min-height: calc(100vh - 160px);
    padding: 2rem 0;
}

/* Success Header */
.success-header {
    margin-bottom: 2rem;
}

.success-icon {
    font-size: 4rem;
    color: #059669;
    margin-bottom: 1rem;
}

.success-title {
    color: #1f2937;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.success-subtitle {
    color: #6b7280;
    font-size: 1.1rem;
}

/* Cards */
.reservation-confirmed-card,
.payment-details-card,
.special-requests-card,
.next-steps-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem;
    border-bottom: 2px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h4,
.card-header h5 {
    margin: 0;
    color: #374151;
    font-weight: 600;
}

.confirmation-badge {
    background: #059669;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.card-body {
    padding: 1.5rem;
}

/* Reservation Info */
.cena-title {
    color: #2563eb;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
}

.info-grid {
    display: grid;
    gap: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid #2563eb;
}

.info-icon {
    background: #2563eb;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.info-content {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.info-value {
    font-weight: 600;
    color: #374151;
}

/* Reservation Code */
.reservation-code-section {
    text-align: center;
    background: #f8fafc;
    border-radius: 8px;
    padding: 1.5rem;
    border: 2px dashed #cbd5e1;
}

.code-display h6 {
    color: #374151;
    margin-bottom: 1rem;
}

.reservation-code {
    background: #2563eb;
    color: white;
    padding: 1rem;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 1.2rem;
    font-weight: bold;
    letter-spacing: 2px;
    margin-bottom: 0.5rem;
}

.qr-code {
    margin-top: 1.5rem;
}

.qr-placeholder {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    color: #9ca3af;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

/* Payment Details */
.payment-info {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1.5rem;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    color: #374151;
}

.payment-row:last-child {
    margin-bottom: 0;
}

.payment-row.total {
    font-size: 1.1rem;
    padding-top: 1rem;
    border-top: 2px solid #e5e7eb;
}

/* Special Requests */
.request-item {
    background: #fefce8;
    border: 1px solid #fde047;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.request-item:last-child {
    margin-bottom: 0;
}

.request-item strong {
    color: #92400e;
    display: block;
    margin-bottom: 0.5rem;
}

.request-item p {
    color: #374151;
    margin: 0;
    line-height: 1.4;
}

/* Next Steps */
.steps-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.step-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.step-item.completed {
    background: #f0fdf4;
    border-left: 4px solid #059669;
}

.step-icon {
    background: #e5e7eb;
    color: #6b7280;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.step-item.completed .step-icon {
    background: #059669;
    color: white;
}

.step-content strong {
    color: #374151;
    display: block;
    margin-bottom: 0.25rem;
}

.step-content p {
    color: #6b7280;
    margin: 0;
    font-size: 0.9rem;
}

/* Action Buttons */
.action-buttons {
    margin-top: 2rem;
    padding: 2rem;
}

.action-buttons .btn {
    padding: 1rem 2rem;
    font-weight: 600;
    border-radius: 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .payment-success-container {
        padding: 1rem 0;
    }
    
    .success-icon {
        font-size: 3rem;
    }
    
    .success-title {
        font-size: 1.75rem;
    }
    
    .card-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .info-grid {
        gap: 0.75rem;
    }
    
    .info-item {
        padding: 0.75rem;
    }
    
    .reservation-code {
        font-size: 1rem;
        letter-spacing: 1px;
    }
    
    .action-buttons {
        padding: 1rem;
    }
    
    .action-buttons .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .payment-row {
        font-size: 0.9rem;
    }
}

/* Print Styles */
@media print {
    .navbar, .footer-custom, .action-buttons {
        display: none !important;
    }
    
    .payment-success-container {
        background: white;
        padding: 0;
    }
    
    .reservation-confirmed-card,
    .payment-details-card {
        box-shadow: none;
        border: 2px solid #e5e7eb;
    }
}

/* Animation */
.success-icon {
    animation: successPulse 2s ease-in-out;
}

@keyframes successPulse {
    0% { transform: scale(0.8); opacity: 0; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar notificación de éxito si no es re-procesamiento
    @if(!isset($yaProcessed) || !$yaProcessed)
    // Confetti effect simple (opcional)
    setTimeout(() => {
        const successIcon = document.querySelector('.success-icon i');
        if (successIcon) {
            successIcon.style.transform = 'scale(1.2)';
            setTimeout(() => {
                successIcon.style.transform = 'scale(1)';
            }, 300);
        }
    }, 500);
    @endif

    // Copiar código de reserva al clipboard
    document.querySelector('.reservation-code')?.addEventListener('click', function() {
        const codigo = this.textContent;
        navigator.clipboard.writeText(codigo).then(function() {
            // Mostrar feedback visual
            const element = document.querySelector('.reservation-code');
            const originalText = element.textContent;
            element.textContent = '¡Copiado!';
            element.style.background = '#059669';
            
            setTimeout(() => {
                element.textContent = originalText;
                element.style.background = '#2563eb';
            }, 2000);
        });
    });

    // Auto-scroll suave al contenido
    document.querySelector('.reservation-confirmed-card')?.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
});
</script>
@endsection