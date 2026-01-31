<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre / Razón Social <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('nombre') is-invalid @enderror"
                   id="nombre"
                   name="nombre"
                   value="{{ old('nombre', $cliente->nombre ?? '') }}"
                   placeholder="Ingrese el nombre del cliente"
                   required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="ruc" class="form-label">RUC / Cédula <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('ruc') is-invalid @enderror"
                   id="ruc"
                   name="ruc"
                   value="{{ old('ruc', $cliente->ruc ?? '') }}"
                   placeholder="Ej: 8-123-456 o 155634567-2-2020"
                   required>
            @error('ruc')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   value="{{ old('email', $cliente->email ?? '') }}"
                   placeholder="correo@ejemplo.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
            <input type="tel"
                   class="form-control @error('telefono') is-invalid @enderror"
                   id="telefono"
                   name="telefono"
                   value="{{ old('telefono', $cliente->telefono ?? '') }}"
                   placeholder="+507 6123-4567"
                   required>
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="grupo_id" class="form-label">Grupo de Cliente <span class="text-danger">*</span></label>
            <select class="form-select @error('grupo_id') is-invalid @enderror" id="grupo_id" name="grupo_id" required>
                <option value="">Seleccionar grupo...</option>
                @foreach($grupos ?? [] as $grupo)
                    <option value="{{ $grupo->id }}" {{ old('grupo_id', $cliente->grupo_id ?? '') == $grupo->id ? 'selected' : '' }}>
                        {{ $grupo->nombre }}
                    </option>
                @endforeach
                @if(!isset($grupos) || count($grupos) == 0)
                    <option value="1">Mayoristas</option>
                    <option value="2">Minoristas</option>
                    <option value="3">Especiales</option>
                @endif
            </select>
            @error('grupo_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">El grupo determina la lista de precios aplicable</div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="dv" class="form-label">Dígito Verificador (DV)</label>
            <input type="text"
                   class="form-control @error('dv') is-invalid @enderror"
                   id="dv"
                   name="dv"
                   value="{{ old('dv', $cliente->dv ?? '') }}"
                   placeholder="Ej: 45"
                   maxlength="2">
            @error('dv')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
            <textarea class="form-control @error('direccion') is-invalid @enderror"
                      id="direccion"
                      name="direccion"
                      rows="2"
                      placeholder="Dirección completa del cliente"
                      required>{{ old('direccion', $cliente->direccion ?? '') }}</textarea>
            @error('direccion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="provincia" class="form-label">Provincia</label>
            <select class="form-select @error('provincia') is-invalid @enderror" id="provincia" name="provincia">
                <option value="">Seleccionar provincia...</option>
                <option value="Bocas del Toro" {{ old('provincia', $cliente->provincia ?? '') == 'Bocas del Toro' ? 'selected' : '' }}>Bocas del Toro</option>
                <option value="Chiriquí" {{ old('provincia', $cliente->provincia ?? '') == 'Chiriquí' ? 'selected' : '' }}>Chiriquí</option>
                <option value="Coclé" {{ old('provincia', $cliente->provincia ?? '') == 'Coclé' ? 'selected' : '' }}>Coclé</option>
                <option value="Colón" {{ old('provincia', $cliente->provincia ?? '') == 'Colón' ? 'selected' : '' }}>Colón</option>
                <option value="Darién" {{ old('provincia', $cliente->provincia ?? '') == 'Darién' ? 'selected' : '' }}>Darién</option>
                <option value="Herrera" {{ old('provincia', $cliente->provincia ?? '') == 'Herrera' ? 'selected' : '' }}>Herrera</option>
                <option value="Los Santos" {{ old('provincia', $cliente->provincia ?? '') == 'Los Santos' ? 'selected' : '' }}>Los Santos</option>
                <option value="Panamá" {{ old('provincia', $cliente->provincia ?? '') == 'Panamá' ? 'selected' : '' }}>Panamá</option>
                <option value="Panamá Oeste" {{ old('provincia', $cliente->provincia ?? '') == 'Panamá Oeste' ? 'selected' : '' }}>Panamá Oeste</option>
                <option value="Veraguas" {{ old('provincia', $cliente->provincia ?? '') == 'Veraguas' ? 'selected' : '' }}>Veraguas</option>
            </select>
            @error('provincia')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="distrito" class="form-label">Distrito</label>
            <input type="text"
                   class="form-control @error('distrito') is-invalid @enderror"
                   id="distrito"
                   name="distrito"
                   value="{{ old('distrito', $cliente->distrito ?? '') }}"
                   placeholder="Ingrese el distrito">
            @error('distrito')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="notas" class="form-label">Notas</label>
            <textarea class="form-control @error('notas') is-invalid @enderror"
                      id="notas"
                      name="notas"
                      rows="2"
                      placeholder="Notas adicionales sobre el cliente">{{ old('notas', $cliente->notas ?? '') }}</textarea>
            @error('notas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if(isset($cliente))
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                           {{ old('activo', $cliente->activo ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Cliente Activo</label>
                </div>
            </div>
        </div>
    @endif
</div>
