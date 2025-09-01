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
                <i class="fas fa-utensils fa-3x"></i>
            </div>
        @endif
        <div class="hero-overlay">
            <div class="container">
                <div class="row align-items-center min-vh-50">
                    <div class="col-md-8">
                        <h1 class="hero-title">{{ $cenaData['title'] }}</h1>
                        <p class="hero-subtitle">{{ $cenaData['formatted_date'] }} • {{ $cenaData['formatted_time'] }}</p>
                        <p class="hero-location"><i class="fas fa-map-marker-alt"></i> {{ $cenaData['location'] }}</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="price-card">
                            <div class="price">{{ $cenaData['formatted_price'] }}</div>
                            <div class="per-person">por persona</div>
                            
                            @if($cenaData['can_book'])
                             <form action="{{ route('reservar') }}" method="POST">
                                @csrf
                                <button class="btn btn-reserve btn-lg w-100 mt-3">
                                    <i class="fas fa-calendar-plus"></i> Reservar Ahora
                                </button>
                            </form>

                                <small class="availability">{{ $cenaData['available_spots'] }} espacios disponibles</small>
                            @else
                                <button class="btn btn-secondary btn-lg w-100 mt-3" disabled>
                                    @if($cenaData['is_past']) Evento Finalizado @else Agotado @endif
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
        <div class="row">
            <!-- Content -->
            <div class="col-lg-8">
                <!-- Menú -->
                <section class="content-card mb-4">
                    <h3><i class="fas fa-utensils text-primary"></i> Menú</h3>
                    <p class="menu-text">{{ $cenaData['menu'] }}</p>
                </section>

                <!-- Detalles -->
                <section class="content-card">
                    <h3><i class="fas fa-info-circle text-primary"></i> Detalles</h3>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $cenaData['formatted_datetime'] }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $cenaData['guests_current'] }}/{{ $cenaData['guests_max'] }} comensales</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
          <div class="col-lg-4">
    <!-- Chef Info -->
    <div class="chef-card mb-4">
        <div class="chef-avatar mb-3">
            @if($cena->user->avatar_url)
                <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}" 
                     class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
            @else
                <i class="fas fa-user-circle fa-5x text-muted"></i>
            @endif
        </div>
        
        <h4 class="chef-name">{{ $cena->user->name ?? 'Chef Anónimo' }}</h4>
        
        @if($cena->user->especialidad)
            <p class="chef-specialty text-primary mb-2">
                <i class="fas fa-star me-1"></i>{{ $cena->user->especialidad }}
            </p>
        @endif
        
        <div class="chef-rating mb-2">
            @if($cena->user->rating > 0)
                <div class="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($cena->user->rating))
                            <i class="fas fa-star text-warning"></i>
                        @elseif($i - 0.5 <= $cena->user->rating)
                            <i class="fas fa-star-half-alt text-warning"></i>
                        @else
                            <i class="far fa-star text-warning"></i>
                        @endif
                    @endfor
                    <span class="ms-1">{{ $cena->user->formatted_rating }}</span>
                </div>
            @endif
        </div>
        
        @if($cena->user->experiencia_anos)
            <p class="chef-experience text-muted mb-2">
                <i class="fas fa-clock me-1"></i>{{ $cena->user->experience_text }}
            </p>
        @endif
        
        @if($cena->user->bio)
            <p class="chef-description">
                {{ Str::limit($cena->user->bio, 120) }}
            </p>
        @else
            <p class="chef-description">
                Especialista en crear experiencias culinarias únicas que conectan a las personas 
                a través de la comida y la conversación.
            </p>
        @endif
        
        <!-- Redes sociales -->
        @if($cena->user->instagram || $cena->user->facebook || $cena->user->website)
            <div class="chef-social mb-3">
                @if($cena->user->instagram)
                    <a href="{{ $cena->user->instagram_url }}" target="_blank" class="text-decoration-none me-2">
                        <i class="fab fa-instagram text-danger"></i>
                    </a>
                @endif
                
                @if($cena->user->facebook)
                    <a href="{{ $cena->user->facebook_url }}" target="_blank" class="text-decoration-none me-2">
                        <i class="fab fa-facebook text-primary"></i>
                    </a>
                @endif
                
                @if($cena->user->website)
                    <a href="{{ $cena->user->website }}" target="_blank" class="text-decoration-none me-2">
                        <i class="fas fa-globe text-info"></i>
                    </a>
                @endif
            </div>
        @endif
        
        <button class="btn btn-outline-primary btn-sm" onclick="showChefModal()">
            <i class="fas fa-user me-1"></i>Ver Perfil Completo
        </button>
    </div>

    <!-- Quick Stats -->
    <div class="stats-card">
        <div class="stat">
            <div class="stat-number">{{ $cenaData['formatted_price'] }}</div>
            <div class="stat-label">Precio</div>
        </div>
        <div class="stat">
            <div class="stat-number">{{ $cenaData['guests_max'] }}</div>
            <div class="stat-label">Max. Personas</div>
        </div>
        <div class="stat">
            <div class="stat-number">{{ abs($cenaData['days_until']) }}</div>
            <div class="stat-label">{{ $cenaData['days_until'] >= 0 ? 'Días' : 'Días Atrás' }}</div>
        </div>
    </div>
</div>

<!-- Modal de Reserva -->
<div class="modal fade" id="reserveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reservar {{ $cenaData['title'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-construction fa-3x text-warning mb-3"></i>
                <h4>Próximamente</h4>
                <p>El sistema de reservas estará disponible muy pronto. ¡Mantente atento!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal del Chef -->
<div class="modal fade" id="chefModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $cenaData['chef_name'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-circle fa-5x text-muted"></i>
                </div>
                <h5>Chef Anfitrión</h5>
                <p>{{ $cenaData['chef_name'] }} es un apasionado de la cocina que crea experiencias gastronómicas memorables para sus invitados.</p>
                
                <hr>
                
                <h6>Especialidades</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Cocina creativa</li>
                    <li><i class="fas fa-check text-success"></i> Experiencias íntimas</li>
                    <li><i class="fas fa-check text-success"></i> Ingredientes locales</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.hero-section {
    position: relative;
    height: 60vh;
    min-height: 400px;
    overflow: hidden;
}

.hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7));
    display: flex;
    align-items: center;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    margin-bottom: 1rem;
}

.hero-subtitle, .hero-location {
    color: rgba(255,255,255,0.9);
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.price-card {
    background: rgba(255,255,255,0.95);
    padding: 2rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.price {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2563eb;
}

.per-person {
    color: #666;
    margin-bottom: 1rem;
}

.btn-reserve {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: none;
    color: white;
    font-weight: 600;
}

.btn-reserve:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    color: white;
}

.availability {
    color: #059669;
    font-weight: 500;
    display: block;
    margin-top: 0.5rem;
}

.content-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.content-card h3 {
    color: #333;
    margin-bottom: 1rem;
}

.menu-text {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #555;
}

.detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.detail-item i {
    color: #2563eb;
    margin-right: 0.5rem;
    width: 20px;
}

.chef-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.chef-avatar {
    color: #2563eb;
    margin-bottom: 1rem;
}

.chef-card h4 {
    color: #333;
    margin-bottom: 0.5rem;
}

.chef-title {
    color: #2563eb;
    font-weight: 600;
    margin-bottom: 1rem;
}

.chef-description {
    color: #666;
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
}

.stats-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-around;
}

.stat {
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2563eb;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .price-card {
        margin-top: 2rem;
    }
    
    .stats-card {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<script>
function showReserveModal() {
    new bootstrap.Modal(document.getElementById('reserveModal')).show();
}

function showChefModal() {
    new bootstrap.Modal(document.getElementById('chefModal')).show();
}
</script>
@endsection