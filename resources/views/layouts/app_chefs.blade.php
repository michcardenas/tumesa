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

    <!-- Estilos específicos -->
    @stack('styles')

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header del Chef */
        .chef-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e293b 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .chef-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.1) 75%, transparent 75%, transparent);
            background-size: 30px 30px;
            animation: move 2s linear infinite;
            opacity: 0.3;
        }

        @keyframes move {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 30px 30px;
            }
        }

        .chef-header .container {
            position: relative;
            z-index: 2;
        }

        .chef-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .chef-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }

        .breadcrumb-nav {
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-top: 1rem;
        }

        .breadcrumb-nav a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .breadcrumb-nav a:hover {
            color: white;
        }

        .breadcrumb-nav .breadcrumb-separator {
            color: rgba(255,255,255,0.6);
            margin: 0 0.5rem;
        }

        .breadcrumb-nav .current {
            color: white;
            font-weight: 500;
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .header-actions .btn {
            border-color: rgba(255,255,255,0.3);
            color: white;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .header-actions .btn:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            color: white;
            transform: translateY(-1px);
        }

        .header-actions .btn i {
            margin-right: 0.5rem;
        }

        /* Main Content */
        .main-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .content-body {
            padding: 2rem;
        }

        /* Cards */
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 1.2rem;
            font-weight: 600;
            color: #495057;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-outline-primary {
            color: #2563eb;
            border-color: #2563eb;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
            transform: translateY(-1px);
        }

        .btn-outline-success {
            color: #059669;
            border-color: #059669;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background-color: #059669;
            border-color: #059669;
            color: white;
            transform: translateY(-1px);
        }

        /* Badges */
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chef-header {
                padding: 1rem 0;
            }

            .chef-header h1 {
                font-size: 1.5rem;
            }

            .header-actions {
                justify-content: center;
                margin-top: 1rem;
            }

            .header-actions .btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }

            .content-body {
                padding: 1rem;
            }

            .breadcrumb-nav {
                text-align: center;
            }
        }

        /* Animation utilities */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    <!-- Header del Chef -->
    <div class="chef-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>@yield('page-title', 'Chef Dashboard')</h1>
                    <p>@yield('page-subtitle', 'Gestiona tu experiencia culinaria')</p>
                    
                    <!-- Breadcrumb Navigation -->
                    <div class="breadcrumb-nav">
                        <a href="{{ route('chef.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                        <span class="breadcrumb-separator">•</span>
                        <span class="current">@yield('breadcrumb', 'Página')</span>
                    </div>
                </div>
                
                <div class="col-md-4 text-md-end">
                    <div class="header-actions">
                        @yield('header-actions')
                        
                        <!-- Botón de volver siempre presente -->
                        <a href="{{ route('chef.dashboard') }}" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="container fade-in">
        <div class="main-content">
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer opcional -->
    <footer class="text-center py-4 text-muted">
        <div class="container">
            <small>&copy; {{ date('Y') }} {{ config('app.name') }}. Panel del Chef Anfitrión.</small>
        </div>
    </footer>

    <!-- Modales -->
    @stack('modals')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animaciones de entrada
        document.addEventListener('DOMContentLoaded', function() {
            // Animar cards al cargar
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('fade-in');
                }, index * 100);
            });

            // Smooth scroll para anchors
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Toast notifications automáticas si hay mensajes de sesión
            @if(session('success'))
                showToast('{{ session('success') }}', 'success');
            @elseif(session('error'))
                showToast('{{ session('error') }}', 'error');
            @elseif(session('warning'))
                showToast('{{ session('warning') }}', 'warning');
            @endif
        });

        // Función para mostrar toasts
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast, {
                delay: 5000
            });
            bsToast.show();
            
            // Remover del DOM cuando se oculte
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        // Crear contenedor de toasts si no existe
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1060';
            document.body.appendChild(container);
            return container;
        }
    </script>

    @stack('scripts')
</body>
</html>