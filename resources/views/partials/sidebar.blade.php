<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h1>Distribuidora Serrano</h1>
        <span>Sistema de Facturación</span>
    </div>

    @php
        $usuario = auth()->user();
        $permiso = optional(optional($usuario)->permiso)->nombre ?? null;
    @endphp

    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>

        @auth
        <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-section mt-3">Facturación</div>

        @if($permiso === 'admin' || in_array($permiso, ['facturacion','gestion_facturacion']))
        <a href="{{ route('facturacion.create') }}" class="nav-link {{ request()->routeIs('facturacion.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i>
            <span>Nueva Factura</span>
        </a>

        <a href="{{ route('facturacion.index') }}" class="nav-link {{ request()->routeIs('facturacion.index') || request()->routeIs('facturacion.show') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            <span>Historial de Facturas</span>
        </a>
        @endif

        <div class="nav-section mt-3">Gestión</div>

        @if($permiso === 'admin' || in_array($permiso, ['clientes','gestion_clientes']))
        <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Clientes</span>
        </a>
        @endif

        @if($permiso === 'admin' || in_array($permiso, ['productos','gestion_productos']))
        <a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span>Productos</span>
        </a>
        @endif

        @if($permiso === 'admin' || in_array($permiso, ['usuarios','gestion_usuarios']))
        <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span>Usuarios</span>
        </a>
        @endif

        <div class="nav-section mt-3">Reportes</div>

        @if($permiso === 'admin' || in_array($permiso, ['reportes','gestion_reportes']))
        <a href="{{ route('reportes.ventas') }}" class="nav-link {{ request()->routeIs('reportes.ventas') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i>
            <span>Reporte de Ventas</span>
        </a>

        <a href="{{ route('reportes.facturas') }}" class="nav-link {{ request()->routeIs('reportes.facturas') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>Reporte de Facturas</span>
        </a>
        @endif
        @endauth
    </nav>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-person text-secondary"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-medium" style="font-size: 0.875rem;">{{ $usuario->nombre ?? $usuario->user ?? 'Usuario' }}</div>
                <div class="text-secondary" style="font-size: 0.75rem;">{{ $permiso ?? '—' }}</div>
            </div>
            @auth
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link p-0 text-secondary" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
            @endauth
        </div>
    </div>
</aside>
