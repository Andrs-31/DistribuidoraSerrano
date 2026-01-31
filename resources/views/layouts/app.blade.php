<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Distribuidora Serrano') - Sistema de Facturación</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --ds-primary: #C41E3A;
            --ds-primary-dark: #A01830;
            --ds-secondary: #1B5E20;
            --ds-secondary-dark: #0D3D12;
            --ds-background: #F8F9FA;
            --ds-surface: #FFFFFF;
            --ds-text-primary: #1A1A1A;
            --ds-text-secondary: #6B7280;
            --ds-border: #E5E7EB;
            --ds-sidebar-width: 260px;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--ds-background);
            color: var(--ds-text-primary);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--ds-sidebar-width);
            height: 100vh;
            background: var(--ds-surface);
            border-right: 1px solid var(--ds-border);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid var(--ds-border);
        }

        .sidebar-brand h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--ds-primary);
            margin: 0;
        }

        .sidebar-brand span {
            font-size: 0.75rem;
            color: var(--ds-text-secondary);
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .sidebar-nav .nav-section {
            padding: 0.5rem 1.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--ds-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--ds-text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--ds-background);
            color: var(--ds-text-primary);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(196, 30, 58, 0.08);
            color: var(--ds-primary);
            border-left-color: var(--ds-primary);
        }

        .sidebar-nav .nav-link i {
            font-size: 1.125rem;
            width: 1.5rem;
            text-align: center;
        }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--ds-border);
        }

        /* Main Content */
        .main-wrapper {
            margin-left: var(--ds-sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-navbar {
            background: var(--ds-surface);
            border-bottom: 1px solid var(--ds-border);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .main-content {
            flex: 1;
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            border: 1px solid var(--ds-border);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: var(--ds-surface);
            border-bottom: 1px solid var(--ds-border);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* Buttons */
        .btn-primary {
            background: var(--ds-primary);
            border-color: var(--ds-primary);
        }

        .btn-primary:hover {
            background: var(--ds-primary-dark);
            border-color: var(--ds-primary-dark);
        }

        .btn-success {
            background: var(--ds-secondary);
            border-color: var(--ds-secondary);
        }

        .btn-success:hover {
            background: var(--ds-secondary-dark);
            border-color: var(--ds-secondary-dark);
        }

        .btn-outline-primary {
            color: var(--ds-primary);
            border-color: var(--ds-primary);
        }

        .btn-outline-primary:hover {
            background: var(--ds-primary);
            border-color: var(--ds-primary);
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--ds-background);
            border-bottom: 2px solid var(--ds-border);
            font-weight: 600;
            font-size: 0.8125rem;
            color: var(--ds-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.025em;
            padding: 0.875rem 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--ds-border);
        }

        /* Form Controls */
        .form-control, .form-select {
            border: 1px solid var(--ds-border);
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--ds-primary);
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.1);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--ds-text-primary);
            margin-bottom: 0.375rem;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.375rem 0.625rem;
            border-radius: 0.375rem;
        }

        .badge-success {
            background: rgba(27, 94, 32, 0.1);
            color: var(--ds-secondary);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #B45309;
        }

        .badge-danger {
            background: rgba(196, 30, 58, 0.1);
            color: var(--ds-primary);
        }

        /* Stats Cards */
        .stat-card {
            background: var(--ds-surface);
            border: 1px solid var(--ds-border);
            border-radius: 0.75rem;
            padding: 1.5rem;
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--ds-text-primary);
        }

        .stat-card .stat-label {
            font-size: 0.875rem;
            color: var(--ds-text-secondary);
        }

        /* Page Header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .page-header .breadcrumb {
            margin: 0;
            padding: 0;
            background: none;
            font-size: 0.8125rem;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('partials.sidebar')

    <div class="main-wrapper">
        @include('partials.navbar')

        <main class="main-content">
            @include('partials.alerts')
            @yield('content')
        </main>

        @include('partials.footer')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CSRF Token para peticiones AJAX
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @stack('scripts')
</body>
</html>
