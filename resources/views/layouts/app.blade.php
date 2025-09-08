
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

    <!-- icono de carga -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-tumesa.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles Mejorados -->
    <style>
        /* ========== Variables CSS ========== */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #1e293b;
            --secondary-dark: #0f172a;
            --text-gray: #64748b;
            --text-gray-light: #94a3b8;
            --navbar-height: 70px;
            --transition: all 0.3s ease;
        }

        /* ========== Body con padding para navbar fixed ========== */
        body {
            padding-top: var(--navbar-height);
        }

        /* ========== Navbar Principal ========== */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 0.8rem 0;
            z-index: 1050;
            min-height: var(--navbar-height);
            transition: var(--transition);
        }

        /* ========== Brand/Logo ========== */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .logo-icon {
            height: 35px;
            width: auto;
            margin-right: 0.5rem;
        }

        /* ========== Navigation Links ========== */
        .navbar-nav .nav-link {
            color: var(--text-gray) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            position: relative;
            padding: 0.5rem 0.75rem;
            transition: var(--transition);
            text-decoration: none;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--primary-color);
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: 80%;
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        /* ========== Authentication Buttons ========== */
        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-login {
            color: var(--text-gray);
            border: 2px solid transparent;
            background: none;
            font-weight: 500;
            padding: 0.5rem 1.2rem;
            border-radius: 0.5rem;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-login:hover {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: rgba(37, 99, 235, 0.05);
        }

        .btn-register {
            background-color: var(--secondary-color);
            color: white;
            border: 2px solid var(--secondary-color);
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-register:hover {
            background-color: var(--secondary-dark);
            border-color: var(--secondary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 41, 59, 0.3);
        }

        /* ========== User Dropdown Styles ========== */
        .user-dropdown {
            position: relative;
        }

        .user-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            border: 2px solid #e2e8f0;
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
            color: var(--text-gray);
        }

        .user-button:hover {
            border-color: var(--primary-color);
            background-color: rgba(37, 99, 235, 0.05);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* ========== Dropdown Menu ========== */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
            padding: 0.5rem;
            margin-top: 0.5rem;
            animation: dropdownFade 0.3s ease;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.6rem 1rem;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .dropdown-item:hover {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }

        .dropdown-header {
            font-size: 0.85rem;
            color: var(--text-gray);
            padding: 0.5rem 1rem;
        }

        /* ========== Mobile Toggle Button ========== */
        .navbar-toggler {
            border: 2px solid transparent;
            padding: 0.25rem 0.5rem;
            background: transparent;
            cursor: pointer;
            transition: var(--transition);
            border-radius: 0.5rem;
        }

        .navbar-toggler:hover {
            background-color: rgba(37, 99, 235, 0.05);
            border-color: var(--primary-color);
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
            border-color: var(--primary-color);
        }

        .navbar-toggler-icon {
            display: block;
            width: 1.5em;
            height: 1.5em;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 100%;
            transition: var(--transition);
        }

        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2837, 99, 235, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M6 6L24 24M6 24L24 6'/%3e%3c/svg%3e");
        }

        /* ========== Fix para el problema del navbar que aparece/desaparece ========== */
        @media (min-width: 992px) {
            .navbar-collapse {
                display: flex !important;
                visibility: visible !important;
            }
        }

        /* ========== Responsive Design ========== */
        @media (max-width: 991.98px) {
            /* Mobile Navigation */
            .navbar-collapse {
                background-color: white;
                border-radius: 0.75rem;
                margin-top: 1rem;
                padding: 1rem;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            }

            .navbar-nav {
                padding: 1rem 0;
            }

            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                margin: 0.25rem 0;
                border-radius: 0.5rem;
                font-size: 1rem;
            }

            .navbar-nav .nav-link:hover {
                background-color: rgba(37, 99, 235, 0.05);
            }

            .navbar-nav .nav-link.active {
                background-color: rgba(37, 99, 235, 0.1);
            }

            .navbar-nav .nav-link::after {
                display: none;
            }

            /* Mobile Auth Buttons */
            .auth-buttons {
                flex-direction: column;
                width: 100%;
                padding-top: 1rem;
                border-top: 1px solid #e2e8f0;
                margin-top: 1rem;
            }

            .btn-login,
            .btn-register {
                width: 100%;
                text-align: center;
                margin: 0.25rem 0;
            }

            /* Mobile User Dropdown */
            .user-dropdown {
                width: 100%;
            }

            .user-button {
                width: 100%;
                justify-content: center;
            }

            .dropdown-menu {
                width: calc(100% - 2rem);
                margin: 0.5rem 1rem;
            }
        }

        @media (max-width: 575.98px) {
            /* Extra small devices */
            .navbar-brand {
                font-size: 1.2rem;
            }

            .logo-icon {
                height: 28px;
            }

            body {
                padding-top: 60px;
            }

            .navbar-custom {
                min-height: 60px;
                padding: 0.5rem 0;
            }
        }

        /* ========== Animaciones adicionales ========== */
        @media (prefers-reduced-motion: no-preference) {
            .navbar-collapse.collapsing {
                transition: height 0.35s ease;
            }

            .navbar-collapse.show {
                animation: slideDown 0.35s ease;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }

        /* ========== Dark mode support (opcional) ========== */
        @media (prefers-color-scheme: dark) {
            .navbar-custom {
                background-color: var(--secondary-color);
                color: white;
            }

            .navbar-nav .nav-link {
                color: #e2e8f0 !important;
            }

            .navbar-nav .nav-link:hover,
            .navbar-nav .nav-link.active {
                color: var(--primary-color) !important;
            }

            .dropdown-menu {
                background-color: var(--secondary-color);
                color: white;
            }

            .dropdown-item {
                color: #e2e8f0;
            }

            .dropdown-item:hover {
                background-color: rgba(255, 255, 255, 0.1);
                color: white;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div id="app">
        <!-- Navigation Mejorada -->
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container">
                <!-- Brand -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('img/logo-tumesa.png') }}" alt="TuMesa Logo" class="logo-icon">
                    <span>TuMesa</span>
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
                               href="{{ route('experiencias') ?? '#' }}">
                               <i class="fas fa-utensils d-lg-none me-2"></i>
                               Experiencias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ser-chef') ? 'active' : '' }}" 
                               href="{{ route('ser-chef') ?? '#' }}">
                               <i class="fas fa-chef-hat d-lg-none me-2"></i>
                               Ser Chef Anfitrión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('como-funciona') ? 'active' : '' }}" 
                               href="{{ route('como-funciona') ?? '#' }}">
                               <i class="fas fa-info-circle d-lg-none me-2"></i>
                               Cómo Funciona
                            </a>
                        </li>
                    </ul>

                    <!-- Right side authentication buttons -->
                    <div class="auth-buttons">
                        @auth
                            @if(Auth::user()->hasRole('chef_anfitrion'))
                                <!-- CHEF DROPDOWN -->
                                <div class="dropdown user-dropdown">
                                    <button class="user-button dropdown-toggle" type="button" id="chefDropdown"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="user-avatar">
                                            <i class="fas fa-chef-hat"></i>
                                        </div>
                                        <span>{{ Str::limit(Auth::user()->name, 15) }}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chefDropdown">
                                        <li>
                                            <h6 class="dropdown-header">
                                                <i class="fas fa-chef-hat me-2"></i>Panel de Chef
                                                <br><small>{{ Auth::user()->email }}</small>
                                            </h6>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('chef.dashboard') }}">
                                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('chef.profile.edit') }}">
                                                <i class="fas fa-user-edit me-2"></i>Mi Perfil
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('chef.ingresos') }}">
                                                <i class="fas fa-dollar-sign me-2"></i>Mis Ingresos
                                            </a>
                                        </li>
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

                            @elseif(Auth::user()->hasRole('comensal'))
                                <!-- COMENSAL DROPDOWN -->
                                <div class="dropdown user-dropdown">
                                    <button class="user-button dropdown-toggle" type="button" id="comensalDropdown"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                        <span>{{ Str::limit(Auth::user()->name, 15) }}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="comensalDropdown">
                                        <li>
                                            <h6 class="dropdown-header">
                                                <i class="fas fa-user me-2"></i>{{ Auth::user()->name }}
                                                <br><small>{{ Auth::user()->email }}</small>
                                            </h6>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('comensal.dashboard') }}">
                                                <i class="fas fa-tachometer-alt me-2"></i>Mi Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('perfil.comensal') }}">
                                                <i class="fas fa-user-edit me-2"></i>Editar Perfil
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('reservas.historial') }}">
                                                <i class="fas fa-calendar me-2"></i>Mis Reservas
                                            </a>
                                        </li>
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
                                <!-- USUARIO SIN ROL ESPECÍFICO -->
                                <div class="dropdown user-dropdown">
                                    <button class="user-button dropdown-toggle" type="button" id="userDropdown" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span>{{ Str::limit(Auth::user()->name, 15) }}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li>
                                            <div class="dropdown-header">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <small>Completa tu perfil para acceder a todas las funciones</small>
                                            </div>
                                        </li>
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
                            @endif
                        @else
                            <!-- Usuario no autenticado -->
                            <a href="{{ route('login') }}" class="btn btn-login">
                                <i class="fas fa-sign-in-alt d-lg-none me-2"></i>
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-register">
                                <i class="fas fa-user-plus d-lg-none me-2"></i>
                                Registrarse
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main style="margin-top: 80px;">
            @yield('content')
        </main>

    <footer class="footer-main">
    <!-- Wave decoration -->
    <div class="footer-wave"></div>
    
    <div class="container">
        <div class="row">
            <!-- Brand Column -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-brand">
                    <img src="{{ asset('img/logo-tumesa.png') }}" alt="TuMesa Logo">
                    <h4>TuMesa</h4>
                </div>
                <p class="footer-description">
                    Descubre experiencias gastronómicas únicas en casas de chefs anfitriones. 
                    Conectamos paladares curiosos con creadores culinarios apasionados.
                </p>
                <div class="social-buttons">
                    <a href="https://www.instagram.com/tumesaarg/" target="_blank" rel="noopener noreferrer" class="social-button instagram" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/profile.php?id=61579955470419" target="_blank" rel="noopener noreferrer" class="social-button facebook" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                  
                </div>
            </div>
            
            <!-- Quick Links Column -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-section">
                    <h5>Explora</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('experiencias') ?? '#' }}"><i class="fas fa-utensils"></i> Experiencias</a></li>
                        <li><a href="{{ route('experiencias') ?? '#' }}"><i class="fas fa-star"></i> Destacadas</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- For Hosts Column -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-section">
                    <h5>Para Chefs</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('ser-chef') ?? '#' }}"><i class="fas fa-chef-hat"></i> Ser Anfitrión</a></li>
                        <li><a href="{{ route('ser-chef') ?? '#' }}"><i class="fas fa-book"></i> Guía del Chef</a></li>
                        <li><a href="{{ route('terminos') }}"><i class="fas fa-shield-alt"></i> Términos y condiciones</a></li>
                         <li><a href="{{ route('privacidad') }}"><i class="fas fa-user-shield"></i> Política de Privacidad</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Contact & Newsletter Column -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-section">
                    <h5>Contacto</h5>
                    <div class="contact-info">
                        <p><i class="fas fa-envelope"></i> hola@tumesa.com.ar</p>
                        <p><i class="fas fa-phone"></i> +54 11 1234-5678</p>
                        <p><i class="fas fa-map-pin"></i> Buenos Aires, Argentina</p>
                    </div>
                    
             
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p class="footer-copyright">
                    &copy; {{ date('Y') }} TuMesa Argentina. Todos los derechos reservados.
                </p>
                <ul class="footer-legal-links">
                    <li><a href="{{ route('terminos') }}">Términos y Condiciones</a></li>
                    <li><a href="{{ route('privacidad') }}">Política de Privacidad</a></li>
                    <li><a href="{{ route('como-funciona') ?? '#' }}">Cómo Funciona</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script adicional para mejorar la experiencia -->
    <script>
        // Cerrar el menú móvil al hacer clic en un enlace
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const navbarToggler = document.querySelector('.navbar-toggler');
            
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) {
                        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                });
            });

            // Cambiar el navbar al hacer scroll (opcional)
            let lastScroll = 0;
            window.addEventListener('scroll', () => {
                const navbar = document.querySelector('.navbar-custom');
                const currentScroll = window.pageYOffset;
                
                if (currentScroll > 100) {
                    navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
                } else {
                    navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.08)';
                }
                
                lastScroll = currentScroll;
            });
        });
    </script>
    @stack('scripts')
</body>
</html>