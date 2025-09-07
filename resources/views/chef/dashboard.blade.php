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
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
       <tbody>
    @forelse($proximas_cenas as $cena)
    @php
        $ahora = \Carbon\Carbon::now();
        $fechaCena = $cena['datetime'];
        $minutosParaCena = $ahora->diffInMinutes($fechaCena, false);
        $esPasada = $fechaCena < $ahora;
        $horasDesde = abs($ahora->diffInHours($fechaCena));
        $estadoCena = $cena['estado']; // Obtener el estado real de la BD
    @endphp
    
    <tr class="{{ $esPasada ? 'table-secondary' : '' }}">
        <td>
            {{ $cena['fecha_formatted'] }}
            @if($estadoCena === 'in_progress')
                <br><span class="badge bg-success">
                    <i class="fas fa-circle" style="animation: pulse 2s infinite;"></i> EN CURSO
                </span>
            @elseif($estadoCena === 'completed')
                <br><span class="badge bg-secondary">Finalizada</span>
            @elseif($estadoCena === 'cancelled')
                <br><span class="badge bg-danger">Cancelada</span>
            @elseif($minutosParaCena <= 30 && $minutosParaCena > 0)
                <br><span class="badge bg-warning">En {{ round($minutosParaCena) }} min</span>
            @endif
        </td>
        <td>{{ $cena['titulo'] }}</td>
        <td>{{ $cena['comensales_actuales'] }}/{{ $cena['comensales_max'] }}</td>
        <td>${{ number_format($cena['precio'], 0, ',', '.') }}</td>
        <td>
            @if($estadoCena === 'in_progress')
                <span class="badge bg-success">
                    <i class="fas fa-play-circle"></i> En curso
                </span>
            @elseif($estadoCena === 'completed')
                <span class="badge bg-secondary">Completada</span>
            @elseif($estadoCena === 'cancelled')
                <span class="badge bg-danger">Cancelada</span>
            @elseif($minutosParaCena > 0 && $minutosParaCena <= 30)
                <span class="badge bg-warning">Por iniciar</span>
            @elseif($esPasada)
                <span class="badge bg-secondary">Finalizada</span>
            @else
                <span class="badge bg-primary">Próxima</span>
            @endif
        </td>
       <td>
    <div class="action-buttons d-flex gap-1">
        {{-- Botón Asistencia - Solo si está en curso o por iniciar (y NO finalizada ni cancelada) --}}
        @if(($estadoCena === 'in_progress' || ($minutosParaCena <= 30 && $minutosParaCena >= -120)) 
            && $estadoCena !== 'completed' 
            && $estadoCena !== 'cancelled')
            <a href="{{ route('chef.dinners.asistencia', $cena['id']) }}" 
               class="btn btn-sm {{ $estadoCena === 'in_progress' ? 'btn-success' : 'btn-warning' }}" 
               title="Marcar asistencia de comensales">
                <i class="fas fa-check-circle"></i>
                <span class="d-none d-md-inline">
                    {{ $estadoCena === 'in_progress' ? 'Asistencia' : 'Iniciar' }}
                </span>
            </a>
        @endif

        {{-- Botón Ver - Siempre visible --}}
        <a href="{{ route('chef.dinners.show', $cena['id']) }}" 
           class="btn btn-sm btn-outline-primary" 
           title="Ver detalles">
            <i class="fas fa-eye"></i>
        </a>
        
        {{-- Botón Editar - Solo para cenas no iniciadas --}}
        @if($estadoCena !== 'in_progress' && $estadoCena !== 'completed' && !$esPasada)
            <a href="{{ route('chef.dinners.edit', $cena['id']) }}" 
               class="btn btn-sm btn-outline-success" 
               title="Editar cena">
                <i class="fas fa-edit"></i>
            </a>
        @else
            <button class="btn btn-sm btn-outline-secondary" 
                    disabled
                    title="No se puede editar">
                <i class="fas fa-lock"></i>
            </button>
        @endif
    </div>
</td>

    </tr>
    @empty
    <tr>
        <td colspan="6" class="text-center text-muted py-4">
            <i class="fas fa-calendar-times fa-2x mb-3"></i>
            <br>
            <strong>No tienes cenas programadas</strong>
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

.stat-icon.bg-primary { background-color: #2563eb !important; }
.stat-icon.bg-success { background-color: #059669 !important; }
.stat-icon.bg-warning { background-color: #d97706 !important; }
.stat-icon.bg-danger { background-color: #1e293b !important; }

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

.gallery-preview-container {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
}

/* ==================== VALIDACIÓN DE FECHA/HORA ==================== */
input[name="datetime"]:invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

#datetime-error-message {
    animation: slideDown 0.3s ease-out;
    border-left: 4px solid #ffc107;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ==================== GOOGLE MAPS Y BÚSQUEDA ==================== */
#locationInput {
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

#locationInput:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

.input-group .btn {
    border: 2px solid #e9ecef;
    border-left: none;
}

.input-group .btn:hover {
    background-color: #f8f9fa;
    border-color: #2563eb;
}

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
    margin-top: 10px;
}

#selectedAddress .alert {
    margin-bottom: 0;
    padding: 10px 15px;
    border-radius: 6px;
    border-left: 4px solid #28a745;
}

/* ==================== SUGERENCIAS DE GOOGLE MAPS ==================== */
.suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    max-height: 300px;
    overflow-y: auto;
    animation: fadeInDown 0.2s ease-out;
    margin-top: 2px;
    width: calc(100% - 4px);
    left: 2px;
}

.suggestions-header {
    padding: 8px 12px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-size: 11px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1.2;
}

.suggestion-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.2s ease;
    font-size: 13px;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item:hover, .suggestion-item.selected {
    background-color: #e3f2fd;
    border-left: 3px solid #2563eb;
}

.suggestion-item i {
    width: 16px;
    margin-right: 8px;
    color: #2563eb;
    flex-shrink: 0;
}

.suggestion-content {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}

.suggestion-main {
    font-weight: 500;
    color: #1f2937;
    font-size: 13px;
    line-height: 1.3;
    margin-bottom: 1px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.suggestion-secondary {
    font-size: 11px;
    color: #6b7280;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.suggestions-dropdown::-webkit-scrollbar {
    width: 4px;
}

.suggestions-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.suggestions-dropdown::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.suggestions-dropdown::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
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
        padding: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
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
    
    .suggestions-dropdown {
        max-height: 200px;
        font-size: 12px;
    }
    
    .suggestion-item {
        padding: 6px 10px;
    }
    
    .suggestion-main {
        font-size: 12px;
    }
    
    .suggestion-secondary {
        font-size: 10px;
    }
    
    .suggestions-header {
        padding: 6px 10px;
        font-size: 10px;
    }
}
</style>

<script>
// ==================== VARIABLES GLOBALES ====================
let map, marker, infoWindow, geocoder, autocompleteService, placesService;
let coverImage = null;
let galleryImages = [];
const maxGalleryImages = 5;
const maxFileSize = 5 * 1024 * 1024; // 5MB

// ==================== INICIALIZACIÓN ====================
document.addEventListener('DOMContentLoaded', function() {
    setupDateTimeValidation();
    setupImageUploads();
    setupNavigation();
    setupModalEvents();
});

// ==================== NAVEGACIÓN DEL DASHBOARD ====================
function setupNavigation() {
    // Función para mostrar secciones
    window.showSection = function(sectionName) {
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });
        
        document.querySelectorAll('.menu-link').forEach(link => {
            link.classList.remove('active');
        });
        
        const targetSection = document.getElementById(sectionName + '-section');
        if (targetSection) {
            targetSection.classList.add('active');
        } else {
            document.getElementById('dashboard-section').classList.add('active');
        }
        
        event.target.closest('.menu-link').classList.add('active');
    };
    
    // Manejar clicks en sidebar
    document.querySelectorAll('.menu-link:not([data-bs-toggle])').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.classList.contains('disabled') && !this.hasAttribute('data-bs-toggle')) {
                e.preventDefault();
                document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
    
    // Form submission para perfil
    document.querySelector('.profile-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('¡Perfil actualizado exitosamente! (Esta es una demo)');
    });
}

// ==================== EVENTOS DEL MODAL ====================
function setupModalEvents() {
    const newDinnerModal = document.getElementById('newDinnerModal');
    if (!newDinnerModal) return;

    // Inicializar Google Maps al abrir modal
    newDinnerModal.addEventListener('shown.bs.modal', function () {
        if (typeof google === 'undefined' || !google.maps) {
            loadGoogleMaps();
        } else {
            initMap();
        }
    });

    // Limpiar al cerrar modal
    newDinnerModal.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('dinnerForm');
        if (form) form.reset();
        
        document.getElementById('selectedAddress').innerHTML = '';
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        
        hideDateTimeError();
        resetImageUploads();
        resetMapLocation();
        
        console.log('🧹 Modal limpiado correctamente');
    });

    // Botón de mi ubicación
    document.getElementById('myLocationBtn')?.addEventListener('click', getUserLocation);
}

// ==================== VALIDACIÓN DE FECHA Y HORA ====================
function setupDateTimeValidation() {
    const datetimeInput = document.querySelector('input[name="datetime"]');
    if (!datetimeInput) return;
    
    function setMinDateTime() {
        const now = new Date();
        now.setHours(now.getHours() + 1);
        
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        datetimeInput.min = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
    
    setMinDateTime();
    setInterval(setMinDateTime, 60000);
    
    datetimeInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const minDate = new Date();
        minDate.setHours(minDate.getHours() + 1);
        
        if (selectedDate < minDate) {
            this.setCustomValidity('La cena debe programarse al menos 1 hora en el futuro');
            this.reportValidity();
            showDateTimeError();
        } else {
            this.setCustomValidity('');
            hideDateTimeError();
        }
    });
    
    datetimeInput.addEventListener('blur', function() {
        this.dispatchEvent(new Event('change'));
    });
}

function showDateTimeError() {
    let errorDiv = document.getElementById('datetime-error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'datetime-error-message';
        errorDiv.className = 'alert alert-warning mt-2';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Fecha inválida:</strong> La cena debe programarse al menos 1 hora en el futuro.
            <br><small class="text-muted">Esto permite tiempo suficiente para que los comensales se enteren y reserven.</small>
        `;
        
        const datetimeInput = document.querySelector('input[name="datetime"]');
        datetimeInput.parentNode.insertBefore(errorDiv, datetimeInput.nextSibling);
    }
}

function hideDateTimeError() {
    const errorDiv = document.getElementById('datetime-error-message');
    if (errorDiv) errorDiv.remove();
}

// ==================== GOOGLE MAPS ====================
function loadGoogleMaps() {
    if (typeof google !== 'undefined' && google.maps) {
        initMap();
        return;
    }

    if (document.querySelector('script[src*="maps.googleapis.com"]')) return;

    window.initMapCallback = function() {
        if (typeof google !== 'undefined' && google.maps) {
            initMap();
        } else {
            setTimeout(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    initMap();
                } else {
                    showMapError('Google Maps no se cargó correctamente');
                }
            }, 1000);
        }
    };

    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCuh8GSFyFxvDaiEeWcW7JXs2KIcf89dHY&libraries=places&callback=initMapCallback';
    script.async = true;
    script.defer = true;
    script.onerror = () => showMapError('Error cargando Google Maps');
    document.head.appendChild(script);
}

function initMap() {
    try {
        if (!google.maps || !google.maps.places) {
            throw new Error('APIs de Google Maps no disponibles');
        }

        const defaultLocation = { lat: -34.6037, lng: -58.3816 };
        
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: defaultLocation,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });

        geocoder = new google.maps.Geocoder();
        autocompleteService = new google.maps.places.AutocompleteService();
        placesService = new google.maps.places.PlacesService(map);
        infoWindow = new google.maps.InfoWindow();

        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            title: 'Ubicación de la cena',
            draggable: true
        });

        map.addListener('click', function(event) {
            placeMarker(event.latLng);
            updateLocationDisplay(event.latLng);
        });

        marker.addListener('dragend', function(event) {
            updateLocationDisplay(event.latLng);
        });

        setupAdvancedSearch();
        updateLocationDisplay(defaultLocation, 'Buenos Aires, Argentina (ubicación predeterminada)');
        
        console.log('🎉 Google Maps inicializado exitosamente');
        
    } catch (error) {
        console.error('❌ Error inicializando Google Maps:', error);
        showMapError(`Error inicializando el mapa: ${error.message}`);
    }
}

function setupAdvancedSearch() {
    const input = document.getElementById('locationInput');
    if (!input) return;

    let searchTimeout;
    
    input.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(() => getPlacePredictions(query), 500);
        } else {
            hideSuggestions();
        }
    });

    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const dropdown = document.getElementById('suggestions-dropdown');
            if (dropdown) {
                const firstItem = dropdown.querySelector('.suggestion-item');
                if (firstItem) firstItem.click();
            } else {
                searchLocation();
            }
        }
    });

    // Ocultar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('suggestions-dropdown');
        if (dropdown && !input.contains(e.target) && !dropdown.contains(e.target)) {
            hideSuggestions();
        }
    });

    input.addEventListener('blur', function() {
        setTimeout(() => {
            const dropdown = document.getElementById('suggestions-dropdown');
            if (dropdown && !dropdown.matches(':hover')) {
                hideSuggestions();
            }
        }, 150);
    });
}

function getPlacePredictions(query) {
    if (!autocompleteService) {
        fallbackToGeocoding(query);
        return;
    }

    const request = {
        input: query,
        types: ['establishment', 'geocode'],
        componentRestrictions: { country: ['co', 'ar', 'mx', 'us', 'es'] }
    };

    autocompleteService.getPlacePredictions(request, function(predictions, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK && predictions?.length > 0) {
            showSuggestions(predictions);
        } else {
            setTimeout(() => fallbackToGeocoding(query), 100);
        }
    });
}

function fallbackToGeocoding(query) {
    if (!geocoder) return;

    geocoder.geocode({ address: query }, function(results, status) {
        if (status === 'OK' && results?.length > 0) {
            const suggestions = results.slice(0, 5).map(result => ({
                description: result.formatted_address,
                place_id: result.place_id,
                structured_formatting: {
                    main_text: result.address_components[0]?.long_name || result.formatted_address,
                    secondary_text: result.formatted_address
                },
                types: result.types
            }));
            showSuggestions(suggestions);
        }
    });
}

function showSuggestions(predictions) {
    hideSuggestions();
    
    const inputGroup = document.getElementById('locationInput').closest('.input-group');
    if (!inputGroup) return;
    
    const dropdown = document.createElement('div');
    dropdown.id = 'suggestions-dropdown';
    dropdown.className = 'suggestions-dropdown';
    
    const header = document.createElement('div');
    header.className = 'suggestions-header';
    header.innerHTML = `<i class="fas fa-search"></i> ${predictions.length} lugar${predictions.length > 1 ? 'es' : ''} encontrado${predictions.length > 1 ? 's' : ''}`;
    dropdown.appendChild(header);
    
    predictions.slice(0, 6).forEach(prediction => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        
        const icon = getPlaceIcon(prediction.types);
        
        item.innerHTML = `
            <i class="${icon}"></i>
            <div class="suggestion-content">
                <div class="suggestion-main">${prediction.structured_formatting.main_text}</div>
                <div class="suggestion-secondary">${prediction.structured_formatting.secondary_text || ''}</div>
            </div>
        `;
        
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            selectPlace(prediction);
        });
        
        dropdown.appendChild(item);
    });
    
    inputGroup.parentNode.insertBefore(dropdown, inputGroup.nextSibling);
}

function selectPlace(prediction) {
    hideSuggestions();
    document.getElementById('locationInput').value = prediction.description;

    if (prediction.place_id && placesService) {
        const request = {
            placeId: prediction.place_id,
            fields: ['name', 'geometry', 'formatted_address', 'rating', 'types']
        };

        placesService.getDetails(request, function(place, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                const location = place.geometry.location;
                placeMarker(location);
                map.setCenter(location);
                map.setZoom(16);
                updateLocationDisplayAdvanced(location, place);
            } else {
                geocodeAndPlace(prediction.description);
            }
        });
    } else {
        geocodeAndPlace(prediction.description);
    }
}

function geocodeAndPlace(address) {
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === 'OK' && results[0]) {
            const location = results[0].geometry.location;
            placeMarker(location);
            map.setCenter(location);
            map.setZoom(15);
            updateLocationDisplay(location, results[0].formatted_address);
        }
    });
}

function getPlaceIcon(types) {
    if (!types || !Array.isArray(types)) return 'fas fa-map-marker-alt';
    
    if (types.includes('restaurant') || types.includes('food')) return 'fas fa-utensils';
    if (types.includes('lodging')) return 'fas fa-bed';
    if (types.includes('tourist_attraction')) return 'fas fa-camera';
    if (types.includes('shopping_mall') || types.includes('store')) return 'fas fa-shopping-bag';
    if (types.includes('park')) return 'fas fa-tree';
    if (types.includes('locality') || types.includes('sublocality')) return 'fas fa-city';
    if (types.includes('country')) return 'fas fa-flag';
    
    return 'fas fa-map-marker-alt';
}

function hideSuggestions() {
    const dropdown = document.getElementById('suggestions-dropdown');
    if (dropdown) dropdown.remove();
}

function placeMarker(location) {
    if (marker) marker.setPosition(location);
    if (map) map.setCenter(location);
}

function updateLocationDisplay(location, placeName = null) {
    const lat = typeof location.lat === 'function' ? location.lat() : location.lat;
    const lng = typeof location.lng === 'function' ? location.lng() : location.lng;
    
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    
    const displayName = placeName || document.getElementById('locationInput').value.trim() || 'Ubicación seleccionada';
    
    if (placeName && placeName !== document.getElementById('locationInput').value) {
        document.getElementById('locationInput').value = placeName;
    }
    
    const addressElement = document.getElementById('selectedAddress');
    addressElement.innerHTML = `
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <strong>${displayName}</strong>
            <br>
            <small>Coordenadas: ${lat.toFixed(6)}, ${lng.toFixed(6)}</small>
        </div>
    `;
}

function updateLocationDisplayAdvanced(location, place) {
    updateLocationDisplay(location, place.formatted_address);
    
    if (infoWindow && marker) {
        const rating = place.rating ? `⭐ ${place.rating}` : '';
        const name = place.name || place.formatted_address;
        
        infoWindow.setContent(`
            <div style="padding: 10px; max-width: 300px;">
                <strong>${name}</strong>
                ${rating}<br>
                <small>${place.formatted_address}</small>
            </div>
        `);
        infoWindow.open(map, marker);
    }
}

function getUserLocation() {
    if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización.');
        return;
    }

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
            
            const currentName = document.getElementById('locationInput').value.trim();
            if (!currentName) {
                document.getElementById('locationInput').value = 'Mi ubicación actual';
                updateLocationDisplay(userLocation, 'Mi ubicación actual');
            } else {
                updateLocationDisplay(userLocation);
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
}

function showMapError(message) {
    const mapElement = document.getElementById('map');
    if (mapElement) {
        mapElement.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error de Google Maps:</strong> ${message}
                <br><small>Recarga la página si el problema persiste.</small>
            </div>
        `;
    }
}

// ==================== MANEJO DE IMÁGENES ====================
function setupImageUploads() {
    setupCoverImageUpload();
    setupGalleryImageUpload();
}

function setupCoverImageUpload() {
    const dropZone = document.getElementById('coverImageDropZone');
    const input = document.getElementById('coverImageInput');
    
    if (!dropZone || !input) return;
    
    dropZone.addEventListener('click', () => input.click());
    dropZone.addEventListener('dragover', handleDragOver);
    dropZone.addEventListener('dragleave', handleDragLeave);
    dropZone.addEventListener('drop', (e) => handleCoverImageDrop(e));
    input.addEventListener('change', (e) => handleCoverImageSelect(e.target.files));
}

function setupGalleryImageUpload() {
    const dropZone = document.getElementById('galleryDropZone');
    const input = document.getElementById('galleryImagesInput');
    
    if (!dropZone || !input) return;
    
    dropZone.addEventListener('click', () => input.click());
    dropZone.addEventListener('dragover', handleDragOver);
    dropZone.addEventListener('dragleave', handleDragLeave);
    dropZone.addEventListener('drop', (e) => handleGalleryImagesDrop(e));
    input.addEventListener('change', (e) => handleGalleryImagesSelect(e.target.files));
}

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

function handleCoverImageDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    e.target.closest('.image-drop-zone').classList.remove('dragover');
    handleCoverImageSelect(e.dataTransfer.files);
}

function handleCoverImageSelect(files) {
    if (files && files[0] && validateImageFile(files[0])) {
        setCoverImage(files[0]);
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
}

function changeCoverImage() {
    document.getElementById('coverImageInput').click();
}

function handleGalleryImagesDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    e.target.closest('.image-drop-zone').classList.remove('dragover');
    handleGalleryImagesSelect(e.dataTransfer.files);
}

function handleGalleryImagesSelect(files) {
    if (!files || files.length === 0) return;
    
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

function addGalleryImage(file) {
    const id = Date.now() + Math.random();
    galleryImages.push({ id, file });
}

function removeGalleryImage(id) {
    galleryImages = galleryImages.filter(img => img.id !== id);
    updateGalleryDisplay();
}

function updateGalleryDisplay() {
    const container = document.getElementById('galleryPreview');
    if (!container) return;
    
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

function validateImageFile(file) {
    if (!file.type.startsWith('image/')) {
        showError('Solo se permiten archivos de imagen');
        return false;
    }
    
    if (file.size > maxFileSize) {
        showError('El archivo es muy grande. Máximo 5MB');
        return false;
    }
    
    return true;
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message alert alert-warning';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
    
    const section = document.querySelector('.image-upload-section');
    if (section) {
        section.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 3000);
    }
}

// ==================== CREAR CENA ====================
function createDinner() {
    const form = document.getElementById('dinnerForm');
    const formData = new FormData(form);
    
    // Validación de fecha
    const datetime = formData.get('datetime');
    if (datetime) {
        const selectedDate = new Date(datetime);
        const minDate = new Date();
        minDate.setHours(minDate.getHours() + 1);
        
        if (selectedDate < minDate) {
            showDateTimeError();
            document.querySelector('input[name="datetime"]').focus();
            alert('La cena debe programarse al menos 1 hora en el futuro.');
            return false;
        }
    }
    
    hideDateTimeError();
    
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
    
    // Agregar imágenes
    if (coverImage) {
        formData.append('cover_image', coverImage);
    }
    
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
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('¡Cena creada exitosamente!');
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('newDinnerModal'));
            modal.hide();
            form.reset();
            document.getElementById('selectedAddress').innerHTML = '';
            
            resetImageUploads();
            resetMapLocation();
            
            window.location.reload();
        } else {
            if (data.errors && data.errors.datetime) {
                showDateTimeError();
                document.querySelector('input[name="datetime"]').focus();
            }
            alert('Error: ' + (data.message || 'No se pudo crear la cena'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión. Inténtalo de nuevo.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// ==================== FUNCIONES DE LIMPIEZA ====================
function resetImageUploads() {
    coverImage = null;
    const preview = document.getElementById('coverImagePreview');
    const dropZone = document.getElementById('coverImageDropZone');
    const input = document.getElementById('coverImageInput');
    
    if (preview && dropZone && input) {
        preview.classList.add('d-none');
        dropZone.classList.remove('d-none');
        input.value = '';
    }
    
    galleryImages = [];
    const galleryContainer = document.getElementById('galleryPreview');
    if (galleryContainer) galleryContainer.innerHTML = '';
    
    const counter = document.querySelector('.image-counter');
    if (counter) counter.remove();
}

function resetMapLocation() {
    if (map && marker) {
        const defaultLocation = { lat: -34.6037, lng: -58.3816 };
        marker.setPosition(defaultLocation);
        map.setCenter(defaultLocation);
        map.setZoom(10);
        
        if (infoWindow) infoWindow.close();
    }
}

// ==================== FUNCIONES GLOBALES ====================
window.initMap = initMap;
window.initMapCallback = window.initMapCallback;
window.createDinner = createDinner;
window.removeCoverImage = removeCoverImage;
window.changeCoverImage = changeCoverImage;
window.removeGalleryImage = removeGalleryImage;
</script>
@endsection