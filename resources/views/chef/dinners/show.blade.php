@extends('layouts.chef')

@section('title', 'Detalles de ' . $cenaData['title'])

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('chef.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('chef.dinners.index') }}">Mis Cenas</a></li>
                            <li class="breadcrumb-item active">{{ $cenaData['title'] }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-2">{{ $cenaData['title'] }}</h1>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge badge-{{ $cenaData['status_color'] }}">
                            {{ $cenaData['status_label'] }}
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $cenaData['formatted_datetime'] }}
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            {{ $cenaData['days_until'] >= 0 ? $cenaData['days_until'] . ' días restantes' : 'Hace ' . abs($cenaData['days_until']) . ' días' }}
                        </span>
                    </div>
                </div>
                
                <div class="btn-group">
                    @if($cenaData['can_edit'])
                        <a href="{{ route('chef.dinners.edit', $cena) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                    @endif
                    
                    @if($cenaData['can_publish'])
                        <form action="{{ route('chef.dinners.publish', $cena) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-eye me-1"></i>Publicar
                            </button>
                        </form>
                    @endif
                    
                    @if($cenaData['can_cancel'])
                        <button type="button" class="btn btn-outline-danger" onclick="confirmCancel()">
                            <i class="fas fa-ban me-1"></i>Cancelar
                        </button>
                    @endif
                    
                    <a href="{{ route('cenas.show', $cena) }}" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Ver Público
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon occupancy">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $cenaData['guests_current'] }}/{{ $cenaData['guests_max'] }}</h3>
                    <p>Comensales</p>
                    <small class="text-muted">{{ $cenaData['occupancy_percentage'] }}% ocupación</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $cenaData['formatted_current_revenue'] }}</h3>
                    <p>Ingresos Actuales</p>
                    <small class="text-muted">de {{ $cenaData['formatted_total_revenue'] }} potencial</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon price">
                    <i class="fas fa-tag"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $cenaData['formatted_price'] }}</h3>
                    <p>Precio por Persona</p>
                    <small class="text-muted">{{ $cenaData['available_spots'] }} espacios libres</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon time">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ abs($cenaData['days_until']) }}</h3>
                    <p>{{ $cenaData['days_until'] >= 0 ? 'Días Restantes' : 'Días Transcurridos' }}</p>
                    <small class="text-muted">{{ $cenaData['formatted_time'] }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Content -->
        <div class="col-lg-8">
            <!-- Menu Section -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-utensils me-2"></i>Menú de la Experiencia</h5>
                </div>
                <div class="card-body">
                    <div class="menu-content">
                        <p class="menu-text">{{ $cenaData['menu'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Location & Map -->
            @if($cenaData['latitude'] && $cenaData['longitude'])
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-map-marker-alt me-2"></i>Ubicación</h5>
                </div>
                <div class="card-body">
                    <div class="location-info mb-3">
                        <p class="mb-1"><strong>Dirección:</strong> {{ $cenaData['location'] }}</p>
                        <p class="text-muted mb-0">
                            <small>Coordenadas: {{ $cenaData['latitude'] }}, {{ $cenaData['longitude'] }}</small>
                        </p>
                    </div>
                    <div class="map-container">
                        <div id="map" class="chef-map"></div>
                    </div>
                </div>
            </div>
            @else
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-map-marker-alt me-2"></i>Ubicación</h5>
                </div>
                <div class="card-body">
                    <p><strong>Dirección:</strong> {{ $cenaData['location'] }}</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tip:</strong> Agrega coordenadas GPS para mostrar un mapa a tus invitados.
                    </div>
                </div>
            </div>
            @endif

            <!-- Images -->
            @if($cenaData['cover_image_url'] || count($cenaData['gallery_image_urls']) > 0)
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-images me-2"></i>Imágenes</h5>
                </div>
                <div class="card-body">
                    @if($cenaData['cover_image_url'])
                        <div class="mb-3">
                            <h6>Imagen Principal</h6>
                            <img src="{{ $cenaData['cover_image_url'] }}" alt="Imagen principal" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    @endif
                    
                    @if(count($cenaData['gallery_image_urls']) > 0)
                        <div>
                            <h6>Galería</h6>
                            <div class="row g-2">
                                @foreach($cenaData['gallery_image_urls'] as $image)
                                    <div class="col-md-3">
                                        <img src="{{ $image }}" alt="Galería" class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Chef Info -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user-circle me-2"></i>Información del Chef</h5>
                </div>
                <div class="card-body text-center">
                    <div class="chef-avatar mb-3">
                        @if($cenaData['chef_avatar'])
                            <img src="{{ $cenaData['chef_avatar'] }}" alt="{{ $cenaData['chef_name'] }}" class="chef-image">
                        @else
                            <div class="chef-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <h6>{{ $cenaData['chef_name'] }}</h6>
                    @if($cenaData['chef_especialidad'])
                        <p class="text-primary mb-1">{{ $cenaData['chef_especialidad'] }}</p>
                    @endif
                    @if($cenaData['chef_experiencia'])
                        <small class="text-muted">{{ $cenaData['chef_experiencia'] }} años de experiencia</small>
                    @endif
                    <hr>
                    <p class="text-muted mb-0">
                        <i class="fas fa-envelope me-1"></i>
                        {{ $cenaData['chef_email'] }}
                    </p>
                </div>
            </div>

            <!-- Event Details -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Detalles del Evento</h5>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Estado:</span>
                        <span class="badge badge-{{ $cenaData['status_color'] }}">{{ $cenaData['status_label'] }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Activo:</span>
                        <span class="badge badge-{{ $cenaData['is_active'] ? 'success' : 'secondary' }}">
                            {{ $cenaData['is_active'] ? 'Sí' : 'No' }}
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Creado:</span>
                        <span class="text-muted">{{ $cenaData['created_ago'] }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Actualizado:</span>
                        <span class="text-muted">{{ $cenaData['updated_ago'] }}</span>
                    </div>
                    @if($cenaData['is_past'])
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Evento finalizado</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('chef.dinners.reservations', $cena) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i>Ver Reservas
                        </a>
                        @if($cenaData['can_edit'])
                            <a href="{{ route('chef.dinners.edit', $cena) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar Evento
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="shareEvent()">
                            <i class="fas fa-share-alt me-1"></i>Compartir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Leaflet CSS and JS -->
@if($cenaData['latitude'] && $cenaData['longitude'])
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif

<style>
:root {
    --primary-color: #2563eb;
    --primary-dark: #1d4ed8;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
}

.breadcrumb {
    background: none;
    padding: 0;
    margin-bottom: 0.5rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: var(--gray-500);
}

.badge-success {
    background-color: var(--success-color);
    color: white;
}

.badge-warning {
    background-color: var(--warning-color);
    color: white;
}

.badge-danger {
    background-color: var(--danger-color);
    color: white;
}

.badge-primary {
    background-color: var(--primary-color);
    color: white;
}

.badge-secondary {
    background-color: var(--gray-500);
    color: white;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.stat-icon.occupancy {
    background-color: var(--primary-color);
}

.stat-icon.revenue {
    background-color: var(--success-color);
}

.stat-icon.price {
    background-color: var(--warning-color);
}

.stat-icon.time {
    background-color: var(--gray-600);
}

.stat-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0 0 0.25rem 0;
    line-height: 1;
}

.stat-content p {
    color: var(--gray-600);
    font-weight: 500;
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
}

.stat-content small {
    font-size: 0.75rem;
}

.content-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--gray-200);
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    background-color: var(--gray-50);
    border-radius: 12px 12px 0 0;
}

.card-header h5 {
    margin: 0;
    font-weight: 600;
    color: var(--gray-800);
}

.card-body {
    padding: 1.5rem;
}

.menu-content {
    padding: 1.5rem;
    background-color: var(--gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.menu-text {
    font-size: 1rem;
    line-height: 1.6;
    color: var(--gray-700);
    margin: 0;
}

.chef-map {
    width: 100%;
    height: 300px;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
}

.chef-avatar {
    display: flex;
    justify-content: center;
}

.chef-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-color);
}

.chef-placeholder {
    width: 80px;
    height: 80px;
    background-color: var(--gray-300);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-500);
    font-size: 2rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: var(--gray-600);
}

.btn-group .btn {
    font-size: 0.875rem;
}

.custom-marker {
    background: var(--primary-color);
    width: 30px;
    height: 30px;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
}

.custom-marker i {
    transform: rotate(45deg);
    font-size: 14px;
}

@media (max-width: 768px) {
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
        margin-top: 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<script>
@if($cenaData['latitude'] && $cenaData['longitude'])
let map, marker;

function initMap() {
    const lat = {{ $cenaData['latitude'] }};
    const lng = {{ $cenaData['longitude'] }};
    
    // Crear el mapa
    map = L.map('map').setView([lat, lng], 15);
    
    // Agregar tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Crear icono personalizado
    const customIcon = L.divIcon({
        html: '<div class="custom-marker"><i class="fas fa-utensils"></i></div>',
        className: 'custom-marker-container',
        iconSize: [30, 30],
        iconAnchor: [15, 30]
    });
    
    // Agregar marcador
    marker = L.marker([lat, lng], { icon: customIcon }).addTo(map)
        .bindPopup(`
            <div style="font-family: Inter, sans-serif; padding: 0.5rem;">
                <h6 style="color: #1e293b; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fas fa-utensils" style="color: #2563eb; margin-right: 0.5rem;"></i>
                    {{ $cenaData['title'] }}
                </h6>
                <p style="color: #64748b; font-size: 0.875rem; margin: 0;">
                    <i class="fas fa-map-marker-alt" style="color: #64748b; margin-right: 0.5rem;"></i>
                    {{ $cenaData['location'] }}
                </p>
            </div>
        `);
        
    // Agregar estilo CSS
    const style = document.createElement('style');
    style.textContent = `
        .custom-marker-container {
            background: none;
            border: none;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    `;
    document.head.appendChild(style);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('map')) {
        initMap();
    }
});
@endif

function confirmCancel() {
    if (confirm('¿Estás seguro de que deseas cancelar esta cena? Esta acción no se puede deshacer.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('chef.dinners.cancel', $cena) }}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function shareEvent() {
    const url = `{{ route('cenas.show', $cena) }}`;
    const title = `{{ $cenaData['title'] }}`;
    const text = `Te invito a esta experiencia culinaria: ${title}`;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: url
        });
    } else {
        // Fallback: copiar al portapapeles
        navigator.clipboard.writeText(url).then(() => {
            alert('Enlace copiado al portapapeles');
        });
    }
}
</script>
@endsection