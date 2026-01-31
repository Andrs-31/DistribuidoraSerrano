<form action="{{ url()->current() }}" method="GET" class="row g-3">
    <div class="col-md-3">
        <label for="fecha_desde" class="form-label">Fecha Desde</label>
        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
               value="{{ request('fecha_desde', now()->startOfMonth()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-3">
        <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
               value="{{ request('fecha_hasta', now()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-3">
        <label for="cliente_id" class="form-label">Cliente</label>
        <select class="form-select" id="cliente_id" name="cliente_id">
            <option value="">Todos los clientes</option>
            @foreach($clientes ?? [] as $cliente)
                <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                    {{ $cliente->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label for="estado" class="form-label">Estado</label>
        <select class="form-select" id="estado" name="estado">
            <option value="">Todos</option>
            <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagadas</option>
            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
            <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anuladas</option>
        </select>
    </div>

    <div class="col-12">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search me-1"></i>Generar Reporte
            </button>
            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i>Limpiar Filtros
            </a>

            <!-- Quick Date Buttons -->
            <div class="btn-group ms-auto">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">Hoy</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">Esta Semana</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('month')">Este Mes</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('year')">Este Año</button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function setDateRange(range) {
    const today = new Date();
    let fechaDesde, fechaHasta;

    switch(range) {
        case 'today':
            fechaDesde = fechaHasta = formatDate(today);
            break;
        case 'week':
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            fechaDesde = formatDate(startOfWeek);
            fechaHasta = formatDate(today);
            break;
        case 'month':
            fechaDesde = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
            fechaHasta = formatDate(today);
            break;
        case 'year':
            fechaDesde = formatDate(new Date(today.getFullYear(), 0, 1));
            fechaHasta = formatDate(today);
            break;
    }

    document.getElementById('fecha_desde').value = fechaDesde;
    document.getElementById('fecha_hasta').value = fechaHasta;
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
}
</script>
@endpush
