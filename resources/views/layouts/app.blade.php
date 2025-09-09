
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

    <!-- Bootstrap CSS (solo para utilidades, no para el navbar) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles para Navbar Fullscreen Mobile -->
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

        /* ========== Reset y Base ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding-top: var(--navbar-height);
            font-family: 'Figtree', sans-serif;
            overflow-x: hidden;
        }

        body.menu-open {
            overflow: hidden;
        }

        /* ========== Navbar Principal ========== */
        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            z-index: 1000;
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .navbar-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ========== Brand/Logo ========== */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            z-index: 1002;
        }

        .logo-icon {
            height: 65px;
            width: auto;
            margin-right: 0.5rem;
        }

        /* ========== Desktop Navigation ========== */
        .navbar-desktop {
            display: none;
            flex: 1;
            align-items: center;
            justify-content: space-between;
            margin-left: 3rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 1rem;
            margin: 0;
            padding: 0;
        }

        .nav-link {
            color: var(--text-gray);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            position: relative;
            transition: var(--transition);
        }

        .nav-link::after {
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

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        /* ========== Auth Buttons Desktop ========== */
        .auth-buttons-desktop {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-login {
            color: var(--text-gray);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1.2rem;
            border: 2px solid transparent;
            border-radius: 0.5rem;
            transition: var(--transition);
        }

        .btn-login:hover {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: rgba(37, 99, 235, 0.05);
        }

        .btn-register {
            background-color: var(--secondary-color);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-register:hover {
            background-color: var(--secondary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 41, 59, 0.3);
        }

        /* ========== Mobile Menu Button ========== */
        .menu-toggle {
            display: block;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 1002;
            width: 40px;
            height: 40px;
            position: relative;
        }

        .menu-toggle span {
            display: block;
            width: 25px;
            height: 2px;
            background-color: var(--secondary-color);
            margin: 5px auto;
            transition: var(--transition);
            transform-origin: center;
        }

        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* ========== Mobile Fullscreen Menu ========== */
        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            z-index: 1001;
            display: flex;
            flex-direction: column;
            padding-top: var(--navbar-height);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        .mobile-menu-content {
            padding: 2rem 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .mobile-nav-links {
            list-style: none;
            padding: 0;
            margin: 0 0 2rem 0;
        }

        .mobile-nav-link {
            display: flex;
            align-items: center;
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 500;
            padding: 1rem 1.5rem;
            margin: 0.5rem 0;
            border-radius: 0.75rem;
            transition: var(--transition);
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .mobile-nav-link:hover,
        .mobile-nav-link.active {
            background: var(--primary-color);
            color: white;
            transform: translateX(10px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .mobile-nav-link i {
            margin-right: 1rem;
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }

        /* ========== Mobile Auth Section ========== */
        .mobile-auth {
            padding-top: 2rem;
            border-top: 2px solid #e2e8f0;
        }

        .mobile-auth-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mobile-btn {
            padding: 1rem;
            text-align: center;
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: var(--transition);
            font-size: 1.1rem;
        }

        .mobile-btn-login {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .mobile-btn-login:hover {
            background: var(--primary-color);
            color: white;
        }

        .mobile-btn-register {
            background: var(--secondary-color);
            color: white;
            border: 2px solid var(--secondary-color);
        }

        .mobile-btn-register:hover {
            background: var(--secondary-dark);
            border-color: var(--secondary-dark);
        }

        /* ========== User Profile Mobile ========== */
        .mobile-user-profile {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .mobile-user-info {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .mobile-user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .mobile-user-details h3 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--secondary-color);
        }

        .mobile-user-details p {
            margin: 0;
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .mobile-user-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .mobile-user-menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-gray);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: var(--transition);
            margin: 0.25rem 0;
        }

        .mobile-user-menu-item:hover {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .mobile-user-menu-item i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .mobile-user-menu-item.logout {
            color: #ef4444;
            margin-top: 1rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 1rem;
        }

        /* ========== Responsive Breakpoints ========== */
        @media (min-width: 992px) {
            .navbar-desktop {
                display: flex;
            }
            
            .menu-toggle {
                display: none;
            }
            
            .mobile-menu {
                display: none;
            }
        }

        @media (max-width: 991px) {
            .navbar-container {
                padding: 0 1rem;
            }
        }

        /* ========== User Dropdown Desktop ========== */
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

        .user-avatar-small {
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

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            margin-top: 0.5rem;
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            padding: 0.6rem 1rem;
            color: var(--text-gray);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }

        .dropdown-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 0.5rem 0;
        }

        /* ========== Overlay para cerrar el menú ========== */
        .menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .menu-overlay.active {
            opacity: 1;
            visibility: visible;
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
    </style>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar-custom">
            <div class="navbar-container">
                <!-- Brand -->
                <a href="{{ url('/') }}" class="navbar-brand">
                    <img src="{{ asset('img/logo-tumesa.png') }}" alt="TuMesa Logo" class="logo-icon">
                </a>

                <!-- Desktop Navigation -->
                <div class="navbar-desktop">
                    <ul class="nav-links">
                        <li>
                            <a href="{{ route('experiencias') ?? '#' }}" 
                               class="nav-link {{ request()->routeIs('experiencias') ? 'active' : '' }}">
                                Experiencias
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('ser-chef') ?? '#' }}" 
                               class="nav-link {{ request()->routeIs('ser-chef') ? 'active' : '' }}">
                                Ser Anfitrión
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('como-funciona') ?? '#' }}" 
                               class="nav-link {{ request()->routeIs('como-funciona') ? 'active' : '' }}">
                                Cómo Funciona
                            </a>
                        </li>
                    </ul>

                    <!-- Desktop Auth -->
                    <div class="auth-buttons-desktop">
                        @auth
                            <div class="user-dropdown">
                                <button class="user-button" onclick="toggleDropdown(event)">
                                    <div class="user-avatar-small">
                                        @if(Auth::user()->hasRole('chef_anfitrion'))
                                            <i class="fas fa-chef-hat"></i>
                                        @else
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <span>{{ Str::limit(Auth::user()->name, 15) }}</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" id="userDropdown">
                                    @if(Auth::user()->hasRole('chef_anfitrion'))
                                        <a href="{{ route('chef.dashboard') }}" class="dropdown-item">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                        </a>
                                        <a href="{{ route('chef.profile.edit') }}" class="dropdown-item">
                                            <i class="fas fa-user-edit me-2"></i>Mi Perfil
                                        </a>
                                        <a href="{{ route('chef.ingresos') }}" class="dropdown-item">
                                            <i class="fas fa-dollar-sign me-2"></i>Mis Ingresos
                                        </a>
                                    @elseif(Auth::user()->hasRole('comensal'))
                                        <a href="{{ route('comensal.dashboard') }}" class="dropdown-item">
                                            <i class="fas fa-tachometer-alt me-2"></i>Mi Dashboard
                                        </a>
                                        <a href="{{ route('perfil.comensal') }}" class="dropdown-item">
                                            <i class="fas fa-user-edit me-2"></i>Editar Perfil
                                        </a>
                                        <a href="{{ route('reservas.historial') }}" class="dropdown-item">
                                            <i class="fas fa-calendar me-2"></i>Mis Reservas
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item" style="color: #ef4444;">
                                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-login">Iniciar Sesión</a>
                            <a href="{{ route('register') }}" class="btn-register">Registrarse</a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="menu-toggle" onclick="toggleMobileMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </nav>

        <!-- Mobile Fullscreen Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-content">
                @auth
                    <!-- User Profile Section -->
                    <div class="mobile-user-profile">
                        <div class="mobile-user-info">
                            <div class="mobile-user-avatar">
                                @if(Auth::user()->hasRole('chef_anfitrion'))
                                    <i class="fas fa-chef-hat"></i>
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="mobile-user-details">
                                <h3>{{ Auth::user()->name }}</h3>
                                <p>{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <nav class="mobile-user-menu">
                            @if(Auth::user()->hasRole('chef_anfitrion'))
                                <a href="{{ route('chef.dashboard') }}" class="mobile-user-menu-item">
                                    <i class="fas fa-tachometer-alt"></i>Dashboard
                                </a>
                                <a href="{{ route('chef.profile.edit') }}" class="mobile-user-menu-item">
                                    <i class="fas fa-user-edit"></i>Mi Perfil
                                </a>
                                <a href="{{ route('chef.ingresos') }}" class="mobile-user-menu-item">
                                    <i class="fas fa-dollar-sign"></i>Mis Ingresos
                                </a>
                            @elseif(Auth::user()->hasRole('comensal'))
                                <a href="{{ route('comensal.dashboard') }}" class="mobile-user-menu-item">
                                    <i class="fas fa-tachometer-alt"></i>Mi Dashboard
                                </a>
                                <a href="{{ route('perfil.comensal') }}" class="mobile-user-menu-item">
                                    <i class="fas fa-user-edit"></i>Editar Perfil
                                </a>
                                <a href="{{ route('reservas.historial') }}" class="mobile-user-menu-item">
                                    <i class="fas fa-calendar"></i>Mis Reservas
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="mobile-user-menu-item logout">
                                    <i class="fas fa-sign-out-alt"></i>Cerrar Sesión
                                </button>
                            </form>
                        </nav>
                    </div>
                @endauth

                <!-- Navigation Links -->
                <nav>
                    <ul class="mobile-nav-links">
                        <li>
                            <a href="{{ route('experiencias') ?? '#' }}" 
                               class="mobile-nav-link {{ request()->routeIs('experiencias') ? 'active' : '' }}">
                                <i class="fas fa-utensils"></i>
                                Experiencias
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('ser-chef') ?? '#' }}" 
                               class="mobile-nav-link {{ request()->routeIs('ser-chef') ? 'active' : '' }}">
                                <i class="fas fa-chef-hat"></i>
                                Ser Chef Anfitrión
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('como-funciona') ?? '#' }}" 
                               class="mobile-nav-link {{ request()->routeIs('como-funciona') ? 'active' : '' }}">
                                <i class="fas fa-info-circle"></i>
                                Cómo Funciona
                            </a>
                        </li>
                    </ul>
                </nav>

                @guest
                    <!-- Auth Buttons for Guests -->
                    <div class="mobile-auth">
                        <div class="mobile-auth-buttons">
                            <a href="{{ route('login') }}" class="mobile-btn mobile-btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('register') }}" class="mobile-btn mobile-btn-register">
                                <i class="fas fa-user-plus me-2"></i>
                                Registrarse
                            </a>
                        </div>
                    </div>
                @endguest
            </div>
        </div>

        <!-- Overlay -->
        <div class="menu-overlay" id="menuOverlay" onclick="toggleMobileMenu()"></div>

        <!-- Main content -->
        <main style="">
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
     <!-- Scripts -->
    <script>
        // Toggle Mobile Menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('menuOverlay');
            const toggle = document.querySelector('.menu-toggle');
            const body = document.body;

            menu.classList.toggle('active');
            overlay.classList.toggle('active');
            toggle.classList.toggle('active');
            body.classList.toggle('menu-open');
        }

        // Toggle Desktop Dropdown
        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !event.target.closest('.user-dropdown')) {
                dropdown.classList.remove('show');
            }
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-nav-link, .mobile-user-menu-item').forEach(link => {
            link.addEventListener('click', function() {
                const menu = document.getElementById('mobileMenu');
                const overlay = document.getElementById('menuOverlay');
                const toggle = document.querySelector('.menu-toggle');
                const body = document.body;

                if (menu.classList.contains('active')) {
                    menu.classList.remove('active');
                    overlay.classList.remove('active');
                    toggle.classList.remove('active');
                    body.classList.remove('menu-open');
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
            } else {
                navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.08)';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>