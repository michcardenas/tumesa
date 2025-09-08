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
                                            <th>Email</th>
                                            <th>Rol Actual</th>
                                            <th>Roles Spatie</th>
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
                                                            <small class="text-muted">{{ ucfirst($usuario->provider) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>
                                                @if($usuario->role == 'admin')
                                                    <span class="badge bg-danger">Administrador</span>
                                                @elseif($usuario->role == 'chef_anfitrion')
                                                    <span class="badge bg-warning">Chef Anfitrión</span>
                                                @elseif($usuario->role == 'comensal')
                                                    <span class="badge bg-success">Comensal</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $usuario->role }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($usuario->roles->count() > 0)
                                                    @foreach($usuario->roles as $role)
                                                        <span class="badge bg-info me-1">{{ $role->name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Sin roles</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $usuario->created_at->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                @if($usuario->id !== auth()->id())
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#changeRoleModal"
                                                            data-user-id="{{ $usuario->id }}"
                                                            data-user-name="{{ $usuario->name }}"
                                                            data-current-role="{{ $usuario->role }}">
                                                        <i class="fas fa-user-cog"></i> Cambiar Rol
                                                    </button>
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

<style>
.admin-container {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

.btn {
    border-radius: 0.375rem;
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const changeRoleModal = document.getElementById('changeRoleModal');
    
    if (changeRoleModal) {
        changeRoleModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const currentRole = button.getAttribute('data-current-role');
            
            // Actualizar el formulario
            const form = document.getElementById('changeRoleForm');
            form.action = `/admin/usuarios/${userId}/role`;
            
            // Actualizar el nombre del usuario
            document.getElementById('userName').textContent = userName;
            
            // Seleccionar el rol actual
            const roleSelect = form.querySelector('select[name="role"]');
            roleSelect.value = currentRole;
        });
    }
});
</script>
@endsection