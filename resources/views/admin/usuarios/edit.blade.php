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
                            <h2>Editar Usuario</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
                                    <li class="breadcrumb-item active">Editar: {{ $user->name }}</li>
                                </ol>
                            </nav>
                        </div>
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a Lista
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Por favor corrige los siguientes errores:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <!-- Formulario de Edición -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i>Información del Usuario
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Información Básica -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-user me-2"></i>Información Personal
                                        </h6>
                                    </div>
                                    
                                    <!-- Avatar -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Avatar</label>
                                        <div class="d-flex align-items-center">
                                            @if($user->avatar)
                                                @if(str_starts_with($user->avatar, 'http'))
                                                    <img src="{{ $user->avatar }}" alt="Avatar actual" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar actual" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                @endif
                                            @else
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 60px; height: 60px; font-weight: bold; font-size: 24px;">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <input type="file" name="avatar" class="form-control" accept="image/*">
                                                <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                                   value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                                                   value="{{ old('telefono', $user->telefono) }}" placeholder="Ej: +57 300 123 4567">
                                            @error('telefono')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                                <option value="">Seleccionar rol...</option>
                                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                                    Administrador
                                                </option>
                                                <option value="chef_anfitrion" {{ old('role', $user->role) == 'chef_anfitrion' ? 'selected' : '' }}>
                                                    Chef Anfitrión
                                                </option>
                                                <option value="comensal" {{ old('role', $user->role) == 'comensal' ? 'selected' : '' }}>
                                                    Comensal
                                                </option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Dirección</label>
                                            <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                                                      rows="2" placeholder="Dirección completa">{{ old('direccion', $user->direccion) }}</textarea>
                                            @error('direccion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Biografía</label>
                                            <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" 
                                                      rows="3" placeholder="Descripción personal o profesional">{{ old('bio', $user->bio) }}</textarea>
                                            @error('bio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Chef -->
                                <div class="row mb-4" id="chef-info" style="{{ $user->role == 'chef_anfitrion' ? '' : 'display: none;' }}">
                                    <div class="col-12">
                                        <h6 class="text-warning border-bottom pb-2 mb-3">
                                            <i class="fas fa-chef-hat me-2"></i>Información de Chef
                                        </h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Especialidad</label>
                                            <input type="text" name="especialidad" class="form-control @error('especialidad') is-invalid @enderror" 
                                                   value="{{ old('especialidad', $user->especialidad) }}" 
                                                   placeholder="Ej: Cocina Italiana, Asiática, Molecular...">
                                            @error('especialidad')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Años de Experiencia</label>
                                            <input type="number" name="experiencia_anos" class="form-control @error('experiencia_anos') is-invalid @enderror" 
                                                   value="{{ old('experiencia_anos', $user->experiencia_anos) }}" min="0" max="50">
                                            @error('experiencia_anos')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" 
                                                   value="{{ old('website', $user->website) }}" 
                                                   placeholder="https://mi-sitio-web.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Redes Sociales -->
                                <div class="row mb-4" id="social-info" style="{{ $user->role == 'chef_anfitrion' ? '' : 'display: none;' }}">
                                    <div class="col-12">
                                        <h6 class="text-info border-bottom pb-2 mb-3">
                                            <i class="fas fa-share-alt me-2"></i>Redes Sociales
                                        </h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fab fa-instagram text-danger me-1"></i>Instagram
                                            </label>
                                            <input type="text" name="instagram" class="form-control @error('instagram') is-invalid @enderror" 
                                                   value="{{ old('instagram', $user->instagram) }}" 
                                                   placeholder="@usuario o https://instagram.com/usuario">
                                            @error('instagram')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fab fa-facebook text-primary me-1"></i>Facebook
                                            </label>
                                            <input type="text" name="facebook" class="form-control @error('facebook') is-invalid @enderror" 
                                                   value="{{ old('facebook', $user->facebook) }}" 
                                                   placeholder="usuario o https://facebook.com/usuario">
                                            @error('facebook')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Información de Sistema (Solo lectura) -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-secondary border-bottom pb-2 mb-3">
                                            <i class="fas fa-info-circle me-2"></i>Información del Sistema
                                        </h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Proveedor de Autenticación</label>
                                            <input type="text" class="form-control-plaintext" readonly 
                                                   value="{{ $user->provider ? ucfirst($user->provider) : 'Registro directo' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha de Registro</label>
                                            <input type="text" class="form-control-plaintext" readonly 
                                                   value="{{ $user->created_at->format('d/m/Y H:i:s') }}">
                                        </div>
                                    </div>

                                    @if($user->role == 'chef_anfitrion' && $user->rating > 0)
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Calificación</label>
                                                <input type="text" class="form-control-plaintext" readonly 
                                                       value="{{ $user->formatted_rating }}/5.0">
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Botones de Acción -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                                </button>
                                                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary ms-2">
                                                    <i class="fas fa-times me-2"></i>Cancelar
                                                </a>
                                            </div>
                                            <div>
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Los campos marcados con <span class="text-danger">*</span> son obligatorios
                                                </small>
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
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('[name="role"]');
    const chefInfo = document.getElementById('chef-info');
    const socialInfo = document.getElementById('social-info');
    
    function toggleChefFields() {
        const isChef = roleSelect.value === 'chef_anfitrion';
        chefInfo.style.display = isChef ? '' : 'none';
        socialInfo.style.display = isChef ? '' : 'none';
        
        // Hacer campos obligatorios solo para chefs
        const chefFields = chefInfo.querySelectorAll('input, textarea, select');
        chefFields.forEach(field => {
            if (isChef && (field.name === 'especialidad')) {
                field.setAttribute('required', 'required');
            } else {
                field.removeAttribute('required');
            }
        });
    }
    
    // Ejecutar al cargar y al cambiar
    roleSelect.addEventListener('change', toggleChefFields);
    toggleChefFields(); // Ejecutar inmediatamente
});
</script>
@endsection