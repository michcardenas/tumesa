@extends('layouts.app')

@section('title', $cenaData['title'])

@section('content')
<div class="container-fluid p-0">
    <!-- Hero Section -->
    <div class="hero-section">
        @if($cenaData['cover_image_url'])
            <img src="{{ $cenaData['cover_image_url'] }}" alt="{{ $cenaData['title'] }}" class="hero-image">
        @else
            <div class="hero-placeholder">
                <div class="hero-pattern"></div>
                <i class="fas fa-utensils fa-4x"></i>
            </div>
        @endif
        <div class="hero-overlay">
            <div class="container">
                <div class="row align-items-center min-vh-70">
                    <div class="col-lg-7">
                        <div class="hero-content">
                            <div class="hero-badges mb-4">
                                <span class="hero-badge primary">
                                    <i class="fas fa-star me-2"></i>Experiencia Premium
                                </span>
                                <span class="hero-badge {{ $cenaData['is_past'] ? 'danger' : 'success' }}">
                                    <i class="fas fa-{{ $cenaData['is_past'] ? 'clock' : 'calendar-check' }} me-2"></i>
                                    {{ $cenaData['is_past'] ? 'Evento Finalizado' : 'Próximo Evento' }}
                                </span>
                            </div>
                            <h1 class="hero-title">{{ $cenaData['title'] }}</h1>
                            <div class="hero-details">
                                <div class="hero-detail">
                                    <div class="detail-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="detail-text">
                                        <span class="detail-label">Fecha</span>
                                        <span class="detail-value">{{ $cenaData['formatted_date'] }}</span>
                                    </div>
                                </div>
                                <div class="hero-detail">
                                    <div class="detail-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="detail-text">
                                        <span class="detail-label">Hora</span>
                                        <span class="detail-value">{{ $cenaData['formatted_time'] }}</span>
                                    </div>
                                </div>
                                <div class="hero-detail">
                                    <div class="detail-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="detail-text">
                                        <span class="detail-label">Ubicación</span>
                                        <span class="detail-value">{{ $cenaData['location'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="price-card glass-effect">
                            <div class="price-header">
                                <div class="price-badge">
                                    <i class="fas fa-crown"></i>
                                    <span>Experiencia Exclusiva</span>
                                </div>
                                <div class="price">{{ $cenaData['formatted_price'] }}</div>
                                <div class="per-person">por persona</div>
                            </div>
                            
                            @if($cenaData['can_book'])
                                <form action="{{ route('reservar') }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-reserve btn-lg w-100 elegant-btn" onclick="showReserveModal()">
                                        <span class="btn-shine"></span>
                                        <i class="fas fa-calendar-plus me-2"></i>
                                        <span>Reservar Experiencia</span>
                                    </button>
                                </form>
                                <div class="availability-info glass-light">
                                    <div class="availability-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="availability-text">
                                        <span class="availability-number">{{ $cenaData['available_spots'] }}</span>
                                        <span class="availability-label">espacios disponibles</span>
                                    </div>
                                </div>
                            @else
                                <button class="btn btn-unavailable btn-lg w-100 elegant-btn" disabled>
                                    <i class="fas fa-{{ $cenaData['is_past'] ? 'clock' : 'ban' }} me-2"></i>
                                    {{ $cenaData['is_past'] ? 'Evento Finalizado' : 'Sin Disponibilidad' }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row g-5">
            <!-- Content -->
            <div class="col-lg-8">
                <!-- Menú Section -->
                <section class="content-card elegant-card animate-on-scroll">
                    <div class="section-header">
                        <div class="section-icon elegant-icon">
                            <i class="fas fa-utensils"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="section-title">
                            <h3>Menú Gastronómico</h3>
                            <p class="section-subtitle">Una experiencia culinaria cuidadosamente diseñada</p>
                        </div>
                    </div>
                    <div class="menu-content elegant-content">
                        <div class="menu-decoration">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <p class="menu-text">{{ $cenaData['menu'] }}</p>
                    </div>
                </section>

                <!-- Location & Map Section -->
                @if($cenaData['latitude'] && $cenaData['longitude'])
                <section class="content-card elegant-card animate-on-scroll">
                    <div class="section-header">
                        <div class="section-icon elegant-icon location-icon">
                            <i class="fas fa-map-marked-alt"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="section-title">
                            <h3>Ubicación del Evento</h3>
                            <p class="section-subtitle">{{ $cenaData['location'] }}</p>
                        </div>
                    </div>
                    <div class="map-container elegant-content">
                        <div id="map" class="elegant-map"></div>
                        <div class="map-overlay glass-light">
                            <div class="location-info">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span>{{ $cenaData['location'] }}</span>
                            </div>
                            <button class="btn btn-sm btn-directions" onclick="openDirections()">
                                <i class="fas fa-directions me-1"></i>Cómo llegar
                            </button>
                        </div>
                    </div>
                </section>
                @endif

                <!-- Detalles Section -->
                <section class="content-card elegant-card animate-on-scroll">
                    <div class="section-header">
                        <div class="section-icon elegant-icon">
                            <i class="fas fa-info-circle"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="section-title">
                            <h3>Detalles de la Experiencia</h3>
                            <p class="section-subtitle">Información completa del evento</p>
                        </div>
                    </div>
                    <div class="details-grid">
                        <div class="detail-card elegant-detail glass-light">
                            <div class="detail-icon calendar">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Fecha y Hora</div>
                                <div class="detail-value">{{ $cenaData['formatted_datetime'] }}</div>
                                <div class="detail-extra">{{ \Carbon\Carbon::parse($cenaData['datetime'])->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="detail-card elegant-detail glass-light">
                            <div class="detail-icon guests">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Comensales</div>
                                <div class="detail-value">{{ $cenaData['guests_current'] }}/{{ $cenaData['guests_max'] }} personas</div>
                                <div class="detail-extra">
                                    @if($cenaData['available_spots'] > 0)
                                        {{ $cenaData['available_spots'] }} espacios libres
                                    @else
                                        Evento completo
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="detail-card elegant-detail glass-light">
                            <div class="detail-icon location">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Ubicación</div>
                                <div class="detail-value">{{ $cenaData['location'] }}</div>
                                <div class="detail-extra">Evento privado</div>
                            </div>
                        </div>
                        <div class="detail-card elegant-detail glass-light">
                            <div class="detail-icon price">
                                <i class="fas fa-gem"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Inversión</div>
                                <div class="detail-value">{{ $cenaData['formatted_price'] }}</div>
                                <div class="detail-extra">Por persona</div>
                            </div>
                        </div>
                    </div>
                </section>

                @if($cenaData['gallery_image_urls'] && count($cenaData['gallery_image_urls']) > 0)
                <!-- Gallery Section -->
                <section class="content-card elegant-card animate-on-scroll">
                    <div class="section-header">
                        <div class="section-icon elegant-icon">
                            <i class="fas fa-images"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="section-title">
                            <h3>Galería Visual</h3>
                            <p class="section-subtitle">Momentos de experiencias anteriores</p>
                        </div>
                    </div>
                    <div class="gallery-grid elegant-gallery">
                        @foreach($cenaData['gallery_image_urls'] as $index => $image)
                            <div class="gallery-item" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                <img src="{{ $image }}" alt="Galería {{ $index + 1 }}" class="gallery-image">
                                <div class="gallery-overlay">
                                    <i class="fas fa-expand-alt"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Chef Info -->
                <div class="chef-card elegant-card glass-effect animate-on-scroll">
                    <div class="chef-header">
                        <div class="chef-avatar-container">
                            <div class="chef-avatar elegant-avatar">
                                @if($cena->user->avatar_url)
                                    <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}" class="chef-image">
                                @else
                                    <div class="chef-placeholder elegant-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                <div class="avatar-ring"></div>
                            </div>
                            <div class="chef-badge">
                                <i class="fas fa-star"></i>
                                <span>Chef</span>
                            </div>
                        </div>
                        <div class="chef-info">
                            <h4 class="chef-name">{{ $cena->user->name ?? 'Chef Anónimo' }}</h4>
                            @if($cena->user->especialidad)
                                <p class="chef-specialty">{{ $cena->user->especialidad }}</p>
                            @else
                                <p class="chef-specialty">Chef Anfitrión</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($cena->user->rating > 0)
                    <div class="chef-rating elegant-rating">
                        <div class="rating-container">
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($cena->user->rating))
                                        <i class="fas fa-star"></i>
                                    @elseif($i - 0.5 <= $cena->user->rating)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="rating-details">
                                <span class="rating-value">{{ $cena->user->formatted_rating ?? number_format($cena->user->rating, 1) }}</span>
                                <span class="rating-label">de 5 estrellas</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($cena->user->experiencia_anos)
                        <div class="chef-experience elegant-experience">
                            <div class="experience-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="experience-text">
                                <span class="experience-number">{{ $cena->user->experiencia_anos }}</span>
                                <span class="experience-label">{{ $cena->user->experiencia_anos == 1 ? 'año' : 'años' }} de experiencia</span>
                            </div>
                        </div>
                    @endif
                    
                    <div class="chef-bio elegant-bio">
                        @if($cena->user->bio)
                            <p>{{ Str::limit($cena->user->bio, 120) }}</p>
                        @else
                            <p>Especialista en crear experiencias culinarias únicas que conectan a las personas a través de la comida y la conversación auténtica.</p>
                        @endif
                    </div>
                    
                    @if($cena->user->instagram || $cena->user->facebook || $cena->user->website)
                        <div class="chef-social elegant-social">
                            <div class="social-label">Sígueme en:</div>
                            <div class="social-links">
                                @if($cena->user->instagram)
                                    <a href="{{ $cena->user->instagram_url ?? '#' }}" target="_blank" class="social-link elegant-social-link instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                @endif
                                @if($cena->user->facebook)
                                    <a href="{{ $cena->user->facebook_url ?? '#' }}" target="_blank" class="social-link elegant-social-link facebook">
                                        <i class="fab fa-facebook"></i>
                                    </a>
                                @endif
                                @if($cena->user->website)
                                    <a href="{{ $cena->user->website }}" target="_blank" class="social-link elegant-social-link website">
                                        <i class="fas fa-globe"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <button class="btn btn-chef-profile w-100 elegant-btn" onclick="showChefModal()">
                        <span class="btn-shine"></span>
                        <i class="fas fa-user-circle me-2"></i>
                        <span>Conocer al Chef</span>
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="stats-card elegant-card glass-light animate-on-scroll">
                    <div class="stats-header">
                        <h5><i class="fas fa-chart-line me-2"></i>Información Rápida</h5>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-item elegant-stat">
                            <div class="stat-icon price-stat">
                                <i class="fas fa-gem"></i>
                                <div class="stat-glow"></div>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $cenaData['formatted_price'] }}</div>
                                <div class="stat-label">Inversión</div>
                            </div>
                        </div>
                        <div class="stat-item elegant-stat">
                            <div class="stat-icon guests-stat">
                                <i class="fas fa-users"></i>
                                <div class="stat-glow"></div>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $cenaData['guests_max'] }}</div>
                                <div class="stat-label">Max. Invitados</div>
                            </div>
                        </div>
                        <div class="stat-item elegant-stat">
                            <div class="stat-icon time-stat">
                                <i class="fas fa-clock"></i>
                                <div class="stat-glow"></div>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ abs($cenaData['days_until']) }}</div>
                                <div class="stat-label">{{ $cenaData['days_until'] >= 0 ? 'Días restantes' : 'Días transcurridos' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Modal de Reserva -->
<div class="modal fade" id="reserveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content elegant-modal glass-effect">
            <div class="modal-header elegant-modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Reservar {{ $cenaData['title'] }}
                </h5>
                <button type="button" class="btn-close elegant-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="construction-animation">
                    <div class="construction-icon">
                        <i class="fas fa-tools fa-4x"></i>
                    </div>
                    <div class="construction-particles">
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                    </div>
                </div>
                <h4 class="mt-4 mb-3">Sistema en Desarrollo</h4>
                <p class="text-muted mb-4">Estamos perfeccionando la experiencia de reserva para ofrecerte el mejor servicio. ¡Muy pronto estará disponible!</p>
                <div class="coming-soon-features">
                    <div class="feature-item glass-light">
                        <div class="feature-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span>Reservas instantáneas</span>
                    </div>
                    <div class="feature-item glass-light">
                        <div class="feature-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span>Confirmación automática</span>
                    </div>
                    <div class="feature-item glass-light">
                        <div class="feature-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span>Pagos seguros</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer elegant-modal-footer justify-content-center">
                <button type="button" class="btn btn-primary elegant-btn" data-bs-dismiss="modal">
                    <span class="btn-shine"></span>
                    <i class="fas fa-bell me-2"></i>
                    <span>Notificarme</span>
                </button>
                <button type="button" class="btn btn-outline-secondary elegant-btn-outline" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal del Chef -->
<div class="modal fade" id="chefModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content elegant-modal glass-effect">
            <div class="modal-header elegant-modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-circle me-2"></i>
                    Conoce a {{ $cena->user->name ?? 'nuestro Chef' }}
                </h5>
                <button type="button" class="btn-close elegant-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="chef-profile-header text-center mb-4">
                    <div class="chef-modal-avatar-container">
                        <div class="chef-modal-avatar elegant-avatar">
                            @if($cena->user->avatar_url)
                                <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}" class="chef-modal-image">
                            @else
                                <div class="chef-modal-placeholder elegant-placeholder">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                            @endif
                            <div class="avatar-ring"></div>
                        </div>
                        <div class="chef-modal-badge">
                            <i class="fas fa-crown"></i>
                            <span>Chef Profesional</span>
                        </div>
                    </div>
                    <h4 class="chef-modal-name">{{ $cena->user->name ?? 'Chef Anónimo' }}</h4>
                    @if($cena->user->especialidad)
                        <p class="chef-modal-specialty">{{ $cena->user->especialidad }}</p>
                    @endif
                    
                    @if($cena->user->rating > 0)
                        <div class="rating-display elegant-rating mb-3">
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($cena->user->rating))
                                        <i class="fas fa-star"></i>
                                    @elseif($i - 0.5 <= $cena->user->rating)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="rating-text">{{ $cena->user->formatted_rating ?? number_format($cena->user->rating, 1) }} de 5 estrellas</span>
                        </div>
                    @endif
                </div>
                
                @if($cena->user->bio)
                    <div class="chef-full-bio elegant-content mb-4">
                        <div class="content-header">
                            <i class="fas fa-quote-left me-2"></i>
                            <h6>Filosofía Culinaria</h6>
                        </div>
                        <p class="bio-text">{{ $cena->user->bio }}</p>
                    </div>
                @endif
                
                @if($cena->user->experiencia_anos)
                    <div class="chef-stats elegant-content mb-4">
                        <div class="content-header">
                            <i class="fas fa-award me-2"></i>
                            <h6>Experiencia</h6>
                        </div>
                        <div class="stats-row">
                            <div class="stat-badge">
                                <span class="stat-number">{{ $cena->user->experiencia_anos }}</span>
                                <span class="stat-label">{{ $cena->user->experiencia_anos == 1 ? 'Año' : 'Años' }}</span>
                            </div>
                            <p class="stat-description">de dedicación a la alta gastronomía</p>
                        </div>
                    </div>
                @endif
                
                <div class="chef-specialties elegant-content mb-4">
                    <div class="content-header">
                        <i class="fas fa-utensils me-2"></i>
                        <h6>Especialidades</h6>
                    </div>
                    <div class="specialties-list">
                        @if($cena->user->especialidad)
                            <span class="specialty-tag elegant-tag primary">{{ $cena->user->especialidad }}</span>
                        @endif
                        <span class="specialty-tag elegant-tag">Cocina de Autor</span>
                        <span class="specialty-tag elegant-tag">Experiencias Íntimas</span>
                        <span class="specialty-tag elegant-tag">Ingredientes Locales</span>
                        <span class="specialty-tag elegant-tag">Maridajes Únicos</span>
                    </div>
                </div>
                
                @if($cena->user->instagram || $cena->user->facebook || $cena->user->website)
                    <div class="chef-social-full elegant-content text-center">
                        <div class="content-header">
                            <i class="fas fa-share-alt me-2"></i>
                            <h6>Conecta Conmigo</h6>
                        </div>
                        <div class="social-links-modal">
                            @if($cena->user->instagram)
                                <a href="{{ $cena->user->instagram_url ?? '#' }}" target="_blank" class="social-link-modal elegant-social-modal instagram">
                                    <div class="social-icon">
                                        <i class="fab fa-instagram"></i>
                                    </div>
                                    <span>Instagram</span>
                                </a>
                            @endif
                            @if($cena->user->facebook)
                                <a href="{{ $cena->user->facebook_url ?? '#' }}" target="_blank" class="social-link-modal elegant-social-modal facebook">
                                    <div class="social-icon">
                                        <i class="fab fa-facebook"></i>
                                    </div>
                                    <span>Facebook</span>
                                </a>
                            @endif
                            @if($cena->user->website)
                                <a href="{{ $cena->user->website }}" target="_blank" class="social-link-modal elegant-social-modal website">
                                    <div class="social-icon">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <span>Sitio Web</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --gold-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    --elegant-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    
    --primary-color: #667eea;
    --primary-dark: #5a67d8;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --gold-color: #f6d365;
    --text-primary: #1a202c;
    --text-secondary: #4a5568;
    --text-muted: #718096;
    
    --glass-bg: rgba(255, 255, 255, 0.25);
    --glass-border: rgba(255, 255, 255, 0.18);
    --shadow-elegant: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    --shadow-hover: 0 15px 35px rgba(31, 38, 135, 0.2);
    --border-radius: 16px;
    --border-radius-lg: 24px;
}

* {
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--text-primary);
}

/* Glass Effect Classes */
.glass-effect {
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border);
    box-shadow: var(--shadow-elegant);
}

.glass-light {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Hero Section */
.hero-section {
    position: relative;
    height: 80vh;
    min-height: 600px;
    overflow: hidden;
    display: flex;
    align-items: center;
}

.hero-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
}

.hero-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    z-index: 1;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 1px, transparent 1px),
        radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 50px 50px;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        rgba(0,0,0,0.7) 0%, 
        rgba(0,0,0,0.5) 50%, 
        rgba(0,0,0,0.6) 100%);
    z-index: 2;
    display: flex;
    align-items: center;
}

.hero-content {
    z-index: 3;
    position: relative;
}

.hero-badges {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hero-badge.primary {
    background: var(--gold-gradient);
    box-shadow: 0 4px 15px rgba(246, 211, 101, 0.3);
}

.hero-badge.success {
    background: var(--success-gradient);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.hero-badge.danger {
    background: var(--secondary-gradient);
    box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
}

.hero-title {
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 800;
    color: white;
    text-shadow: 0 4px 8px rgba(0,0,0,0.4);
    margin: 2rem 0;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.hero-details {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.hero-detail {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius: var(--border-radius);
    border: 1px solid rgba(255,255,255,0.2);
}

.detail-icon {
    width: 50px;
    height: 50px;
    background: var(--gold-gradient);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-right: 1rem;
    box-shadow: 0 4px 15px rgba(246, 211, 101, 0.3);
}

.detail-text {
    flex: 1;
}

.detail-label {
    display: block;
    color: rgba(255,255,255,0.8);
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.detail-value {
    display: block;
    color: white;
    font-size: 1.1rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

/* Price Card */
.price-card {
    padding: 3rem 2.5rem;
    border-radius: var(--border-radius-lg);
    position: relative;
    overflow: hidden;
}

.price-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
    opacity: 0;
}

.price-card:hover::before {
    animation: shimmer 2s ease-in-out infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
}

.price-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: var(--gold-gradient);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(246, 211, 101, 0.3);
}

.price-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.price {
    font-size: 3.5rem;
    font-weight: 900;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin: 1rem 0 0.5rem 0;
    filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.3));
}

.per-person {
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 1.1rem;
}

/* Elegant Buttons */
.elegant-btn {
    position: relative;
    overflow: hidden;
    border-radius: var(--border-radius);
    padding: 1.25rem 2rem;
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    border: none;
    background: var(--success-gradient);
    color: white;
}

.elegant-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
    color: white;
}

.btn-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.elegant-btn:hover .btn-shine {
    left: 100%;
}

.btn-unavailable {
    background: var(--dark-gradient);
    color: white;
    border: none;
    padding: 1.25rem 2rem;
    border-radius: var(--border-radius);
    font-weight: 600;
}

.elegant-btn-outline {
    background: transparent;
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    padding: 1rem 2rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    transition: all 0.3s ease;
}

.elegant-btn-outline:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-1px);
}

.availability-info {
    margin-top: 2rem;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.availability-icon {
    width: 40px;
    height: 40px;
    background: var(--success-gradient);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.availability-text {
    flex: 1;
}

.availability-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--success-color);
    line-height: 1;
}

.availability-label {
    display: block;
    color: var(--text-secondary);
    font-size: 0.9rem;
    font-weight: 500;
}

/* Content Cards */
.elegant-card {
    background: white;
    padding: 3rem;
    border-radius: var(--border-radius-lg);
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.elegant-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.elegant-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

.section-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 2.5rem;
}

.elegant-icon {
    width: 70px;
    height: 70px;
    background: var(--primary-gradient);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    margin-right: 1.5rem;
    position: relative;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.elegant-icon.location-icon {
    background: var(--success-gradient);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
}

.icon-glow {
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    background: inherit;
    border-radius: inherit;
    filter: blur(15px);
    opacity: 0.6;
    z-index: -1;
}

.section-title h3 {
    color: var(--text-primary);
    font-weight: 800;
    font-size: 1.75rem;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.02em;
}

.section-subtitle {
    color: var(--text-muted);
    font-size: 1rem;
    font-weight: 500;
    margin: 0;
}

.elegant-content {
    position: relative;
    padding: 2rem;
    background: linear-gradient(145deg, #f8fafc, #f1f5f9);
    border-radius: var(--border-radius);
    border: 1px solid rgba(0,0,0,0.05);
}

.menu-content {
    border-left: 4px solid;
    border-image: var(--primary-gradient) 1;
}

.menu-decoration {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    background: var(--elegant-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--success-color);
    font-size: 1.2rem;
}

.menu-text {
    font-size: 1.2rem;
    line-height: 1.8;
    color: var(--text-primary);
    margin: 0;
    font-weight: 500;
}

/* Map Styles */
.map-container {
    position: relative;
    height: 400px;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.elegant-map {
    width: 100%;
    height: 100%;
    border-radius: var(--border-radius);
}

.map-overlay {
    position: absolute;
    bottom: 1rem;
    left: 1rem;
    right: 1rem;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1000;
}

.location-info {
    display: flex;
    align-items: center;
    color: var(--text-primary);
    font-weight: 600;
    flex: 1;
}

.btn-directions {
    background: var(--primary-gradient);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.btn-directions:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.elegant-detail {
    padding: 2rem;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.elegant-detail::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.elegant-detail:hover::before {
    transform: scaleX(1);
}

.elegant-detail:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.detail-card {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
}

.detail-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
    flex-shrink: 0;
    position: relative;
}

.detail-icon::after {
    content: '';
    position: absolute;
    top: -3px;
    left: -3px;
    right: -3px;
    bottom: -3px;
    background: inherit;
    border-radius: inherit;
    filter: blur(8px);
    opacity: 0.6;
    z-index: -1;
}

.detail-icon.calendar {
    background: var(--primary-gradient);
}

.detail-icon.guests {
    background: var(--success-gradient);
}

.detail-icon.location {
    background: var(--secondary-gradient);
}

.detail-icon.price {
    background: var(--gold-gradient);
}

.detail-content {
    flex: 1;
}

.detail-label {
    font-size: 0.875rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.detail-extra {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
}

/* Chef Card */
.chef-card {
    text-align: center;
    position: relative;
}

.chef-header {
    margin-bottom: 2rem;
}

.chef-avatar-container {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
}

.elegant-avatar {
    position: relative;
    display: inline-block;
}

.chef-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    position: relative;
    z-index: 2;
}

.elegant-placeholder {
    width: 100px;
    height: 100px;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    position: relative;
    z-index: 2;
}

.avatar-ring {
    position: absolute;
    top: -8px;
    left: -8px;
    right: -8px;
    bottom: -8px;
    border: 3px solid transparent;
    border-radius: 50%;
    background: var(--gold-gradient);
    background-clip: border-box;
    z-index: 1;
}

.avatar-ring::before {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    right: 3px;
    bottom: 3px;
    background: white;
    border-radius: 50%;
    z-index: 1;
}

.chef-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
    background: var(--gold-gradient);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    box-shadow: 0 4px 15px rgba(246, 211, 101, 0.4);
    z-index: 3;
}

.chef-name {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.chef-specialty {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 1rem;
    margin: 0;
}

.elegant-rating {
    margin: 1.5rem 0;
}

.rating-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.rating-stars {
    display: flex;
    gap: 0.25rem;
}

.rating-stars i {
    color: #fbbf24;
    font-size: 1.2rem;
    filter: drop-shadow(0 2px 4px rgba(251, 191, 36, 0.3));
}

.rating-details {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.rating-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
}

.rating-label {
    font-size: 0.875rem;
    color: var(--text-muted);
    font-weight: 500;
}

.elegant-experience {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin: 1.5rem 0;
    padding: 1rem;
    background: linear-gradient(145deg, #f0f9ff, #e0f2fe);
    border-radius: var(--border-radius);
    border: 1px solid rgba(14, 165, 233, 0.1);
}

.experience-icon {
    width: 40px;
    height: 40px;
    background: var(--gold-gradient);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.experience-text {
    display: flex;
    flex-direction: column;
}

.experience-number {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
}

.experience-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.elegant-bio {
    margin: 1.5rem 0 2rem 0;
    padding: 1.5rem;
    background: linear-gradient(145deg, #fafafa, #f0f0f0);
    border-radius: var(--border-radius);
    border-left: 4px solid;
    border-image: var(--primary-gradient) 1;
}

.elegant-bio p {
    margin: 0;
    color: var(--text-secondary);
    line-height: 1.7;
    font-size: 1rem;
}

.elegant-social {
    margin: 2rem 0;
}

.social-label {
    font-size: 0.875rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.social-links {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.elegant-social-link {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    font-size: 1.3rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.elegant-social-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.elegant-social-link:hover::before {
    left: 100%;
}

.elegant-social-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.elegant-social-link.instagram {
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
}

.elegant-social-link.facebook {
    background: linear-gradient(145deg, #1877f2, #0c5aa6);
}

.elegant-social-link.website {
    background: var(--dark-gradient);
}

/* Stats Card */
.stats-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid;
    border-image: var(--elegant-gradient) 1;
}

.stats-header h5 {
    color: var(--text-primary);
    font-weight: 700;
    margin: 0;
}

.stats-grid {
    display: grid;
    gap: 1.5rem;
}

.elegant-stat {
    padding: 1.5rem;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.elegant-stat::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.elegant-stat:hover::before {
    transform: scaleX(1);
}

.elegant-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
    position: relative;
    flex-shrink: 0;
}

.stat-glow {
    position: absolute;
    top: -3px;
    left: -3px;
    right: -3px;
    bottom: -3px;
    background: inherit;
    border-radius: inherit;
    filter: blur(8px);
    opacity: 0.6;
    z-index: -1;
}

.price-stat {
    background: var(--gold-gradient);
}

.guests-stat {
    background: var(--success-gradient);
}

.time-stat {
    background: var(--secondary-gradient);
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-muted);
    font-weight: 500;
}

/* Gallery */
.elegant-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.gallery-item {
    border-radius: var(--border-radius);
    overflow: hidden;
    aspect-ratio: 1;
    position: relative;
    cursor: pointer;
}

.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    font-size: 1.5rem;
}

.gallery-item:hover .gallery-image {
    transform: scale(1.1);
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

/* Modal Styles */
.elegant-modal {
    border: none;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-elegant);
    overflow: hidden;
}

.elegant-modal-header {
    background: linear-gradient(145deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid rgba(0,0,0,0.1);
    padding: 2rem 2.5rem;
    position: relative;
}

.elegant-modal-header::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
}

.elegant-close {
    background: var(--secondary-gradient);
    border-radius: 8px;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.construction-animation {
    position: relative;
    margin-bottom: 2rem;
}

.construction-icon {
    color: #f59e0b;
    position: relative;
    z-index: 2;
}

.construction-particles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.particle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: #f59e0b;
    border-radius: 50%;
    animation: float 2s ease-in-out infinite;
}

.particle:nth-child(1) {
    top: -30px;
    left: -20px;
    animation-delay: 0s;
}

.particle:nth-child(2) {
    top: -25px;
    right: -15px;
    animation-delay: 0.5s;
}

.particle:nth-child(3) {
    bottom: -20px;
    left: 10px;
    animation-delay: 1s;
}

@keyframes float {
    0%, 100% { transform: translateY(0); opacity: 1; }
    50% { transform: translateY(-10px); opacity: 0.7; }
}

.coming-soon-features {
    display: grid;
    gap: 1rem;
    margin-top: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.feature-icon {
    width: 30px;
    height: 30px;
    background: var(--success-gradient);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.elegant-modal-footer {
    background: linear-gradient(145deg, #f8fafc, #f1f5f9);
    border-top: 1px solid rgba(0,0,0,0.1);
    padding: 1.5rem 2.5rem;
}

/* Chef Modal Specific Styles */
.chef-modal-avatar-container {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.chef-modal-avatar {
    position: relative;
    display: inline-block;
}

.chef-modal-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    position: relative;
    z-index: 2;
}

.chef-modal-placeholder {
    width: 120px;
    height: 120px;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
    z-index: 2;
}

.chef-modal-badge {
    position: absolute;
    bottom: -5px;
    right: -10px;
    background: var(--gold-gradient);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(246, 211, 101, 0.4);
    z-index: 3;
}

.chef-modal-name {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.chef-modal-specialty {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0 0 1rem 0;
}

.rating-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.rating-text {
    color: var(--text-secondary);
    font-weight: 600;
}

.content-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.content-header h6 {
    margin: 0;
    font-weight: 700;
    font-size: 1.1rem;
}

.bio-text {
    color: var(--text-secondary);
    line-height: 1.7;
    font-size: 1rem;
    margin: 0;
}

.stats-row {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    background: var(--gold-gradient);
    color: white;
    border-radius: var(--border-radius);
    min-width: 80px;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.25rem;
}

.stat-description {
    color: var(--text-secondary);
    margin: 0;
    flex: 1;
}

.specialties-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.elegant-tag {
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}

.elegant-tag.primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.elegant-tag:not(.primary) {
    background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
    color: var(--text-primary);
    border: 1px solid rgba(0,0,0,0.1);
}

.elegant-tag:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.social-links-modal {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.elegant-social-modal {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem 1rem;
    border-radius: var(--border-radius);
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.elegant-social-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.elegant-social-modal:hover::before {
    left: 100%;
}

.elegant-social-modal:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    color: white;
}

.social-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-on-scroll {
    animation: fadeInUp 0.8s ease-out forwards;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hero-title {
        font-size: clamp(2rem, 5vw, 3rem);
    }
    
    .elegant-card {
        padding: 2rem;
    }
    
    .price-card {
        padding: 2rem;
    }
}

@media (max-width: 768px) {
    .hero-section {
        height: 70vh;
        min-height: 500px;
    }
    
    .hero-badges {
        justify-content: center;
    }
    
    .hero-details {
        margin-top: 2rem;
    }
    
    .price-card {
        margin-top: 3rem;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        gap: 1rem;
    }
    
    .elegant-card {
        padding: 1.5rem;
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
    }
    
    .elegant-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .social-links-modal {
        grid-template-columns: 1fr;
    }
    
    .map-overlay {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .elegant-card {
        padding: 1rem;
    }
    
    .price-card {
        padding: 1.5rem;
    }
    
    .chef-card {
        padding: 1.5rem;
    }
    
    .hero-detail {
        padding: 0.75rem;
    }
    
    .detail-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-gradient);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}
</style>

<script>
let map, marker;

function initMap() {
    @if($cenaData['latitude'] && $cenaData['longitude'])
        const lat = {{ $cenaData['latitude'] }};
        const lng = {{ $cenaData['longitude'] }};
        
        // Crear el mapa
        map = L.map('map').setView([lat, lng], 15);
        
        // Agregar tiles con estilo elegante
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);
        
        // Crear icono personalizado
        const customIcon = L.divIcon({
            html: '<div class="custom-marker"><i class="fas fa-map-marker-alt"></i></div>',
            className: 'custom-marker-container',
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });
        
        // Agregar marcador
        marker = L.marker([lat, lng], { icon: customIcon }).addTo(map)
            .bindPopup(`
                <div class="elegant-popup">
                    <h6><i class="fas fa-utensils me-2"></i>{{ $cenaData['title'] }}</h6>
                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $cenaData['location'] }}</p>
                    <p class="mb-0"><i class="fas fa-calendar me-2"></i>{{ $cenaData['formatted_datetime'] }}</p>
                </div>
            `);
        
        // Agregar estilo CSS para el marcador personalizado
        const style = document.createElement('style');
        style.textContent = `
            .custom-marker-container {
                background: none;
                border: none;
            }
            .custom-marker {
                width: 40px;
                height: 40px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 16px;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                animation: bounce 2s infinite;
            }
            .custom-marker i {
                transform: rotate(45deg);
            }
            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% { transform: rotate(-45deg) translateY(0); }
                40% { transform: rotate(-45deg) translateY(-10px); }
                60% { transform: rotate(-45deg) translateY(-5px); }
            }
            .elegant-popup {
                font-family: 'Inter', sans-serif;
                padding: 0.5rem;
            }
            .elegant-popup h6 {
                color: #1f2937;
                font-weight: 700;
                margin-bottom: 0.75rem;
                font-size: 1rem;
            }
            .elegant-popup p {
                color: #6b7280;
                font-size: 0.875rem;
                margin-bottom: 0.5rem;
            }
            .leaflet-popup-content-wrapper {
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            }
        `;
        document.head.appendChild(style);
    @endif
}

function openDirections() {
    @if($cenaData['latitude'] && $cenaData['longitude'])
        const lat = {{ $cenaData['latitude'] }};
        const lng = {{ $cenaData['longitude'] }};
        const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
        window.open(url, '_blank');
    @endif
}

function showReserveModal() {
    const modal = new bootstrap.Modal(document.getElementById('reserveModal'));
    modal.show();
}

function showChefModal() {
    const modal = new bootstrap.Modal(document.getElementById('chefModal'));
    modal.show();
}

// Inicializar el mapa cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa si existe el contenedor
    if (document.getElementById('map')) {
        initMap();
    }
    
    // Smooth scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = `${index * 0.1}s`;
                entry.target.style.animationFillMode = 'both';
                entry.target.classList.add('animate-on-scroll');
            }
        });
    }, observerOptions);

    const animatedElements = document.querySelectorAll('.animate-on-scroll, .elegant-card');
    animatedElements.forEach(el => observer.observe(el));
    
    // Parallax effect for hero section
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.hero-section');
        if (hero) {
            const rate = scrolled * -0.5;
            hero.style.transform = `translateY(${rate}px)`;
        }
    });
});
</script>
@endsection