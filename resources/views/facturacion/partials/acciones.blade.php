<div class="d-flex flex-wrap justify-content-between gap-2">
    <a href="{{ route('facturacion.index') }}" class="btn btn-secondary">
        <i class="bi bi-x-lg me-1"></i>Cancelar
    </a>

    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" id="btnVistaPrevia">
            <i class="bi bi-eye me-1"></i>Vista Previa
        </button>

        <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
            <i class="bi bi-save me-1"></i>Guardar Borrador
        </button>

        <button type="submit" name="action" value="emit" class="btn btn-success">
            <i class="bi bi-check-lg me-1"></i>Emitir Factura
        </button>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Vista Previa de Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="facturaForm" name="action" value="emit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i>Emitir Factura
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnVistaPrevia').addEventListener('click', function() {
    // Generate preview content
    const cliente = document.getElementById('cliente_id');
    const clienteNombre = cliente.options[cliente.selectedIndex]?.textContent || 'No seleccionado';
    const fecha = document.getElementById('fecha').value;
    const numero = document.getElementById('numero').value;

    let productosHtml = '';
    document.querySelectorAll('.detalle-row').forEach(row => {
        const productoSelect = row.querySelector('.producto-select');
        const productoNombre = productoSelect.options[productoSelect.selectedIndex]?.textContent || '-';
        const cantidad = row.querySelector('.cantidad-input').value;
        const unidad = row.querySelector('.unidad-input').value;
        const precio = row.querySelector('.precio-input').value;
        const subtotal = row.querySelector('.subtotal-input').value;

        if (productoSelect.value) {
            productosHtml += `
                <tr>
                    <td>${productoNombre}</td>
                    <td class="text-center">${cantidad}</td>
                    <td class="text-center">${unidad}</td>
                    <td class="text-end">B/. ${parseFloat(precio).toFixed(2)}</td>
                    <td class="text-end">B/. ${parseFloat(subtotal).toFixed(2)}</td>
                </tr>
            `;
        }
    });

    const subtotal = document.getElementById('subtotalDisplay').textContent;
    const itbms = document.getElementById('itbmsDisplay').textContent;
    const total = document.getElementById('totalDisplay').textContent;
    const observaciones = document.getElementById('observaciones').value;

    const previewHtml = `
        <div class="p-3">
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1">Distribuidora Serrano</h4>
                <p class="text-secondary mb-0">RUC: 12345678-1-2020</p>
                <p class="text-secondary mb-0">Ciudad de Panamá, Panamá</p>
            </div>

            <hr>

            <div class="row mb-4">
                <div class="col-6">
                    <strong>Cliente:</strong><br>
                    ${clienteNombre}<br>
                    <small class="text-secondary">${document.getElementById('clienteRuc')?.textContent || ''}</small>
                </div>
                <div class="col-6 text-end">
                    <strong>Factura N°:</strong> ${numero}<br>
                    <strong>Fecha:</strong> ${fecha}
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Descripción</th>
                        <th class="text-center" style="width: 80px;">Cant.</th>
                        <th class="text-center" style="width: 80px;">Unidad</th>
                        <th class="text-end" style="width: 100px;">P. Unit.</th>
                        <th class="text-end" style="width: 100px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${productosHtml || '<tr><td colspan="5" class="text-center text-secondary">Sin productos</td></tr>'}
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-6">
                    ${observaciones ? '<p><strong>Observaciones:</strong><br>' + observaciones + '</p>' : ''}
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-end">Subtotal:</td>
                            <td class="text-end fw-medium">${subtotal}</td>
                        </tr>
                        <tr>
                            <td class="text-end">ITBMS (7%):</td>
                            <td class="text-end fw-medium">${itbms}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-end"><strong>TOTAL:</strong></td>
                            <td class="text-end"><strong class="fs-5">${total}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    `;

    document.getElementById('previewContent').innerHTML = previewHtml;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
});
</script>
@endpush
