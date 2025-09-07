@extends('layouts.app')

@section('content')

<div class="welcome-page">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            {!! $contenidos['hero_titulo']->valor ?? 'Descubre experiencias gastronómicas únicas en <span class="text-primary">hogares locales</span>' !!}
                        </h1>
                        <p class="hero-subtitle">
                            {{ $contenidos['hero_subtitulo']->valor ?? 'Conecta con chefs anfitriones apasionados y disfruta de cenas íntimas, auténticas y memorables en espacios privados únicos.' }}
                        </p>
                        <div class="hero-buttons">
                            <a href="#experiencias" class="btn btn-primary btn-lg me-3">
                                {{ $contenidos['hero_boton1']->valor ?? 'Explorar Experiencias' }}
                            </a>
                            <a href="#convertirse-chef" class="btn btn-outline-secondary btn-lg">
                                {{ $contenidos['hero_boton2']->valor ?? 'Convertirse en Chef' }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="{{ $contenidos['hero_imagen']->valor ?? 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80' }}" 
                             alt="Experiencia gastronómica en hogar" class="img-fluid rounded-4 shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Por qué elegir TuMesa Section -->
    <section id="por-que-elegir" class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">
                    {{ $contenidos['elegir_titulo']->valor ?? '¿Por qué elegir TuMesa?' }}
                </h2>
                <p class="section-subtitle">
                    {{ $contenidos['elegir_subtitulo']->valor ?? 'Descubre lo que hace especial cada experiencia gastronómica' }}
                </p>
            </div>
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="{{ $contenidos['feature1_icono']->valor ?? 'fas fa-utensils' }}"></i>
                        </div>
                        <h4>{{ $contenidos['feature1_titulo']->valor ?? 'Culinarias Auténticas' }}</h4>
                        <p>{{ $contenidos['feature1_descripcion']->valor ?? 'Experimenta sabores auténticos preparados por chefs locales apasionados con ingredientes frescos y recetas tradicionales.' }}</p>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="{{ $contenidos['feature2_icono']->valor ?? 'fas fa-users' }}"></i>
                        </div>
                        <h4>{{ $contenidos['feature2_titulo']->valor ?? 'Cocineros Todos' }}</h4>
                        <p>{{ $contenidos['feature2_descripcion']->valor ?? 'Conecta con una comunidad diversa de chefs anfitriones, cada uno con su propia historia y especialidad culinaria única.' }}</p>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="{{ $contenidos['feature3_icono']->valor ?? 'fas fa-shield-alt' }}"></i>
                        </div>
                        <h4>{{ $contenidos['feature3_titulo']->valor ?? 'Segura y Confiable' }}</h4>
                        <p>{{ $contenidos['feature3_descripcion']->valor ?? 'Todas nuestras experiencias están verificadas y nuestros chefs pasan por un proceso de selección riguroso para tu seguridad.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experiencias Destacadas - Esta sección mantiene la lógica de BD original -->
    <section id="experiencias" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Experiencias Destacadas</h2>
                <p class="section-subtitle">Las experiencias mejor valoradas por nuestros comensales</p>
            </div>
            
            @if($cenas_destacadas->count() > 0)
            <div class="row g-4">
                @foreach($cenas_destacadas->take(6) as $cena)
                <div class="col-md-6 col-lg-4">
                    <div class="experience-card">
                        @if($cena['cover_image_url'])
                            <img src="{{ $cena['cover_image_url'] }}" 
                                 alt="{{ $cena['title'] }}" 
                                 class="experience-image"
                                 loading="lazy">
                        @else
                            <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" 
                                 alt="{{ $cena['title'] }}" 
                                 class="experience-image"
                                 loading="lazy">
                        @endif
                        
                        <div class="experience-content">
                            <h5>{{ $cena['title'] }}</h5>
                            <p class="experience-chef">{{ $cena['chef_name'] }}</p>
                            
                            <div class="experience-description">
                                @php
                                    $menuText = strip_tags($cena['menu_preview']);
                                    $menuText = preg_replace('/\s+/', ' ', $menuText);
                                    $menuText = trim($menuText);
                                    
                                    if (empty($menuText)) {
                                        $menuText = 'Deliciosa experiencia culinaria preparada especialmente para ti.';
                                    }
                                    
                                    $preview = Str::limit($menuText, 100);
                                @endphp
                                
                                <p class="menu-preview-text">{{ $preview }}</p>
                            </div>
                            
                            <div class="experience-meta mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $cena['formatted_date'] }}
                                </small>
                                <small class="text-muted ms-3">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ Str::limit($cena['location'], 20) }}
                                </small>
                            </div>
                            
                            <div class="experience-availability mb-2">
                                @if($cena['available_spots'] > 0)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ $cena['available_spots'] }} espacios disponibles
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-hourglass-half me-1"></i>
                                        Lista de espera
                                    </span>
                                @endif
                            </div>
                            
                            <div class="experience-footer">
                                <span class="experience-price">{{ $cena['formatted_price'] }}/persona</span>
                                @if($cena['available_spots'] > 0)
                                    <a href="{{ route('cenas.show', $cena['id']) }}" class="btn btn-dark btn-sm">
                                        Reservar
                                    </a>
                                @else
                                    <a href="{{ route('cenas.show', $cena['id']) }}" class="btn btn-outline-dark btn-sm">
                                        Ver Detalles
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="row">
                <div class="col-12 text-center">
                    <div class="empty-state py-5">
                        <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Próximamente nuevas experiencias</h4>
                        <p class="text-muted">Nuestros chefs están preparando increíbles experiencias culinarias para ti.</p>
                        <a href="{{ route('ser-chef') ?? '#' }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>Únete como Chef
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="text-center mt-4">
                <a href="{{ route('experiencias') ?? '#' }}" class="btn btn-link">
                    Ver Todas las Experiencias
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Como Funciona Section -->
    <section id="como-funciona" class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">
                    {{ $contenidos['funciona_titulo']->valor ?? 'Cómo Funciona' }}
                </h2>
                <p class="section-subtitle">
                    {{ $contenidos['funciona_subtitulo']->valor ?? 'Cuatro simples pasos para vivir una experiencia única' }}
                </p>
            </div>
            <div class="row g-4">
                <!-- Paso 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">1</span>
                            <i class="{{ $contenidos['paso1_icono']->valor ?? 'fas fa-search' }}"></i>
                        </div>
                        <h5>{{ $contenidos['paso1_titulo']->valor ?? 'Explora' }}</h5>
                        <p>{{ $contenidos['paso1_descripcion']->valor ?? 'Navega por cientos de experiencias gastronómicas únicas cerca de ti y encuentra la perfecta para tu ocasión.' }}</p>
                    </div>
                </div>
                <!-- Paso 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">2</span>
                            <i class="{{ $contenidos['paso2_icono']->valor ?? 'fas fa-calendar-check' }}"></i>
                        </div>
                        <h5>{{ $contenidos['paso2_titulo']->valor ?? 'Reserva' }}</h5>
                        <p>{{ $contenidos['paso2_descripcion']->valor ?? 'Selecciona la fecha y hora que mejor te convenga y confirma tu reserva de forma segura en nuestra plataforma.' }}</p>
                    </div>
                </div>
                <!-- Paso 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">3</span>
                            <i class="{{ $contenidos['paso3_icono']->valor ?? 'fas fa-utensils' }}"></i>
                        </div>
                        <h5>{{ $contenidos['paso3_titulo']->valor ?? 'Disfruta' }}</h5>
                        <p>{{ $contenidos['paso3_descripcion']->valor ?? 'Vive una experiencia gastronómica inolvidable y conecta con otros amantes de la buena comida y tu chef anfitrión.' }}</p>
                    </div>
                </div>
                <!-- Paso 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">4</span>
                            <i class="{{ $contenidos['paso4_icono']->valor ?? 'fas fa-heart' }}"></i>
                        </div>
                        <h5>{{ $contenidos['paso4_titulo']->valor ?? 'Comparte' }}</h5>
                        <p>{{ $contenidos['paso4_descripcion']->valor ?? 'Deja tu reseña y comparte tu experiencia para ayudar a otros comensales a descubrir nuevos sabores.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 cta-section">
        <div class="container">
            <div class="cta-card text-center">
                <h2 class="text-white mb-3">
                    {{ $contenidos['cta_titulo']->valor ?? '¿Listo para tu próxima aventura gastronómica?' }}
                </h2>
                <p class="text-white lead mb-4">
                    {{ $contenidos['cta_descripcion']->valor ?? 'Únete a miles de comensales que ya han descubierto sabores únicos' }}
                </p>
                <div class="cta-buttons">
                    <a href="{{ route('register') ?? '#' }}" class="btn btn-light btn-lg me-3">
                        {{ $contenidos['cta_boton1']->valor ?? 'Crear mi cuenta' }}
                    </a>
                    <a href="#experiencias" class="btn btn-outline-light btn-lg">
                        {{ $contenidos['cta_boton2']->valor ?? 'Explorar ahora' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Hero Section */
.hero-section {
    background-color: #f8fafc;
    padding: 2rem 0;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: #1e293b;
}

.hero-subtitle {
    font-size: 1.125rem;
    color: #64748b;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.hero-buttons .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 0.5rem;
}

.btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.btn-outline-secondary {
    color: #64748b;
    border-color: #d1d5db;
}

.btn-outline-secondary:hover {
    background-color: #64748b;
    border-color: #64748b;
    color: white;
}

.hero-image {
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-image img {
    max-width: 100%;
    height: auto;
}

/* Sections */
.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
}

/* Feature Cards */
.feature-card {
    padding: 2rem 1rem;
    height: 100%;
}

.feature-icon {
    width: 80px;
    height: 80px;
    background-color: #dbeafe;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.feature-icon i {
    font-size: 2rem;
    color: #3b82f6;
}

.feature-card h4 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #1e293b;
}

.feature-card p {
    color: #64748b;
    line-height: 1.5;
}

/* Experience Cards */
.experience-card {
    background: white;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.experience-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.experience-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.experience-content {
    padding: 1.5rem;
}

.experience-rating {
    background: #fef3c7;
    color: #d97706;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
}

.experience-rating i {
    margin-right: 0.25rem;
}

.experience-card h5 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #1e293b;
}

.experience-chef {
    color: #64748b;
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
}

.experience-description {
    color: #64748b;
    margin-bottom: 1rem;
    line-height: 1.5;
    font-size: 0.875rem;
}

.experience-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.experience-price {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
}

/* Step Cards */
.step-card {
    padding: 2rem 1rem;
    height: 100%;
}

.step-icon {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
}

.step-number {
    position: absolute;
    top: -15px;
    left: -15px;
    background: #3b82f6;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
    z-index: 1;
}

.step-icon i {
    font-size: 2.5rem;
    color: #64748b;
}

.step-card h5 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #1e293b;
}

.step-card p {
    color: #64748b;
    line-height: 1.5;
    font-size: 0.875rem;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
}

.cta-card {
    padding: 3rem 2rem;
}

.cta-card h2 {
    font-size: 2rem;
    font-weight: 700;
}

.cta-buttons .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .section-title {
        font-size: 1.75rem;
    }
    
    .hero-buttons .btn,
    .cta-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .hero-buttons .btn:last-child,
    .cta-buttons .btn:last-child {
        margin-bottom: 0;
    }
    
    .hero-image img {
        margin-top: 2rem;
    }
    
    .cta-card {
        padding: 2rem 1rem;
    }
    
    .cta-card h2 {
        font-size: 1.5rem;
    }
}

.btn-link {
    color: #3b82f6;
    text-decoration: none;
}

.btn-link:hover {
    color: #1e40af;
    text-decoration: underline;
}
.experience-description {
    margin-bottom: 1rem;
}

.menu-preview-text {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.4;
    margin-bottom: 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Mejorar las tarjetas */
.experience-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.experience-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.experience-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.experience-content {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.experience-content h5 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.experience-chef {
    color: #059669;
    font-weight: 500;
    font-size: 14px;
    margin-bottom: 0.75rem;
}

.experience-meta {
    margin-top: auto;
}

.experience-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.experience-price {
    font-weight: 600;
    color: #1f2937;
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .experience-content {
        padding: 1rem;
    }
    
    .menu-preview-text {
        font-size: 13px;
    }
    
    .experience-content h5 {
        font-size: 1.1rem;
    }
}
</style>
@endsection