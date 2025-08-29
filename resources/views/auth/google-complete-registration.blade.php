<x-guest-layout>
    <div style="min-height: 100vh; background-color: #f8fafc; display: flex; flex-direction: column; justify-content: center; padding: 3rem 1rem;">
        <div style="margin: 0 auto; width: 100%; max-width: 28rem;">
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="margin: 0 auto 1.5rem; height: 3rem; width: 3rem; background-color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                    <svg style="height: 1.5rem; width: 1.5rem; color: #2563eb;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 style="font-size: 1.875rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">
                    Completa tu perfil
                </h2>
                <p style="font-size: 0.875rem; color: #64748b; line-height: 1.5;">
                    @if(isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'])
                        ¡Bienvenido de nuevo! Para continuar, selecciona tu tipo de cuenta.
                    @else
                        Para completar tu registro, selecciona tu tipo de cuenta.
                    @endif
                </p>
            </div>

            <!-- Card principal -->
            <div style="background-color: #ffffff; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                
                <!-- Información del usuario de Google -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: #f8fafc; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: center;">
                        @if($googleUserData['avatar'])
                            <div style="position: relative; margin-right: 0.75rem;">
                                <img src="{{ $googleUserData['avatar'] }}" alt="Avatar" style="width: 3rem; height: 3rem; border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <div style="position: absolute; bottom: -0.25rem; right: -0.25rem; width: 1.25rem; height: 1.25rem; background-color: #10b981; border-radius: 50%; border: 2px solid #ffffff; display: flex; align-items: center; justify-content: center;">
                                    <svg style="width: 0.5rem; height: 0.5rem; color: #ffffff;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                </div>
                            </div>
                        @else
                            <div style="width: 3rem; height: 3rem; background-color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 600; font-size: 1.125rem; margin-right: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                {{ substr($googleUserData['name'], 0, 1) }}
                            </div>
                        @endif
                        
                        <div style="flex: 1; min-width: 0;">
                            <h3 style="font-size: 0.875rem; font-weight: 500; color: #111827; margin: 0 0 0.25rem 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $googleUserData['name'] }}</h3>
                            <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $googleUserData['email'] }}</p>
                            <p style="font-size: 0.75rem; color: #10b981; margin: 0; display: flex; align-items: center;">
                                <svg style="width: 0.75rem; height: 0.75rem; margin-right: 0.25rem;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                Verificado con Google
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('auth.google.complete-registration.store') }}">
                    @csrf

                    <!-- Selector de tipo de cuenta -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.875rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.75rem;">
                            ¿Cómo quieres usar TuMesa?
                        </label>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <!-- Opción Comensal -->
                            <div style="position: relative;">
                                <input id="role_comensal" name="role" type="radio" value="comensal" 
                                       style="position: absolute; opacity: 0;"
                                       {{ old('role') == 'comensal' ? 'checked' : '' }} required>
                                <label for="role_comensal" style="display: flex; align-items: center; padding: 1rem; background-color: #ffffff; border: 2px solid #e5e7eb; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s ease; position: relative;">
                                    <div style="flex-shrink: 0; margin-right: 0.75rem;">
                                        <div style="width: 2.5rem; height: 2.5rem; background: linear-gradient(135deg, #fed7aa, #fb923c); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                            🍽️
                                        </div>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <div>
                                                <h4 style="font-size: 0.875rem; font-weight: 500; color: #111827; margin: 0 0 0.25rem 0;">Comensal</h4>
                                                <p style="font-size: 0.75rem; color: #64748b; margin: 0; line-height: 1.4;">Reserva mesas y disfruta experiencias culinarias únicas</p>
                                            </div>
                                            <div class="radio-indicator" style="width: 1rem; height: 1rem; border: 2px solid #d1d5db; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 0.5rem;">
                                                <div style="width: 0.375rem; height: 0.375rem; background-color: #2563eb; border-radius: 50%; opacity: 0; transition: opacity 0.2s ease;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Opción Chef Anfitrión -->
                            <div style="position: relative;">
                                <input id="role_chef_anfitrion" name="role" type="radio" value="chef_anfitrion" 
                                       style="position: absolute; opacity: 0;"
                                       {{ old('role') == 'chef_anfitrion' ? 'checked' : '' }} required>
                                <label for="role_chef_anfitrion" style="display: flex; align-items: center; padding: 1rem; background-color: #ffffff; border: 2px solid #e5e7eb; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s ease; position: relative;">
                                    <div style="flex-shrink: 0; margin-right: 0.75rem;">
                                        <div style="width: 2.5rem; height: 2.5rem; background: linear-gradient(135deg, #dbeafe, #3b82f6); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                            👨‍🍳
                                        </div>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <div>
                                                <h4 style="font-size: 0.875rem; font-weight: 500; color: #111827; margin: 0 0 0.25rem 0;">Chef Anfitrión</h4>
                                                <p style="font-size: 0.75rem; color: #64748b; margin: 0; line-height: 1.4;">Ofrece experiencias culinarias y gestiona tu restaurante</p>
                                            </div>
                                            <div class="radio-indicator" style="width: 1rem; height: 1rem; border: 2px solid #d1d5db; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 0.5rem;">
                                                <div style="width: 0.375rem; height: 0.375rem; background-color: #2563eb; border-radius: 50%; opacity: 0; transition: opacity 0.2s ease;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('role')
                            <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Términos y condiciones -->
                    <div style="margin-bottom: 1.5rem; padding: 0.75rem; background-color: #f8fafc; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                        <div style="display: flex; align-items: flex-start;">
                            <input id="terms" name="terms" type="checkbox" value="1" 
                                   style="margin-top: 0.125rem; height: 1rem; width: 1rem; color: #2563eb; border-color: #d1d5db; border-radius: 0.25rem;"
                                   {{ old('terms') ? 'checked' : '' }} required>
                            <label for="terms" style="margin-left: 0.75rem; font-size: 0.75rem; color: #64748b; line-height: 1.5; cursor: pointer;">
                                Acepto los 
                                <a href="#" style="color: #2563eb; text-decoration: underline; font-weight: 500;">términos y condiciones</a> 
                                y la 
                                <a href="#" style="color: #2563eb; text-decoration: underline; font-weight: 500;">política de privacidad</a>
                            </label>
                        </div>
                        @error('terms')
                            <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                        <a href="{{ route('login') }}" style="font-size: 0.875rem; color: #64748b; font-weight: 500; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#64748b'">
                            ← Volver al login
                        </a>

                        <button type="submit" style="background-color: #1e293b; color: #ffffff; font-weight: 500; padding: 0.625rem 1.5rem; border-radius: 0.5rem; border: none; cursor: pointer; transition: background-color 0.2s ease; font-size: 0.875rem;" onmouseover="this.style.backgroundColor='#0f172a'" onmouseout="this.style.backgroundColor='#1e293b'">
                            @if(isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'])
                                Actualizar perfil
                            @else
                                Completar registro
                            @endif
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="font-size: 0.75rem; color: #9ca3af;">
                    Tu información está protegida y segura
                </p>
            </div>
        </div>
    </div>

    <style>
        /* Efectos para los radio buttons */
        input[type="radio"]:checked + label {
            border-color: #2563eb !important;
            background-color: #eff6ff !important;
        }
        
        input[type="radio"]:checked + label .radio-indicator {
            border-color: #2563eb !important;
        }
        
        input[type="radio"]:checked + label .radio-indicator div {
            opacity: 1 !important;
        }
        
        /* Hover effects para las opciones */
        label:hover {
            border-color: #93c5fd !important;
            background-color: #f8fafc !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Focus states */
        input[type="radio"]:focus + label {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }
        
        input[type="checkbox"]:focus {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            .container-main {
                padding: 1rem !important;
            }
            
            .card-main {
                padding: 1.5rem !important;
            }
            
            .button-container {
                flex-direction: column !important;
                gap: 1rem;
            }
            
            .button-container a,
            .button-container button {
                width: 100% !important;
                text-align: center !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            
            // Manejar envío del formulario
            form.addEventListener('submit', function(e) {
                submitButton.textContent = 'Procesando...';
                submitButton.disabled = true;
                submitButton.style.opacity = '0.7';
                
                // Restaurar si hay error
                setTimeout(() => {
                    if (submitButton.disabled) {
                        submitButton.textContent = originalButtonText;
                        submitButton.disabled = false;
                        submitButton.style.opacity = '1';
                    }
                }, 5000);
            });

            // Mejorar la accesibilidad con teclado
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('keydown', function(e) {
                    if (e.key === ' ') {
                        e.preventDefault();
                        this.checked = true;
                        this.dispatchEvent(new Event('change'));
                    }
                });
            });
        });
    </script>
</x-guest-layout>