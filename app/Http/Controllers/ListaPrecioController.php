<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\ListaPrecio;
use App\Models\Producto;
use App\Models\GrupoCliente;

class ListaPrecioController extends Controller
{
    public function porCliente($clienteId)
    {
        $cliente = Cliente::where('ruc', $clienteId)->firstOrFail();

        
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

/**
     * Pantalla: Precios por grupo para un producto
     */
    public function indexPorProducto(Producto $producto)
{
    $grupos = GrupoCliente::orderBy('nombre')->get();

    $precios = ListaPrecio::where('id_producto_fk', $producto->id_producto)
    ->orderBy('id_grupo_fk')
    ->orderBy('unidad')
    ->get();


    return view('productos.precios', compact('producto', 'grupos', 'precios'));
}

public function upsertPrecio(Request $request, Producto $producto)
{
    $request->validate([
        'id_grupo_fk' => 'required|exists:grupo_clientes,id_grupo',
        'unidad' => 'required|string|max:20',
        'precio_base' => 'required|numeric|min:0',
        'estado' => 'required|in:0,1',
    ]);

    ListaPrecio::updateOrCreate(
        [
            'id_producto_fk' => $producto->id_producto,
            'id_grupo_fk'    => $request->id_grupo_fk,
            'unidad'         => $request->unidad, 
        ],
        [
            'precio_base' => $request->precio_base,
            'estado'      => $request->estado,
        ]
    );

    return back()->with('success', 'Precio guardado correctamente.');
}

public function desactivarPrecio(Producto $producto, ListaPrecio $listaPrecio)
{
    if ($listaPrecio->id_producto_fk != $producto->id_producto) {
        abort(404);
    }

    $listaPrecio->update(['estado' => 0]);

    return back()->with('success', 'Precio desactivado.');
}

    /**
     * Asignar “producto para un cliente” (realmente: precio para el grupo del cliente)
     */
    public function upsertPrecioPorCliente(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'cliente_ruc' => ['required', 'string', 'max:70'],
            'unidad'      => ['required', 'string', 'max:50'],
            'precio_base' => ['required', 'numeric', 'min:0'],
        ]);

        $cliente = Cliente::where('ruc', $data['cliente_ruc'])->firstOrFail();

        if (!$cliente->id_grupo_fk) {
            return back()->withErrors([
                'cliente_ruc' => 'Este cliente no tiene grupo asignado.',
            ]);
        }

        ListaPrecio::updateOrCreate(
            [
                'id_producto_fk' => $producto->id_producto,
                'id_grupo_fk'    => (int)$cliente->id_grupo_fk,
                'unidad'         => $data['unidad'],
            ],
            [
                'precio_base' => $data['precio_base'],
                'estado'      => 1,
            ]
        );

        return back()->with('success', 'Asignación creada/actualizada para el grupo del cliente.');
    }
}
