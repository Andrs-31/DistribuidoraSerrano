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
                        <option value="{{ $cliente->id }}" {{ request('cliente') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }}
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
                    @forelse($facturas ?? [] as $factura)
                        <tr>
                            <td class="fw-medium">{{ $factura->numero }}</td>
                            <td>
                                <div class="fw-medium">{{ $factura->cliente->nombre ?? 'N/A' }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">{{ $factura->cliente->ruc ?? '' }}</div>
                            </td>
                            <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                            <td>B/. {{ number_format($factura->subtotal, 2) }}</td>
                            <td>B/. {{ number_format($factura->itbms, 2) }}</td>
                            <td class="fw-medium">B/. {{ number_format($factura->total, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $factura->estado === 'pagada' ? 'success' : ($factura->estado === 'pendiente' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($factura->estado) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('facturacion.show', $factura) }}" class="btn btn-sm btn-light" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('facturacion.print', $factura) }}" class="btn btn-sm btn-light" title="Imprimir" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-light" title="Enviar por correo"
                                            onclick="enviarCorreo({{ $factura->id }})">
                                        <i class="bi bi-envelope"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Demo data -->
                        <tr>
                            <td class="fw-medium">FAC-2024-0012</td>
                            <td>
                                <div class="fw-medium">Comercial El Triunfo, S.A.</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">155634567-2-2020</div>
                            </td>
                            <td>{{ now()->format('d/m/Y') }}</td>
                            <td>B/. 220.09</td>
                            <td>B/. 15.41</td>
                            <td class="fw-medium">B/. 235.50</td>
                            <td><span class="badge badge-success">Pagada</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-printer"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">FAC-2024-0011</td>
                            <td>
                                <div class="fw-medium">Mini Super Central</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">8-456-789</div>
                            </td>
                            <td>{{ now()->format('d/m/Y') }}</td>
                            <td>B/. 168.22</td>
                            <td>B/. 11.78</td>
                            <td class="fw-medium">B/. 180.00</td>
                            <td><span class="badge badge-success">Pagada</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-printer"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">FAC-2024-0010</td>
                            <td>
                                <div class="fw-medium">Abarrotería Don José</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">4-234-567</div>
                            </td>
                            <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                            <td>B/. 504.44</td>
                            <td>B/. 35.31</td>
                            <td class="fw-medium">B/. 539.75</td>
                            <td><span class="badge badge-warning">Pendiente</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-printer"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">FAC-2024-0009</td>
                            <td>
                                <div class="fw-medium">Super La Economía</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">155789012-2-2019</div>
                            </td>
                            <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                            <td>B/. 1,098.13</td>
                            <td>B/. 76.87</td>
                            <td class="fw-medium">B/. 1,175.00</td>
                            <td><span class="badge badge-success">Pagada</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-printer"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">FAC-2024-0008</td>
                            <td>
                                <div class="fw-medium">Tienda La Bendición</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">8-789-012</div>
                            </td>
                            <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                            <td>B/. 79.91</td>
                            <td>B/. 5.59</td>
                            <td class="fw-medium">B/. 85.50</td>
                            <td><span class="badge badge-success">Pagada</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-printer"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-envelope"></i></button>
                                </div>
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
</script>
@endpush
