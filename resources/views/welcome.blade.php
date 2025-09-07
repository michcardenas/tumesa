@extends('layouts.app')

@section('content')

<div class="welcome-page">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Descubre experiencias gastronómicas únicas en <span class="text-primary">hogares locales</span></h1>
                        <p class="hero-subtitle">Conecta con chefs anfitriones apasionados y disfruta de cenas íntimas, auténticas y memorables en espacios privados únicos.</p>
                        <div class="hero-buttons">
                            <a href="#experiencias" class="btn btn-primary btn-lg me-3">Explorar Experiencias</a>
                            <a href="#convertirse-chef" class="btn btn-outline-secondary btn-lg">Convertirse en Chef</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" 
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
                <h2 class="section-title">¿Por qué elegir TuMesa?</h2>
                <p class="section-subtitle">Descubre lo que hace especial cada experiencia gastronómica</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4>Culinarias Auténticas</h4>
                        <p>Experimenta sabores auténticos preparados por chefs locales apasionados con ingredientes frescos y recetas tradicionales.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Cocineros Todos</h4>
                        <p>Conecta con una comunidad diversa de chefs anfitriones, cada uno con su propia historia y especialidad culinaria única.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Segura y Confiable</h4>
                        <p>Todas nuestras experiencias están verificadas y nuestros chefs pasan por un proceso de selección riguroso para tu seguridad.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

  <!-- Experiencias Destacadas -->
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
                        
                        {{-- CAMBIO PRINCIPAL: Preview limpio del menú --}}
                        <div class="experience-description">
                            @php
                                // Limpiar HTML y crear preview
                                $menuText = strip_tags($cena['menu_preview']);
                                $menuText = preg_replace('/\s+/', ' ', $menuText); // Limpiar espacios extra
                                $menuText = trim($menuText);
                                
                                // Si está vacío, usar un texto por defecto
                                if (empty($menuText)) {
                                    $menuText = 'Deliciosa experiencia culinaria preparada especialmente para ti.';
                                }
                                
                                // Limitar a 100 caracteres para las tarjetas
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
        <!-- Estado vacío cuando no hay cenas -->
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
                <h2 class="section-title">Cómo Funciona</h2>
                <p class="section-subtitle">Cuatro simples pasos para vivir una experiencia única</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">1</span>
                            <i class="fas fa-search"></i>
                        </div>
                        <h5>Explora</h5>
                        <p>Navega por cientos de experiencias gastronómicas únicas cerca de ti y encuentra la perfecta para tu ocasión.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">2</span>
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h5>Reserva</h5>
                        <p>Selecciona la fecha y hora que mejor te convenga y confirma tu reserva de forma segura en nuestra plataforma.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">3</span>
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h5>Disfruta</h5>
                        <p>Vive una experiencia gastronómica inolvidable y conecta con otros amantes de la buena comida y tu chef anfitrión.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">4</span>
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5>Comparte</h5>
                        <p>Deja tu reseña y comparte tu experiencia para ayudar a otros comensales a descubrir nuevos sabores.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 cta-section">
        <div class="container">
            <div class="cta-card text-center">
                <h2 class="text-white mb-3">¿Listo para tu próxima aventura gastronómica?</h2>
                <p class="text-white lead mb-4">Únete a miles de comensales que ya han descubierto sabores únicos</p>
                <div class="cta-buttons">
                    <a href="{{ route('register') ?? '#' }}" class="btn btn-light btn-lg me-3">Crear mi cuenta</a>
                    <a href="#experiencias" class="btn btn-outline-light btn-lg">Explorar ahora</a>
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
</style>
@endsection