<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TUMESA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300,400,500,600,700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                margin: 0;
                padding: 0;
            }
            
            /* Animaciones */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .nav-link {
                position: relative;
                transition: all 0.3s ease;
            }
            
            .nav-link::after {
                content: '';
                position: absolute;
                width: 0;
                height: 2px;
                bottom: -4px;
                left: 50%;
                background-color: #3b82f6;
                transition: all 0.3s ease;
                transform: translateX(-50%);
            }
            
            .nav-link:hover::after {
                width: 100%;
            }
            
            /* Dropdown animation */
            .dropdown-menu {
                animation: fadeIn 0.2s ease-out;
            }
        </style>
    </head>
    <body>
        <!-- Navigation Bar -->
        <nav style="background-color: #ffffff; 
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); 
                    position: sticky; 
                    top: 0; 
                    z-index: 1000;
                    border-bottom: 1px solid #f3f4f6;">
            <div style="max-width: 1280px; 
                        margin: 0 auto; 
                        padding: 0 24px;">
                <div style="display: flex; 
                            justify-content: space-between; 
                            align-items: center; 
                            height: 70px;">
                    
                    <!-- Logo -->
                    <div style="display: flex; align-items: center;">
                        <a href="{{ route('dashboard') }}" 
                           style="display: flex; 
                                  align-items: center; 
                                  text-decoration: none; 
                                  gap: 8px;">
                            <img src="{{ asset('img/logo-tumesa.png') }}" 
                                 alt="TUMESA" 
                                 style="height: 40px; 
                                        width: 40px; 
                                        object-fit: contain;">
                            <span style="font-size: 20px; 
                                         font-weight: 600; 
                                         color: #111827;">TuMesa</span>
                        </a>
                    </div>
                    
                    <!-- Center Navigation Links -->
                    <div style="display: flex; 
                                align-items: center; 
                                gap: 40px;">
                        <a href="#" 
                           class="nav-link"
                           style="color: #4b5563; 
                                  text-decoration: none; 
                                  font-size: 15px; 
                                  font-weight: 500; 
                                  padding: 8px 0;"
                           onmouseover="this.style.color='#3b82f6';"
                           onmouseout="this.style.color='#4b5563';">
                            Experiencias
                        </a>
                        <a href="#" 
                           class="nav-link"
                           style="color: #4b5563; 
                                  text-decoration: none; 
                                  font-size: 15px; 
                                  font-weight: 500; 
                                  padding: 8px 0;"
                           onmouseover="this.style.color='#3b82f6';"
                           onmouseout="this.style.color='#4b5563';">
                            Ser Chef Anfitrión
                        </a>
                        <a href="#" 
                           class="nav-link"
                           style="color: #4b5563; 
                                  text-decoration: none; 
                                  font-size: 15px; 
                                  font-weight: 500; 
                                  padding: 8px 0;"
                           onmouseover="this.style.color='#3b82f6';"
                           onmouseout="this.style.color='#4b5563';">
                            Cómo Funciona
                        </a>
                    </div>
                    
                    <!-- Right Side - Auth Links -->
                    <div style="display: flex; 
                                align-items: center; 
                                gap: 16px;">
                        @guest
                            <a href="{{ route('login') }}" 
                               style="color: #4b5563; 
                                      text-decoration: none; 
                                      font-size: 15px; 
                                      font-weight: 500; 
                                      padding: 8px 16px; 
                                      transition: all 0.3s ease;"
                               onmouseover="this.style.color='#3b82f6';"
                               onmouseout="this.style.color='#4b5563';">
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('register') }}" 
                               style="background-color: #111827; 
                                      color: white; 
                                      text-decoration: none; 
                                      font-size: 15px; 
                                      font-weight: 500; 
                                      padding: 10px 24px; 
                                      border-radius: 8px; 
                                      transition: all 0.3s ease;"
                               onmouseover="this.style.backgroundColor='#1f2937'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.15)';"
                               onmouseout="this.style.backgroundColor='#111827'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                Registrarse
                            </a>
                        @else
                            <!-- User Dropdown -->
                            <div style="position: relative;">
                                <button onclick="toggleDropdown()" 
                                        style="display: flex; 
                                               align-items: center; 
                                               gap: 8px; 
                                               background: none; 
                                               border: none; 
                                               cursor: pointer; 
                                               padding: 8px 12px; 
                                               border-radius: 8px; 
                                               transition: all 0.3s ease;"
                                        onmouseover="this.style.backgroundColor='#f3f4f6';"
                                        onmouseout="this.style.backgroundColor='transparent';">
                                    <span style="color: #374151; 
                                                 font-size: 15px; 
                                                 font-weight: 500;">
                                        {{ Auth::user()->name }}
                                    </span>
                                    <svg style="width: 16px; height: 16px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="userDropdown" 
                                     class="dropdown-menu"
                                     style="display: none; 
                                            position: absolute; 
                                            right: 0; 
                                            top: 100%; 
                                            margin-top: 8px; 
                                            background: white; 
                                            border-radius: 12px; 
                                            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); 
                                            min-width: 200px; 
                                            overflow: hidden;">
                                    <a href="{{ route('profile.edit') }}" 
                                       style="display: block; 
                                              padding: 12px 20px; 
                                              color: #374151; 
                                              text-decoration: none; 
                                              font-size: 14px; 
                                              transition: all 0.2s ease;"
                                       onmouseover="this.style.backgroundColor='#f3f4f6'; this.style.color='#3b82f6';"
                                       onmouseout="this.style.backgroundColor='transparent'; this.style.color='#374151';">
                                        Mi Perfil
                                    </a>
                                    <a href="#" 
                                       style="display: block; 
                                              padding: 12px 20px; 
                                              color: #374151; 
                                              text-decoration: none; 
                                              font-size: 14px; 
                                              transition: all 0.2s ease;"
                                       onmouseover="this.style.backgroundColor='#f3f4f6'; this.style.color='#3b82f6';"
                                       onmouseout="this.style.backgroundColor='transparent'; this.style.color='#374151';">
                                        Mis Reservas
                                    </a>
                                    <div style="border-top: 1px solid #e5e7eb; margin: 8px 0;"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                style="display: block; 
                                                       width: 100%; 
                                                       text-align: left; 
                                                       padding: 12px 20px; 
                                                       color: #dc2626; 
                                                       background: none; 
                                                       border: none; 
                                                       font-size: 14px; 
                                                       cursor: pointer; 
                                                       transition: all 0.2s ease;"
                                                onmouseover="this.style.backgroundColor='#fee2e2';"
                                                onmouseout="this.style.backgroundColor='transparent';">
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @isset($header)
            <header style="background-color: #ffffff; 
                           box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                <div style="max-width: 1280px; 
                            margin: 0 auto; 
                            padding: 24px;">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main style="background-color: #f9fafb; 
                     min-height: calc(100vh - 70px);">
            <div style="max-width: 1280px; 
                        margin: 0 auto; 
                        padding: 24px;">
                {{ $slot }}
            </div>
        </main>

        <script>
            // Toggle dropdown menu
            function toggleDropdown() {
                const dropdown = document.getElementById('userDropdown');
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('userDropdown');
                const button = event.target.closest('button');
                
                if (!button || !button.getAttribute('onclick') || !button.getAttribute('onclick').includes('toggleDropdown')) {
                    dropdown.style.display = 'none';
                }
            });
        </script>
    </body>
</html>