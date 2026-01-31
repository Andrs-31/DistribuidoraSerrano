<div class="row">
    <div class="col-lg-8">
        <!-- Header Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Datos Generales</h6>
            </div>
            <div class="card-body">
                @include('facturacion.partials.encabezado')
            </div>
        </div>

        <!-- Detail Section -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Productos</h6>
            </div>
            <div class="card-body">
                @include('facturacion.partials.detalle')
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Totals Section -->
        @include('facturacion.partials.totales')
    </div>
</div>
