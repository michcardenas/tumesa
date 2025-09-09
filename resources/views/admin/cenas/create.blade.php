?>

@extends('layouts.admin')

@section('title', 'Crear Nueva Cena')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Crear Nueva Cena</h1>
            <p class="text-muted">Completa la información para crear una nueva experiencia gastronómica</p>
        </div>
        <a href="{{ route('admin.cenas.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('admin.cenas.store') }}" method="POST" enctype="multipart/form-data" id="cenaForm">
        @csrf
        
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
                                    <option value="{{ $chef->id }}" {{ old('user_id') == $chef->id ? 'selected' : '' }}>
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
                                   id="title" name="title" value="{{ old('title') }}" 
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
                                           id="datetime" name="datetime" value="{{ old('datetime') }}" required>
                                    @error('datetime')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="guests_max" class="form-label">Máx. Invitados <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('guests_max') is-invalid @enderror" 
                                           id="guests_max" name="guests_max" value="{{ old('guests_max') }}" 
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
                                               id="price" name="price" value="{{ old('price') }}" 
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
                                      placeholder="Describe el menú que ofrecerás...">{{ old('menu') }}</textarea>
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
                                   id="location" name="location" value="{{ old('location') }}" 
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
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
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
                                      placeholder="Alergias, restricciones dietéticas, etc.">{{ old('special_requirements') }}</textarea>
                            @error('special_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Política de Cancelación -->
                        <div class="mb-3">
                            <label for="cancellation_policy" class="form-label">Política de Cancelación</label>
                            <textarea class="form-control @error('cancellation_policy') is-invalid @enderror" 
                                      id="cancellation_policy" name="cancellation_policy" rows="3" 
                                      placeholder="Describe la política de cancelación...">{{ old('cancellation_policy') }}</textarea>
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
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Borrador</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Activo -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Experiencia Activa
                                </label>
                            </div>
                            <small class="text-muted">Solo las experiencias activas son visibles para los usuarios</small>
                        </div>
                    </div>
                </div>

                <!-- Imágenes -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Imágenes</h5>
                    </div>
                    <div class="card-body">
                        <!-- Imagen de Portada -->
                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Imagen de Portada</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                   id="cover_image" name="cover_image" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG, WEBP. Máx: 2MB</small>
                            @error('cover_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Galería -->
                        <div class="mb-3">
                            <label for="gallery_images" class="form-label">Galería de Imágenes</label>
                            <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                                   id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                            <small class="text-muted">Puedes seleccionar múltiples imágenes</small>
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
                                <i class="fas fa-save me-2"></i>Crear Cena
                            </button>
                            <a href="{{ route('admin.cenas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let map, marker, autocomplete;

function initMap() {
    // Coordenadas por defecto (Buenos Aires)
    const defaultLat = -34.6037;
    const defaultLng = -58.3816;
    
    // Inicializar mapa
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: defaultLat, lng: defaultLng },
        zoom: 13,
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
        position: { lat: defaultLat, lng: defaultLng },
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

    // Si hay coordenadas previas (en caso de error de validación)
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    
    if (oldLat && oldLng) {
        const position = { lat: parseFloat(oldLat), lng: parseFloat(oldLng) };
        map.setCenter(position);
        marker.setPosition(position);
    }
}

function onPlaceChanged() {
    const place = autocomplete.getPlace();
    
    if (!place.geometry) {
        return;
    }

    // Centrar mapa en el lugar seleccionado
    if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
    } else {
        map.setCenter(place.geometry.location);
        map.setZoom(17);
    }

    // Actualizar marcador y coordenadas
    marker.setPosition(place.geometry.location);
    updateCoordinates(place.geometry.location);
}

function onMarkerDragEnd() {
    const position = marker.getPosition();
    updateCoordinates(position);
    
    // Actualizar dirección mediante geocoding
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
    
    // Actualizar dirección
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

// Callback para la API
function initMapCallback() {
    initMap();
}

// Cargar Google Maps API
function loadGoogleMaps() {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCuh8GSFyFxvDaiEeWcW7JXs2KIcf89dHY&libraries=places&callback=initMapCallback';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    loadGoogleMaps();
    
    // Validación del formulario
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
</style>
@endsection