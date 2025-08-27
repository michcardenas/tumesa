<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chef\ChefController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagoController;


/*
|--------------------------------------------------------------------------
| Página Principal
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');


//detalle cena
Route::prefix('cenas')->name('cenas.')->group(function () {
    Route::get('/', [HomeController::class, 'search'])->name('index');
    Route::get('/{cena}', [HomeController::class, 'showCena'])->name('show'); // Para usuarios finales
});
Route::get('/pago-exito', fn() => '✅ Pago exitoso')->name('pago.exito');
Route::get('/pago-error', fn() => '❌ Pago fallido')->name('pago.error');
Route::get('/pago-pendiente', fn() => '🕓 Pago pendiente')->name('pago.pendiente');


//pagos

Route::post('/reservar', [PagoController::class, 'reservar'])->name('reservar');
Route::get('/pago-exito/{codigoReserva}', [App\Http\Controllers\PagoController::class, 'pagoExito'])->name('pago.exito');
Route::get('/pago-error/{codigoReserva}', [App\Http\Controllers\PagoController::class, 'pagoError'])->name('pago.error');
Route::get('/pago-pendiente/{codigoReserva}', [App\Http\Controllers\PagoController::class, 'pagoPendiente'])->name('pago.pendiente');

//comensal 
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/comensal/dashboard', [App\Http\Controllers\ComensalController::class, 'dashboard'])->name('comensal.dashboard');
    Route::get('/comensal/checkout/{cena}', [App\Http\Controllers\ComensalController::class, 'checkout'])->name('comensal.checkout');
    Route::post('/comensal/procesar-reserva', [App\Http\Controllers\ComensalController::class, 'procesarReserva'])->name('comensal.procesar-reserva');


});
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


//Chef
Route::middleware(['auth'])->prefix('chef')->name('chef.')->group(function () {
    Route::get('/dashboard', [ChefController::class, 'dashboard'])->name('dashboard');
        Route::post('/dinners', [ChefController::class, 'storeDinner'])->name('dinners.store');
          Route::get('/dinners/{cena}', [ChefController::class, 'showDinner'])->name('dinners.show');
    Route::get('/dinners/{cena}/edit', [ChefController::class, 'editDinner'])->name('dinners.edit');

    
  
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