@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Editar Usuario</h5>
                <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Modificando: <span class="fw-medium">{{ $usuario->nombre ?? 'Usuario' }}</span></p>
            </div>

            <form action="{{ route('usuarios.update', $usuario->id ?? 0) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    @include('usuarios.partials.form')
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div>
                        @if(auth()->id() != ($usuario->id ?? 0))
                            <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                                <span>Eliminar Usuario</span>
                            </button>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary d-inline-flex align-items-center gap-2 me-2">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                            <span>Cancelar</span>
                        </a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2"><i class="bi bi-check-lg" aria-hidden="true"></i><span>Guardar Cambios</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
@if(auth()->id() != ($usuario->id ?? 0))
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar a <strong>{{ $usuario->nombre }}</strong>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('usuarios.destroy', $usuario->id ?? 0) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
