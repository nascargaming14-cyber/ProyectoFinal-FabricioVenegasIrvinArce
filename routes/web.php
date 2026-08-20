<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ReporteController;

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

// Administración y carrito (requiere estar logueado)
Route::middleware(['auth'])->group(function () {
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('productos', ProductoController::class)->except(['show']);

    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar/{producto}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::patch('/carrito/actualizar/{item}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::delete('/carrito/eliminar/{item}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::post('/carrito/checkout', [CarritoController::class, 'checkout'])->name('carrito.checkout');
    Route::get('/pedidos/{pedido}/confirmacion', [CarritoController::class, 'confirmacion'])->name('pedidos.confirmacion');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/mes', [ReporteController::class, 'porMes'])->name('reportes.mes');
    Route::get('/reportes/cliente', [ReporteController::class, 'porCliente'])->name('reportes.cliente');
});
