<div class="card shadow-sm border-0 mb-4">
    <!-- Toast container para notificaciones -->
    <div id="detalleToastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1100;"></div>

    <div class="card-header bg-white py-3 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary bg-opacity-10 p-2 rounded text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Detalle de Productos</h6>
                    <small class="text-muted d-none d-md-block">Agregue los artículos a facturar</small>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill d-flex align-items-center gap-2 shadow-sm" id="btnAgregarProducto">
                    <i class="bi bi-plus-circle"></i>
                    <span class="fw-semibold">Agregar Producto</span>
                </button>

                <!-- Terminar factura moved to acciones.blade.php -->
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaDetalle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 40%;">Producto</th>
                        <th style="width: 12%;">Cantidad</th>
                        <th style="width: 15%;">Unidad</th>
                        <th style="width: 15%;">Precio Unit.</th>
                        <th style="width: 15%;">Subtotal</th>
                        <th class="text-center pe-4" style="width: 50px;">Acción</th>
                    </tr>
                </thead>
                <tbody id="detalleBody" class="border-top-0">
                    @if(isset($factura) && $factura->detalles->count() > 0)
                        @foreach($factura->detalles as $index => $detalle)
                            <tr class="detalle-row" data-index="{{ $index }}">
                                <td class="ps-4">
                                    <select class="form-select form-select-sm producto-select" name="detalles[{{ $index }}][producto_id]" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($productos ?? [] as $producto)
                                            <option value="{{ $producto->id }}"
                                                    data-unidades="{{ json_encode($producto->unidades ?? []) }}"
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
                                    <select class="form-select form-select-sm unidad-select" name="detalles[{{ $index }}][unidad]" required>
                                        <option value="{{ $detalle->unidad }}" selected>{{ $detalle->unidad }}</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted">B/.</span>
                                        <input type="number" class="form-control precio-input"
                                               name="detalles[{{ $index }}][precio_unitario]"
                                               value="{{ $detalle->precio_unitario }}"
                                               min="0" step="0.01" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted">B/.</span>
                                        <input type="text" class="form-control subtotal-input fw-bold bg-light"
                                               value="{{ number_format($detalle->subtotal, 2, '.', '') }}" readonly>
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-eliminar" title="Eliminar fila">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="text-center text-secondary py-5" id="emptyMessage" style="display: none;">
            <div class="mb-3">
                <i class="bi bi-cart-plus opacity-25" style="font-size: 4rem;"></i>
            </div>
            <h6 class="fw-bold">La lista está vacía</h6>
            <p class="small mb-0 text-muted">Haga clic en el botón superior para agregar productos.</p>
        </div>
    </div>
</div>

<template id="productoRowTemplate">
    <tr class="detalle-row" data-index="__INDEX__">
        <td class="ps-4">
            <select class="form-select form-select-sm producto-select" name="detalles[__INDEX__][producto_id]" required>
                <option value="">Seleccionar producto...</option>
            </select>
            <input type="hidden" name="detalles[__INDEX__][descripcion]" value="">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm cantidad-input"
                   name="detalles[__INDEX__][cantidad]" value="1" min="1" step="1" required>
        </td>
        <td>
            <select class="form-select form-select-sm unidad-select" name="detalles[__INDEX__][unidad]" required>
                <option value="">...</option>
            </select>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted">B/.</span>
                <input type="number" class="form-control precio-input"
                       name="detalles[__INDEX__][precio_unitario]" value="0.00" min="0" step="0.01" required>
            </div>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted">B/.</span>
                <input type="text" class="form-control subtotal-input fw-bold bg-light" value="0.00" readonly>
                <input type="hidden" name="detalles[__INDEX__][subtotal]" class="subtotal-hidden" value="0.00">
            </div>
        </td>
        <td class="text-center pe-4">
            <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-eliminar">
                <i class="bi bi-trash3"></i>
            </button>
        </td>
    </tr>
</template>

<!-- Modal: Vista previa de factura -->
<div class="modal fade" id="facturaPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista previa de factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="facturaPreviewBody">
                <!-- Contenido generado dinámicamente -->
                <div class="text-center text-muted py-5">Generando vista previa...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-outline-primary" id="btnEnviarCorreo">Enviar por correo</button>
                <button type="button" class="btn btn-primary" id="btnDescargarImprimir">Descargar/Imprimir</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    let rowIndex = document.querySelectorAll('.detalle-row').length;

    // --- FUNCIÓN PARA MOSTRAR NOTIFICACIONES ---
    function showToast(message, type = 'danger') {
        const container = document.getElementById('detalleToastContainer');
        const toastId = 'toast-' + Date.now();
        
        const bgClass = type === 'danger' ? 'bg-danger' : (type === 'warning' ? 'bg-warning' : 'bg-success');
        const icon = type === 'danger' ? 'exclamation-circle' : (type === 'warning' ? 'exclamation-triangle' : 'check-circle');
        
        const toastHtml = `
            <div id="${toastId}" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${bgClass} text-white">
                    <i class="bi bi-${icon} me-2"></i>
                    <strong class="me-auto">${type === 'danger' ? 'Error' : (type === 'warning' ? 'Advertencia' : 'Éxito')}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', toastHtml);
        
        // Auto-remover después de 4 segundos
        setTimeout(() => {
            const element = document.getElementById(toastId);
            if (element) {
                element.classList.remove('show');
                setTimeout(() => element.remove(), 300);
            }
        }, 4000);
    }

    // --- LÓGICA DE SELECTS ---
    
    function poblarSelectProducto(select, productos) {
        select.innerHTML = '<option value="">Seleccionar producto...</option>';
        productos.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = `${p.codigo} - ${p.nombre}`;
            opt.dataset.unidades = JSON.stringify(p.unidades || []);
            select.appendChild(opt);
        });
    }

    function manejarCambioProducto(row) {
        const selectProd = row.querySelector('.producto-select');
        const option = selectProd.options[selectProd.selectedIndex];
        const uSelect = row.querySelector('.unidad-select');
        const precioInput = row.querySelector('.precio-input');
        const descInput = row.querySelector('input[name*="[descripcion]"]');

        if (!option || !option.value) {
            uSelect.innerHTML = '<option value="">...</option>';
            precioInput.value = "0.00";
            return;
        }

        const unidades = JSON.parse(option.dataset.unidades || '[]');
        uSelect.innerHTML = '<option value="">Seleccionar unidad...</option>';
        
        unidades.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.unidad;
            opt.textContent = `${u.unidad} (B/. ${u.precio})`;
            opt.dataset.precio = u.precio;
            uSelect.appendChild(opt);
        });

        descInput.value = option.textContent.trim();

        if (unidades.length === 1) {
            uSelect.selectedIndex = 1;
            precioInput.value = unidades[0].precio;
        } else {
            precioInput.value = "0.00";
        }
        
        calculateRowSubtotal(row);
    }

    // --- EXPOSICIÓN GLOBAL ---
    window.actualizarProductosDelCliente = function(productos) {
        window.productosDelCliente = productos;
        document.getElementById('detalleBody').innerHTML = '';
        updateEmptyMessage();
        calculateTotals();
    };

    // --- EVENTOS ---
    function attachRowEvents() {
        document.querySelectorAll('.producto-select').forEach(s => {
            s.onchange = (e) => manejarCambioProducto(e.target.closest('.detalle-row'));
        });

        document.querySelectorAll('.unidad-select').forEach(s => {
            s.onchange = (e) => {
                const row = e.target.closest('.detalle-row');
                const opt = e.target.options[e.target.selectedIndex];
                if (opt && opt.dataset.precio) {
                    row.querySelector('.precio-input').value = opt.dataset.precio;
                    calculateRowSubtotal(row);
                }
            };
        });

        document.querySelectorAll('.cantidad-input, .precio-input').forEach(i => {
            i.oninput = (e) => calculateRowSubtotal(e.target.closest('.detalle-row'));
        });

        document.querySelectorAll('.btn-eliminar').forEach(b => {
            b.onclick = (e) => {
                e.target.closest('.detalle-row').remove();
                calculateTotals();
                updateEmptyMessage();
            };
        });
    }

    document.getElementById('btnAgregarProducto').addEventListener('click', function() {
        if (!window.productosDelCliente) {
            alert("Seleccione un cliente primero.");
            return;
        }

        const template = document.getElementById('productoRowTemplate').innerHTML;
        const newRowHtml = template.replace(/__INDEX__/g, rowIndex);
        document.getElementById('detalleBody').insertAdjacentHTML('beforeend', newRowHtml);
        
        const lastRow = document.querySelector(`.detalle-row[data-index="${rowIndex}"]`);
        poblarSelectProducto(lastRow.querySelector('.producto-select'), window.productosDelCliente);
        
        rowIndex++;
        attachRowEvents();
        updateEmptyMessage();
    });

    // --- CÁLCULOS (SIN ITBMS) ---
    function calculateRowSubtotal(row) {
        const c = parseFloat(row.querySelector('.cantidad-input').value) || 0;
        const p = parseFloat(row.querySelector('.precio-input').value) || 0;
        const subtotalValue = (c * p).toFixed(2);
        row.querySelector('.subtotal-input').value = subtotalValue;
        row.querySelector('.subtotal-hidden').value = subtotalValue;
        calculateTotals();
    }

    function calculateTotals() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(i => total += parseFloat(i.value) || 0);
        
        // Actualizamos solo los campos necesarios (Total y Subtotal serán iguales)
        if(document.getElementById('subtotalDisplay')) {
            document.getElementById('subtotalDisplay').textContent = 'B/. ' + total.toFixed(2);
        }
        
        // Si tienes un elemento de ITBMS visual, lo ponemos en 0 o lo ocultamos
        if(document.getElementById('itbmsDisplay')) {
            document.getElementById('itbmsDisplay').textContent = 'B/. 0.00';
        }
        
        if(document.getElementById('totalDisplay')) {
            document.getElementById('totalDisplay').textContent = 'B/. ' + total.toFixed(2);
        }
    }

    function updateEmptyMessage() {
        const rows = document.querySelectorAll('.detalle-row');
        const hasRows = rows.length > 0;
        document.getElementById('emptyMessage').style.display = hasRows ? 'none' : 'block';
        document.getElementById('tablaDetalle').style.display = hasRows ? 'table' : 'none';
        
        const sc = document.getElementById('cliente_id');
        if (sc) sc.disabled = hasRows;
    }

    // --- INICIO ---
    document.addEventListener('DOMContentLoaded', function() {
        localStorage.removeItem('factura_temporal');
        attachRowEvents();
        updateEmptyMessage();
        calculateTotals();

        document.getElementById('facturaForm')?.addEventListener('submit', function() {
            const sc = document.getElementById('cliente_id');
            if(sc) sc.disabled = false;
        });

        // --- PREVIEW / ACCIONES DE FINALIZAR FACTURA ---
        const previewBody = document.getElementById('facturaPreviewBody');
        const previewModalEl = document.getElementById('facturaPreviewModal');
        const previewModal = (window.bootstrap && previewModalEl) ? new bootstrap.Modal(previewModalEl) : null;

        function buildPreviewHtml() {
            const rows = document.querySelectorAll('.detalle-row');
            let total = 0;
            let rowsHtml = '';

            rows.forEach(r => {
                const desc = (r.querySelector('input[name*="[descripcion]"]')?.value || '').trim();
                const cantidad = parseFloat(r.querySelector('.cantidad-input')?.value || 0) || 0;
                const unidad = r.querySelector('.unidad-select')?.value || '';
                const precio = parseFloat(r.querySelector('.precio-input')?.value || 0) || 0;
                const subtotal = parseFloat(r.querySelector('.subtotal-input')?.value || 0) || 0;
                total += subtotal;

                rowsHtml += `<tr>
                    <td>${escapeHtml(desc)}</td>
                    <td class="text-end">${cantidad}</td>
                    <td class="text-center">${escapeHtml(unidad)}</td>
                    <td class="text-end">B/. ${precio.toFixed(2)}</td>
                    <td class="text-end">B/. ${subtotal.toFixed(2)}</td>
                </tr>`;
            });

            // Gather client and invoice metadata if present
            const clienteSel = document.getElementById('cliente_id');
            const clienteNombre = clienteSel ? clienteSel.options[clienteSel.selectedIndex]?.textContent || '' : '';
            const clienteRucEl = document.getElementById('clienteRuc');
            const clienteRuc = clienteRucEl ? clienteRucEl.textContent : '';
            const fecha = document.getElementById('fecha') ? document.getElementById('fecha').value : '';
            const numero = document.getElementById('numero') ? document.getElementById('numero').value : '';

            const html = `
                <div class="p-3">
                    <div class="text-center mb-3">
                        <h4 class="fw-bold mb-1">Distribuidora Serrano</h4>
                        <p class="text-secondary mb-0">RUC: 12345678-1-2020</p>
                        <p class="text-secondary mb-0">Ciudad de Panamá, Panamá</p>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Cliente:</strong><br>
                            ${escapeHtml(clienteNombre)}<br>
                            <small class="text-secondary">${escapeHtml(clienteRuc || '')}</small>
                        </div>
                        <div class="col-6 text-end">
                            <strong>Factura N°:</strong> ${escapeHtml(numero || '')}<br>
                            <strong>Fecha:</strong> ${escapeHtml(fecha || '')}
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-center">Unidad</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th class="text-end">B/. ${total.toFixed(2)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>`;

            return html;
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        document.getElementById('btnTerminarFactura')?.addEventListener('click', function() {
            // Validar que cliente esté seleccionado
            const clienteSelect = document.getElementById('cliente_id');
            if (!clienteSelect || !clienteSelect.value || clienteSelect.value === '') {
                showToast('Debe seleccionar un cliente antes de terminar la factura.', 'danger');
                return;
            }

            // Validar que exista al menos un producto
            const rows = document.querySelectorAll('.detalle-row');
            
            if (rows.length === 0) {
                showToast('Debe agregar al menos un producto para terminar la factura.', 'danger');
                return;
            }

            // Validar que todos los productos agregados tengan un producto seleccionado
            let productosNoSeleccionados = [];
            rows.forEach((row, index) => {
                const productoSelect = row.querySelector('.producto-select');
                if (!productoSelect.value || productoSelect.value === '') {
                    productosNoSeleccionados.push(index + 1);
                }
            });

            if (productosNoSeleccionados.length > 0) {
                showToast(`Debe seleccionar un producto para las filas: ${productosNoSeleccionados.join(', ')}`, 'danger');
                return;
            }

            // Si pasó las validaciones, mostrar preview
            const html = buildPreviewHtml();
            if(previewBody) previewBody.innerHTML = html;
            if(previewModal) previewModal.show();
        });

        // helper to submit emission to server, then perform followup action
        function submitEmit(postAction, followup) {
            const form = document.getElementById('facturaForm');
            if (!form) {
                // fallback: perform followup directly
                if (typeof followup === 'function') followup();
                return;
            }

            // IMPORTANTE: Habilitar cliente_id antes de enviar
            const clienteSelect = document.getElementById('cliente_id');
            const wasDisabled = clienteSelect && clienteSelect.disabled;
            if (clienteSelect && wasDisabled) {
                clienteSelect.disabled = false;
            }

            // visual feedback
            const modalFooterBtns = previewModalEl.querySelectorAll('.modal-footer button');
            modalFooterBtns.forEach(b => b.disabled = true);

            const formData = new FormData(form);
            formData.set('action', 'emit');
            formData.set('post_emit_action', postAction || 'none');

            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

            fetch(form.action, {
                method: form.method || 'POST',
                headers: token ? { 'X-CSRF-TOKEN': token } : {},
                body: formData,
            }).then(resp => {
                // consider success if 2xx
                if (resp.ok) {
                    return resp.json().then(data => {
                        if (data.success) {
                            showToast(`Factura #${data.factura_id || ''} emitida correctamente`, 'success');
                            if (typeof followup === 'function') followup();
                            // Optionally redirect or close modal
                            if (previewModal) previewModal.hide();
                            
                            // Recargar la página después de 1.5 segundos para mostrar el toast
                            setTimeout(() => {
                                window.location.href = '{{ route("facturacion.create") }}';
                            }, 1500);
                        } else {
                            showToast(data.message || 'Error al emitir la factura', 'danger');
                            // Re-disable cliente_id if it was disabled
                            if (wasDisabled && clienteSelect) clienteSelect.disabled = true;
                            form.submit();
                        }
                    });
                } else {
                    // Handle error response
                    return resp.json().then(data => {
                        showToast(data.message || 'Error al emitir la factura', 'danger');
                        // Re-disable cliente_id if it was disabled
                        if (wasDisabled && clienteSelect) clienteSelect.disabled = true;
                    }).catch(() => {
                        // Re-disable cliente_id if it was disabled
                        if (wasDisabled && clienteSelect) clienteSelect.disabled = true;
                        form.submit();
                    });
                }
            }).catch(err => {
                console.error(err);
                form.submit();
            }).finally(() => {
                modalFooterBtns.forEach(b => b.disabled = false);
            });
        }

        document.getElementById('btnDescargarImprimir')?.addEventListener('click', function() {
            submitEmit('download', function() {
                generarPdfDelServidor({save: true});
            });
        });

        document.getElementById('btnEnviarCorreo')?.addEventListener('click', function() {
            submitEmit('email', function() {
                generarPdfDelServidor({email: true});
            });
        });

        // --- PDF generation (server-side) ---
        async function generarPdfDelServidor(options = {}) {
            try {
                // Recolectar datos de la factura
                const rows = document.querySelectorAll('.detalle-row');
                let detalles = [];
                let total = 0;

                rows.forEach(r => {
                    const desc = (r.querySelector('input[name*="[descripcion]"]')?.value || '').trim();
                    const cantidad = parseFloat(r.querySelector('.cantidad-input')?.value || 0) || 0;
                    const unidad = r.querySelector('.unidad-select')?.value || '';
                    const precio = parseFloat(r.querySelector('.precio-input')?.value || 0) || 0;
                    const subtotal = parseFloat(r.querySelector('.subtotal-input')?.value || 0) || 0;
                    total += subtotal;

                    if (desc) {
                        detalles.push({
                            descripcion: desc,
                            cantidad: cantidad,
                            unidad: unidad,
                            precio_unitario: precio,
                            subtotal: subtotal
                        });
                    }
                });

                // Datos del cliente
                const clienteSel = document.getElementById('cliente_id');
                const clienteNombre = clienteSel ? clienteSel.options[clienteSel.selectedIndex]?.textContent || '' : '';
                const clienteRucEl = document.getElementById('clienteRuc');
                const clienteRuc = clienteRucEl ? clienteRucEl.textContent : '';
                const fecha = document.getElementById('fecha') ? document.getElementById('fecha').value : new Date().toISOString().split('T')[0];
                const numero = document.getElementById('numero') ? document.getElementById('numero').value : 'TEMP';

                // Enviar al servidor
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch('{{ route("facturacion.generar-pdf") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        cliente_nombre: clienteNombre,
                        cliente_ruc: clienteRuc,
                        numero: numero,
                        fecha: fecha,
                        detalles: detalles,
                        total: total
                    })
                });

                if (!response.ok) {
                    showToast('Error al generar PDF: ' + response.statusText, 'danger');
                    return;
                }

                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                const filename = `factura_${numero}_${fecha}.pdf`;

                if (options.save) {
                    // Descargar el PDF
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                } else if (options.email) {
                    // Descargar y abrir cliente de correo
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    
                    // Abrir cliente de correo
                    const subject = encodeURIComponent('Factura - Emitida');
                    const body = encodeURIComponent('Adjunto encontrará la factura emitida.');
                    window.location.href = `mailto:?subject=${subject}&body=${body}`;
                }

                // Limpiar URL
                setTimeout(() => URL.revokeObjectURL(url), 100);
            } catch (err) {
                console.error('Error generando PDF:', err);
                showToast('Error al generar PDF.', 'danger');
            }
        }
    });
})();
</script>
@endpush