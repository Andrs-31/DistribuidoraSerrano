@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="bi bi-box-seam"></i>
        </div>
        <h1>Distribuidora Serrano</h1>
        <p>Sistema de Facturación</p>
    </div>

    <div class="auth-body">
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="correo@ejemplo.com"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="text-danger mt-1" style="font-size: 0.8125rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Ingrese su contraseña"
                        required
                    >
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger mt-1" style="font-size: 0.8125rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember" style="font-size: 0.875rem;">
                        Recordar sesión
                    </label>
                </div>
                <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size: 0.875rem; color: var(--ds-primary);">
                    ¿Olvidó su contraseña?
                </a>
            </div>

            {{-- CAPTCHA --}}
            <div class="mb-4 text-center">
                {!! NoCaptcha::display() !!}

                @error('g-recaptcha-response')
                    <div class="text-danger mt-2" style="font-size: 0.8125rem;">
                        {{ $message }}
                    </div>
                @enderror
            </div>


            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Iniciar Sesión
            </button>
        </form>
    </div>

    <div class="auth-footer">
        <p class="mb-0">&copy; {{ date('Y') }} Distribuidora Serrano</p>
    </div>
</div>
@endsection

@push('scripts')
{{-- IMPLEMENTACION DE CAPTCHA --}}
{!! NoCaptcha::renderJs() !!}
{{-- ----------------------- --}}
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>
@endpush
