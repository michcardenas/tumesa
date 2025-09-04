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
        <!-- Buscador de Códigos -->
        <div class="search-section mb-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" 
                               id="codigoSearch" 
                               class="form-control form-control-lg" 
                               placeholder="🔍 Buscar por código de reserva (ej: RSV-ABC12345)"
                               autocomplete="off">
                        <button class="btn btn-primary" type="button" onclick="buscarCodigo()">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        El comensal puede mostrar su código QR o dictarte el código de reserva
                    </small>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-success btn-lg w-100" onclick="marcarTodosPresentes()">
                        <i class="fas fa-check-double"></i> Marcar Todos Presentes
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Asistencia -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $stats['total_comensales'] }}</h4>
                        <p>Total Comensales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $stats['comensales_presentes'] }}</h4>
                        <p>Presentes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="stat-icon bg-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $stats['comensales_ausentes'] }}</h4>
                        <p>Ausentes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $stats['pendientes'] }}</h4>
                        <p>Sin Marcar</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $stats['total_reservas'] }}</h4>
                        <p>Total Reservas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="stat-icon bg-secondary">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ $stats['total_comensales'] > 0 ? round(($stats['comensales_presentes'] / $stats['total_comensales']) * 100) : 0 }}%</h4>
                        <p>Asistencia</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Comensales -->
        <div class="section-header">
            <h2>Lista de Comensales</h2>
            <div class="filter-buttons">
                <button class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="all">
                    <i class="fas fa-list"></i> Todos
                </button>
                <button class="btn btn-outline-warning btn-sm filter-btn" data-filter="pendiente">
                    <i class="fas fa-clock"></i> Pendientes
                </button>
                <button class="btn btn-outline-success btn-sm filter-btn" data-filter="presente">
                    <i class="fas fa-check"></i> Presentes
                </button>
                <button class="btn btn-outline-danger btn-sm filter-btn" data-filter="ausente">
                    <i class="fas fa-times"></i> Ausentes
                </button>
            </div>
        </div>

        @if($reservas->count() > 0)
            <div class="table-container">
                <table class="table table-striped" id="reservasTable">
                    <thead>
                        <tr>
                            <th>Reserva</th>
                            <th>Contacto</th>
                            <th>Comensales</th>
                            <th>Estado Reserva</th>
                            <th>Estado Asistencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                        <tr id="reserva-{{ $reserva->id }}" 
                            class="reserva-row" 
                            data-codigo="{{ $reserva->codigo_reserva }}"
                            data-estado-asistencia="{{ $reserva->estado_asistencia }}">
                            <td>
                                <div class="codigo-reserva">
                                    <strong class="text-primary">{{ $reserva->codigo_reserva }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $reserva->created_at->format('d/m/Y H:i') }}</small>
                                </div>
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
                                <!-- <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Restricciones
                                </small> -->
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $reserva->estado_badge['class'] }}">
                                    {{ $reserva->estado_badge['texto'] }}
                                </span>
                            </td>
                            <td>
                                <div class="asistencia-status">
                                    <span class="badge asistencia-badge {{ $reserva->estado_asistencia_badge['class'] }}">
                                        {{ $reserva->estado_asistencia_badge['texto'] }}
                                    </span>
                                    @if($reserva->fecha_asistencia_marcada)
                                    <br>
                                    <small class="text-muted timestamp">
                                        <i class="fas fa-clock"></i> {{ $reserva->fecha_asistencia_marcada->format('H:i') }}
                                    </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="asistencia-controls">
                                    @if(!$reserva->asistencia_marcada)
                                        <button class="btn btn-sm btn-success asistencia-btn" 
                                                data-reserva="{{ $reserva->id }}" 
                                                data-estado="presente"
                                                data-nombre="{{ $reserva->nombre_contacto }}">
                                            <i class="fas fa-check"></i> Presente
                                        </button>
                                        <button class="btn btn-sm btn-danger asistencia-btn" 
                                                data-reserva="{{ $reserva->id }}" 
                                                data-estado="ausente"
                                                data-nombre="{{ $reserva->nombre_contacto }}">
                                            <i class="fas fa-times"></i> Ausente
                                        </button>
                                    @else
                                        <!-- <button class="btn btn-sm btn-outline-secondary" 
                                                onclick="resetearAsistencia({{ $reserva->id }})">
                                            <i class="fas fa-undo"></i> Cambiar
                                        </button> -->
                                        @if($reserva->comentarios_asistencia)
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="verComentarios('{{ addslashes($reserva->comentarios_asistencia) }}')"
                                                    title="Ver comentarios">
                                                <i class="fas fa-comment"></i>
                                            </button>
                                        @endif
                                    @endif
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

<!-- Modal para Comentarios de Asistencia -->
<div class="modal fade" id="comentariosModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-check"></i> 
                    <span id="modalTitulo">Marcar Asistencia</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Reserva:</strong> <span id="modalReserva"></span>
                </div>
                <div class="mb-3">
                    <strong>Estado:</strong> 
                    <span id="modalEstado" class="badge"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Comentarios (opcional):</label>
                    <textarea id="modalComentarios" 
                              class="form-control" 
                              rows="3" 
                              maxlength="500"
                              placeholder="Ej: Llegó 15 minutos tarde, solo vinieron 3 de 4 personas, etc."></textarea>
                    <small class="text-muted">Máximo 500 caracteres</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmarAsistencia" class="btn btn-primary">
                    <i class="fas fa-save"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Comentarios -->
<div class="modal fade" id="verComentariosModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comment"></i> Comentarios de Asistencia
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <div id="comentariosTexto"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
.search-section {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 2px solid #e2e8f0;
}

.codigo-reserva strong {
    font-size: 1.1rem;
    font-family: 'Courier New', monospace;
}

.asistencia-controls {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.asistencia-controls .btn {
    min-width: 80px;
}

.asistencia-status {
    text-align: center;
}

.asistencia-badge {
    font-size: 0.8rem;
    min-width: 80px;
}

.filter-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-btn.active {
    background-color: #2563eb;
    color: white;
    border-color: #2563eb;
}

.reserva-row.highlight {
    background-color: #fef3c7 !important;
    border: 2px solid #f59e0b;
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

    .filter-buttons {
        justify-content: center;
        margin-top: 1rem;
    }
}
</style>

<script>
let reservaActual = null;
let estadoActual = null;

document.addEventListener('DOMContentLoaded', function() {
    // Verificar que los elementos existen antes de agregar listeners
    const confirmarBtn = document.getElementById('confirmarAsistencia');
    if (confirmarBtn) {
        confirmarBtn.addEventListener('click', function() {
            if (reservaActual && estadoActual) {
                const comentarios = document.getElementById('modalComentarios').value.trim();
                marcarAsistencia(reservaActual, estadoActual, comentarios);
            }
        });
    }

    // Manejar clicks en botones de asistencia
    document.querySelectorAll('.asistencia-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reservaId = this.dataset.reserva;
            const estado = this.dataset.estado;
            const nombre = this.dataset.nombre;
            
            mostrarModalComentarios(reservaId, estado, nombre);
        });
    });

    // Manejar filtros
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            aplicarFiltro(filter);
            
            // Actualizar botón activo
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Buscar al presionar Enter
    const searchInput = document.getElementById('codigoSearch');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarCodigo();
            }
        });
    }
});

function mostrarModalComentarios(reservaId, estado, nombre) {
    reservaActual = reservaId;
    estadoActual = estado;
    
    // Verificar que los elementos existen
    const modalReserva = document.getElementById('modalReserva');
    const modalEstado = document.getElementById('modalEstado');
    const modalTitulo = document.getElementById('modalTitulo');
    const modalComentarios = document.getElementById('modalComentarios');
    
    if (!modalReserva || !modalEstado || !modalTitulo || !modalComentarios) {
        console.error('Elementos del modal no encontrados');
        alert('Error: Modal no disponible');
        return;
    }
    
    // Configurar modal
    modalReserva.textContent = nombre;
    modalEstado.textContent = estado === 'presente' ? 'Presente' : 'Ausente';
    modalEstado.className = `badge ${estado === 'presente' ? 'bg-success' : 'bg-danger'}`;
    modalTitulo.textContent = `Marcar como ${estado === 'presente' ? 'Presente' : 'Ausente'}`;
    modalComentarios.value = '';
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('comentariosModal'));
    modal.show();
}

function verComentarios(comentarios) {
    const comentariosTexto = document.getElementById('comentariosTexto');
    if (comentariosTexto) {
        comentariosTexto.textContent = comentarios;
        const modal = new bootstrap.Modal(document.getElementById('verComentariosModal'));
        modal.show();
    }
}

function buscarCodigo() {
    const searchInput = document.getElementById('codigoSearch');
    if (!searchInput) return;
    
    const codigo = searchInput.value.trim().toUpperCase();
    
    if (!codigo) {
        alert('Por favor ingresa un código de reserva');
        return;
    }

    // Limpiar highlights previos
    document.querySelectorAll('.reserva-row').forEach(row => {
        row.classList.remove('highlight');
    });

    // Buscar la fila correspondiente
    const fila = document.querySelector(`[data-codigo="${codigo}"]`);
    
    if (fila) {
        // Highlight y scroll
        fila.classList.add('highlight');
        fila.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Limpiar campo de búsqueda
        searchInput.value = '';
        
        // Mostrar notificación
        mostrarNotificacion('Reserva encontrada', 'success');
    } else {
        mostrarNotificacion('Código de reserva no encontrado', 'error');
    }
}

function aplicarFiltro(filtro) {
    const filas = document.querySelectorAll('.reserva-row');
    
    filas.forEach(fila => {
        const estadoAsistencia = fila.dataset.estadoAsistencia;
        
        if (filtro === 'all' || estadoAsistencia === filtro) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

function marcarAsistencia(reservaId, estado, comentarios = '') {
    const row = document.getElementById(`reserva-${reservaId}`);
    if (!row) {
        console.error('Fila de reserva no encontrada');
        return;
    }
    
    const controls = row.querySelector('.asistencia-controls');
    const statusDiv = row.querySelector('.asistencia-status');
    const badge = statusDiv.querySelector('.asistencia-badge');
    
    // Deshabilitar botón durante la petición
    const btnConfirmar = document.getElementById('confirmarAsistencia');
    if (btnConfirmar) {
        btnConfirmar.disabled = true;
        btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    }

    // Hacer petición AJAX
    fetch(`/chef/reservas/${reservaId}/asistencia`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            estado: estado,
            comentarios: comentarios 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('comentariosModal'));
            if (modal) modal.hide();
            
            // Actualizar UI
            badge.className = `badge asistencia-badge ${data.badge.class}`;
            badge.textContent = data.badge.texto;
            
            // Cambiar controles
            let nuevosControles = `
                <button class="btn btn-sm btn-outline-secondary" 
                        onclick="resetearAsistencia(${reservaId})">
                    <i class="fas fa-undo"></i> Cambiar
                </button>
            `;
            
            // Agregar botón de comentarios si existen
            if (data.comentarios) {
                nuevosControles += `
                    <button class="btn btn-sm btn-outline-info" 
                            onclick="verComentarios('${data.comentarios.replace(/'/g, "\\'")}'))"
                            title="Ver comentarios">
                        <i class="fas fa-comment"></i>
                    </button>
                `;
            }
            
            controls.innerHTML = nuevosControles;
            
            // Actualizar datos de la fila
            row.dataset.estadoAsistencia = estado;
            
            // Agregar timestamp si no existe
            let timestamp = statusDiv.querySelector('.timestamp');
            if (!timestamp) {
                timestamp = document.createElement('small');
                timestamp.className = 'text-muted d-block timestamp';
                statusDiv.appendChild(timestamp);
            }
            timestamp.innerHTML = `<i class="fas fa-clock"></i> ${data.fecha_marcada}`;
            
            mostrarNotificacion('Asistencia registrada correctamente', 'success');
            
            // Actualizar estadísticas después de un momento
            setTimeout(() => {
                location.reload();
            }, 1500);
            
        } else {
            mostrarNotificacion('Error al marcar asistencia: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión', 'error');
    })
    .finally(() => {
        // Rehabilitar botón
        if (btnConfirmar) {
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = '<i class="fas fa-save"></i> Confirmar';
        }
    });
}

function resetearAsistencia(reservaId) {
    if (confirm('¿Cambiar el estado de asistencia de esta reserva?')) {
        fetch(`/chef/reservas/${reservaId}/resetear-asistencia`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('Asistencia reseteada correctamente', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                mostrarNotificacion('Error al resetear asistencia: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        });
    }
}

function marcarTodosPresentes() {
    if (confirm('¿Marcar todos los comensales pendientes como presentes?')) {
        const botonesPendientes = document.querySelectorAll('.asistencia-btn[data-estado="presente"]');
        
        if (botonesPendientes.length === 0) {
            mostrarNotificacion('No hay reservas pendientes para marcar', 'info');
            return;
        }
        
        let procesados = 0;
        
        botonesPendientes.forEach((btn, index) => {
            setTimeout(() => {
                const reservaId = btn.dataset.reserva;
                marcarAsistencia(reservaId, 'presente', 'Marcado masivamente como presente');
                procesados++;
                
                if (procesados === botonesPendientes.length) {
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            }, index * 1000); // Espaciar las peticiones 1 segundo
        });
    }
}

function mostrarNotificacion(mensaje, tipo) {
    const alertClass = tipo === 'success' ? 'alert-success' : 
                     tipo === 'info' ? 'alert-info' : 'alert-danger';
    const icon = tipo === 'success' ? 'fa-check' : 
                tipo === 'info' ? 'fa-info-circle' : 'fa-exclamation-triangle';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas ${icon}"></i> ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

@endsection