<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\ListaPrecio;
use App\Models\Producto;

class ListaPrecioController extends Controller
{
    public function porCliente($clienteId)
    {
        $cliente = Cliente::findOrFail($clienteId);
        
        // Si el cliente tiene grupo asignado, obtener precios de ese grupo
        // Si no tiene grupo, obtener todos los productos
        if ($cliente->id_grupo_fk) {
            $productosData = ListaPrecio::with('producto')
                ->where('id_grupo_fk', $cliente->id_grupo_fk)
                ->where('estado', 1)
                ->get()
                ->groupBy('id_producto_fk')
                ->map(function ($grupo) {
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
                })
                ->values()
                ->all();
        } else {
            // Si no tiene grupo, devolver todos los productos disponibles
            $productosData = Producto::where('estado', 1)
                ->get()
                ->map(function ($producto) {
                    return [
                        'id' => $producto->id_producto,
                        'nombre' => $producto->nombre,
                        'codigo' => $producto->id_producto,
                        'unidades' => [
                            [
                                'unidad' => 'unidad',
                                'precio' => 0,
                                'id_lista' => 0,
                            ]
                        ],
                        'unidad_default' => 'unidad',
                        'precio_default' => 0,
                    ];
                })
                ->values()
                ->all();
        }

        return response()->json($productosData);
    }

}
