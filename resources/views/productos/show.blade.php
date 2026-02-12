@extends('layouts.app')

@section('title', 'Detalle de Producto')
@section('page-title', 'Detalle de Producto')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $producto->nombre }}</h5>
                    <div class="text-secondary" style="font-size: 0.85rem;">
                        Código: {{ 'PRD-'.str_pad($producto->id_producto, 3, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>

            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item"><strong>ID:</strong> {{ $producto->id_producto }}</li>
                    <li class="list-group-item">
                        <strong>Estado:</strong>
                        <span class="badge badge-{{ $producto->estado == 1 ? 'success' : 'danger' }}">
                            {{ $producto->estado == 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Unidad:</strong> {{ $producto->precioBase->unidad ?? '-' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Precio Base (grupo 1):</strong>
                        B/. {{ number_format($producto->precioBase->precio_base ?? 0, 2) }}
                    </li>
                </ul>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
