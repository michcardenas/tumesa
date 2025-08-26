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
            ]);
            
        } catch (\Exception $e) {
            // Si Spatie no está configurado o hay error, continuar sin problema
            Log::error('No se pudo asignar rol de Spatie: ' . $e->getMessage());
        }

        // Disparar evento de usuario registrado
        event(new Registered($user));

        // Hacer login automático
        Auth::login($user);

        // 🎯 REDIRECCIONAR SEGÚN EL ROL
        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'));
                
            case 'chef_anfitrion':
                return redirect()->intended(route('chef.dashboard'));
                
            case 'comensal':
            default:
                return redirect()->intended(route('dashboard'));
        }
    }
}