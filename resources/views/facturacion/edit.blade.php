@extends('layouts.app')

@section('title', 'Editar Factura')
@section('page-title', 'Editar Factura')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('facturacion.index') }}">Facturas</a></li>
    <li class="breadcrumb-item active">{{ $factura->numero ?? 'Editar' }}</li>
@endsection

@section('content')
<form action="{{ route('facturacion.update', $factura ?? 1) }}" method="POST" id="facturaForm">
    @csrf
    @method('PUT')

    @include('facturacion.partials.form')

    <div class="mt-4">
        @include('facturacion.partials.acciones')
    </div>
</form>
@endsection
