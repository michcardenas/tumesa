{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="admin-container">
    <!-- Header Simple -->
    <div class="admin-header">
        <div class="container-fluid">
            <h1>Panel de Administración</h1>
            <p>Bienvenido, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="admin-sidebar">
                    <ul class="admin-menu">
                        <li class="menu-item">
                            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                                <i class="fas fa-file-alt"></i>
                                Gestión de Páginas
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link disabled">
                                <i class="fas fa-users"></i>
                                Usuarios
                                <span class="coming-soon">Próximamente</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link disabled">
                                <i class="fas fa-utensils"></i>
                                Experiencias
                                <span class="coming-soon">Próximamente</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-9">
                <div class="admin-content">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Gestión de Páginas</li>
                        </ol>
                    </nav>

                    <!-- Contenido de Gestión de Páginas -->
                    <div class="content-section">
                        <div class="section-header">
                            <h2>Gestión de Páginas</h2>
                           
                        </div>

                        <!-- Tabla de Páginas -->
                        <div class="table-container">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Slug</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $pages = [
                                            ['id' => 1, 'title' => 'Experiencias', 'slug' => 'experiencias', 'status' => 'Publicada'],
                                            ['id' => 2, 'title' => 'Ser Chef Anfitrión', 'slug' => 'ser-chef', 'status' => 'Publicada'],
                                            ['id' => 3, 'title' => 'Cómo Funciona', 'slug' => 'como-funciona', 'status' => 'Borrador'],
                                        ];
                                    @endphp

                                    @foreach($pages as $page)
                                    <tr>
                                        <td>{{ $page['id'] }}</td>
                                        <td>{{ $page['title'] }}</td>
                                        <td>
                                            <code>/{{ $page['slug'] }}</code>
                                        </td>
                                        <td>
                                            @if($page['status'] == 'Publicada')
                                                <span class="badge bg-success">{{ $page['status'] }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ $page['status'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<style>
/* Admin Container */
.admin-container {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Admin Header */
.admin-header {
    background: #343a40;
    color: white;
    padding: 1rem 0;
    margin-bottom: 0;
}

.admin-header h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.admin-header p {
    margin: 0;
    opacity: 0.8;
    font-size: 0.9rem;
}

/* Sidebar */
.admin-sidebar {
    background: white;
    border-radius: 8px;
    padding: 1rem 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.admin-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item {
    margin-bottom: 0.25rem;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
}

.menu-link:hover {
    background: #f8f9fa;
    color: #495057;
}

.menu-link.active {
    background: #007bff;
    color: white;
}

.menu-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.menu-link i {
    margin-right: 0.75rem;
    width: 16px;
    text-align: center;
}

.coming-soon {
    margin-left: auto;
    font-size: 0.7rem;
    background: #ffc107;
    color: #000;
    padding: 0.1rem 0.4rem;
    border-radius: 10px;
}

/* Content Area */
.admin-content {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.breadcrumb {
    background: none;
    padding: 0;
    margin-bottom: 1.5rem;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
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

/* Table Container */
.table-container {
    overflow-x: auto;
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

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    padding: 0.25rem 0.5rem;
}

/* Modal Improvements */
.modal-header {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.modal-title {
    color: #495057;
    font-weight: 600;
}

/* Code styling for slugs */
code {
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-content {
        margin-top: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .action-buttons {
        justify-content: center;
    }
}

/* Loading States */
.btn:disabled {
    opacity: 0.6;
}

/* Success/Error States */
.alert {
    border: none;
    border-radius: 6px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generar slug desde el título
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.querySelector('input[name="slug"]');
    
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugInput.value = slug;
        });
    }
    
    // Confirmación para eliminar
    document.querySelectorAll('.btn-outline-danger').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres eliminar esta página?')) {
                // Aquí iría la lógica de eliminación
                console.log('Página eliminada');
            }
        });
    });
});
</script>