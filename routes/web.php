<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chef\ChefController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ExperienciasController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ComensalController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\PaginaController;
use App\Http\Controllers\SeoController;

/*
|--------------------------------------------------------------------------
| Página Principal
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/auth/google/complete-registration', [RegisteredUserController::class, 'showGoogleCompleteRegistration'])
    ->name('auth.google.complete-registration')
    ->middleware('guest');

// Ruta para procesar el formulario de completar registro de Google  
Route::post('/auth/google/complete-registration', [RegisteredUserController::class, 'storeGoogleCompleteRegistration'])
    ->name('auth.google.complete-registration.store')
    ->middleware('guest');
Route::get('/debug-google', function() {
    return [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
        'redirect_url' => env('GOOGLE_REDIRECT_URL'),
        'config' => config('services.google')
    ];
});
//detalle cena
Route::prefix('cenas')->name('cenas.')->group(function () {
    Route::get('/', [HomeController::class, 'search'])->name('index');
    Route::get('/{cena}', [HomeController::class, 'showCena'])->name('show'); // Para usuarios finales
});
Route::get('/pago-exito', fn() => '✅ Pago exitoso')->name('pago.exito');
Route::get('/pago-error', fn() => '❌ Pago fallido')->name('pago.error');
Route::get('/pago-pendiente', fn() => '🕓 Pago pendiente')->name('pago.pendiente');

Route::get('/politica-de-privacidad', [LegalController::class, 'privacidad'])->name('privacidad');
Route::get('/reseñas/{cena}/{reserva}/create', [ResenaController::class, 'create'])
    ->name('reseñas.create');
Route::post('/reseñas', [ResenaController::class, 'store'])
    ->name('reseñas.store');

//pagos
Route::patch('/chef/cenas/{cena}/terminar', [App\Http\Controllers\Chef\ChefController::class, 'terminarCena'])
    ->name('chef.cenas.terminar');

Route::post('/reservar', [PagoController::class, 'reservar'])->name('reservar');
Route::get('/pago-exito/{codigoReserva}', [App\Http\Controllers\PagoController::class, 'pagoExito'])->name('pago.exito');
Route::get('/pago-error/{codigoReserva}', [App\Http\Controllers\PagoController::class, 'pagoError'])->name('pago.error');
Route::get('/pago-pendiente/{codigoReserva}', [App\Http\Controllers\PagoController::class, 'pagoPendiente'])->name('pago.pendiente');
// Rutas para Chef
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chef/dashboard', [App\Http\Controllers\Chef\ChefController::class, 'dashboard'])->name('chef.dashboard');
    Route::post('/chef/dinners', [App\Http\Controllers\Chef\ChefController::class, 'storeDinner'])->name('chef.dinners.store');
    Route::get('/chef/dinners/{cena}/edit', [App\Http\Controllers\Chef\ChefController::class, 'editDinner'])->name('chef.dinners.edit');
    Route::get('/chef/dinners/{cena}', [App\Http\Controllers\Chef\ChefController::class, 'showDinner'])->name('chef.dinners.show');
    Route::get('/dinners/{cena}/asistencia', [AsistenciaController::class, 'show'])
    ->name('chef.dinners.asistencia');
    Route::post('/reservas/{reserva}/resetear-asistencia', [AsistenciaController::class, 'resetearAsistencia'])
    ->name('chef.reservas.resetear-asistencia');
   
});
// Rutas SEO (sin middleware)
// En routes/web.php, agrega esta ruta:
Route::put('/admin/seo/update', [SeoController::class, 'update'])->name('admin.seo.update');
Route::put('/admin/seo/update', [SeoController::class, 'update'])->name('admin.seo.update');

// En routes/web.php
Route::get('/admin/paginas/experiencias/edit', [PaginaController::class, 'editExperiencias'])->name('admin.paginas.experiencias.edit');
Route::get('/admin/paginas/ser-chef/edit', [PaginaController::class, 'editSerChef'])->name('admin.paginas.ser-chef.edit');
Route::get('/admin/paginas/como-funciona/edit', [PaginaController::class, 'editComoFunciona'])->name('admin.paginas.como-funciona.edit');
Route::put('/admin/paginas/ser-chef/update', [PaginaController::class, 'updateSerChef'])->name('admin.paginas.ser-chef.update');
Route::put('/admin/paginas/como-funciona/update', [PaginaController::class, 'updateComoFunciona'])->name('admin.paginas.como-funciona.update');

    // Rutas del Chef
Route::middleware(['auth'])->prefix('chef')->group(function () {
    // ... tus otras rutas del chef ...
    
    // AGREGAR ESTA LÍNEA:
    Route::post('/reservas/{reserva}/asistencia', [App\Http\Controllers\AsistenciaController::class, 'marcarAsistencia']);
});
//comensal 
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/comensal/dashboard', [App\Http\Controllers\ComensalController::class, 'dashboard'])->name('comensal.dashboard');
    Route::get('/comensal/checkout/{cena}', [App\Http\Controllers\ComensalController::class, 'checkout'])->name('comensal.checkout');
    Route::post('/comensal/procesar-reserva', [App\Http\Controllers\ComensalController::class, 'procesarReserva'])->name('comensal.procesar-reserva');


});

Route::get('/auth/google', [RegisteredUserController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [RegisteredUserController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

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
Route::get('/terminos-y-condiciones', [LegalController::class, 'terminos'])->name('terminos');



/*
|--------------------------------------------------------------------------
| Rutas de Usuario Autenticado
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

Route::put('/admin/paginas/experiencias/update', [PaginaController::class, 'updateExperiencias'])->name('admin.paginas.experiencias.update');
   Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Ruta simplificada para el perfil (para navbar)
    Route::get('/mi-perfil', function () {
        return redirect()->route('profile.edit');
    })->name('profile');
    
    // Reservas del usuario
Route::get('/mis-reservas', [ReservaController::class, 'historial'])->name('reservas.historial')->middleware('auth');

Route::get('/perfil-comensal', [ProfileController::class, 'perfilComensal'])
    ->name('perfil.comensal')
    ->middleware('auth');
    Route::put('/perfil-comensal', [ProfileController::class, 'updatecomensal'])->name('perfil.comensal.update');
    Route::get('/reservas/{reserva}', [ComensalController::class, 'verDetalleReserva'])
        ->name('reservas.detalle');
Route::get('/resenas', [ResenaController::class, 'index'])->name('resenas.index');

});
Route::get('/experiencias', [ExperienciasController::class, 'index'])
    ->name('experiencias');

//Chef
Route::middleware(['auth'])->prefix('chef')->name('chef.')->group(function () {
    Route::get('/dashboard', [ChefController::class, 'dashboard'])->name('dashboard');
        Route::post('/dinners', [ChefController::class, 'storeDinner'])->name('dinners.store');
          Route::get('/dinners/{cena}', [ChefController::class, 'showDinner'])->name('dinners.show');
    Route::get('/dinners/{cena}/edit', [ChefController::class, 'editDinner'])->name('dinners.edit');
        Route::put('/dinners/{cena}', [ChefController::class, 'updateDinner'])->name('dinners.update');


      Route::get('/dashboard', [ChefController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/ingresos', [IngresosController::class, 'index'])->name('ingresos');
 Route::get('/perfil/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil/update', [ProfileController::class, 'update'])->name('profile.update');
    
  
});
/*
|--------------------------------------------------------------------------
| Rutas de Administrador
|--------------------------------------------------------------------------
*/

  Route::get('/paginas/experiencias', [PaginaController::class, 'editarExperiencias'])->name('paginas.experiencias');
    Route::get('/paginas/ser-chef', [PaginaController::class, 'editarSerChef'])->name('paginas.ser-chef');
    Route::get('/paginas/como-funciona', [PaginaController::class, 'editarComoFunciona'])->name('paginas.como-funciona');
    
    // Ruta para actualizar cualquier página
    Route::put('/paginas/{paginaId}', [PaginaController::class, 'actualizar'])->name('paginas.actualizar');
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard principal del admin
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestión de Páginas (CRUD completo)
    Route::resource('pages', PageController::class);
});
Route::get('/ser-chef', [ExperienciasController::class, 'serChef'])
    ->name('ser-chef');
  Route::get('/como-funciona', [ExperienciasController::class, 'comoFunciona'])
    ->name('como-funciona');  
/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';