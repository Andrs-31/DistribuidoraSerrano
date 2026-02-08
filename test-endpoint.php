<?php
/**
 * Script de prueba para verificar el endpoint de productos por cliente
 * Ejecutar desde la raíz del proyecto: php test-endpoint.php
 */

// Cargar el framework de Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Hacer la aplicación disponible
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Cliente;
use App\Models\ListaPrecio;

echo "=== Test de Endpoint de Productos por Cliente ===\n\n";

// 1. Verificar clientes existentes
echo "1. Buscando clientes...\n";
$clientes = Cliente::where('estado', 1)->get();
echo "   Total de clientes activos: " . $clientes->count() . "\n";

if ($clientes->count() === 0) {
    echo "   ⚠️  NO HAY CLIENTES ACTIVOS EN LA BD\n\n";
} else {
    $cliente = $clientes->first();
    echo "   ✓ Cliente encontrado: " . $cliente->nombre . " (RUC: " . $cliente->ruc . ")\n";
    echo "   ID Grupo: " . $cliente->id_grupo_fk . "\n\n";

    // 2. Verificar productos para ese cliente
    echo "2. Buscando productos para el cliente...\n";
    $productosData = ListaPrecio::with('producto')
        ->where('id_grupo_fk', $cliente->id_grupo_fk)
        ->where('estado', 1)
        ->get();
    
    echo "   Total de registros lista_precios: " . $productosData->count() . "\n";
    
    if ($productosData->count() === 0) {
        echo "   ⚠️  NO HAY PRODUCTOS PARA ESTE CLIENTE\n\n";
    } else {
        $grouped = $productosData->groupBy('id_producto_fk');
        echo "   Total de productos únicos: " . $grouped->count() . "\n\n";

        // 3. Mostrar estructura de datos
        echo "3. Estructura de datos que enviaría el endpoint:\n";
        $productosResponse = $grouped->map(function ($grupo) {
            $primero = $grupo->first();
            
            $unidades = $grupo->map(function ($item) {
                return [
                    'unidad' => $item->unidad,
                    'precio' => floatval($item->precio_base),
                    'id_lista' => $item->id_lista,
                ];
            })->values()->all();

            return [
                'id' => $primero->producto->id_producto,
                'nombre' => $primero->producto->nombre,
                'codigo' => $primero->id_producto_fk,
                'unidades' => $unidades,
                'unidad_default' => $unidades[0]['unidad'] ?? '',
                'precio_default' => $unidades[0]['precio'] ?? 0,
            ];
        })->values()->all();

        echo json_encode($productosResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n\n✓ El endpoint devolvería esta estructura\n";
    }
}

echo "\n=== Fin del Test ===\n";
