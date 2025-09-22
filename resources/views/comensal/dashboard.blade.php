{{-- resources/views/comensal/dashboard.blade.php --}}
@extends('layouts.app_comensal')

@section('content')
<div class="comensal-container">
    <!-- Header Simple -->
    <div class="comensal-header">
        <div class="container-fluid">
            <h1>Panel del Comensal</h1>
            <p>Bienvenido, {{ Auth::user()->name }} - Descubre experiencias culinarias únicas</p>
        </div>
    </div>

    <!-- Resumen de Estados (NUEVO) -->
    @if(isset($resumenEstados))
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">Resumen de tus Reservas</h6>
                    <div class="d-flex gap-3 flex-wrap">
                        @if($resumenEstados['pendientes'] > 0)
                        <div class="badge bg-warning text-dark p-2">
                            <i class="fas fa-clock"></i> {{ $resumenEstados['pendientes'] }} Pendientes
                        </div>
                        @endif
                        @if($resumenEstados['confirmadas'] > 0)
                        <div class="badge bg-info p-2">
                            <i class="fas fa-check"></i> {{ $resumenEstados['confirmadas'] }} Confirmadas
                        </div>
                        @endif
                        @if($resumenEstados['completadas'] > 0)
                        <div class="badge bg-success p-2">
                            <i class="fas fa-flag-checkered"></i> {{ $resumenEstados['completadas'] }} Completadas
                        </div>
                        @endif
                        @if($resumenEstados['canceladas'] > 0)
                        <div class="badge bg-danger p-2">
                            <i class="fas fa-times"></i> {{ $resumenEstados['canceladas'] }} Canceladas
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Estadísticas Reales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ $stats['reservas_activas'] }}</h4>
                    <p>Cenas Reservadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ $stats['cenas_disfrutadas'] }}</h4>
                    <p>Cenas Disfrutadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ $stats['chefs_favoritos'] }}</h4>
                    <p>Chefs Favoritos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h4>${{ number_format($stats['gastado_mes'], 0, ',', '.') }}</h4>
                    <p>Gastado Este Mes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Próximas Reservas -->
    <div class="section-header">
        <h2>Tus Próximas Cenas</h2>
        <a href="{{ route('experiencias') }}" class="btn btn-primary">
            <i class="fas fa-search"></i> Buscar Más Cenas
        </a>
    </div>

    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Chef</th>
                    <th>Título</th>
                    <th>Ubicación</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proximasReservas as $reserva)
                <tr>
                    <td>
                        <strong>{{ $reserva->cena->datetime->format('D, j M') }}</strong><br>
                        <small class="text-muted">{{ $reserva->cena->datetime->format('g:i A') }}</small>
                    </td>
                    <td>
                        <div class="chef-info">
                            <strong>{{ $reserva->cena->chef->name }}</strong>
                        </div>
                    </td>
                    <td>
                        <strong>{{ $reserva->cena->title }}</strong><br>
                        <small class="text-muted">{{ Str::limit($reserva->cena->menu, 40) }}</small>
                    </td>
                    <td>
                        <small>{{ Str::limit($reserva->cena->location, 25) }}</small>
                    </td>
                    <td>
                        <strong>{{ $reserva->precio_total_formateado }}</strong>
                        @if($reserva->cantidad_comensales > 1)
                            <br><small class="text-muted">{{ $reserva->cantidad_comensales }} comensales</small>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = '';
                            $badgeText = '';
                            $iconClass = '';
                            
                            switch($reserva->estado) {
                                case 'pendiente':
                                    $badgeClass = 'bg-warning';
                                    $badgeText = 'Pendiente';
                                    $iconClass = 'fas fa-clock';
                                    break;
                                case 'confirmada':
                                    $badgeClass = 'bg-info';
                                    $badgeText = 'Confirmada';
                                    $iconClass = 'fas fa-check';
                                    break;
                                case 'pagada':
                                    $badgeClass = 'bg-success';
                                    $badgeText = 'Pagada';
                                    $iconClass = 'fas fa-check-double';
                                    break;
                                default:
                                    $badgeClass = 'bg-secondary';
                                    $badgeText = ucfirst($reserva->estado);
                                    $iconClass = 'fas fa-info';
                            }
                        @endphp
                        
                        <span class="badge {{ $badgeClass }} d-flex align-items-center gap-1">
                            <i class="{{ $iconClass }}"></i> {{ $badgeText }}
                        </span>
                        
                        @if($reserva->estado_pago === 'pendiente')
                            <small class="text-danger d-block mt-1">
                                <i class="fas fa-exclamation-triangle"></i> Pago pendiente
                            </small>
                        @endif
                    </td>
                  <td>
                        <div class="action-buttons">
                            <a href="{{ route('reservas.detalle', $reserva->id) }}" 
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($reserva->puede_cancelar)
                                <button class="btn btn-sm btn-outline-danger" title="Cancelar reserva" onclick="cancelarReserva({{ $reserva->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif

                            @if($reserva->estado_pago === 'pendiente')
                                <button class="btn btn-sm btn-outline-success" title="Completar pago" onclick="completarPago({{ $reserva->id }})">
                                    <i class="fas fa-credit-card"></i>
                                </button>
                            @endif

                         {{-- ✅ Nuevo: botón para dejar reseña si está completada y no hay reseña aún --}}
                                @if($reserva->estado === 'completada' && !$reserva->reseña)
                                    <a href="{{ route('reseñas.create', ['cena' => $reserva->cena->id, 'reserva' => $reserva->id]) }}"
                                    class="btn btn-sm btn-warning" title="Dejar reseña">
                                        <i class="fas fa-star"></i>
                                    </a>
                                @endif

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-calendar-times fa-2x mb-3 text-muted"></i>
                        <br>
                        <strong>No tienes reservas próximas</strong>
                        <br>
                        <small class="text-muted">Explora las cenas disponibles y haz tu primera reserva</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Historial de Reservas Pasadas (NUEVO) -->
    @if(isset($reservasPasadas) && $reservasPasadas->count() > 0)
    <div class="section-header mt-5">
        <h2>Historial de Cenas</h2>
        <span class="badge bg-info">{{ $reservasPasadas->count() }} cenas en total</span>
    </div>

    <div class="table-container">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Cena</th>
                    <th>Chef</th>
                    <th>Estado</th>
                    <th>Total Pagado</th>
                    <th>Calificación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservasPasadas as $reserva)
                <tr class="{{ $reserva->estado === 'cancelada' ? 'table-danger opacity-75' : '' }}">
                    <td>
                        <small>{{ $reserva->cena->datetime->format('d/m/Y') }}</small>
                    </td>
                    <td>
                        <strong>{{ Str::limit($reserva->cena->title, 30) }}</strong>
                    </td>
                    <td>
                        {{ $reserva->cena->chef->name }}
                    </td>
                    <td>
                        @if($reserva->estado === 'completada')
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Asististe
                            </span>
                        @elseif($reserva->estado === 'cancelada')
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle"></i> Cancelada
                            </span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($reserva->estado) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($reserva->estado_pago === 'pagado')
                            <strong class="text-success">{{ $reserva->precio_total_formateado }}</strong>
                        @elseif($reserva->estado_pago === 'reembolsado')
                            <span class="text-info">Reembolsado</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($reserva->calificacion)
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $reserva->calificacion)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        @elseif($reserva->estado === 'completada')
                            <button class="btn btn-sm btn-outline-warning" onclick="calificarCena({{ $reserva->id }})">
                                <i class="fas fa-star"></i> Calificar
                            </button>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                  <button class="btn btn-sm btn-outline-primary" 
                        onclick="window.location.href='{{ route('reservas.detalle', $reserva->id) }}'">
                    <i class="fas fa-eye"></i>
                </button>

                        @if($reserva->estado === 'completada' && !$reserva->resena)
                            <button class="btn btn-sm btn-outline-success" onclick="escribirResena({{ $reserva->id }})">
                                <i class="fas fa-pen"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Resto de las secciones (Cenas Disponibles, etc.) -->
    <!-- ... código existente ... -->

</div>
     <!-- Sección Cenas Disponibles -->
             <!-- Sección Cenas Disponibles -->
<div id="cenas-disponibles-section" class="content-section">
    <div class="section-header">
        <h2>Cenas Disponibles</h2>
        <div class="search-filters">
            <select class="form-select form-select-sm" id="filterTipoCocina">
                <option value="">Todos los tipos</option>
                <option value="italiana">Italiana</option>
                <option value="francesa">Francesa</option>
                <option value="asiatica">Asiática</option>
                <option value="colombiana">Colombiana</option>
                <option value="mediterranea">Mediterránea</option>
                <option value="fusion">Fusión</option>
            </select>
        </div>
    </div>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Explora todas las cenas disponibles y reserva tu lugar en experiencias culinarias exclusivas.
    </div>

    <!-- Lista de cenas disponibles con datos reales -->
    <div class="row" id="cenasGrid">
        @forelse($cenasDisponibles as $cena)
        @php
            $esEnCurso = $cena->status === 'in_progress';
            $yaInicio = $cena->datetime <= now();
        @endphp
        
     <div class="col-md-6 mb-4 cena-item" data-tipo="{{ strtolower($cena->tipo_cocina ?? '') }}">
    <div class="available-dinner-card {{ $esEnCurso ? 'border-warning' : '' }}">
        <div class="dinner-image position-relative">
            @if($cena->cover_image_url)
                <img src="{{ $cena->cover_image_url }}" alt="{{ $cena->title }}">
            @else
                <img src="https://via.placeholder.com/400x200?text={{ urlencode($cena->title) }}" alt="{{ $cena->title }}">
            @endif
            
            <div class="price-tag">{{ $cena->formatted_price }}</div>
            
            @if($esEnCurso)
                <div class="badge bg-success position-absolute top-0 start-0 m-2">
                    <i class="fas fa-circle text-white me-1" style="animation: pulse 2s infinite;"></i>
                    EN CURSO
                </div>
            @endif

            @if($cena->is_full)
                <div class="full-badge">
                    <i class="fas fa-users"></i> Completa
                </div>
            @else
                <div class="available-spots">
                    {{ $cena->available_spots }} lugares disponibles
                </div>
            @endif
        </div>
        
        <div class="dinner-content">
            <div class="dinner-header">
                <h5>{{ $cena->title }}</h5>
                <div class="chef-rating">
                    <small>Chef: {{ $cena->chef->name }}</small>
                    <span class="rating">⭐ 4.8</span>
                </div>
            </div>
            
            <p class="dinner-description">{{ Str::limit($cena->menu, 80) }}</p>
            
            <div class="dinner-details">
                <span><i class="fas fa-calendar"></i> {{ $cena->datetime->format('D, j M') }}</span>
                <span><i class="fas fa-clock"></i> {{ $cena->datetime->format('g:i A') }}</span>
                @if($esEnCurso)
                    <span class="text-success fw-bold">
                        <i class="fas fa-play-circle"></i> En progreso
                    </span>
                @else
                    <span><i class="fas fa-users"></i> {{ $cena->guests_current }}/{{ $cena->guests_max }} lugares</span>
                @endif
                @if($cena->location)
                    <span><i class="fas fa-map-marker-alt"></i> {{ Str::limit($cena->location, 20) }}</span>
                @endif
            </div>
            
            @if($esEnCurso)
                <div class="alert alert-warning p-2 mt-2 mb-0">
                    <small><i class="fas fa-info-circle"></i> Esta cena ya comenzó. No se admiten nuevas reservas.</small>
                </div>
            @elseif($yaInicio)
                <button class="btn btn-secondary btn-sm w-100 mt-2" disabled>
                    <i class="fas fa-clock"></i> Cena ya iniciada
                </button>
            @elseif($cena->is_full)
                <button class="btn btn-secondary btn-sm w-100 mt-2" disabled>
                    <i class="fas fa-users"></i> Cena Completa
                </button>
            @else
                <button class="btn btn-primary btn-sm w-100 mt-2" onclick="reservarCena({{ $cena->id }})">
                    <i class="fas fa-calendar-plus"></i> Reservar Ahora
                </button>
            @endif
        </div>
    </div>
</div>

        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-utensils fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay cenas disponibles</h4>
                <p class="text-muted">Vuelve pronto para descubrir nuevas experiencias culinarias</p>
                <a href="{{ route('ser-chef') ?? '#' }}" class="btn btn-outline-primary">
                    <i class="fas fa-chef-hat"></i> ¿Quieres ser chef anfitrión?
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
                    <!-- Sección Historial -->
                    <div id="historial-section" class="content-section">
                        <div class="section-header">
                            <h2>Historial de Cenas</h2>
                        </div>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-trophy"></i>
                            ¡Has disfrutado de 12 cenas increíbles! Continúa explorando nuevos sabores y chefs talentosos.
                        </div>

                        <!-- Lista de cenas pasadas -->
                        <div class="history-list">
                            <div class="history-item">
                                <div class="history-date">
                                    <span class="date">15</span>
                                    <span class="month">Jul</span>
                                </div>
                                <div class="history-content">
                                    <h6>Parrillada Argentina Premium</h6>
                                    <p class="text-muted mb-1">Chef: Roberto Silva</p>
                                    <div class="rating mb-1">
                                        <span class="text-warning">⭐⭐⭐⭐⭐</span>
                                        <small class="text-muted">Tu calificación</small>
                                    </div>
                                    <small class="text-success">$62.000 - Pagado</small>
                                </div>
                                <div class="history-actions">
                                    <button class="btn btn-sm btn-outline-primary">Ver Reseña</button>
                                </div>
                            </div>

                            <div class="history-item">
                                <div class="history-date">
                                    <span class="date">08</span>
                                    <span class="month">Jul</span>
                                </div>
                                <div class="history-content">
                                    <h6>Cocina Mediterránea Auténtica</h6>
                                    <p class="text-muted mb-1">Chef: Elena Vásquez</p>
                                    <div class="rating mb-1">
                                        <span class="text-warning">⭐⭐⭐⭐</span>
                                        <small class="text-muted">Tu calificación</small>
                                    </div>
                                    <small class="text-success">$55.000 - Pagado</small>
                                </div>
                                <div class="history-actions">
                                    <button class="btn btn-sm btn-outline-primary">Ver Reseña</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Favoritos -->
                    <div id="favoritos-section" class="content-section">
                        <div class="section-header">
                            <h2>Tus Chefs Favoritos</h2>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="favorite-chef-card">
                                    <div class="chef-avatar">
                                        <i class="fas fa-user-circle fa-3x"></i>
                                    </div>
                                    <div class="chef-info">
                                        <h5>María González</h5>
                                        <p class="specialty">Especialista en Cocina Italiana</p>
                                        <div class="chef-stats">
                                            <span class="rating">⭐ 4.9 (127 reseñas)</span>
                                            <span class="experience">8 años experiencia</span>
                                        </div>
                                        <div class="chef-actions mt-2">
                                            <button class="btn btn-sm btn-primary">Ver Perfil</button>
                                            <button class="btn btn-sm btn-outline-secondary">Mensaje</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="favorite-chef-card">
                                    <div class="chef-avatar">
                                        <i class="fas fa-user-circle fa-3x"></i>
                                    </div>
                                    <div class="chef-info">
                                        <h5>Carlos Mendoza</h5>
                                        <p class="specialty">Experto en Brunch y Desayunos</p>
                                        <div class="chef-stats">
                                            <span class="rating">⭐ 4.8 (89 reseñas)</span>
                                            <span class="experience">5 años experiencia</span>
                                        </div>
                                        <div class="chef-actions mt-2">
                                            <button class="btn btn-sm btn-primary">Ver Perfil</button>
                                            <button class="btn btn-sm btn-outline-secondary">Mensaje</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Editar Perfil -->
                    <div id="perfil-section" class="content-section">
                        <div class="section-header">
                            <h2>Mi Perfil de Comensal</h2>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <form class="profile-form">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" value="{{ Auth::user()->name }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" placeholder="+57 300 123 4567">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Preferencias Alimentarias</label>
                                        <div class="preferences-grid">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="vegetariano">
                                                <label class="form-check-label" for="vegetariano">Vegetariano</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="vegano">
                                                <label class="form-check-label" for="vegano">Vegano</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="gluten-free">
                                                <label class="form-check-label" for="gluten-free">Sin Gluten</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sin-lactosa">
                                                <label class="form-check-label" for="sin-lactosa">Sin Lactosa</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alergias o Restricciones</label>
                                        <textarea class="form-control" rows="3" placeholder="Menciona cualquier alergia o restricción alimentaria que tengamos que conocer..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <div class="profile-photo-section">
                                    <h5>Foto de Perfil</h5>
                                    <div class="photo-placeholder">
                                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm mt-2">
                                        <i class="fas fa-camera"></i> Cambiar Foto
                                    </button>
                                    
                                    <div class="profile-stats mt-4">
                                        <h6>Tus Estadísticas</h6>
                                        <div class="stat-item">
                                            <span class="stat-label">Miembro desde:</span>
                                            <span class="stat-value">Enero 2025</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Cenas disfrutadas:</span>
                                            <span class="stat-value">12</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Puntuación promedio:</span>
                                            <span class="stat-value">⭐ 4.8</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Comensal Container */
    .comensal-container {
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    /* Comensal Header */
    .comensal-header {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: white;
        padding: 1rem 0;
        margin-bottom: 0;
    }

    .comensal-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .comensal-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .comensal-content {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    /* Section Header */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #dee2e6;
    }

    .section-header h2 {
        margin: 0;
        color: #495057;
        font-size: 1.5rem;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        margin-right: 1rem;
    }

    .stat-info h4 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: bold;
        color: #495057;
    }

    .stat-info p {
        margin: 0;
        color: #6c757d;
        font-size: 0.9rem;
    }

    /* Recommended Dinner Cards */
    .recommended-dinner-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .recommended-dinner-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }

    .dinner-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .dinner-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .price-tag {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #2563eb;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .dinner-content {
        padding: 1.25rem;
    }

    .dinner-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .dinner-header h5 {
        margin: 0;
        color: #1f2937;
        font-weight: 600;
    }

    .chef-rating {
        text-align: right;
    }

    .chef-rating small {
        display: block;
        color: #6b7280;
    }

    .rating {
        color: #f59e0b;
        font-weight: 500;
    }

    .dinner-description {
        color: #6b7280;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .dinner-details {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .dinner-details span {
        color: #6b7280;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .dinner-details i {
        color: #2563eb;
    }

    /* Reservation Stats Cards */
    .reservation-stats-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-align: center;
        margin-bottom: 1rem;
    }

    .reservation-stats-card h5 {
        color: #6c757d;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .reservation-stats-card h2 {
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    /* Table Container */
    .table-container {
        overflow-x: auto;
        background: white;
        border-radius: 8px;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .table td {
        vertical-align: middle;
    }

    /* Chef Info in Tables */
    .chef-info strong {
        color: #2563eb;
        font-size: 0.95rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.25rem;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
    }

    /* History List */
    .history-list {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
    }

    .history-item {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .history-item:last-child {
        margin-bottom: 0;
    }

    .history-date {
        text-align: center;
        min-width: 60px;
        background: #f1f5f9;
        border-radius: 8px;
        padding: 0.5rem;
    }

    .history-date .date {
        display: block;
        font-size: 1.5rem;
        font-weight: bold;
        color: #2563eb;
        line-height: 1;
    }

    .history-date .month {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    .history-content {
        flex: 1;
    }

    .history-content h6 {
        margin: 0 0 0.25rem 0;
        color: #1f2937;
        font-weight: 600;
    }

    .history-content p {
        margin: 0;
        font-size: 0.85rem;
    }

    .history-actions {
        margin-left: auto;
    }

    /* Favorite Chef Cards */
    .favorite-chef-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }

    .favorite-chef-card:hover {
        transform: translateY(-2px);
    }

    .chef-avatar {
        color: #2563eb;
        margin-bottom: 1rem;
    }

    .chef-info h5 {
        margin-bottom: 0.25rem;
        color: #1f2937;
        font-weight: 600;
    }

    .specialty {
        color: #6b7280;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .chef-stats {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .chef-stats span {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .chef-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    /* Profile Section */
    .profile-form {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
    }

    .profile-photo-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
    }

    .photo-placeholder {
        margin: 1rem 0;
    }

    .profile-stats {
        background: white;
        border-radius: 8px;
        padding: 1rem;
    }

    .profile-stats h6 {
        margin-bottom: 1rem;
        color: #374151;
        font-weight: 600;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .stat-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .stat-value {
        color: #374151;
        font-weight: 500;
        font-size: 0.9rem;
    }

    /* Preferences Grid */
    .preferences-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .form-check {
        margin-bottom: 0.5rem;
    }

    /* Search Filters */
    .search-filters {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .search-filters .form-select {
        max-width: 200px;
    }

    /* Badges */
    .badge {
        font-size: 0.75rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .comensal-content {
            margin-top: 1rem;
        }
        
        .section-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .stat-card {
            flex-direction: column;
            text-align: center;
        }
        
        .stat-icon {
            margin-right: 0;
            margin-bottom: 1rem;
        }

        .dinner-details {
            flex-direction: column;
            gap: 0.5rem;
        }

        .chef-stats {
            flex-direction: column;
            gap: 0.25rem;
        }

        .chef-actions {
            flex-direction: column;
        }

        .preferences-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Colores personalizados */
    .btn-primary {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
    }

    .btn-primary:hover {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
    }

    .text-primary {
        color: #2563eb !important;
    }

    .stat-icon.bg-primary {
        background-color: #2563eb !important;
    }

    .stat-icon.bg-success {
        background-color: #059669 !important;
    }

    .stat-icon.bg-warning {
        background-color: #d97706 !important;
    }

    .stat-icon.bg-danger {
        background-color: #dc2626 !important;
    }

    /* Alerts */
    .alert {
        border: none;
        border-radius: 6px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar secciones
    window.showSection = function(sectionName) {
        // Ocultar todas las secciones
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Remover active de todos los links
        document.querySelectorAll('.menu-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Mostrar la sección seleccionada
        const targetSection = document.getElementById(sectionName + '-section');
        if (targetSection) {
            targetSection.classList.add('active');
        } else {
            // Si no existe la sección, mostrar dashboard
            document.getElementById('dashboard-section').classList.add('active');
        }
        
        // Activar el link correspondiente
        event.target.closest('.menu-link').classList.add('active');
    };
    
    // Manejar clicks en sidebar
    document.querySelectorAll('.menu-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remover active de todos
            document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active'));
            
            // Agregar active al clickeado
            this.classList.add('active');
        });
    });
    
    // Form submission para perfil
    document.querySelector('.profile-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('¡Perfil actualizado exitosamente! (Esta es una demo)');
    });

    // Eventos para botones de reserva
    document.querySelectorAll('.recommended-dinner-card .btn-primary').forEach(button => {
        button.addEventListener('click', function() {
            alert('Funcionalidad de reserva próximamente disponible');
        });
    });

    document.querySelectorAll('.btn-outline-primary, .btn-outline-secondary, .btn-outline-success')
  .forEach(button => {
      // No agregar event listener si el botón ya tiene onclick definido
      if (!button.hasAttribute('onclick')) {
          button.addEventListener('click', function(e) {
              if (this.title) {
                  alert(`Función "${this.title}" próximamente disponible`);
              } else {
                  alert('Funcionalidad próximamente disponible');
              }
          });
      }
  });

}); // Cierre del DOMContentLoaded

// Función para reservar cena
function reservarCena(cenaId) {
    // Redirigir directamente al checkout
    window.location.href = `/comensal/checkout/${cenaId}`;
}

// Función para completar pago de una reserva existente
function completarPago(reservaId) {
    // Redirigir al checkout con la reserva existente
    window.location.href = `/comensal/completar-pago/${reservaId}`;
}
</script>
@endsection