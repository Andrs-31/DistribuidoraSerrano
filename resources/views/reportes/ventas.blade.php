@extends('layouts.app')

@section('title', 'Reporte de Ventas')
@section('page-title', 'Reporte de Ventas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Reporte de Ventas</li>
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
                    <div class="stat-value">{{ $totalFacturas ?? 156 }}</div>
                    <div class="stat-label">Total Facturas</div>
                </div>
                <div class="stat-icon" style="background: rgba(196, 30, 58, 0.1); color: var(--ds-primary);">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">B/. {{ number_format($totalVentas ?? 45670.50, 2) }}</div>
                    <div class="stat-label">Total Ventas</div>
                </div>
                <div class="stat-icon" style="background: rgba(27, 94, 32, 0.1); color: var(--ds-secondary);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">B/. {{ number_format($promedioVenta ?? 292.76, 2) }}</div>
                    <div class="stat-label">Promedio por Factura</div>
                </div>
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">{{ $clientesAtendidos ?? 42 }}</div>
                    <div class="stat-label">Clientes Atendidos</div>
                </div>
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Sales by Client -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Ventas por Cliente</h5>
                <span class="badge bg-light text-dark">Top 10</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th class="text-center">Facturas</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventasPorCliente ?? [] as $venta)
                                <tr>
                                    <td class="fw-medium">{{ $venta->cliente->nombre }}</td>
                                    <td class="text-center">{{ $venta->facturas_count }}</td>
                                    <td class="text-end fw-medium">B/. {{ number_format($venta->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="fw-medium">Comercial El Triunfo, S.A.</td>
                                    <td class="text-center">24</td>
                                    <td class="text-end fw-medium">B/. 8,540.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Mini Super Central</td>
                                    <td class="text-center">18</td>
                                    <td class="text-end fw-medium">B/. 5,230.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Abarrotería Don José</td>
                                    <td class="text-center">15</td>
                                    <td class="text-end fw-medium">B/. 4,120.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Super La Economía</td>
                                    <td class="text-center">12</td>
                                    <td class="text-end fw-medium">B/. 3,890.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Tienda La Bendición</td>
                                    <td class="text-center">10</td>
                                    <td class="text-end fw-medium">B/. 2,450.00</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales by Product -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Productos Más Vendidos</h5>
                <span class="badge bg-light text-dark">Top 10</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventasPorProducto ?? [] as $venta)
                                <tr>
                                    <td class="fw-medium">{{ $venta->producto->nombre }}</td>
                                    <td class="text-center">{{ $venta->cantidad_total }}</td>
                                    <td class="text-end fw-medium">B/. {{ number_format($venta->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="fw-medium">Arroz Calidad Extra</td>
                                    <td class="text-center">245</td>
                                    <td class="text-end fw-medium">B/. 11,025.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Aceite Vegetal 1L</td>
                                    <td class="text-center">189</td>
                                    <td class="text-end fw-medium">B/. 4,536.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Azúcar Refinada</td>
                                    <td class="text-center">156</td>
                                    <td class="text-end fw-medium">B/. 6,006.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Leche Evaporada</td>
                                    <td class="text-center">134</td>
                                    <td class="text-end fw-medium">B/. 6,968.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Frijoles Negros</td>
                                    <td class="text-center">98</td>
                                    <td class="text-end fw-medium">B/. 6,370.00</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Options -->
<div class="mt-4 d-flex justify-content-end gap-2">
    <button class="btn btn-outline-secondary">
        <i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF
    </button>
    <button class="btn btn-outline-secondary">
        <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
    </button>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Imprimir
    </button>
</div>
@endsection
