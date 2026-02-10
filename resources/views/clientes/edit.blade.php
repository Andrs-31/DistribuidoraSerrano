@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('page-title', 'Editar Cliente')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Editar Cliente</h5>
                <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Modifique la información del cliente: {{ $cliente->nombre ?? 'Cliente' }}</p>
            </div>

            <form action="{{ route('clientes.update', $cliente->ruc) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    @include('clientes.partials.form')
                </div>

                <div class="card-footer d-flex justify-content-between">

                    <div class="d-flex gap-2">
                        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Actualizar Cliente
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rucInput = document.getElementById('ruc');
    if (rucInput) rucInput.setAttribute('readonly', 'readonly');

    const provinciaSelect = document.getElementById('provincia');
    const distritoSelect = document.getElementById('distrito');
    const direccionTextarea = document.getElementById('direccion');

    const provinciaId = @json($provinciaId ?? '');
    const distritoId = @json($distritoId ?? '');
    const distritos = @json($distritos ?? []);

    // Seleccionar provincia guardada
    if (provinciaSelect && provinciaId) {
        provinciaSelect.value = provinciaId;
    }

    // Poblar distritos guardados (los que envió el controlador) y seleccionar el guardado
    if (distritoSelect) {
        if (Array.isArray(distritos) && distritos.length) {
            distritoSelect.innerHTML = '<option value="">Seleccionar distrito...</option>';
            distritos.forEach(function (d) {
                const opt = document.createElement('option');
                opt.value = d.codigo_distrito ?? d.id ?? '';
                opt.textContent = d.nombre_distrito ?? d.nombre ?? '';
                if ((d.codigo_distrito ?? d.id ?? '') == (distritoId ?? '')) opt.selected = true;
                distritoSelect.appendChild(opt);
            });
            distritoSelect.disabled = false;
        } else {
            distritoSelect.innerHTML = '<option value="">Seleccionar provincia primero...</option>';
            distritoSelect.disabled = !provinciaId;
        }
    }

    // Colocar la dirección simple en el textarea (solo la parte de la dirección)
    const direccionSimple = @json($direccionSimple ?? '');
    if (direccionTextarea && direccionSimple) {
        direccionTextarea.value = direccionSimple;
    }

    // Si el usuario cambia provincia, cargar distritos y obligar a seleccionar nuevo distrito
    if (provinciaSelect) {
        provinciaSelect.addEventListener('change', function () {
            const codigoProvincia = this.value;

            if (!codigoProvincia) {
                if (distritoSelect) {
                    distritoSelect.innerHTML = '<option value="">Seleccionar provincia primero...</option>';
                    distritoSelect.disabled = true;
                }
                return;
            }

            // limpiar y habilitar para nueva selección
            if (distritoSelect) {
                distritoSelect.innerHTML = '<option value="">Cargando distritos...</option>';
                distritoSelect.disabled = true;
            }

            fetch('/distritos/' + codigoProvincia)
                .then(function (res) {
                    if (!res.ok) throw new Error('Error al cargar distritos');
                    return res.json();
                })
                .then(function (data) {
                    if (!distritoSelect) return;
                    distritoSelect.innerHTML = '<option value="">Seleccionar distrito...</option>';
                    data.forEach(function (d) {
                        const opt = document.createElement('option');
                        opt.value = d.codigo_distrito ?? d.id ?? '';
                        opt.textContent = d.nombre_distrito ?? d.nombre ?? '';
                        distritoSelect.appendChild(opt);
                    });
                    distritoSelect.disabled = false;
                })
                .catch(function (error) {
                    console.error('Error al cargar distritos:', error);
                    if (!distritoSelect) return;
                    distritoSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
                    distritoSelect.disabled = true;
                });
        });
    }
});
</script>
@endpush

@endsection
