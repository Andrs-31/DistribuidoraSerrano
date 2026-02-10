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
                <span class="badge badge-{{ ($cliente->estado ?? 1) ? 'success' : 'danger' }} mb-3">
                    {{ ($cliente->estado ?? 1) ? 'Activo' : 'Inactivo' }}
                </span>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('clientes.edit', $cliente->ruc) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <a href="{{ route('facturacion.create', ['cliente' => $cliente->ruc]) }}" class="btn btn-success btn-sm">
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
                        <div class="fw-medium">{{ $cliente->grupoCliente->nombre ?? '—' }}</div>
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
                        <div class="fw-medium">{{ $cliente->direccion ?? 'Calle 50, Local 12, Ciudad' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-calendar-check"></i> Fecha de registro
                        </div>
                        <div class="fw-medium">{{ \Carbon\Carbon::parse($cliente->fecha_registro)?->format('d/m/Y H:i') ?? 'Sin registro' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-pencil-square"></i> Fecha de edición
                        </div>
                        <div class="fw-medium">{{ \Carbon\Carbon::parse($cliente->fecha_edicion)?->format('d/m/Y H:i') ?? 'Sin edición' }}</div>
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
                        <div class="fs-4 fw-bold">{{ $facturas_count }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Total Compras</div>
                        <div class="fs-4 fw-bold">B/. {{ number_format($total_compras, 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Última Compra</div>
                        <div class="fw-medium">{{ $ultima_factura?->fecha_factura?->format('d/m/Y') ?? 'Sin facturas' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary" style="font-size: 0.75rem;">Cliente Desde</div>
                        <div class="fw-medium">{{ \Carbon\Carbon::parse($cliente->fecha_registro)?->format('M Y') ?? 'Sin registro' }}</div>
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
                <a href="{{ route('facturacion.index', ['cliente' => $cliente->ruc]) }}" class="btn btn-sm btn-outline-primary">
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
                            @forelse($facturas as $factura)
                                <tr>
                                    <td class="fw-medium">{{ $factura->num_factura }}</td>
                                    <td>{{ $factura->fecha_factura?->format('d/m/Y') ?? '—' }}</td>
                                    <td>{{ $factura->detalles_count ?? 0 }} items</td>
                                    <td class="fw-medium">B/. {{ number_format($factura->total ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ ($factura->estado ?? '') === 'pagada' ? 'success' : 'warning' }}">
                                            {{ ucfirst($factura->estado ?? '') }}
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
                                    <td colspan="6" class="text-center text-secondary">No hay facturas para este cliente.</td>
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
