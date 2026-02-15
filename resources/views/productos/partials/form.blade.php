@php
    $isEdit = isset($producto) && ($producto?->id_producto);
    $codigo = $isEdit ? ('PRD-'.str_pad($producto->id_producto, 3, '0', STR_PAD_LEFT)) : 'Se generará al guardar';
    $precioBase = $isEdit ? ($producto->precioBase ?? null) : null;
@endphp

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Código</label>
            <input type="text" class="form-control" value="{{ $codigo }}" disabled>
            <div class="form-text">El código se genera automáticamente.</div>
        </div>
    </div>

    {{-- Nombre del Producto --}}
    <div class="{{ isset($producto) ? 'col-md-8' : 'col-md-12' }}">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('nombre') is-invalid @enderror"
                   id="nombre"
                   name="nombre"
                   value="{{ old('nombre', $producto->nombre ?? '') }}"
                   required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Estado (Solo visible en edición) --}}
    @if(isset($producto))
    <div class="col-md-4">
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="1" {{ $producto->estado == 1 ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ $producto->estado == 0 ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
    </div>
    @endif
</div>
