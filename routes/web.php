<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;

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

require __DIR__.'/auth.php';
// Catálogo público (cualquiera puede ver productos, sin login)
Route::get('/tienda', [ProductoController::class, 'catalogo'])->name('tienda.index');
Route::get('/tienda/{producto}', [ProductoController::class, 'show'])->name('tienda.show');

// Administración (requiere estar logueado)
Route::middleware(['auth'])->group(function () {
    Route::resource('categorias', CategoriaController::class);
    Route::resource('productos', ProductoController::class)->except(['show']);
    Route::resource('categorias', CategoriaController::class)->except(['show']);
});
