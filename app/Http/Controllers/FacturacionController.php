<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacturacionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Carga eficiente con relación al cliente
        $query = Factura::with('cliente');

        // 2. Filtro por N° de Factura (Clave Primaria)
        if ($request->filled('search')) {
            $query->where('num_factura', 'like', '%' . $request->search . '%');
        }

        // 3. Filtro por Cliente (FK ruc_cliente_fk)
        if ($request->filled('cliente')) {
            $query->where('ruc_cliente_fk', $request->cliente);
        }

        // 4. Filtro por Rango de Fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_factura', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_factura', '<=', $request->fecha_hasta);
        }

        // 5. Ejecutar consulta con paginación
        $facturas = $query->orderBy('num_factura', 'desc')->paginate(10);

        // 6. Obtener clientes para el selector de filtros
        $clientes = Cliente::orderBy('nombre')->get();

        return view('facturacion.index', compact('facturas', 'clientes'));
    }

    public function create()
    {
        $clientes = Cliente::where('estado', 1)->get();
        $productos = Producto::where('estado', 1)->get();
        
        // Obtener el próximo número de factura secuencial
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $proximoNumero = str_pad($ultimaFactura + 1, 6, '0', STR_PAD_LEFT);
        
        return view('facturacion.create', compact('clientes', 'productos', 'proximoNumero'));
    }

    public function store(Request $request)
    {
        // Validar datos básicos
        $validated = $request->validate([
            'cliente_id' => 'required|string|exists:clientes,ruc',
            'numero' => 'required|string',
            'fecha' => 'required|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.unidad' => 'required|string',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.subtotal' => 'required|numeric|min:0',
        ]);

        try {
            $factura = DB::transaction(function () use ($validated, $request) {
                // Obtener cliente
                $cliente = Cliente::findOrFail($validated['cliente_id']);

                // Calcular total
                $total = collect($validated['detalles'])->sum('subtotal');

                // Convertir número de factura a entero
                $numFacturaInt = (int) $validated['numero'];

                // Crear factura usando la transacción
                $factura = Factura::create([
                    'num_factura' => $numFacturaInt,
                    'fecha_factura' => $validated['fecha'],
                    'ruc_cliente_fk' => $validated['cliente_id'],
                    'id_usuario_fk' => Auth::id() ?? 1,
                    'subtotal' => $total,
                    'impuesto' => 0,
                    'total' => $total,
                    'estado' => 'emitido',
                ]);

                // Guardar detalles
                foreach ($validated['detalles'] as $detalle) {
                    FacturaDetalle::create([
                        'num_factura_fk' => $factura->num_factura,
                        'id_producto_fk' => $detalle['producto_id'],
                        'cantidad' => $detalle['cantidad'],
                        'unidad' => $detalle['unidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal_linea' => $detalle['subtotal'],
                    ]);
                }

                return $factura;
            });

            // Si es emit action, retornar JSON success
            if ($request->input('action') === 'emit') {
                return response()->json([
                    'success' => true,
                    'message' => 'Factura emitida correctamente',
                    'factura_id' => str_pad($factura->num_factura, 6, '0', STR_PAD_LEFT),
                ]);
            }

            return redirect()->route('facturacion.show', $factura->num_factura)
                ->with('success', 'Factura guardada y emitida correctamente.');
        } catch (\Exception $e) {
            if ($request->input('action') === 'emit') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar factura: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al guardar factura: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($factura)
    {
        // Lógica para mostrar factura
        return view('facturacion.show', compact('factura'));
    }

    public function edit($factura)
    {
        $clientes = Cliente::where('estado', 1)->get();
        $productos = Producto::where('estado', 1)->get();
        
        return view('facturacion.edit', compact('factura', 'clientes', 'productos'));
    }

    public function update(Request $request, $factura)
    {
        // Lógica para actualizar factura
        return redirect()->route('facturacion.show', $factura)->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy($factura)
    {
        // Lógica para eliminar factura
        return redirect()->route('facturacion.index')->with('success', 'Factura eliminada correctamente.');
    }

    public function generarPdf(Request $request)
    {
        // Recibe datos de la factura en JSON y genera PDF
        $data = $request->json()->all();

        // Extraer datos
        $cliente_nombre = $data['cliente_nombre'] ?? 'Cliente';
        $cliente_ruc = $data['cliente_ruc'] ?? '';
        $numero = $data['numero'] ?? '';
        $fecha = $data['fecha'] ?? date('Y-m-d');
        $detalles = $data['detalles'] ?? [];
        $total = $data['total'] ?? 0;

        // Datos de la empresa (hardcoded, idealmente de config)
        $empresa = [
            'nombre' => 'Distribuidora Serrano',
            'ruc' => '12345678-1-2020',
            'ciudad' => 'Ciudad de Panamá, Panamá',
        ];

        // Generar HTML para PDF
        $html = view('facturacion.pdf', compact('empresa', 'cliente_nombre', 'cliente_ruc', 'numero', 'fecha', 'detalles', 'total'))->render();

        // Generar PDF
        $pdf = Pdf::loadHTML($html)->setPaper('a4');

        return $pdf->download("factura_{$numero}_{$fecha}.pdf");
    }

    public function obtenerDatosPdf(Request $request, $num_factura) // <-- Agregamos Request aquí
    {
        try {
            // Buscamos la factura en la base de datos con sus relaciones corregidas
            $factura = Factura::with(['cliente', 'detalles.producto'])->findOrFail($num_factura);

            if (!$factura->cliente) {
                return response()->json(['error' => 'La factura no tiene un cliente asignado'], 404);
            }

            return response()->json([
                'numero' => str_pad($factura->num_factura, 6, '0', STR_PAD_LEFT),
                'fecha' => $factura->fecha_factura ? $factura->fecha_factura->format('Y-m-d') : date('Y-m-d'),
                'cliente_nombre' => $factura->cliente->nombre,
                'cliente_ruc' => $factura->cliente->ruc,
                'total' => (float)$factura->total,
                'detalles' => $factura->detalles->map(function($d) {
                    return [
                        'descripcion' => ($d->producto->codigo ?? '') . ' - ' . ($d->producto->nombre ?? 'Producto'),
                        'cantidad' => $d->cantidad,
                        'unidad' => $d->unidad,
                        'precio_unitario' => (float)$d->precio_unitario,
                        'subtotal' => (float)$d->subtotal_linea
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function anular($num_factura)
    {
        try {
            // Usamos findOrFail para que si no existe dispare el catch
            $factura = Factura::findOrFail($num_factura);

            if ($factura->estado === 'anulada') {
                return response()->json(['success' => false, 'message' => 'La factura ya está anulada.'], 400);
            }

            $factura->estado = 'anulada';
            $factura->save();

            return response()->json([
                'success' => true, 
                'message' => 'Factura N° ' . $num_factura . ' anulada exitosamente.'
            ]);

        } catch (\Exception $e) {
            // Esto devolverá el error real para que lo veas en la consola
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
