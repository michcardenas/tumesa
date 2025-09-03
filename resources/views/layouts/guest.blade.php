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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body style="margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #ffffff; min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        
        <!-- Fondo con patrón de gradiente suave -->
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, #eff8ff 0%, #ffffff 50%, #dbeafe 100%); z-index: -1;"></div>
        
        <!-- Círculos decorativos -->
        <div style="position: fixed; top: -200px; right: -200px; width: 400px; height: 400px; background: radial-gradient(circle, #dbeafe 0%, transparent 70%); opacity: 0.5; z-index: 0;"></div>
        <div style="position: fixed; bottom: -150px; left: -150px; width: 300px; height: 300px; background: radial-gradient(circle, #eff8ff 0%, transparent 70%); opacity: 0.5; z-index: 0;"></div>
        
        <!-- Contenedor principal -->
        <div style="width: 100%; max-width: 420px; padding: 20px; position: relative; z-index: 10;">
            
            <!-- Logo -->
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="display: inline-block; padding: 20px;  ">
                    <a href="{{ route('home') }}" style="display: inline-block; transition: transform 0.3s ease;">
            <img src="{{ asset('img/logo-tumesa.png') }}"
                 alt="TUMESA"
                 style="width: 90px;
                        height: 90px;
                        object-fit: contain;
                        transition: opacity 0.3s ease;"
                 onmouseover="this.style.opacity='0.8'"
                 onmouseout="this.style.opacity='1'">
        </a>
                </div>
         
            </div>
            
            <!-- Contenedor del formulario -->
            <div style="background: white; 
                        border-radius: 24px; 
                        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.08), 0 0 0 1px rgba(59, 130, 246, 0.1); 
                        padding: 40px 30px; 
                        position: relative; 
                        overflow: hidden;">
                
                <!-- Decoración superior -->
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 50%, #3b82f6 100%);"></div>
                
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            <div style="text-align: center; margin-top: 30px; color: #6b7280; font-size: 12px;">
                © {{ date('Y') }} TUMESA. Todos los derechos reservados.
            </div>
        </div>
        
        <style>
            * {
                box-sizing: border-box;
            }
            
            /* Animación suave para elementos */
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
            
            body > div {
                animation: fadeIn 0.6s ease-out;
            }
        </style>
    </body>
</html>