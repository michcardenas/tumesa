<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TuMesa') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles -->
    <style>
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

        /* User Dropdown Styles */
        .user-dropdown {
            position: relative;
        }

        .user-button {
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            color: #374151;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .user-button:hover {
            background-color: #f8fafc;
            border-color: #2563eb;
            color: #2563eb;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .logo-icon {
            height: 32px;
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
        
        /* Mostrar siempre el menú en pantallas grandes */
        @media (min-width: 992px) {
            .navbar-collapse {
                display: flex !important;
                visibility: visible !important;
            }
        }
        .menu-link-static {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
}

.menu-link-static:hover {
    background: #f1f5f9;
    color: #2563eb;
    text-decoration: none;
}

.menu-link-static.active {
    background: #2563eb;
    color: white;
}

.menu-link-static i {
    margin-right: 0.75rem;
    width: 16px;
    text-align: center;
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

            .user-button {
                width: 200px;
                margin: 0.25rem 0;
                justify-content: center;
            }
        }
        
        /* Dropdown styles */
        .dropdown-menu {
            z-index: 1060;
            border: 1px solid #e9ecef;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8fafc;
            color: #2563eb;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }

        /* Main content spacing */
        .main-content {
            margin-top: 80px;
            min-height: calc(100vh - 160px);
        }

        /* Footer */
        .footer-custom {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #e2e8f0;
            padding: 2rem 0;
            margin-top: auto;
        }

        .footer-custom h5 {
            color: #f1f5f9;
            font-weight: 600;
        }

        .footer-custom .social-links a {
            color: #94a3b8;
            font-size: 1.25rem;
            transition: color 0.3s ease;
        }

        .footer-custom .social-links a:hover {
            color: #2563eb;
        }

        /* Sidebar Styles */
        .comensal-sidebar {
            background: white;
            border-radius: 8px;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 100px;
            margin-top: 1rem;
        }

        .comensal-menu {
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
            text-decoration: none;
        }

        .menu-link.active {
            background: #2563eb;
            color: white;
        }

        .menu-link i {
            margin-right: 0.75rem;
            width: 16px;
            text-align: center;
        }

        /* Utility classes */
        .bg-comensal {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .text-comensal {
            color: #2563eb !important;
        }

        .btn-comensal {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .btn-comensal:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: white;
        }

        /* Content wrapper para vistas */
        .comensal-content {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 1rem;
        }

        /* Mobile sidebar */
        @media (max-width: 768px) {
            .comensal-sidebar {
                position: relative;
                top: auto;
                margin-bottom: 1rem;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <div id="app" class="d-flex flex-column min-vh-100">
        <!-- Navigation -->
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
                            <div class="dropdown user-dropdown">
                                <button class="user-button dropdown-toggle" type="button" id="userDropdown" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <span>{{ Str::limit(Auth::user()->name, 15) }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li>
                                        <h6 class="dropdown-header">
                                            <i class="fas fa-user me-2"></i>{{ Auth::user()->name }}
                                            <br><small class="text-muted">{{ Auth::user()->email }}</small>
                                        </h6>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('comensal.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Mi Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') ?? '#' }}">
                                        <i class="fas fa-user-edit me-2"></i>Editar Perfil
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('reservas.historial') }}">
                                        <i class="fas fa-calendar me-2"></i>Mis Reservas
                                    </a></li>
                               
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
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

        <!-- Main content -->
        <main class="main-content flex-grow-1">
            <div class="container-fluid">
                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-md-3">
                        <div class="comensal-sidebar">
                            <ul class="comensal-menu">
                                <li class="menu-item">
                                    <a href="{{ route('comensal.dashboard') }}" class="menu-link {{ request()->routeIs('comensal.dashboard') ? 'active' : '' }}">
                                        <i class="fas fa-tachometer-alt"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="{{ route('experiencias') }}"class="menu-link" >
                                        <i class="fas fa-search"></i>
                                        Cenas Disponibles
                                    </a>
                                </li>
                            
                               <li class="menu-item">
                                    <a href="{{ route('reservas.historial') }}" 
                                    class="menu-link-static {{ request()->routeIs('reservas.historial') ? 'active' : '' }}">
                                        <i class="fas fa-history"></i>
                                        Historial
                                    </a>
                                </li>


                               
                                <li class="menu-item">
                                    <a href="{{ route('perfil.comensal') }}" class="menu-link-static {{ request()->routeIs('perfil.comensal') ? 'active' : '' }}">
                                        <i class="fas fa-user-edit"></i>
                                        Mi Perfil
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Content Area -->
                    <div class="col-md-9">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer-custom mt-auto">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <img src="{{ asset('img/logo-tumesa.png') }}" alt="TuMesa Logo" 
                                 style="height: 24px; margin-right: 0.5rem;">
                            <h5 class="mb-0">TuMesa</h5>
                        </div>
                        <p class="mb-0">Conectando comensales con experiencias gastronómicas únicas.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="social-links">
                            <a href="#" class="text-decoration-none me-3">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="#" class="text-decoration-none me-3">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-decoration-none">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                        <p class="mb-0 mt-2">&copy; {{ date('Y') }} TuMesa. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar click del dropdown del usuario
            const userDropdown = document.getElementById('userDropdown');
            if (userDropdown) {
                userDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                });
            }

            // Auto-collapse navbar en mobile después de click
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const navbarCollapse = document.getElementById('navbarNav');
                    if (navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                        bsCollapse.hide();
                    }
                });
            });

            // Manejar logout con confirmación
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('¿Estás seguro que deseas cerrar sesión?')) {
                        e.preventDefault();
                    }
                });
            });

            // Función global para mostrar secciones (para el sidebar)
            window.showSection = function(sectionName) {
                // Ocultar todas las secciones
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                // Remover active de todos los links del sidebar
                document.querySelectorAll('.comensal-sidebar .menu-link').forEach(link => {
                    link.classList.remove('active');
                });
                
                // Mostrar la sección seleccionada
                const targetSection = document.getElementById(sectionName + '-section');
                if (targetSection) {
                    targetSection.classList.add('active');
                } else {
                    // Si no existe la sección, mostrar dashboard
                    const dashboardSection = document.getElementById('dashboard-section');
                    if (dashboardSection) {
                        dashboardSection.classList.add('active');
                    }
                }
                
                // Activar el link correspondiente si el evento viene del sidebar
                if (window.event && window.event.target) {
                    const clickedLink = window.event.target.closest('.menu-link');
                    if (clickedLink) {
                        clickedLink.classList.add('active');
                    }
                }
            };

            // Manejar clicks en el sidebar
            document.querySelectorAll('.comensal-sidebar .menu-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Solo prevenir default para links que usan showSection
                    if (this.getAttribute('onclick') && this.getAttribute('onclick').includes('showSection')) {
                        e.preventDefault();
                        
                        // Remover active de todos
                        document.querySelectorAll('.comensal-sidebar .menu-link').forEach(l => l.classList.remove('active'));
                        
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