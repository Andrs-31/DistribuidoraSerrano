@props(['factura' => null])

<div class="card bg-light border-0">
    <div class="card-body">
        <h6 class="mb-3 fw-bold">Resumen de Factura</h6>

        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
            <span class="text-secondary">Subtotal:</span>
            <span class="fw-medium" id="resumenSubtotal">B/. {{ number_format($factura->subtotal ?? 0, 2) }}</span>
        </div>

        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
            <span class="text-secondary">ITBMS (7%):</span>
            <span class="fw-medium" id="resumenItbms">B/. {{ number_format($factura->itbms ?? 0, 2) }}</span>
        </div>

        <div class="d-flex justify-content-between pt-2">
            <span class="fw-bold fs-5">TOTAL:</span>
            <span class="fw-bold fs-4" style="color: var(--ds-primary);" id="resumenTotal">B/. {{ number_format($factura->total ?? 0, 2) }}</span>
        </div>
    </div>
</div>

<!-- Stats Mini -->
@if(isset($factura) && isset($factura->detalles))
<div class="mt-3">
    <div class="row g-2">
        <div class="col-6">
            <div class="bg-white p-3 rounded border text-center">
                <div class="text-secondary" style="font-size: 0.75rem;">Productos</div>
                <div class="fw-bold fs-4" id="resumenProductos">{{ $factura->detalles->count() }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="bg-white p-3 rounded border text-center">
                <div class="text-secondary" style="font-size: 0.75rem;">Unidades</div>
                <div class="fw-bold fs-4" id="resumenUnidades">{{ $factura->detalles->sum('cantidad') }}</div>
            </div>
        </div>
    </div>
</div>
@endif
