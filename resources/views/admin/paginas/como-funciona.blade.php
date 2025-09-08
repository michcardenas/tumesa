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
                            <h2>Editar Página: Cómo Funciona</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Editar Cómo Funciona</li>
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
                    <form action="{{ route('admin.paginas.como-funciona.update') }}" method="POST" enctype="multipart/form-data">
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
                                                   value="{{ $contenidos->where('clave', 'hero_titulo')->first()?->valor ?? 'Cómo Funciona TuMesa' }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="hero_descripcion" class="form-control" rows="3">{{ $contenidos->where('clave', 'hero_descripcion')->first()?->valor ?? 'Descubre el flujo completo: desde encontrar una experiencia hasta realizar la reserva y compartir tu comida con personas increíbles. Simple, seguro y pensado para ti.' }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Texto Botón 1</label>
                                                <input type="text" name="hero_boton1" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'hero_boton1')->first()?->valor ?? 'Ver Experiencias' }}">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Texto Botón 2</label>
                                                <input type="text" name="hero_boton2" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'hero_boton2')->first()?->valor ?? 'Ser Chef Anfitrión' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Tabs -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tabs me-2"></i>Sección Tabs</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="tabs_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'tabs_titulo')->first()?->valor ?? 'Elige tu camino' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Subtítulo</label>
                                            <input type="text" name="tabs_subtitulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'tabs_subtitulo')->first()?->valor ?? 'Te mostramos el proceso paso a paso.' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Tab 1</label>
                                                <input type="text" name="tab1_texto" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'tab1_texto')->first()?->valor ?? 'Para Invitados' }}">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Tab 2</label>
                                                <input type="text" name="tab2_texto" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'tab2_texto')->first()?->valor ?? 'Para Chefs Anfitriones' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Para Invitados -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Sección Para Invitados</h5>
                            </div>
                            <div class="card-body">
                                <!-- Pasos Invitados -->
                                <h6>Pasos para Invitados</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 1</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="guest_paso1_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'guest_paso1_titulo')->first()?->valor ?? 'Explora experiencias' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="guest_paso1_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'guest_paso1_descripcion')->first()?->valor ?? 'Filtra por ciudad, precio y fecha. Revisa fotos, menú y reseñas del chef.' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 2</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="guest_paso2_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'guest_paso2_titulo')->first()?->valor ?? 'Reserva tu lugar' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="guest_paso2_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'guest_paso2_descripcion')->first()?->valor ?? 'Elige cantidad de personas y confirma con un pago seguro.' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 3</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="guest_paso3_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'guest_paso3_titulo')->first()?->valor ?? 'Confirmación inmediata' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="guest_paso3_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'guest_paso3_descripcion')->first()?->valor ?? 'Recibes los detalles por email y tu panel. El chef se prepara para recibirte.' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 4</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="guest_paso4_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'guest_paso4_titulo')->first()?->valor ?? 'Disfruta y califica' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="guest_paso4_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'guest_paso4_descripcion')->first()?->valor ?? 'Vive la experiencia, deja una reseña y ayuda a otros a elegir.' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timeline Invitados -->
                                <h6>Timeline para Invitados</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Título Timeline</label>
                                        <input type="text" name="guest_timeline_titulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'guest_timeline_titulo')->first()?->valor ?? 'Tu recorrido como invitado' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subtítulo Timeline</label>
                                        <input type="text" name="guest_timeline_subtitulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'guest_timeline_subtitulo')->first()?->valor ?? 'Así se ve de principio a fin.' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    @for($i = 1; $i <= 5; $i++)
                                    <div class="col-md-4 mb-3">
                                        <h6>Timeline Item {{ $i }}</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="guest_timeline{{ $i }}_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'guest_timeline' . $i . '_titulo')->first()?->valor ?? 
                                                   ['Buscar', 'Seleccionar', 'Reservar', 'Asistir', 'Calificar'][$i-1] }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" name="guest_timeline{{ $i }}_descripcion" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'guest_timeline' . $i . '_descripcion')->first()?->valor ?? 
                                                   ['Usa filtros inteligentes', 'Lee menú y políticas', 'Pago seguro', 'Llega puntualmente', 'Comparte tu opinión'][$i-1] }}">
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Sección Para Chefs -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chef-hat me-2"></i>Sección Para Chefs</h5>
                            </div>
                            <div class="card-body">
                                <!-- Pasos Chefs -->
                                <h6>Pasos para Chefs</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 1</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="chef_paso1_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'chef_paso1_titulo')->first()?->valor ?? 'Crea tu perfil' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="chef_paso1_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'chef_paso1_descripcion')->first()?->valor ?? 'Completa información, fotos y tu especialidad culinaria.' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 2</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="chef_paso2_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'chef_paso2_titulo')->first()?->valor ?? 'Publica una experiencia' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="chef_paso2_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'chef_paso2_descripcion')->first()?->valor ?? 'Define menú, precio por persona, cupos y fecha/hora.' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 3</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="chef_paso3_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'chef_paso3_titulo')->first()?->valor ?? 'Gestiona reservas' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="chef_paso3_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'chef_paso3_descripcion')->first()?->valor ?? 'Confirma y organiza la logística desde tu panel.' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Paso 4</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="chef_paso4_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'chef_paso4_titulo')->first()?->valor ?? 'Recibe pagos' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="chef_paso4_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'chef_paso4_descripcion')->first()?->valor ?? 'Liquidez transparente según las políticas establecidas.' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timeline Chefs -->
                                <h6>Timeline para Chefs</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Título Timeline</label>
                                        <input type="text" name="chef_timeline_titulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'chef_timeline_titulo')->first()?->valor ?? 'Flujo para Anfitriones' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subtítulo Timeline</label>
                                        <input type="text" name="chef_timeline_subtitulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'chef_timeline_subtitulo')->first()?->valor ?? 'Publica, recibe invitados y crece tu comunidad.' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    @for($i = 1; $i <= 5; $i++)
                                    <div class="col-md-4 mb-3">
                                        <h6>Timeline Item {{ $i }}</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="chef_timeline{{ $i }}_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'chef_timeline' . $i . '_titulo')->first()?->valor ?? 
                                                   ['Perfil', 'Experiencia', 'Reservas', 'Evento', 'Liquidación'][$i-1] }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" name="chef_timeline{{ $i }}_descripcion" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'chef_timeline' . $i . '_descripcion')->first()?->valor ?? 
                                                   ['Identidad y verificación', 'Menú y disponibilidad', 'Notificaciones y control', 'Anfitriona con confianza', 'Ingresos en tu cuenta'][$i-1] }}">
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Sección Pagos y Seguridad -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Pagos y Seguridad</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Título</label>
                                        <input type="text" name="pagos_titulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'pagos_titulo')->first()?->valor ?? 'Pagos y Seguridad' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subtítulo</label>
                                        <input type="text" name="pagos_subtitulo" class="form-control" 
                                               value="{{ $contenidos->where('clave', 'pagos_subtitulo')->first()?->valor ?? 'Transparencia y soporte en cada paso.' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Card 1 -->
                                    <div class="col-md-4 mb-4">
                                        <h6>Tarjeta 1</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="pagos_card1_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'pagos_card1_titulo')->first()?->valor ?? 'Métodos de pago' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="pagos_card1_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'pagos_card1_descripcion')->first()?->valor ?? 'Operamos con pasarelas seguras. Tus datos se procesan de forma cifrada.' }}</textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 mb-2">
                                                <label class="form-label">Badge</label>
                                                <input type="text" name="pagos_card1_badge" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'pagos_card1_badge')->first()?->valor ?? 'Seguro' }}">
                                            </div>
                                            <div class="col-6 mb-2">
                                                <label class="form-label">Texto Badge</label>
                                                <input type="text" name="pagos_card1_badge_texto" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'pagos_card1_badge_texto')->first()?->valor ?? 'PCI-DSS / 3-D Secure' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card 2 -->
                                    <div class="col-md-4 mb-4">
                                        <h6>Tarjeta 2</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="pagos_card2_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'pagos_card2_titulo')->first()?->valor ?? 'Política de cancelación' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="pagos_card2_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'pagos_card2_descripcion')->first()?->valor ?? 'Las cancelaciones y reembolsos dependen de la política definida en cada experiencia.' }}</textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 mb-2">
                                                <label class="form-label">Badge</label>
                                                <input type="text" name="pagos_card2_badge" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'pagos_card2_badge')->first()?->valor ?? 'Claridad' }}">
                                            </div>
                                            <div class="col-6 mb-2">
                                                <label class="form-label">Texto Badge</label>
                                                <input type="text" name="pagos_card2_badge_texto" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'pagos_card2_badge_texto')->first()?->valor ?? 'Se muestra antes de pagar' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card 3 -->
                                    <div class="col-md-4 mb-4">
                                        <h6>Tarjeta 3</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="pagos_card3_titulo" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'pagos_card3_titulo')->first()?->valor ?? 'Protección y soporte' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="pagos_card3_descripcion" class="form-control" rows="2">{{ $contenidos->where('clave', 'pagos_card3_descripcion')->first()?->valor ?? 'Asistencia 24/7 y lineamientos de seguridad para invitados y anfitriones.' }}</textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 mb-2">
                                                <label class="form-label">Badge</label>
                                                <input type="text" name="pagos_card3_badge" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'pagos_card3_badge')->first()?->valor ?? '24/7' }}">
                                            </div>
                                            <div class="col-6 mb-2">
                                                <label class="form-label">Texto Badge</label>
                                                <input type="text" name="pagos_card3_badge_texto" class="form-control" 
                                                       value="{{ $contenidos->where('clave', 'pagos_card3_badge_texto')->first()?->valor ?? 'Acompañamiento continuo' }}">
                                            </div>
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
                                    @for($i = 1; $i <= 4; $i++)
                                    <div class="col-md-6 mb-4">
                                        <h6>Pregunta {{ $i }}</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Pregunta</label>
                                            <input type="text" name="faq{{ $i }}_pregunta" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'faq' . $i . '_pregunta')->first()?->valor ?? 
                                                   ['¿Puedo cambiar mi reserva?', '¿Cuándo se realiza el cobro?', '¿Qué pasa si el evento se cancela?', '¿Cómo reporto un problema?'][$i-1] }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Respuesta</label>
                                            <textarea name="faq{{ $i }}_respuesta" class="form-control" rows="3">{{ $contenidos->where('clave', 'faq' . $i . '_respuesta')->first()?->valor ?? 
                                            ['Sí, según disponibilidad del anfitrión y la política de cambios/cancelación de esa experiencia.', 
                                             'Al confirmar la reserva. La liquidación al anfitrión se efectúa según las condiciones acordadas.',
                                             'Te notificamos y aplicamos la política de reembolso o reprogramación correspondiente.',
                                             'Desde tu panel o vía soporte 24/7 indicando el ID de tu reserva y lo ocurrido.'][$i-1] }}</textarea>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Sección CTA Final -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Llamada a la Acción Final</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Texto Botón 1</label>
                                            <input type="text" name="cta_boton1" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'cta_boton1')->first()?->valor ?? 'Explorar experiencias' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Texto Botón 2</label>
                                            <input type="text" name="cta_boton2" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'cta_boton2')->first()?->valor ?? 'Quiero ser chef anfitrión' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Texto de Ayuda</label>
                                            <input type="text" name="cta_texto_ayuda" class="form-control" 
                                                   value="{{ $contenidos->where('clave', 'cta_texto_ayuda')->first()?->valor ?? '¿Necesitas ayuda adicional? Escríbenos y te acompañamos en el proceso.' }}">
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