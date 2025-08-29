<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div style="background: #dbeafe; 
                    border: 1px solid #3b82f6; 
                    color: #1e40af; 
                    padding: 12px 16px; 
                    border-radius: 12px; 
                    margin-bottom: 20px; 
                    font-size: 14px;">
            {{ session('status') }}
        </div>
    @endif

    <!-- Título -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="margin: 0 0 8px 0; 
                   color: #111827; 
                   font-size: 26px; 
                   font-weight: 600;">
            Bienvenido
        </h2>
        <p style="margin: 0; 
                  color: #6b7280; 
                  font-size: 15px;">
            Ingresa a tu cuenta para continuar
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div style="margin-bottom: 20px;">
            <label for="email" 
                   style="display: block; 
                          color: #374151; 
                          font-size: 14px; 
                          font-weight: 500; 
                          margin-bottom: 8px;">
                Correo Electrónico
            </label>
            <div style="position: relative;">
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="tucorreo@ejemplo.com"
                       style="width: 100%; 
                              padding: 12px 16px 12px 44px; 
                              background: #eff8ff; 
                              border: 2px solid #dbeafe; 
                              border-radius: 12px; 
                              color: #111827; 
                              font-size: 15px; 
                              transition: all 0.3s ease; 
                              outline: none;"
                       onfocus="this.style.borderColor='#3b82f6'; this.style.background='#ffffff'; this.style.boxShadow='0 0 0 4px rgba(59, 130, 246, 0.1)';"
                       onblur="this.style.borderColor='#dbeafe'; this.style.background='#eff8ff'; this.style.boxShadow='none';">
                <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            @error('email')
                <span style="color: #dc2626; font-size: 13px; margin-top: 6px; display: block;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Password -->
        <div style="margin-bottom: 20px;">
            <label for="password" 
                   style="display: block; 
                          color: #374151; 
                          font-size: 14px; 
                          font-weight: 500; 
                          margin-bottom: 8px;">
                Contraseña
            </label>
            <div style="position: relative;">
                <input id="password" 
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••"
                       style="width: 100%; 
                              padding: 12px 16px 12px 44px; 
                              background: #eff8ff; 
                              border: 2px solid #dbeafe; 
                              border-radius: 12px; 
                              color: #111827; 
                              font-size: 15px; 
                              transition: all 0.3s ease; 
                              outline: none;"
                       onfocus="this.style.borderColor='#3b82f6'; this.style.background='#ffffff'; this.style.boxShadow='0 0 0 4px rgba(59, 130, 246, 0.1)';"
                       onblur="this.style.borderColor='#dbeafe'; this.style.background='#eff8ff'; this.style.boxShadow='none';">
                <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            @error('password')
                <span style="color: #dc2626; font-size: 13px; margin-top: 6px; display: block;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div style="display: flex; 
                    justify-content: space-between; 
                    align-items: center; 
                    margin-bottom: 24px;">
            <label style="display: flex; 
                          align-items: center; 
                          cursor: pointer; 
                          color: #4b5563; 
                          font-size: 14px;">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember"
                       style="width: 18px; 
                              height: 18px; 
                              margin-right: 8px; 
                              cursor: pointer; 
                              accent-color: #3b82f6;">
                <span>Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" 
                   style="color: #3b82f6; 
                          font-size: 14px; 
                          text-decoration: none; 
                          font-weight: 500;
                          transition: all 0.3s ease;"
                   onmouseover="this.style.color='#2563eb'; this.style.textDecoration='underline';"
                   onmouseout="this.style.color='#3b82f6'; this.style.textDecoration='none';">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <button type="submit" 
                style="width: 100%; 
                       padding: 14px 24px; 
                       background: #3b82f6; 
                       color: white; 
                       border: none; 
                       border-radius: 12px; 
                       font-size: 16px; 
                       font-weight: 600; 
                       cursor: pointer; 
                       transition: all 0.3s ease; 
                       box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3); 
                       position: relative; 
                       overflow: hidden;"
                onmouseover="this.style.background='#2563eb'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(59, 130, 246, 0.4)';"
                onmouseout="this.style.background='#3b82f6'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(59, 130, 246, 0.3)';">
            Iniciar Sesión
        </button>
    </form>

    <!-- 🚀 NUEVO: Separador y Botón de Google -->
    <div style="position: relative; margin: 24px 0;">
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 0;">
        <span style="position: absolute; 
                     top: 50%; 
                     left: 50%; 
                     transform: translate(-50%, -50%); 
                     background: white; 
                     padding: 0 16px; 
                     color: #6b7280; 
                     font-size: 14px; 
                     font-weight: 500;">
            o
        </span>
    </div>

    <!-- Google OAuth Button -->
    <a href="{{ route('auth.google') }}" 
       style="display: flex; 
              align-items: center; 
              justify-content: center; 
              width: 100%; 
              padding: 14px 24px; 
              background: #ffffff; 
              color: #374151; 
              border: 2px solid #e5e7eb; 
              border-radius: 12px; 
              font-size: 16px; 
              font-weight: 600; 
              text-decoration: none; 
              cursor: pointer; 
              transition: all 0.3s ease; 
              box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); 
              position: relative; 
              overflow: hidden;"
       onmouseover="this.style.borderColor='#d1d5db'; this.style.background='#f9fafb'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.1)';"
       onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#ffffff'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.05)';">
        
        <!-- Google Icon SVG -->
        <svg style="width: 20px; height: 20px; margin-right: 12px;" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        
        <span>Continuar con Google</span>
    </a>

    <!-- Register Link -->
    @if (Route::has('register'))
        <div style="text-align: center; 
                    margin-top: 24px; 
                    padding-top: 24px; 
                    border-top: 1px solid #e5e7eb;">
            <p style="margin: 0; 
                      color: #6b7280; 
                      font-size: 14px;">
                ¿No tienes una cuenta? 
                <a href="{{ route('register') }}" 
                   style="color: #3b82f6; 
                          font-weight: 600; 
                          text-decoration: none; 
                          transition: all 0.3s ease;"
                   onmouseover="this.style.color='#2563eb'; this.style.textDecoration='underline';"
                   onmouseout="this.style.color='#3b82f6'; this.style.textDecoration='none';">
                    Regístrate aquí
                </a>
            </p>
        </div>
    @endif

    <style>
        /* Placeholder styles */
        input::placeholder {
            color: #9ca3af;
        }
        
        /* Remove autofill background */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-text-fill-color: #111827 !important;
            -webkit-box-shadow: 0 0 0 30px #eff8ff inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        
        /* Focus styles for better accessibility */
        input:focus,
        button:focus,
        a:focus {
            outline: none;
        }
        
        /* Smooth transitions */
        input, button, a {
            transition: all 0.3s ease;
        }
        
        /* Button active state */
        button[type="submit"]:active,
        a[href]:active {
            transform: scale(0.98) !important;
        }

        /* 🚀 NUEVO: Estilos específicos para el botón de Google */
        a[href*="auth/google"]:hover {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%) !important;
        }
        
        a[href*="auth/google"]:active {
            background: #f3f4f6 !important;
            transform: scale(0.98) translateY(0px) !important;
        }
    </style>
</x-guest-layout>