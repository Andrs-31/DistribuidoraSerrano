@extends('layouts.app')

@section('title', 'Editar Producto')
@section('page-title', 'Editar Producto')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Editar Producto</h5>
                <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Modifique la información del producto: {{ $producto->nombre ?? 'Producto' }}</p>
            </div>

            <form action="{{ route('productos.update', $producto ?? 1) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    @include('productos.partials.form')
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-1"></i>Desactivar
                    </button>
                    <div class="d-flex gap-2">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Actualizar Producto
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Confirmar desactivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">¿Está seguro de que desea desactivar el producto <strong>{{ $producto->nombre ?? 'Producto' }}</strong>? Esta acción no eliminará los registros históricos.</p>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('productos.destroy', $producto ?? 1) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Desactivar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
