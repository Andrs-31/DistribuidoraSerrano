<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistema de Facturación')</title>

    {{-- Bootstrap (puedes cambiar a Tailwind luego) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body>

    {{-- Navbar --}}
    @include('partials.navbar')

    <div class="container-fluid">
        <div class="row">

            {{-- Sidebar --}}
            <div class="col-2 bg-light min-vh-100">
                @include('partials.sidebar')
            </div>

            {{-- Contenido --}}
            <div class="col-10 p-4">
                @include('partials.alerts')

                @yield('content')
            </div>

        </div>
    </div>

    {{-- Footer --}}
    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
