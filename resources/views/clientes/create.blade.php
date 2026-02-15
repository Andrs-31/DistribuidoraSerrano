@extends('layouts.app')

@section('title', 'Nuevo Cliente')
@section('page-title', 'Nuevo Cliente')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active">Nuevo</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Registrar Nuevo Cliente</h5>
                <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Complete la información del cliente</p>
            </div>

            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    @include('clientes.partials.form')
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA DE PROVINCIAS Y DISTRITOS ---
    const provinciaSelect = document.getElementById('provincia');
    const distritoSelect = document.getElementById('distrito');

    provinciaSelect.addEventListener('change', function() {
        const codigoProvincia = this.value;
        if (!codigoProvincia) {
            distritoSelect.disabled = true;
            distritoSelect.innerHTML = '<option value="">Seleccionar provincia primero...</option>';
            return;
        }

        fetch(`/distritos/${codigoProvincia}`)
            .then(response => response.json())
            .then(data => {
                distritoSelect.innerHTML = '<option value="">Seleccionar distrito...</option>';
                data.forEach(distrito => {
                    const option = document.createElement('option');
                    option.value = distrito.codigo_distrito;
                    option.textContent = distrito.nombre_distrito;
                    if (option.value === '{{ old('distrito', '') }}') option.selected = true;
                    distritoSelect.appendChild(option);
                });
                distritoSelect.disabled = false;
            });
    });

    if (provinciaSelect.value) provinciaSelect.dispatchEvent(new Event('change'));

    // --- LÓGICA DE GRUPOS PERSONALIZADOS ---
    const grupoSelect = document.getElementById('grupo_id');
    const customGroupContainer = document.getElementById('custom_group_container'); // Asegúrate de que este ID esté en el partial

    if(grupoSelect) {
        grupoSelect.addEventListener('change', function() {
            if (this.value === 'create_custom') {
                customGroupContainer.classList.remove('d-none');
            } else {
                customGroupContainer.classList.add('d-none');
            }
        });

        // Disparar al cargar por si hay errores de validación
        if (grupoSelect.value === 'create_custom') {
            customGroupContainer.classList.remove('d-none');
        }
    }
});
</script>
@endsection