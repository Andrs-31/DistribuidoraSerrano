{{-- Formulario de Usuario - Partial (Bootstrap) --}}
@php
    $isEdit = isset($usuario) && $usuario->id;
@endphp

<div class="mb-4">
    <h6 class="mb-3">Información Personal</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $usuario->nombre ?? '') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ingrese el nombre completo" required>
            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email', $usuario->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" placeholder="correo@ejemplo.com" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" name="telefono" id="telefono" value="{{ old('telefono', $usuario->telefono ?? '') }}" class="form-control @error('telefono') is-invalid @enderror" placeholder="+51 999 999 999">
            @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="dni" class="form-label">DNI</label>
            <input type="text" name="dni" id="dni" value="{{ old('dni', $usuario->dni ?? '') }}" class="form-control @error('dni') is-invalid @enderror" placeholder="12345678" maxlength="8">
            @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="mb-3">Credenciales de acceso</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="password" class="form-label">Contraseña {!! !$isEdit ? '<span class="text-danger">*</span>' : '' !!}</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ $isEdit ? 'Dejar vacío para mantener actual' : 'Mínimo 8 caracteres' }}" {{ $isEdit ? '' : 'required' }}>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')" aria-label="Mostrar/ocultar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            @if($isEdit)
                <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
            @endif
        </div>

        <div class="col-md-6">
            <label for="password_confirmation" class="form-label">Confirmar contraseña {!! !$isEdit ? '<span class="text-danger">*</span>' : '' !!}</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repita la contraseña" {{ $isEdit ? '' : 'required' }}>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')" aria-label="Mostrar/ocultar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="mb-3">Rol y estado</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="rol" class="form-label">Rol de usuario <span class="text-danger">*</span></label>
            <select name="rol" id="rol" class="form-select @error('rol') is-invalid @enderror" required>
                <option value="">Seleccionar rol</option>
                <option value="admin" {{ old('rol', $usuario->rol ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                <option value="vendedor" {{ old('rol', $usuario->rol ?? '') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                <option value="cajero" {{ old('rol', $usuario->rol ?? '') == 'cajero' ? 'selected' : '' }}>Cajero</option>
                <option value="almacen" {{ old('rol', $usuario->rol ?? '') == 'almacen' ? 'selected' : '' }}>Almacén</option>
            </select>
            @error('rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                <option value="activo" {{ old('estado', $usuario->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $usuario->estado ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mt-3 p-3 bg-light rounded">
        <small class="text-muted">Permisos por rol: <strong>Admin</strong> (acceso total), <strong>Vendedor</strong> (crear facturas, gestionar clientes), <strong>Cajero</strong> (procesar pagos), <strong>Almacén</strong> (gestionar inventario).</small>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush
