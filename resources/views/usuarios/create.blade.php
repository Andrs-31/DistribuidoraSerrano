@extends('layouts.app')

@section('title', 'Nuevo Usuario')
@section('page-title', 'Nuevo Usuario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Nuevo</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Registrar Nuevo Usuario</h5>
                <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Complete la información del usuario</p>
            </div>

            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    @include('usuarios.partials.form')
                </div>

                <div class="card-footer d-flex justify-content-end gap-2 align-items-center">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                        <span>Cancelar</span>
                    </a>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-check-lg" aria-hidden="true"></i>
                        <span>Crear Usuario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
