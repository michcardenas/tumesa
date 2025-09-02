@extends('layouts.app_comensal')

@section('title', 'Mis Reservas')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-history text-primary me-2"></i>
                Mi Historial de Reservas
            </h2>
            
            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-primary">{{ $estadisticas['total'] }}</h5>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-success">{{ $estadisticas['confirmadas'] }}</h5>
                            <small class="text-muted">Confirmadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-info">{{ $estadisticas['pagadas'] }}</h5>
                            <small class="text-muted">Pagadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-warning">{{ $estadisticas['canceladas'] }}</h5>
                            <small class="text-muted">Canceladas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-secondary">{{ $estadisticas['completadas'] }}</h5>
                            <small class="text-muted">Completadas</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de reservas -->
            @if($reservas->count() > 0)
                <div class="row">
                    @foreach($reservas as $reserva)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $reserva->cena->title }}</h6>
                                    <span class="badge {{ $reserva->estado_badge['class'] }}">
                                        {{ $reserva->estado_badge['texto'] }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">
                                        <i class="fas fa-user-tie me-2"></i>
                                        <strong>Chef:</strong> {{ $reserva->cena->user->name }}
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        <strong>Fecha:</strong> {{ $reserva->cena->datetime->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-users me-2"></i>
                                        <strong>Comensales:</strong> {{ $reserva->cantidad_comensales }}
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-euro-sign me-2"></i>
                                        <strong>Total:</strong> {{ $reserva->precio_total_formateado }}
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-barcode me-2"></i>
                                        <strong>Código:</strong> {{ $reserva->codigo_reserva }}
                                    </p>
                                    
                                    @if($reserva->restricciones_alimentarias)
                                        <div class="mt-2">
                                            <small class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Restricciones alimentarias
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer text-center">
                                    <a href="{{ route('cenas.show', $reserva->cena) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Ver Experiencia
                                    </a>
                                    @if($reserva->puede_cancelar)
                                        <button class="btn btn-outline-danger btn-sm ms-2">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center">
                    {{ $reservas->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No tienes reservas aún</h4>
                    <p class="text-muted">Explora nuestras experiencias gastronómicas y haz tu primera reserva</p>
                    <a href="{{ route('experiencias') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Explorar Experiencias
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection