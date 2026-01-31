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
                        <option value="{{ $grupo->id }}" {{ request('grupo') == $grupo->id ? 'selected' : '' }}>
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
                            <td>{{ $cliente->grupo->nombre ?? 'Sin grupo' }}</td>
                            <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $cliente->activo ? 'success' : 'danger' }}">
                                    {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-light" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-light" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-light text-danger" title="Eliminar"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $cliente->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $cliente->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title">Confirmar eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p class="mb-0">¿Está seguro de que desea desactivar al cliente <strong>{{ $cliente->nombre }}</strong>?</p>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Desactivar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Demo data -->
                        <tr>
                            <td>
                                <div class="fw-medium">Comercial El Triunfo, S.A.</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">eltriunfo@gmail.com</div>
                            </td>
                            <td>155634567-2-2020</td>
                            <td><span class="badge bg-light text-dark">Mayoristas</span></td>
                            <td>+507 6789-0123</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-medium">Mini Super Central</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">msuper@hotmail.com</div>
                            </td>
                            <td>8-456-789</td>
                            <td><span class="badge bg-light text-dark">Minoristas</span></td>
                            <td>+507 6123-4567</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-medium">Abarrotería Don José</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">djose@gmail.com</div>
                            </td>
                            <td>4-234-567</td>
                            <td><span class="badge bg-light text-dark">Minoristas</span></td>
                            <td>+507 6234-5678</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-medium">Super La Economía</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">economia@gmail.com</div>
                            </td>
                            <td>155789012-2-2019</td>
                            <td><span class="badge bg-light text-dark">Mayoristas</span></td>
                            <td>+507 6345-6789</td>
                            <td><span class="badge badge-danger">Inactivo</span></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
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
@endsection
