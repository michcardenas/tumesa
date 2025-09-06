@extends('layouts.app_chef')

@section('content')
<div class="section-header">
    <h2>Gestión de Ingresos</h2>
</div>

<!-- Tarjetas de resumen -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="income-card">
            <h5>Ingresos Este Mes</h5>
            <h2 class="text-success">${{ number_format($ingresos['mes'] ?? 0, 0, ',', '.') }}</h2>
            <small class="text-muted">{{ $ingresos['cenas_mes'] ?? 0 }} cenas realizadas</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="income-card">
            <h5>Ingresos Pendientes</h5>
            <h2 class="text-warning">${{ number_format($ingresos['pendientes'] ?? 0, 0, ',', '.') }}</h2>
            <small class="text-muted">{{ $ingresos['cenas_pendientes'] ?? 0 }} cenas por cobrar</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="income-card">
            <h5>Total Acumulado</h5>
            <h2 class="text-primary">${{ number_format($ingresos['total'] ?? 0, 0, ',', '.') }}</h2>
            <small class="text-muted">Desde el inicio</small>
        </div>
    </div>
</div>

<!-- Indicador de crecimiento -->
<div class="alert {{ $ingresos['crecimiento'] >= 0 ? 'alert-success' : 'alert-warning' }} mb-4">
    <i class="fas {{ $ingresos['crecimiento'] >= 0 ? 'fa-chart-line' : 'fa-chart-line-down' }}"></i>
    <strong>Estado financiero:</strong> 
    @if($ingresos['crecimiento'] >= 0)
        Tus ingresos han aumentado un {{ $ingresos['crecimiento'] }}% este mes.
    @else
        Tus ingresos han disminuido un {{ abs($ingresos['crecimiento']) }}% este mes.
    @endif
</div>

<!-- Resumen de reservas -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Resumen de Reservas</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-success">{{ $resumenReservas['pagadas'] }}</h4>
                        <p class="text-muted">Reservas Pagadas</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-warning">{{ $resumenReservas['pendientes'] }}</h4>
                        <p class="text-muted">Reservas Pendientes</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-danger">{{ $resumenReservas['canceladas'] }}</h4>
                        <p class="text-muted">Reservas Canceladas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historial mensual -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Historial de Ingresos (Últimos 6 meses)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Reservas</th>
                                <th>Ingresos</th>
                                <th>Promedio por Reserva</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historialMeses as $mes)
                                <tr>
                                    <td><strong>{{ $mes['mes'] }}</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $mes['reservas'] }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success">${{ number_format($mes['ingresos'], 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @if($mes['reservas'] > 0)
                                            <span class="text-muted">${{ number_format($mes['ingresos'] / $mes['reservas'], 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">$0</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay datos disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Últimas reservas -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Últimas Reservas Pagadas</h5>
                <small class="text-muted">Últimas 10 transacciones</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cena</th>
                                <th>Cliente</th>
                                <th>Comensales</th>
                                <th>Monto</th>
                                <th>Fecha Pago</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasReservas as $reserva)
                                <tr>
                                    <td>
                                        <code>{{ $reserva->codigo_reserva }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ Str::limit($reserva->cena->title, 30) }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $reserva->cena->formatted_date }}</small>
                                    </td>
                                    <td>
                                        {{ $reserva->nombre_contacto }}
                                        <br>
                                        <small class="text-muted">{{ $reserva->email_contacto }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $reserva->cantidad_comensales }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($reserva->precio_total, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        {{ $reserva->fecha_pago ? $reserva->fecha_pago->format('d/m/Y') : '-' }}
                                        <br>
                                        <small class="text-muted">{{ $reserva->fecha_pago ? $reserva->fecha_pago->format('H:i') : '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $reserva->estado_pago_badge['class'] }}">
                                            {{ $reserva->estado_pago_badge['texto'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-receipt fa-3x mb-3"></i>
                                            <p>No tienes reservas pagadas aún</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.income-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    margin-bottom: 20px;
}

.income-card h5 {
    color: #6c757d;
    margin-bottom: 10px;
    font-size: 14px;
    font-weight: 600;
}

.income-card h2 {
    margin-bottom: 5px;
    font-weight: bold;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.badge {
    font-size: 12px;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
}
</style>
@endpush