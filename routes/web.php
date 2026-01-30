<?php

use Illuminate\Support\Facades\Route; #Ya viene con esto
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\FacturaController;

# Ruta para bienvenida de Laravel
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

// Route::resource('clientes', ClienteController::class);
// Route::resource('productos', ProductoController::class);
// Route::resource('facturacion', FacturaController::class);

