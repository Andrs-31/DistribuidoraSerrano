<header class="main-navbar">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-link d-lg-none p-0 text-secondary" type="button" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list fs-4"></i>
        </button>

        <button class="btn btn-link d-none d-lg-inline-block p-0 text-secondary" type="button" id="toggleSidebar">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="ms-1">
            @hasSection('page-title')
                <h1 class="h5 mb-0 fw-semibold">@yield('page-title')</h1>
            @endif
            @hasSection('breadcrumb')
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 0.8125rem;">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="position-relative d-none d-md-block">
            <input type="text" class="form-control form-control-sm ps-4" placeholder="Buscar..." style="width: 200px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-secondary" style="font-size: 0.875rem;"></i>
        </div>

        <a href="{{ route('facturacion.create') }}" class="btn btn-primary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i>
            <span>Nueva Factura</span>
        </a>

        <div class="dropdown">
            <button class="btn btn-link p-0 text-secondary position-relative" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.625rem;">
                    3
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="width: 300px;">
                <li><h6 class="dropdown-header">Notificaciones</h6></li>
                <li><a class="dropdown-item small py-2" href="#">
                    <div class="fw-medium">Nueva factura creada</div>
                    <div class="text-secondary">Hace 5 minutos</div>
                </a></li>
                <li><a class="dropdown-item small py-2" href="#">
                    <div class="fw-medium">Cliente actualizado</div>
                    <div class="text-secondary">Hace 1 hora</div>
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item small text-center text-primary" href="#">Ver todas</a></li>
            </ul>
        </div>

        <div class="d-none d-lg-block text-end border-start ps-3">
            <div class="fw-medium" style="font-size: 0.875rem;">{{ now()->format('d M, Y') }}</div>
            <div class="text-secondary" style="font-size: 0.75rem;">{{ now()->format('h:i A') }}</div>
        </div>
    </div>
</header>