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
    private function normalizarNombre(string $nombre): string
    {
        // 1) quita acentos -> ascii
        // 2) lowercase
        // 3) colapsa espacios
        $n = Str::of($nombre)->ascii()->lower();
        $n = preg_replace('/\s+/', ' ', trim((string)$n));
        return $n;
    }

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
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'unidad'      => ['required', 'string', 'max:50'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'estado'      => ['required', 'in:0,1'],
        ]);

        $nombreKey = $this->normalizarNombre($data['nombre']);

        // No repetir productos (ignorando mayus/acentos)
        $existe = Producto::where('nombre_key', $nombreKey)->exists();
        if ($existe) {
            return back()
                ->withErrors(['nombre' => 'Este producto ya existe'])
                ->withInput();
        }

        DB::transaction(function () use ($data, $nombreKey) {
            $producto = Producto::create([
                'nombre'      => $data['nombre'],
                'nombre_key'  => $nombreKey,
                'estado'      => (int)$data['estado'],
            ]);

            
            ListaPrecio::create([
                'id_producto_fk' => $producto->id_producto,
                'id_grupo_fk'    => 1,
                'unidad'         => $data['unidad'],
                'precio_base'    => $data['precio_base'],
                'estado'         => 1,
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
            'unidad'      => ['required', 'string', 'max:50'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'estado'      => ['required', 'in:0,1'],
        ]);

        $nombreKey = $this->normalizarNombre($data['nombre']);

        // Evitar duplicados excepto el mismo registro
        $existe = Producto::where('nombre_key', $nombreKey)
            ->where('id_producto', '!=', $producto->id_producto)
            ->exists();

        if ($existe) {
            return back()
                ->withErrors(['nombre' => 'Ya existe otro producto con este nombre (ignorando mayúsculas/acentos).'])
                ->withInput();
        }

        DB::transaction(function () use ($producto, $data, $nombreKey) {
            $producto->update([
                'nombre'     => $data['nombre'],
                'nombre_key' => $nombreKey,
                'estado'     => (int)$data['estado'],
            ]);

            // Actualiza precio base para grupo 1
            ListaPrecio::updateOrCreate(
                [
                    'id_producto_fk' => $producto->id_producto,
                    'id_grupo_fk'    => 1,
                ],
                [
                    'unidad'      => $data['unidad'],
                    'precio_base' => $data['precio_base'],
                    'estado'      => 1,
                ]
            );
        });

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    // “Eliminar” = desactivar 
    public function destroy(Producto $producto)
    {
        DB::transaction(function () use ($producto) {
            $producto->update(['estado' => 0]);

            //  desactivar precios del producto
            ListaPrecio::where('id_producto_fk', $producto->id_producto)
                ->update(['estado' => 0]);
        });

        return redirect()->route('productos.index')
            ->with('success', 'Producto desactivado correctamente.');
    }
}
