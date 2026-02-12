@extends('layouts.app')

@section('title', 'Precios por Cliente/Grupo')
@section('page-title', 'Precios por Cliente/Grupo')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">Precios</li>
@endsection

@section('content')
@php
    $codigo = 'PRD-'.str_pad($producto->id_producto, 3, '0', STR_PAD_LEFT);
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Precios por Cliente/Grupo</h5>
            <div class="text-secondary" style="font-size:.85rem;">
                {{ $codigo }} — <strong>{{ $producto->nombre }}</strong>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('productos.show', $producto) }}" class="btn btn-light">Ver</a>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Cliente/Grupo</th>
                        <th style="width: 160px;">Unidad</th>
                        <th style="width: 200px;">Precio</th>
                        <th style="width: 140px;">Estado</th>
                        <th class="text-end" style="width: 220px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupos as $g)
@php
    $fila = $precios->where('id_grupo_fk', $g->id_grupo)->first();
    $formId = "form-precio-{$g->id_grupo}";
@endphp

<tr>
    <td class="fw-medium">
        {{ $g->nombre }}
        <div class="text-secondary" style="font-size:.8rem;">ID Grupo: {{ $g->id_grupo }}</div>
    </td>

    <td>
        <select class="form-select form-select-sm" name="unidad" required form="{{ $formId }}">
            @foreach(['lb','kg','Unidad','Caja','Quintal','Litro','Galón','Saco','Paquete'] as $u)
                <option value="{{ $u }}" {{ old('unidad', $fila->unidad ?? 'lb') === $u ? 'selected' : '' }}>{{ $u }}</option>
            @endforeach
        </select>
    </td>

    <td>
        <div class="input-group input-group-sm">
            <span class="input-group-text">B/.</span>
            <input type="number" step="0.01" min="0"
                   class="form-control"
                   name="precio_base"
                   value="{{ old('precio_base', $fila->precio_base ?? 0) }}"
                   required
                   form="{{ $formId }}">
        </div>
    </td>

    <td>
        <select class="form-select form-select-sm" name="estado" required form="{{ $formId }}">
            <option value="1" {{ old('estado', isset($fila)?(string)$fila->estado:'1') === '1' ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('estado', isset($fila)?(string)$fila->estado:'1') === '0' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </td>

    <td class="text-end">
        {{-- FORM SOLO PARA UPSERT --}}
        <form id="{{ $formId }}" action="{{ route('productos.precios.upsert', $producto) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="id_grupo_fk" value="{{ $g->id_grupo }}">
            <button class="btn btn-sm btn-primary">
                <i class="bi bi-save me-1"></i>Guardar
            </button>
        </form>

        {{-- DELETE SEPARADO  --}}
        @if($fila)
            <button type="button" class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#modalDesactivar{{ $fila->id_lista }}">
                <i class="bi bi-trash"></i>
            </button>

            <div class="modal fade" id="modalDesactivar{{ $fila->id_lista }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Desactivar precio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ¿Desactivar el precio para <strong>{{ $g->nombre }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                            <form action="{{ route('productos.precios.desactivar', [$producto->id_producto, $fila->id_lista]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Desactivar</button>
                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
@endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
