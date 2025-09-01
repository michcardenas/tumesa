<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cena;
use Illuminate\Support\Facades\Log;

class IngresosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Verificación de permisos (similar al ChefController)
        $hasChefRole = false;
        
        if ($user->role === 'chef_anfitrion') {
            $hasChefRole = true;
        } elseif (method_exists($user, 'hasRole')) {
            if ($user->hasRole('chef') || $user->hasRole('chef_anfitrion')) {
                $hasChefRole = true;
            }
        }
        
        if (!$hasChefRole) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Obtener datos de ingresos
        $userId = $user->id;
        $currentMonth = now()->startOfMonth();
        $nextMonth = now()->addMonth()->startOfMonth();

        $ingresos = [
            'mes' => Cena::where('user_id', $userId)
                ->whereBetween('datetime', [$currentMonth, $nextMonth])
                ->where('status', 'completed')
                ->sum(\DB::raw('price * guests_current')),
                
            'pendientes' => Cena::where('user_id', $userId)
                ->where('datetime', '>', now())
                ->where('status', 'published')
                ->sum(\DB::raw('price * guests_max')),
                
            'total' => Cena::where('user_id', $userId)
                ->where('status', 'completed')
                ->sum(\DB::raw('price * guests_current')),
                
            'cenas_mes' => Cena::where('user_id', $userId)
                ->whereBetween('datetime', [$currentMonth, $nextMonth])
                ->count(),
                
            'cenas_pendientes' => Cena::where('user_id', $userId)
                ->where('datetime', '>', now())
                ->where('status', 'published')
                ->count(),
                
            'crecimiento' => 15 // Calcular comparando con mes anterior
        ];

        return view('chef.ingresos', compact('ingresos', 'user'));
    }
}