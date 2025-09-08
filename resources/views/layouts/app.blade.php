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
        .footer-main {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #e2e8f0;
        padding: 4rem 0 1.5rem;
        margin-top: 5rem;
        position: relative;
    }
    
    .footer-wave {
        position: absolute;
        top: -50px;
        left: 0;
        width: 100%;
        height: 50px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 100'%3E%3Cpath fill='%231e293b' fill-opacity='1' d='M0,32L48,42.7C96,53,192,75,288,74.7C384,75,480,53,576,48C672,43,768,53,864,58.7C960,64,1056,64,1152,58.7C1248,53,1344,43,1392,37.3L1440,32L1440,100L1392,100C1344,100,1248,100,1152,100C1056,100,960,100,864,100C768,100,672,100,576,100C480,100,384,100,288,100C192,100,96,100,48,100L0,100Z'%3E%3C/path%3E%3C/svg%3E");
        background-size: cover;
        background-repeat: no-repeat;
    }
    
    .footer-brand {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .footer-brand img {
        height: 40px;
        margin-right: 0.75rem;
        filter: brightness(0) invert(1);
    }
    
    .footer-brand h4 {
        color: #ffffff;
        font-weight: 700;
        margin: 0;
        font-size: 1.75rem;
    }
    
    .footer-description {
        color: #cbd5e1;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    
    .footer-section h5 {
        color: #ffffff;
        font-weight: 600;
        margin-bottom: 1.25rem;
        font-size: 1.1rem;
        position: relative;
        padding-bottom: 0.5rem;
    }
    
    .footer-section h5:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 30px;
        height: 2px;
        background: #2563eb;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 0.75rem;
    }
    
    .footer-links a {
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }
    
    .footer-links a:hover {
        color: #2563eb;
        transform: translateX(5px);
    }
    
    .footer-links a i {
        margin-right: 0.5rem;
        font-size: 0.85rem;
        color: #64748b;
    }
    
    .social-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    
    .social-button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .social-button:hover {
        background: #2563eb;
        border-color: #2563eb;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        color: #ffffff;
    }
    
    .social-button.facebook:hover {
        background: #1877f2;
        border-color: #1877f2;
    }
    
    .social-button.instagram:hover {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        border-color: transparent;
    }
    
    .newsletter-box {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    
    .newsletter-box h6 {
        color: #ffffff;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    
    .newsletter-form {
        display: flex;
        gap: 0.5rem;
    }
    
    .newsletter-form input {
        flex: 1;
        padding: 0.5rem 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
        color: #ffffff;
        font-size: 0.9rem;
    }
    
    .newsletter-form input::placeholder {
        color: #94a3b8;
    }
    
    .newsletter-form input:focus {
        outline: none;
        border-color: #2563eb;
        background: rgba(255, 255, 255, 0.15);
    }
    
    .newsletter-form button {
        background: #2563eb;
        color: white;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .newsletter-form button:hover {
        background: #1d4ed8;
        transform: translateX(2px);
    }
    
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 3rem;
        padding-top: 1.5rem;
    }
    
    .footer-bottom-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .footer-copyright {
        color: #94a3b8;
        font-size: 0.9rem;
        margin: 0;
    }
    
    .footer-legal-links {
        display: flex;
        gap: 1.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-legal-links a {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }
    
    .footer-legal-links a:hover {
        color: #2563eb;
    }
    
    .contact-info {
        margin-top: 1rem;
    }
    
    .contact-info p {
        color: #cbd5e1;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }
    
    .contact-info p i {
        margin-right: 0.75rem;
        color: #64748b;
        width: 16px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .footer-main {
            padding: 3rem 0 1.5rem;
        }
        
        .footer-bottom-content {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        
        .footer-legal-links {
            justify-content: center;
        }
        
        .newsletter-form {
            flex-direction: column;
        }
        
        .newsletter-form button {
            width: 100%;
        }
        
        .social-buttons {
            justify-content: center;
        }
    }
    .user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.dropdown-header {
    padding: 0.75rem 1rem;
    margin-bottom: 0;
}

.text-danger {
    color: #dc3545 !important;
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
                    @if(Auth::user()->hasRole('chef_anfitrion'))
                        <!-- Chef Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-login dropdown-toggle" type="button" id="userDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('perfil.chef') ?? '#' }}">
                                    <i class="fas fa-user me-2"></i>Mi Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('chef.dashboard') ?? '#' }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Mi Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('chef.experiencias') ?? '#' }}">
                                    <i class="fas fa-utensils me-2"></i>Mis Experiencias
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('chef.reservas') ?? '#' }}">
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

                    @elseif(Auth::user()->hasRole('comensal'))
                        <!-- Comensal Dropdown -->
                        <div class="dropdown user-dropdown">
                            <button class="btn btn-login dropdown-toggle" type="button" id="userDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar me-2">
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
                                <li><a class="dropdown-item" href="{{ route('comensal.dashboard') ?? '#' }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Mi Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('perfil.comensal.edit') ?? '#' }}">
                                    <i class="fas fa-user-edit me-2"></i>Editar Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('reservas.historial') ?? '#' }}">
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

                    @elseif(Auth::user()->hasRole('admin'))
                        <!-- Admin Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-login dropdown-toggle" type="button" id="userDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-shield me-1"></i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') ?? '#' }}">
                                    <i class="fas fa-cog me-2"></i>Panel Admin
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
                        <!-- Usuario sin rol definido o rol desconocido -->
                        <div class="dropdown">
                            <button class="btn btn-login dropdown-toggle" type="button" id="userDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile') ?? '#' }}">
                                    <i class="fas fa-user me-2"></i>Mi Perfil
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
                    @endif

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
                    <li><a href="#">Términos y Condiciones</a></li>
                    <li><a href="#">Política de Privacidad</a></li>
                    <li><a href="{{ route('como-funciona') ?? '#' }}">Cómo Funciona</a></li>
                </ul>
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