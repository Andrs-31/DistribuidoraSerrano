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
