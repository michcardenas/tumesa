@extends('layouts.app_chefs')

@section('title', 'Detalles de la Cena')
@section('page-title', $cenaData['title'])
@section('page-subtitle', 'Detalles de tu cena')

@section('header-actions')
<div class="header-actions">
    @if($cenaData['can_edit'])
    <a href="{{ route('chef.dinners.edit', $cena->id) }}" class="btn btn-outline-light btn-sm me-2">
        <i class="fas fa-edit"></i> Editar
    </a>
    @endif
    <a href="{{ route('chef.dashboard') }}" class="btn btn-outline-light btn-sm">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>
@endsection

@section('content')
<!-- Navegación -->
<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('chef.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('chef.dashboard') }}">Mis Cenas</a></li>
                <li class="breadcrumb-item active">{{ $cenaData['title'] }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Estado y información general -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h3 class="card-title">{{ $cenaData['title'] }}</h3>
                    <span class="badge bg-{{ $cenaData['status_color'] }} fs-6">
                        {{ $cenaData['status_label'] }}
                    </span>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-calendar text-primary"></i> Fecha y Hora</h6>
                        <p class="mb-3">{{ $cenaData['formatted_date'] }} a las {{ $cenaData['formatted_time'] }}</p>
                        
                        @if($cenaData['days_until'] >= 0)
                            <p class="text-success small">
                                <i class="fas fa-clock"></i> 
                                @if($cenaData['days_until'] == 0)
                                    ¡Es hoy!
                                @elseif($cenaData['days_until'] == 1)
                                    Mañana
                                @else
                                    En {{ $cenaData['days_until'] }} días
                                @endif
                            </p>
                        @else
                            <p class="text-muted small">
                                <i class="fas fa-history"></i> Hace {{ abs($cenaData['days_until']) }} días
                            </p>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <h6><i class="fas fa-map-marker-alt text-danger"></i> Ubicación</h6>
                        <p class="mb-3">{{ $cenaData['location'] }}</p>
                        <small class="text-muted">
                            <i class="fas fa-crosshairs"></i> 
                            {{ $cenaData['latitude'] }}, {{ $cenaData['longitude'] }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas de reservas -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6><i class="fas fa-users text-info"></i> Reservas</h6>
                <div class="text-center">
                    <h2 class="text-primary">{{ $cenaData['guests_current'] }}/{{ $cenaData['guests_max'] }}</h2>
                    <p class="text-muted mb-2">Comensales</p>
                    
                    @if($cenaData['available_spots'] > 0)
                        <p class="text-success small">
                            <i class="fas fa-check-circle"></i> {{ $cenaData['available_spots'] }} espacios disponibles
                        </p>
                    @else
                        <p class="text-warning small">
                            <i class="fas fa-exclamation-circle"></i> Cena completa
                        </p>
                    @endif
                </div>
                
                <hr>
                
                <h6><i class="fas fa-dollar-sign text-success"></i> Ingresos</h6>
                <p class="mb-1">
                    <strong>Por persona:</strong> {{ $cenaData['formatted_price'] }}
                </p>
                <p class="mb-1">
                    <strong>Actual:</strong> ${{ number_format($cenaData['current_revenue'], 0, ',', '.') }}
                </p>
                <p class="text-muted small">
                    <strong>Potencial:</strong> ${{ number_format($cenaData['total_revenue_potential'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Información de Comensales y Reservas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Comensales y Reservas</h5>
            </div>
            <div class="card-body">
                @if($reservasData['total_reservas'] > 0)
                    <!-- Estadísticas de Reservas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <h4 class="text-primary mb-1">{{ $reservasData['total_reservas'] }}</h4>
                                <small class="text-muted">Total Reservas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <h4 class="text-success mb-1">{{ $reservasData['reservas_confirmadas'] }}</h4>
                                <small class="text-muted">Confirmadas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <h4 class="text-info mb-1">{{ $reservasData['reservas_pagadas'] }}</h4>
                                <small class="text-muted">Pagadas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <h4 class="text-warning mb-1">{{ $reservasData['reservas_pendientes'] }}</h4>
                                <small class="text-muted">Pendientes</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded">
                                <h6 class="text-primary"><i class="fas fa-users me-2"></i>Comensales Confirmados</h6>
                                <p class="mb-0 h5">{{ $reservasData['total_comensales_reservados'] }} personas</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded">
                                <h6 class="text-success"><i class="fas fa-dollar-sign me-2"></i>Ingresos Pagados</h6>
                                <p class="mb-0 h5">${{ number_format($reservasData['ingresos_confirmados'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded">
                                <h6 class="text-info"><i class="fas fa-chart-line me-2"></i>Promedio por Reserva</h6>
                                <p class="mb-0 h5">{{ $reservasData['promedio_comensales_por_reserva'] }} comensales</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Reservas -->
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-list me-2"></i>Detalle de Reservas</h6>
                    <div class="reservas-list">
                        @foreach($reservasData['lista_reservas'] as $reserva)
                            <div class="reserva-item border rounded p-3 mb-3 {{ $reserva->estado == 'cancelada' ? 'bg-light' : '' }}">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            @if($reserva->user && $reserva->user->avatar)
                                                <img src="{{ $reserva->user->avatar_url }}" alt="{{ $reserva->user->name }}" 
                                                     class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $reserva->nombre_contacto ?? ($reserva->user->name ?? 'Sin nombre') }}</h6>
                                                <small class="text-muted">{{ $reserva->codigo_reserva }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <span class="badge {{ $reserva->estado_badge['class'] }}">
                                            {{ $reserva->estado_badge['texto'] }}
                                        </span>
                                        @if($reserva->estado_pago != 'pendiente')
                                            <br><small class="badge {{ $reserva->estado_pago_badge['class'] }} mt-1">
                                                {{ $reserva->estado_pago_badge['texto'] }}
                                            </small>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-2 text-center">
                                        <span class="h6 text-primary">{{ $reserva->cantidad_comensales }}</span>
                                        <small class="d-block text-muted">{{ $reserva->cantidad_comensales == 1 ? 'comensal' : 'comensales' }}</small>
                                    </div>
                                    
                                    <div class="col-md-2 text-center">
                                        <span class="h6 text-success">{{ $reserva->precio_total_formateado }}</span>
                                        <small class="d-block text-muted">Total</small>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="contact-info">
                                            @if($reserva->email_contacto)
                                                <small class="d-block"><i class="fas fa-envelope me-1"></i>{{ $reserva->email_contacto }}</small>
                                            @endif
                                           
                                            @if($reserva->restricciones_alimentarias)
                                                <small class="d-block text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Restricciones</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if($reserva->comentarios_especiales || $reserva->restricciones_alimentarias || $reserva->solicitudes_especiales)
                                    <hr class="my-2">
                                    <div class="additional-info">
                                        @if($reserva->restricciones_alimentarias)
                                            <p class="mb-1"><strong>Restricciones alimentarias:</strong> <span class="text-warning">{{ $reserva->restricciones_alimentarias }}</span></p>
                                        @endif
                                        @if($reserva->solicitudes_especiales)
                                            <p class="mb-1"><strong>Solicitudes especiales:</strong> {{ $reserva->solicitudes_especiales }}</p>
                                        @endif
                                        @if($reserva->comentarios_especiales)
                                            <p class="mb-0"><strong>Comentarios:</strong> {{ $reserva->comentarios_especiales }}</p>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($reserva->calificacion)
                                    <hr class="my-2">
                                    <div class="rating-info">
                                        <strong>Calificación:</strong>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $reserva->calificacion ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        @if($reserva->resena)
                                            <p class="mt-1 mb-0 fst-italic">"{{ $reserva->resena }}"</p>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="reservation-dates mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>Reservado: {{ $reserva->created_at->format('d/m/Y H:i') }}
                                        @if($reserva->fecha_confirmacion)
                                            | <i class="fas fa-check me-1"></i>Confirmado: {{ $reserva->fecha_confirmacion->format('d/m/Y H:i') }}
                                        @endif
                                        @if($reserva->fecha_pago)
                                            | <i class="fas fa-dollar-sign me-1"></i>Pagado: {{ $reserva->fecha_pago->format('d/m/Y H:i') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Sin Reservas -->
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted mb-2">Sin reservas aún</h5>
                        <p class="text-muted">Cuando los comensales hagan reservas aparecerán aquí con toda su información.</p>
                        @if($cenaData['status'] == 'draft')
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Tip:</strong> Publica tu cena para que los comensales puedan hacer reservas.
                            </div>
                        @elseif(!$cenaData['is_active'])
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Nota:</strong> Tu cena está inactiva. Actívala para recibir reservas.
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Menú -->

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5><i class="fas fa-utensils text-warning"></i> Descripción del Menú</h5>
                <div class="menu-description">
                    {!! $cenaData['menu'] !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Imágenes -->
@if($cenaData['cover_image_url'] || $cenaData['gallery_image_urls']->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5><i class="fas fa-images text-info"></i> Imágenes de la Cena</h5>
                
                @if($cenaData['cover_image_url'])
                <div class="mb-3">
                    <h6>Imagen de Portada</h6>
                    <img src="{{ $cenaData['cover_image_url'] }}" 
                         alt="Imagen de portada" 
                         class="img-fluid rounded shadow-sm"
                         style="max-height: 300px; width: auto;">
                </div>
                @endif
                
                @if($cenaData['gallery_image_urls']->count() > 0)
                <div class="mb-3">
                    <h6>Galería de Imágenes</h6>
                    <div class="row">
                        @foreach($cenaData['gallery_image_urls'] as $imageUrl)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <img src="{{ $imageUrl }}" 
                                 alt="Imagen de galería" 
                                 class="img-fluid rounded shadow-sm gallery-image"
                                 style="height: 150px; width: 100%; object-fit: cover; cursor: pointer;"
                                 onclick="showImageModal('{{ $imageUrl }}')">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Mapa -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5><i class="fas fa-map-marked-alt text-success"></i> Ubicación en el Mapa</h5>
                <div id="showMap" style="height: 400px; border-radius: 8px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Información adicional -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6><i class="fas fa-info-circle text-primary"></i> Información de Creación</h6>
                <p class="mb-1">
                    <strong>Creada:</strong> {{ $cenaData['created_at']->format('d/m/Y H:i') }}
                </p>
                <p class="mb-1">
                    <strong>Última actualización:</strong> {{ $cenaData['updated_at']->format('d/m/Y H:i') }}
                </p>
                <p class="mb-0">
                    <strong>Estado:</strong> 
                    <span class="badge bg-{{ $cenaData['status_color'] }}">
                        {{ $cenaData['status_label'] }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6><i class="fas fa-cog text-secondary"></i> Acciones</h6>
                <div class="d-grid gap-2">
                    @if($cenaData['can_edit'])
                    <a href="{{ route('chef.dinners.edit', $cena->id) }}" class="btn btn-outline-success">
                        <i class="fas fa-edit"></i> Editar Cena
                    </a>
                    @endif
                    
                    <a href="{{ route('chef.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt"></i> Volver al Dashboard
                    </a>
                    
                    @if(!$cenaData['is_past'])
                    <button class="btn btn-outline-info" onclick="copyShareLink()">
                        <i class="fas fa-share"></i> Compartir Cena
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imagen de la Cena</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Imagen" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.menu-description {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #2563eb;
    font-size: 1.1rem;
    line-height: 1.6;
}

.gallery-image:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.header-actions .btn {
    border-color: rgba(255,255,255,0.3);
}

.header-actions .btn:hover {
    background-color: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.5);
}

#showMap {
    border: 2px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Estilos adicionales para la información del chef */
.chef-avatar-container {
    position: relative;
}

.chef-avatar-large {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-width: 3px !important;
}

.chef-bio {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid #2563eb;
}

.chef-social .btn {
    font-size: 0.875rem;
}

.rating-stars {
    margin-bottom: 0.5rem;
}

.rating-stars i {
    font-size: 1.1rem;
}

.breadcrumb {
    background: none;
    padding: 0.5rem 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d;
}

/* Estilos para la sección de comensales */
.stat-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef !important;
    transition: all 0.2s ease;
}

.stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.info-card {
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.info-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.reserva-item {
    background: #ffffff;
    transition: all 0.2s ease;
}

.reserva-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.contact-info {
    font-size: 0.875rem;
}

.additional-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    border-left: 3px solid #17a2b8;
}

.rating-info {
    background: #fff3cd;
    padding: 0.75rem;
    border-radius: 6px;
    border-left: 3px solid #ffc107;
}

.reservation-dates {
    font-size: 0.8rem;
}

.reservas-list {
    max-height: 600px;
    overflow-y: auto;
}

.reservas-list::-webkit-scrollbar {
    width: 6px;
}

.reservas-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.reservas-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.reservas-list::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

@push('scripts')
<script>
// Variables para el mapa
let showMap;
let showMarker;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa
    initShowMap();
});

// Función para mostrar imagen en modal
function showImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Función para copiar enlace de compartir
function copyShareLink() {
    const url = window.location.href.replace('/chef/dinners/', '/cenas/');
    navigator.clipboard.writeText(url).then(() => {
        alert('Enlace copiado al portapapeles');
    }).catch(() => {
        alert('No se pudo copiar el enlace');
    });
}

// Inicializar mapa de solo lectura
function initShowMap() {
    if (typeof google === 'undefined') {
        loadGoogleMapsForShow();
        return;
    }

    const location = {
        lat: {{ $cenaData['latitude'] }},
        lng: {{ $cenaData['longitude'] }}
    };

    showMap = new google.maps.Map(document.getElementById('showMap'), {
        zoom: 15,
        center: location,
        mapTypeControl: false,
        streetViewControl: true,
        fullscreenControl: true,
        scrollwheel: true,
        disableDoubleClickZoom: false
    });

    showMarker = new google.maps.Marker({
        position: location,
        map: showMap,
        title: '{{ addslashes($cenaData["title"]) }}',
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#dc2626" stroke="#ffffff" stroke-width="1"/>
                    <circle cx="12" cy="9" r="2" fill="#ffffff"/>
                </svg>
            `),
            scaledSize: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(20, 40)
        }
    });

    // InfoWindow más simple para evitar duplicación de información
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="padding: 10px; font-family: 'Segoe UI', sans-serif;">
                <h6 style="margin: 0 0 8px 0; color: #1f2937;">{{ addslashes($cenaData["title"]) }}</h6>
                <div style="font-size: 12px; color: #9ca3af;">
                    <i class="fas fa-calendar"></i> {{ $cenaData["formatted_datetime"] }}
                    <br>
                    <i class="fas fa-users"></i> {{ $cenaData["guests_current"] }}/{{ $cenaData["guests_max"] }} comensales
                    <br>
                    <i class="fas fa-dollar-sign"></i> {{ $cenaData["formatted_price"] }} por persona
                </div>
            </div>
        `
    });

    showMarker.addListener('click', () => {
        infoWindow.open(showMap, showMarker);
    });
    
    // Abrir info window por defecto después de cargar
    setTimeout(() => {
        infoWindow.open(showMap, showMarker);
    }, 1000);
}

function loadGoogleMapsForShow() {
    if (document.querySelector('script[src*="maps.googleapis.com"]')) {
        return;
    }

    window.initShowMapCallback = function() {
        initShowMap();
    };

    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCuh8GSFyFxvDaiEeWcW7JXs2KIcf89dHY&callback=initShowMapCallback';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}
</script>
@endpush