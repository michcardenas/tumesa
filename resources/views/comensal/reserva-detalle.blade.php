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
                $pagoExitoso = $reserva->estado_pago === 'pagado';
                $canSeeExactLocation = $pagoExitoso && $hoursUntilCena <= 24;
            @endphp

            @if($canSeeExactLocation)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Ubicación exacta disponible</strong>
                    <br><small>Pago confirmado - Ya puedes ver la ubicación precisa y obtener direcciones.</small>
                </div>
            @elseif($pagoExitoso && $hoursUntilCena > 24)
                <div class="alert alert-info">
                    <i class="fas fa-clock"></i>
                    <strong>Ubicación exacta en {{ ceil($hoursUntilCena - 24) }} horas</strong>
                    <br><small>Pago confirmado - Recibirás la ubicación exacta 24 horas antes del evento.</small>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Área aproximada</strong>
                    <br><small>
                        @if($reserva->estado_pago === 'pendiente')
                            Completa el pago para acceder a la ubicación exacta.
                        @elseif($reserva->estado_pago === 'fallido')
                            El pago falló. Completa el pago para acceder a la ubicación exacta.
                        @else
                            Confirma tu pago para acceder a la ubicación exacta.
                        @endif
                    </small>
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
                        @if($pagoExitoso)
                            <button class="btn btn-primary btn-sm" onclick="openReservaDirections()" id="directions-btn">
                                @if($canSeeExactLocation)
                                    <i class="fas fa-directions"></i> Cómo llegar
                                @else
                                    <i class="fas fa-clock"></i> Disponible pronto
                                @endif
                            </button>

                            @if($canSeeExactLocation)
                            <button class="btn btn-success btn-sm mt-1" onclick="shareLocation()">
                                <i class="fas fa-share-alt"></i> Compartir
                            </button>
                            @endif
                        @else
                            <button class="btn btn-outline-warning btn-sm" onclick="openReservaDirections()" id="directions-btn">
                                <i class="fas fa-exclamation-triangle"></i> Pago requerido
                            </button>
                        @endif
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
                            <p><strong>Cantidad de Invitados :</strong> {{ $reserva->cantidad_comensales }}</p>
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

                    {{-- Botón para completar pago si está pendiente o falló --}}
                    @if(in_array($reserva->estado_pago, ['pendiente', 'fallido']))
                    <button class="btn btn-success w-100 mb-2" onclick="completarPago({{ $reserva->id }})">
                        <i class="fas fa-credit-card"></i> Completar Pago
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
    pagoExitoso: {{ $reserva->estado_pago === 'pagado' ? 'true' : 'false' }},
    canSeeExactLocation: {{ ($reserva->estado_pago === 'pagado' && now()->diffInHours($reserva->cena->datetime, false) <= 24) ? 'true' : 'false' }},
    estadoPago: @json($reserva->estado_pago),
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
    
    let displayLat, displayLng, zoom, showCircle = false;
    
    if (reservaData.canSeeExactLocation) {
        // Mostrar ubicación exacta para comensales 24h antes
        displayLat = reservaData.exactLat;
        displayLng = reservaData.exactLng;
        zoom = 17;
        showCircle = false;
    } else {
        // Mostrar área aproximada para comensales con reserva
        const offsetRange = 0.002; // Menor offset porque ya tiene reserva (~200m)
        const randomOffsetLat = (Math.random() - 0.5) * offsetRange;
        const randomOffsetLng = (Math.random() - 0.5) * offsetRange;
        
        displayLat = reservaData.exactLat + randomOffsetLat;
        displayLng = reservaData.exactLng + randomOffsetLng;
        zoom = 15;
        showCircle = true;
    }
    
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
    
    // Crear marcador
    reservaMarker = new google.maps.Marker({
        position: { lat: displayLat, lng: displayLng },
        map: reservaMap,
        title: reservaData.cenaTitle,
        icon: {
            path: reservaData.canSeeExactLocation ? 
                google.maps.SymbolPath.BACKWARD_CLOSED_ARROW : 
                google.maps.SymbolPath.CIRCLE,
            scale: reservaData.canSeeExactLocation ? 10 : 15,
            fillColor: reservaData.canSeeExactLocation ? '#059669' : '#f59e0b',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 3
        }
    });
    
    // Círculo de área aproximada si es necesario
    if (showCircle) {
        new google.maps.Circle({
            strokeColor: '#f59e0b',
            strokeOpacity: 0.6,
            strokeWeight: 2,
            fillColor: '#f59e0b',
            fillOpacity: 0.15,
            map: reservaMap,
            center: { lat: displayLat, lng: displayLng },
            radius: 300 // Radio más pequeño para comensales (300m)
        });
    }
    
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
    let statusBadge, additionalInfo;

    if (reservaData.canSeeExactLocation) {
        statusBadge = '<span style="background: #059669; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">UBICACIÓN EXACTA</span>';
        additionalInfo = '<div style="margin-top: 8px; padding: 6px; background: #f0fdf4; border-radius: 4px;"><small style="color: #059669; font-weight: 500;"><i class="fas fa-directions"></i> Puedes obtener direcciones exactas</small></div>';
    } else if (reservaData.pagoExitoso) {
        statusBadge = '<span style="background: #2563eb; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">ÁREA DE COMENSAL</span>';
        additionalInfo = `<div style="margin-top: 8px; padding: 6px; background: #eff6ff; border-radius: 4px;"><small style="color: #1d4ed8; font-weight: 500;"><i class="fas fa-clock"></i> Ubicación exacta en ${Math.ceil(reservaData.hoursUntilCena - 24)}h</small></div>`;
    } else {
        statusBadge = '<span style="background: #f59e0b; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">ÁREA APROXIMADA</span>';
        let pagoMessage = '';
        switch(reservaData.estadoPago) {
            case 'pendiente':
                pagoMessage = 'Completa el pago para acceder a la ubicación exacta';
                break;
            case 'fallido':
                pagoMessage = 'El pago falló. Completa el pago para la ubicación exacta';
                break;
            default:
                pagoMessage = 'Confirma tu pago para acceder a la ubicación exacta';
        }
        additionalInfo = `<div style="margin-top: 8px; padding: 6px; background: #fffbeb; border-radius: 4px;"><small style="color: #92400e; font-weight: 500;"><i class="fas fa-exclamation-triangle"></i> ${pagoMessage}</small></div>`;
    }

    return `
        <div style="font-family: Inter, sans-serif; padding: 12px; max-width: 300px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                ${statusBadge}
                <span style="background: ${reservaData.pagoExitoso ? '#059669' : '#dc2626'}; color: white; padding: 3px 6px; border-radius: 8px; font-size: 10px; font-weight: 600;">
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

    if (reservaData.canSeeExactLocation) {
        btn.innerHTML = '<i class="fas fa-directions"></i> Cómo llegar';
        btn.className = 'btn btn-success btn-sm';
        btn.disabled = false;
    } else if (reservaData.pagoExitoso) {
        btn.innerHTML = `<i class="fas fa-clock"></i> Disponible en ${Math.ceil(reservaData.hoursUntilCena - 24)}h`;
        btn.className = 'btn btn-outline-primary btn-sm';
        btn.disabled = false;
    } else {
        btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Pago requerido';
        btn.className = 'btn btn-outline-warning btn-sm';
        btn.disabled = false;
    }
}

function openReservaDirections() {
    if (reservaData.canSeeExactLocation) {
        // Abrir direcciones exactas
        const url = `https://www.google.com/maps/dir/?api=1&destination=${reservaData.exactLat},${reservaData.exactLng}`;
        window.open(url, '_blank');
    } else if (reservaData.pagoExitoso) {
        // Mostrar información para comensales con pago exitoso pero más de 24h antes
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Ubicación exacta pronto',
                html: `
                    <div style="text-align: left;">
                        <p>Tu pago está <strong>confirmado</strong>. La ubicación exacta estará disponible en <strong>${Math.ceil(reservaData.hoursUntilCena - 24)} horas</strong>.</p>
                        <div style="background: #f0f9ff; padding: 12px; border-radius: 6px; margin: 12px 0;">
                            <strong>Tu código de reserva:</strong> <code>${reservaData.reservaCode}</code><br>
                            <strong>Estado de pago:</strong> <span style="color: #059669;">✓ Confirmado</span>
                        </div>
                        <p><strong>Mientras tanto:</strong></p>
                        <ul style="text-align: left; padding-left: 20px;">
                            <li>Tu reserva está confirmada y asegurada</li>
                            <li>Recibirás la ubicación exacta automáticamente</li>
                            <li>Puedes ver el área de comensal en el mapa</li>
                            <li>Contacta al chef si tienes dudas</li>
                        </ul>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert(`Ubicación exacta disponible en ${Math.ceil(reservaData.hoursUntilCena - 24)} horas para comensales con pago confirmado.`);
        }
    } else {
        // Mostrar información para comensales sin pago exitoso
        let titulo, mensaje, icono;

        if (reservaData.estadoPago === 'pendiente') {
            titulo = 'Pago pendiente';
            mensaje = `
                <div style="text-align: left;">
                    <p>Para acceder a la ubicación exacta, necesitas <strong>completar el pago</strong> de tu reserva.</p>
                    <div style="background: #fffbeb; padding: 12px; border-radius: 6px; margin: 12px 0; border-left: 4px solid #f59e0b;">
                        <strong>Tu código de reserva:</strong> <code>${reservaData.reservaCode}</code><br>
                        <strong>Estado de pago:</strong> <span style="color: #f59e0b;">⏳ Pendiente</span>
                    </div>
                    <p><strong>Puedes:</strong></p>
                    <ul style="text-align: left; padding-left: 20px;">
                        <li>Completar el pago usando el botón "Completar Pago"</li>
                        <li>Ver el área aproximada en el mapa actual</li>
                        <li>Contactar al chef para dudas</li>
                    </ul>
                </div>
            `;
            icono = 'warning';
        } else if (reservaData.estadoPago === 'fallido') {
            titulo = 'Pago falló';
            mensaje = `
                <div style="text-align: left;">
                    <p>Tu pago no se pudo procesar. <strong>Completa el pago</strong> para acceder a la ubicación exacta.</p>
                    <div style="background: #fef2f2; padding: 12px; border-radius: 6px; margin: 12px 0; border-left: 4px solid #dc2626;">
                        <strong>Tu código de reserva:</strong> <code>${reservaData.reservaCode}</code><br>
                        <strong>Estado de pago:</strong> <span style="color: #dc2626;">❌ Falló</span>
                    </div>
                    <p><strong>Acción requerida:</strong></p>
                    <ul style="text-align: left; padding-left: 20px;">
                        <li>Usa el botón "Completar Pago" para intentar nuevamente</li>
                        <li>Verifica tu método de pago</li>
                        <li>Contacta soporte si el problema persiste</li>
                    </ul>
                </div>
            `;
            icono = 'error';
        } else {
            titulo = 'Confirma tu pago';
            mensaje = `
                <div style="text-align: left;">
                    <p>Para acceder a la ubicación exacta, necesitas <strong>confirmar tu pago</strong>.</p>
                    <div style="background: #f9fafb; padding: 12px; border-radius: 6px; margin: 12px 0;">
                        <strong>Tu código de reserva:</strong> <code>${reservaData.reservaCode}</code>
                    </div>
                    <p>Completa el proceso de pago para asegurar tu lugar en la cena.</p>
                </div>
            `;
            icono = 'info';
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: titulo,
                html: mensaje,
                icon: icono,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert(titulo + ': ' + mensaje.replace(/<[^>]*>/g, '').trim());
        }
    }
}

function shareLocation() {
    if (!reservaData.canSeeExactLocation) return;
    
    const shareData = {
        title: reservaData.cenaTitle,
        text: `Ubicación de la cena: ${reservaData.cenaLocation}`,
        url: `https://www.google.com/maps/dir/?api=1&destination=${reservaData.exactLat},${reservaData.exactLng}`
    };
    
    if (navigator.share) {
        navigator.share(shareData);
    } else {
        // Fallback: copiar al portapapeles
        navigator.clipboard.writeText(shareData.url).then(() => {
            alert('Enlace de ubicación copiado al portapapeles');
        });
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

function completarPago(reservaId) {
    // Redirigir al checkout con la reserva existente para completar el pago
    window.location.href = `/comensal/completar-pago/${reservaId}`;
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