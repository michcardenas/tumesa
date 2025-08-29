<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        @if(isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'])
            {{ __('¡Bienvenido de nuevo! Para completar tu perfil, por favor selecciona tu tipo de cuenta.') }}
        @else
            {{ __('¡Bienvenido! Para completar tu registro con Google, por favor selecciona tu tipo de cuenta y acepta nuestros términos.') }}
        @endif
    </div>

    <!-- Información del usuario de Google -->
    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <div class="flex items-center space-x-4">
            @if($googleUserData['avatar'])
                <img src="{{ $googleUserData['avatar'] }}" alt="Avatar" class="w-12 h-12 rounded-full">
            @else
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ substr($googleUserData['name'], 0, 1) }}
                </div>
            @endif
            
            <div>
                <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $googleUserData['name'] }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $googleUserData['email'] }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    Autenticado con Google
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('auth.google.complete-registration.store') }}">
        @csrf

        <!-- Tipo de cuenta -->
        <div class="mb-6">
            <x-input-label for="role" :value="__('Tipo de cuenta')" class="mb-3" />
            
            <div class="space-y-3">
                <!-- Opción Comensal -->
                <div class="flex items-start">
                    <input id="role_comensal" name="role" type="radio" value="comensal" 
                           class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-blue-600"
                           {{ old('role') == 'comensal' ? 'checked' : '' }} required>
                    <label for="role_comensal" class="ml-3 cursor-pointer">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <span class="text-lg mr-2">🍽️</span>
                            Comensal
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 ml-6">
                            Quiero reservar mesas y disfrutar experiencias culinarias
                        </div>
                    </label>
                </div>

                <!-- Opción Chef Anfitrión -->
                <div class="flex items-start">
                    <input id="role_chef_anfitrion" name="role" type="radio" value="chef_anfitrion" 
                           class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-blue-600"
                           {{ old('role') == 'chef_anfitrion' ? 'checked' : '' }} required>
                    <label for="role_chef_anfitrion" class="ml-3 cursor-pointer">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <span class="text-lg mr-2">👨‍🍳</span>
                            Chef Anfitrión
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 ml-6">
                            Quiero ofrecer experiencias culinarias y gestionar mi restaurante
                        </div>
                    </label>
                </div>
            </div>

            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Términos y condiciones -->
        <div class="mb-6">
            <div class="flex items-start">
                <input id="terms" name="terms" type="checkbox" value="1" 
                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-blue-600"
                       {{ old('terms') ? 'checked' : '' }} required>
                <label for="terms" class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                    Acepto los 
                    <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 underline">
                        términos y condiciones
                    </a> 
                    y la 
                    <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 underline">
                        política de privacidad
                    </a>
                </label>
            </div>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                {{ __('Cancelar y volver al login') }}
            </a>

            <x-primary-button class="ml-3">
                @if(isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'])
                    {{ __('Actualizar Perfil') }}
                @else
                    {{ __('Completar Registro') }}
                @endif
            </x-primary-button>
        </div>
    </form>

    <script>
        // JavaScript para mejorar la UX
        document.addEventListener('DOMContentLoaded', function() {
            const roleInputs = document.querySelectorAll('input[name="role"]');
            const labels = document.querySelectorAll('label[for^="role_"]');
            
            // Función para actualizar estilos
            function updateStyles() {
                labels.forEach(label => {
                    const input = document.getElementById(label.getAttribute('for'));
                    const container = label.closest('div');
                    
                    if (input && input.checked) {
                        container.classList.add('bg-blue-50', 'dark:bg-blue-900/30', 'border-blue-200', 'dark:border-blue-700');
                        container.classList.remove('border-transparent');
                    } else {
                        container.classList.remove('bg-blue-50', 'dark:bg-blue-900/30', 'border-blue-200', 'dark:border-blue-700');
                        container.classList.add('border-transparent');
                    }
                });
            }
            
            // Agregar bordes a los contenedores
            labels.forEach(label => {
                const container = label.closest('div');
                container.classList.add('border', 'rounded-lg', 'p-3', 'transition-all', 'duration-200', 'hover:border-gray-300', 'dark:hover:border-gray-600');
            });
            
            // Actualizar estilos al cambiar selección
            roleInputs.forEach(input => {
                input.addEventListener('change', updateStyles);
            });
            
            // Aplicar estilos iniciales
            updateStyles();
        });
    </script>
</x-guest-layout>