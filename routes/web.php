<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Página Principal
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    // Si está autenticado, redirigir al dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    // Si no, mostrar welcome
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin autenticación)
|--------------------------------------------------------------------------
*/
Route::get('/experiencias', function () {
    return view('experiencias');
})->name('experiencias');

Route::get('/ser-chef', function () {
    return view('ser-chef');
})->name('ser-chef');

Route::get('/como-funciona', function () {
    return view('como-funciona');
})->name('como-funciona');

/*
|--------------------------------------------------------------------------
| Rutas de Usuario Autenticado
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Ruta simplificada para el perfil (para navbar)
    Route::get('/mi-perfil', function () {
        return redirect()->route('profile.edit');
    })->name('profile');
    
    // Reservas del usuario
    Route::get('/mis-reservas', function () {
        return view('reservas.index');
    })->name('reservas');
});

/*
|--------------------------------------------------------------------------
| Rutas de Administrador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard principal del admin
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestión de Páginas (CRUD completo)
    Route::resource('pages', PageController::class);
});

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';