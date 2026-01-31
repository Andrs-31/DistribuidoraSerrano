@props(['productos' => []])

<div class="buscador-producto">
    <div class="input-group">
        <span class="input-group-text bg-white">
            <i class="bi bi-search"></i>
        </span>
        <input type="text"
               class="form-control"
               id="buscarProducto"
               placeholder="Buscar producto por código o nombre..."
               autocomplete="off">
        <button class="btn btn-primary" type="button" id="btnBuscarProducto">
            Agregar
        </button>
    </div>

    <!-- Search Results Dropdown -->
    <div class="position-relative">
        <div class="list-group position-absolute w-100 shadow-sm"
             id="resultadosBusqueda"
             style="display: none; z-index: 1000; max-height: 300px; overflow-y: auto;">
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const productos = @json($productos);
    const inputBuscar = document.getElementById('buscarProducto');
    const resultados = document.getElementById('resultadosBusqueda');

    inputBuscar.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();

        if (query.length < 2) {
            resultados.style.display = 'none';
            return;
        }

        const filtrados = productos.filter(p =>
            p.nombre.toLowerCase().includes(query) ||
            p.codigo.toLowerCase().includes(query)
        ).slice(0, 10);

        if (filtrados.length > 0) {
            resultados.innerHTML = filtrados.map(p => `
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        data-id="${p.id}" data-codigo="${p.codigo}" data-nombre="${p.nombre}"
                        data-precio="${p.precio_base}" data-unidad="${p.unidad}">
                    <div>
                        <div class="fw-medium">${p.nombre}</div>
                        <small class="text-secondary">${p.codigo} | ${p.unidad}</small>
                    </div>
                    <span class="badge bg-light text-dark">B/. ${parseFloat(p.precio_base).toFixed(2)}</span>
                </button>
            `).join('');
            resultados.style.display = 'block';

            // Add click handlers
            resultados.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', function() {
                    agregarProductoDesdeBoton(this);
                });
            });
        } else {
            resultados.innerHTML = '<div class="list-group-item text-center text-secondary">No se encontraron productos</div>';
            resultados.style.display = 'block';
        }
    });

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.buscador-producto')) {
            resultados.style.display = 'none';
        }
    });

    // Close on escape
    inputBuscar.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            resultados.style.display = 'none';
        }
    });

    function agregarProductoDesdeBoton(btn) {
        const producto = {
            id: btn.dataset.id,
            codigo: btn.dataset.codigo,
            nombre: btn.dataset.nombre,
            precio: btn.dataset.precio,
            unidad: btn.dataset.unidad
        };

        // Find or create empty row
        let emptyRow = document.querySelector('.detalle-row .producto-select[value=""]')?.closest('.detalle-row');

        if (!emptyRow) {
            // Click add button to create new row
            document.getElementById('btnAgregarProducto').click();
            emptyRow = document.querySelector('.detalle-row:last-child');
        }

        if (emptyRow) {
            const select = emptyRow.querySelector('.producto-select');
            select.value = producto.id;
            emptyRow.querySelector('.unidad-input').value = producto.unidad;
            emptyRow.querySelector('.precio-input').value = parseFloat(producto.precio).toFixed(2);
            emptyRow.querySelector('input[name*="[descripcion]"]').value = producto.codigo + ' - ' + producto.nombre;

            // Calculate subtotal
            const cantidad = parseFloat(emptyRow.querySelector('.cantidad-input').value) || 1;
            const precio = parseFloat(producto.precio) || 0;
            emptyRow.querySelector('.subtotal-input').value = (cantidad * precio).toFixed(2);

            // Trigger total recalculation
            if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        }

        // Clear search
        inputBuscar.value = '';
        resultados.style.display = 'none';
    }
})();
</script>
@endpush
