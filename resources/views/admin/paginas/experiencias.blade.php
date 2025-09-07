
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
                            <h2>Editar Página: Experiencias</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Editar Experiencias</li>
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

                    <!-- Formulario Principal -->
                    <form action="{{ route('admin.paginas.experiencias.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Hero Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-star me-2"></i>Sección Hero</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Título Principal</label>
                                            <textarea name="hero_titulo" class="form-control" rows="3" placeholder="Descubre experiencias gastronómicas únicas...">{{ $contenidos->where('clave', 'hero_titulo')->first()?->valor ?? 'Descubre experiencias gastronómicas únicas en hogares locales' }}</textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Subtítulo</label>
                                            <textarea name="hero_subtitulo" class="form-control" rows="3" placeholder="Conecta con chefs anfitriones...">{{ $contenidos->where('clave', 'hero_subtitulo')->first()?->valor ?? 'Conecta con chefs anfitriones apasionados y disfruta de cenas íntimas, auténticas y memorables en espacios privados únicos.' }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Texto Botón 1</label>
                                                <input type="text" name="hero_boton1" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'hero_boton1')->first()?->valor ?? 'Explorar Experiencias' }}">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Texto Botón 2</label>
                                                <input type="text" name="hero_boton2" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'hero_boton2')->first()?->valor ?? 'Convertirse en Chef' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Imagen Hero</label>
                                            <div class="current-image mb-2">
                                                @php $heroImg = $contenidos->where('clave', 'hero_imagen')->first()?->valor @endphp
                                                @if($heroImg)
                                                    @if(Str::startsWith($heroImg, ['http://', 'https://']))
                                                        <img src="{{ $heroImg }}" alt="Hero actual" class="img-thumbnail" style="max-width: 200px;">
                                                    @else
                                                        <img src="{{ asset('storage/' . $heroImg) }}" alt="Hero actual" class="img-thumbnail" style="max-width: 200px;">
                                                    @endif
                                                @else
                                                    <div class="text-muted">No hay imagen actual</div>
                                                @endif
                                            </div>
                                            <input type="file" name="hero_imagen" class="form-control" 
                                                   accept="image/*" id="heroImageInput">
                                            <small class="text-muted">JPG, PNG, WebP hasta 2MB (recomendado: 800x600px)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Por Qué Elegir -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>¿Por qué elegir TuMesa?</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Título de Sección</label>
                                        <input type="text" name="elegir_titulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'elegir_titulo')->first()?->valor ?? '¿Por qué elegir TuMesa?' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subtítulo de Sección</label>
                                        <input type="text" name="elegir_subtitulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'elegir_subtitulo')->first()?->valor ?? 'Descubre lo que hace especial cada experiencia gastronómica' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Feature 1 -->
                                    <div class="col-md-4">
                                        <h6>Feature 1</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono</label>
                                            <input type="text" name="feature1_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'feature1_icono')->first()?->valor ?? 'fas fa-utensils' }}"
                                                   placeholder="fas fa-utensils">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="feature1_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'feature1_titulo')->first()?->valor ?? 'Culinarias Auténticas' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="feature1_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'feature1_descripcion')->first()?->valor ?? 'Experimenta sabores auténticos preparados por chefs locales apasionados con ingredientes frescos y recetas tradicionales.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Feature 2 -->
                                    <div class="col-md-4">
                                        <h6>Feature 2</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono</label>
                                            <input type="text" name="feature2_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'feature2_icono')->first()?->valor ?? 'fas fa-users' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="feature2_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'feature2_titulo')->first()?->valor ?? 'Cocineros Todos' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="feature2_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'feature2_descripcion')->first()?->valor ?? 'Conecta con una comunidad diversa de chefs anfitriones, cada uno con su propia historia y especialidad culinaria única.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Feature 3 -->
                                    <div class="col-md-4">
                                        <h6>Feature 3</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono</label>
                                            <input type="text" name="feature3_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'feature3_icono')->first()?->valor ?? 'fas fa-shield-alt' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="feature3_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'feature3_titulo')->first()?->valor ?? 'Segura y Confiable' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="feature3_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'feature3_descripcion')->first()?->valor ?? 'Todas nuestras experiencias están verificadas y nuestros chefs pasan por un proceso de selección riguroso para tu seguridad.' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Cómo Funciona -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Cómo Funciona</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Título de Sección</label>
                                        <input type="text" name="funciona_titulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'funciona_titulo')->first()?->valor ?? 'Cómo Funciona' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subtítulo de Sección</label>
                                        <input type="text" name="funciona_subtitulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'funciona_subtitulo')->first()?->valor ?? 'Cuatro simples pasos para vivir una experiencia única' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Paso 1 -->
                                    <div class="col-md-3">
                                        <h6>Paso 1</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono</label>
                                            <input type="text" name="paso1_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso1_icono')->first()?->valor ?? 'fas fa-search' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="paso1_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso1_titulo')->first()?->valor ?? 'Explora' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="paso1_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'paso1_descripcion')->first()?->valor ?? 'Navega por cientos de experiencias gastronómicas únicas cerca de ti y encuentra la perfecta para tu ocasión.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Paso 2 -->
                                    <div class="col-md-3">
                                        <h6>Paso 2</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono</label>
                                            <input type="text" name="paso2_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso2_icono')->first()?->valor ?? 'fas fa-calendar-check' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="paso2_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso2_titulo')->first()?->valor ?? 'Reserva' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="paso2_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'paso2_descripcion')->first()?->valor ?? 'Selecciona la fecha y hora que mejor te convenga y confirma tu reserva de forma segura en nuestra plataforma.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Paso 3 -->
                                    <div class="col-md-3">
                                        <h6>Paso 3</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono</label>
                                            <input type="text" name="paso3_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso3_icono')->first()?->valor ?? 'fas fa-utensils' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="paso3_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso3_titulo')->first()?->valor ?? 'Disfruta' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="paso3_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'paso3_descripcion')->first()?->valor ?? 'Vive una experiencia gastronómica inolvidable y conecta con otros amantes de la buena comida y tu chef anfitrión.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Paso 4 -->
                                    <div class="col-md-3">
                                        <h6>Paso 4</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono</label>
                                            <input type="text" name="paso4_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso4_icono')->first()?->valor ?? 'fas fa-heart' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="paso4_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso4_titulo')->first()?->valor ?? 'Comparte' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="paso4_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'paso4_descripcion')->first()?->valor ?? 'Deja tu reseña y comparte tu experiencia para ayudar a otros comensales a descubrir nuevos sabores.' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección CTA -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Call to Action</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Título CTA</label>
                                            <input type="text" name="cta_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'cta_titulo')->first()?->valor ?? '¿Listo para tu próxima aventura gastronómica?' }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Descripción CTA</label>
                                            <textarea name="cta_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'cta_descripcion')->first()?->valor ?? 'Únete a miles de comensales que ya han descubierto sabores únicos' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Texto Botón Principal</label>
                                                <input type="text" name="cta_boton1" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'cta_boton1')->first()?->valor ?? 'Crear mi cuenta' }}">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Texto Botón Secundario</label>
                                                <input type="text" name="cta_boton2" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'cta_boton2')->first()?->valor ?? 'Explorar ahora' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    border-radius: 0.375rem;
}

.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

h6 {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.current-image {
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    text-align: center;
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
@endsection