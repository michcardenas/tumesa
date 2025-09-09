<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Chef Dashboard') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
       <!-- icono de carga -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-tumesa.png') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos específicos del chef -->
    @stack('styles')

    <style>
        /* Estilos base del chef */
        .chef-container {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .chef-header {
            color: white;
            padding: 1rem 0;
            margin-bottom: 0;
        }

        .chef-header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .chef-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        /* Sidebar */
        .chef-sidebar {
            background: white;
            border-radius: 8px;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .chef-menu {
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
            cursor: pointer;
        }

        .menu-link:hover {
            background: #f1f5f9;
            color: #2563eb;
        }

        .menu-link.active {
            background: #2563eb;
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
            background: #64748b;
            color: white;
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
        }

        /* Content Area */
        .chef-content {
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

        /* Income Cards */
        .income-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 1rem;
        }

        .income-card h5 {
            color: #6c757d;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .income-card h2 {
            margin-bottom: 0.5rem;
            font-weight: bold;
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
            background: #f1f5f9;
            border-bottom: 2px solid #dee2e6;
        }

        .modal-title {
            color: #2563eb;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chef-content {
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
            background-color: #1e293b !important;
        }

        .text-primary {
            color: #2563eb !important;
        }

        .btn-outline-primary {
            color: #2563eb;
            border-color: #2563eb;
        }

        .btn-outline-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .btn-outline-success {
            color: #059669;
            border-color: #059669;
        }

        .btn-outline-success:hover {
            background-color: #059669;
            border-color: #059669;
            color: white;
        }
            .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0.8rem 0;
            z-index: 1050;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #2563eb !important;
            text-decoration: none;
        }
        
        .navbar-nav .nav-link {
            color: #64748b !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
            text-decoration: none;
        }
        
        .navbar-nav .nav-link:hover {
            color: #2563eb !important;
        }
        
        .navbar-nav .nav-link.active {
            color: #2563eb !important;
            font-weight: 600;
        }
        
        .btn-login {
            color: #64748b;
            border: none;
            background: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            transition: color 0.3s ease;
            text-decoration: none;
        }
        
        .btn-login:hover {
            color: #2563eb;
        }
        
        .btn-register {
            background-color: #1e293b;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }
        
        .btn-register:hover {
            background-color: #0f172a;
            color: white;
        }
        
        .logo-icon {
            height: 65px;
            width: auto;
            margin-right: 0.5rem;
        }
        
        /* Navbar toggler styles */
        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
            background: none;
            cursor: pointer;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }
        
        .navbar-toggler-icon {
            display: block;
            width: 1.5em;
            height: 1.5em;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 100%;
        }
        
        /* IMPORTANTE: Mostrar siempre el menú en pantallas grandes */
        @media (min-width: 992px) {
            .navbar-collapse {
                display: flex !important;
                visibility: visible !important;
            }
        }
        
        /* Mobile styles */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                display: none;
            }
            
            .navbar-collapse.show {
                display: block !important;
            }
            
            .navbar-nav {
                text-align: center;
                margin-top: 1rem;
            }
            
            .auth-buttons {
                justify-content: center !important;
                margin-top: 1rem;
                flex-direction: column;
                align-items: center;
            }
            
            .navbar-collapse {
                border-top: 1px solid #e9ecef;
                margin-top: 1rem;
                padding-top: 1rem;
            }
            
            .btn-login,
            .btn-register {
                width: 200px;
                margin: 0.25rem 0;
                text-align: center;
                display: block;
            }
        }
        
        /* Dropdown styles */
        .dropdown-menu {
            z-index: 1060;
            border: 1px solid #e9ecef;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body class="font-sans antialiased">
      <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container">
                <!-- Brand -->
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('img/logo-tumesa.png') }}" alt="TuMesa Logo" class="logo-icon">
                    TuMesa
                </a>

                <!-- Mobile toggle button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation items -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Center navigation links -->
                    <ul class="navbar-nav mx-auto">
                          <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('experiencias') ? 'active' : '' }}" 
                               href="{{ route('experiencias') ?? '#' }}">Experiencias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ser-chef') ? 'active' : '' }}" 
                               href="{{ route('ser-chef') ?? '#' }}">Ser Chef Anfitrión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('como-funciona') ? 'active' : '' }}" 
                               href="{{ route('como-funciona') ?? '#' }}">Cómo Funciona</a>
                        </li>
                    </ul>

                    <!-- Right side authentication buttons -->
                    <div class="d-flex auth-buttons">
                        @auth
                            <!-- User is logged in -->
                            <div class="dropdown">
                                <button class="btn btn-login dropdown-toggle" type="button" id="userDropdown" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i>
                                    {{ Auth::user()->name }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile') ?? '#' }}">
                                        <i class="fas fa-user me-2"></i>Mi Perfil
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('chef.dashboard') }}">
                                        <i class="fas fa-calendar me-2"></i>Mis Reservas
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <!-- User is not logged in -->
                            <a href="{{ route('login') }}" class="btn btn-login">Iniciar Sesión</a>
                            <a href="{{ route('register') }}" class="btn btn-register">Registrarse</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    <div class="chef-container">
        <!-- Header del Chef -->
        <div class="chef-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col">
                        <h1>@yield('page-title', 'Panel del Chef')</h1>
                        <p>Bienvenido, {{ Auth::user()->name }} - @yield('page-subtitle', 'Administra tu cocina')</p>
                    </div>
                    <div class="col-auto">
                        @yield('header-actions')
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar Navigation -->
                <div class="col-md-3">
                    @include('chef.partials.sidebar')
                </div>

                <!-- Main Content -->
                <div class="col-md-9">
                    <div class="chef-content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales globales -->
    @stack('modals')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
    <!-- Scripts específicos de chef -->
    <script>
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
            if (event && event.target) {
                event.target.closest('.menu-link').classList.add('active');
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Manejar clicks en sidebar
            document.querySelectorAll('.menu-link:not([data-bs-toggle])').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!this.classList.contains('disabled') && !this.hasAttribute('data-bs-toggle')) {
                        e.preventDefault();
                        
                        // Remover active de todos
                        document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active'));
                        
                        // Agregar active al clickeado
                        this.classList.add('active');
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>