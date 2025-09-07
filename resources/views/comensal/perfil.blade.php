@extends('layouts.app_comensal')

@section('content')
<div class="container">
    <div class="comensal-content">
        <h2 class="mb-4 d-flex align-items-center">
            <i class="fas fa-user-edit me-2"></i> Editar Perfil
        </h2>

        {{-- Alertas de éxito / error --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Corrige los errores:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            {{-- Columna izquierda: Avatar + Rating --}}
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        {{-- Avatar actual --}}
                        @php
                            $avatar = $user->avatar; // Puede ser URL (Google) o path local (storage)
                            // Si no hay avatar, un placeholder con inicial
                            $initial = strtoupper(substr($user->name, 0, 1));
                        @endphp

                        <div class="mb-3">
                            @if($avatar)
                                {{-- Si es URL absoluta (http...) úsala tal cual, si no, asume storage --}}
                                @if(Str::startsWith($avatar, ['http://', 'https://']))
                                    <img id="avatarPreview" src="{{ $avatar }}" alt="Avatar" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                                @else
                                    <img id="avatarPreview" src="{{ asset('storage/'.$avatar) }}" alt="Avatar" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                                @endif
                            @else
                                <div id="avatarFallback" class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width:120px;height:120px;background:linear-gradient(135deg,#2563eb,#1e40af);color:#fff;font-size:48px;font-weight:700;">
                                    {{ $initial }}
                                </div>
                                <img id="avatarPreview" src="" alt="Avatar" class="rounded-circle d-none" style="width:120px;height:120px;object-fit:cover;">
                            @endif
                        </div>

                        {{-- Rating (solo lectura) --}}
                        <div class="mb-3">
                            @php
                                $rating = (float) ($user->rating ?? 0);
                                $stars = floor($rating);
                                $half  = ($rating - $stars) >= 0.5;
                            @endphp
                            <div class="mb-1">
                                {{-- estrellas llenas --}}
                                @for($i=0; $i<$stars; $i++)
                                    <i class="fas fa-star text-warning"></i>
                                @endfor
                                {{-- media estrella --}}
                                @if($half)
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                @endif
                                {{-- estrellas vacías --}}
                                @for($i=0; $i<(5 - $stars - ($half ? 1 : 0)); $i++)
                                    <i class="far fa-star text-muted"></i>
                                @endfor
                            </div>
                            <span class="badge bg-primary">{{ number_format($rating, 1) }} / 5</span>
                        </div>

                        <hr>

                        {{-- Formulario solo para cambiar avatar (URL o archivo) --}}
                        <form action="{{ route('perfil.comensal.update') }}" method="POST" enctype="multipart/form-data" class="text-start">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">URL de Avatar (opcional)</label>
                                <input type="url" name="avatar_url" class="form-control" placeholder="https://..." value="{{ old('avatar_url') }}">
                                <small class="text-muted">Si pegas una URL, tendrá prioridad sobre la imagen subida.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Subir Avatar (opcional)</label>
                                <input type="file" name="avatar_file" class="form-control" accept="image/*" id="avatarFileInput">
                                <small class="text-muted">JPG/PNG/WebP hasta 2MB.</small>
                            </div>

                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-image me-1"></i> Actualizar Avatar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: Datos editables --}}
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('perfil.comensal.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="update_type" value="profile">

                            {{-- Información básica --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nombre *</label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Correo *</label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                       value="{{ old('telefono', $user->telefono) }}" 
                                       placeholder="Ej: +54 11 1234-5678">
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Biografía</label>
                                <textarea name="bio" class="form-control" rows="4" 
                                          placeholder="Cuéntanos algo sobre ti...">{{ old('bio', $user->bio) }}</textarea>
                                <small class="text-muted">Describe tus gustos culinarios, experiencias gastronómicas favoritas, etc.</small>
                            </div>

                            {{-- Campos para el avatar --}}
                            <div class="mb-3">
                                <label class="form-label">Cambiar Foto de Perfil</label>
                                <input type="file" name="avatar_file" class="form-control" accept="image/*" id="avatarFileInput">
                                <small class="text-muted">JPG/PNG/WebP hasta 2MB (opcional)</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('perfil.comensal') }}" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sección para cambiar contraseña --}}
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-lock me-2"></i> Cambiar Contraseña
                        </h5>
                        
                        <form action="{{ route('perfil.comensal.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Contraseña Actual *</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña *</label>
                                    <input type="password" name="password" class="form-control" required minlength="8">
                                    <small class="text-muted">Mínimo 8 caracteres</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña *</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i> Actualizar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Preview de avatar cuando seleccionas archivo --}}
<script>
document.getElementById('avatarFileInput')?.addEventListener('change', function (e) {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
        const img = document.getElementById('avatarPreview');
        const fallback = document.getElementById('avatarFallback');
        if (img) {
            img.src = ev.target.result;
            img.classList.remove('d-none');
        }
        if (fallback) fallback.classList.add('d-none');
    };
    reader.readAsDataURL(file);
});
</script>
@endsection