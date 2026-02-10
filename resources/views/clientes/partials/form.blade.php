<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre / Razón Social <span class="text-danger"> *</span></label>
            <input type="text"
                   class="form-control @error('nombre') is-invalid @enderror"
                   id="nombre"
                   name="nombre"
                   value="{{ old('nombre', $cliente->nombre ?? '') }}"
                   placeholder="Ingrese el nombre del cliente"
                   onkeypress="validarNombreKeypress(event)"
                   oninput="validarNombreInput(this)"
                   required>
            <div id="nombre-error" class="invalid-feedback" style="display: none;"></div>
            @error('nombre')
                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="ruc" class="form-label">RUC / Cédula <span class="text-danger"> *</span></label>
            <input type="text"
                   class="form-control @error('ruc') is-invalid @enderror"
                   id="ruc"
                   name="ruc"
                   value="{{ old('ruc', $cliente->ruc ?? '') }}"
                   placeholder="Ej: 8-123-456 o 155634567-2-2020"
                   onkeypress="validarRucKeypress(event)"
                   oninput="validarRucInput(this)"
                   required>
            <div id="ruc-error" class="invalid-feedback" style="display: none;"></div>
            @error('ruc')
                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico <span class="text-danger"> *</span></label>
            <input type="text"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   value="{{ old('email', $cliente->email ?? '') }}"
                   placeholder="correo@ejemplo.com"
                   onblur="validarEmailBlur(this)"
                   onfocus="limpiarErrorEmail(this)"
                   required>
            <div id="email-error" class="invalid-feedback" style="display: none;"></div>
            @error('email')
                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono o Celular <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('telefono') is-invalid @enderror"
                   id="telefono"
                   name="telefono"
                   value="{{ old('telefono', $cliente->telefono ?? '') }}"
                   placeholder="+507 6640-7034"
                   onkeypress="validarTelefonoKeypress(event)"
                   oninput="validarTelefonoInput(this)"
                   required>
            <div id="telefono-error" class="invalid-feedback" style="display: none;"></div>
            @error('telefono')
                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="grupo_id" class="form-label">Grupo de Cliente <span class="text-danger">*</span></label>
            <select class="form-select @error('grupo_id') is-invalid @enderror" id="grupo_id" name="grupo_id" required>
                <option value="">Seleccionar grupo...</option>
                @foreach($grupos ?? [] as $grupo)
                    <option value="{{ $grupo->id_grupo }}" {{ old('grupo_id', $cliente->id_grupo_fk ?? '') == $grupo->id_grupo ? 'selected' : '' }}>
                        {{ $grupo->nombre}}
                    </option>
                @endforeach
                <option value="create_new" {{ old('grupo_id') == 'create_new' ? 'selected' : '' }}>
                    + Crear nuevo grupo (usar nombre del cliente)
                </option>
            </select>
            @error('grupo_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">El grupo determina la lista de precios aplicable</div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="estado" class="form-label">Estado de Cliente <span class="text-danger">*</span></label>
            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                <option value="1" {{ old('estado', $cliente->estado ?? '1') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('estado', $cliente->estado ?? '1') == '0' ? 'selected' : '' }}>Inactivo</option>
            </select>

            @error('estado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="provincia" class="form-label">Provincia <span class="text-danger">*</span></label>
            <select class="form-select @error('provincia') is-invalid @enderror" id="provincia" name="provincia" required>
                <option value="">Seleccionar provincia...</option>
                @foreach($provincias ?? [] as $provincia)
                    <option value="{{ $provincia->codigo_provincia }}"
                            {{ old('provincia', $cliente->codigo_provincia ?? '') == $provincia->codigo_provincia ? 'selected' : '' }}>
                        {{ $provincia->nombre_provincia }}
                    </option>
                @endforeach
            </select>
            @error('provincia')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="distrito" class="form-label">Distrito <span class="text-danger">*</span></label>
            <select class="form-select @error('distrito') is-invalid @enderror" id="distrito" name="distrito" disabled required>
                <option value="">Seleccionar provincia primero...</option>
            </select>
            @error('distrito')
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
</div>

<script>
// ===================== NOMBRE / RAZÓN SOCIAL =====================
function validarNombreKeypress(event) {
    const char = event.key;
    const input = event.target;
    const valor = input.value;

    // No puede empezar con espacio
    if (valor === '' && char === ' ') {
        event.preventDefault();
        return;
    }

    const esLetra = /[a-zA-ZáéíóúñÁÉÍÓÚÑ]/.test(char);
    const esNumero = /\d/.test(char);
    const esHash = char === '#';
    const esEspacio = char === ' ';

    // Rechazar si no es letra, número, # o espacio
    if (!esLetra && !esNumero && !esHash && !esEspacio) {
        event.preventDefault();
        return;
    }

    // Máximo 1 número
    if (esNumero && (valor.match(/\d/g) || []).length >= 1) {
        event.preventDefault();
        return;
    }

    // Máximo 1 #
    if (esHash && (valor.match(/#/g) || []).length >= 1) {
        event.preventDefault();
        return;
    }

    // No espacios dobles
    if (esEspacio && valor[valor.length - 1] === ' ') {
        event.preventDefault();
        return;
    }
}

function validarNombreInput(input) {
    let valor = input.value;
    let nuevoValor = '';

    for (let i = 0; i < valor.length; i++) {
        const char = valor[i];
        const esLetra = /[a-zA-ZáéíóúñÁÉÍÓÚÑ]/.test(char);
        const esNumero = /\d/.test(char);
        const esHash = char === '#';
        const esEspacio = char === ' ';

        if (esLetra || esNumero || esHash || esEspacio) {
            if (esNumero && (nuevoValor.match(/\d/g) || []).length >= 1) continue;
            if (esHash && (nuevoValor.match(/#/g) || []).length >= 1) continue;
            if (esEspacio && nuevoValor[nuevoValor.length - 1] === ' ') continue;

            nuevoValor += char;
        }
    }

    if (nuevoValor !== valor) {
        input.value = nuevoValor;
    }
}

// ===================== RUC / CÉDULA =====================
function validarRucKeypress(event) {
    const char = event.key;
    const input = event.target;
    const valor = input.value;

    const esNumero = /\d/.test(char);
    const esGuion = char === '-';

    // Solo números y guiones
    if (!esNumero && !esGuion) {
        event.preventDefault();
        return;
    }

    // No dos guiones seguidos
    if (esGuion && valor[valor.length - 1] === '-') {
        event.preventDefault();
        return;
    }
}

function validarRucInput(input) {
    let valor = input.value;
    let nuevoValor = '';

    for (let i = 0; i < valor.length; i++) {
        const char = valor[i];
        const esNumero = /\d/.test(char);
        const esGuion = char === '-';

        if (esNumero || esGuion) {
            if (esGuion && nuevoValor[nuevoValor.length - 1] === '-') continue;
            nuevoValor += char;
        }
    }

    if (nuevoValor !== valor) {
        input.value = nuevoValor;
    }
}

// ===================== EMAIL =====================
function validarEmailBlur(input) {
    const valor = input.value;
    let mensajeError = '';
    let esValido = true;

    if (valor.trim() === '') {
        limpiarErrorEmail(input);
        return;
    }

    // No debe tener espacios
    if (/ /.test(valor)) {
        mensajeError = 'El correo no puede contener espacios';
        esValido = false;
    }

    // Debe contener @
    if (!valor.includes('@')) {
        mensajeError = 'El correo debe contener un (@)';
        esValido = false;
    }

    // Validar formato básico
    const patronEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (valor && !patronEmail.test(valor)) {
        mensajeError = 'Formato de correo inválido';
        esValido = false;
    }

    if (esValido && !mensajeError) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        const errorElement = document.getElementById('email-error');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        const errorElement = document.getElementById('email-error');
        if (errorElement) {
            errorElement.textContent = mensajeError;
            errorElement.style.display = 'block';
        }
    }
}

function limpiarErrorEmail(input) {
    input.classList.remove('is-invalid');
    const errorElement = document.getElementById('email-error');
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
}

// ===================== TELÉFONO =====================
function validarTelefonoKeypress(event) {
    const char = event.key;
    const input = event.target;
    const valor = input.value;

    const esNumero = /\d/.test(char);
    const esGuion = char === '-';
    const esEspacio = char === ' ';
    const esPlus = char === '+';

    // Rechazar caracteres no válidos
    if (!esNumero && !esGuion && !esEspacio && !esPlus) {
        event.preventDefault();
        return;
    }

    // No dos guiones seguidos
    if (esGuion && valor[valor.length - 1] === '-') {
        event.preventDefault();
        return;
    }

    // Máximo 1 guion
    if (esGuion && (valor.match(/\-/g) || []).length >= 1) {
        event.preventDefault();
        return;
    }

    // El + solo al inicio
    if (esPlus && valor.length > 0) {
        event.preventDefault();
        return;
    }
}

function validarTelefonoInput(input) {
    let valor = input.value;
    let nuevoValor = '';
    let conteoGuiones = 0;

    for (let i = 0; i < valor.length; i++) {
        const char = valor[i];
        const esNumero = /\d/.test(char);
        const esGuion = char === '-';
        const esEspacio = char === ' ';
        const esPlus = char === '+';

        if (esNumero || esGuion || esEspacio) {
            if (esGuion) {
                if (conteoGuiones >= 1) continue;
                if (nuevoValor[nuevoValor.length - 1] === '-') continue;
                conteoGuiones++;
            }
            nuevoValor += char;
        } else if (esPlus && i === 0) {
            nuevoValor += char;
        }
    }

    if (nuevoValor !== valor) {
        input.value = nuevoValor;
    }
}
</script>
