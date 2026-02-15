<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ListaPrecio;
use App\Models\GrupoCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    

    public function index(Request $request)
{
    $grupos = GrupoCliente::where('estado', 1)->orderBy('nombre')->get();

    $grupoId = $request->get('grupo'); // id_grupo seleccionado (opcional)

    $productosQuery = Producto::query();

    // Búsqueda
    if ($search = $request->get('search')) {
        $productosQuery->where(function ($q) use ($search) {
            $q->where('nombre', 'like', "%{$search}%")
              ->orWhere('id_producto', $search);
        });
    }

    // Estado
    if ($estado = $request->get('estado')) {
        if ($estado === 'activo') $productosQuery->where('estado', 1);
        if ($estado === 'inactivo') $productosQuery->where('estado', 0);
    }

    // ✅ Filtro por grupo (según lista_precios)
    if (!empty($grupoId)) {
        $productosQuery->whereHas('precios', function ($q) use ($grupoId) {
            $q->where('id_grupo_fk', $grupoId)
              ->where('estado', 1);
        });
    }

    // ✅ Cargar precio/unidad según grupo seleccionado
    $productosQuery->with(['precios' => function ($q) use ($grupoId) {
        if (!empty($grupoId)) {
            $q->where('id_grupo_fk', $grupoId)->where('estado', 1);
        } else {
            $q->where('id_grupo_fk', 1)->where('estado', 1); // por defecto grupo 1
        }
    }]);

    $productos = $productosQuery->orderBy('id_producto', 'desc')->paginate(10);

    return view('productos.index', compact('productos', 'grupos', 'grupoId'));
}

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
        {
           $data = $request->validate(['nombre' => ['required', 'string', 'max:255', 'unique:productos,nombre'],
            ], [
                'nombre.unique' => 'Este producto ya existe.',
                'nombre.required' => 'El nombre del producto es obligatorio.',
            ]);

            DB::transaction(function () use ($data) {
                // Se crea el producto con estado activo (1) por defecto
                Producto::create([
                    'nombre' => $data['nombre'],
                    'estado' => 1,
                ]);
            });

            return redirect()->route('productos.index')
                ->with('success', 'Producto creado correctamente.');
        }

    public function show(Producto $producto)
    {
        $producto->load(['precioBase', 'precios']);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $producto->load('precioBase');
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'estado'      => ['required', 'in:0,1']],
            [
                'nombre.required' => 'El nombre del producto es obligatorio.',
            ]);


        DB::transaction(function () use ($producto, $data) {
            $producto->update([
                'nombre'     => $data['nombre'],
                'estado'     => (int)$data['estado'],
            ]);

            // Si el producto se desactiva, también se desactivan sus precios
            if ($data['estado'] == 0) {
                ListaPrecio::where('id_producto_fk', $producto->id_producto)
                    ->update(['estado' => 0]);
            }               
        });

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    
    public function destroy(Producto $producto)
    {
        $producto = Producto::findOrFail($producto->id_producto);

        // Toggle estado: 1 -> 0 (desactivar), 0 -> 1 (activar)
        $producto->estado = $producto->estado == 1 ? 0 : 1;
        $producto->save();
        // Si el producto se desactiva, también se desactivan sus precios
            if ($producto->estado == 0) {
                ListaPrecio::where('id_producto_fk', $producto->id_producto)
                    ->update(['estado' => 0]);
            }       
        $mensaje = $producto->estado == 1 ? 'Producto activado correctamente' : 'Producto desactivado correctamente';

        return redirect()->route('productos.index')->with('success', $mensaje);
    }
}
