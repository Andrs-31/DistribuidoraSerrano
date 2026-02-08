<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">Datos Generales</h6>
            </div>
            <div class="card-body">
                @include('facturacion.partials.encabezado')
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">Opciones</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small fw-medium">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3" placeholder="Notas para la factura..."></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Productos</h6>
            </div>
            <div class="card-body p-0">
                @include('facturacion.partials.detalle')
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-end">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary">Subtotal:</span>
                    <span id="subtotalDisplay" class="fw-bold">B/. 0.00</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="h5 mb-0 fw-bold text-dark">Total:</span>
                    <span id="totalDisplay" class="h4 mb-0 fw-bold text-danger">B/. 0.00</span>
                </div>
            </div>
        </div>
    </div>
</div>