{{-- resources/views/chef/asistencia.blade.php --}}
@extends('layouts.app_chef')

@section('content')
<div class="chef-container">
    <!-- Header -->
    <div class="chef-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Control de Asistencia</h1>
                    <p>{{ $cena->title }} - {{ $cena->formatted_date }}</p>
                </div>
                <a href="{{ route('chef.dashboard') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="chef-content">
        <!-- Información de la Cena -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $reservas->sum('cantidad_comensales') }}</h4>
                        <p>Total Comensales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $reservas->count() }}</h4>
                        <p>Reservas Confirmadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $cena->formatted_date }}</h4>
                        <p>Fecha y Hora</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Comensales -->
        <div class="section-header">
            <h2>Lista de Comensales</h2>
            <div>
                <button class="btn btn-success" onclick="marcarTodosPresentes()">
                    <i class="fas fa-check-double"></i> Marcar Todos Presentes
                </button>
            </div>
        </div>

        @if($reservas->count() > 0)
            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Reserva</th>
                            <th>Contacto</th>
                            <th>Comensales</th>
                            <th>Estado</th>
                            <th>Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                        <tr id="reserva-{{ $reserva->id }}">
                            <td>
                                <strong>{{ $reserva->codigo_reserva }}</strong>
                                <br>
                                <small class="text-muted">{{ $reserva->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $reserva->nombre_contacto }}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-phone"></i> {{ $reserva->telefono_contacto }}
                                </small>
                                @if($reserva->email_contacto)
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-envelope"></i> {{ $reserva->email_contacto }}
                                </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $reserva->cantidad_comensales }} personas</span>
                                @if($reserva->restricciones_alimentarias)
                                <br>
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Restricciones
                                </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $reserva->estado_badge['class'] }}">
                                    {{ $reserva->estado_badge['texto'] }}
                                </span>
                            </td>
                            <td>
                                <div class="asistencia-controls">
                                    <button class="btn btn-sm btn-success asistencia-btn" 
                                            data-reserva="{{ $reserva->id }}" 
                                            data-estado="presente">
                                        <i class="fas fa-check"></i> Presente
                                    </button>
                                    <button class="btn btn-sm btn-danger asistencia-btn" 
                                            data-reserva="{{ $reserva->id }}" 
                                            data-estado="ausente">
                                        <i class="fas fa-times"></i> Ausente
                                    </button>
                                </div>
                                <div class="asistencia-status d-none mt-2">
                                    <span class="badge"></span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>No hay reservas confirmadas</strong>
                <br>
                Aún no hay comensales confirmados para esta cena.
            </div>
        @endif
    </div>
</div>

<style>
.asistencia-controls {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.asistencia-controls .btn {
    min-width: 80px;
}

.asistencia-status .badge {
    font-size: 0.75rem;
}

.stat-card {
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .asistencia-controls {
        flex-direction: column;
    }
    
    .asistencia-controls .btn {
        width: 100%;
        margin-bottom: 0.25rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clicks en botones de asistencia
    document.querySelectorAll('.asistencia-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reservaId = this.dataset.reserva;
            const estado = this.dataset.estado;
            marcarAsistencia(reservaId, estado);
        });
    });
});

function marcarAsistencia(reservaId, estado) {
    const row = document.getElementById(`reserva-${reservaId}`);
    const controls = row.querySelector('.asistencia-controls');
    const status = row.querySelector('.asistencia-status');
    const badge = status.querySelector('.badge');
    
    // Actualizar UI inmediatamente
    controls.style.display = 'none';
    status.classList.remove('d-none');
    
    if (estado === 'presente') {
        badge.className = 'badge bg-success';
        badge.innerHTML = '<i class="fas fa-check"></i> Presente';
    } else {
        badge.className = 'badge bg-danger';
        badge.innerHTML = '<i class="fas fa-times"></i> Ausente';
    }
    
    // Aquí puedes agregar la llamada AJAX al servidor
    console.log(`Reserva ${reservaId} marcada como ${estado}`);
    
    // Simular éxito
    setTimeout(() => {
        // Mostrar confirmación
        const notification = document.createElement('div');
        notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-check"></i> Asistencia registrada correctamente
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }, 500);
}

function marcarTodosPresentes() {
    if (confirm('¿Marcar todos los comensales como presentes?')) {
        document.querySelectorAll('.asistencia-btn[data-estado="presente"]').forEach(btn => {
            if (!btn.closest('.asistencia-controls').style.display) {
                btn.click();
            }
        });
    }
}
</script>
@endsection