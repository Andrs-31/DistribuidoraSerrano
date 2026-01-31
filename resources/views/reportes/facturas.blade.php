@extends('layouts.app')

@section('title', 'Reporte de Facturas')
@section('page-title', 'Reporte de Facturas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Reporte de Facturas</li>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtros de Reporte</h5>
    </div>
    <div class="card-body">
        @include('reportes.filtros')
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">{{ $facturasEmitidas ?? 156 }}</div>
                    <div class="stat-label">Facturas Emitidas</div>
                </div>
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">{{ $facturasPagadas ?? 142 }}</div>
                    <div class="stat-label">Facturas Pagadas</div>
                </div>
                <div class="stat-icon" style="background: rgba(27, 94, 32, 0.1); color: var(--ds-secondary);">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">{{ $facturasPendientes ?? 14 }}</div>
                    <div class="stat-label">Facturas Pendientes</div>
                </div>
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                    <i class="bi bi-clock"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">B/. {{ number_format($totalItbms ?? 3196.94, 2) }}</div>
                    <div class="stat-label">Total ITBMS</div>
                </div>
                <div class="stat-icon" style="background: rgba(196, 30, 58, 0.1); color: var(--ds-primary);">
                    <i class="bi bi-percent"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoices Table -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Listado de Facturas</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </button>
            <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-file-earmark-excel me-1"></i>Excel
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N° Factura</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">ITBMS</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturas ?? [] as $factura)
                        <tr>
                            <td class="fw-medium">{{ $factura->numero }}</td>
                            <td>{{ $factura->cliente->nombre }}</td>
                            <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                            <td class="text-end">B/. {{ number_format($factura->subtotal, 2) }}</td>
                            <td class="text-end">B/. {{ number_format($factura->itbms, 2) }}</td>
                            <td class="text-end fw-medium">B/. {{ number_format($factura->total, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $factura->estado === 'pagada' ? 'success' : 'warning' }}">
                                    {{ ucfirst($factura->estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="fw-medium">FAC-2024-0012</td>
                            <td>Comercial El Triunfo, S.A.</td>
                            <td>{{ now()->format('d/m/Y') }}</td>
                            <td class="text-end">B/. 220.09</td>
                            <td class="text-end">B/. 15.41</td>
                            <td class="text-end fw-medium">B/. 235.50</td>
                            <td><span class="badge badge-success">Pagada</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">FAC-2024-0011</td>
                            <td>Mini Super Central</td>
                            <td>{{ now()->format('d/m/Y') }}</td>
                            <td class="text-end">B/. 168.22</td>
                            <td class="text-end">B/. 11.78</td>
                            <td class="text-end fw-medium">B/. 180.00</td>
                            <td><span class="badge badge-success">Pagada</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">FAC-2024-0010</td>
                            <td>Abarrotería Don José</td>
                            <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                            <td class="text-end">B/. 504.44</td>
                            <td class="text-end">B/. 35.31</td>
                            <td class="text-end fw-medium">B/. 539.75</td>
                            <td><span class="badge badge-warning">Pendiente</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">FAC-2024-0009</td>
                            <td>Super La Economía</td>
                            <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                            <td class="text-end">B/. 1,098.13</td>
                            <td class="text-end">B/. 76.87</td>
                            <td class="text-end fw-medium">B/. 1,175.00</td>
                            <td><span class="badge badge-success">Pagada</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">FAC-2024-0008</td>
                            <td>Tienda La Bendición</td>
                            <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                            <td class="text-end">B/. 79.91</td>
                            <td class="text-end">B/. 5.59</td>
                            <td class="text-end fw-medium">B/. 85.50</td>
                            <td><span class="badge badge-success">Pagada</span></td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">TOTALES:</td>
                        <td class="text-end">B/. {{ number_format($totalSubtotal ?? 2070.79, 2) }}</td>
                        <td class="text-end">B/. {{ number_format($totalItbmsDetalle ?? 144.96, 2) }}</td>
                        <td class="text-end">B/. {{ number_format($totalGeneral ?? 2215.75, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
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
