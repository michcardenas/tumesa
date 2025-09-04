@extends('layouts.app_chef')

@section('content')
<div class="section-header">
    <h2>Editar Perfil de Chef</h2>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Error en la validación:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <form action="{{ route('chef.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')
            
            <!-- Información Personal -->
            <h5 class="mb-3">
                <i class="fas fa-user text-primary"></i> Información Personal
            </h5>
            
            <div class="mb-3">
                <label class="form-label">Nombre del Chef</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       name="name" 
                       value="{{ old('name', $user->name) }}" 
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       name="email" 
                       value="{{ old('email', $user->email) }}" 
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" 
                       class="form-control @error('telefono') is-invalid @enderror" 
                       name="telefono" 
                       value="{{ old('telefono', $user->telefono) }}" 
                       placeholder="Ej: +54 300 123 4567">
                @error('telefono')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" 
                       class="form-control @error('direccion') is-invalid @enderror" 
                       name="direccion" 
                       value="{{ old('direccion', $user->direccion) }}" 
                       placeholder="Tu dirección completa">
                @error('direccion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr class="my-4">
            
            <!-- Información Profesional -->
            <h5 class="mb-3">
                <i class="fas fa-chef-hat text-primary"></i> Información Profesional
            </h5>
            
            <div class="mb-3">
                <label class="form-label">Biografía</label>
                <textarea class="form-control @error('bio') is-invalid @enderror" 
                          name="bio" 
                          rows="4" 
                          placeholder="Cuéntanos sobre tu experiencia y pasión por la cocina...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Describe tu trayectoria, estilo de cocina y lo que te hace único.</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Especialidad</label>
                <input type="text" 
                       class="form-control @error('especialidad') is-invalid @enderror" 
                       name="especialidad" 
                       value="{{ old('especialidad', $user->especialidad) }}" 
                       placeholder="Ej: Cocina italiana, Pastelería francesa, Parrilla argentina...">
                @error('especialidad')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Años de Experiencia</label>
                <input type="number" 
                       class="form-control @error('experiencia_anos') is-invalid @enderror" 
                       name="experiencia_anos" 
                       value="{{ old('experiencia_anos', $user->experiencia_anos) }}" 
                       min="0" 
                       max="50"
                       placeholder="¿Cuántos años llevas cocinando profesionalmente?">
                @error('experiencia_anos')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr class="my-4">
            
            <!-- Redes Sociales -->
            <h5 class="mb-3">
                <i class="fas fa-share-alt text-primary"></i> Redes Sociales y Web
            </h5>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fab fa-instagram text-danger"></i> Instagram
                </label>
                <input type="text" 
                       class="form-control @error('instagram') is-invalid @enderror" 
                       name="instagram" 
                       value="{{ old('instagram', $user->instagram) }}" 
                       placeholder="@tu_usuario o URL completa">
                @error('instagram')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Puedes ingresar tu usuario con @ o la URL completa</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fab fa-facebook text-primary"></i> Facebook
                </label>
                <input type="text" 
                       class="form-control @error('facebook') is-invalid @enderror" 
                       name="facebook" 
                       value="{{ old('facebook', $user->facebook) }}" 
                       placeholder="tu.pagina o URL completa">
                @error('facebook')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Puedes ingresar tu página o la URL completa</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-globe"></i> Sitio Web
                </label>
                <input type="url" 
                       class="form-control @error('website') is-invalid @enderror" 
                       name="website" 
                       value="{{ old('website', $user->website) }}" 
                       placeholder="https://tu-sitio-web.com">
                @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr class="my-4">
            
            <!-- Cambiar Contraseña -->
            <h5 class="mb-3">
                <i class="fas fa-lock text-warning"></i> Cambiar Contraseña (Opcional)
            </h5>
            
            <div class="mb-3">
                <label class="form-label">Nueva Contraseña</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       name="password" 
                       placeholder="Dejar en blanco para mantener la actual">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Confirmar Nueva Contraseña</label>
                <input type="password" 
                       class="form-control" 
                       name="password_confirmation" 
                       placeholder="Confirma tu nueva contraseña">
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            
            <a href="{{ route('chef.dashboard') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </form>
    </div>
    
    <div class="col-md-4">
        <div class="profile-photo-section">
            <h5>
                <i class="fas fa-camera text-primary"></i> Foto de Perfil
            </h5>
            
            <div class="photo-preview mb-3">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" 
                         alt="Avatar" 
                         class="avatar-preview">
                @else
                    <div class="photo-placeholder">
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                    </div>
                @endif
            </div>
            
            <form action="{{ route('chef.profile.update') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  id="avatarForm">
                @csrf
                @method('PUT')
                
                <!-- Mantener otros datos del usuario -->
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="telefono" value="{{ $user->telefono }}">
                <input type="hidden" name="direccion" value="{{ $user->direccion }}">
                <input type="hidden" name="bio" value="{{ $user->bio }}">
                <input type="hidden" name="especialidad" value="{{ $user->especialidad }}">
                <input type="hidden" name="experiencia_anos" value="{{ $user->experiencia_anos }}">
                <input type="hidden" name="instagram" value="{{ $user->instagram }}">
                <input type="hidden" name="facebook" value="{{ $user->facebook }}">
                <input type="hidden" name="website" value="{{ $user->website }}">
                
                <div class="mb-3">
                    <input type="file" 
                           class="form-control @error('avatar') is-invalid @enderror" 
                           name="avatar" 
                           accept="image/*" 
                           onchange="document.getElementById('avatarForm').submit()">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        JPG, PNG, GIF. Máximo 2MB.
                    </small>
                </div>
            </form>
        </div>
        
        <div class="profile-info-card mt-4">
            <h6>Información de la Cuenta</h6>
            <p><strong>Rol:</strong> {{ ucwords(str_replace('_', ' ', $user->role)) }}</p>
            <p><strong>Miembro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
            @if($user->email_verified_at)
                <p><strong>Email verificado:</strong> 
                    <span class="text-success">
                        <i class="fas fa-check-circle"></i> Verificado
                    </span>
                </p>
            @endif
            @if($user->rating)
                <p><strong>Calificación:</strong> 
                    <span class="text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $user->rating)
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $user->rating)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                        ({{ $user->formatted_rating }})
                    </span>
                </p>
            @endif
            @if($user->experiencia_anos)
                <p><strong>Experiencia:</strong> {{ $user->experience_text }}</p>
            @endif
            @if($user->especialidad)
                <p><strong>Especialidad:</strong> {{ $user->especialidad }}</p>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e9ecef;
    display: block;
    margin: 0 auto;
}

.profile-info-card {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.profile-info-card h6 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.profile-info-card p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.invalid-feedback {
    display: block;
}
</style>
@endsection