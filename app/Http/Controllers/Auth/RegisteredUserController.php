<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
 public function store(Request $request): RedirectResponse
{
    // Validación incluyendo el campo role
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role' => ['required', 'string', 'in:comensal,chef_anfitrion'], // ✅ Validar rol
        'terms' => ['required', 'accepted'], // ✅ Validar términos y condiciones
    ]);

    // Crear usuario con el role incluido
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role, // ✅ Guardar el rol en la tabla users
        'provider' => 'manual', // 📝 NUEVO: Indicar que es registro manual (no Google)
    ]);

    // 🚀 ASIGNAR ROLES DE SPATIE (si tienes Spatie instalado)
    try {
        // Mapear los roles del formulario a los nombres que usas en Spatie
        $spatieRoleMap = [
            'comensal' => 'comensal',        // También podrías usar 'cliente'
            'chef_anfitrion' => 'chef',      // Mapear a 'chef' que tienes en tu BD
        ];

        $spatieRole = $spatieRoleMap[$request->role] ?? 'comensal';
        
        // Asignar el rol usando Spatie
        $user->assignRole($spatieRole);
        
        Log::info('Usuario registrado con roles:', [
            'user_id' => $user->id,
            'role_campo' => $user->role,
            'spatie_role' => $spatieRole,
            'provider' => 'manual', // 📝 NUEVO: Log del provider
            'registration_method' => 'formulario_manual', // 📝 NUEVO: Log del método de registro
        ]);
        
    } catch (\Exception $e) {
        // Si Spatie no está configurado o hay error, continuar sin problema
        Log::error('No se pudo asignar rol de Spatie: ' . $e->getMessage());
    }

    // Disparar evento de usuario registrado
    event(new Registered($user));

    // Hacer login automático
    Auth::login($user);

    // 📝 NUEVO: Log de inicio de redirección post-registro
    Log::info('=== REDIRECCIÓN POST-REGISTRO MANUAL ===');
    Log::info('Usuario creado y logueado:', [
        'user_id' => $user->id,
        'role' => $user->role,
        'provider' => 'manual',
    ]);

    // 🎯 REDIRECCIONAR SEGÚN EL ROL
    switch ($user->role) {
        case 'admin':
            Log::info('🔧 Redirigiendo nuevo usuario admin');
            return redirect()->intended(route('admin.dashboard'));
            
        case 'chef_anfitrion':
            Log::info('👨‍🍳 Redirigiendo nuevo usuario chef_anfitrion');
            return redirect()->intended(route('chef.dashboard'));
            
        case 'comensal':
        default:
            Log::info('🍽️ Redirigiendo nuevo usuario comensal (o default)');
            return redirect()->intended(route('dashboard'));
    }
}
public function redirectToGoogle(): RedirectResponse
{
    return Socialite::driver('google')->redirect();
}

/**
 * Handle Google OAuth callback
 */
public function handleGoogleCallback(Request $request): RedirectResponse
{
    try {
        $googleUser = Socialite::driver('google')->user();
        
        // Verificar si el usuario ya existe
        $user = User::where('email', $googleUser->getEmail())->first();
        
        if ($user) {
            // Usuario existente - actualizar información de Google si es necesario
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'provider' => 'google',
            ]);
            
            Log::info('Usuario existente logueado via Google:', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } else {
            // Crear nuevo usuario desde Google
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'provider' => 'google',
                'role' => 'comensal', // Rol por defecto para usuarios de Google
                'email_verified_at' => now(), // Google ya verificó el email
            ]);

            // 🚀 ASIGNAR ROL POR DEFECTO DE SPATIE
            try {
                $user->assignRole('comensal'); // Rol por defecto
                
                Log::info('Nuevo usuario creado via Google:', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => 'comensal',
                ]);
            } catch (\Exception $e) {
                Log::error('No se pudo asignar rol de Spatie al usuario de Google: ' . $e->getMessage());
            }

            // Disparar evento de usuario registrado
            event(new Registered($user));
        }

        // Hacer login
        Auth::login($user);

        // Redireccionar según el rol usando el método helper
        return $this->redirectUserByRole($user);
        
    } catch (Exception $e) {
        Log::error('Error en Google OAuth: ' . $e->getMessage());
        return redirect('/login')->withErrors(['error' => 'Error al autenticar con Google. Por favor, intenta de nuevo.']);
    }
}

/**
 * Redirect user based on their role (método helper)
 */
private function redirectUserByRole(User $user): RedirectResponse
{
    // 📝 Log de redirección
    Log::info('=== REDIRECCIÓN POST-LOGIN GOOGLE ===');
    Log::info('Usuario logueado:', [
        'user_id' => $user->id,
        'role' => $user->role,
        'provider' => $user->provider,
    ]);

    switch ($user->role) {
        case 'admin':
            Log::info('🔧 Redirigiendo usuario admin (Google)');
            return redirect()->intended(route('admin.dashboard'));
            
        case 'chef_anfitrion':
            Log::info('👨‍🍳 Redirigiendo usuario chef_anfitrion (Google)');
            return redirect()->intended(route('chef.dashboard'));
            
        case 'comensal':
        default:
            Log::info('🍽️ Redirigiendo usuario comensal (Google o default)');
            return redirect()->intended(route('dashboard'));
    }
}
}