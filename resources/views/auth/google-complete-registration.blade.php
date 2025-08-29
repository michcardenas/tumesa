<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header con logo/icono -->
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    ¡Un paso más!
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    @if(isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'])
                        {{ __('¡Bienvenido de nuevo! Para completar tu perfil, selecciona tu tipo de cuenta.') }}
                    @else
                        {{ __('Para completar tu registro con Google, selecciona tu tipo de cuenta.') }}
                    @endif
                </p>
            </div>

            <!-- Card principal -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl p-8 border-0 relative overflow-hidden">
                <!-- Decoración de fondo -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-full -mr-16 -mt-16 opacity-50"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-pink-100 to-yellow-100 dark:from-pink-900/20 dark:to-yellow-900/20 rounded-full -ml-12 -mb-12 opacity-50"></div>

                <!-- Información del usuario de Google -->
                <div class="relative mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl border border-blue-100 dark:border-blue-800">
                    <div class="flex items-center space-x-4">
                        @if($googleUserData['avatar'])
                            <div class="relative">
                                <img src="{{ $googleUserData['avatar'] }}" alt="Avatar" class="w-16 h-16 rounded-full ring-4 ring-white dark:ring-gray-700 shadow-lg">
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white dark:border-gray-700 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                </div>
                            </div>
                        @else
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg ring-4 ring-white dark:ring-gray-700">
                                {{ substr($googleUserData['name'], 0, 1) }}
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ $googleUserData['name'] }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $googleUserData['email'] }}</p>
                            <div class="flex items-center text-xs">
                                <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                <span class="text-green-600 dark:text-green-400 font-medium">Verificado con Google</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('auth.google.complete-registration.store') }}" class="relative space-y-6">
                    @csrf

                    <!-- Selector de tipo de cuenta -->
                    <div class="space-y-4">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">¿Cómo quieres usar TuMesa?</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Selecciona el tipo de cuenta que mejor se adapte a ti</p>
                        </div>
                        
                        <div class="grid gap-4">
                            <!-- Opción Comensal -->
                            <div class="role-option relative">
                                <input id="role_comensal" name="role" type="radio" value="comensal" 
                                       class="sr-only peer"
                                       {{ old('role') == 'comensal' ? 'checked' : '' }} required>
                                <label for="role_comensal" class="flex items-start p-6 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer transition-all duration-300 hover:border-blue-300 hover:shadow-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:shadow-lg group">
                                    <div class="flex-shrink-0 mr-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center text-2xl shadow-md group-hover:scale-110 transition-transform duration-300">
                                            🍽️
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Comensal</h4>
                                            <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-500 rounded-full flex items-center justify-center peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all duration-200">
                                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200"></div>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                            Descubre restaurantes únicos, reserva mesas y disfruta de experiencias culinarias extraordinarias.
                                        </p>
                                        <div class="mt-3 flex items-center text-xs text-blue-600 dark:text-blue-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Reservas • Reseñas • Experiencias gastronómicas
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Opción Chef Anfitrión -->
                            <div class="role-option relative">
                                <input id="role_chef_anfitrion" name="role" type="radio" value="chef_anfitrion" 
                                       class="sr-only peer"
                                       {{ old('role') == 'chef_anfitrion' ? 'checked' : '' }} required>
                                <label for="role_chef_anfitrion" class="flex items-start p-6 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer transition-all duration-300 hover:border-purple-300 hover:shadow-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 peer-checked:shadow-lg group">
                                    <div class="flex-shrink-0 mr-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-2xl shadow-md group-hover:scale-110 transition-transform duration-300">
                                            👨‍🍳
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Chef Anfitrión</h4>
                                            <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-500 rounded-full flex items-center justify-center peer-checked:border-purple-500 peer-checked:bg-purple-500 transition-all duration-200">
                                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200"></div>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                            Comparte tu pasión culinaria, gestiona tu restaurante y conecta con comensales que buscan experiencias únicas.
                                        </p>
                                        <div class="mt-3 flex items-center text-xs text-purple-600 dark:text-purple-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Gestión • Marketing • Dashboard de chef
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start">
                                <input id="terms" name="terms" type="checkbox" value="1" 
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-blue-600"
                                       {{ old('terms') ? 'checked' : '' }} required>
                                <label for="terms" class="ml-3 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    Al continuar, acepto los 
                                    <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 underline font-medium transition-colors duration-200">
                                        términos y condiciones de servicio
                                    </a> 
                                    y la 
                                    <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 underline font-medium transition-colors duration-200">
                                        política de privacidad
                                    </a> de TuMesa.
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex items-center justify-between pt-6">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200 font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            {{ __('Volver al login') }}
                        </a>

                        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold text-sm rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition-all duration-300 transform hover:-translate-y-0.5">
                            @if(isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'])
                                {{ __('✨ Actualizar Perfil') }}
                            @else
                                {{ __('🚀 Completar Registro') }}
                            @endif
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Tu información está protegida y encriptada de forma segura
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .role-option {
            animation: slideIn 0.5s ease-out forwards;
        }

        .role-option:nth-child(2) {
            animation-delay: 0.1s;
        }

        .role-option:nth-child(3) {
            animation-delay: 0.2s;
        }

        /* Efectos de hover mejorados */
        .role-option label:hover {
            transform: translateY(-2px);
        }

        /* Radio button personalizado */
        .role-option input[type="radio"]:checked + label .w-5 {
            background-color: currentColor;
            border-color: currentColor;
        }

        /* Gradientes animados en los iconos */
        .role-option:hover .w-12 {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        /* Efectos de glassmorphism sutiles */
        .bg-gradient-to-r {
            backdrop-filter: blur(10px);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleInputs = document.querySelectorAll('input[name="role"]');
            const form = document.querySelector('form');
            
            // Animaciones de entrada escalonadas
            const roleOptions = document.querySelectorAll('.role-option');
            roleOptions.forEach((option, index) => {
                setTimeout(() => {
                    option.style.opacity = '1';
                    option.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Efectos de selección mejorados
            roleInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Remover estilos de todas las opciones
                    document.querySelectorAll('.role-option label').forEach(label => {
                        label.style.transform = 'scale(1)';
                    });
                    
                    // Animar la opción seleccionada
                    const selectedLabel = this.nextElementSibling;
                    selectedLabel.style.transform = 'scale(1.02)';
                    
                    setTimeout(() => {
                        selectedLabel.style.transform = 'scale(1)';
                    }, 200);
                });
            });

            // Validación visual mejorada
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Procesando...';
                submitButton.disabled = true;
                
                // Restaurar el botón si hay error de validación
                setTimeout(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }, 3000);
            });

            // Efectos de hover en los términos
            const termsLinks = document.querySelectorAll('a[href="#"]');
            termsLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-1px)';
                });
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</x-guest-layout>