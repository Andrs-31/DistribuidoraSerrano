<div class="row g-3">
    <div class="col-md-6">
        <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
        <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
            <option value="">Seleccionar cliente...</option>
            @foreach($clientes ?? [] as $cliente)
                <option value="{{ $cliente->id }}"
                        data-ruc="{{ $cliente->ruc }}"
                        data-direccion="{{ $cliente->direccion }}"
                        data-telefono="{{ $cliente->telefono }}"
                        data-grupo="{{ $cliente->grupo_id }}"
                        {{ old('cliente_id', $factura->cliente_id ?? request('cliente')) == $cliente->id ? 'selected' : '' }}>
                    {{ $cliente->nombre }} - {{ $cliente->ruc }}
                </option>
            @endforeach
        </select>
        @error('cliente_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
        <input type="date"
               class="form-control @error('fecha') is-invalid @enderror"
               id="fecha"
               name="fecha"
               value="{{ old('fecha', isset($factura) ? $factura->fecha->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required>
        @error('fecha')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label for="numero" class="form-label">N° Factura</label>
        <input type="text"
               class="form-control"
               id="numero"
               name="numero"
               value="{{ old('numero', $factura->numero ?? $proximoNumero ?? 'FAC-' . now()->format('Y') . '-0001') }}"
               readonly>
        <div class="form-text">Generado automáticamente</div>
    </div>

    <!-- Client Info Display -->
    <div class="col-12" id="clienteInfo" style="display: none;">
        <div class="alert alert-light border mb-0">
            <div class="row">
                <div class="col-md-4">
                    <small class="text-secondary">RUC/Cédula:</small>
                    <div class="fw-medium" id="clienteRuc">-</div>
                </div>
                <div class="col-md-4">
                    <small class="text-secondary">Teléfono:</small>
                    <div class="fw-medium" id="clienteTelefono">-</div>
                </div>
                <div class="col-md-4">
                    <small class="text-secondary">Dirección:</small>
                    <div class="fw-medium" id="clienteDireccion">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('cliente_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('clienteInfo');

    if (this.value) {
        document.getElementById('clienteRuc').textContent = selected.dataset.ruc || '-';
        document.getElementById('clienteTelefono').textContent = selected.dataset.telefono || '-';
        document.getElementById('clienteDireccion').textContent = selected.dataset.direccion || '-';
        infoDiv.style.display = 'block';

        // Store grupo for price lookup
        window.clienteGrupoId = selected.dataset.grupo;
    } else {
        infoDiv.style.display = 'none';
    }
});

// Trigger on page load if client is selected
if (document.getElementById('cliente_id').value) {
    document.getElementById('cliente_id').dispatchEvent(new Event('change'));
}
</script>
@endpush
