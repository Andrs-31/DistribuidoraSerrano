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

<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmTitle">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmBtnAction" class="btn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div id="detalleToastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
@endsection

@push('scripts')
<script>
    let confirmModal;

    // --- FUNCIÓN PARA MOSTRAR NOTIFICACIONES (AÑADIR ESTO) ---
    function showToast(message, type = 'success') {
        const container = document.getElementById('detalleToastContainer');
        if (!container) return;

        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : (type === 'warning' ? 'bg-warning' : 'bg-danger');
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';

        const toastHtml = `
            <div id="${toastId}" class="toast show shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${bgClass} text-white">
                    <i class="bi ${icon} me-2"></i>
                    <strong class="me-auto">Sistema de Facturación</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body fw-bold">
                    ${message}
                </div>
            </div>`;
        
        container.insertAdjacentHTML('beforeend', toastHtml);

        // Auto-eliminar después de 4 segundos
        setTimeout(() => {
            const toastElement = document.getElementById(toastId);
            if (toastElement) {
                toastElement.classList.remove('show');
                setTimeout(() => toastElement.remove(), 300);
            }
        }, 4000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        confirmModal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
    });

    const getBtnConfirmar = () => document.getElementById('confirmBtnAction');
    
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
    const btn = getBtnConfirmar();
    document.getElementById('confirmTitle').innerText = 'Enviar Factura';
    document.getElementById('confirmMessage').innerText = `¿Desea enviar la factura N° ${facturaId} por correo electrónico al cliente?`;
    
    btn.className = 'btn btn-primary';
    btn.innerText = 'Enviar Correo';

    btn.onclick = async () => {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';

        try {
            const response = await fetch(`/facturacion/${facturaId}/enviar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            // 1. Cerramos el modal de confirmación primero
            confirmModal.hide();

            // 2. REEMPLAZO TOTAL DEL ALERT: Usamos tu función showToast
            if (data.success) {
                // Si el controlador respondió success: true
                showToast(data.message, 'success');
            } else {
                // Si hubo un error controlado (ej. cliente sin email)
                showToast(data.message || 'Error al enviar la factura', 'danger');
            }

        } catch (error) {
            confirmModal.hide();
            // Error de red o servidor (500)
            showToast("No se pudo conectar con el servidor para enviar el correo", "danger");
        } finally {
            btn.disabled = false;
            btn.innerText = 'Enviar Correo';
        }
    };
    confirmModal.show();
}

    function anularFactura(numFactura) {
        const btn = getBtnConfirmar();
        document.getElementById('confirmTitle').innerText = 'Anular Factura';
        document.getElementById('confirmMessage').innerHTML = `¿Está seguro de que desea anular la factura <strong>${numFactura}</strong>? <br><small class="text-danger">Esta acción no se puede deshacer.</small>`;
        
        btn.className = 'btn btn-danger';
        btn.innerText = 'Anular Permanentemente';

        btn.onclick = async () => {
            btn.disabled = true;
            try {
                const response = await fetch(`/facturacion/${numFactura}/anular`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                    btn.disabled = false;
                }
            } catch (error) {
                console.error(error);
                btn.disabled = false;
            }
        };
        confirmModal.show();
    }
</script>
@endpush
