<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\GrupoCliente;
use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\Factura;

class ClienteController extends Controller
{
    public function index()
    {
        $query = Cliente::with('grupoCliente')->with('facturas');

        // Filtro de búsqueda
        if (request('search')) {
            $search = request('search');
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('ruc', 'like', "%{$search}%");
        }

        // Filtro por grupo
        if (request('grupo')) {
            $query->where('id_grupo_fk', request('grupo'));
        }

        // Filtro por estado
        if (request('estado') == 'activo') {
            $query->where('estado', 1);
        } elseif (request('estado') == 'inactivo') {
            $query->where('estado', 0);
        }

        $clientes = $query->paginate(15);
        $grupos = GrupoCliente::where('estado', 1)->get();

        return view('clientes.index', compact('clientes', 'grupos'));
    }

    public function create()
    {
        $grupos = GrupoCliente::where('estado', 1)->get();
        $provincias = Provincia::orderBy('nombre_provincia')->get();

        return view('clientes.create', compact('grupos', 'provincias'));
    }

    public function store(Request $request)
    {
        // Validar datos básicos
        $request->validate([
            'ruc'        => 'required|unique:clientes,ruc',
            'nombre'     => 'required|string|max:255',
            'email'      => 'nullable|email|unique:clientes,email',
            'telefono'   => 'required|string|max:50',
            'grupo_id'   => 'required',
            'estado'     => 'required|in:0,1',
            'provincia'  => 'required',
            'distrito'   => 'required',
            'direccion'  => 'required'
        ]);

        // Manejar creación de nuevo grupo si se selecciona "create_new"
        $grupoId = $request->grupo_id;
        if ($request->grupo_id === 'create_new') {
            $grupo = GrupoCliente::create([
                'nombre' => $request->nombre,
                'estado' => 1,
            ]);
            $grupoId = $grupo->id_grupo;
        } else {
            // Validar que el grupo existe
            $request->validate([
                'grupo_id' => 'exists:grupo_clientes,id_grupo'
            ]);
        }

        // Obtener información de provincia y distrito
        $provincia = Provincia::findOrFail($request->provincia);
        $distrito = Distrito::findOrFail($request->distrito);

        $direccionCompleta = "{$provincia->nombre_provincia}, {$distrito->nombre_distrito}, {$request->direccion}";

        Cliente::create([
            'ruc'         => $request->ruc,
            'nombre'      => $request->nombre,
            'telefono'    => $request->telefono,
            'email'       => $request->email,
            'direccion'   => $direccionCompleta,
            'estado'      => $request->estado,
            'id_grupo_fk' => $grupoId,
        ]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente');
    }

    public function show($ruc)
    {
        $cliente = Cliente::with('grupoCliente')->findOrFail($ruc);

        // Obtener facturas relacionadas y conteos necesarios
        $facturas = Factura::where('ruc_cliente_fk', $ruc)
            ->withCount('detalles')
            ->orderByDesc('fecha_factura')
            ->get();

        $facturas_count = $facturas->count();
        $total_compras = $facturas->sum('total') ?? 0;
        $ultima_factura = $facturas->first();

        return view('clientes.show', compact('cliente', 'facturas', 'facturas_count', 'total_compras', 'ultima_factura'));
    }

    public function edit($ruc)
    {
        $cliente = Cliente::findOrFail($ruc);
        $grupos = GrupoCliente::where('estado', 1)->get();
        $provincias = Provincia::orderBy('nombre_provincia')->get();

        // Extraer provincia y distrito de la dirección guardada
        $provinciaActual = $cliente->getProvinciaFromDireccion();
        $distritoActual = $cliente->getDistritoFromDireccion();
        $direccionSimple = $cliente->getDireccionSimple();

        // Buscar el ID de provincia por nombre
        $provinciaId = Provincia::where('nombre_provincia', $provinciaActual)->first()?->codigo_provincia;

        // Buscar el ID de distrito por nombre
        $distritoId = Distrito::where('nombre_distrito', $distritoActual)->first()?->codigo_distrito;

        // Obtener distritos de la provincia actual
        $distritos = $provinciaId ? Distrito::where('codigo_provincia', $provinciaId)->get() : collect();

        return view('clientes.edit', compact(
            'cliente',
            'grupos',
            'provincias',
            'distritos',
            'provinciaId',
            'distritoId',
            'direccionSimple'
        ));
    }

    public function update(Request $request, $ruc)
    {
        $cliente = Cliente::findOrFail($ruc);

        $request->validate([
            'nombre'     => 'required|string|max:255',
            'email'      => 'nullable|email|unique:clientes,email,' . $cliente->ruc . ',ruc',
            'telefono'   => 'required|string|max:50',
            'estado'     => 'required|in:0,1',
            'grupo_id'   => 'required',
            'provincia'  => 'required',
            'distrito'   => 'required',
            'direccion'  => 'required'
        ]);

        // Manejar creación de nuevo grupo si se selecciona "create_new"
        $grupoId = $request->grupo_id;
        if ($request->grupo_id === 'create_new') {
            $grupo = GrupoCliente::create([
                'nombre' => $request->nombre,
                'estado' => 1,
            ]);
            $grupoId = $grupo->id_grupo;
        } else {
            // Validar que el grupo existe
            $request->validate([
                'grupo_id' => 'exists:grupo_clientes,id_grupo'
            ]);
        }

        // Obtener información de provincia y distrito
        $provincia = Provincia::findOrFail($request->provincia);
        $distrito = Distrito::findOrFail($request->distrito);

        $direccionCompleta = "{$provincia->nombre_provincia}, {$distrito->nombre_distrito}, {$request->direccion}";

        $cliente->update([
            'nombre'      => $request->nombre,
            'telefono'    => $request->telefono,
            'email'       => $request->email,
            'direccion'   => $direccionCompleta,
            'estado'      => $request->estado,
            'id_grupo_fk' => $grupoId,
        ]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente');
    }

    /**
     * Toggle cliente estado (activar/desactivar)
     */
    public function destroy($ruc)
    {
        $cliente = Cliente::findOrFail($ruc);

        // Toggle estado: 1 -> 0 (desactivar), 0 -> 1 (activar)
        $cliente->estado = $cliente->estado == 1 ? 0 : 1;
        $cliente->save();

        $mensaje = $cliente->estado == 1 ? 'Cliente activado correctamente' : 'Cliente desactivado correctamente';

        return redirect()->route('clientes.index')->with('success', $mensaje);
    }
}
