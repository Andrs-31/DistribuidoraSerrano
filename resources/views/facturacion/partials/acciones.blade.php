<div class="d-flex flex-wrap justify-content-between gap-2">
    <a href="{{ route('facturacion.index') }}" class="btn btn-secondary" id="btnCancelarFactura">
    <i class="bi bi-x-lg me-1"></i>Cancelar
</a>

    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" id="btnTerminarFactura">
            <i class="bi bi-check2-circle me-1"></i>Terminar factura
        </button>
    </div>
</div>

@push('scripts')
<!-- Emisión y vista previa ahora se manejan desde el detalle -->
@endpush
