@extends('layouts.app_chef')

@section('content')
<div class="section-header">
    <h2>Gestión de Ingresos</h2>
</div>

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

<div class="alert alert-success">
    <i class="fas fa-chart-line"></i>
    <strong>Estado financiero</strong> Tus ingresos han aumentado un {{ $ingresos['crecimiento'] }}% este mes.
</div>
@endsection