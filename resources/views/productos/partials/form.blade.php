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

    <div class="col-md-8">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('nombre') is-invalid @enderror"
                   id="nombre"
                   name="nombre"
                   value="{{ old('nombre', $producto->nombre ?? '') }}"
                   placeholder="Ingrese el nombre del producto"
                   required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="unidad" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
            <select class="form-select @error('unidad') is-invalid @enderror" id="unidad" name="unidad" required>
                <option value="">Seleccionar unidad...</option>
                @php
                    $unidadOld = old('unidad', $precioBase->unidad ?? '');
                @endphp
                @foreach(['lb','kg','Unidad','Caja','Quintal','Litro','Galón','Saco','Paquete'] as $u)
                    <option value="{{ $u }}" {{ $unidadOld === $u ? 'selected' : '' }}>{{ $u }}</option>
                @endforeach
            </select>
            @error('unidad')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">La unidad se guarda en la lista de precios.</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="precio_base" class="form-label">Precio Base (B/.) <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">B/.</span>
                <input type="number"
                       class="form-control @error('precio_base') is-invalid @enderror"
                       id="precio_base"
                       name="precio_base"
                       value="{{ old('precio_base', $precioBase->precio_base ?? '') }}"
                       placeholder="0.00"
                       step="0.01"
                       min="0"
                       required>
            </div>
            @error('precio_base')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div class="form-text"></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
            @php
                $estadoOld = old('estado', isset($producto) ? (string)$producto->estado : '1');
            @endphp
            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                <option value="1" {{ $estadoOld === '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ $estadoOld === '0' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
