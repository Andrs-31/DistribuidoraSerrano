@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Gestión de Precios: <span class="text-primary">{{ $cliente->nombre }}</span></h5>
            <small class="text-muted">
                Grupo: <strong>{{ $cliente->grupoCliente->nombre ?? 'Sin Grupo' }}</strong> | 
                RUC: {{ $cliente->ruc }}
            </small>
        </div>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">Volver</a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="width: 180px;">Unidad</th>
                        <th style="width: 200px;">Precio</th>
                        <th style="width: 140px;">Estado</th>
                        <th class="text-end" style="width: 150px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $p)
                        @php $formId = "form-prod-{$p->id_producto}"; @endphp
                        <tr id="row-{{ $p->id_producto }}">
                            <td class="fw-medium">
                                {{ $p->nombre }}
                                <div class="text-muted" style="font-size: .75rem;">ID: {{ $p->id_producto }}</div>
                            </td>

                            <td>
                                <select class="form-select form-select-sm sel-unidad"
                                        data-producto="{{ $p->id_producto }}"
                                        name="unidad" required form="{{ $formId }}">
                                    @foreach(['lb','kg','Unidad','Caja','Quintal','Litro','Galón','Saco','Paquete'] as $u)
                                        <option value="{{ $u }}">{{ $u }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">B/.</span>
                                    <input type="number" step="0.01"
                                           class="form-control inp-precio"
                                           name="precio_base"
                                           required form="{{ $formId }}">
                                </div>
                            </td>

                            <td>
                                <select class="form-select form-select-sm sel-estado"
                                        name="estado" required form="{{ $formId }}">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </td>

                            <td class="text-end">
                                <form id="{{ $formId }}" action="{{ route('productos.precios.upsert', $p->id_producto) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_grupo_fk" value="{{ $cliente->id_grupo_fk }}">
                                    
                                    <button type="submit" class="btn btn-sm btn-light btn-save text-success w-100" form="{{ $formId }}">
                                        <i class="bi bi-plus-lg"></i>
                                        <span class="ms-1">Agregar</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const preciosData = @json($precios);

function normalizar(valor){
    return String(valor ?? '').toLowerCase().trim();
}

function resetFila(fila){
    fila.querySelector('.inp-precio').value = "0.00";
    fila.querySelector('.sel-estado').value = "1";
    const btn = fila.querySelector('.btn-save');
    btn.classList.replace('text-primary', 'text-success');
    btn.querySelector('span').innerText = "Agregar";
    btn.querySelector('i').className = "bi bi-plus-lg";
}

function setActualizar(fila, registro){
    fila.querySelector('.inp-precio').value = Number(registro.precio_base || 0).toFixed(2);
    fila.querySelector('.sel-estado').value = String(registro.estado ?? "1");
    const btn = fila.querySelector('.btn-save');
    btn.classList.replace('text-success', 'text-primary');
    btn.querySelector('span').innerText = "Actualizar";
    btn.querySelector('i').className = "bi bi-arrow-repeat";
}

function actualizarFila(productoId){
    const fila = document.querySelector(`#row-${productoId}`);
    if(!fila) return;

    const unidadActual = normalizar(fila.querySelector('.sel-unidad').value);
    resetFila(fila);

    // Buscamos el registro por Producto y Unidad (el grupo ya está filtrado desde el controlador)
    const registro = preciosData.find(p =>
        Number(p.id_producto_fk) === Number(productoId) &&
        normalizar(p.unidad) === unidadActual
    );

    if(registro) setActualizar(fila, registro);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sel-unidad').forEach(select => {
        select.addEventListener('change', () => {
            actualizarFila(select.dataset.producto);
        });
        actualizarFila(select.dataset.producto);
    });
});
</script>
@endsection