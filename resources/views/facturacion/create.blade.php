@extends('layouts.app')

@section('title', 'Nueva Factura')
@section('page-title', 'Nueva Factura')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('facturacion.index') }}">Facturas</a></li>
    <li class="breadcrumb-item active">Nueva</li>
@endsection

@section('content')
<form action="{{ route('facturacion.store') }}" method="POST" id="facturaForm">
    @csrf

    @include('facturacion.partials.form')

    <div class="mt-4">
        @include('facturacion.partials.acciones')
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Colapsar el sidebar y expandir el área de trabajo para la tabla
        const sidebar = document.querySelector('.sidebar');
        const wrapper = document.querySelector('.main-wrapper');
        
        if (sidebar && wrapper) {
            sidebar.classList.add('collapsed');
            wrapper.classList.add('expanded');
        }

        // 2. Ocultar el buscador (Search) de la navbar
        // Usamos el selector que apunta al contenedor del input y la lupa
        const searchContainer = document.querySelector('.main-navbar .position-relative.d-none.d-md-block');
        if (searchContainer) {
            searchContainer.style.setProperty('display', 'none', 'important');
        }

        // NOTA: No tocamos el contenedor de Date/Time (.d-none.d-lg-block.text-end)
        // para que permanezca visible como solicitaste.
    });
</script>
@endpush
