<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h1>Distribuidora Serrano</h1>
        <span>Sistema de Facturación</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>

        <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-section mt-3">Facturación</div>

        <a href="{{ route('facturacion.create') }}" class="nav-link {{ request()->routeIs('facturacion.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i>
            <span>Nueva Factura</span>
        </a>

        <a href="{{ route('facturacion.index') }}" class="nav-link {{ request()->routeIs('facturacion.index') || request()->routeIs('facturacion.show') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            <span>Historial de Facturas</span>
        </a>

        <div class="nav-section mt-3">Gestión</div>

        <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Clientes</span>
        </a>

        <a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span>Productos</span>
        </a>

        <div class="nav-section mt-3">Reportes</div>

        <a href="{{ route('reportes.ventas') }}" class="nav-link {{ request()->routeIs('reportes.ventas') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i>
            <span>Reporte de Ventas</span>
        </a>

        <a href="{{ route('reportes.facturas') }}" class="nav-link {{ request()->routeIs('reportes.facturas') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>Reporte de Facturas</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-person text-secondary"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-medium" style="font-size: 0.875rem;">{{ auth()->user()->name ?? 'Usuario' }}</div>
                <div class="text-secondary" style="font-size: 0.75rem;">{{ auth()->user()->role ?? 'Vendedor' }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link p-0 text-secondary" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
