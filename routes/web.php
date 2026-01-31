<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

# Rutas básicas para la navegación del proyecto
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

// Dashboard
Route::view('/dashboard', 'dashboard.index')->name('dashboard.index');

// Clientes (vistas simples)
Route::get('/clientes', function () { return view('clientes.index'); })->name('clientes.index');
Route::get('/clientes/create', function () { return view('clientes.create'); })->name('clientes.create');
Route::get('/clientes/{cliente}/edit', function ($cliente) { return view('clientes.edit', compact('cliente')); })->name('clientes.edit');
Route::get('/clientes/{cliente}', function ($cliente) { return view('clientes.show', compact('cliente')); })->name('clientes.show');

// Productos (vistas simples)
Route::get('/productos', function () { return view('productos.index'); })->name('productos.index');
Route::get('/productos/create', function () { return view('productos.create'); })->name('productos.create');
Route::get('/productos/{producto}/edit', function ($producto) { return view('productos.edit', compact('producto')); })->name('productos.edit');
Route::get('/productos/{producto}', function ($producto) { return view('productos.show', compact('producto')); })->name('productos.show');

// Facturación
Route::get('/facturacion', function () { return view('facturacion.index'); })->name('facturacion.index');
Route::get('/facturacion/create', function () { return view('facturacion.create'); })->name('facturacion.create');
Route::get('/facturacion/{factura}', function ($factura) { return view('facturacion.show', compact('factura')); })->name('facturacion.show');

// Reportes
Route::get('/reportes/ventas', function () { return view('reportes.ventas'); })->name('reportes.ventas');
Route::get('/reportes/facturas', function () { return view('reportes.facturas'); })->name('reportes.facturas');

// Logout (simple).
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Rutas de autenticación mínimas (vistas existentes)
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', function (Request $request) { return back()->with('error', 'Inicio de sesión no implementado.'); })->name('login.post');
Route::get('/register', function () { return redirect()->route('login'); })->name('register');
Route::get('/password/reset', function () { return view('auth.forgot-password'); })->name('password.request');

// Endpoints de formulario (stubs) para que los formularios no fallen
// Clientes
Route::post('/clientes', function (Request $r) { return redirect()->route('clientes.index')->with('success', 'Cliente guardado (simulado).'); })->name('clientes.store');
Route::match(['put','patch'], '/clientes/{cliente}', function ($cliente) { return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente actualizado (simulado).'); })->name('clientes.update');
Route::delete('/clientes/{cliente}', function ($cliente) { return redirect()->route('clientes.index')->with('success', 'Cliente eliminado (simulado).'); })->name('clientes.destroy');

// Productos
Route::post('/productos', function (Request $r) { return redirect()->route('productos.index')->with('success', 'Producto guardado (simulado).'); })->name('productos.store');
Route::match(['put','patch'], '/productos/{producto}', function ($producto) { return redirect()->route('productos.show', $producto)->with('success', 'Producto actualizado (simulado).'); })->name('productos.update');
Route::delete('/productos/{producto}', function ($producto) { return redirect()->route('productos.index')->with('success', 'Producto eliminado (simulado).'); })->name('productos.destroy');

// Facturación
Route::post('/facturacion', function (Request $r) { return redirect()->route('facturacion.index')->with('success', 'Factura guardada (simulado).'); })->name('facturacion.store');
Route::match(['put','patch'], '/facturacion/{factura}', function ($factura) { return redirect()->route('facturacion.show', $factura)->with('success', 'Factura actualizada (simulado).'); })->name('facturacion.update');
Route::delete('/facturacion/{factura}', function ($factura) { return redirect()->route('facturacion.index')->with('success', 'Factura eliminada (simulado).'); })->name('facturacion.destroy');
Route::get('/facturacion/{factura}/print', function ($factura) { return view('facturacion.print', compact('factura')); })->name('facturacion.print');

