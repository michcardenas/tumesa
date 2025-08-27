<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // 🚀 REDIRECCIÓN POR ROL CON LOGS
        $user = Auth::user();
        
        // 🔍 LOG: Información del usuario autenticado
        Log::info('=== INICIO DE REDIRECCIÓN POST-LOGIN ===');
        Log::info('Usuario autenticado:', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_campo' => $user->role,
            'created_at' => $user->created_at,
        ]);

        // 🔍 LOG: Obtener roles de Spatie
        $spatieRoles = [];
        $efectiveRole = null;

        try {
            if (method_exists($user, 'getRoleNames')) {
                $spatieRoles = $user->getRoleNames()->toArray();
                Log::info('Roles de Spatie:', $spatieRoles);
                
                // 🎯 DETERMINAR ROL EFECTIVO
                // Prioridad: campo role de users > roles de Spatie
                if (!empty($user->role)) {
                    $efectiveRole = $user->role;
                    Log::info('Usando rol del campo users.role: ' . $efectiveRole);
                } else {
                    // Mapear roles de Spatie a roles del sistema
                    if (in_array('admin', $spatieRoles)) {
                        $efectiveRole = 'admin';
                    } elseif (in_array('chef', $spatieRoles) || in_array('chef_anfitrion', $spatieRoles)) {
                        $efectiveRole = 'chef_anfitrion';
                    } elseif (in_array('comensal', $spatieRoles) || in_array('cliente', $spatieRoles)) {
                        $efectiveRole = 'comensal';
                    } else {
                        $efectiveRole = 'comensal'; // default
                    }
                    Log::info('Rol determinado desde Spatie: ' . $efectiveRole);
                }
            } else {
                Log::warning('Método getRoleNames no existe - usando campo role de users');
                $efectiveRole = $user->role ?? 'comensal';
            }
        } catch (\Exception $e) {
            Log::error('Error obteniendo roles de Spatie: ' . $e->getMessage());
            $efectiveRole = $user->role ?? 'comensal';
        }

        // 🔍 LOG: Switch case debugging
        Log::info('Rol efectivo para redirección: "' . $efectiveRole . '"');
        
        switch ($efectiveRole) {
            case 'admin':
                Log::info('🔧 Caso ADMIN detectado - Redirigiendo a admin.dashboard');
                
                try {
                    $adminRoute = route('admin.dashboard');
                    Log::info('Ruta admin generada: ' . $adminRoute);
                    return redirect()->intended($adminRoute);
                } catch (\Exception $e) {
                    Log::error('Error generando ruta admin.dashboard: ' . $e->getMessage());
                    return redirect('/dashboard')->with('error', 'Error de redirección admin');
                }
                
            case 'chef_anfitrion':
                Log::info('👨‍🍳 Caso CHEF_ANFITRION detectado - Redirigiendo a chef.dashboard');
                
                try {
                    $chefRoute = route('chef.dashboard');
                    Log::info('Ruta chef generada: ' . $chefRoute);
                    Log::info('✅ REDIRIGIENDO CHEF EXITOSAMENTE a: ' . $chefRoute);
                    return redirect()->intended($chefRoute);
                } catch (\Exception $e) {
                    Log::error('Error generando ruta chef.dashboard: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                    return redirect('/dashboard')->with('error', 'Error de redirección chef - verifica que existe la ruta chef.dashboard');
                }
                
            case 'comensal':
                Log::info('🍽️ Caso COMENSAL detectado - Redirigiendo a comensal.dashboard');
                
                try {
                    $comensalRoute = route('comensal.dashboard'); // Cambiar esta línea
                    Log::info('Ruta comensal generada: ' . $comensalRoute);
                    return redirect()->intended($comensalRoute);
                } catch (\Exception $e) {
                    Log::error('Error generando ruta comensal.dashboard: ' . $e->getMessage());
                    return redirect('/dashboard')->with('error', 'Error de redirección comensal');
                }
                
            default:
                Log::warning('⚠️ ROL NO RECONOCIDO: "' . $efectiveRole . '" - Usando default (comensal)');
                Log::info('Roles Spatie detectados: ' . implode(', ', $spatieRoles));
                Log::info('Roles disponibles esperados: admin, chef_anfitrion, comensal');
                
                try {
                    $defaultRoute = route('dashboard');
                    Log::info('Ruta default generada: ' . $defaultRoute);
                    return redirect()->intended($defaultRoute);
                } catch (\Exception $e) {
                    Log::error('Error generando ruta default: ' . $e->getMessage());
                    return redirect('/dashboard')->with('error', 'Error de redirección default');
                }
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        Log::info('=== LOGOUT DE USUARIO ===');
        Log::info('Usuario cerrando sesión:', [
            'id' => $user->id ?? 'N/A',
            'name' => $user->name ?? 'N/A',
            'email' => $user->email ?? 'N/A',
            'role' => $user->role ?? 'N/A',
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        Log::info('Logout completado - Redirigiendo a home');

        return redirect('/');
    }
}