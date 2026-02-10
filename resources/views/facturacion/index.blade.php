@extends('layouts.app')

@section('title', 'Historial de Facturas')
@section('page-title', 'Historial de Facturas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Facturas</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h5 class="mb-1">Historial de Facturas</h5>
            <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Consulta y gestiona todas las facturas emitidas</p>
        </div>
        <a href="{{ route('facturacion.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Nueva Factura
        </a>
    </div>

    <div class="card-body border-bottom">
        <form action="{{ route('facturacion.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="N° de factura...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="cliente">
                    <option value="">Todos los clientes</option>
                    @foreach($clientes ?? [] as $cliente)
                        {{-- Cambiamos $cliente->id por $cliente->ruc --}}
                        <option value="{{ $cliente->ruc }}" {{ request('cliente') == $cliente->ruc ? 'selected' : '' }}>
                            {{ $cliente->nombre }} ({{ $cliente->ruc }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde') }}" placeholder="Desde">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta') }}" placeholder="Hasta">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="{{ route('facturacion.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N° Factura</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Subtotal</th>
                        <th>ITBMS</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturas as $factura)
                        <tr>
                            <td class="fw-medium">{{ $factura->num_factura }}</td>
                            <td>
                                <div class="fw-medium">{{ $factura->cliente->nombre ?? 'Sin Nombre' }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">
                                    {{ $factura->ruc_cliente_fk }}
                                </div>
                            </td>
                            <td>
                                @if($factura->fecha_factura)
                                    {{ $factura->fecha_factura->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Sin fecha</span>
                                @endif
                            </td>
                            <td>B/. {{ number_format($factura->subtotal, 2) }}</td>
                            <td>B/. {{ number_format($factura->itbms, 2) }}</td>
                            <td class="fw-medium">B/. {{ number_format($factura->total, 2) }}</td>
                            <td>
                            <span class="badge {{ $factura->estado === 'pagada' ? 'bg-success' : ($factura->estado === 'anulada' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($factura->estado) }}
                            </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light" onclick="vistaPreviaHistorial('{{ $factura->num_factura }}')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light" onclick="enviarCorreo('{{ $factura->num_factura }}')">
                                        <i class="bi bi-envelope"></i>
                                    </button>
                                    @if($factura->estado !== 'anulada')
                                        <button type="button" class="btn btn-sm btn-light text-danger" onclick="anularFactura('{{ $factura->num_factura }}')" title="Anular Factura">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No se encontraron registros en la base de datos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($facturas) && $facturas->hasPages())
        <div class="card-footer">
            {{ $facturas->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    async function vistaPreviaHistorial(numFactura) {
        try {
            // 1. Obtener datos
            const response = await fetch(`/facturacion/${numFactura}/datos-pdf`);
            if (!response.ok) throw new Error('No se encontraron datos de la factura');
            
            const datosFactura = await response.json();

            // 2. Generar el PDF usando tu endpoint existente
            const resPdf = await fetch('/facturacion/generar-pdf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(datosFactura)
            });

            if (resPdf.ok) {
                const blob = await resPdf.blob();
                const url = URL.createObjectURL(blob);
                window.open(url, '_blank'); // Abre la vista previa
            } else {
                alert("Error al procesar el PDF");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("No se pudo generar la vista previa: " + error.message);
        }
    }


function enviarCorreo(facturaId) {
    if (confirm('¿Desea enviar esta factura por correo electrónico al cliente?')) {
        // AJAX call to send email
        fetch(`/facturacion/${facturaId}/enviar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Factura enviada correctamente');
        })
        .catch(error => {
            alert('Error al enviar la factura');
        });
    }
}

async function anularFactura(numFactura) {
    if (!confirm(`¿Desea anular la factura N° ${numFactura}?`)) return;

    try {
        const response = await fetch(`/facturacion/${numFactura}/anular`, {
            method: 'POST', // Cambiado a POST para coincidir con web.php
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json' // Esto le dice a Laravel que queremos JSON, no HTML
            }
        });

        // Verificamos si la respuesta es realmente JSON antes de parsear
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            const data = await response.json();
            
            if (response.ok && data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Error: " + (data.error || data.message));
            }
        } else {
            // Si el servidor mandó un error en HTML, lo capturamos aquí
            const textError = await response.text();
            console.error("Error del servidor (HTML):", textError);
            alert("El servidor devolvió un error interno. Revisa la consola (F12).");
        }
    } catch (error) {
        console.error("Error en la petición:", error);
        alert("No se pudo conectar con el servidor.");
    }
}
</script>
@endpush
