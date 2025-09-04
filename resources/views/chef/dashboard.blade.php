{{-- resources/views/chef/dashboard.blade.php --}}
@extends('layouts.app_chef')

@section('content')
<div class="chef-container">
    <!-- Header Simple -->
    <div class="chef-header">
        <div class="container-fluid">
            <h1>Panel del Chef</h1>
            <p>Bienvenido, {{ Auth::user()->name }} - Administra tu cocina</p>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="">
        <div class="chef-content">
            <!-- Dashboard Principal -->
            <div id="dashboard-section" class="content-section active">
                <!-- Estadísticas Rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-info">
                                <h4>{{ $stats['cenas_mes'] ?? 0 }}</h4>
                                <p>Cenas Este Mes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h4>{{ $stats['comensales_totales'] ?? 0 }}</h4>
                                <p>Comensales Totales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h4>{{ $stats['cenas_pendientes'] ?? 0 }}</h4>
                                <p>Cenas Pendientes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-danger">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stat-info">
                                <h4>${{ number_format($stats['ingresos_mes'] ?? 0, 0, ',', '.') }}</h4>
                                <p>Ingresos del Mes</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Opcional: Mostrar mensaje cuando no hay datos -->
                @if(($stats['cenas_mes'] ?? 0) === 0 && ($stats['cenas_pendientes'] ?? 0) === 0)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>¡Comienza tu aventura culinaria!</strong> 
                    No tienes cenas programadas aún. Crea tu primera cena usando el botón "Nueva Cena".
                </div>
                @endif

                <!-- Próximas Cenas -->
                <div class="section-header">
                    <h2>Próximas Cenas</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newDinnerModal" style="background-color: #2563eb; border-color: #2563eb;">
                        <i class="fas fa-plus"></i> Nueva Cena
                    </button>
                </div>

                <div class="table-container">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Título</th>
                                <th>Comensales</th>
                                <th>Precio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proximas_cenas as $cena)
                            <tr>
                                <td>{{ $cena['fecha_formatted'] }}</td>
                                <td>{{ $cena['titulo'] }}</td>
                                <td>{{ $cena['comensales_actuales'] }}/{{ $cena['comensales_max'] }}</td>
                                <td>${{ number_format($cena['precio'], 0, ',', '.') }}</td>
                                
                                <td>
                                    <div class="action-buttons">
                                        @php
                                            $ahora = \Carbon\Carbon::now();
                                            $fechaCena = \Carbon\Carbon::parse($cena['fecha']);
                                            $minutosParaCena = $ahora->diffInMinutes($fechaCena, false); // false para valores negativos si ya pasó
                                        @endphp
                                        
                                        {{-- Botón Iniciar Cena - Solo visible 30 minutos antes --}}
                                        @if($minutosParaCena <= 30 && $minutosParaCena >= -120) {{-- Visible desde 30 min antes hasta 2 horas después --}}
                                            <button class="btn btn-sm btn-warning" 
                                                    onclick="iniciarCena({{ $cena['id'] }})"
                                                    title="Marcar asistencia">
                                                <i class="fas fa-play-circle"></i>
                                                @if($minutosParaCena > 0)
                                                    Iniciar ({{ $minutosParaCena }} min)
                                                @else
                                                    En curso
                                                @endif
                                            </button>
                                        @endif
                                        
                                        {{-- Botones normales siempre visibles --}}
                                        <a href="{{ route('chef.dinners.show', $cena['id']) }}" 
                                        class="btn btn-sm btn-outline-primary" 
                                        title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('chef.dinners.edit', $cena['id']) }}" 
                                        class="btn btn-sm btn-outline-success" 
                                        title="Editar cena">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                    <br>
                                    <strong>No tienes cenas próximas</strong>
                                    <br>
                                    <small>Crea tu primera cena usando el botón "Nueva Cena"</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> <!-- Cierre de dashboard-section -->

            <!-- Sección Mis Cenas -->
            <div id="mis-cenas-section" class="content-section">
                <div class="section-header">
                    <h2>Mis Cenas</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newDinnerModal" style="background-color: #2563eb; border-color: #2563eb;">
                        <i class="fas fa-plus"></i> Nueva Cena
                    </button>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Aquí aparecerán todas tus cenas organizadas. Podrás editarlas, ver los comensales registrados y gestionar los pagos.
                </div>
            </div> <!-- Cierre de mis-cenas-section -->

        </div> <!-- Cierre de chef-content -->
    </div> <!-- Cierre de col-md-9 -->
</div> <!-- Cierre de chef-container -->

<!-- Modal para Nueva Cena -->
<div class="modal fade" id="newDinnerModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus"></i> Nueva Cena
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="dinnerForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Título de la Cena</label>
                                <input type="text" class="form-control" name="title" placeholder="Ej: Noche Italiana Tradicional">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha y Hora</label>
                                <input type="datetime-local" class="form-control" name="datetime">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Número de Comensales</label>
                                <input type="number" class="form-control" name="guests" placeholder="8" min="1" max="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Precio por Persona</label>
                                <input type="number" class="form-control" name="price" placeholder="45000">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción del Menú</label>
                        <textarea class="form-control" name="menu" rows="3" placeholder="Describe los platos que incluirás en esta cena..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-images text-primary"></i> Imágenes de la Cena
                        </label>
                        
                        <!-- Imagen de Portada -->
                        <div class="image-upload-section mb-3">
                            <h6 class="image-section-title">
                                <i class="fas fa-star text-warning"></i> Imagen de Portada
                            </h6>
                            <p class="image-section-description">Esta será la imagen principal que verán los comensales</p>
                            
                            <div class="cover-image-upload">
                                <input type="file" 
                                       id="coverImageInput" 
                                       name="cover_image" 
                                       accept="image/*" 
                                       class="d-none">
                                
                                <div id="coverImageDropZone" class="image-drop-zone cover-drop-zone">
                                    <div class="drop-zone-content">
                                        <i class="fas fa-cloud-upload-alt drop-zone-icon"></i>
                                        <h5>Imagen de Portada</h5>
                                        <p>Arrastra tu imagen aquí o <span class="upload-link">haz clic para seleccionar</span></p>
                                        <small class="text-muted">JPG, PNG o WebP • Máximo 5MB</small>
                                    </div>
                                </div>
                                
                                <!-- Preview de imagen de portada -->
                                <div id="coverImagePreview" class="image-preview-container d-none">
                                    <div class="image-preview-wrapper">
                                        <img id="coverImagePreviewImg" src="" alt="Portada" class="cover-preview-img">
                                        <div class="image-overlay">
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger remove-image-btn" 
                                                    onclick="removeCoverImage()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary change-image-btn" 
                                                    onclick="changeCoverImage()">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="image-info">
                                        <small class="text-success">
                                            <i class="fas fa-check-circle"></i> Imagen de portada cargada
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Galería de Imágenes Adicionales -->
                        <div class="image-upload-section">
                            <h6 class="image-section-title">
                                <i class="fas fa-images text-info"></i> Galería de Imágenes (Opcional)
                            </h6>
                            <p class="image-section-description">Agrega hasta 5 imágenes adicionales de tus platos o ambiente</p>
                            
                            <div class="gallery-images-upload">
                                <input type="file" 
                                       id="galleryImagesInput" 
                                       name="gallery_images[]" 
                                       accept="image/*" 
                                       multiple 
                                       class="d-none">
                                
                                <div id="galleryDropZone" class="image-drop-zone gallery-drop-zone">
                                    <div class="drop-zone-content">
                                        <i class="fas fa-photo-video drop-zone-icon"></i>
                                        <h6>Galería de Imágenes</h6>
                                        <p>Arrastra varias imágenes aquí o <span class="upload-link">haz clic para seleccionar</span></p>
                                        <small class="text-muted">Máximo 5 imágenes • JPG, PNG o WebP • 5MB cada una</small>
                                    </div>
                                </div>
                                
                                <!-- Preview de galería -->
                                <div id="galleryPreview" class="gallery-preview-container"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 🌍 SECCIÓN DE UBICACIÓN SIMPLIFICADA -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Ubicación de la Cena
                        </label>
                        
                        <!-- Un solo input con búsqueda inteligente -->
                        <div class="input-group mb-3">
                            <input type="text" 
                                   id="locationInput" 
                                   class="form-control" 
                                   name="location" 
                                   placeholder="🔍 Busca cualquier lugar: restaurantes, hoteles, ciudades, direcciones..."
                                   autocomplete="off">
                            <button type="button" 
                                    id="myLocationBtn" 
                                    class="btn btn-outline-success" 
                                    title="Usar mi ubicación actual">
                                <i class="fas fa-crosshairs"></i>
                            </button>
                        </div>
                        
                        <!-- Mapa interactivo -->
                        <div id="map" style="height: 400px; width: 100%; border-radius: 8px; border: 2px solid #e9ecef;"></div>
                        
                        <!-- Campos ocultos para guardar coordenadas -->
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        
                        <!-- Confirmación visual de la ubicación seleccionada -->
                        <div id="selectedAddress" class="mt-2"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" style="background-color: #2563eb; border-color: #2563eb;" onclick="createDinner()">
                    <i class="fas fa-save"></i> Crear Cena
                </button>
            </div>
        </div>
    </div>
</div>
<style>
/* ==================== CONTENEDOR PRINCIPAL ==================== */
.chef-container {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* ==================== HEADER ==================== */
.chef-header {
    color: white;
    padding: 1rem 0;
    margin-bottom: 0;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
}

.chef-header h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.chef-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

/* ==================== ÁREA DE CONTENIDO ==================== */
.chef-content {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    min-height: 90%;
}

.content-section {
    display: none;
}

.content-section.active {
    display: block;
}

/* ==================== HEADER DE SECCIONES ==================== */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #dee2e6;
}

.section-header h2 {
    margin: 0;
    color: #495057;
    font-size: 1.5rem;
}

/* ==================== TARJETAS DE ESTADÍSTICAS ==================== */
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-right: 1rem;
}

.stat-icon.bg-primary {
    background-color: #2563eb !important;
}

.stat-icon.bg-success {
    background-color: #059669 !important;
}

.stat-icon.bg-warning {
    background-color: #d97706 !important;
}

.stat-icon.bg-danger {
    background-color: #1e293b !important;
}

.stat-info h4 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: bold;
    color: #495057;
}

.stat-info p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

/* ==================== TABLAS ==================== */
.table-container {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

/* ==================== BOTONES DE ACCIÓN ==================== */
.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    padding: 0.25rem 0.5rem;
}

/* ==================== MODAL ==================== */
.modal-header {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-bottom: 2px solid #dee2e6;
}

.modal-title {
    color: #2563eb;
    font-weight: 600;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 2px solid #e9ecef;
    background-color: #f8fafc;
}

.modal-xl {
    max-width: 1200px;
}

/* ==================== FORMULARIOS ==================== */
.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-label i {
    color: #2563eb;
    margin-right: 0.25rem;
}

/* ==================== CARGA DE IMÁGENES ==================== */
.image-upload-section {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 2px solid #e2e8f0;
    margin-bottom: 1rem;
}

.image-section-title {
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.image-section-description {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.image-drop-zone {
    border: 3px dashed #cbd5e1;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.image-drop-zone:hover {
    border-color: #2563eb;
    background: #f1f5f9;
}

.drop-zone-content {
    pointer-events: none;
}

.drop-zone-icon {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 1rem;
}

.upload-link {
    color: #2563eb;
    font-weight: 600;
    text-decoration: underline;
}

     .image-preview-container {
            position: relative;
            margin-top: 1rem;
        }
        
        .image-preview-wrapper {
            position: relative;
            display: inline-block;
            max-width: 100%;
        }
        
        .cover-preview-img {
            width: 100%;
            max-width: 400px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .image-overlay {
            position: absolute;
            top: 8px;
            right: 8px;
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .image-preview-wrapper:hover .image-overlay {
            opacity: 1;
        }
        
        .remove-image-btn, .change-image-btn {
            padding: 0.5rem;
            border-radius: 50%;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .image-info {
            margin-top: 0.5rem;
            text-align: center;
        }

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-preview-wrapper:hover .image-overlay {
    opacity: 1;
}
img, svg {
    max-width: 11rem;
    height: auto;
}
.gallery-preview-container {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
}

/* ==================== MAPA ==================== */
#map {
    height: 400px;
    width: 100%;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: border-color 0.3s ease;
}

#map:hover {
    border-color: #2563eb;
}

#selectedAddress {
    font-size: 0.875rem;
    color: #059669 !important;
    font-weight: 500;
    margin-top: 8px;
    padding: 8px;
    background-color: #f0fdf4;
    border-radius: 4px;
    border-left: 3px solid #059669;
}

/* ==================== ALERTAS ==================== */
.alert {
    border: none;
    border-radius: 6px;
}

.alert-info {
    background-color: #e0f2fe;
    border-left: 4px solid #2563eb;
    color: #075985;
}

/* ==================== BOTONES PERSONALIZADOS ==================== */
.btn-primary {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
}

.btn-primary:hover {
    background-color: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
}

.btn-secondary {
    background-color: #64748b !important;
    border-color: #64748b !important;
}

.btn-secondary:hover {
    background-color: #475569 !important;
    border-color: #475569 !important;
}

.btn-outline-primary {
    color: #2563eb;
    border-color: #2563eb;
}

.btn-outline-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
    color: white;
}

.btn-outline-success {
    color: #059669;
    border-color: #059669;
}

.btn-outline-success:hover {
    background-color: #059669;
    border-color: #059669;
    color: white;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .chef-content {
        margin-top: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    #map {
        height: 300px !important;
    }
    
    .modal-xl {
        max-width: 95%;
        margin: 1rem auto;
    }
    
    .image-drop-zone {
        padding: 1.5rem 1rem;
    }
    
    .gallery-preview-container {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}
</style>

<script>
// Variables globales para Google Maps
let map;
let marker;
let infoWindow;
let geocoder;
let autocompleteService;
let placesService;

document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar secciones (MANTENER SIN CAMBIOS)
    window.showSection = function(sectionName) {
        // Ocultar todas las secciones
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Remover active de todos los links
        document.querySelectorAll('.menu-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Mostrar la sección seleccionada
        const targetSection = document.getElementById(sectionName + '-section');
        if (targetSection) {
            targetSection.classList.add('active');
        } else {
            // Si no existe la sección, mostrar dashboard
            document.getElementById('dashboard-section').classList.add('active');
        }
        
        // Activar el link correspondiente
        event.target.closest('.menu-link').classList.add('active');
    };
    
    // Manejar clicks en sidebar (MANTENER SIN CAMBIOS)
    document.querySelectorAll('.menu-link:not([data-bs-toggle])').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.classList.contains('disabled') && !this.hasAttribute('data-bs-toggle')) {
                e.preventDefault();
                
                // Remover active de todos
                document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active'));
                
                // Agregar active al clickeado
                this.classList.add('active');
            }
        });
    });
    
    // Form submission para perfil (MANTENER SIN CAMBIOS)
    document.querySelector('.profile-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('¡Perfil actualizado exitosamente! (Esta es una demo)');
    });

    // 🗺️ INICIALIZAR GOOGLE MAPS CUANDO SE ABRE EL MODAL
    const newDinnerModal = document.getElementById('newDinnerModal');
    if (newDinnerModal) {
        newDinnerModal.addEventListener('shown.bs.modal', function () {
            // Cargar Google Maps si no está cargado
            if (typeof google === 'undefined' || !google.maps) {
                loadGoogleMaps();
            } else {
                initMap();
            }
        });
    }

    // Botón de mi ubicación
    document.getElementById('myLocationBtn')?.addEventListener('click', getUserLocation);
    
    // Limpiar al cerrar modal
    if (newDinnerModal) {
        newDinnerModal.addEventListener('hidden.bs.modal', function() {
            // Limpiar formulario
            const form = document.getElementById('dinnerForm');
            if (form) {
                form.reset();
            }
            
            // Limpiar indicadores de ubicación
            document.getElementById('selectedAddress').innerHTML = '';
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
            
            // Resetear mapa si existe
            if (map && marker) {
                const defaultLocation = { lat: 4.711, lng: -74.0721 };
                marker.setPosition(defaultLocation);
                map.setCenter(defaultLocation);
                map.setZoom(13);
                
                if (infoWindow) {
                    infoWindow.close();
                }
            }
            
            console.log('🧹 Modal limpiado correctamente');
        });
    }
});

// 🌍 CARGAR GOOGLE MAPS API (SOLO MAPS, SIN PLACES)
function loadGoogleMaps() {
    // Verificar si ya está cargado
    if (typeof google !== 'undefined' && google.maps) {
        initMap();
        return;
    }

    // Verificar si ya existe el script
    if (document.querySelector('script[src*="maps.googleapis.com"]')) {
        return;
    }

    // Crear función global para el callback
    window.initMapCallback = function() {
        if (typeof google !== 'undefined' && google.maps) {
            initMap();
        } else {
            setTimeout(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    initMap();
                } else {
                    console.error('Google Maps not loaded properly');
                    document.getElementById('map').innerHTML = 
                        '<div class="alert alert-warning">Error cargando Google Maps. Reintenta abriendo el modal nuevamente.</div>';
                }
            }, 1000);
        }
    };

    // 🔑 CARGAR SOLO MAPS API (SIN PLACES) - Esto evita el error
    const script = document.createElement('script');
script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCuh8GSFyFxvDaiEeWcW7JXs2KIcf89dHY&libraries=places&loading=async&callback=initMapCallback';
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        console.error('Error loading Google Maps script');
        document.getElementById('map').innerHTML = 
            '<div class="alert alert-danger">Error cargando Google Maps. Verifica tu conexión a internet.</div>';
    };
    document.head.appendChild(script);
}

// 🗺️ INICIALIZAR MAPA SIMPLE
function initMap() {
    try {
        // Verificar que Google Maps esté cargado
        if (typeof google === 'undefined' || !google.maps) {
            throw new Error('Google Maps no está cargado');
        }

        // Ubicación por defecto (Buenos Aires, Argentina)
        const defaultLocation = { lat: -34.6037, lng: -58.3816 };
        
        // Crear mapa
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 13,
            center: defaultLocation,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });

        // Inicializar servicios
        infoWindow = new google.maps.InfoWindow();
        geocoder = new google.maps.Geocoder();
        autocompleteService = new google.maps.places.AutocompleteService();
        placesService = new google.maps.places.PlacesService(map);

        // Crear marcador
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            title: 'Ubicación de la cena',
            draggable: true,
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#2563eb" stroke="#ffffff" stroke-width="1"/>
                        <circle cx="12" cy="9" r="2" fill="#ffffff"/>
                    </svg>
                `),
                scaledSize: new google.maps.Size(40, 40),
                anchor: new google.maps.Point(20, 40)
            }
        });

        // Event listeners del mapa
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
            updateLocationDisplay(event.latLng);
        });

        // Event listener del marcador
        marker.addListener('dragend', function(event) {
            updateLocationDisplay(event.latLng);
        });

        // 🔍 CONFIGURAR BUSCADOR SIMPLE
        setupSimpleSearch();

        // Inicializar display
        updateLocationDisplay(defaultLocation, 'Buenos Aires, Argentina (ubicación predeterminada)');

        console.log('🌍 Google Maps con buscador simple inicializado exitosamente');
        
    } catch (error) {
        console.error('❌ Error inicializando Google Maps:', error);
        document.getElementById('map').innerHTML = 
            `<div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> 
                Error cargando el mapa: ${error.message}
                <br><small>Verifica tu conexión a internet e intenta de nuevo.</small>
            </div>`;
    }
}

// 🔍 CONFIGURAR BUSCADOR SIMPLE CON GEOCODING
// 🔍 REEMPLAZA TODA TU FUNCIÓN setupSimpleSearch() POR ESTA:

function setupSimpleSearch() {
    const input = document.getElementById('locationInput');
    const inputGroup = input.parentElement;
    
    // Crear botón de búsqueda si no existe
    if (!document.getElementById('searchBtn')) {
        const searchBtn = document.createElement('button');
        searchBtn.type = 'button';
        searchBtn.id = 'searchBtn';
        searchBtn.className = 'btn btn-outline-primary';
        searchBtn.innerHTML = '<i class="fas fa-search"></i>';
        searchBtn.title = 'Buscar lugar';
        inputGroup.insertBefore(searchBtn, document.getElementById('myLocationBtn'));
    }
    
    // Event listeners
    document.getElementById('searchBtn').addEventListener('click', searchLocation);
    
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchLocation();
        }
    });
    
    // 🌟 NUEVO: Autocompletado mientras escribes
    let searchTimeout;
    input.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                getPlaceSuggestions(query);
            }, 300);
        } else {
            hideSuggestions();
        }
    });
    
    // Ocultar sugerencias al hacer clic fuera
    input.addEventListener('blur', function() {
        setTimeout(hideSuggestions, 200);
    });
    
    console.log('🔍 Buscador avanzado configurado');
}
function getPlaceSuggestions(query) {
    if (!autocompleteService) return;
    
    console.log('🔍 Buscando sugerencias para:', query);
    
    const request = {
        input: query,
        types: ['establishment', 'geocode'],
        componentRestrictions: { country: ['co', 'us', 'fr', 'es', 'mx', 'ar'] }
    };
    
    autocompleteService.getPlacePredictions(request, function(predictions, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
            console.log('✅ Sugerencias encontradas:', predictions.length);
            showSuggestions(predictions);
        } else {
            hideSuggestions();
        }
    });
}

// 📋 MOSTRAR SUGERENCIAS
function showSuggestions(predictions) {
    hideSuggestions(); // Limpiar anteriores
    
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.id = 'suggestions';
    suggestionsContainer.className = 'suggestions-dropdown';
    
    // Header
    const header = document.createElement('div');
    header.className = 'suggestions-header';
    header.innerHTML = `<i class="fas fa-search"></i> Lugares encontrados`;
    suggestionsContainer.appendChild(header);
    
    // Agregar cada sugerencia
    predictions.slice(0, 6).forEach(prediction => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        
        // Determinar ícono según el tipo
        const icon = getPlaceTypeIcon(prediction.types);
        
        item.innerHTML = `
            <i class="${icon}"></i>
            <div class="suggestion-content">
                <div class="suggestion-main">${prediction.structured_formatting.main_text}</div>
                <div class="suggestion-secondary">${prediction.structured_formatting.secondary_text || ''}</div>
            </div>
        `;
        
        item.addEventListener('click', function() {
            selectPlaceFromSuggestion(prediction);
        });
        
        suggestionsContainer.appendChild(item);
    });
    
    // Posicionar y mostrar
    const searchInput = document.getElementById('locationInput');
    searchInput.parentElement.appendChild(suggestionsContainer);
}

// 🏷️ OBTENER ÍCONO SEGÚN TIPO DE LUGAR
function getPlaceTypeIcon(types) {
    if (types.includes('restaurant')) return 'fas fa-utensils';
    if (types.includes('lodging')) return 'fas fa-bed';
    if (types.includes('tourist_attraction')) return 'fas fa-camera';
    if (types.includes('shopping_mall')) return 'fas fa-shopping-bag';
    if (types.includes('park')) return 'fas fa-tree';
    if (types.includes('locality')) return 'fas fa-city';
    if (types.includes('country')) return 'fas fa-flag';
    return 'fas fa-map-marker-alt';
}

// 📍 SELECCIONAR LUGAR DESDE SUGERENCIA
function selectPlaceFromSuggestion(prediction) {
    console.log('📍 Lugar seleccionado:', prediction.description);
    
    hideSuggestions();
    
    // Actualizar input
    document.getElementById('locationInput').value = prediction.description;
    
    // Obtener detalles del lugar
    const request = {
        placeId: prediction.place_id,
        fields: ['name', 'geometry', 'formatted_address', 'rating', 'types']
    };
    
    placesService.getDetails(request, function(place, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
            console.log('✅ Detalles del lugar obtenidos:', place);
            const location = place.geometry.location;
            
            // Actualizar mapa
            placeMarker(location);
            map.setCenter(location);
            map.setZoom(16);
            
            // Actualizar información con detalles ricos
            updateLocationDisplayAdvanced(location, place);
        } else {
            // Fallback con geocoding
            searchLocation();
        }
    });
}

// 📋 ACTUALIZAR DISPLAY CON INFORMACIÓN AVANZADA
function updateLocationDisplayAdvanced(location, place) {
    let lat, lng;
    
    // Obtener coordenadas
    if (typeof location.lat === 'function') {
        lat = location.lat();
        lng = location.lng();
    } else {
        lat = location.lat;
        lng = location.lng;
    }
    
    // Actualizar coordenadas ocultas
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    
    // Mostrar confirmación con información rica
    const addressElement = document.getElementById('selectedAddress');
    const name = place.name || place.formatted_address;
    const rating = place.rating ? ` ⭐ ${place.rating}` : '';
    
    addressElement.innerHTML = `
        <i class="fas fa-check-circle text-success"></i>
        <strong>${name}${rating}</strong>
        <br>
        <small class="text-muted">
            📍 ${place.formatted_address}<br>
            <i class="fas fa-crosshairs"></i> 
            ${lat.toFixed(6)}, ${lng.toFixed(6)}
        </small>
    `;
    
    // Mostrar info window mejorado
    if (infoWindow && marker) {
        infoWindow.setContent(`
            <div style="padding: 15px; min-width: 250px; max-width: 350px; font-family: 'Segoe UI', sans-serif;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                    <i class="${getPlaceTypeIcon(place.types)}" style="color: #2563eb; margin-right: 8px; font-size: 18px;"></i>
                    <strong style="color: #1f2937; font-size: 16px;">${name}</strong>
                </div>
                ${rating ? `
                <div style="margin-bottom: 8px;">
                    <span style="color: #f59e0b;">⭐</span>
                    <span style="color: #374151; font-weight: 500;">${place.rating}/5</span>
                </div>
                ` : ''}
                <div style="color: #6b7280; font-size: 12px; line-height: 1.4; margin-bottom: 10px;">
                    📍 ${place.formatted_address}
                </div>
                <div style="padding: 8px; background: #f0fdf4; border-radius: 6px; border-left: 3px solid #059669;">
                    <small style="color: #059669; font-weight: 500;">
                        <i class="fas fa-check"></i> Ubicación confirmada para la cena
                    </small>
                </div>
            </div>
        `);
        
        infoWindow.open(map, marker);
    }
}

// 🙈 OCULTAR SUGERENCIAS
function hideSuggestions() {
    const suggestions = document.getElementById('suggestions');
    if (suggestions) {
        suggestions.remove();
    }
}
// 🔍 BUSCAR UBICACIÓN CON GEOCODING
function searchLocation() {
    const query = document.getElementById('locationInput').value.trim();
    
    if (!query) {
        alert('🔍 Escribe el nombre de un lugar\n\nEjemplos:\n• New York\n• París, Francia\n• Tokyo, Japan\n• Plaza Bolívar, Bogotá');
        return;
    }
    
    if (!geocoder) {
        alert('Servicio de búsqueda no disponible');
        return;
    }
    
    // Mostrar loading
    const btn = document.getElementById('searchBtn');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    
    console.log('🔍 Buscando con Geocoder:', query);
    
    geocoder.geocode({
        address: query
    }, function(results, status) {
        // Restaurar botón
        btn.innerHTML = originalContent;
        btn.disabled = false;
        
        if (status === 'OK' && results && results.length > 0) {
            console.log('✅ Lugar encontrado:', results[0]);
            
            const result = results[0];
            const location = result.geometry.location;
            const address = result.formatted_address;
            
            // Actualizar mapa
            placeMarker(location);
            map.setCenter(location);
            map.setZoom(15);
            
            // Actualizar campos
            document.getElementById('locationInput').value = address;
            updateLocationDisplay(location, address);
            
        } else {
            console.warn('❌ No se encontró el lugar:', status);
            
            let message = `No se encontró "${query}"\n\n`;
            if (status === 'REQUEST_DENIED') {
                message += 'Problema con la API key. Verifica la configuración.';
            } else if (status === 'ZERO_RESULTS') {
                message += 'Intenta con:\n• Un nombre más específico\n• Una ciudad conocida\n• Agregar el país: "París, Francia"';
            } else {
                message += 'Error en la búsqueda. Intenta de nuevo.';
            }
            
            alert(message);
        }
    });
}

// 📍 COLOCAR MARCADOR EN EL MAPA
function placeMarker(location) {
    if (marker) {
        marker.setPosition(location);
    }
    map.setCenter(location);
}

// 📋 ACTUALIZAR DISPLAY DE UBICACIÓN
function updateLocationDisplay(location, placeName = null) {
    let lat, lng;
    
    // Obtener coordenadas
    if (typeof location.lat === 'function') {
        lat = location.lat();
        lng = location.lng();
    } else if (location.lat && location.lng) {
        lat = location.lat;
        lng = location.lng;
    } else {
        console.error('Formato de ubicación no reconocido:', location);
        return;
    }
    
    // Actualizar coordenadas ocultas
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    
    // Usar el nombre del lugar si se proporciona, sino el del input
    const displayName = placeName || document.getElementById('locationInput').value.trim();
    
    // Actualizar input si se proporciona un nombre mejor
    if (placeName && placeName !== document.getElementById('locationInput').value) {
        document.getElementById('locationInput').value = placeName;
    }
    
    // Mostrar confirmación
    const addressElement = document.getElementById('selectedAddress');
    if (displayName) {
        addressElement.innerHTML = `
            <i class="fas fa-check-circle text-success"></i>
            <strong>${displayName}</strong>
            <br>
            <small class="text-muted">
                <i class="fas fa-crosshairs"></i> 
                ${lat.toFixed(6)}, ${lng.toFixed(6)}
            </small>
        `;
    } else {
        addressElement.innerHTML = `
            <i class="fas fa-map-marker-alt text-primary"></i>
            <strong>Ubicación seleccionada</strong>
            <br>
            <small class="text-muted">
                <i class="fas fa-crosshairs"></i> 
                ${lat.toFixed(6)}, ${lng.toFixed(6)}
            </small>
        `;
    }
    
    // Mostrar info en el marcador
    if (infoWindow && marker) {
        const finalDisplayName = displayName || 'Ubicación seleccionada';
        
        infoWindow.setContent(`
            <div style="padding: 15px; min-width: 250px; max-width: 350px; font-family: 'Segoe UI', sans-serif;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: #2563eb; margin-right: 8px; font-size: 18px;"></i>
                    <strong style="color: #1f2937; font-size: 16px;">${finalDisplayName}</strong>
                </div>
                <div style="color: #6b7280; font-size: 12px; line-height: 1.4;">
                    <i class="fas fa-crosshairs" style="margin-right: 4px;"></i>
                    Coordenadas: ${lat.toFixed(6)}, ${lng.toFixed(6)}
                </div>
                <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
                    <small style="color: #059669; font-weight: 500;">
                        <i class="fas fa-check"></i> Ubicación confirmada para la cena
                    </small>
                </div>
            </div>
        `);
        
        infoWindow.open(map, marker);
    }
}

// 📱 OBTENER UBICACIÓN DEL USUARIO
function getUserLocation() {
    if (navigator.geolocation) {
        const btn = document.getElementById('myLocationBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                placeMarker(userLocation);
                map.setCenter(userLocation);
                map.setZoom(16);
                
                // Actualizar display con ubicación actual
                updateLocationDisplay(userLocation);
                
                // Si no hay nombre escrito, sugerir uno
                const currentName = document.getElementById('locationInput').value.trim();
                if (!currentName) {
                    document.getElementById('locationInput').value = 'Mi ubicación actual';
                    updateLocationDisplay(userLocation, 'Mi ubicación actual');
                }
                
                btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
                btn.disabled = false;
            },
            function(error) {
                console.error('Error obteniendo ubicación:', error);
                alert('No se pudo obtener tu ubicación. Verifica los permisos del navegador.');
                btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
                btn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        alert('Tu navegador no soporta geolocalización.');
    }
}

// 💾 CREAR CENA (MANTENER SIN CAMBIOS)
function createDinner() {
    const form = document.getElementById('dinnerForm');
    const formData = new FormData(form);
    
    // Validaciones básicas
    if (!formData.get('title')) {
        alert('Por favor ingresa el título de la cena');
        return;
    }
    
    if (!formData.get('datetime')) {
        alert('Por favor selecciona la fecha y hora');
        return;
    }
    
    if (!formData.get('location')) {
        alert('Por favor selecciona una ubicación en el mapa');
        return;
    }

    // Agregar token CSRF
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    // Agregar imágenes si existen
    if (coverImage) {
        formData.append('cover_image', coverImage);
    }
    
    // Agregar imágenes de galería
    galleryImages.forEach((img, index) => {
        formData.append(`gallery_images[${index}]`, img.file);
    });

    // Mostrar indicador de carga
    const submitBtn = document.querySelector('#newDinnerModal .btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
    submitBtn.disabled = true;

    // Enviar datos al servidor
    fetch('/chef/dinners', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Éxito
            alert('¡Cena creada exitosamente!');
            
            // Cerrar modal y limpiar
            const modal = bootstrap.Modal.getInstance(document.getElementById('newDinnerModal'));
            modal.hide();
            form.reset();
            document.getElementById('selectedAddress').innerHTML = '';
            
            // Limpiar imágenes
            resetImageUploads();
            resetMapLocation();
            
            // Recargar página para mostrar la nueva cena
            window.location.reload();
            
        } else {
            // Error de validación
            alert('Error: ' + (data.message || 'No se pudo crear la cena'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Hacer las funciones globales
window.initMap = initMap;
window.initMapCallback = window.initMapCallback;


let coverImage = null;
let galleryImages = [];
const maxGalleryImages = 5;
const maxFileSize = 5 * 1024 * 1024; // 5MB

document.addEventListener('DOMContentLoaded', function() {
    setupImageUploads();
});

function setupImageUploads() {
    // Setup cover image
    setupCoverImageUpload();
    
    // Setup gallery images
    setupGalleryImageUpload();
}

// 🖼️ IMAGEN DE PORTADA
function setupCoverImageUpload() {
    const dropZone = document.getElementById('coverImageDropZone');
    const input = document.getElementById('coverImageInput');
    
    // Click para seleccionar
    dropZone.addEventListener('click', () => input.click());
    
    // Drag & Drop
    dropZone.addEventListener('dragover', handleDragOver);
    dropZone.addEventListener('dragleave', handleDragLeave);
    dropZone.addEventListener('drop', (e) => handleCoverImageDrop(e));
    
    // Input change
    input.addEventListener('change', (e) => handleCoverImageSelect(e.target.files));
}

function handleCoverImageDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const dropZone = e.target.closest('.image-drop-zone');
    dropZone.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    handleCoverImageSelect(files);
}

function handleCoverImageSelect(files) {
    if (files && files[0]) {
        const file = files[0];
        
        if (validateImageFile(file)) {
            setCoverImage(file);
        }
    }
}

function setCoverImage(file) {
    coverImage = file;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('coverImagePreview');
        const img = document.getElementById('coverImagePreviewImg');
        const dropZone = document.getElementById('coverImageDropZone');
        
        img.src = e.target.result;
        dropZone.classList.add('d-none');
        preview.classList.remove('d-none');
        preview.classList.add('upload-success');
        
        console.log('✅ Imagen de portada cargada:', file.name);
    };
    reader.readAsDataURL(file);
}

function removeCoverImage() {
    coverImage = null;
    
    const preview = document.getElementById('coverImagePreview');
    const dropZone = document.getElementById('coverImageDropZone');
    const input = document.getElementById('coverImageInput');
    
    preview.classList.add('d-none');
    dropZone.classList.remove('d-none');
    input.value = '';
    
    console.log('🗑️ Imagen de portada eliminada');
}

function changeCoverImage() {
    document.getElementById('coverImageInput').click();
}

// 🖼️ GALERÍA DE IMÁGENES
function setupGalleryImageUpload() {
    const dropZone = document.getElementById('galleryDropZone');
    const input = document.getElementById('galleryImagesInput');
    
    // Click para seleccionar
    dropZone.addEventListener('click', () => input.click());
    
    // Drag & Drop
    dropZone.addEventListener('dragover', handleDragOver);
    dropZone.addEventListener('dragleave', handleDragLeave);
    dropZone.addEventListener('drop', (e) => handleGalleryImagesDrop(e));
    
    // Input change
    input.addEventListener('change', (e) => handleGalleryImagesSelect(e.target.files));
}

function handleGalleryImagesDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const dropZone = e.target.closest('.image-drop-zone');
    dropZone.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    handleGalleryImagesSelect(files);
}

function handleGalleryImagesSelect(files) {
    if (files && files.length > 0) {
        for (let file of files) {
            if (galleryImages.length >= maxGalleryImages) {
                showError(`Máximo ${maxGalleryImages} imágenes en la galería`);
                break;
            }
            
            if (validateImageFile(file)) {
                addGalleryImage(file);
            }
        }
        updateGalleryDisplay();
    }
}

function addGalleryImage(file) {
    const id = Date.now() + Math.random();
    galleryImages.push({ id, file });
    
    console.log('✅ Imagen agregada a galería:', file.name);
}

function removeGalleryImage(id) {
    galleryImages = galleryImages.filter(img => img.id !== id);
    updateGalleryDisplay();
    
    console.log('🗑️ Imagen eliminada de galería');
}

function updateGalleryDisplay() {
    const container = document.getElementById('galleryPreview');
    
    if (galleryImages.length === 0) {
        container.innerHTML = '';
        return;
    }
    
    container.innerHTML = galleryImages.map(img => `
        <div class="gallery-item upload-success">
            <img src="${URL.createObjectURL(img.file)}" alt="Galería">
            <div class="image-overlay">
                <button type="button" 
                        class="btn btn-sm btn-danger remove-image-btn" 
                        onclick="removeGalleryImage(${img.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
    
    // Mostrar contador
    const dropZone = document.getElementById('galleryDropZone');
    const existing = dropZone.querySelector('.image-counter');
    if (existing) existing.remove();
    
    if (galleryImages.length > 0) {
        const counter = document.createElement('div');
        counter.className = 'image-counter';
        counter.innerHTML = `${galleryImages.length}/${maxGalleryImages} imágenes`;
        dropZone.appendChild(counter);
    }
}

// 🛡️ FUNCIONES AUXILIARES
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    e.target.closest('.image-drop-zone').classList.add('dragover');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    e.target.closest('.image-drop-zone').classList.remove('dragover');
}

function validateImageFile(file) {
    // Validar tipo
    if (!file.type.startsWith('image/')) {
        showError('Solo se permiten archivos de imagen');
        return false;
    }
    
    // Validar tamaño
    if (file.size > maxFileSize) {
        showError('El archivo es muy grande. Máximo 5MB');
        return false;
    }
    
    return true;
}

function showError(message) {
    // Crear mensaje de error temporal
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
    
    // Agregar al final de la sección de imágenes
    const section = document.querySelector('.image-upload-section');
    section.appendChild(errorDiv);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        errorDiv.remove();
    }, 3000);
    
    console.warn('❌ Error de imagen:', message);
}

// 📤 FUNCIÓN PARA OBTENER DATOS DE IMÁGENES (para el formulario)
function getImageData() {
    return {
        coverImage: coverImage,
        galleryImages: galleryImages.map(img => img.file),
        totalImages: (coverImage ? 1 : 0) + galleryImages.length
    };
}

// Hacer función global para uso en createDinner()
window.getImageData = getImageData;
</script>
@endsection