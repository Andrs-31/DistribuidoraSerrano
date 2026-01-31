@extends('layouts.app')

@section('title', 'Detalle de Producto')
@section('page-title', 'Detalle de Producto')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">{{ $producto->nombre ?? 'Detalle' }}</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Product Info -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="rounded bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-box-seam fs-1 text-secondary"></i>
                </div>
                <h4 class="mb-1">{{ $producto->nombre ?? 'Arroz Calidad Extra' }}</h4>
                <p class="text-secondary mb-2">{{ $producto->codigo ?? 'PRD-001' }}</p>
                <span class="badge bg-light text-dark mb-2">{{ $producto->categoria ?? 'Granos' }}</span>
                <br>
                <span class="badge badge-{{ ($producto->activo ?? true) ? 'success' : 'danger' }} mb-3">
                    {{ ($producto->activo ?? true) ? 'Activo' : 'Inactivo' }}
                </span>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('productos.edit', $producto ?? 1) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                </div>
            </div>
            <div class="card-body border-top">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-rulers"></i> Unidad de Medida
                        </div>
                        <div class="fw-medium">{{ $producto->unidad ?? 'Quintal' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-currency-dollar"></i> Precio Base
                        </div>
                        <div class="fw-medium fs-4" style="color: var(--ds-primary);">B/. {{ number_format($producto->precio_base ?? 45.00, 2) }}</div>
                    </div>
                    @if(isset($producto->descripcion) && $producto->descripcion)
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-card-text"></i> Descripción
                        </div>
                        <div>{{ $producto->descripcion }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Estadísticas</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Veces Facturado</div>
                        <div class="fs-4 fw-bold">{{ $producto->veces_facturado ?? 156 }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Unidades Vendidas</div>
                        <div class="fs-4 fw-bold">{{ $producto->unidades_vendidas ?? 892 }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Última Venta</div>
                        <div class="fw-medium">{{ $producto->ultima_venta->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Creado</div>
                        <div class="fw-medium">{{ $producto->created_at->format('M Y') ?? 'Ene 2023' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prices by Group -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Precios por Grupo de Cliente</h5>
                <a href="{{ route('productos.edit', $producto ?? 1) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Editar Precios
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Grupo</th>
                                <th>Precio</th>
                                <th>Descuento</th>
                                <th>Precio Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($producto->precios ?? [] as $precio)
                                <tr>
                                    <td class="fw-medium">{{ $precio->grupo->nombre }}</td>
                                    <td>B/. {{ number_format($precio->precio, 2) }}</td>
                                    <td>{{ $precio->descuento }}%</td>
                                    <td class="fw-medium">B/. {{ number_format($precio->precio * (1 - $precio->descuento/100), 2) }}</td>
                                </tr>
                            @empty
                                <!-- Demo data -->
                                <tr>
                                    <td class="fw-medium">Mayoristas</td>
                                    <td>B/. 42.00</td>
                                    <td>5%</td>
                                    <td class="fw-medium text-success">B/. 39.90</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Minoristas</td>
                                    <td>B/. 45.00</td>
                                    <td>0%</td>
                                    <td class="fw-medium">B/. 45.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Especiales</td>
                                    <td>B/. 40.00</td>
                                    <td>10%</td>
                                    <td class="fw-medium text-success">B/. 36.00</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Ventas Recientes</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($producto->ventasRecientes ?? [] as $venta)
                                <tr>
                                    <td class="fw-medium">{{ $venta->factura->numero }}</td>
                                    <td>{{ $venta->factura->cliente->nombre }}</td>
                                    <td>{{ $venta->factura->fecha->format('d/m/Y') }}</td>
                                    <td>{{ $venta->cantidad }}</td>
                                    <td class="fw-medium">B/. {{ number_format($venta->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <!-- Demo data -->
                                <tr>
                                    <td class="fw-medium">FAC-2024-0012</td>
                                    <td>Comercial El Triunfo</td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                    <td>5</td>
                                    <td class="fw-medium">B/. 199.50</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0010</td>
                                    <td>Mini Super Central</td>
                                    <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                                    <td>3</td>
                                    <td class="fw-medium">B/. 135.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0008</td>
                                    <td>Abarrotería Don José</td>
                                    <td>{{ now()->subDays(3)->format('d/m/Y') }}</td>
                                    <td>10</td>
                                    <td class="fw-medium">B/. 360.00</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
