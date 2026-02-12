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
                <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">
                    Modifique la información del producto: <strong>{{ $producto->nombre }}</strong>
                </p>
            </div>

            <form action="{{ route('productos.update', $producto) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    @include('productos.partials.form', ['producto' => $producto])
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
