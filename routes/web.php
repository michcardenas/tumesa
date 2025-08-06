<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/experiencias', function () {
    return view('experiencias');
})->name('experiencias');

Route::get('/ser-chef', function () {
    return view('ser-chef');
})->name('ser-chef');

Route::get('/como-funciona', function () {
    return view('como-funciona');
})->name('como-funciona');
require __DIR__.'/auth.php';
