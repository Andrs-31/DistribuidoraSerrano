@extends('layouts.app')

@section('title', 'Detalle de Factura')
@section('page-title', 'Detalle de Factura')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('facturacion.index') }}">Facturas</a></li>
    <li class="breadcrumb-item active">{{ $factura->numero ?? 'FAC-2024-0012' }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1">Factura {{ $factura->numero ?? 'FAC-2024-0012' }}</h5>
                    <span class="badge badge-{{ ($factura->estado ?? 'pagada') === 'pagada' ? 'success' : 'warning' }}">
                        {{ ucfirst($factura->estado ?? 'Pagada') }}
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('facturacion.print', $factura ?? 1) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </a>
                    <button type="button" class="btn btn-outline-primary" onclick="enviarCorreo({{ $factura->id ?? 1 }})">
                        <i class="bi bi-envelope me-1"></i>Enviar
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Invoice Header -->
                <div class="row mb-4 pb-4 border-bottom">
                    <div class="col-md-6">
                        <h6 class="text-secondary mb-2">FACTURADO A:</h6>
                        <h5 class="fw-bold mb-1">{{ $factura->cliente->nombre ?? 'Comercial El Triunfo, S.A.' }}</h5>
                        <p class="mb-1">RUC: {{ $factura->cliente->ruc ?? '155634567-2-2020' }}</p>
                        <p class="mb-1">{{ $factura->cliente->direccion ?? 'Calle 50, Local 12, Ciudad de Panamá' }}</p>
                        <p class="mb-0">Tel: {{ $factura->cliente->telefono ?? '+507 6789-0123' }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-secondary mb-2">INFORMACIÓN DE FACTURA:</h6>
                        <p class="mb-1"><strong>Fecha:</strong> {{ $factura->fecha->format('d/m/Y') ?? now()->format('d/m/Y') }}</p>
                        <p class="mb-1"><strong>N° Factura:</strong> {{ $factura->numero ?? 'FAC-2024-0012' }}</p>
                        <p class="mb-0"><strong>Vendedor:</strong> {{ $factura->usuario->name ?? 'Admin' }}</p>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-responsive mb-4">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Descripción</th>
                                <th class="text-center" style="width: 100px;">Cantidad</th>
                                <th class="text-center" style="width: 100px;">Unidad</th>
                                <th class="text-end" style="width: 120px;">Precio Unit.</th>
                                <th class="text-end" style="width: 120px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($factura->detalles ?? [] as $detalle)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $detalle->producto->nombre ?? $detalle->descripcion }}</div>
                                        <small class="text-secondary">{{ $detalle->producto->codigo ?? '' }}</small>
                                    </td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-center">{{ $detalle->unidad }}</td>
                                    <td class="text-end">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="text-end fw-medium">B/. {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <!-- Demo data -->
                                <tr>
                                    <td>
                                        <div class="fw-medium">Arroz Calidad Extra</div>
                                        <small class="text-secondary">PRD-001</small>
                                    </td>
                                    <td class="text-center">3</td>
                                    <td class="text-center">Quintal</td>
                                    <td class="text-end">B/. 45.00</td>
                                    <td class="text-end fw-medium">B/. 135.00</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-medium">Aceite Vegetal 1L</div>
                                        <small class="text-secondary">PRD-002</small>
                                    </td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">Caja (12 unid)</td>
                                    <td class="text-end">B/. 24.00</td>
                                    <td class="text-end fw-medium">B/. 48.00</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-medium">Azúcar Refinada</div>
                                        <small class="text-secondary">PRD-003</small>
                                    </td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">Quintal</td>
                                    <td class="text-end">B/. 38.50</td>
                                    <td class="text-end fw-medium">B/. 38.50</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="row">
                    <div class="col-md-6">
                        @if(isset($factura->observaciones) && $factura->observaciones)
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-secondary mb-2">Observaciones:</h6>
                                <p class="mb-0">{{ $factura->observaciones }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span class="fw-medium">B/. {{ number_format($factura->subtotal ?? 221.50, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>ITBMS (7%):</span>
                                <span class="fw-medium">B/. {{ number_format($factura->itbms ?? 15.51, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold fs-5">TOTAL:</span>
                                <span class="fw-bold fs-5" style="color: var(--ds-primary);">B/. {{ number_format($factura->total ?? 237.01, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Actions Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Acciones</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('facturacion.print', $factura ?? 1) }}" class="btn btn-primary" target="_blank">
                        <i class="bi bi-printer me-2"></i>Imprimir Factura
                    </a>
                    <button type="button" class="btn btn-outline-primary" onclick="enviarCorreo({{ $factura->id ?? 1 }})">
                        <i class="bi bi-envelope me-2"></i>Enviar por Correo
                    </button>
                    <a href="{{ route('facturacion.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Historial
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Información</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-secondary">Creada por:</small>
                    <div class="fw-medium">{{ $factura->usuario->name ?? 'Administrador' }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-secondary">Fecha de creación:</small>
                    <div class="fw-medium">{{ $factura->created_at->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</div>
                </div>
                @if(isset($factura->email_enviado_at) && $factura->email_enviado_at)
                    <div class="mb-0">
                        <small class="text-secondary">Enviado por correo:</small>
                        <div class="fw-medium">{{ $factura->email_enviado_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function enviarCorreo(facturaId) {
    if (confirm('¿Desea enviar esta factura por correo electrónico al cliente?')) {
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
</script>
@endpush
