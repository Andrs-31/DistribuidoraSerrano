@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inicio</li>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">{{ $totalFacturasHoy ?? 12 }}</div>
                    <div class="stat-label">Facturas Hoy</div>
                </div>
                <div class="stat-icon" style="background: rgba(196, 30, 58, 0.1); color: var(--ds-primary);">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center gap-1" style="font-size: 0.8125rem;">
                <span class="text-success fw-medium"><i class="bi bi-arrow-up"></i> 12%</span>
                <span class="text-secondary">vs ayer</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">B/. {{ number_format($ventasHoy ?? 1250.50, 2) }}</div>
                    <div class="stat-label">Ventas del Día</div>
                </div>
                <div class="stat-icon" style="background: rgba(27, 94, 32, 0.1); color: var(--ds-secondary);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center gap-1" style="font-size: 0.8125rem;">
                <span class="text-success fw-medium"><i class="bi bi-arrow-up"></i> 8%</span>
                <span class="text-secondary">vs ayer</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">{{ $totalClientes ?? 48 }}</div>
                    <div class="stat-label">Clientes Activos</div>
                </div>
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center gap-1" style="font-size: 0.8125rem;">
                <span class="text-success fw-medium"><i class="bi bi-plus"></i> 3</span>
                <span class="text-secondary">este mes</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">{{ $totalProductos ?? 156 }}</div>
                    <div class="stat-label">Productos</div>
                </div>
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center gap-1" style="font-size: 0.8125rem;">
                <span class="text-secondary">Total en catálogo</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Invoices -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Facturas Recientes</h5>
                <a href="{{ route('facturacion.index') }}" class="btn btn-sm btn-outline-primary">
                    Ver Todas
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>N° Factura</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($facturasRecientes ?? [] as $factura)
                                <tr>
                                    <td class="fw-medium">{{ $factura->numero }}</td>
                                    <td>{{ $factura->cliente->nombre ?? 'N/A' }}</td>
                                    <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                                    <td class="fw-medium">B/. {{ number_format($factura->total, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $factura->estado === 'pagada' ? 'success' : 'warning' }}">
                                            {{ ucfirst($factura->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('facturacion.show', $factura) }}" class="btn btn-sm btn-light">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="text-center py-4 text-secondary">
                                            <i class="bi bi-receipt fs-1 mb-2 d-block opacity-50"></i>
                                            <p class="mb-0">No hay facturas recientes</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Demo data if empty -->
                            @if(!isset($facturasRecientes) || count($facturasRecientes) === 0)
                                <tr>
                                    <td class="fw-medium">FAC-2024-0012</td>
                                    <td>Comercial El Triunfo</td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                    <td class="fw-medium">B/. 245.50</td>
                                    <td><span class="badge badge-success">Pagada</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0011</td>
                                    <td>Mini Super Central</td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                    <td class="fw-medium">B/. 189.00</td>
                                    <td><span class="badge badge-success">Pagada</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0010</td>
                                    <td>Abarrotería Don José</td>
                                    <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                                    <td class="fw-medium">B/. 567.25</td>
                                    <td><span class="badge badge-warning">Pendiente</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0009</td>
                                    <td>Super La Economía</td>
                                    <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                                    <td class="fw-medium">B/. 1,234.00</td>
                                    <td><span class="badge badge-success">Pagada</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0008</td>
                                    <td>Tienda La Bendición</td>
                                    <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                                    <td class="fw-medium">B/. 89.75</td>
                                    <td><span class="badge badge-success">Pagada</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Top Products -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    {{-- <a href="{{ route('facturacion.create') }}" class="btn btn-primary d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-plus-lg me-2"></i>Nueva Factura</span>
                        <i class="bi bi-arrow-right"></i>
                    </a> --}}
                    {{-- <a href="{{ route('clientes.create') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-person-plus me-2"></i>Agregar Cliente</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="{{ route('productos.create') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-box-seam me-2"></i>Agregar Producto</span>
                        <i class="bi bi-arrow-right"></i>
                    </a> --}}
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Productos Más Vendidos</h5>
                <span class="text-secondary" style="font-size: 0.75rem;">Este mes</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topProductos ?? [] as $index => $producto)
                        <li class="list-group-item d-flex align-items-center gap-3 py-3">
                            <span class="badge bg-light text-dark fw-bold" style="min-width: 24px;">{{ $index + 1 }}</span>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $producto->nombre }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">{{ $producto->unidad }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-medium">{{ $producto->cantidad_vendida }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">vendidos</div>
                            </div>
                        </li>
                    @empty
                        <!-- Demo data -->
                        <li class="list-group-item d-flex align-items-center gap-3 py-3">
                            <span class="badge bg-light text-dark fw-bold" style="min-width: 24px;">1</span>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Arroz Calidad Extra</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Quintal</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-medium">245</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">vendidos</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center gap-3 py-3">
                            <span class="badge bg-light text-dark fw-bold" style="min-width: 24px;">2</span>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Aceite Vegetal 1L</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Caja</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-medium">189</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">vendidos</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center gap-3 py-3">
                            <span class="badge bg-light text-dark fw-bold" style="min-width: 24px;">3</span>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Azúcar Refinada</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Quintal</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-medium">156</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">vendidos</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center gap-3 py-3">
                            <span class="badge bg-light text-dark fw-bold" style="min-width: 24px;">4</span>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Leche Evaporada</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Caja</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-medium">134</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">vendidos</div>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
