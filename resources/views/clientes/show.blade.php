@extends('layouts.app')

@section('title', 'Detalle de Cliente')
@section('page-title', 'Detalle de Cliente')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active">{{ $cliente->nombre ?? 'Detalle' }}</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Client Info -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-building fs-1 text-secondary"></i>
                </div>
                <h4 class="mb-1">{{ $cliente->nombre ?? 'Comercial El Triunfo, S.A.' }}</h4>
                <p class="text-secondary mb-3">{{ $cliente->ruc ?? '155634567-2-2020' }}</p>
                <span class="badge badge-{{ ($cliente->activo ?? true) ? 'success' : 'danger' }} mb-3">
                    {{ ($cliente->activo ?? true) ? 'Activo' : 'Inactivo' }}
                </span>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('clientes.edit', $cliente ?? 1) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <a href="{{ route('facturacion.create', ['cliente' => $cliente->id ?? 1]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-receipt me-1"></i>Nueva Factura
                    </a>
                </div>
            </div>
            <div class="card-body border-top">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-people"></i> Grupo
                        </div>
                        <div class="fw-medium">{{ $cliente->grupo->nombre ?? 'Mayoristas' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-envelope"></i> Correo
                        </div>
                        <div class="fw-medium">{{ $cliente->email ?? 'eltriunfo@gmail.com' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-telephone"></i> Teléfono
                        </div>
                        <div class="fw-medium">{{ $cliente->telefono ?? '+507 6789-0123' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-geo-alt"></i> Dirección
                        </div>
                        <div class="fw-medium">{{ $cliente->direccion ?? 'Calle 50, Local 12, Ciudad de Panamá' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-map"></i> Ubicación
                        </div>
                        <div class="fw-medium">{{ $cliente->distrito ?? 'Panamá' }}, {{ $cliente->provincia ?? 'Panamá' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Resumen</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Total Facturas</div>
                        <div class="fs-4 fw-bold">{{ $cliente->facturas_count ?? 24 }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Total Compras</div>
                        <div class="fs-4 fw-bold">B/. {{ number_format($cliente->total_compras ?? 12450.00, 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Última Compra</div>
                        <div class="fw-medium">{{ $cliente->ultima_factura->fecha->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Cliente Desde</div>
                        <div class="fw-medium">{{ $cliente->created_at->format('M Y') ?? 'Ene 2023' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Historial de Facturas</h5>
                <a href="{{ route('facturacion.index', ['cliente' => $cliente->id ?? 1]) }}" class="btn btn-sm btn-outline-primary">
                    Ver Todas
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>N° Factura</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cliente->facturas ?? [] as $factura)
                                <tr>
                                    <td class="fw-medium">{{ $factura->numero }}</td>
                                    <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                                    <td>{{ $factura->detalles_count }} items</td>
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
                                <!-- Demo data -->
                                <tr>
                                    <td class="fw-medium">FAC-2024-0012</td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                    <td>5 items</td>
                                    <td class="fw-medium">B/. 245.50</td>
                                    <td><span class="badge badge-success">Pagada</span></td>
                                    <td><button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0008</td>
                                    <td>{{ now()->subDays(5)->format('d/m/Y') }}</td>
                                    <td>8 items</td>
                                    <td class="fw-medium">B/. 567.25</td>
                                    <td><span class="badge badge-success">Pagada</span></td>
                                    <td><button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0003</td>
                                    <td>{{ now()->subDays(12)->format('d/m/Y') }}</td>
                                    <td>3 items</td>
                                    <td class="fw-medium">B/. 189.00</td>
                                    <td><span class="badge badge-success">Pagada</span></td>
                                    <td><button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">FAC-2024-0001</td>
                                    <td>{{ now()->subDays(20)->format('d/m/Y') }}</td>
                                    <td>12 items</td>
                                    <td class="fw-medium">B/. 1,234.00</td>
                                    <td><span class="badge badge-success">Pagada</span></td>
                                    <td><button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if(isset($cliente->notas) && $cliente->notas)
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Notas</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-secondary">{{ $cliente->notas }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
