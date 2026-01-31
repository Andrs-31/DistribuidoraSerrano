<div class="card bg-light border-0">
    <div class="card-body">
        <h6 class="mb-3">Resumen de Factura</h6>

        <div class="d-flex justify-content-between mb-2">
            <span class="text-secondary">Subtotal:</span>
            <span class="fw-medium" id="subtotalDisplay">B/. {{ number_format($factura->subtotal ?? 0, 2) }}</span>
        </div>

        <div class="d-flex justify-content-between mb-2">
            <span class="text-secondary">ITBMS (7%):</span>
            <span class="fw-medium" id="itbmsDisplay">B/. {{ number_format($factura->itbms ?? 0, 2) }}</span>
        </div>

        <hr>

        <div class="d-flex justify-content-between">
            <span class="fw-bold fs-5">Total:</span>
            <span class="fw-bold fs-5" style="color: var(--ds-primary);" id="totalDisplay">B/. {{ number_format($factura->total ?? 0, 2) }}</span>
        </div>

        <!-- Hidden inputs for form submission -->
        <input type="hidden" name="subtotal" id="subtotalInput" value="{{ $factura->subtotal ?? 0 }}">
        <input type="hidden" name="itbms" id="itbmsInput" value="{{ $factura->itbms ?? 0 }}">
        <input type="hidden" name="total" id="totalInput" value="{{ $factura->total ?? 0 }}">
    </div>
</div>

<!-- Additional Options -->
<div class="card mt-3">
    <div class="card-body">
        <h6 class="mb-3">Opciones</h6>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" id="observaciones" name="observaciones" rows="2"
                      placeholder="Notas adicionales para la factura...">{{ old('observaciones', $factura->observaciones ?? '') }}</textarea>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="enviar_correo" name="enviar_correo" value="1">
            <label class="form-check-label" for="enviar_correo">
                Enviar factura por correo electrónico al cliente
            </label>
        </div>
    </div>
</div>
