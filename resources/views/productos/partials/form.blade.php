<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('codigo') is-invalid @enderror"
                   id="codigo"
                   name="codigo"
                   value="{{ old('codigo', $producto->codigo ?? '') }}"
                   placeholder="Ej: PRD-001"
                   required>
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-8">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('nombre') is-invalid @enderror"
                   id="nombre"
                   name="nombre"
                   value="{{ old('nombre', $producto->nombre ?? '') }}"
                   placeholder="Ingrese el nombre del producto"
                   required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                      id="descripcion"
                      name="descripcion"
                      rows="2"
                      placeholder="Descripción detallada del producto">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
            <select class="form-select @error('categoria') is-invalid @enderror" id="categoria" name="categoria" required>
                <option value="">Seleccionar categoría...</option>
                @foreach($categorias ?? [] as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categoria', $producto->categoria ?? '') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
                @if(!isset($categorias) || count($categorias) == 0)
                    <option value="granos" {{ old('categoria', $producto->categoria ?? '') == 'granos' ? 'selected' : '' }}>Granos y Cereales</option>
                    <option value="aceites" {{ old('categoria', $producto->categoria ?? '') == 'aceites' ? 'selected' : '' }}>Aceites y Grasas</option>
                    <option value="lacteos" {{ old('categoria', $producto->categoria ?? '') == 'lacteos' ? 'selected' : '' }}>Lácteos</option>
                    <option value="bebidas" {{ old('categoria', $producto->categoria ?? '') == 'bebidas' ? 'selected' : '' }}>Bebidas</option>
                    <option value="limpieza" {{ old('categoria', $producto->categoria ?? '') == 'limpieza' ? 'selected' : '' }}>Limpieza</option>
                    <option value="otros" {{ old('categoria', $producto->categoria ?? '') == 'otros' ? 'selected' : '' }}>Otros</option>
                @endif
            </select>
            @error('categoria')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="unidad" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
            <select class="form-select @error('unidad') is-invalid @enderror" id="unidad" name="unidad" required>
                <option value="">Seleccionar unidad...</option>
                <option value="Unidad" {{ old('unidad', $producto->unidad ?? '') == 'Unidad' ? 'selected' : '' }}>Unidad</option>
                <option value="Caja" {{ old('unidad', $producto->unidad ?? '') == 'Caja' ? 'selected' : '' }}>Caja</option>
                <option value="Quintal" {{ old('unidad', $producto->unidad ?? '') == 'Quintal' ? 'selected' : '' }}>Quintal</option>
                <option value="Libra" {{ old('unidad', $producto->unidad ?? '') == 'Libra' ? 'selected' : '' }}>Libra</option>
                <option value="Kilogramo" {{ old('unidad', $producto->unidad ?? '') == 'Kilogramo' ? 'selected' : '' }}>Kilogramo</option>
                <option value="Galón" {{ old('unidad', $producto->unidad ?? '') == 'Galón' ? 'selected' : '' }}>Galón</option>
                <option value="Litro" {{ old('unidad', $producto->unidad ?? '') == 'Litro' ? 'selected' : '' }}>Litro</option>
                <option value="Saco" {{ old('unidad', $producto->unidad ?? '') == 'Saco' ? 'selected' : '' }}>Saco</option>
                <option value="Paquete" {{ old('unidad', $producto->unidad ?? '') == 'Paquete' ? 'selected' : '' }}>Paquete</option>
            </select>
            @error('unidad')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="precio_base" class="form-label">Precio Base (B/.) <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">B/.</span>
                <input type="number"
                       class="form-control @error('precio_base') is-invalid @enderror"
                       id="precio_base"
                       name="precio_base"
                       value="{{ old('precio_base', $producto->precio_base ?? '') }}"
                       placeholder="0.00"
                       step="0.01"
                       min="0"
                       required>
            </div>
            @error('precio_base')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div class="form-text">Precio de referencia, los precios finales se definen por grupo de cliente</div>
        </div>
    </div>

    @if(isset($producto))
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                           {{ old('activo', $producto->activo ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Producto Activo</label>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Price List Section (for edit mode) -->
@if(isset($producto) && isset($grupos))
<div class="border-top pt-4 mt-3">
    <h6 class="mb-3">Precios por Grupo de Cliente</h6>
    <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Define precios diferenciados para cada grupo de clientes</p>

    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th>Grupo</th>
                    <th style="width: 200px;">Precio (B/.)</th>
                    <th style="width: 150px;">Descuento %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupos as $grupo)
                    @php
                        $precioGrupo = $producto->precios->where('grupo_id', $grupo->id)->first();
                    @endphp
                    <tr>
                        <td class="fw-medium">{{ $grupo->nombre }}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">B/.</span>
                                <input type="number"
                                       class="form-control"
                                       name="precios[{{ $grupo->id }}][precio]"
                                       value="{{ old("precios.{$grupo->id}.precio", $precioGrupo->precio ?? $producto->precio_base) }}"
                                       step="0.01"
                                       min="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number"
                                       class="form-control"
                                       name="precios[{{ $grupo->id }}][descuento]"
                                       value="{{ old("precios.{$grupo->id}.descuento", $precioGrupo->descuento ?? 0) }}"
                                       step="0.1"
                                       min="0"
                                       max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
