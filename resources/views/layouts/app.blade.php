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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div id="app">
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
                                    <li><a class="dropdown-item" href="{{ route('reservas.historial') ?? '#' }}">
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

        <!-- Main content -->
        <main style="margin-top: 80px;">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-dark text-light py-4 mt-5">
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
                            <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-light"><i class="fab fa-twitter"></i></a>
                        </div>
                        <p class="mb-0 mt-2">&copy; {{ date('Y') }} TuMesa. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>