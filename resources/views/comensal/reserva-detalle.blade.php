@extends('layouts.app_comensal')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Detalle de Reserva</h2>
                <a href="{{ route('comensal.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <!-- Card de Información de la Cena -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $reserva->cena->title }}</h4>
                </div>
                
                @if($reserva->cena->cover_image_url)
                <img src="{{ $reserva->cena->cover_image_url }}" class="card-img-top" alt="{{ $reserva->cena->title }}" style="max-height: 300px; object-fit: cover;">
                @endif
                
                <div class="card-body">
                    <!-- Estado de la cena -->
                    <div class="mb-3">
                        @if($cenaEnCurso)
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-circle" style="animation: pulse 2s infinite;"></i> CENA EN CURSO
                            </span>
                        @elseif($cenaPasada)
                            <span class="badge bg-secondary fs-6">CENA FINALIZADA</span>
                        @elseif($minutosParaCena <= 30 && $minutosParaCena > 0)
                            <span class="badge bg-warning text-dark fs-6">COMIENZA EN {{ round($minutosParaCena) }} MINUTOS</span>
                        @endif
                    </div>

                    <!-- Información del Chef -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5><i class="fas fa-chef-hat text-primary"></i> Chef Anfitrión</h5>
                            <p class="mb-1"><strong>{{ $reserva->cena->chef->name }}</strong></p>
                            @if($reserva->cena->chef->especialidad)
                            <p class="text-muted">{{ $reserva->cena->chef->especialidad }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Detalles de la Cena -->
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-calendar text-primary"></i> <strong>Fecha:</strong> {{ $reserva->cena->datetime->format('l, d \d\e F Y') }}</p>
                            <p><i class="fas fa-clock text-primary"></i> <strong>Hora:</strong> {{ $reserva->cena->datetime->format('g:i A') }}</p>
                            <p><i class="fas fa-map-marker-alt text-primary"></i> <strong>Ubicación:</strong> {{ $reserva->cena->location }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-users text-primary"></i> <strong>Capacidad:</strong> {{ $reserva->cena->guests_current }}/{{ $reserva->cena->guests_max }} comensales</p>
                            <p><i class="fas fa-dollar-sign text-primary"></i> <strong>Precio por persona:</strong> {{ $reserva->cena->formatted_price }}</p>
                        </div>
                    </div>

                    <!-- Menú -->
                    <div class="mt-3">
                        <h5><i class="fas fa-utensils text-primary"></i> Menú</h5>
                        <p>{{ $reserva->cena->menu }}</p>
                    </div>

                    @if($reserva->cena->special_requirements)
                    <div class="mt-3">
                        <h5><i class="fas fa-info-circle text-warning"></i> Requisitos Especiales</h5>
                        <p>{{ $reserva->cena->special_requirements }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card de Tu Reserva -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Tu Reserva</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Código de Reserva:</strong> {{ $reserva->codigo_reserva }}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge {{ $reserva->estado_badge['class'] }}">
                                    {{ $reserva->estado_badge['texto'] }}
                                </span>
                            </p>
                            <p><strong>Estado de Pago:</strong> 
                                <span class="badge {{ $reserva->estado_pago_badge['class'] }}">
                                    {{ $reserva->estado_pago_badge['texto'] }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cantidad de Comensales:</strong> {{ $reserva->cantidad_comensales }}</p>
                            <p><strong>Total Pagado:</strong> {{ $reserva->precio_total_formateado }}</p>
                            <p><strong>Fecha de Reserva:</strong> 
                                {{ $reserva->created_at->translatedFormat('d \d\e F \d\e Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($reserva->asistencia_marcada)
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-check-circle"></i> <strong>Asistencia:</strong> 
                        <span class="badge {{ $reserva->estado_asistencia_badge['class'] }}">
                            {{ $reserva->estado_asistencia_badge['texto'] }}
                        </span>
                        @if($reserva->comentarios_asistencia)
                        <br><small>{{ $reserva->comentarios_asistencia }}</small>
                        @endif
                    </div>
                    @endif

                    @if($reserva->restricciones_alimentarias || $reserva->solicitudes_especiales)
                    <div class="mt-3">
                        <h6>Información Adicional Proporcionada:</h6>
                        @if($reserva->restricciones_alimentarias)
                        <p><strong>Restricciones Alimentarias:</strong> {{ $reserva->restricciones_alimentarias }}</p>
                        @endif
                        @if($reserva->solicitudes_especiales)
                        <p><strong>Solicitudes Especiales:</strong> {{ $reserva->solicitudes_especiales }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar de Acciones -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 100px;">
                <div class="card-body">
                    <h5>Acciones</h5>
                    
                    @if($puedeCancelar)
                    <button class="btn btn-danger w-100 mb-2" onclick="cancelarReserva({{ $reserva->id }})">
                        <i class="fas fa-times-circle"></i> Cancelar Reserva
                    </button>
                    @endif

                    @if($puedeCalificar)
                    <button class="btn btn-warning w-100 mb-2" onclick="calificarCena({{ $reserva->id }})">
                        <i class="fas fa-star"></i> Calificar Experiencia
                    </button>
                    @endif

                    @if($reserva->calificacion)
                    <div class="text-center mb-3">
                        <p class="mb-1">Tu Calificación:</p>
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $reserva->calificacion)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        @if($reserva->resena)
                        <p class="mt-2 text-muted small">"{{ $reserva->resena }}"</p>
                        @endif
                    </div>
                    @endif

                    <hr>
                    
                    <div class="contact-info">
                        <h6>Información de Contacto</h6>
                        <p class="small">
                            <i class="fas fa-user"></i> {{ $reserva->nombre_contacto }}<br>
                            <i class="fas fa-phone"></i> {{ $reserva->telefono_contacto }}<br>
                            <i class="fas fa-envelope"></i> {{ $reserva->email_contacto }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelarReserva(id) {
    if(confirm('¿Estás seguro de cancelar esta reserva? Esta acción no se puede deshacer.')) {
        // Implementar cancelación
        window.location.href = `/reservas/${id}/cancelar`;
    }
}

function calificarCena(id) {
    // Implementar modal o redirección para calificar
    window.location.href = `/reservas/${id}/calificar`;
}
</script>

<style>
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.3; }
    100% { opacity: 1; }
}
</style>
@endsection