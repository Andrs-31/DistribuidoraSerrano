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
    let productos = @json($productos);
    const inputBuscar = document.getElementById('buscarProducto');
    const resultados = document.getElementById('resultadosBusqueda');

    // Función pública para actualizar los productos
    window.actualizarBuscadorProductos = function(nuevosProductos) {
        productos = nuevosProductos;
        inputBuscar.value = '';
        resultados.style.display = 'none';
        console.log('Buscador actualizado con', productos.length, 'productos');
    };

    if (!inputBuscar) return;

    inputBuscar.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();

        if (query.length < 2) {
            resultados.style.display = 'none';
            return;
        }

        const filtrados = productos.filter(p =>
            p.nombre.toLowerCase().includes(query) ||
            (p.codigo && p.codigo.toLowerCase().includes(query))
        ).slice(0, 10);

        if (filtrados.length > 0) {
            resultados.innerHTML = filtrados.map(p => `
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        data-id="${p.id}" data-codigo="${p.codigo}" data-nombre="${p.nombre}"
                        data-unidades='${JSON.stringify(p.unidades)}' 
                        data-precio-default="${p.precio_default}">
                    <div>
                        <div class="fw-medium">${p.nombre}</div>
                        <small class="text-secondary">${p.codigo} | ${p.unidades.map(u => u.unidad).join(', ')}</small>
                    </div>
                    <span class="badge bg-light text-dark">B/. ${parseFloat(p.precio_default).toFixed(2)}</span>
                </button>
            `).join('');
            resultados.style.display = 'block';

            resultados.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    agregarProducto(this);
                });
            });
        } else {
            resultados.innerHTML = '<div class="list-group-item text-center text-secondary">No se encontraron productos</div>';
            resultados.style.display = 'block';
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.buscador-producto')) {
            resultados.style.display = 'none';
        }
    });

    inputBuscar.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            resultados.style.display = 'none';
        }
    });

    function agregarProducto(btn) {
        const unidades = JSON.parse(btn.dataset.unidades || '[]');
        const precioDefault = parseFloat(btn.dataset.precioDefault);

        let emptyRow = document.querySelector('.detalle-row .producto-select[value=""]')?.closest('.detalle-row');

        if (!emptyRow) {
            document.getElementById('btnAgregarProducto').click();
            emptyRow = document.querySelector('.detalle-row:last-child');
        }

        if (emptyRow) {
            const select = emptyRow.querySelector('.producto-select');
            
            // Buscar opción con este ID
            Array.from(select.options).forEach(opt => {
                if (opt.value == btn.dataset.id) {
                    select.value = btn.dataset.id;
                    opt.dataset.unidades = btn.dataset.unidades;
                    opt.dataset.precioDefault = btn.dataset.precioDefault;
                }
            });

            // Llenar select de unidades
            const unidadSelect = emptyRow.querySelector('.unidad-input');
            unidadSelect.innerHTML = '';
            unidades.forEach(u => {
                const opt = document.createElement('option');
                opt.value = u.unidad;
                opt.textContent = u.unidad + ' - B/. ' + parseFloat(u.precio).toFixed(2);
                opt.dataset.precio = u.precio;
                unidadSelect.appendChild(opt);
            });
            
            if (unidades.length > 0) {
                unidadSelect.value = unidades[0].unidad;
            }

            // Disparar change para que se actualice todo
            select.dispatchEvent(new Event('change'));
            emptyRow.querySelector('input[name*="[descripcion]"]').value = btn.dataset.codigo + ' - ' + btn.dataset.nombre;
        }

        inputBuscar.value = '';
        resultados.style.display = 'none';
    }
})();
</script>
@endpush
