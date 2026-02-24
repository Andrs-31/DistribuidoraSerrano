<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FacturacionController;
use App\Http\Controllers\ListaPrecioController;
use App\Models\Producto;
use App\Models\Cliente;


use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UbicacionController;

use App\Http\Controllers\ProductoController;

# Rutas básicas para la navegación del proyecto
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard.index') : redirect()->route('login');
});

// Rutas protegidas por auth
Route::middleware('auth')->group(function () {
    // Dashboard (acceso para cualquier usuario autenticado)
    Route::view('/dashboard', 'dashboard.index')->name('dashboard.index');

    // Clientes (requieren permiso 'gestion_clientes')
    Route::middleware('permiso:gestion_clientes')->group(function () {
        Route::resource('clientes', ClienteController::class);

        // Distritos dinámicos (AJAX)
        Route::get('/distritos/{provincia}', [UbicacionController::class, 'distritos']);

        Route::get('clientes/{cliente}/precios', [ListaPrecioController::class, 'indexPorCliente'])
        ->name('clientes.precios');
    });


    // // Productos (requieren permiso 'gestion_productos')
    // Route::middleware('permiso:gestion_productos')->group(function () {
    //     Route::get('/productos', function () { return view('productos.index'); })->name('productos.index');
    //     Route::get('/productos/create', function () { return view('productos.create'); })->name('productos.create');
    //     Route::get('/productos/{producto}/edit', function ($producto) { return view('productos.edit', compact('producto')); })->name('productos.edit');
    //     Route::get('/productos/{producto}', function ($producto) { return view('productos.show', compact('producto')); })->name('productos.show');

    //     // Productos - endpoints
    //     Route::post('/productos', function (Request $r) { return redirect()->route('productos.index')->with('success', 'Producto guardado (simulado).'); })->name('productos.store');
    //     Route::match(['put','patch'], '/productos/{producto}', function ($producto) { return redirect()->route('productos.show', $producto)->with('success', 'Producto actualizado (simulado).'); })->name('productos.update');
    //     Route::delete('/productos/{producto}', function ($producto) { return redirect()->route('productos.index')->with('success', 'Producto eliminado (simulado).'); })->name('productos.destroy');
    // });

    // Productos (requieren permiso 'gestion_productos')
    Route::middleware('permiso:gestion_productos')->group(function () {
        Route::resource('productos', ProductoController::class);

        Route::get('productos/{producto}/precios', [ListaPrecioController::class, 'indexPorProducto'])
            ->name('productos.precios');

        Route::post('productos/{producto}/precios', [ListaPrecioController::class, 'upsertPrecio'])
            ->name('productos.precios.upsert');

        Route::delete('productos/{producto}/precios/{listaPrecio}', [ListaPrecioController::class, 'desactivarPrecio'])
            ->name('productos.precios.desactivar');

        Route::post('productos/{producto}/precios/por-cliente', [ListaPrecioController::class, 'upsertPrecioPorCliente'])
            ->name('productos.precios.porCliente');


    });

    // Usuarios (requieren permiso 'gestion_usuarios')
    Route::middleware('permiso:gestion_usuarios')->group(function () {
        Route::get('/usuarios', function () { return view('usuarios.index'); })->name('usuarios.index');
        Route::get('/usuarios/create', function () { return view('usuarios.create'); })->name('usuarios.create');
        Route::post('/usuarios', function (Request $r) { return redirect()->route('usuarios.index')->with('success', 'Usuario guardado (simulado).'); })->name('usuarios.store');
        Route::get('/usuarios/{usuario}', function ($usuario) { return view('usuarios.show', compact('usuario')); })->name('usuarios.show');
        Route::get('/usuarios/{usuario}/edit', function ($usuario) { return view('usuarios.edit', compact('usuario')); })->name('usuarios.edit');
        Route::match(['put','patch'], '/usuarios/{usuario}', function ($usuario) { return redirect()->route('usuarios.show', $usuario)->with('success', 'Usuario actualizado (simulado).'); })->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', function ($usuario) { return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado (simulado).'); })->name('usuarios.destroy');
        Route::post('/usuarios/{usuario}/reset-password', function ($usuario) { return redirect()->route('usuarios.show', $usuario)->with('success', 'Contraseña reseteada (simulado).'); })->name('usuarios.reset-password');
        Route::post('/usuarios/{usuario}/toggle-status', function ($usuario) { return redirect()->route('usuarios.show', $usuario)->with('success', 'Estado cambiado (simulado).'); })->name('usuarios.toggle-status');
    });

   // Facturación (requieren permiso 'facturacion')
    Route::middleware('permiso:facturacion')->group(function () {
        Route::get('/facturacion', [FacturacionController::class, 'index'])->name('facturacion.index');
        Route::get('/facturacion/create', [FacturacionController::class, 'create'])->name('facturacion.create');
        Route::post('/facturacion', [FacturacionController::class, 'store'])->name('facturacion.store');
        Route::get('/facturacion/{factura}', [FacturacionController::class, 'show'])->name('facturacion.show');
        Route::get('/facturacion/{factura}/edit', [FacturacionController::class, 'edit'])->name('facturacion.edit');
        Route::match(['put','patch'], '/facturacion/{factura}', [FacturacionController::class, 'update'])->name('facturacion.update');
        Route::delete('/facturacion/{factura}', [FacturacionController::class, 'destroy'])->name('facturacion.destroy');
        Route::get('/facturacion/{factura}/print', function ($factura) { return view('facturacion.print', compact('factura')); })->name('facturacion.print');

        // Endpoints para PDF
        Route::post('/facturacion/pdf/generar', [FacturacionController::class, 'generarPdf'])->name('facturacion.generar-pdf');
        Route::post('/facturacion/generar-pdf', [FacturacionController::class, 'generarPdf']);
        Route::get('/facturacion/{factura}/datos-pdf', [FacturacionController::class, 'obtenerDatosPdf']);
        Route::get('/facturacion/{num_factura}/pdf', [FacturacionController::class, 'descargarPdf'])->name('facturacion.pdf.descargar');

        // Auxiliares
        Route::get('/facturacion/clientes/{clienteId}/productos', [ListaPrecioController::class, 'porCliente'])->name('facturacion.productos-por-cliente');
        
        // Anular Factura
        Route::post('/facturacion/{factura}/anular', [FacturacionController::class, 'anular'])->name('facturacion.anular');

        // --- EL CAMBIO ESTÁ AQUÍ ---
        // Esta ruta debe coincidir con el fetch del botón "Enviar Correo" en tu JS
        Route::post('/facturacion/{num_factura}/enviar', [FacturacionController::class, 'enviarEmail'])->name('facturacion.enviar-email');
    });
    
    // Reportes (requieren permiso 'reportes')
    Route::middleware('permiso:reportes')->group(function () {
        Route::get('/reportes/ventas', function () { return view('reportes.ventas'); })->name('reportes.ventas');
        Route::get('/reportes/facturas', function () { return view('reportes.facturas'); })->name('reportes.facturas');
    });
});

// Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas auxiliares de auth
// Route::get('/register', function () { return redirect()->route('login'); })->name('register');
Route::get('/password/reset', function () { return view('auth.forgot-password'); })->name('password.request');

// Endpoints moved into the auth group above

