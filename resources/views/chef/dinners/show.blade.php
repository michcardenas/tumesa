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

<!-- Información del Chef -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Información del Chef Anfitrión</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="chef-avatar-container mb-3">
                            @if($cena->user->avatar_url)
                                <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}" 
                                     class="chef-avatar-large rounded-circle border border-primary" 
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="chef-avatar-large rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 120px; height: 120px;">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                            @endif
                        </div>
                        @if($cena->user->rating > 0)
                            <div class="chef-rating mb-2">
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($cena->user->rating))
                                            <i class="fas fa-star text-warning"></i>
                                        @elseif($i - 0.5 <= $cena->user->rating)
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="text-muted d-block">{{ number_format($cena->user->rating, 1) }} de 5</small>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="text-primary mb-2">{{ $cena->user->name }}</h4>
                                
                                @if($cena->user->especialidad)
                                    <p class="mb-2">
                                        <i class="fas fa-star text-warning me-2"></i>
                                        <strong>Especialidad:</strong> {{ $cena->user->especialidad }}
                                    </p>
                                @endif
                                
                                @if($cena->user->experiencia_anos)
                                    <p class="mb-2">
                                        <i class="fas fa-award text-success me-2"></i>
                                        <strong>Experiencia:</strong> {{ $cena->user->experiencia_anos }} {{ $cena->user->experiencia_anos == 1 ? 'año' : 'años' }}
                                    </p>
                                @endif
                                
                                <p class="mb-2">
                                    <i class="fas fa-envelope text-info me-2"></i>
                                    <strong>Email:</strong> {{ $cena->user->email }}
                                </p>
                                
                                @if($cena->user->telefono)
                                    <p class="mb-2">
                                        <i class="fas fa-phone text-success me-2"></i>
                                        <strong>Teléfono:</strong> {{ $cena->user->telefono }}
                                    </p>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                @if($cena->user->bio)
                                    <div class="chef-bio">
                                        <h6 class="text-primary"><i class="fas fa-quote-left me-2"></i>Acerca de mí</h6>
                                        <p class="text-muted fst-italic">{{ $cena->user->bio }}</p>
                                    </div>
                                @endif
                                
                                @if($cena->user->instagram || $cena->user->facebook || $cena->user->website)
                                    <div class="chef-social mt-3">
                                        <h6 class="text-primary"><i class="fas fa-share-alt me-2"></i>Redes Sociales</h6>
                                        <div class="d-flex gap-2">
                                            @if($cena->user->instagram)
                                                <a href="{{ $cena->user->instagram_url ?? $cena->user->instagram }}" target="_blank" 
                                                   class="btn btn-outline-danger btn-sm">
                                                    <i class="fab fa-instagram"></i> Instagram
                                                </a>
                                            @endif
                                            @if($cena->user->facebook)
                                                <a href="{{ $cena->user->facebook_url ?? $cena->user->facebook }}" target="_blank" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fab fa-facebook"></i> Facebook
                                                </a>
                                            @endif
                                            @if($cena->user->website)
                                                <a href="{{ $cena->user->website }}" target="_blank" 
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-globe"></i> Website
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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
                    {!! nl2br(e($cenaData['menu'])) !!}
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