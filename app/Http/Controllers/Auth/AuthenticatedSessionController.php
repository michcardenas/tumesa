<?php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 🚀 AQUÍ ES DONDE AGREGAMOS LA REDIRECCIÓN POR ROL
        $user = Auth::user();
        
        // Verificar el rol y redirigir
        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'));
                
            case 'chef_anfitrion':
                // Cuando crees el dashboard del chef
                return redirect()->intended('/chef/dashboard');
                
            case 'comensal':
            default:
                // Dashboard normal para comensales
                return redirect()->intended(RouteServiceProvider::HOME);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}