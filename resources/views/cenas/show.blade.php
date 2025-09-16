@extends('layouts.app')

@section('title', $cenaData['title'])

@section('content')
<div class="container-fluid">
    <!-- Hero Section Simplificado -->
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
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="hero-content">
                            <span class="badge-status {{ $cenaData['is_past'] ? 'badge-past' : 'badge-upcoming' }}">
                                {{ $cenaData['is_past'] ? 'Evento Finalizado' : 'Próximo Evento' }}
                            </span>
                            <h1 class="hero-title">{{ $cenaData['title'] }}</h1>
                            <div class="hero-info">
                                <div class="info-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ \Carbon\Carbon::parse($cenaData['datetime'])->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY - HH:mm') }}</span>
                                </div>
                                 @if(!empty($cenaData['clean_location']))
                                        <div class="info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>{{ $cenaData['clean_location'] }}</span>
                                        </div>
                                        @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="price-card">
                            <div class="price-display">
                                <span class="price">{{ $cenaData['formatted_price'] }}</span>
                                <span class="per-person">por persona</span>
                            </div>
                            
                            @if($cenaData['can_book'])
                                @auth
                                    <a href="{{ route('comensal.checkout', $cena->id) }}" class="btn btn-reserve">
                                        <i class="fas fa-calendar-plus me-2"></i>
                                        Reservar Ahora
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-reserve">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Iniciar Sesión para Reservar
                                    </a>
                                @endauth
                                <div class="availability">
                                    <i class="fas fa-users me-2"></i>
                                    {{ $cenaData['available_spots'] }} espacios disponibles
                                </div>
                            @else
                                <button class="btn btn-unavailable" disabled>
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

    <!-- Contenido Principal -->
    <div class="container py-5">
        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-8">
                <!-- Menú -->
            
            <div class="content-card">
                <h3><i class="fas fa-utensils me-2"></i>Menú</h3>
                <div class="menu-content">
                    {{-- CAMBIO PRINCIPAL: Quitar {{ }} y usar {!! !!} --}}
                    {!! $cenaData['menu'] !!}
                </div>
            </div>
                <!-- Detalles -->
                <div class="content-card">
                    <h3><i class="fas fa-info-circle me-2"></i>Detalles</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <i class="fas fa-calendar text-primary"></i>
                            <div>
                                <strong>Fecha y Hora</strong>
                                <span>{{ $cenaData['formatted_datetime'] }}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-users text-primary"></i>
                            <div>
                                <strong>Comensales</strong>
                                <span>{{ $cenaData['guests_current'] }}/{{ $cenaData['guests_max'] }} personas</span>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-dollar-sign text-primary"></i>
                            <div>
                                <strong>Precio</strong>
                                <span>{{ $cenaData['formatted_price'] }} por persona</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mapa Simple -->
                @if($cenaData['latitude'] && $cenaData['longitude'])
                <div class="content-card">
                    <h3><i class="fas fa-map-marked-alt me-2"></i>Ubicación</h3>
                    <div class="map-container">
                        <div id="map"></div>
                        <button class="btn btn-sm btn-outline-primary mt-3" onclick="openDirections()">
                            <i class="fas fa-directions me-1"></i>Cómo llegar
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar del Chef -->
            <div class="col-lg-4">
                <div class="chef-card">
                    <div class="chef-header">
                        <div class="chef-avatar">
                            @if($cena->user->avatar_url)
                                <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}">
                            @else
                                <div class="avatar-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            @if($cena->user->rating > 4.5)
                                <div class="chef-badge">
                                    <i class="fas fa-crown"></i>
                                </div>
                            @endif
                        </div>
                        <div class="chef-info">
                            <h4>{{ $cena->user->name ?? 'Chef Anónimo' }}</h4>
                            @if($cena->user->especialidad)
                                <p class="chef-specialty">{{ $cena->user->especialidad }}</p>
                            @else
                                <p class="chef-specialty">Chef Especializado</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Rating y Experiencia -->
                    @if($cena->user->rating > 0 || $cena->user->experiencia_anos)
                    <div class="chef-stats">
                        @if($cena->user->rating > 0)
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stars">
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
                                <span class="stat-text">{{ $cena->user->formatted_rating ?? number_format($cena->user->rating, 1) }} de 5 estrellas</span>
                            </div>
                        </div>
                        @endif

                        @if($cena->user->experiencia_anos)
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number">{{ $cena->user->experiencia_anos }}</span>
                                <span class="stat-text">{{ $cena->user->experience_text ?? ($cena->user->experiencia_anos . ' ' . ($cena->user->experiencia_anos == 1 ? 'año' : 'años') . ' de experiencia') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Bio del Chef -->
                    @if($cena->user->bio)
                    <div class="chef-section">
                        <h5 class="section-title">
                            <i class="fas fa-quote-left me-2"></i>Sobre el Chef
                        </h5>
                        <div class="chef-bio">
                            <p>{{ $cena->user->bio }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Información de Contacto -->
                    <div class="chef-section">
                        <h5 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>Información
                        </h5>
                        <div class="contact-info">
                           

                            @if($cena->user->especialidad)
                            <div class="contact-item">
                                <i class="fas fa-user-tie text-primary"></i>
                                <div class="contact-content">
                                    <strong>Tipo de Chef</strong>
                                    <span>{{ ucfirst($cena->user->especialidad) }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Redes Sociales -->
                    @if($cena->user->instagram || $cena->user->facebook || $cena->user->website)
                    <div class="chef-section">
                        <h5 class="section-title">
                            <i class="fas fa-share-alt me-2"></i>Sígueme
                        </h5>
                        <div class="chef-social">
                            @if($cena->user->instagram)
                                <a href="{{ $cena->user->instagram_url }}" target="_blank" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                    <span>Instagram</span>
                                </a>
                            @endif
                            @if($cena->user->facebook)
                                <a href="{{ $cena->user->facebook_url }}" target="_blank" class="social-link" title="Facebook">
                                    <i class="fab fa-facebook"></i>
                                    <span>Facebook</span>
                                </a>
                            @endif
                            @if($cena->user->website)
                                <a href="{{ $cena->user->website }}" target="_blank" class="social-link" title="Sitio Web">
                                    <i class="fas fa-globe"></i>
                                    <span>Website</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Botón de Contacto -->
                    @if($cena->user->telefono || $cena->user->email)
                    <div class="chef-section">
                        <button class="btn btn-outline-primary w-100" onclick="showContactModal()">
                            <i class="fas fa-envelope me-2"></i>
                            Contactar Chef
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Información Adicional del Evento -->
                @if($cena->special_requirements || $cena->cancellation_policy)
                <div class="chef-card mt-3">
                    <div class="chef-section">
                        <h5 class="section-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>Información Importante
                        </h5>
                        
                        @if($cena->special_requirements)
                        <div class="info-item">
                            <strong>Requisitos Especiales:</strong>
                            <p class="mb-2">{{ $cena->special_requirements }}</p>
                        </div>
                        @endif

                        @if($cena->cancellation_policy)
                        <div class="info-item">
                            <strong>Política de Cancelación:</strong>
                            <p class="mb-0">{{ $cena->cancellation_policy }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Contacto -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-envelope me-2"></i>
                    Contactar a {{ $cena->user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="chef-contact-info text-center mb-4">
                    <div class="chef-avatar-small">
                        @if($cena->user->avatar_url)
                            <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}">
                        @else
                            <div class="avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <h4>{{ $cena->user->name }}</h4>
                    <p class="text-muted">{{ $cena->user->especialidad ?? 'Chef Profesional' }}</p>
                </div>

                <div class="contact-options">
                    @if($cena->user->telefono)
                    <div class="contact-option">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                      
                    </div>
                    @endif

                    @if($cena->user->email)
                    <div class="contact-option">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <strong>Email</strong>
                            <span>{{ $cena->user->email }}</span>
                            <a href="mailto:{{ $cena->user->email }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-envelope me-1"></i>Enviar Email
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($cena->user->instagram)
                    <div class="contact-option">
                        <div class="contact-icon">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <div class="contact-details">
                            <strong>Instagram</strong>
                            <span>@{{ str_replace(['https://instagram.com/', 'https://www.instagram.com/'], '', $cena->user->instagram) }}</span>
                            <a href="{{ $cena->user->instagram_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fab fa-instagram me-1"></i>Ver Perfil
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Evento:</strong> {{ $cena->title }}
                    <br>
                    <small>{{ $cena->formatted_date }}</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reserveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Reservar {{ $cenaData['title'] }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-tools fa-4x text-warning"></i>
                </div>
                <h4 class="mb-3">Sistema en Desarrollo</h4>
                <p class="text-muted mb-4">Estamos trabajando en la funcionalidad de reservas. ¡Muy pronto estará disponible!</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Te notificaremos cuando esté listo
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-bell me-2"></i>Notificarme
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Google Maps JS -->
<script>
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCuh8GSFyFxvDaiEeWcW7JXs2KIcf89dHY&libraries=places&loading=async&callback=initMapCallback';
    script.async = true;
    document.head.appendChild(script);
</script>

<style>
        :root {
            --primary-color: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border-color: #e5e7eb;
            --bg-light: #f9fafb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: var(--text-primary);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 60vh;
            min-height: 500px;
            overflow: hidden;
        }

        .hero-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
        }

        .hero-content {
            color: white;
        }

        .badge-status {
            background: var(--success-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .badge-past {
            background: var(--warning-color);
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .hero-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
        }

        .info-item i {
            width: 20px;
            color: var(--primary-light);
        }

        /* Price Card */
        .price-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
        }

        .price-display {
            margin-bottom: 2rem;
        }

        .price {
            display: block;
            font-size: 3rem;
            font-weight: 900;
            color: var(--primary-color);
            line-height: 1;
        }

        .per-person {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .btn-reserve {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-reserve:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .btn-unavailable {
            background: #6b7280;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
        }

        .availability {
            margin-top: 1rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Content Cards */
        .content-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .content-card h3 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .content-card h3 i {
            color: var(--primary-color);
        }

        .menu-content {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .menu-content p {
            margin: 0;
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--text-primary);
        }

        /* Details Grid */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .detail-item i {
            font-size: 1.2rem;
            margin-top: 0.25rem;
        }

        .detail-item div strong {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .detail-item div span {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Map */
        .map-container #map {
            width: 100%;
            height: 300px;
            border-radius: 8px;
        }

        /* Chef Card */
        .chef-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
            position: sticky;
            top: 2rem;
        }

        .chef-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .chef-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
        }

        .chef-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
        }

        .chef-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            border: 2px solid white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #b45309;
            font-size: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .chef-info h4 {
            margin: 0 0 0.25rem 0;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .chef-specialty {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1rem;
        }

        /* Chef Stats */
        .chef-stats {
            margin-bottom: 2rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stat-content {
            flex: 1;
        }

        .stars {
            display: flex;
            gap: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .stars i {
            color: #fbbf24;
            font-size: 1rem;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .stat-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Chef Sections */
        .chef-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .chef-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .chef-bio p {
            color: var(--text-secondary);
            line-height: 1.7;
            margin: 0;
            font-size: 1rem;
        }

        /* Contact Info */
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .contact-item i {
            font-size: 1.2rem;
            margin-top: 0.25rem;
        }

        .contact-content {
            flex: 1;
        }

        .contact-content strong {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .contact-content span {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Social Links Actualizadas */
        .chef-social {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .social-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .social-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            color: white;
        }

        .social-link:has(.fa-instagram) {
            background: linear-gradient(45deg, #f09433, #dc2743);
        }

        .social-link:has(.fa-facebook) {
            background: #1877f2;
        }

        .social-link:has(.fa-globe) {
            background: #6b7280;
        }

        .social-link i {
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        /* Info Items para requisitos especiales */
        .info-item {
            margin-bottom: 1rem;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item strong {
            color: var(--text-primary);
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
        }

        .info-item p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin: 0;
        }

        /* Modal de Contacto */
        .chef-contact-info {
            padding: 1rem 0;
        }

        .chef-avatar-small {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 1rem auto;
            position: relative;
        }

        .chef-avatar-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chef-avatar-small .avatar-placeholder {
            font-size: 2rem;
        }

        .contact-options {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .contact-option {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--bg-light);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .contact-details {
            flex: 1;
        }

        .contact-details strong {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .contact-details span {
            display: block;
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }

        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                height: 50vh;
                min-height: 400px;
            }
            
            .hero-info {
                margin-top: 1rem;
            }
            
            .price-card {
                margin-top: 2rem;
            }
            
            .content-card {
                padding: 1.5rem;
            }
            
            .chef-card {
                position: static;
                margin-top: 2rem;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .price {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 576px) {
            .content-card {
                padding: 1rem;
            }
            
            .chef-card {
                padding: 1rem;
            }
            
            .price-card {
                padding: 1.5rem;
            }
            
            .detail-item {
                flex-direction: column;
                text-align: center;
            }
            
            .chef-header {
                flex-direction: column;
                text-align: center;
            }
            
            .info-item {
                justify-content: center;
            }
        }

        #map {
    height: 300px;
    width: 100%;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}

.map-container {
    position: relative;
}

/* Indicador de estado de ubicación */
.location-status {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255, 255, 255, 0.95);
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.location-exact {
    color: #059669;
    background: rgba(5, 150, 105, 0.1);
    border: 1px solid #059669;
}

.location-approximate {
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid #f59e0b;
}

@media (max-width: 768px) {
    #map {
        height: 250px;
    }
}
</style>

<script>
let map;

function initMapCallback() {
    @if($cenaData['latitude'] && $cenaData['longitude'])
        initMap();
    @endif
}

function initMap() {
    @if($cenaData['latitude'] && $cenaData['longitude'])
        const exactLat = {{ $cenaData['latitude'] }};
        const exactLng = {{ $cenaData['longitude'] }};
        const userHasReservation = {{ $cenaData['user_has_reservation'] ? 'true' : 'false' }};
        const canSeeExactLocation = {{ $cenaData['can_see_exact_location'] ? 'true' : 'false' }};
        const hoursUntilCena = {{ $cenaData['hours_until_cena'] }};
        
        let displayLat, displayLng, zoom, markerTitle, markerColor, showCircle = false;
        
        if (canSeeExactLocation) {
            // Usuario con reserva y menos de 24h - mostrar ubicación exacta
            displayLat = exactLat;
            displayLng = exactLng;
            zoom = 16;
            markerTitle = '{{ $cenaData["title"] }} - Ubicación exacta';
            markerColor = '#059669'; // Verde para confirmado
            showCircle = false;
        } else {
            // Mostrar ubicación aproximada (sin reserva o más de 24h)
            const offsetRange = 0.004; // Aproximadamente 400 metros
            const randomOffsetLat = (Math.random() - 0.5) * offsetRange;
            const randomOffsetLng = (Math.random() - 0.5) * offsetRange;
            
            displayLat = exactLat + randomOffsetLat;
            displayLng = exactLng + randomOffsetLng;
            zoom = 13;
            markerTitle = '{{ $cenaData["title"] }} - Área aproximada';
            markerColor = userHasReservation ? '#f59e0b' : '#6b7280'; // Amarillo para reservado, gris para público
            showCircle = true;
        }
        
        // Configuración del mapa
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: displayLat, lng: displayLng },
            zoom: zoom,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });
        
        // Crear marcador según el estado
        const marker = new google.maps.Marker({
            position: { lat: displayLat, lng: displayLng },
            map: map,
            title: markerTitle,
            icon: canSeeExactLocation ? {
                // Marcador preciso para ubicación exacta
                path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                scale: 8,
                fillColor: markerColor,
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2
            } : {
                // Marcador circular para ubicación aproximada
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: markerColor,
                fillOpacity: 0.8,
                strokeColor: '#ffffff',
                strokeWeight: 3
            }
        });
        
        // Círculo de área aproximada si es necesario
        if (showCircle) {
            const approximateCircle = new google.maps.Circle({
                strokeColor: markerColor,
                strokeOpacity: 0.6,
                strokeWeight: 2,
                fillColor: markerColor,
                fillOpacity: 0.1,
                map: map,
                center: { lat: displayLat, lng: displayLng },
                radius: 600 // 600 metros de radio
            });
        }
        
        // Crear InfoWindow según el estado
        let infoContent = '';
        
        if (canSeeExactLocation) {
            // Usuario con reserva - ubicación exacta disponible
            infoContent = `
                <div style="font-family: Inter, sans-serif; padding: 12px; max-width: 280px;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="background: #059669; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; margin-right: 8px;">
                            UBICACIÓN EXACTA
                        </span>
                        @if($cenaData['reservation_code'])
                        <span style="background: #2563eb; color: white; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 600;">
                            {{ $cenaData['reservation_code'] }}
                        </span>
                        @endif
                    </div>
                    <strong style="color: #111827; font-size: 16px;">{{ $cenaData['title'] }}</strong><br>
                    <div style="margin: 8px 0; padding: 8px; background: #f0fdf4; border-radius: 6px;">
                        <small style="color: #059669; font-weight: 500;">
                            <i class="fas fa-map-marker-alt"></i> {{ $cenaData['location'] }}
                        </small>
                    </div>
                    <small style="color: #2563eb; font-size: 13px;">
                        <i class="fas fa-calendar"></i> {{ $cenaData['formatted_datetime'] }}
                    </small>
                    <div style="margin-top: 8px; padding: 6px; background: #eff6ff; border-radius: 4px;">
                        <small style="color: #1d4ed8; font-weight: 500;">
                            <i class="fas fa-check-circle"></i> Tienes una reserva confirmada
                        </small>
                    </div>
                </div>
            `;
        } else if (userHasReservation) {
            // Usuario con reserva pero más de 24h antes
            infoContent = `
                <div style="font-family: Inter, sans-serif; padding: 12px; max-width: 280px;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="background: #f59e0b; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; margin-right: 8px;">
                            ÁREA APROXIMADA
                        </span>
                        @if($cenaData['reservation_code'])
                        <span style="background: #2563eb; color: white; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 600;">
                            {{ $cenaData['reservation_code'] }}
                        </span>
                        @endif
                    </div>
                    <strong style="color: #111827; font-size: 16px;">{{ $cenaData['title'] }}</strong><br>
                    <div style="margin: 8px 0; padding: 8px; background: #fffbeb; border-radius: 6px; border-left: 3px solid #f59e0b;">
                        <small style="color: #92400e; font-weight: 500;">
                            <i class="fas fa-clock"></i> Ubicación exacta disponible en ${Math.ceil(hoursUntilCena - 24)}h
                        </small>
                    </div>
                    <small style="color: #6b7280; font-size: 13px;">
                        <i class="fas fa-map-marker-alt"></i> {{ $cenaData['location'] }}
                    </small><br>
                    <small style="color: #2563eb; font-size: 13px;">
                        <i class="fas fa-calendar"></i> {{ $cenaData['formatted_datetime'] }}
                    </small>
                    <div style="margin-top: 8px; padding: 6px; background: #eff6ff; border-radius: 4px;">
                        <small style="color: #1d4ed8; font-weight: 500;">
                            <i class="fas fa-ticket-alt"></i> Tienes una reserva confirmada
                        </small>
                    </div>
                </div>
            `;
        } else {
            // Usuario sin reserva - solo área general
            infoContent = `
                <div style="font-family: Inter, sans-serif; padding: 12px; max-width: 280px;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="background: #6b7280; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                            ÁREA GENERAL
                        </span>
                    </div>
                    <strong style="color: #111827; font-size: 16px;">{{ $cenaData['title'] }}</strong><br>
                    <div style="margin: 8px 0; padding: 8px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #6b7280;">
                        <small style="color: #374151; font-weight: 500;">
                            <i class="fas fa-info-circle"></i> Ubicación exacta solo para comensales confirmados
                        </small>
                    </div>
                    <small style="color: #6b7280; font-size: 13px;">
                        <i class="fas fa-map-marker-alt"></i> {{ $cenaData['location'] }}
                    </small><br>
                    <small style="color: #2563eb; font-size: 13px;">
                        <i class="fas fa-calendar"></i> {{ $cenaData['formatted_datetime'] }}
                    </small>
                    @if($cenaData['is_available'])
                    <div style="margin-top: 8px; padding: 6px; background: #f0fdf4; border-radius: 4px;">
                        <small style="color: #059669; font-weight: 500;">
                            <i class="fas fa-calendar-plus"></i> Haz tu reserva para obtener la ubicación exacta
                        </small>
                    </div>
                    @endif
                </div>
            `;
        }
        
        const infoWindow = new google.maps.InfoWindow({
            content: infoContent
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
    @endif
}

function openDirections() {
    @if($cenaData['latitude'] && $cenaData['longitude'])
        const exactLat = {{ $cenaData['latitude'] }};
        const exactLng = {{ $cenaData['longitude'] }};
        const userHasReservation = {{ $cenaData['user_has_reservation'] ? 'true' : 'false' }};
        const canSeeExactLocation = {{ $cenaData['can_see_exact_location'] ? 'true' : 'false' }};
        const hoursUntilCena = {{ $cenaData['hours_until_cena'] }};
        
        if (canSeeExactLocation) {
            // Direcciones exactas disponibles para usuarios con reserva
            const url = `https://www.google.com/maps/dir/?api=1&destination=${exactLat},${exactLng}`;
            window.open(url, '_blank');
        } else {
            // Mostrar mensaje según el estado del usuario
            let title, message, icon;
            
            if (userHasReservation) {
                // Usuario con reserva pero más de 24h antes
                title = 'Ubicación disponible pronto';
                message = `
                    <div style="text-align: left;">
                        <p>Como tienes una reserva confirmada, la ubicación exacta estará disponible <strong>${Math.ceil(hoursUntilCena - 24)} horas antes</strong> del evento.</p>
                        @if($cenaData['reservation_code'])
                        <p><small><strong>Tu código de reserva:</strong> {{ $cenaData['reservation_code'] }}</small></p>
                        @endif
                        <hr>
                        <p><strong>Mientras tanto:</strong></p>
                        <ul style="text-align: left; padding-left: 20px;">
                            <li>Tu reserva está confirmada</li>
                            <li>Recibirás la ubicación exacta automáticamente</li>
                            <li>Puedes contactar al chef si tienes preguntas</li>
                        </ul>
                    </div>
                `;
                icon = 'info';
            } else {
                // Usuario sin reserva
                title = 'Ubicación solo para comensales';
                message = `
                    <div style="text-align: left;">
                        <p>La ubicación exacta solo está disponible para personas con <strong>reserva confirmada</strong>.</p>
                        <hr>
                        <p><strong>Para obtener la ubicación exacta:</strong></p>
                        <ul style="text-align: left; padding-left: 20px;">
                            <li>Haz tu reserva para esta cena</li>
                            <li>Recibirás la ubicación 24h antes del evento</li>
                            <li>Podrás obtener direcciones precisas</li>
                        </ul>
                    </div>
                `;
                icon = 'warning';
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    html: message,
                    icon: icon,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#2563eb'
                });
            } else {
                alert(title + '\n\n' + message.replace(/<[^>]*>/g, '').trim());
            }
        }
    @endif
}

function showContactModal() {
    const modal = new bootstrap.Modal(document.getElementById('contactModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Actualizar el botón de direcciones según disponibilidad
    @if($cenaData['latitude'] && $cenaData['longitude'])
        const userHasReservation = {{ $cenaData['user_has_reservation'] ? 'true' : 'false' }};
        const canSeeExactLocation = {{ $cenaData['can_see_exact_location'] ? 'true' : 'false' }};
        const hoursUntilCena = {{ $cenaData['hours_until_cena'] }};
        
        const directionsBtn = document.querySelector('button[onclick="openDirections()"]');
        if (directionsBtn) {
            if (canSeeExactLocation) {
                directionsBtn.innerHTML = '<i class="fas fa-directions me-1"></i>Cómo llegar';
                directionsBtn.className = 'btn btn-sm btn-success mt-3';
            } else if (userHasReservation) {
                const hoursLeft = Math.ceil(hoursUntilCena - 24);
                directionsBtn.innerHTML = `<i class="fas fa-clock me-1"></i>Disponible en ${hoursLeft}h`;
                directionsBtn.className = 'btn btn-sm btn-outline-warning mt-3';
            } else {
                directionsBtn.innerHTML = '<i class="fas fa-lock me-1"></i>Solo para comensales';
                directionsBtn.className = 'btn btn-sm btn-outline-secondary mt-3';
            }
        }
    @endif
});
</script>
@endsection