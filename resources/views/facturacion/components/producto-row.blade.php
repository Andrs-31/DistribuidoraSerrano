@props(['index' => 0, 'detalle' => null, 'productos' => []])

<tr class="detalle-row" data-index="{{ $index }}">
    <td>
        <select class="form-select form-select-sm producto-select" name="detalles[{{ $index }}][producto_id]" required>
            <option value="">Seleccionar producto...</option>
            @foreach($productos as $producto)
                <option value="{{ $producto->id }}"
                        data-precio="{{ $producto->precio_base }}"
                        data-unidad="{{ $producto->unidad }}"
                        data-nombre="{{ $producto->nombre }}"
                        {{ ($detalle->producto_id ?? '') == $producto->id ? 'selected' : '' }}>
                    {{ $producto->codigo }} - {{ $producto->nombre }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="detalles[{{ $index }}][descripcion]" class="descripcion-input"
               value="{{ $detalle->descripcion ?? '' }}">
    </td>
    <td>
        <input type="number" class="form-control form-control-sm cantidad-input"
               name="detalles[{{ $index }}][cantidad]"
               value="{{ $detalle->cantidad ?? 1 }}"
               min="1" step="1" required>
    </td>
    <td>
        <input type="text" class="form-control form-control-sm unidad-input"
               name="detalles[{{ $index }}][unidad]"
               value="{{ $detalle->unidad ?? '' }}" readonly>
    </td>
    <td>
        <div class="input-group input-group-sm">
            <span class="input-group-text">B/.</span>
            <input type="number" class="form-control precio-input"
                   name="detalles[{{ $index }}][precio_unitario]"
                   value="{{ number_format($detalle->precio_unitario ?? 0, 2, '.', '') }}"
                   min="0" step="0.01" required>
        </div>
    </td>
    <td>
        <div class="input-group input-group-sm">
            <span class="input-group-text">B/.</span>
            <input type="text" class="form-control subtotal-input"
                   value="{{ number_format($detalle->subtotal ?? 0, 2, '.', '') }}" readonly>
        </div>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-link text-danger btn-eliminar p-0">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
