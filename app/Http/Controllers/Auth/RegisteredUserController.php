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
use Illuminate\Support\Str;

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
            
            // ✅ VERIFICAR SI EL USUARIO EXISTENTE TIENE ROL
            if (empty($user->role) || $user->role === null) {
                Log::info('🔄 Usuario existente SIN ROL - Enviando a completar registro:', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'current_role' => $user->role ?? 'null',
                ]);
                
                // Guardar datos en sesión incluyendo el user_id para actualizar en lugar de crear
                session([
                    'google_user_data' => [
                        'user_id' => $user->id,  // 🆕 ID del usuario existente
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'is_existing_user' => true, // 🆕 Bandera para saber que existe
                    ]
                ]);

                // Redirigir al formulario de completar registro
                return redirect()->route('auth.google.complete-registration');
            }
            
            // Usuario existente CON ROL - login normal
            Log::info('Usuario existente CON ROL logueado via Google:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ]);

            // Hacer login
            Auth::login($user);

            // Redireccionar según el rol
            return $this->redirectUserByRole($user);
        } else {
            // 🚀 USUARIO NUEVO - GUARDAR DATOS EN SESIÓN Y REDIRIGIR A FORMULARIO
            Log::info('🆕 Nuevo usuario de Google - Guardando datos en sesión para completar registro');
            
            // Guardar datos de Google en la sesión
            session([
                'google_user_data' => [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'is_existing_user' => false, // 🆕 Bandera para saber que es nuevo
                ]
            ]);

            Log::info('Datos de Google guardados en sesión:', [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
            ]);

            // Redirigir al formulario de registro adicional
            return redirect()->route('auth.google.complete-registration');
        }
        
    } catch (Exception $e) {
        Log::error('Error en Google OAuth: ' . $e->getMessage());
        return redirect('/login')->withErrors(['error' => 'Error al autenticar con Google. Por favor, intenta de nuevo.']);
    }
}

/**
 * 🆕 Mostrar formulario para completar registro de usuario de Google
 */
public function showGoogleCompleteRegistration(): View
{
    // Verificar que hay datos de Google en sesión
    $googleUserData = session('google_user_data');
    
    if (!$googleUserData) {
        Log::warning('Intento de acceso a completar registro sin datos de Google en sesión');
        return redirect('/login')->withErrors(['error' => 'Sesión expirada. Por favor, inicia sesión de nuevo.']);
    }

    Log::info('🖥️ Mostrando formulario de completar registro para usuario de Google:', [
        'email' => $googleUserData['email']
    ]);

    return view('auth.google-complete-registration', compact('googleUserData'));
}

/**
 * 🆕 Procesar formulario de registro adicional de Google
 */
public function storeGoogleCompleteRegistration(Request $request): RedirectResponse
{
    // Debug: Log de inicio
    Log::info('=== PROCESANDO FORMULARIO GOOGLE ===');
    Log::info('Request data:', $request->all());
    
    // Verificar que hay datos de Google en sesión
    $googleUserData = session('google_user_data');
    
    if (!$googleUserData) {
        Log::warning('No hay datos de Google en sesión');
        return redirect('/login')->withErrors(['error' => 'Sesión expirada. Por favor, inicia sesión de nuevo.']);
    }

    Log::info('Datos de Google en sesión:', $googleUserData);

    // Validar el formulario
    try {
        $request->validate([
            'role' => ['required', 'string', 'in:comensal,chef_anfitrion'],
            'terms' => ['required', 'accepted'],
        ]);
        
        Log::info('Validación pasó correctamente');
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Error de validación:', $e->errors());
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();
    }

    try {
        // Verificar si es usuario existente o nuevo
        if (isset($googleUserData['is_existing_user']) && $googleUserData['is_existing_user'] === true) {
            // USUARIO EXISTENTE - ACTUALIZAR ROL
            $user = User::find($googleUserData['user_id']);
            
            if (!$user) {
                Log::error('Usuario existente no encontrado con ID: ' . $googleUserData['user_id']);
                return redirect('/login')->withErrors(['error' => 'Error en el sistema. Por favor, intenta de nuevo.']);
            }

            // Actualizar rol del usuario existente
            $user->update([
                'role' => $request->role,
                'google_id' => $googleUserData['google_id'],
                'avatar' => $googleUserData['avatar'] ?? $user->avatar,
                'provider' => 'google',
            ]);

            Log::info('Usuario existente actualizado:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'new_role' => $request->role,
            ]);
            
        } else {
            // USUARIO NUEVO - CREAR USUARIO
            $user = User::create([
                'name' => $googleUserData['name'],
                'email' => $googleUserData['email'],
                'google_id' => $googleUserData['google_id'],
                'avatar' => $googleUserData['avatar'] ?? null,
                'provider' => 'google',
                'role' => $request->role,
                'password' => Hash::make(Str::random(32)), // Contraseña aleatoria para usuarios de Google
                'email_verified_at' => now(),
            ]);

            // Disparar evento de usuario registrado solo para usuarios nuevos
            event(new Registered($user));

            Log::info('Usuario nuevo creado:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $request->role,
            ]);
        }

        // ASIGNAR ROL DE SPATIE (si está configurado)
        try {
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $spatieRoleMap = [
                    'comensal' => 'comensal',
                    'chef_anfitrion' => 'chef',
                ];

                $spatieRole = $spatieRoleMap[$request->role] ?? 'comensal';
                
                // Limpiar roles anteriores y asignar el nuevo
                $user->syncRoles([$spatieRole]);
                
                Log::info('Rol de Spatie asignado:', [
                    'user_id' => $user->id,
                    'spatie_role' => $spatieRole,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('No se pudo asignar rol de Spatie: ' . $e->getMessage());
            // Continuar sin fallo crítico
        }

        // Limpiar datos de Google de la sesión
        session()->forget('google_user_data');

        // Hacer login
        Auth::login($user);

        Log::info('Usuario logueado exitosamente:', [
            'user_id' => $user->id,
            'role' => $user->role,
        ]);

        // Redireccionar según el rol
        return $this->redirectUserByRole($user);
        
    } catch (\Exception $e) {
        Log::error('Error al procesar usuario desde Google:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->withErrors(['error' => 'Error al completar el registro. Por favor, intenta de nuevo.'])
            ->withInput();
    }
}

/**
 * Redirect user based on their role
 */
private function redirectUserByRole(User $user): RedirectResponse
{
    Log::info('Redirigiendo usuario:', [
        'user_id' => $user->id,
        'role' => $user->role,
    ]);

    switch ($user->role) {
        case 'admin':
            try {
                return redirect()->route('admin.dashboard');
            } catch (\Exception $e) {
                Log::error('Ruta admin.dashboard no existe');
                return redirect('/dashboard')->with('success', 'Perfil actualizado correctamente');
            }
            
        case 'chef_anfitrion':
            try {
                return redirect()->route('chef.dashboard');
            } catch (\Exception $e) {
                Log::error('Ruta chef.dashboard no existe');
                return redirect('/dashboard')->with('success', 'Perfil actualizado correctamente');
            }
            
        case 'comensal':
        default:
            try {
                return redirect()->route('dashboard');
            } catch (\Exception $e) {
                Log::error('Ruta dashboard no existe');
                return redirect('/home')->with('success', 'Perfil actualizado correctamente');
            }
    }
}
}