@extends('layouts.app')

@section('title', 'Gestión de Clientes')
@section('page-title', 'Clientes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Clientes</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h5 class="mb-1">Listado de Clientes</h5>
            <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Administra la información de los clientes de la distribuidora</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Nuevo Cliente
        </a>
    </div>

    <div class="card-body border-bottom">
        <form action="{{ route('clientes.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, RUC...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="grupo">
                    <option value="">Todos los grupos</option>
                        @foreach($grupos ?? [] as $grupo)
                            <option value="{{ $grupo->id_grupo }}" {{ request('grupo') == $grupo->id_grupo ? 'selected' : '' }}>
                                {{ $grupo->nombre }}
                            </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="estado">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>RUC / Cédula</th>
                        <th>Grupo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes ?? [] as $cliente)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $cliente->nombre }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">{{ $cliente->email }}</div>
                            </td>
                            <td>{{ $cliente->ruc ?? 'N/A' }}</td>
                            <td>{{ $cliente->grupoCliente->nombre ?? 'Sin grupo' }}</td>
                            <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $cliente->estado == 1 ? 'success' : 'danger' }}">
                                    {{ $cliente->estado == 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('clientes.show', $cliente->ruc) }}" class="btn btn-sm btn-light" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente->ruc) }}" class="btn btn-sm btn-light" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($cliente->estado == 1)
                                        <button type="button" class="btn btn-sm btn-light text-warning" title="Desactivar"
                                                data-bs-toggle="modal" data-bs-target="#toggleModal{{ $cliente->ruc }}">
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light text-success" title="Activar"
                                                data-bs-toggle="modal" data-bs-target="#toggleModal{{ $cliente->ruc }}">
                                            <i class="bi bi-person-plus"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Toggle Modal (activar/desactivar) -->
                                <div class="modal fade" id="toggleModal{{ $cliente->ruc }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title">{{ $cliente->estado == 1 ? 'Confirmar desactivación' : 'Confirmar activación' }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p class="mb-0">{!! $cliente->estado == 1 ? "¿Está seguro de que desea desactivar al cliente <strong>{$cliente->nombre}</strong>?" : "¿Está seguro de que desea activar al cliente <strong>{$cliente->nombre}</strong>?" !!}</p>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('clientes.destroy', $cliente->ruc) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn {{ $cliente->estado == 1 ? 'btn-warning' : 'btn-success' }}">
                                                        {{ $cliente->estado == 1 ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary">No hay clientes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($clientes) && $clientes->hasPages())
        <div class="card-footer">
            {{ $clientes->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- QUE SE HAGA EL SIDEBAR DINAMICO CUANDO INGRESO A ESA SECCION --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const wrapper = document.querySelector('.main-wrapper');

        if (sidebar && wrapper) {
            sidebar.classList.add('collapsed');
            wrapper.classList.add('expanded');
        }
    });
</script>
@endpush

@endsection
