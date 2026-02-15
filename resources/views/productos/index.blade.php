@extends('layouts.app')

@section('title', 'Gestión de Productos')
@section('page-title', 'Productos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Productos</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h5 class="mb-1">Catálogo de Productos</h5>
            <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">
                Administra los productos y sus precios por grupo de cliente
            </p>
        </div>
        <a href="{{ route('productos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Nuevo Producto
        </a>
    </div>

    <div class="card-body border-bottom">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                           placeholder="Buscar por nombre o ID...">
                </div>
            </div>

            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="col-md-3">
    <select class="form-select" name="grupo">
        <option value="">Todos los grupos</option>
        @foreach($grupos as $g)
            <option value="{{ $g->id_grupo }}" {{ (string)request('grupo') === (string)$g->id_grupo ? 'selected' : '' }}>
                {{ $g->nombre }}
            </option>
        @endforeach
    </select>
</div>


            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        @php
                            $codigo = 'PRD-'.str_pad($producto->id_producto, 3, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <td class="fw-medium">{{ $codigo }}</td>
                            <td class="fw-medium">{{ $producto->nombre }}</td>
                            <td>
                                <span class="badge badge-{{ $producto->estado == 1 ? 'success' : 'danger' }}">
                                    {{ $producto->estado == 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    @if($producto->estado == 1)
                                        <button type="button" class="btn btn-sm btn-light text-warning" title="Desactivar"
                                                data-bs-toggle="modal" data-bs-target="#toggleModal{{ $producto->id_producto }}">
                                            <i class="bi bi-toggle-on"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light text-success" title="Activar"
                                                data-bs-toggle="modal" data-bs-target="#toggleModal{{ $producto->id_producto }}">
                                            <i class="bi bi-toggle-off text-success"></i>
                                        </button>
                                    @endif
                                </div>
                                <a href="{{ route('productos.precios', $producto) }}" 
                                class="btn btn-sm btn-light"
                                title="Administrar Precios">
                                    <i class="bi bi-tags"></i>
                                </a>


                                <!-- Toggle Modal (activar/desactivar) -->
                                <div class="modal fade" id="toggleModal{{ $producto->id_producto }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title">{{ $producto->estado == 1 ? 'Confirmar desactivación' : 'Confirmar activación' }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p class="mb-0">{!! $producto->estado == 1 ? "¿Está seguro de que desea desactivar al producto <strong>{$producto->nombre}</strong>?" : "¿Está seguro de que desea activar al producto <strong>{$producto->nombre}</strong>?" !!}</p>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn {{ $producto->estado == 1 ? 'btn-warning' : 'btn-success' }}">
                                                        {{ $producto->estado == 1 ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">
                                No hay productos para mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($productos->hasPages())
        <div class="card-footer">
            {{ $productos->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
