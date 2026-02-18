@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inicio</li>
@endsection

@section('content')
@php
    use Illuminate\Support\Facades\DB;

    $usuario = auth()->user();

    // Permiso desde BD
    $permiso = null;
    if ($usuario && isset($usuario->permiso_fk)) {
        $permiso = DB::table('permisos')
            ->where('id_permiso', $usuario->permiso_fk)
            ->value('nombre');
    }

    $can = function(array $allowed) use ($permiso) {
        return $permiso === 'admin' || in_array($permiso, $allowed);
    };

    // Permisos
    $canFacturar = $can(['facturacion']);
    $canClientes = $can(['gestion_clientes']);
    $canProductos = $can(['gestion_productos']);
    $canUsuarios  = $can(['gestion_usuarios']);
    $canReportes  = $can(['reportes']);

    // Texto de bienvenida
    $bienvenida = match ($permiso) {
        'admin' => 'Acceso total al sistema.',
        'facturacion' => 'Puedes crear, consultar e imprimir facturas.',
        'gestion_clientes' => 'Puedes registrar, editar y desactivar clientes.',
        'gestion_productos' => 'Puedes gestionar productos y administrar precios.',
        'gestion_usuarios' => 'Puedes administrar usuarios del sistema.',
        'reportes' => 'Puedes consultar reportes y estadísticas.',
        default => 'Acceso limitado según permiso asignado.',
    };

    // Título del HERO (dinámico)
    $heroTitle = match ($permiso) {
        'admin' => 'Panel de control',
        'facturacion' => 'Panel de facturación',
        'gestion_clientes' => 'Gestión de clientes',
        'gestion_productos' => 'Gestión de productos',
        'gestion_usuarios' => 'Administración de usuarios',
        'reportes' => 'Resumen ejecutivo',
        default => 'Panel principal',
    };

    // CTA principal (solo 1)
    $cta = null;
    if ($canFacturar) {
        $cta = ['label' => 'Crear factura', 'route' => 'facturacion.create', 'icon' => 'bi bi-plus-lg'];
    } elseif ($canProductos) {
        $cta = ['label' => 'Gestionar catálogo', 'route' => 'productos.index', 'icon' => 'bi bi-box-seam'];
    } elseif ($canClientes) {
        $cta = ['label' => 'Gestionar clientes', 'route' => 'clientes.index', 'icon' => 'bi bi-people'];
    } elseif ($canReportes) {
        $cta = ['label' => 'Ver reportes', 'route' => 'reportes.ventas', 'icon' => 'bi bi-bar-chart'];
    } elseif ($canUsuarios) {
        $cta = ['label' => 'Administrar usuarios', 'route' => 'usuarios.index', 'icon' => 'bi bi-person-circle'];
    }

    // KPIs (demo / placeholders)
    $totalFacturasHoy = $totalFacturasHoy ?? 12;
    $ventasHoy = $ventasHoy ?? 1250.50;
    $totalClientes = $totalClientes ?? 48;
    $totalProductos = $totalProductos ?? 156;

    // Extra KPIs (si estás en productos)
    $totalCategorias = $totalCategorias ?? 12;
    $productosBajoStock = $productosBajoStock ?? 7;

    // Acciones rápidas (sin duplicados, máx 4)
    $actions = [];

    if ($cta) {
        $actions[] = ['title' => $cta['label'], 'route' => $cta['route'], 'icon' => $cta['icon']];
    }

    $pushAction = function($title, $route, $icon) use (&$actions) {
        foreach ($actions as $a) if ($a['route'] === $route) return;
        $actions[] = compact('title','route','icon');
    };

    if ($canFacturar) $pushAction('Historial facturas', 'facturacion.index', 'bi bi-receipt');
    if ($canClientes) $pushAction('Clientes', 'clientes.index', 'bi bi-people');
    if ($canProductos) $pushAction('Productos', 'productos.index', 'bi bi-box-seam');
    if ($canReportes) $pushAction('Reporte ventas', 'reportes.ventas', 'bi bi-graph-up');
    if ($canUsuarios) $pushAction('Usuarios', 'usuarios.index', 'bi bi-person-circle');

    $actions = array_slice($actions, 0, 4);

    $rolBonito = $usuario->nombre ?? $usuario->name ?? 'Usuario';
@endphp

<style>
  .card { border: 1px solid rgba(0,0,0,.06); border-radius: 14px; }
  .card-header { background: transparent; border-bottom: 1px solid rgba(0,0,0,.06); padding: .9rem 1rem; }
  .btn { border-radius: 10px; }
  .table thead th { font-size:.78rem; text-transform: uppercase; letter-spacing:.04em; color:#6c757d; }
  .table td { padding: .85rem 1rem; }

  /* HERO */
  .ds-hero {
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 16px;
    background: linear-gradient(180deg, rgba(0,0,0,.02), rgba(0,0,0,0));
  }
  /* ✅ Título más elegante */
  .ds-hero-title {
    font-size: 1.6rem;   /* antes ~2rem */
    font-weight: 800;
    letter-spacing: -.35px;
    margin-bottom: .35rem;
  }

  /* ✅ Línea divisoria fina */
  .ds-divider {
    border-top: 1px solid rgba(0,0,0,.05);
    margin: 14px 0 18px;
  }

  /* KPI */
  .kpi-card .label { font-size:.82rem; color:#6c757d; }
  .kpi-card .value { font-size: 1.65rem; font-weight: 800; letter-spacing: -.2px; }
  .kpi-icon {
    width: 52px; height: 52px;
    border-radius: 999px;
    background: rgba(0,0,0,.03);
    display:flex; align-items:center; justify-content:center;
  }

  /* ✅ Hover sutil en tarjetas KPI */
  .kpi-hover {
    transition: transform .12s ease, box-shadow .12s ease;
  }
  .kpi-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0,0,0,.06);
  }
</style>

{{-- HERO --}}
<div class="ds-hero p-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="ds-hero-title">{{ $heroTitle }}</h2>

            <div class="text-secondary" style="font-size:.95rem;">
                <strong>{{ $rolBonito }}</strong>
                <span class="mx-1">•</span>
                <span>{{ $bienvenida }}</span>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                <span class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i>Sesión activa
                </span>
                <span class="badge bg-light text-secondary">
                    <i class="bi bi-calendar3 me-1"></i>{{ now()->format('d/m/Y') }}
                </span>
            </div>
        </div>

        @if($cta)
            <a href="{{ route($cta['route']) }}"
               class="btn btn-primary d-inline-flex align-items-center gap-2 px-3">
                <i class="{{ $cta['icon'] }}"></i>
                <span>{{ $cta['label'] }}</span>
            </a>
        @endif
    </div>
</div>

{{-- Divisor --}}
<div class="ds-divider"></div>

{{-- KPIs --}}
<div class="row g-3 mb-4">

    @if($canFacturar)
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card kpi-hover">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">Facturas hoy</div>
                        <div class="value">{{ $totalFacturasHoy }}</div>
                        <div class="label">Generadas</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="bi bi-receipt fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card kpi-hover">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">Ventas del día</div>
                        <div class="value">B/. {{ number_format($ventasHoy, 2) }}</div>
                        <div class="label">Total</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="bi bi-currency-dollar fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($canClientes)
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card kpi-hover">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">Clientes activos</div>
                        <div class="value">{{ $totalClientes }}</div>
                        <div class="label">Registrados</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="bi bi-people fs-4 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($canProductos)
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card kpi-hover">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">Productos</div>
                        <div class="value">{{ $totalProductos }}</div>
                        <div class="label">En catálogo</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="bi bi-box-seam fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

    @endif

</div>

<div class="row g-4">
    {{-- Principal --}}
    <div class="col-lg-8">
        @if($canFacturar)
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Facturas recientes</h5>
                    <a href="{{ route('facturacion.index') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th class="text-end">Total</th>
                                    <th>Estado</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facturasRecientes ?? [] as $factura)
                                    <tr>
                                        <td class="fw-semibold">{{ $factura->numero }}</td>
                                        <td>{{ $factura->cliente->nombre ?? 'N/A' }}</td>
                                        <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                                        <td class="fw-semibold text-end">B/. {{ number_format($factura->total, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $factura->estado === 'pagada' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ ucfirst($factura->estado) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('facturacion.show', $factura) }}" class="btn btn-sm btn-light">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="text-center py-5 text-secondary">
                                                <i class="bi bi-receipt fs-2 mb-2 d-block opacity-50"></i>
                                                <div class="fw-semibold text-dark">No hay facturas recientes</div>
                                                <div style="font-size:.9rem;">Cuando registres una factura, aparecerá aquí.</div>
                                                <a href="{{ route('facturacion.create') }}" class="btn btn-outline-secondary btn-sm mt-3">
                                                    <i class="bi bi-plus-lg me-1"></i>Crear factura
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px;">
                            <i class="bi bi-lock text-secondary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold mb-1">Módulo no disponible</div>
                            <div class="text-secondary" style="font-size:.9rem;">
                                No tienes acceso a <strong>Facturación</strong>. 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Lateral --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Acciones rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @forelse($actions as $a)
                        <a href="{{ route($a['route']) }}"
                           class="btn btn-outline-secondary d-flex align-items-center justify-content-between">
                            <span><i class="{{ $a['icon'] }} me-2"></i>{{ $a['title'] }}</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    @empty
                        <div class="text-secondary text-center py-2">No hay acciones disponibles.</div>
                    @endforelse
                </div>
            </div>
        </div>

        @if($canProductos)
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Top productos</h5>
                    <span class="text-secondary" style="font-size: 0.75rem;">Este mes</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topProductos ?? [] as $index => $producto)
                            <li class="list-group-item d-flex align-items-center gap-3 py-3">
                                <span class="badge bg-light text-dark fw-bold" style="min-width: 24px;">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $producto->nombre }}</div>
                                    <div class="text-secondary" style="font-size: 0.75rem;">{{ $producto->unidad }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">{{ $producto->cantidad_vendida }}</div>
                                    <div class="text-secondary" style="font-size: 0.75rem;">vendidos</div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-secondary py-3 text-center">No hay datos para mostrar.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
