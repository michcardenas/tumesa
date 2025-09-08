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
                            <h2>Editar Página: Ser Chef Anfitrión</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Editar Ser Chef</li>
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
                    <form action="{{ route('admin.paginas.ser-chef.update') }}" method="POST" enctype="multipart/form-data">
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
                                            <input type="text" name="hero_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'hero_titulo')->first()?->valor ?? 'Conviértete en Chef Anfitrión' }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="hero_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'hero_descripcion')->first()?->valor ?? 'Comparte tu pasión por la cocina, conoce gente nueva y gana dinero extra ofreciendo experiencias gastronómicas únicas en tu hogar.' }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Texto del Botón</label>
                                            <input type="text" name="hero_boton" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'hero_boton')->first()?->valor ?? 'Comenzar Ahora' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Beneficios -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-heart me-2"></i>¿Por qué ser Chef Anfitrión?</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Título de Sección</label>
                                    <input type="text" name="beneficios_titulo" class="form-control" 
                                           value="{{ $contenidos->where('clave', 'beneficios_titulo')->first()?->valor ?? '¿Por qué ser Chef Anfitrión?' }}">
                                </div>

                                <div class="row">
                                    <!-- Beneficio 1 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Beneficio 1</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono/Emoji</label>
                                            <input type="text" name="beneficio1_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'beneficio1_icono')->first()?->valor ?? '💰' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="beneficio1_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'beneficio1_titulo')->first()?->valor ?? 'Gana dinero extra' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="beneficio1_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'beneficio1_descripcion')->first()?->valor ?? 'Los chefs anfitriones ganan en promedio $70.000–$170.000 por experiencia, dependiendo de la ciudad y demanda del mercado.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Beneficio 2 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Beneficio 2</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono/Emoji</label>
                                            <input type="text" name="beneficio2_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'beneficio2_icono')->first()?->valor ?? '🤝' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="beneficio2_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'beneficio2_titulo')->first()?->valor ?? 'Conoce gente nueva' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="beneficio2_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'beneficio2_descripcion')->first()?->valor ?? 'Conecta con personas de todo el mundo que comparten tu pasión por la gastronomía y crea vínculos únicos.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Beneficio 3 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Beneficio 3</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono/Emoji</label>
                                            <input type="text" name="beneficio3_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'beneficio3_icono')->first()?->valor ?? '⭐' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="beneficio3_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'beneficio3_titulo')->first()?->valor ?? 'Comparte tu talento' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="beneficio3_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'beneficio3_descripcion')->first()?->valor ?? 'Enseña tus recetas favoritas, técnicas culinarias únicas y transmite tu amor por la cocina a otros.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Beneficio 4 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Beneficio 4</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Icono/Emoji</label>
                                            <input type="text" name="beneficio4_icono" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'beneficio4_icono')->first()?->valor ?? '🛡️' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="beneficio4_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'beneficio4_titulo')->first()?->valor ?? 'Protección total' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="beneficio4_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'beneficio4_descripcion')->first()?->valor ?? 'Disfruta de soporte 24/7 y pólizas integrales que te respaldan durante cada experiencia gastronómica.' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Pasos -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-list-ol me-2"></i>Solicitud para Chef Anfitrión</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Título Principal</label>
                                        <input type="text" name="pasos_titulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'pasos_titulo')->first()?->valor ?? 'Solicitud para Chef Anfitrión' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subtítulo</label>
                                        <input type="text" name="pasos_subtitulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'pasos_subtitulo')->first()?->valor ?? 'Completa tu perfil en 4 pasos sencillos' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Paso 1 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 1</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="paso1_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso1_titulo')->first()?->valor ?? 'Información Personal' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="paso1_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'paso1_descripcion')->first()?->valor ?? 'Cuéntanos sobre ti, tu experiencia culinaria y tu pasión por la gastronomía.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Paso 2 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 2</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="paso2_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso2_titulo')->first()?->valor ?? 'Tu Espacio' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="paso2_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'paso2_descripcion')->first()?->valor ?? 'Describe tu cocina equipada y el ambiente acogedor para recibir a tus invitados.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Paso 3 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 3</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="paso3_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso3_titulo')->first()?->valor ?? 'Experiencia Culinaria' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="paso3_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'paso3_descripcion')->first()?->valor ?? 'Detalla tu menú especial y el tipo de experiencia gastronómica única que ofrecerás.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Paso 4 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 4</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="paso4_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'paso4_titulo')->first()?->valor ?? 'Precios y Disponibilidad' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="paso4_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'paso4_descripcion')->first()?->valor ?? 'Establece precios competitivos por persona y define tus horarios disponibles.' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección FAQ -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Preguntas Frecuentes</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Título de Sección</label>
                                    <input type="text" name="faq_titulo" class="form-control" 
                                           value="{{ $contenidos->where('clave', 'faq_titulo')->first()?->valor ?? 'Preguntas Frecuentes' }}">
                                </div>

                                <div class="row">
                                    <!-- FAQ 1 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Pregunta 1</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Pregunta</label>
                                            <input type="text" name="faq1_pregunta" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'faq1_pregunta')->first()?->valor ?? '¿Cuánto puedo ganar como chef anfitrión?' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Respuesta</label>
                                            <textarea name="faq1_respuesta" class="form-control" rows="3">{{ $contenidos->where('clave', 'faq1_respuesta')->first()?->valor ?? 'Los ingresos varían según tu ubicación, estrategia de precios y frecuencia de eventos. La mayoría de nuestros chefs ganan entre $70.000 y $170.000 por experiencia, con algunos superando estas cifras en ubicaciones premium.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- FAQ 2 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Pregunta 2</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Pregunta</label>
                                            <input type="text" name="faq2_pregunta" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'faq2_pregunta')->first()?->valor ?? '¿Qué requisitos necesito para comenzar?' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Respuesta</label>
                                            <textarea name="faq2_respuesta" class="form-control" rows="3">{{ $contenidos->where('clave', 'faq2_respuesta')->first()?->valor ?? 'Necesitas ser mayor de edad, contar con un espacio limpio y seguro, cumplir con normas básicas de higiene alimentaria, y completar nuestro proceso de verificación de identidad y antecedentes.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- FAQ 3 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Pregunta 3</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Pregunta</label>
                                            <input type="text" name="faq3_pregunta" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'faq3_pregunta')->first()?->valor ?? '¿Cómo manejo las reservas y pagos?' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Respuesta</label>
                                            <textarea name="faq3_respuesta" class="form-control" rows="3">{{ $contenidos->where('clave', 'faq3_respuesta')->first()?->valor ?? 'Todo se gestiona desde tu panel de control personalizado. Las reservas se confirman automáticamente y los pagos se procesan de forma segura, liquidándose según la política de pagos que establecemos juntos.' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- FAQ 4 -->
                                    <div class="col-md-6 mb-4">
                                        <h6>Pregunta 4</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Pregunta</label>
                                            <input type="text" name="faq4_pregunta" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'faq4_pregunta')->first()?->valor ?? '¿Qué incluye la protección ofrecida?' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Respuesta</label>
                                            <textarea name="faq4_respuesta" class="form-control" rows="3">{{ $contenidos->where('clave', 'faq4_respuesta')->first()?->valor ?? 'Ofrecemos cobertura integral para daños accidentales, asistencia 24/7 durante los eventos, y soporte completo para cualquier situación imprevista. Los detalles específicos se proporcionan al activar tu perfil de chef.' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección CTA Final -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Llamada a la Acción Final</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Texto del Botón Final</label>
                                    <input type="text" name="cta_boton_final" class="form-control" 
                                           value="{{ $contenidos->where('clave', 'cta_boton_final')->first()?->valor ?? 'Crear mi perfil de Chef' }}">
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

h6 {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
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