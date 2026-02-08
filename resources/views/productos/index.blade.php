@extends('layouts.app')

@section('title', 'Gestión de Productos')
@section('page-title', 'Productos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Productos</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h5 class="mb-1">Catálogo de Productos</h5>
            <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">Administra los productos y sus precios por grupo de cliente</p>
        </div>
        <a href="{{ route('productos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Nuevo Producto
        </a>
    </div>

    <div class="card-body border-bottom">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, código...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="categoria">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias ?? [] as $categoria)
                        <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                    <option value="granos">Granos y Cereales</option>
                    <option value="aceites">Aceites y Grasas</option>
                    <option value="lacteos">Lácteos</option>
                    <option value="bebidas">Bebidas</option>
                    <option value="limpieza">Limpieza</option>
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
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Unidad</th>
                        <th>Precio Base</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos ?? [] as $producto)
                        <tr>
                            <td class="fw-medium">{{ $producto->codigo ?? 'PRD-'.str_pad($producto->id_producto ?? 1, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="fw-medium">{{ $producto->nombre }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">{{ Str::limit($producto->descripcion ?? '', 40) }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $producto->categoria ?? 'General' }}</span></td>
                            <td>{{ $producto->unidad ?? '-' }}</td>
                            <td class="fw-medium">B/. {{ number_format($producto->precio_base ?? 0, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ ($producto->estado ?? $producto->activo ?? false) ? 'success' : 'danger' }}">
                                    {{ ($producto->estado ?? $producto->activo ?? false) ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-light" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-light" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-light text-danger" title="Eliminar"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $producto->id ?? $producto->id_producto }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $producto->id ?? $producto->id_producto }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title">Confirmar eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p class="mb-0">¿Está seguro de que desea desactivar el producto <strong>{{ $producto->nombre }}</strong>?</p>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline">
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
                            <td class="fw-medium">PRD-001</td>
                            <td>
                                <div class="fw-medium">Arroz Calidad Extra</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Arroz de primera calidad, grano largo</div>
                            </td>
                            <td><span class="badge bg-light text-dark">Granos</span></td>
                            <td>Quintal</td>
                            <td class="fw-medium">B/. 45.00</td>
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
                            <td class="fw-medium">PRD-002</td>
                            <td>
                                <div class="fw-medium">Aceite Vegetal 1L</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Aceite vegetal puro, botella de 1 litro</div>
                            </td>
                            <td><span class="badge bg-light text-dark">Aceites</span></td>
                            <td>Caja (12 unid)</td>
                            <td class="fw-medium">B/. 24.00</td>
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
                            <td class="fw-medium">PRD-003</td>
                            <td>
                                <div class="fw-medium">Azúcar Refinada</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Azúcar blanca refinada</div>
                            </td>
                            <td><span class="badge bg-light text-dark">Granos</span></td>
                            <td>Quintal</td>
                            <td class="fw-medium">B/. 38.50</td>
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
                            <td class="fw-medium">PRD-004</td>
                            <td>
                                <div class="fw-medium">Leche Evaporada</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Leche evaporada en lata 400ml</div>
                            </td>
                            <td><span class="badge bg-light text-dark">Lácteos</span></td>
                            <td>Caja (48 unid)</td>
                            <td class="fw-medium">B/. 52.00</td>
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
                            <td class="fw-medium">PRD-005</td>
                            <td>
                                <div class="fw-medium">Frijoles Negros</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">Frijoles negros de primera</div>
                            </td>
                            <td><span class="badge bg-light text-dark">Granos</span></td>
                            <td>Quintal</td>
                            <td class="fw-medium">B/. 65.00</td>
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

    @if(isset($productos) && $productos->hasPages())
        <div class="card-footer">
            {{ $productos->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
