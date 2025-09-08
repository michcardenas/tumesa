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
                            <h2>Dashboard de Tu Negocio</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Tu Negocio</li>
                                </ol>
                            </nav>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>

                    <!-- Estadísticas Principales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card text-center">
                                <div class="card-body">
                                    <div class="stats-icon bg-primary">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <h3 class="stats-number">{{ $totalCenas }}</h3>
                                    <p class="stats-label">Experiencias Creadas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card text-center">
                                <div class="card-body">
                                    <div class="stats-icon bg-info">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <h3 class="stats-number">{{ $totalReservas }}</h3>
                                    <p class="stats-label">Total Reservas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card text-center">
                                <div class="card-body">
                                    <div class="stats-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <h3 class="stats-number">{{ $reservasPagadas }}</h3>
                                    <p class="stats-label">Reservas Pagadas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card text-center">
                                <div class="card-body">
                                    <div class="stats-icon bg-warning">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <h3 class="stats-number">${{ number_format($totalIngresos, 0, ',', '.') }}</h3>
                                    <p class="stats-label">Ingresos Totales</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Ingresos por Mes -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>Ingresos Últimos 6 Meses
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($ingresosPorMes->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Mes/Año</th>
                                                        <th class="text-end">Ingresos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($ingresosPorMes as $ingreso)
                                                    <tr>
                                                        <td>
                                                            {{ \Carbon\Carbon::createFromDate($ingreso->año, $ingreso->mes, 1)->format('M Y') }}
                                                        </td>
                                                        <td class="text-end">
                                                            <strong>${{ number_format($ingreso->total, 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-chart-line fa-2x text-muted mb-3"></i>
                                            <p class="text-muted">No hay ingresos registrados aún</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Métricas Adicionales -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-percentage me-2"></i>Tasa de Conversión
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    @php
                                        $tasaConversion = $totalReservas > 0 ? ($reservasPagadas / $totalReservas) * 100 : 0;
                                    @endphp
                                    <h3 class="text-primary">{{ number_format($tasaConversion, 1) }}%</h3>
                                    <p class="text-muted">Reservas que se convierten en pagos</p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calculator me-2"></i>Promedio por Reserva
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    @php
                                        $promedioPorReserva = $reservasPagadas > 0 ? $totalIngresos / $reservasPagadas : 0;
                                    @endphp
                                    <h3 class="text-success">${{ number_format($promedioPorReserva, 0, ',', '.') }}</h3>
                                    <p class="text-muted">Ingreso promedio por reserva pagada</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Experiencias Más Populares -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-star me-2"></i>Experiencias Más Populares
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($cenasPopulares->count() > 0)
                                        @foreach($cenasPopulares as $cena)
                                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ Str::limit($cena->title, 30) }}</h6>
                                                <small class="text-muted">
                                                    {{ $cena->formatted_date }} • {{ $cena->formatted_price }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary">{{ $cena->reservas_count }} reservas</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-utensils fa-2x text-muted mb-3"></i>
                                            <p class="text-muted">No hay experiencias creadas aún</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Reservas Recientes -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-clock me-2"></i>Reservas Recientes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($reservasRecientes->count() > 0)
                                        @foreach($reservasRecientes->take(5) as $reserva)
                                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $reserva->user->name }}</h6>
                                                <small class="text-muted">
                                                    {{ Str::limit($reserva->cena->title, 25) }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $reserva->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <div class="mb-1">
                                                    <span class="badge {{ $reserva->estado_badge['class'] }}">
                                                        {{ $reserva->estado_badge['texto'] }}
                                                    </span>
                                                </div>
                                                <small class="text-muted">{{ $reserva->precio_total_formateado }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                            <p class="text-muted">No hay reservas aún</p>
                                        </div>
                                    @endif
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

.stats-card {
    transition: transform 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
    font-size: 24px;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 5px;
    color: #2c3e50;
}

.stats-label {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.badge {
    font-size: 0.75rem;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

h6 {
    color: #2c3e50;
    font-weight: 600;
}

.border-bottom {
    border-bottom: 1px solid #dee2e6 !important;
}
</style>
@endsection