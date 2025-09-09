{{-- ============================================ --}}
{{-- RUTAS (agregar en routes/web.php) --}}
{{-- ============================================ --}}

{{-- 
Route::get('/admin/cenas', [App\Http\Controllers\AdminCenasController::class, 'index'])->name('admin.cenas');
Route::get('/admin/cenas/create', [App\Http\Controllers\AdminCenasController::class, 'create'])->name('admin.cenas.create');
Route::get('/admin/cenas/{cena}/edit', [App\Http\Controllers\AdminCenasController::class, 'edit'])->name('admin.cenas.edit');
Route::delete('/admin/cenas/{cena}', [App\Http\Controllers\AdminCenasController::class, 'destroy'])->name('admin.cenas.destroy');
--}}

{{-- ============================================ --}}
{{-- VISTA: resources/views/admin/cenas/index.blade.php --}}
{{-- ============================================ --}}

@extends('layouts.admin')

@section('title', 'Gestión de Cenas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Cenas</h1>
            <p class="text-muted">Administra todas las experiencias gastronómicas</p>
        </div>
        <a href="{{ route('admin.cenas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Cena
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-utensils text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Total Cenas</h6>
                            <h4 class="mb-0">{{ $cenas->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-calendar-check text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Próximas</h6>
                            <h4 class="mb-0">{{ $cenas->where('datetime', '>', now())->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Finalizadas</h6>
                            <h4 class="mb-0">{{ $cenas->where('datetime', '<', now())->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Chefs Activos</h6>
                            <h4 class="mb-0">{{ $cenas->pluck('user_id')->unique()->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($cenas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4">Cena</th>
                                <th class="border-0">Chef</th>
                                <th class="border-0">Fecha</th>
                                <th class="border-0">Precio</th>
                                <th class="border-0">Capacidad</th>
                                <th class="border-0">Estado</th>
                                <th class="border-0 text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cenas as $cena)
                                <tr>
                                    <td class="ps-4">
                                        <div>
                                            <h6 class="mb-1">{{ $cena->title }}</h6>
                                            <small class="text-muted">{{ Str::limit($cena->location, 30) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($cena->user)
                                                <div class="avatar avatar-sm rounded-circle bg-primary text-white me-2">
                                                    {{ strtoupper(substr($cena->user->name, 0, 1)) }}
                                                </div>
                                                <span>{{ $cena->user->name }}</span>
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div>{{ $cena->datetime->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $cena->datetime->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">${{ number_format($cena->price, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $cena->guests_current ?? 0 }}/{{ $cena->guests_max }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($cena->datetime > now())
                                            <span class="badge bg-success">Próxima</span>
                                        @else
                                            <span class="badge bg-secondary">Finalizada</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.cenas.edit', $cena) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $cena->id }}"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $cena->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Estás seguro de que deseas eliminar la cena <strong>"{{ $cena->title }}"</strong>?</p>
                                                <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('admin.cenas.destroy', $cena) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-utensils text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted">No hay cenas registradas</h5>
                    <p class="text-muted">Comienza creando tu primera experiencia gastronómica.</p>
                    <a href="{{ route('admin.cenas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Primera Cena
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}

.card {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.04);
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection