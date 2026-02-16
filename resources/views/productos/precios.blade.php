@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Precios por Cliente/Grupo</h5>
            <small class="text-muted">Producto: <strong>{{ $producto->nombre }}</strong></small>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">Volver</a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Cliente/Grupo</th>
                        <th style="width: 180px;">Unidad</th>
                        <th style="width: 200px;">Precio</th>
                        <th style="width: 140px;">Estado</th>
                        <th class="text-end" style="width: 200px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupos as $g)
                        @php $formId = "form-precio-{$g->id_grupo}"; @endphp
                        <tr id="row-{{ $g->id_grupo }}">
                            <td class="fw-medium">{{ $g->nombre }}</td>

                            <td>
                                <select class="form-select form-select-sm sel-unidad"
                                        data-grupo="{{ $g->id_grupo }}"
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
                                <form id="{{ $formId }}" action="{{ route('productos.precios.upsert', $producto->id_producto) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_grupo_fk" value="{{ $g->id_grupo }}">
                                    
                                    <div class="btn-group w-100">
                                        <button type="submit" class="btn btn-sm btn-light btn-save text-success" title="Guardar Precio">
                                            <i class="bi bi-plus-lg"></i>
                                            <span class="ms-1">Agregar</span>
                                        </button>

                                        <a href="{{ route('clientes.index', ['grupo' => $g->id_grupo]) }}"
                                           class="btn btn-sm btn-light text-info"
                                           title="Ver clientes de este grupo">
                                            <i class="bi bi-people-fill"></i>
                                        </a>
                                    </div>
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

function actualizarFila(grupoId){
    const fila = document.querySelector(`#row-${grupoId}`);
    if(!fila) return;

    const unidadActual = normalizar(fila.querySelector('.sel-unidad').value);
    
    resetFila(fila);

    // Buscar si ya existe un precio para este grupo y unidad
    const registro = preciosData.find(p =>
        Number(p.id_grupo_fk) === Number(grupoId) &&
        normalizar(p.unidad) === unidadActual
    );

    if(registro) {
        setActualizar(fila, registro);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sel-unidad').forEach(select => {
        select.addEventListener('change', () => {
            actualizarFila(select.dataset.grupo);
        });
        // Inicializar estado de cada fila al cargar
        actualizarFila(select.dataset.grupo);
    });
});
</script>
@endsection