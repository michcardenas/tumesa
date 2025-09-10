?>

@extends('layouts.app')

@section('title', 'Editar Cena')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="admin-sidebar">
                <ul class="admin-menu">
                    <li class="menu-item">
                        <a href="{{ route('admin.dashboard') }}" class="menu-link">
                            <i class="fas fa-file-alt"></i>
                            Gestión de Páginas
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.usuarios') }}" class="menu-link">
                            <i class="fas fa-users"></i>
                            Usuarios
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.negocio') }}" class="menu-link">
                            <i class="fas fa-utensils"></i>
                            Tu negocio
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.cenas') }}" class="menu-link active">
                            <i class="fas fa-cog"></i>
                            Cenas
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="col-md-9">
            <div class="admin-content">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.cenas') }}">Gestión de Cenas</a></li>
                        <li class="breadcrumb-item active">Editar Cena</li>
                    </ol>
                </nav>

                <div class="py-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-1">Editar Cena</h1>
                            <p class="text-muted">Modifica la información de "{{ $cena->title }}"</p>
                        </div>
                        <a href="{{ route('admin.cenas') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>

                    <form action="{{ route('admin.cenas.update', $cena) }}" method="POST" enctype="multipart/form-data" id="cenaForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Información Principal -->
                            <div class="col-lg-8">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Información Principal</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Chef -->
                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">Chef Anfitrión <span class="text-danger">*</span></label>
                                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                                <option value="">Seleccionar chef...</option>
                                                @foreach($chefs as $chef)
                                                    <option value="{{ $chef->id }}" {{ (old('user_id', $cena->user_id) == $chef->id) ? 'selected' : '' }}>
                                                        {{ $chef->name }} - {{ $chef->email }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Título -->
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Título de la Experiencia <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title', $cena->title) }}" 
                                                   placeholder="Ej: Noche Italiana Gourmet" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Fecha y Hora -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="datetime" class="form-label">Fecha y Hora <span class="text-danger">*</span></label>
                                                    <input type="datetime-local" class="form-control @error('datetime') is-invalid @enderror" 
                                                           id="datetime" name="datetime" value="{{ old('datetime', $cena->datetime->format('Y-m-d\TH:i')) }}" required>
                                                    @error('datetime')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="guests_max" class="form-label">Máx. Invitados <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('guests_max') is-invalid @enderror" 
                                                           id="guests_max" name="guests_max" value="{{ old('guests_max', $cena->guests_max) }}" 
                                                           min="1" max="50" required>
                                                    @error('guests_max')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Precio por Persona <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                               id="price" name="price" value="{{ old('price', $cena->price) }}" 
                                                               min="0" step="0.01" required>
                                                    </div>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Menú -->
                                        <div class="mb-3">
                                            <label for="menu" class="form-label">Descripción del Menú <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('menu') is-invalid @enderror" 
                                                      id="menu" name="menu" rows="4" required 
                                                      placeholder="Describe el menú que ofrecerás...">{{ old('menu', $cena->menu) }}</textarea>
                                            @error('menu')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Ubicación y Mapa -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Ubicación</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Dirección -->
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Dirección <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                                   id="location" name="location" value="{{ old('location', $cena->location) }}" 
                                                   placeholder="Ingresa la dirección completa..." required>
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Mapa -->
                                        <div class="mb-3">
                                            <label class="form-label">Ubicación en el Mapa</label>
                                            <div id="map" style="height: 300px; border-radius: 8px;"></div>
                                        </div>

                                        <!-- Coordenadas (ocultas) -->
                                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $cena->latitude) }}">
                                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $cena->longitude) }}">
                                    </div>
                                </div>

                                <!-- Información Adicional -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Información Adicional</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Requerimientos Especiales -->
                                        <div class="mb-3">
                                            <label for="special_requirements" class="form-label">Requerimientos Especiales</label>
                                            <textarea class="form-control @error('special_requirements') is-invalid @enderror" 
                                                      id="special_requirements" name="special_requirements" rows="3" 
                                                      placeholder="Alergias, restricciones dietéticas, etc.">{{ old('special_requirements', $cena->special_requirements) }}</textarea>
                                            @error('special_requirements')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Política de Cancelación -->
                                        <div class="mb-3">
                                            <label for="cancellation_policy" class="form-label">Política de Cancelación</label>
                                            <textarea class="form-control @error('cancellation_policy') is-invalid @enderror" 
                                                      id="cancellation_policy" name="cancellation_policy" rows="3" 
                                                      placeholder="Describe la política de cancelación...">{{ old('cancellation_policy', $cena->cancellation_policy) }}</textarea>
                                            @error('cancellation_policy')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="col-lg-4">
                                <!-- Estado y Configuración -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Estado y Configuración</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Estado -->
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="draft" {{ old('status', $cena->status) == 'draft' ? 'selected' : '' }}>Borrador</option>
                                                <option value="published" {{ old('status', $cena->status) == 'published' ? 'selected' : '' }}>Publicado</option>
                                                <option value="cancelled" {{ old('status', $cena->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Activo -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                       {{ old('is_active', $cena->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Experiencia Activa
                                                </label>
                                            </div>
                                            <small class="text-muted">Solo las experiencias activas son visibles para los usuarios</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Imágenes Actuales -->
                                @if($cena->cover_image || ($cena->gallery_images && count($cena->gallery_images) > 0))
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Imágenes Actuales</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($cena->cover_image)
                                            <div class="mb-3">
                                                <label class="form-label">Imagen de Portada Actual</label>
                                                <div>
                                                    <img src="{{ $cena->cover_image_url }}" alt="Portada" class="img-thumbnail" style="max-width: 200px;">
                                                </div>
                                            </div>
                                        @endif

                                        @if($cena->gallery_images && count($cena->gallery_images) > 0)
                                            <div class="mb-3">
                                                <label class="form-label">Galería Actual</label>
                                                <div class="row">
                                                    @foreach($cena->gallery_image_urls as $imageUrl)
                                                        <div class="col-6 mb-2">
                                                            <img src="{{ $imageUrl }}" alt="Galería" class="img-thumbnail">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <!-- Nuevas Imágenes -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Cambiar Imágenes</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Imagen de Portada -->
                                        <div class="mb-3">
                                            <label for="cover_image" class="form-label">Nueva Imagen de Portada</label>
                                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                                   id="cover_image" name="cover_image" accept="image/*">
                                            <small class="text-muted">Formatos: JPG, PNG, WEBP. Máx: 2MB. Solo si quieres cambiar la actual.</small>
                                            @error('cover_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Galería -->
                                        <div class="mb-3">
                                            <label for="gallery_images" class="form-label">Nueva Galería de Imágenes</label>
                                            <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                                                   id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                            <small class="text-muted">Solo si quieres reemplazar toda la galería actual.</small>
                                            @error('gallery_images.*')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de Acción -->
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Actualizar Cena
                                            </button>
                                            <a href="{{ route('admin.cenas') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Cancelar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let map, marker, autocomplete;

function initMap() {
    // Coordenadas existentes o por defecto (Buenos Aires)
    const existingLat = {{ $cena->latitude ?? -34.6037 }};
    const existingLng = {{ $cena->longitude ?? -58.3816 }};
    
    // Inicializar mapa
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: existingLat, lng: existingLng },
        zoom: 15,
        styles: [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            }
        ]
    });

    // Marcador
    marker = new google.maps.Marker({
        position: { lat: existingLat, lng: existingLng },
        map: map,
        draggable: true,
        title: 'Ubicación de la cena'
    });

    // Autocomplete para la dirección
    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('location'),
        {
            types: ['address'],
            componentRestrictions: { country: 'ar' }
        }
    );

    // Eventos
    autocomplete.addListener('place_changed', onPlaceChanged);
    marker.addListener('dragend', onMarkerDragEnd);
    map.addListener('click', onMapClick);
}

function onPlaceChanged() {
    const place = autocomplete.getPlace();
    
    if (!place.geometry) {
        return;
    }

    if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
    } else {
        map.setCenter(place.geometry.location);
        map.setZoom(17);
    }

    marker.setPosition(place.geometry.location);
    updateCoordinates(place.geometry.location);
}

function onMarkerDragEnd() {
    const position = marker.getPosition();
    updateCoordinates(position);
    
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: position }, (results, status) => {
        if (status === 'OK' && results[0]) {
            document.getElementById('location').value = results[0].formatted_address;
        }
    });
}

function onMapClick(e) {
    marker.setPosition(e.latLng);
    updateCoordinates(e.latLng);
    
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: e.latLng }, (results, status) => {
        if (status === 'OK' && results[0]) {
            document.getElementById('location').value = results[0].formatted_address;
        }
    });
}

function updateCoordinates(position) {
    document.getElementById('latitude').value = position.lat();
    document.getElementById('longitude').value = position.lng();
}

function initMapCallback() {
    initMap();
}

function loadGoogleMaps() {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCuh8GSFyFxvDaiEeWcW7JXs2KIcf89dHY&libraries=places&callback=initMapCallback';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

document.addEventListener('DOMContentLoaded', function() {
    loadGoogleMaps();
    
    document.getElementById('cenaForm').addEventListener('submit', function(e) {
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        
        if (!lat || !lng) {
            e.preventDefault();
            alert('Por favor, selecciona una ubicación válida en el mapa.');
            return false;
        }
    });
});
</script>

<style>
.card {
    border: none;
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 25px rgba(0,0,0,0.1) !important;
}

.form-label {
    font-weight: 600;
    color: #374151;
}

.text-danger {
    color: #dc3545 !important;
}

#map {
    border: 2px solid #e5e7eb;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.btn {
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.menu-link.active {
    background-color: #e3f2fd;
    color: #1976d2;
    font-weight: 600;
}

.img-thumbnail {
    max-width: 100%;
    height: auto;
}
</style>
@endsection