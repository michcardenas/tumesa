<x-guest-layout>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-white rounded-full flex items-center justify-center shadow-md border border-gray-200">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                    Completa tu perfil
                </h2>
                <p class="mt-2 text-center text-sm text-slate-600">
                    @if(isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'])
                        {{ __('¡Bienvenido de nuevo! Para continuar, selecciona tu tipo de cuenta.') }}
                    @else
                        {{ __('Para completar tu registro, selecciona tu tipo de cuenta.') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-sm sm:rounded-lg sm:px-10 border border-gray-200">
                <!-- Información del usuario de Google -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center space-x-3">
                        @if($googleUserData['avatar'])
                            <div class="relative">
                                <img src="{{ $googleUserData['avatar'] }}" alt="Avatar" class="w-12 h-12 rounded-full border-2 border-white shadow-sm">
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-lg shadow-sm">
                                {{ substr($googleUserData['name'], 0, 1) }}
                            </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $googleUserData['name'] }}</h3>
                            <p class="text-xs text-slate-600 truncate">{{ $googleUserData['email'] }}</p>
                            <p class="text-xs text-green-600 flex items-center mt-1">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                Verificado con Google
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('auth.google.complete-registration.store') }}" class="space-y-6">
                    @csrf

                    <!-- Tipo de cuenta -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-3">
                            ¿Cómo quieres usar TuMesa?
                        </label>
                        
                        <div class="space-y-3">
                            <!-- Opción Comensal -->
                            <div class="relative">
                                <input id="role_comensal" name="role" type="radio" value="comensal" 
                                       class="sr-only peer"
                                       {{ old('role') == 'comensal' ? 'checked' : '' }} required>
                                <label for="role_comensal" class="flex items-center p-4 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-blue-600 hover:bg-gray-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all duration-200">
                                    <div class="flex-shrink-0 mr-3">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center text-lg">
                                            🍽️
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">Comensal</h4>
                                                <p class="text-xs text-slate-600 mt-1">Reserva mesas y disfruta experiencias culinarias</p>
                                            </div>
                                            <div class="w-4 h-4 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-blue-600 peer-checked:bg-blue-600">
                                                <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Opción Chef Anfitrión -->
                            <div class="relative">
                                <input id="role_chef_anfitrion" name="role" type="radio" value="chef_anfitrion" 
                                       class="sr-only peer"
                                       {{ old('role') == 'chef_anfitrion' ? 'checked' : '' }} required>
                                <label for="role_chef_anfitrion" class="flex items-center p-4 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-blue-600 hover:bg-gray-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all duration-200">
                                    <div class="flex-shrink-0 mr-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-lg">
                                            👨‍🍳
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">Chef Anfitrión</h4>
                                                <p class="text-xs text-slate-600 mt-1">Ofrece experiencias y gestiona tu restaurante</p>
                                            </div>
                                            <div class="w-4 h-4 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-blue-600 peer-checked:bg-blue-600">
                                                <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex items-start">
                            <input id="terms" name="terms" type="checkbox" value="1" 
                                   class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-600"
                                   {{ old('terms') ? 'checked' : '' }} required>
                            <label for="terms" class="ml-3 text-xs text-slate-600 leading-relaxed">
                                Acepto los 
                                <a href="#" class="text-blue-600 hover:text-blue-500 underline font-medium">
                                    términos y condiciones
                                </a> 
                                y la 
                                <a href="#" class="text-blue-600 hover:text-blue-500 underline font-medium">
                                    política de privacidad
                                </a>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('terms')" class="mt-2" />
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-blue-600 font-medium transition-colors duration-200">
                            ← Volver al login
                        </a>

                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-medium py-2.5 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                            @if(isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'])
                                {{ __('Actualizar perfil') }}
                            @else
                                {{ __('Completar registro') }}
                            @endif
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-xs text-slate-500">
                    Tu información está protegida y segura
                </p>
            </div>
        </div>
    </div>

    <style>
        /* Radio button personalizado que funciona con la estructura peer */
        input[type="radio"]:checked + label .w-4 {
            border-color: #2563eb;
            background-color: #2563eb;
        }
        
        input[type="radio"]:checked + label .w-1\.5 {
            opacity: 1;
        }
        
        /* Hover effects */
        label:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Focus states */
        input[type="radio"]:focus + label {
            ring: 2px;
            ring-color: #2563eb;
            ring-opacity: 0.5;
        }
        
        /* Smooth transitions */
        label {
            transition: all 0.2s ease;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                
                submitButton.textContent = 'Procesando...';
                submitButton.disabled = true;
                
                // Restaurar si hay error
                setTimeout(() => {
                    if (submitButton.disabled) {
                        submitButton.textContent = originalText;
                        submitButton.disabled = false;
                    }
                }, 5000);
            });
        });
    </script>
</x-guest-layout>