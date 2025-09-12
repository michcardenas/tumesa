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
                            <h2>Gestión de Usuarios</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Usuarios</li>
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

                    <!-- Filtros y Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $usuarios->total() }}</h5>
                                    <p class="card-text text-muted">Total Usuarios</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $usuarios->where('role', 'admin')->count() }}</h5>
                                    <p class="card-text text-muted">Administradores</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $usuarios->where('role', 'chef_anfitrion')->count() }}</h5>
                                    <p class="card-text text-muted">Chefs</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $usuarios->where('role', 'comensal')->count() }}</h5>
                                    <p class="card-text text-muted">Comensales</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Usuarios -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Lista de Usuarios</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Contacto</th>
                                            <th>Rol & Permisos</th>
                                            <th>Información Chef</th>
                                            <th>Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usuarios as $usuario)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($usuario->avatar)
                                                        @if(str_starts_with($usuario->avatar, 'http'))
                                                            <img src="{{ $usuario->avatar }}" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        @else
                                                            <img src="{{ asset('storage/' . $usuario->avatar) }}" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        @endif
                                                    @else
                                                        <div class="rounded-circle me-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 40px; height: 40px; font-weight: bold;">
                                                            {{ substr($usuario->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $usuario->name }}</div>
                                                        @if($usuario->provider)
                                                            <small class="text-muted">
                                                                <i class="fab fa-{{ $usuario->provider }}"></i> 
                                                                {{ ucfirst($usuario->provider) }}
                                                            </small>
                                                        @endif
                                                        @if($usuario->bio)
                                                            <div>
                                                                <small class="text-muted" title="{{ $usuario->bio }}">
                                                                    {{ Str::limit($usuario->bio, 30) }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="d-block">
                                                        <i class="fas fa-envelope me-1"></i>{{ $usuario->email }}
                                                    </small>
                                                    @if($usuario->telefono)
                                                        <small class="d-block text-muted">
                                                            <i class="fas fa-phone me-1"></i>{{ $usuario->telefono }}
                                                        </small>
                                                    @endif
                                                    @if($usuario->direccion)
                                                        <small class="d-block text-muted" title="{{ $usuario->direccion }}">
                                                            <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($usuario->direccion, 25) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="mb-1">
                                                    @if($usuario->role == 'admin')
                                                        <span class="badge bg-danger">Administrador</span>
                                                    @elseif($usuario->role == 'chef_anfitrion')
                                                        <span class="badge bg-warning">Chef Anfitrión</span>
                                                    @elseif($usuario->role == 'comensal')
                                                        <span class="badge bg-success">Comensal</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $usuario->role }}</span>
                                                    @endif
                                                </div>
                                                @if($usuario->roles->count() > 0)
                                                    <div>
                                                        @foreach($usuario->roles as $role)
                                                            <span class="badge bg-info me-1" style="font-size: 0.7em;">{{ $role->name }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($usuario->role == 'chef_anfitrion')
                                                    <div class="small">
                                                        @if($usuario->especialidad)
                                                            <div class="mb-1">
                                                                <strong>Especialidad:</strong> {{ $usuario->especialidad }}
                                                            </div>
                                                        @endif
                                                        @if($usuario->experiencia_anos)
                                                            <div class="mb-1">
                                                                <i class="fas fa-clock me-1"></i>{{ $usuario->experience_text }}
                                                            </div>
                                                        @endif
                                                        @if($usuario->rating > 0)
                                                            <div class="mb-1">
                                                                <i class="fas fa-star text-warning me-1"></i>{{ $usuario->formatted_rating }}
                                                            </div>
                                                        @endif
                                                        <div class="d-flex gap-1">
                                                            @if($usuario->instagram)
                                                                <a href="{{ $usuario->instagram_url }}" target="_blank" class="text-decoration-none">
                                                                    <i class="fab fa-instagram text-danger"></i>
                                                                </a>
                                                            @endif
                                                            @if($usuario->facebook)
                                                                <a href="{{ $usuario->facebook_url }}" target="_blank" class="text-decoration-none">
                                                                    <i class="fab fa-facebook text-primary"></i>
                                                                </a>
                                                            @endif
                                                            @if($usuario->website)
                                                                <a href="{{ $usuario->website }}" target="_blank" class="text-decoration-none">
                                                                    <i class="fas fa-globe text-info"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <small class="text-muted">No aplica</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="d-block">{{ $usuario->created_at->format('d/m/Y') }}</small>
                                                <small class="text-muted">{{ $usuario->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($usuario->id !== auth()->id())
                                                    <div class="btn-group" role="group">
                                                        <!-- Ver Detalles -->
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#viewUserModal"
                                                                data-user="{{ json_encode($usuario) }}"
                                                                title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        
                                                        <!-- Editar -->
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editUserModal"
                                                                data-user="{{ json_encode($usuario) }}"
                                                                title="Editar usuario">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        
                                                        <!-- Cambiar Rol -->
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#changeRoleModal"
                                                                data-user-id="{{ $usuario->id }}"
                                                                data-user-name="{{ $usuario->name }}"
                                                                data-current-role="{{ $usuario->role }}"
                                                                title="Cambiar rol">
                                                            <i class="fas fa-user-cog"></i>
                                                        </button>
                                                        
                                                        <!-- Eliminar -->
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deleteUserModal"
                                                                data-user-id="{{ $usuario->id }}"
                                                                data-user-name="{{ $usuario->name }}"
                                                                title="Eliminar usuario">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <small class="text-muted">Tu cuenta</small>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            <div class="d-flex justify-content-center">
                                {{ $usuarios->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalles -->
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <textarea name="direccion" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biografía</label>
                                <textarea name="bio" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Especialidad (Solo Chefs)</label>
                                <input type="text" name="especialidad" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Años de Experiencia</label>
                                <input type="number" name="experiencia_anos" class="form-control" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Website</label>
                                <input type="url" name="website" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Instagram</label>
                                <input type="text" name="instagram" class="form-control" placeholder="@usuario o URL">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="text" name="facebook" class="form-control" placeholder="usuario o URL">
                            </div>
                        </div>
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

<!-- Modal para Cambiar Rol -->
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Rol de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changeRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Cambiar rol para: <strong id="userName"></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label">Nuevo Rol</label>
                        <select name="role" class="form-select" required>
                            <option value="">Seleccionar rol...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atención:</strong> Cambiar el rol modificará los permisos del usuario inmediatamente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Rol</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡ATENCIÓN!</strong> Esta acción no se puede deshacer.
                    </div>
                    <p>¿Estás seguro de que quieres eliminar al usuario: <strong id="deleteUserName"></strong>?</p>
                    <p class="text-muted">Se eliminarán también todos sus datos relacionados (experiencias, reservas, etc.).</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal Ver Detalles - Con mejor manejo de errores
    const viewUserModal = document.getElementById('viewUserModal');
    if (viewUserModal) {
        viewUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (!button || !button.hasAttribute('data-user')) {
                console.error('No se encontró data-user en el botón de ver detalles');
                return;
            }
            
            let user;
            try {
                user = JSON.parse(button.getAttribute('data-user'));
            } catch (e) {
                console.error('Error parsing user data for view modal:', e);
                return;
            }
            
            const content = document.getElementById('userDetailsContent');
            if (content) {
                content.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            ${user.avatar ? `<img src="${user.avatar.startsWith('http') ? user.avatar : '/storage/' + user.avatar}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">` : 
                              `<div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 120px; height: 120px; font-size: 48px; font-weight: bold;">${(user.name || 'U').charAt(0)}</div>`}
                            <h4>${user.name || 'Usuario sin nombre'}</h4>
                            <p class="text-muted">${user.email || 'Sin email'}</p>
                        </div>
                        <div class="col-md-8">
                            <h6>Información Personal</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Teléfono:</strong></td><td>${user.telefono || 'No especificado'}</td></tr>
                                <tr><td><strong>Dirección:</strong></td><td>${user.direccion || 'No especificado'}</td></tr>
                                <tr><td><strong>Biografía:</strong></td><td>${user.bio || 'No especificado'}</td></tr>
                                <tr><td><strong>Rol:</strong></td><td>${user.role || 'Sin rol'}</td></tr>
                                <tr><td><strong>Proveedor:</strong></td><td>${user.provider || 'Registro directo'}</td></tr>
                                <tr><td><strong>Registro:</strong></td><td>${user.created_at ? new Date(user.created_at).toLocaleDateString() : 'Fecha desconocida'}</td></tr>
                            </table>
                            
                            ${user.role === 'chef_anfitrion' ? `
                                <h6 class="mt-3">Información Chef</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Especialidad:</strong></td><td>${user.especialidad || 'No especificado'}</td></tr>
                                    <tr><td><strong>Experiencia:</strong></td><td>${user.experiencia_anos ? user.experiencia_anos + ' años' : 'No especificado'}</td></tr>
                                    <tr><td><strong>Rating:</strong></td><td>${user.rating > 0 ? user.rating + '/5' : 'Sin calificaciones'}</td></tr>
                                    <tr><td><strong>Instagram:</strong></td><td>${user.instagram ? `<a href="#" target="_blank">${user.instagram}</a>` : 'No especificado'}</td></tr>
                                    <tr><td><strong>Facebook:</strong></td><td>${user.facebook || 'No especificado'}</td></tr>
                                    <tr><td><strong>Website:</strong></td><td>${user.website ? `<a href="${user.website}" target="_blank">${user.website}</a>` : 'No especificado'}</td></tr>
                                </table>
                            ` : ''}
                        </div>
                    </div>
                `;
            } else {
                console.error('No se encontró el contenedor userDetailsContent');
            }
        });
    } else {
        console.error('No se encontró el modal viewUserModal');
    }

    // Modal Editar Usuario
    const editUserModal = document.getElementById('editUserModal');
    if (editUserModal) {
        editUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const user = JSON.parse(button.getAttribute('data-user'));
            
            const form = document.getElementById('editUserForm');
            if (form) {
                form.action = `/admin/users/${user.id}`;
            }
            
            // Mostrar avatar actual o placeholder
            const currentAvatar = document.getElementById('currentAvatar');
            const avatarPlaceholder = document.getElementById('avatarPlaceholder');
            
            if (currentAvatar && avatarPlaceholder) {
                if (user.avatar) {
                    const avatarUrl = user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`;
                    currentAvatar.src = avatarUrl;
                    currentAvatar.style.display = 'block';
                    avatarPlaceholder.style.display = 'none';
                } else {
                    currentAvatar.style.display = 'none';
                    avatarPlaceholder.textContent = user.name.charAt(0).toUpperCase();
                    avatarPlaceholder.style.display = 'flex';
                }
            }
            
            // Limpiar input de archivo y checkbox
            const avatarInput = document.getElementById('avatarInput');
            const removeAvatar = document.getElementById('removeAvatar');
            if (avatarInput) avatarInput.value = '';
            if (removeAvatar) removeAvatar.checked = false;
            
            // Llenar campos del formulario
            if (form) {
                const setFieldValue = (name, value) => {
                    const field = form.querySelector(`[name="${name}"]`);
                    if (field) field.value = value || '';
                };
                
                setFieldValue('name', user.name);
                setFieldValue('email', user.email);
                setFieldValue('telefono', user.telefono);
                setFieldValue('direccion', user.direccion);
                setFieldValue('bio', user.bio);
                setFieldValue('especialidad', user.especialidad);
                setFieldValue('experiencia_anos', user.experiencia_anos);
                setFieldValue('website', user.website);
                setFieldValue('instagram', user.instagram);
                setFieldValue('facebook', user.facebook);
            }
        });
    }

    // Preview de imagen al seleccionar archivo
    const avatarInput = document.getElementById('avatarInput');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const currentAvatar = document.getElementById('currentAvatar');
            const avatarPlaceholder = document.getElementById('avatarPlaceholder');
            const removeAvatar = document.getElementById('removeAvatar');
            
            if (file && currentAvatar && avatarPlaceholder) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentAvatar.src = e.target.result;
                    currentAvatar.style.display = 'block';
                    avatarPlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
                
                // Desmarcar eliminar avatar si se selecciona nueva imagen
                if (removeAvatar) removeAvatar.checked = false;
            }
        });
    }

    // Manejar checkbox de eliminar avatar
    const removeAvatar = document.getElementById('removeAvatar');
    if (removeAvatar) {
        removeAvatar.addEventListener('change', function() {
            const avatarInput = document.getElementById('avatarInput');
            const currentAvatar = document.getElementById('currentAvatar');
            const avatarPlaceholder = document.getElementById('avatarPlaceholder');
            
            if (this.checked && currentAvatar && avatarPlaceholder) {
                if (avatarInput) avatarInput.value = '';
                currentAvatar.style.display = 'none';
                avatarPlaceholder.style.display = 'flex';
            }
        });
    }

    // Modal Cambiar Rol
    const changeRoleModal = document.getElementById('changeRoleModal');
    if (changeRoleModal) {
        changeRoleModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const currentRole = button.getAttribute('data-current-role');
            
            const userNameElement = document.getElementById('userName');
            const changeRoleForm = document.getElementById('changeRoleForm');
            const roleSelect = changeRoleModal.querySelector('[name="role"]');
            
            if (userNameElement) userNameElement.textContent = userName;
            if (changeRoleForm) changeRoleForm.action = `/admin/users/${userId}/role`;
            if (roleSelect) roleSelect.value = currentRole;
        });
    }

    // Modal Eliminar Usuario
    const deleteUserModal = document.getElementById('deleteUserModal');
    if (deleteUserModal) {
        deleteUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            
            const deleteUserName = document.getElementById('deleteUserName');
            const deleteUserForm = document.getElementById('deleteUserForm');
            
            if (deleteUserName) deleteUserName.textContent = userName;
            if (deleteUserForm) deleteUserForm.action = `/admin/users/${userId}`;
        });
    }
});
</script>
@endsection