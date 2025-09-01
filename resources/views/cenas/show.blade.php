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
                <i class="fas fa-utensils fa-4x"></i>
            </div>
        @endif
        <div class="hero-overlay">
            <div class="container">
                <div class="row align-items-center min-vh-60">
                    <div class="col-lg-7">
                        <div class="hero-content">
                            <span class="hero-badge">{{ $cenaData['is_past'] ? 'Evento Finalizado' : 'Próximo Evento' }}</span>
                            <h1 class="hero-title">{{ $cenaData['title'] }}</h1>
                            <div class="hero-details">
                                <div class="hero-detail">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $cenaData['formatted_date'] }}</span>
                                </div>
                                <div class="hero-detail">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $cenaData['formatted_time'] }}</span>
                                </div>
                                <div class="hero-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $cenaData['location'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="price-card">
                            <div class="price-header">
                                <div class="price">{{ $cenaData['formatted_price'] }}</div>
                                <div class="per-person">por persona</div>
                            </div>
                            
                            @if($cenaData['can_book'])
                                <form action="{{ route('reservar') }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-reserve btn-lg w-100" onclick="showReserveModal()">
                                        <i class="fas fa-calendar-plus me-2"></i>Reservar Ahora
                                    </button>
                                </form>
                                <div class="availability-info">
                                    <i class="fas fa-users me-1"></i>
                                    <span class="availability-text">{{ $cenaData['available_spots'] }} espacios disponibles</span>
                                </div>
                            @else
                                <button class="btn btn-unavailable btn-lg w-100" disabled>
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
        <div class="row g-4">
            <!-- Content -->
            <div class="col-lg-8">
                <!-- Menú Section -->
                <section class="content-card animate-on-scroll">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>Menú de la Experiencia</h3>
                    </div>
                    <div class="menu-content">
                        <p class="menu-text">{{ $cenaData['menu'] }}</p>
                    </div>
                </section>

                <!-- Detalles Section -->
                <section class="content-card animate-on-scroll">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>Detalles del Evento</h3>
                    </div>
                    <div class="details-grid">
                        <div class="detail-card">
                            <i class="fas fa-calendar detail-icon"></i>
                            <div class="detail-content">
                                <div class="detail-label">Fecha y Hora</div>
                                <div class="detail-value">{{ $cenaData['formatted_datetime'] }}</div>
                            </div>
                        </div>
                        <div class="detail-card">
                            <i class="fas fa-users detail-icon"></i>
                            <div class="detail-content">
                                <div class="detail-label">Comensales</div>
                                <div class="detail-value">{{ $cenaData['guests_current'] }}/{{ $cenaData['guests_max'] }} personas</div>
                            </div>
                        </div>
                        <div class="detail-card">
                            <i class="fas fa-map-marker-alt detail-icon"></i>
                            <div class="detail-content">
                                <div class="detail-label">Ubicación</div>
                                <div class="detail-value">{{ $cenaData['location'] }}</div>
                            </div>
                        </div>
                        <div class="detail-card">
                            <i class="fas fa-euro-sign detail-icon"></i>
                            <div class="detail-content">
                                <div class="detail-label">Precio</div>
                                <div class="detail-value">{{ $cenaData['formatted_price'] }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                @if($cenaData['gallery_image_urls'] && count($cenaData['gallery_image_urls']) > 0)
                <!-- Gallery Section -->
                <section class="content-card animate-on-scroll">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3>Galería</h3>
                    </div>
                    <div class="gallery-grid">
                        @foreach($cenaData['gallery_image_urls'] as $image)
                            <div class="gallery-item">
                                <img src="{{ $image }}" alt="Galería" class="gallery-image">
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Chef Info -->
                <div class="chef-card animate-on-scroll">
                    <div class="chef-header">
                        <div class="chef-avatar">
                            @if($cena->user->avatar_url)
                                <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}" 
                                     class="chef-image">
                            @else
                                <div class="chef-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
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
                    <div class="chef-rating">
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
                            <span class="rating-value">{{ $cena->user->formatted_rating ?? number_format($cena->user->rating, 1) }}</span>
                        </div>
                    </div>
                    @endif

                    @if($cena->user->experiencia_anos)
                        <div class="chef-experience">
                            <i class="fas fa-award"></i>
                            <span>{{ $cena->user->experience_text ?? $cena->user->experiencia_anos . ' años de experiencia' }}</span>
                        </div>
                    @endif
                    
                    <div class="chef-bio">
                        @if($cena->user->bio)
                            <p>{{ Str::limit($cena->user->bio, 120) }}</p>
                        @else
                            <p>Especialista en crear experiencias culinarias únicas que conectan a las personas a través de la comida y la conversación.</p>
                        @endif
                    </div>
                    
                    @if($cena->user->instagram || $cena->user->facebook || $cena->user->website)
                        <div class="chef-social">
                            @if($cena->user->instagram)
                                <a href="{{ $cena->user->instagram_url ?? '#' }}" target="_blank" class="social-link instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if($cena->user->facebook)
                                <a href="{{ $cena->user->facebook_url ?? '#' }}" target="_blank" class="social-link facebook">
                                    <i class="fab fa-facebook"></i>
                                </a>
                            @endif
                            @if($cena->user->website)
                                <a href="{{ $cena->user->website }}" target="_blank" class="social-link website">
                                    <i class="fas fa-globe"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    <button class="btn btn-chef-profile w-100" onclick="showChefModal()">
                        <i class="fas fa-user me-2"></i>Ver Perfil Completo
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="stats-card animate-on-scroll">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-icon price">
                                <i class="fas fa-euro-sign"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $cenaData['formatted_price'] }}</div>
                                <div class="stat-label">Precio</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon guests">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $cenaData['guests_max'] }}</div>
                                <div class="stat-label">Max. Personas</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon time">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ abs($cenaData['days_until']) }}</div>
                                <div class="stat-label">{{ $cenaData['days_until'] >= 0 ? 'Días' : 'Días Atrás' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Reserva -->
<div class="modal fade" id="reserveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Reservar {{ $cenaData['title'] }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="construction-icon">
                    <i class="fas fa-tools fa-4x"></i>
                </div>
                <h4 class="mt-3 mb-3">Sistema en Desarrollo</h4>
                <p class="text-muted mb-4">El sistema de reservas estará disponible muy pronto. ¡Mantente atento para ser el primero en reservar!</p>
                <div class="coming-soon-features">
                    <div class="feature-item">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Reservas instantáneas
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Confirmación automática
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Gestión de pagos segura
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-bell me-2"></i>Notificarme
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal del Chef -->
<div class="modal fade" id="chefModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modern-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-circle me-2"></i>
                    Perfil de {{ $cena->user->name ?? 'Chef' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="chef-profile-header text-center mb-4">
                    <div class="chef-modal-avatar mb-3">
                        @if($cena->user->avatar_url)
                            <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}" class="chef-modal-image">
                        @else
                            <div class="chef-modal-placeholder">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    <h4>{{ $cena->user->name ?? 'Chef Anónimo' }}</h4>
                    @if($cena->user->especialidad)
                        <p class="text-primary mb-2">{{ $cena->user->especialidad }}</p>
                    @endif
                    
                    @if($cena->user->rating > 0)
                        <div class="rating-display mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($cena->user->rating))
                                    <i class="fas fa-star text-warning"></i>
                                @elseif($i - 0.5 <= $cena->user->rating)
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                            <span class="ms-2">{{ $cena->user->formatted_rating ?? number_format($cena->user->rating, 1) }} de 5</span>
                        </div>
                    @endif
                </div>
                
                @if($cena->user->bio)
                    <div class="chef-full-bio mb-4">
                        <h6><i class="fas fa-quote-left me-2"></i>Acerca del Chef</h6>
                        <p>{{ $cena->user->bio }}</p>
                    </div>
                @endif
                
                @if($cena->user->experiencia_anos)
                    <div class="chef-stats mb-4">
                        <h6><i class="fas fa-award me-2"></i>Experiencia</h6>
                        <p>{{ $cena->user->experience_text ?? $cena->user->experiencia_anos . ' años de experiencia culinaria' }}</p>
                    </div>
                @endif
                
                <div class="chef-specialties mb-4">
                    <h6><i class="fas fa-star me-2"></i>Especialidades</h6>
                    <div class="specialties-list">
                        @if($cena->user->especialidad)
                            <span class="specialty-tag">{{ $cena->user->especialidad }}</span>
                        @endif
                        <span class="specialty-tag">Cocina creativa</span>
                        <span class="specialty-tag">Experiencias íntimas</span>
                        <span class="specialty-tag">Ingredientes locales</span>
                    </div>
                </div>
                
                @if($cena->user->instagram || $cena->user->facebook || $cena->user->website)
                    <div class="chef-social-full text-center">
                        <h6><i class="fas fa-share-alt me-2"></i>Sígueme</h6>
                        <div class="social-links-modal">
                            @if($cena->user->instagram)
                                <a href="{{ $cena->user->instagram_url ?? '#' }}" target="_blank" class="social-link-modal instagram">
                                    <i class="fab fa-instagram"></i>
                                    <span>Instagram</span>
                                </a>
                            @endif
                            @if($cena->user->facebook)
                                <a href="{{ $cena->user->facebook_url ?? '#' }}" target="_blank" class="social-link-modal facebook">
                                    <i class="fab fa-facebook"></i>
                                    <span>Facebook</span>
                                </a>
                            @endif
                            @if($cena->user->website)
                                <a href="{{ $cena->user->website }}" target="_blank" class="social-link-modal website">
                                    <i class="fas fa-globe"></i>
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
    --primary-color: #2563eb;
    --primary-dark: #1d4ed8;
    --secondary-color: #64748b;
    --success-color: #059669;
    --danger-color: #dc2626;
    --warning-color: #d97706;
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.15);
    --shadow-lg: 0 8px 25px rgba(0,0,0,0.2);
    --border-radius: 12px;
}

/* Hero Section */
.hero-section {
    position: relative;
    height: 70vh;
    min-height: 500px;
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
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    z-index: 1;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.4) 100%);
    z-index: 2;
    display: flex;
    align-items: center;
}

.hero-content {
    z-index: 3;
    position: relative;
}

.hero-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 500;
    backdrop-filter: blur(10px);
    margin-bottom: 1rem;
}

.hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    color: white;
    text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    margin-bottom: 2rem;
    line-height: 1.2;
}

.hero-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.hero-detail {
    display: flex;
    align-items: center;
    color: rgba(255,255,255,0.9);
    font-size: 1.1rem;
    font-weight: 500;
}

.hero-detail i {
    margin-right: 0.75rem;
    width: 20px;
    color: rgba(255,255,255,0.8);
}

/* Price Card */
.price-card {
    background: rgba(255,255,255,0.98);
    padding: 2.5rem 2rem;
    border-radius: var(--border-radius);
    backdrop-filter: blur(20px);
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(255,255,255,0.2);
}

.price-header {
    text-align: center;
    margin-bottom: 2rem;
}

.price {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}

.per-person {
    color: var(--secondary-color);
    font-weight: 500;
    margin-top: 0.5rem;
}

.btn-reserve {
    background: var(--gradient-success);
    border: none;
    color: white;
    font-weight: 600;
    padding: 1rem;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-reserve:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-reserve::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-reserve:hover::before {
    left: 100%;
}

.btn-unavailable {
    background: #6b7280;
    border: none;
    color: white;
    font-weight: 600;
    padding: 1rem;
    border-radius: var(--border-radius);
}

.availability-info {
    text-align: center;
    margin-top: 1rem;
    padding: 0.75rem;
    background: rgba(16, 185, 129, 0.1);
    border-radius: var(--border-radius);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.availability-text {
    color: var(--success-color);
    font-weight: 600;
    font-size: 0.9rem;
}

/* Content Cards */
.content-card {
    background: white;
    padding: 2.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    margin-bottom: 2rem;
    border: 1px solid rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.content-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
}

.section-icon {
    width: 50px;
    height: 50px;
    background: var(--gradient-primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.section-header h3 {
    color: #1f2937;
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0;
}

.menu-content {
    background: #f8fafc;
    padding: 2rem;
    border-radius: var(--border-radius);
    border-left: 4px solid var(--primary-color);
}

.menu-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #374151;
    margin: 0;
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.detail-card {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: var(--border-radius);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.detail-card:hover {
    background: white;
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
}

.detail-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.detail-content {
    flex: 1;
}

.detail-label {
    font-size: 0.875rem;
    color: var(--secondary-color);
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

/* Chef Card */
.chef-card {
    background: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    margin-bottom: 2rem;
    border: 1px solid rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.chef-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.chef-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chef-avatar {
    margin-right: 1rem;
}

.chef-image {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-color);
}

.chef-placeholder {
    width: 60px;
    height: 60px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.chef-info {
    flex: 1;
}

.chef-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.25rem 0;
}

.chef-specialty {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 0.9rem;
    margin: 0;
}

.chef-rating {
    margin-bottom: 1rem;
}

.rating-stars {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.rating-stars i {
    color: #fbbf24;
    font-size: 1rem;
}

.rating-value {
    margin-left: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.chef-experience {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    color: var(--secondary-color);
    font-size: 0.9rem;
}

.chef-experience i {
    margin-right: 0.5rem;
    color: var(--warning-color);
}

.chef-bio {
    margin-bottom: 1.5rem;
    color: #6b7280;
    line-height: 1.6;
}

.chef-social {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.social-link.instagram {
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
}

.social-link.facebook {
    background: #1877f2;
}

.social-link.website {
    background: #6b7280;
}

.btn-chef-profile {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.875rem;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.btn-chef-profile:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
    color: white;
}

/* Stats Card */
.stats-card {
    background: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    border: 1px solid rgba(0,0,0,0.08);
}

.stats-grid {
    display: grid;
    gap: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: var(--border-radius);
}

.stat-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
}

.stat-icon.price {
    background: var(--gradient-success);
}

.stat-icon.guests {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.stat-icon.time {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--secondary-color);
    font-weight: 500;
    margin-top: 0.25rem;
}

/* Gallery */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.gallery-item {
    border-radius: var(--border-radius);
    overflow: hidden;
    aspect-ratio: 1;
}

.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-image:hover {
    transform: scale(1.05);
}

/* Modals */
.modern-modal {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
}

.modern-modal .modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 2rem;
    background: #f9fafb;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.modern-modal .modal-body {
    padding: 2rem;
}

.construction-icon {
    color: var(--warning-color);
    margin-bottom: 1rem;
}

.coming-soon-features {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.feature-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    background: #f0f9ff;
    border-radius: var(--border-radius);
    color: #374151;
    font-weight: 500;
}

/* Chef Modal Specific */
.chef-modal-avatar {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}

.chef-modal-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--primary-color);
}

.chef-modal-placeholder {
    width: 100px;
    height: 100px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.specialties-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.specialty-tag {
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 500;
}

.social-links-modal {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.social-link-modal {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.social-link-modal:hover {
    transform: translateY(-2px);
    color: white;
}

.social-link-modal.instagram {
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
}

.social-link-modal.facebook {
    background: #1877f2;
}

.social-link-modal.website {
    background: #6b7280;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-on-scroll {
    animation: fadeInUp 0.6s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-section {
        height: 60vh;
        min-height: 400px;
    }
    
    .hero-details {
        margin-top: 1rem;
    }
    
    .price-card {
        margin-top: 2rem;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        gap: 1rem;
    }
    
    .chef-header {
        flex-direction: column;
        text-align: center;
    }
    
    .chef-avatar {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .social-links-modal {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 576px) {
    .content-card,
    .chef-card,
    .stats-card {
        padding: 1.5rem;
    }
    
    .price-card {
        padding: 2rem 1.5rem;
    }
    
    .hero-title {
        font-size: 2rem;
    }
}
</style>

<script>
function showReserveModal() {
    const modal = new bootstrap.Modal(document.getElementById('reserveModal'));
    modal.show();
}

function showChefModal() {
    const modal = new bootstrap.Modal(document.getElementById('chefModal'));
    modal.show();
}

// Smooth scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animationDelay = '0.1s';
            entry.target.style.animationFillMode = 'both';
        }
    });
}, observerOptions);

document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    animatedElements.forEach(el => observer.observe(el));
});
</script>
@endsection