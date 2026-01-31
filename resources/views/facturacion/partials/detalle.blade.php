<div class="mb-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0">Detalle de Productos</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarProducto">
            <i class="bi bi-plus-lg me-1"></i>Agregar Producto
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="tablaDetalle">
            <thead class="table-light">
                <tr>
                    <th style="width: 40%;">Producto</th>
                    <th style="width: 15%;">Cantidad</th>
                    <th style="width: 15%;">Unidad</th>
                    <th style="width: 15%;">Precio Unit.</th>
                    <th style="width: 15%;">Subtotal</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody id="detalleBody">
                @if(isset($factura) && $factura->detalles->count() > 0)
                    @foreach($factura->detalles as $index => $detalle)
                        <tr class="detalle-row" data-index="{{ $index }}">
                            <td>
                                <select class="form-select form-select-sm producto-select" name="detalles[{{ $index }}][producto_id]" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($productos ?? [] as $producto)
                                        <option value="{{ $producto->id }}"
                                                data-precio="{{ $producto->precio_base }}"
                                                data-unidad="{{ $producto->unidad }}"
                                                {{ $detalle->producto_id == $producto->id ? 'selected' : '' }}>
                                            {{ $producto->codigo }} - {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="detalles[{{ $index }}][descripcion]" value="{{ $detalle->descripcion }}">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm cantidad-input"
                                       name="detalles[{{ $index }}][cantidad]"
                                       value="{{ $detalle->cantidad }}"
                                       min="1" step="1" required>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm unidad-input"
                                       name="detalles[{{ $index }}][unidad]"
                                       value="{{ $detalle->unidad }}" readonly>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">B/.</span>
                                    <input type="number" class="form-control precio-input"
                                           name="detalles[{{ $index }}][precio_unitario]"
                                           value="{{ $detalle->precio_unitario }}"
                                           min="0" step="0.01" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">B/.</span>
                                    <input type="text" class="form-control subtotal-input"
                                           value="{{ number_format($detalle->subtotal, 2) }}" readonly>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-link text-danger btn-eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="detalle-row" data-index="0">
                        <td>
                            <select class="form-select form-select-sm producto-select" name="detalles[0][producto_id]" required>
                                <option value="">Seleccionar producto...</option>
                                @foreach($productos ?? [] as $producto)
                                    <option value="{{ $producto->id }}"
                                            data-precio="{{ $producto->precio_base }}"
                                            data-unidad="{{ $producto->unidad }}">
                                        {{ $producto->codigo }} - {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="detalles[0][descripcion]" value="">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm cantidad-input"
                                   name="detalles[0][cantidad]" value="1" min="1" step="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm unidad-input"
                                   name="detalles[0][unidad]" value="" readonly>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">B/.</span>
                                <input type="number" class="form-control precio-input"
                                       name="detalles[0][precio_unitario]" value="0.00" min="0" step="0.01" required>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">B/.</span>
                                <input type="text" class="form-control subtotal-input" value="0.00" readonly>
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-link text-danger btn-eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="text-center text-secondary py-3" id="emptyMessage" style="display: none;">
        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
        <p class="mb-0">No hay productos agregados. Haga clic en "Agregar Producto" para comenzar.</p>
    </div>
</div>

<!-- Product Row Template -->
<template id="productoRowTemplate">
    <tr class="detalle-row" data-index="__INDEX__">
        <td>
            <select class="form-select form-select-sm producto-select" name="detalles[__INDEX__][producto_id]" required>
                <option value="">Seleccionar producto...</option>
                @foreach($productos ?? [] as $producto)
                    <option value="{{ $producto->id }}"
                            data-precio="{{ $producto->precio_base }}"
                            data-unidad="{{ $producto->unidad }}">
                        {{ $producto->codigo }} - {{ $producto->nombre }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="detalles[__INDEX__][descripcion]" value="">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm cantidad-input"
                   name="detalles[__INDEX__][cantidad]" value="1" min="1" step="1" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm unidad-input"
                   name="detalles[__INDEX__][unidad]" value="" readonly>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text">B/.</span>
                <input type="number" class="form-control precio-input"
                       name="detalles[__INDEX__][precio_unitario]" value="0.00" min="0" step="0.01" required>
            </div>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text">B/.</span>
                <input type="text" class="form-control subtotal-input" value="0.00" readonly>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-link text-danger btn-eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
let rowIndex = document.querySelectorAll('.detalle-row').length;

// Add new product row
document.getElementById('btnAgregarProducto').addEventListener('click', function() {
    const template = document.getElementById('productoRowTemplate').innerHTML;
    const newRow = template.replace(/__INDEX__/g, rowIndex);
    document.getElementById('detalleBody').insertAdjacentHTML('beforeend', newRow);
    rowIndex++;
    updateEmptyMessage();
    attachRowEvents();
});

// Initialize row events
function attachRowEvents() {
    // Product selection
    document.querySelectorAll('.producto-select').forEach(select => {
        select.removeEventListener('change', handleProductChange);
        select.addEventListener('change', handleProductChange);
    });

    // Quantity/Price change
    document.querySelectorAll('.cantidad-input, .precio-input').forEach(input => {
        input.removeEventListener('input', handleInputChange);
        input.addEventListener('input', handleInputChange);
    });

    // Delete row
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.removeEventListener('click', handleDelete);
        btn.addEventListener('click', handleDelete);
    });
}

function handleProductChange(e) {
    const row = e.target.closest('.detalle-row');
    const selected = e.target.options[e.target.selectedIndex];

    if (e.target.value) {
        row.querySelector('.unidad-input').value = selected.dataset.unidad || '';
        row.querySelector('.precio-input').value = parseFloat(selected.dataset.precio || 0).toFixed(2);
        row.querySelector('input[name*="[descripcion]"]').value = selected.textContent.trim();
        calculateRowSubtotal(row);
    }
}

function handleInputChange(e) {
    const row = e.target.closest('.detalle-row');
    calculateRowSubtotal(row);
}

function handleDelete(e) {
    const rows = document.querySelectorAll('.detalle-row');
    if (rows.length > 1) {
        e.target.closest('.detalle-row').remove();
        calculateTotals();
        updateEmptyMessage();
    } else {
        alert('Debe haber al menos un producto en la factura');
    }
}

function calculateRowSubtotal(row) {
    const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
    const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
    const subtotal = cantidad * precio;
    row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.subtotal-input').forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });

    const itbms = subtotal * 0.07; // 7% ITBMS
    const total = subtotal + itbms;

    document.getElementById('subtotalDisplay').textContent = 'B/. ' + subtotal.toFixed(2);
    document.getElementById('itbmsDisplay').textContent = 'B/. ' + itbms.toFixed(2);
    document.getElementById('totalDisplay').textContent = 'B/. ' + total.toFixed(2);

    // Hidden inputs for form submission
    if (document.getElementById('subtotalInput')) {
        document.getElementById('subtotalInput').value = subtotal.toFixed(2);
        document.getElementById('itbmsInput').value = itbms.toFixed(2);
        document.getElementById('totalInput').value = total.toFixed(2);
    }
}

function updateEmptyMessage() {
    const rows = document.querySelectorAll('.detalle-row');
    document.getElementById('emptyMessage').style.display = rows.length === 0 ? 'block' : 'none';
    document.getElementById('tablaDetalle').style.display = rows.length === 0 ? 'none' : 'table';
}

// Initialize
attachRowEvents();
calculateTotals();
</script>
@endpush
