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
                        <p><strong>Fecha de Reserva:</strong> 
                            {{ $reserva->created_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY H:mm') }}
                        </p>    
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
                <div class="menu-display">
                    {{-- CAMBIO PRINCIPAL: Usar {!! !!} en lugar de {{ }} --}}
                    {!! $reserva->cena->menu !!}
                </div>
            </div>

                    @if($reserva->cena->special_requirements)
                    <div class="mt-3">
                        <h5><i class="fas fa-info-circle text-warning"></i> Requisitos Especiales</h5>
                        <p>{{ $reserva->cena->special_requirements }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @if($reserva->cena->latitude && $reserva->cena->longitude)
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-map-marked-alt"></i> Ubicación de la Cena
        </h5>
    </div>
    <div class="card-body">
        <!-- Estado de ubicación -->
        <div class="location-status-bar mb-3">
            @php
                $hoursUntilCena = now()->diffInHours($reserva->cena->datetime, false);
                $canSeeExactLocation = false; // Siempre false para mostrar rango
            @endphp

            @if($hoursUntilCena <= 1)
                <div class="alert alert-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>Área de la cena</strong>
                    <br><small>Tu reserva está confirmada. El área mostrada te ayudará a ubicarte cerca del lugar.</small>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>Área aproximada</strong>
                    <br><small>Como comensal confirmado, puedes ver el área general donde se realizará la cena.</small>
                </div>
            @endif
        </div>
        
        <!-- Contenedor del mapa -->
        <div class="map-container-reserva">
            <div id="reserva-map" style="height: 350px; width: 100%; border-radius: 8px;"></div>
            
            <!-- Información adicional -->
            <div class="location-info mt-3">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-1"><strong>Fecha y Hora:</strong> {{ $reserva->cena->datetime->format('d/m/Y H:i') }}</p>
                        <p class="mb-0"><strong>Tu Código de Reserva:</strong> <code>{{ $reserva->codigo_reserva }}</code></p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-primary btn-sm" onclick="openReservaDirections()" id="directions-btn">
                            <i class="fas fa-info-circle"></i> Información de Ubicación
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

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

@if($reserva->reseña)
    <!-- Mostrar calificación existente -->
    <div class="card border-warning mb-2">
        <div class="card-body text-center">
            <h6 class="card-title">Tu Calificación</h6>
            <div class="mb-2">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $reserva->reseña->rating)
                        <i class="fas fa-star text-warning"></i>
                    @else
                        <i class="far fa-star text-muted"></i>
                    @endif
                @endfor
            </div>
            @if($reserva->reseña->comentario)
                <p class="card-text small">{{ $reserva->reseña->comentario }}</p>
            @endif
        </div>
    </div>
@elseif($puedeCalificar)
    <!-- Mostrar botón para calificar -->
    <button class="btn btn-warning w-100 mb-2" onclick="calificarCena({{ $reserva->cena->id }}, {{ $reserva->id }})">
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
                            <i class="fas fa-envelope"></i> {{ $reserva->email_contacto }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let reservaMap;
let reservaMarker;

// Datos de la reserva y cena
const reservaData = {
    exactLat: {{ $reserva->cena->latitude }},
    exactLng: {{ $reserva->cena->longitude }},
    hoursUntilCena: {{ now()->diffInHours($reserva->cena->datetime, false) }},
    canSeeExactLocation: false, // Siempre false para mostrar área aproximada
    cenaTitle: @json($reserva->cena->title),
    cenaLocation: @json($reserva->cena->location),
    cenaDateTime: @json($reserva->cena->datetime->format('d/m/Y H:i')),
    reservaCode: @json($reserva->codigo_reserva),
    reservaId: {{ $reserva->id }}
};

function initReservaMapCallback() {
    initReservaMap();
}

function initReservaMap() {
    if (typeof google === 'undefined' || !google.maps) {
        console.error('Google Maps no disponible');
        return;
    }
    
    let displayLat, displayLng, zoom, showCircle = true;

    // Siempre mostrar área aproximada con offset aleatorio
    const offsetRange = 0.003; // Offset para crear área aproximada (~300m)
    const randomOffsetLat = (Math.random() - 0.5) * offsetRange;
    const randomOffsetLng = (Math.random() - 0.5) * offsetRange;

    displayLat = reservaData.exactLat + randomOffsetLat;
    displayLng = reservaData.exactLng + randomOffsetLng;
    zoom = 14; // Zoom más alejado para mostrar área general
    
    // Configurar mapa
    reservaMap = new google.maps.Map(document.getElementById('reserva-map'), {
        center: { lat: displayLat, lng: displayLng },
        zoom: zoom,
        styles: [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            }
        ]
    });
    
    // Crear marcador (siempre estilo área aproximada)
    reservaMarker = new google.maps.Marker({
        position: { lat: displayLat, lng: displayLng },
        map: reservaMap,
        title: reservaData.cenaTitle + ' - Área de comensal',
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 12,
            fillColor: '#2563eb', // Azul para comensales confirmados
            fillOpacity: 0.8,
            strokeColor: '#ffffff',
            strokeWeight: 3
        }
    });
    
    // Círculo de área aproximada (siempre visible)
    new google.maps.Circle({
        strokeColor: '#2563eb',
        strokeOpacity: 0.6,
        strokeWeight: 2,
        fillColor: '#2563eb',
        fillOpacity: 0.1,
        map: reservaMap,
        center: { lat: displayLat, lng: displayLng },
        radius: 400 // Radio para área de comensal confirmado (400m)
    });
    
    // InfoWindow
    const infoWindow = new google.maps.InfoWindow({
        content: createInfoWindowContent()
    });
    
    reservaMarker.addListener('click', () => {
        infoWindow.open(reservaMap, reservaMarker);
    });
    
    // Actualizar botón de direcciones
    updateDirectionsButton();
    
    console.log('✅ Mapa de reserva inicializado');
}

function createInfoWindowContent() {
    const statusBadge = '<span style="background: #2563eb; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">ÁREA DE COMENSAL</span>';

    const additionalInfo = '<div style="margin-top: 8px; padding: 6px; background: #eff6ff; border-radius: 4px;"><small style="color: #1d4ed8; font-weight: 500;"><i class="fas fa-check-circle"></i> Tu reserva está confirmada en esta área</small></div>';

    return `
        <div style="font-family: Inter, sans-serif; padding: 12px; max-width: 300px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                ${statusBadge}
                <span style="background: #059669; color: white; padding: 3px 6px; border-radius: 8px; font-size: 10px; font-weight: 600;">
                    ${reservaData.reservaCode}
                </span>
            </div>
            <strong style="color: #111827; font-size: 16px;">${reservaData.cenaTitle}</strong><br>
            <div style="margin: 8px 0;">
                <small style="color: #6b7280;">
                    <i class="fas fa-map-marker-alt"></i> ${reservaData.cenaLocation}
                </small><br>
                <small style="color: #2563eb;">
                    <i class="fas fa-calendar"></i> ${reservaData.cenaDateTime}
                </small>
            </div>
            ${additionalInfo}
        </div>
    `;
}

function updateDirectionsButton() {
    const btn = document.getElementById('directions-btn');
    if (!btn) return;

    // Siempre mostrar botón de información de ubicación
    btn.innerHTML = '<i class="fas fa-info-circle"></i> Información de Ubicación';
    btn.className = 'btn btn-outline-primary btn-sm';
    btn.disabled = false;
}

function openReservaDirections() {
    // Mostrar información del área para comensales confirmados
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Información de Ubicación',
            html: `
                <div style="text-align: left;">
                    <p>Como comensal confirmado, puedes ver el <strong>área general</strong> donde se realizará la cena.</p>
                    <div style="background: #eff6ff; padding: 12px; border-radius: 6px; margin: 12px 0;">
                        <strong>Tu código de reserva:</strong> <code>${reservaData.reservaCode}</code>
                    </div>
                    <p><strong>Información importante:</strong></p>
                    <ul style="text-align: left; padding-left: 20px;">
                        <li>El área mostrada te ayuda a ubicarte cerca del lugar</li>
                        <li>Tu reserva está confirmada y asegurada</li>
                        <li>El chef te proporcionará detalles específicos si es necesario</li>
                        <li>Contacta al chef directamente para cualquier duda</li>
                    </ul>
                    <div style="background: #f0fdf4; padding: 12px; border-radius: 6px; margin-top: 12px; border-left: 4px solid #059669;">
                        <small><strong>Nota:</strong> Este sistema protege la privacidad tanto de chefs como de comensales.</small>
                    </div>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#2563eb'
        });
    } else {
        alert('Como comensal confirmado, puedes ver el área general de la cena en el mapa.');
    }
}


// Cargar Google Maps si no está cargado
document.addEventListener('DOMContentLoaded', function() {
    if (typeof google === 'undefined' || !google.maps) {
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCuh8GSFyFxvDaiEeWcW7JXs2KIcf89dHY&libraries=places&callback=initReservaMapCallback';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    } else {
        initReservaMap();
    }
});

// Hacer función global
window.initReservaMapCallback = initReservaMapCallback;
function cancelarReserva(id) {
    if(confirm('¿Estás seguro de cancelar esta reserva? Esta acción no se puede deshacer.')) {
        // Implementar cancelación
        window.location.href = `/reservas/${id}/cancelar`;
    }
}

function calificarCena(cenaId, reservaId) {
    window.location.href = `/reseñas/${cenaId}/${reservaId}/create`;
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