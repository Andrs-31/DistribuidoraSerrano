<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $factura->numero ?? 'FAC-2024-0012' }} - Distribuidora Serrano</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            padding: 20px;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 2px solid #C41E3A;
            margin-bottom: 20px;
        }

        .company-info h1 {
            color: #C41E3A;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #666;
            font-size: 11px;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-info h2 {
            color: #C41E3A;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .invoice-info p {
            margin-bottom: 3px;
        }

        .invoice-info strong {
            color: #333;
        }

        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .party {
            width: 48%;
        }

        .party h3 {
            color: #C41E3A;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        .party p {
            margin-bottom: 3px;
        }

        .party .name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #f8f9fa;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            border-bottom: 2px solid #dee2e6;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .product-name {
            font-weight: 500;
        }

        .product-code {
            color: #999;
            font-size: 10px;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 280px;
        }

        .totals-table td {
            padding: 8px;
            border: none;
        }

        .totals-table .label {
            text-align: right;
            color: #666;
        }

        .totals-table .value {
            text-align: right;
            font-weight: 500;
        }

        .totals-table .total-row td {
            border-top: 2px solid #C41E3A;
            font-size: 16px;
            font-weight: bold;
            color: #C41E3A;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .footer p {
            color: #999;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .observations {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .observations h4 {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-container {
                max-width: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>Distribuidora Serrano</h1>
                <p>RUC: 12345678-1-2020 DV: 45</p>
                <p>Calle Principal, Local 123</p>
                <p>Ciudad de Panamá, Panamá</p>
                <p>Tel: +507 123-4567</p>
            </div>
            <div class="invoice-info">
                <h2>FACTURA</h2>
                <p><strong>N°:</strong> {{ $factura->numero ?? 'FAC-2024-0012' }}</p>
                <p><strong>Fecha:</strong> {{ isset($factura) ? $factura->fecha->format('d/m/Y') : now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Parties -->
        <div class="parties">
            <div class="party">
                <h3>Facturado a</h3>
                <p class="name">{{ $factura->cliente->nombre ?? 'Comercial El Triunfo, S.A.' }}</p>
                <p>RUC: {{ $factura->cliente->ruc ?? '155634567-2-2020' }}</p>
                <p>{{ $factura->cliente->direccion ?? 'Calle 50, Local 12, Ciudad de Panamá' }}</p>
                <p>Tel: {{ $factura->cliente->telefono ?? '+507 6789-0123' }}</p>
            </div>
            <div class="party">
                <h3>Información de Pago</h3>
                <p><strong>Condición:</strong> Contado</p>
                <p><strong>Estado:</strong> {{ ucfirst($factura->estado ?? 'Pagada') }}</p>
            </div>
        </div>

        <!-- Products Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Descripción</th>
                    <th class="text-center" style="width: 10%;">Cant.</th>
                    <th class="text-center" style="width: 10%;">Unidad</th>
                    <th class="text-right" style="width: 15%;">P. Unitario</th>
                    <th class="text-right" style="width: 15%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($factura->detalles ?? [] as $detalle)
                    <tr>
                        <td>
                            <div class="product-name">{{ $detalle->producto->nombre ?? $detalle->descripcion }}</div>
                            <div class="product-code">{{ $detalle->producto->codigo ?? '' }}</div>
                        </td>
                        <td class="text-center">{{ $detalle->cantidad }}</td>
                        <td class="text-center">{{ $detalle->unidad }}</td>
                        <td class="text-right">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="text-right">B/. {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <!-- Demo data -->
                    <tr>
                        <td>
                            <div class="product-name">Arroz Calidad Extra</div>
                            <div class="product-code">PRD-001</div>
                        </td>
                        <td class="text-center">3</td>
                        <td class="text-center">Quintal</td>
                        <td class="text-right">B/. 45.00</td>
                        <td class="text-right">B/. 135.00</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="product-name">Aceite Vegetal 1L</div>
                            <div class="product-code">PRD-002</div>
                        </td>
                        <td class="text-center">2</td>
                        <td class="text-center">Caja (12 unid)</td>
                        <td class="text-right">B/. 24.00</td>
                        <td class="text-right">B/. 48.00</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="product-name">Azúcar Refinada</div>
                            <div class="product-code">PRD-003</div>
                        </td>
                        <td class="text-center">1</td>
                        <td class="text-center">Quintal</td>
                        <td class="text-right">B/. 38.50</td>
                        <td class="text-right">B/. 38.50</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Observations -->
        @if(isset($factura->observaciones) && $factura->observaciones)
            <div class="observations">
                <h4>Observaciones</h4>
                <p>{{ $factura->observaciones }}</p>
            </div>
        @endif

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">B/. {{ number_format($factura->subtotal ?? 221.50, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">ITBMS (7%):</td>
                    <td class="value">B/. {{ number_format($factura->itbms ?? 15.51, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">TOTAL:</td>
                    <td class="value">B/. {{ number_format($factura->total ?? 237.01, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Gracias por su preferencia</p>
            <p>Distribuidora Serrano - Sistema de Facturación</p>
            <p>Este documento es una representación impresa de la factura electrónica</p>
        </div>
    </div>

    <!-- Print Button (hidden when printing) -->
    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; background: #C41E3A; color: white; border: none; border-radius: 4px;">
            Imprimir Factura
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px; margin-left: 10px;">
            Cerrar
        </button>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
