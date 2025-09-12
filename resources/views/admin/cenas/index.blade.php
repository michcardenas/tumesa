@extends('layouts.app')

@section('content')
<div class="admin-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="admin-content">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2>Gestión de Experiencias Gastronómicas</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Experiencias</li>
                                </ol>
                            </nav>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $cenas->count() }}</h5>
                                    <p class="card-text text-muted">Total Experiencias</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $cenas->where('status', 'published')->count() }}</h5>
                                    <p class="card-text text-muted">Publicadas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $cenas->where('is_active', true)->count() }}</h5>
                                    <p class="card-text text-muted">Activas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $cenas->where('datetime', '>', now())->count() }}</h5>
                                    <p class="card-text text-muted">Próximas</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Experiencias -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Lista de Experiencias Gastronómicas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Experiencia</th>
                                            <th>Chef</th>
                                            <th>Fecha/Hora</th>
                                            <th>Capacidad</th>
                                            <th>Precio</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cenas as $cena)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($cena->cover_image_url)
                                                        <img src="{{ $cena->cover_image_url }}" alt="{{ $cena->title }}" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded me-2 d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px;">
                                                            <i class="fas fa-utensils text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $cena->title }}</div>
                                                        <small class="text-muted">{{ Str::limit($cena->menu, 40) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($cena->user)
                                                    <div class="d-flex align-items-center">
                                                        @if($cena->user->avatar)
                                                            <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                        @else
                                                            <div class="rounded-circle me-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 30px; height: 30px; font-size: 12px;">
                                                                {{ substr($cena->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <span>{{ $cena->user->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Sin chef</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $cena->datetime->format('d/m/Y') }}</div>
                                                    <small class="text-muted">{{ $cena->datetime->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-bold">{{ $cena->guests_current }}/{{ $cena->guests_max }}</span>
                                                    @if($cena->is_full)
                                                        <span class="badge bg-danger ms-1">Completo</span>
                                                    @elseif($cena->available_spots <= 3)
                                                        <span class="badge bg-warning ms-1">{{ $cena->available_spots }} lugares</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $cena->formatted_price }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    @if($cena->status == 'published')
                                                        <span class="badge bg-success">Publicada</span>
                                                    @elseif($cena->status == 'draft')
                                                        <span class="badge bg-secondary">Borrador</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ $cena->status }}</span>
                                                    @endif
                                                </div>
                                                <div class="mt-1">
                                                    @if($cena->is_active)
                                                        <span class="badge bg-success">Activa</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactiva</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Ver Detalles -->
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#viewCenaModal"
                                                            data-cena="{{ json_encode($cena) }}"
                                                            title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <!-- Editar -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editCenaModal"
                                                            data-cena="{{ json_encode($cena) }}"
                                                            title="Editar experiencia">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    
                                                    <!-- Eliminar -->
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteCenaModal"
                                                            data-cena-id="{{ $cena->id }}"
                                                            data-cena-title="{{ $cena->title }}"
                                                            title="Eliminar experiencia">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalles -->
<div class="modal fade" id="viewCenaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Experiencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cenaDetailsContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Experiencia -->
<div class="modal fade" id="editCenaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Experiencia Gastronómica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCenaForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Título de la Experiencia</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fecha y Hora</label>
                                <input type="datetime-local" name="datetime" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Precio por Persona</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Capacidad Máxima</label>
                                <input type="number" name="guests_max" class="form-control" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Invitados Actuales</label>
                                <input type="number" name="guests_current" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ubicación</label>
                                <input type="text" name="location" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-select" required>
                                    <option value="draft">Borrador</option>
                                    <option value="published">Publicada</option>
                                    <option value="cancelled">Cancelada</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1">
                                    <label class="form-check-label" for="is_active">
                                        Experiencia Activa
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Imagen de Portada</label>
                                <input type="file" name="cover_image" class="form-control" accept="image/*">
                                <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB.</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Menú / Descripción</label>
                        <textarea name="menu" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Requerimientos Especiales</label>
                        <textarea name="special_requirements" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Política de Cancelación</label>
                        <textarea name="cancellation_policy" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Experiencia -->
<div class="modal fade" id="deleteCenaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Experiencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteCenaForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡ATENCIÓN!</strong> Esta acción no se puede deshacer.
                    </div>
                    <p>¿Estás seguro de que quieres eliminar la experiencia: <strong id="deleteCenaTitle"></strong>?</p>
                    <p class="text-muted">Se eliminarán también todas las reservas y datos relacionados.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Experiencia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal Ver Detalles
    const viewCenaModal = document.getElementById('viewCenaModal');
    if (viewCenaModal) {
        viewCenaModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const cena = JSON.parse(button.getAttribute('data-cena'));
            
            const content = document.getElementById('cenaDetailsContent');
            if (content) {
                content.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            ${cena.cover_image_url ? 
                                `<img src="${cena.cover_image_url}" class="img-fluid rounded mb-3" style="max-height: 200px;">` : 
                                `<div class="bg-light rounded p-5 mb-3"><i class="fas fa-utensils fa-3x text-muted"></i></div>`
                            }
                            <h4>${cena.title}</h4>
                            <p class="text-muted">${cena.formatted_price} por persona</p>
                        </div>
                        <div class="col-md-8">
                            <h6>Información General</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Fecha:</strong></td><td>${new Date(cena.datetime).toLocaleDateString()}</td></tr>
                                <tr><td><strong>Hora:</strong></td><td>${new Date(cena.datetime).toLocaleTimeString()}</td></tr>
                                <tr><td><strong>Ubicación:</strong></td><td>${cena.location}</td></tr>
                                <tr><td><strong>Capacidad:</strong></td><td>${cena.guests_current}/${cena.guests_max} personas</td></tr>
                                <tr><td><strong>Estado:</strong></td><td>${cena.status}</td></tr>
                                <tr><td><strong>Activa:</strong></td><td>${cena.is_active ? 'Sí' : 'No'}</td></tr>
                            </table>
                            
                            <h6 class="mt-3">Menú / Descripción</h6>
                            <p>${cena.menu || 'No especificado'}</p>
                            
                            ${cena.special_requirements ? `
                                <h6 class="mt-3">Requerimientos Especiales</h6>
                                <p>${cena.special_requirements}</p>
                            ` : ''}
                            
                            ${cena.cancellation_policy ? `
                                <h6 class="mt-3">Política de Cancelación</h6>
                                <p>${cena.cancellation_policy}</p>
                            ` : ''}
                        </div>
                    </div>
                `;
            }
        });
    }

    // Modal Editar Experiencia
    const editCenaModal = document.getElementById('editCenaModal');
    if (editCenaModal) {
        editCenaModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const cena = JSON.parse(button.getAttribute('data-cena'));
            
            const form = document.getElementById('editCenaForm');
            if (form) {
                form.action = `/admin/cenas/${cena.id}`;
                
                // Función para establecer valores de campos
                const setFieldValue = (name, value) => {
                    const field = form.querySelector(`[name="${name}"]`);
                    if (field) {
                        if (field.type === 'checkbox') {
                            field.checked = Boolean(value);
                        } else {
                            field.value = value || '';
                        }
                    }
                };
                
                // Llenar campos del formulario
                setFieldValue('title', cena.title);
                setFieldValue('price', cena.price);
                setFieldValue('guests_max', cena.guests_max);
                setFieldValue('guests_current', cena.guests_current);
                setFieldValue('location', cena.location);
                setFieldValue('status', cena.status);
                setFieldValue('is_active', cena.is_active);
                setFieldValue('menu', cena.menu);
                setFieldValue('special_requirements', cena.special_requirements);
                setFieldValue('cancellation_policy', cena.cancellation_policy);
                
                // Formatear datetime para input datetime-local
                if (cena.datetime) {
                    const date = new Date(cena.datetime);
                    const formattedDate = date.toISOString().slice(0, 16);
                    setFieldValue('datetime', formattedDate);
                }
            }
        });
    }

    // Modal Eliminar Experiencia
    const deleteCenaModal = document.getElementById('deleteCenaModal');
    if (deleteCenaModal) {
        deleteCenaModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const cenaId = button.getAttribute('data-cena-id');
            const cenaTitle = button.getAttribute('data-cena-title');
            
            const titleElement = document.getElementById('deleteCenaTitle');
            const form = document.getElementById('deleteCenaForm');
            
            if (titleElement) titleElement.textContent = cenaTitle;
            if (form) form.action = `/admin/cenas/${cenaId}`;
        });
    }
});
</script>
@endsection