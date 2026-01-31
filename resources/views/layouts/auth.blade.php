<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Iniciar Sesión') - Distribuidora Serrano</title>

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
            --ds-background: #F8F9FA;
            --ds-surface: #FFFFFF;
            --ds-text-primary: #1A1A1A;
            --ds-text-secondary: #6B7280;
            --ds-border: #E5E7EB;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--ds-background) 0%, #E5E7EB 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
        }

        .auth-card {
            background: var(--ds-surface);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .auth-header {
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--ds-border);
        }

        .auth-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--ds-primary) 0%, var(--ds-primary-dark) 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .auth-logo i {
            font-size: 2rem;
            color: white;
        }

        .auth-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ds-text-primary);
            margin-bottom: 0.25rem;
        }

        .auth-header p {
            font-size: 0.875rem;
            color: var(--ds-text-secondary);
            margin: 0;
        }

        .auth-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--ds-text-primary);
            margin-bottom: 0.375rem;
        }

        .form-control {
            border: 1px solid var(--ds-border);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--ds-primary);
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.1);
        }

        .input-group-text {
            background: var(--ds-background);
            border: 1px solid var(--ds-border);
            border-right: none;
            color: var(--ds-text-secondary);
        }

        .input-group .form-control {
            border-left: none;
        }

        .btn-primary {
            background: var(--ds-primary);
            border-color: var(--ds-primary);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: var(--ds-primary-dark);
            border-color: var(--ds-primary-dark);
            transform: translateY(-1px);
        }

        .form-check-input:checked {
            background-color: var(--ds-primary);
            border-color: var(--ds-primary);
        }

        .auth-footer {
            padding: 1.5rem 2rem;
            background: var(--ds-background);
            text-align: center;
            font-size: 0.8125rem;
            color: var(--ds-text-secondary);
        }

        .alert {
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .alert-danger {
            background: rgba(196, 30, 58, 0.1);
            border: 1px solid rgba(196, 30, 58, 0.2);
            color: var(--ds-primary);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
