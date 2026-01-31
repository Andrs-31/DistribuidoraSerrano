@extends('layouts.app')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3">
        <div>
            <h5 class="mb-1">Gestión de Usuarios</h5>
            <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Administra los usuarios del sistema</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Nuevo Usuario
        </a>
    </div>

    <div class="card-body border-bottom">
        <form action="{{ route('usuarios.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o email...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="rol">
                    <option value="">Todos los roles</option>
                    <option value="admin" {{ request('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                    <option value="vendedor" {{ request('rol') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                    <option value="cajero" {{ request('rol') == 'cajero' ? 'selected' : '' }}>Cajero</option>
                    <option value="almacen" {{ request('rol') == 'almacen' ? 'selected' : '' }}>Almacén</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="estado">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded-3 p-2">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $totalUsuarios ?? 0 }}</div>
                            <div class="text-secondary small">Total usuarios</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-3 p-2">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $usuariosActivos ?? 0 }}</div>
                            <div class="text-secondary small">Activos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning text-dark rounded-3 p-2">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $administradores ?? 0 }}</div>
                            <div class="text-secondary small">Administradores</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-secondary text-white rounded-3 p-2">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $usuariosInactivos ?? 0 }}</div>
                            <div class="text-secondary small">Inactivos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Creado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios ?? [] as $usuario)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $usuario->nombre ?? 'N/A' }}</div>
                                <div class="text-secondary small">{{ $usuario->email ?? 'N/A' }}</div>
                            </td>
                            <td>
                                @php
                                    $labels = ['admin' => 'Administrador', 'vendedor' => 'Vendedor', 'cajero' => 'Cajero', 'almacen' => 'Almacén'];
                                @endphp
                                <span class="badge bg-light text-dark">{{ $labels[$usuario->rol ?? 'vendedor'] ?? ucfirst($usuario->rol ?? '') }}</span>
                            </td>
                            <td>
                                <span class="badge {{ ($usuario->estado ?? 'activo') == 'activo' ? 'bg-success' : 'bg-secondary' }}">{{ ($usuario->estado ?? 'activo') == 'activo' ? 'Activo' : 'Inactivo' }}</span>
                            </td>
                            <td class="text-nowrap">{{ $usuario->ultimo_acceso ? $usuario->ultimo_acceso->diffForHumans() : 'Nunca' }}</td>
                            <td>{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y') : 'N/A' }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('usuarios.show', $usuario->id ?? 0) }}" class="btn btn-sm btn-light" title="Ver"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('usuarios.edit', $usuario->id ?? 0) }}" class="btn btn-sm btn-light" title="Editar"><i class="bi bi-pencil"></i></a>
                                    @if(auth()->id() != ($usuario->id ?? 0))
                                        <button class="btn btn-sm btn-light text-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $usuario->id }}" title="Eliminar"><i class="bi bi-trash"></i></button>
                                    @endif
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteUserModal{{ $usuario->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p class="mb-0">¿Está seguro de que desea eliminar al usuario <strong>{{ $usuario->nombre }}</strong>?</p>
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-secondary">No hay usuarios que mostrar. Crea un nuevo usuario para comenzar.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($usuarios) && $usuarios->hasPages())
            <div class="mt-3">
                {{ $usuarios->withQueryString()->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
