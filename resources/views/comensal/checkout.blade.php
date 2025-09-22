{{-- resources/views/comensal/checkout.blade.php --}}
@extends('layouts.app_comensal')

@section('content')
<div class="comensal-content">
    <!-- Header del Checkout -->
    <div class="checkout-header">
        <div class="d-flex align-items-center">
            <a href="{{ route('comensal.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <div>
                @if(isset($reserva))
                    <h2 class="mb-0">Completar Pago</h2>
                    <p class="text-muted mb-0">Completa el pago de tu reserva existente</p>
                    <small class="badge bg-warning text-dark">Reserva: {{ $reserva->codigo_reserva }}</small>
                @else
                    <h2 class="mb-0">Confirmar Reserva</h2>
                    <p class="text-muted mb-0">Completa los datos para reservar tu lugar</p>
                @endif
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Formulario de Reserva -->
        <div class="col-lg-8">
            <form id="reservaForm" action="{{ route('comensal.procesar-reserva') }}" method="POST">
                @csrf
                <input type="hidden" name="cena_id" value="{{ $cena->id }}">
                @if(isset($reserva))
                    <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                @endif

                <!-- Paso 1: Información de la Cena -->
                <div class="checkout-section">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <h4>Información de la Cena</h4>
                    </div>

                    <div class="cena-info-card">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="cena-image">
                                    @if($cena->cover_image_url)
                                        <img src="{{ $cena->cover_image_url }}" alt="{{ $cena->title }}" class="img-fluid">
                                    @else
                                        <div class="placeholder-image">
                                            <i class="fas fa-utensils fa-3x"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="cena-details">
                                    <h5>{{ $cena->title }}</h5>
                                    <div class="chef-info mb-2">
                                        <i class="fas fa-user-tie text-primary"></i>
                                        <strong>Anfitrión: {{ $cena->chef->name }}</strong>
                                    </div>
                                    <div class="cena-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-calendar text-primary"></i>
                                            <span>{{ $cena->datetime->format('l, j \d\e F Y') }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-clock text-primary"></i>
                                            <span>{{ $cena->datetime->format('g:i A') }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                            <span>{{ $cena->location }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-users text-primary"></i>
                                            <span>{{ $cena->available_spots }} lugares disponibles</span>
                                        </div>
                                    </div>
                                    @if($cena->menu)
                                    <div class="menu-preview mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-utensils"></i>
                                            {{ Str::limit($cena->menu, 100) }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Cantidad de Comensales -->
                <div class="checkout-section">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <h4>¿Cuántos comensales?</h4>
                    </div>

                    <div class="guests-selector">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <label for="cantidad_comensales" class="form-label">Número de comensales</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="decrementGuests()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number"
                                           id="cantidad_comensales"
                                           name="cantidad_comensales"
                                           class="form-control text-center"
                                           value="{{ isset($reserva) ? $reserva->cantidad_comensales : 1 }}"
                                           min="1"
                                           max="{{ $cena->available_spots }}"
                                           {{ isset($reserva) ? 'readonly' : 'readonly' }}>
                                    <button type="button" class="btn btn-outline-secondary" onclick="incrementGuests()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Máximo {{ $cena->available_spots }} comensales disponibles</small>
                            </div>
                            <div class="col-md-6">
                                <div class="price-calculation">
                                    <div class="price-breakdown">
                                        <div class="d-flex justify-content-between">
                                            <span>Precio por persona:</span>
                                            <span class="fw-bold">{{ $cena->formatted_price }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Cantidad de personas:</span>
                                            <span id="guestsDisplay">1</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between total-price">
                                            <span class="fw-bold">Total a pagar:</span>
                                            <span class="fw-bold text-primary" id="totalPrice">{{ $cena->formatted_price }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Información de Contacto -->
                <div class="checkout-section">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <h4>Información de Contacto</h4>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_contacto" class="form-label">Nombre completo *</label>
                                <input type="text"
                                       class="form-control"
                                       id="nombre_contacto"
                                       name="nombre_contacto"
                                       value="{{ isset($reserva) ? $reserva->nombre_contacto : $user->name }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_contacto" class="form-label">Email *</label>
                                <input type="email"
                                       class="form-control"
                                       id="email_contacto"
                                       name="email_contacto"
                                       value="{{ isset($reserva) ? $reserva->email_contacto : $user->email }}"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono_contacto" class="form-label">Teléfono *</label>
                                <input type="tel"
                                       class="form-control"
                                       id="telefono_contacto"
                                       name="telefono_contacto"
                                       value="{{ isset($reserva) ? $reserva->telefono_contacto : '' }}"
                                       placeholder="+54 300 123 4567"
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 4: Información Adicional -->
                <div class="checkout-section">
                    <div class="section-title">
                        <span class="step-number">4</span>
                        <h4>Información Adicional</h4>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="restricciones_alimentarias" class="form-label">Restricciones alimentarias</label>
                                <textarea class="form-control"
                                          id="restricciones_alimentarias"
                                          name="restricciones_alimentarias"
                                          rows="3"
                                          placeholder="Alergias, vegetariano, vegano, etc.">{{ isset($reserva) ? $reserva->restricciones_alimentarias : '' }}</textarea>
                                <small class="text-muted">El Anfitrión necesita conocer estas restricciones con anticipación</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="solicitudes_especiales" class="form-label">Solicitudes especiales</label>
                                <textarea class="form-control" 
                                          id="solicitudes_especiales" 
                                          name="solicitudes_especiales" 
                                          rows="3" 
                                          placeholder="Ocasión especial, preferencias, etc."></textarea>
                                <small class="text-muted">Cumpleaños, aniversarios, preferencias especiales</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comentarios_especiales" class="form-label">Comentarios para el Anfitrión</label>
                        <textarea class="form-control"
                                  id="comentarios_especiales"
                                  name="comentarios_especiales"
                                  rows="2"
                                  placeholder="Cualquier comentario adicional que quieras compartir con el Anfitrión">{{ isset($reserva) ? $reserva->comentarios_especiales : '' }}</textarea>
                    </div>
                </div>

                <!-- Paso 5: Términos y Condiciones -->
                <div class="checkout-section">
                    <div class="section-title">
                        <span class="step-number">5</span>
                        <h4>Términos y Condiciones</h4>
                    </div>

                    <div class="terms-section">
                       <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="acepta_terminos" name="acepta_terminos" required>
                            <label class="form-check-label" for="acepta_terminos">
                                Acepto los <a href="{{ route('terminos') }}" target="_blank">términos y condiciones de uso</a> *
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="acepta_politica_cancelacion" name="acepta_politica_cancelacion" required>
                            <label class="form-check-label" for="acepta_politica_cancelacion">
                                Acepto la <a href="#" data-bs-toggle="modal" data-bs-target="#cancelacionModal">política de cancelación</a> *
                            </label>
                        </div>

                       
                    </div>
                </div>

                <!-- Botón de Proceder al Pago -->
                <div class="checkout-actions">
                    <button type="submit" class="btn btn-success btn-lg w-100" id="btnProcederPago">
                        <i class="fas fa-credit-card me-2"></i>
                        Proceder al Pago - <span id="finalTotalPrice">{{ $cena->formatted_price }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Resumen de Reserva (Sidebar) -->
        <div class="col-lg-4">
            <div class="reservation-summary sticky-top" style="top: 120px;">
                <h5 class="summary-title">
                    <i class="fas fa-receipt"></i>
                    Resumen de Reserva
                </h5>

                <!-- Info de la cena -->
                <div class="summary-section">
                    <h6>{{ $cena->title }}</h6>
                    <p class="text-muted mb-2">con {{ $cena->chef->name }}</p>
                    
                    <div class="summary-details">
                        <div class="detail-row">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $cena->datetime->format('l, j \d\e F Y') }}</span>
                        </div>
                        <div class="detail-row">
                            <i class="fas fa-clock"></i>
                            <span>{{ $cena->datetime->format('g:i A') }}</span>
                        </div>
                        <div class="detail-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $cena->location }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cálculo de precios -->
                <div class="summary-section">
                    <h6>Desglose de Precios</h6>
                    <div class="price-breakdown">
                        <div class="price-row">
                            <span>{{ $cena->formatted_price }} x <span id="summaryGuests">1</span> persona(s)</span>
                            <span id="summarySubtotal">{{ $cena->formatted_price }}</span>
                        </div>
                        <div class="price-row service-fee">
                            <span>Tarifa de servicio</span>
                            <span>$0</span>
                        </div>
                        <hr>
                        <div class="price-row total">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold" id="summaryTotal">{{ $cena->formatted_price }}</span>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="summary-section">
                    <div class="summary-notes">
                        <div class="note-item">
                            <i class="fas fa-shield-alt text-success"></i>
                            <span>Cancelación gratuita hasta 24h antes</span>
                        </div>
                        <div class="note-item">
                            <i class="fas fa-credit-card text-primary"></i>
                            <span>Pago seguro con tarjeta</span>
                        </div>
                        <div class="note-item">
                            <i class="fas fa-envelope text-info"></i>
                            <span>Confirmación inmediata por email</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Términos y Condiciones -->
<div class="modal fade" id="terminosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Términos y Condiciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Servicios</h6>
                <p>TuMesa conecta comensales con chefs anfitriones para experiencias gastronómicas únicas.</p>
                
                <h6>2. Reservas</h6>
                <p>Las reservas están sujetas a confirmación del Anfitrión. El pago se procesa inmediatamente.</p>
                
                <h6>3. Responsabilidades</h6>
                <p>Los Anfitriones son responsables de la calidad de la comida y el servicio. TuMesa facilita la conexión.</p>
                
                <h6>4. Políticas</h6>
                <p>Debes informar restricciones alimentarias al momento de la reserva.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Política de Cancelación -->
<div class="modal fade" id="cancelacionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Política de Cancelación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Cancelación Flexible</strong>
                </div>
                
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <strong>Más de 24 horas:</strong> Reembolso completo
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-exclamation text-warning me-2"></i>
                        <strong>Entre 24-12 horas:</strong> 50% de reembolso
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-times text-danger me-2"></i>
                        <strong>Menos de 12 horas:</strong> Sin reembolso
                    </li>
                </ul>
                
                <p class="text-muted">Los reembolsos se procesan en 3-5 días hábiles.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Checkout Styles */
.checkout-header {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.checkout-section {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-title {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.step-number {
    background: #2563eb;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 1rem;
}

.section-title h4 {
    margin: 0;
    color: #374151;
}

/* Cena Info Card */
.cena-info-card {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.cena-image img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 6px;
}

.placeholder-image {
    width: 100%;
    height: 150px;
    background: #e5e7eb;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
}

.cena-details h5 {
    color: #1f2937;
    font-weight: 600;
    margin-bottom: 1rem;
}

.chef-info {
    color: #374151;
    margin-bottom: 1rem;
}

.chef-info i {
    margin-right: 0.5rem;
}

.cena-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #374151;
}

.meta-item i {
    width: 16px;
    text-align: center;
}

.menu-preview {
    background: white;
    padding: 0.75rem;
    border-radius: 4px;
    border-left: 3px solid #2563eb;
}

/* Guests Selector */
.guests-selector {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.input-group .btn {
    width: 50px;
}

.price-calculation {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.price-breakdown .d-flex {
    margin-bottom: 0.5rem;
}

.total-price {
    font-size: 1.1rem;
    color: #2563eb;
}

/* Reservation Summary */
.reservation-summary {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid #e2e8f0;
}

.summary-title {
    color: #374151;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.summary-title i {
    color: #2563eb;
    margin-right: 0.5rem;
}

.summary-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.summary-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.summary-section h6 {
    color: #374151;
    font-weight: 600;
    margin-bottom: 1rem;
}

.summary-details .detail-row {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    color: #374151;
}

.detail-row i {
    color: #2563eb;
    width: 20px;
    margin-right: 0.75rem;
}

.price-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
}

.price-row.total {
    font-size: 1.1rem;
    color: #2563eb;
}

.service-fee {
    color: #6b7280;
    font-size: 0.85rem;
}

.summary-notes .note-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.85rem;
    color: #374151;
}

.note-item i {
    width: 20px;
    margin-right: 0.75rem;
}

/* Checkout Actions */
.checkout-actions {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.checkout-actions .btn {
    font-size: 1.1rem;
    padding: 1rem 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .cena-meta {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .checkout-section {
        padding: 1.5rem;
    }
    
    .reservation-summary {
        margin-top: 2rem;
        position: relative !important;
        top: auto !important;
    }
    
    .price-calculation {
        margin-top: 1rem;
    }
}

/* Form Validation Styles */
.was-validated .form-control:valid {
    border-color: #059669;
}

.was-validated .form-control:invalid {
    border-color: #dc2626;
}

.form-check-input:checked {
    background-color: #2563eb;
    border-color: #2563eb;
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.loading .btn {
    pointer-events: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const precioPorPersona = {{ $cena->price }};
    const maxGuests = {{ $cena->available_spots }};
    
    // Funciones para incrementar/decrementar comensales
    window.incrementGuests = function() {
        const input = document.getElementById('cantidad_comensales');
        const currentValue = parseInt(input.value);
        
        if (currentValue < maxGuests) {
            input.value = currentValue + 1;
            updatePrices();
        }
    };
    
    window.decrementGuests = function() {
        const input = document.getElementById('cantidad_comensales');
        const currentValue = parseInt(input.value);
        
        if (currentValue > 1) {
            input.value = currentValue - 1;
            updatePrices();
        }
    };
    
    // Actualizar precios cuando cambie la cantidad
    document.getElementById('cantidad_comensales').addEventListener('input', updatePrices);
    
    function updatePrices() {
        const guests = parseInt(document.getElementById('cantidad_comensales').value) || 1;
        const total = precioPorPersona * guests;
        
        // Formatear precio
        const formattedTotal = '$' + total.toLocaleString('es-CO');
        
        // Actualizar displays
        document.getElementById('guestsDisplay').textContent = guests;
        document.getElementById('totalPrice').textContent = formattedTotal;
        document.getElementById('finalTotalPrice').textContent = formattedTotal;
        document.getElementById('summaryGuests').textContent = guests;
        document.getElementById('summarySubtotal').textContent = formattedTotal;
        document.getElementById('summaryTotal').textContent = formattedTotal;
    }
    
    // Validación del formulario
    document.getElementById('reservaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar campos requeridos
        const form = this;
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        // Validar checkboxes
        const terminosCheck = document.getElementById('acepta_terminos');
        const cancelacionCheck = document.getElementById('acepta_politica_cancelacion');
        
        if (!terminosCheck.checked) {
            alert('Debes aceptar los términos y condiciones');
            terminosCheck.focus();
            return;
        }
        
        if (!cancelacionCheck.checked) {
            alert('Debes aceptar la política de cancelación');
            cancelacionCheck.focus();
            return;
        }
        
        // Confirmación final
        const guests = document.getElementById('cantidad_comensales').value;
        const total = document.getElementById('finalTotalPrice').textContent;
        
        if (confirm(`¿Confirmas tu reserva para ${guests} persona(s) por un total de ${total}?`)) {
            // Mostrar loading
            const btn = document.getElementById('btnProcederPago');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            btn.disabled = true;
            
            // Enviar formulario
            this.submit();
        }
    });
    
    // Auto-llenar teléfono si está en el perfil del usuario
    const userPhone = '{{ $user->phone ?? "" }}';
    if (userPhone) {
        document.getElementById('telefono_contacto').value = userPhone;
    }
});
</script>
@endsection