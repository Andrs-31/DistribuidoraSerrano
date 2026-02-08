<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .info-col { flex: 1; }
        .info-col h4 { font-size: 11px; font-weight: bold; margin-bottom: 3px; color: #333; }
        .info-col p { font-size: 11px; margin: 2px 0; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th { background-color: #f5f5f5; border: 1px solid #ddd; padding: 8px; text-align: left; font-weight: bold; font-size: 11px; }
        .table td { border: 1px solid #ddd; padding: 8px; font-size: 11px; }
        .table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { margin-top: 20px; width: 100%; }
        .totals-row { display: flex; justify-content: flex-end; margin: 5px 0; font-size: 12px; }
        .totals-row .label { font-weight: bold; width: 150px; text-align: right; padding-right: 10px; }
        .totals-row .value { width: 100px; text-align: right; }
        .totals-row.total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; padding-top: 5px; margin-top: 10px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $empresa['nombre'] }}</h1>
            <p>RUC: {{ $empresa['ruc'] }}</p>
            <p>{{ $empresa['ciudad'] }}</p>
        </div>

        <!-- Información de la factura -->
        <div class="info-row">
            <div class="info-col">
                <h4>Cliente:</h4>
                <p>{{ $cliente_nombre }}</p>
                @if($cliente_ruc)
                    <p style="font-size: 10px; color: #666;">RUC: {{ $cliente_ruc }}</p>
                @endif
            </div>
            <div class="info-col" style="text-align: right;">
                <h4>Factura #:</h4>
                <p style="font-size: 14px; font-weight: bold;">{{ $numero }}</p>
                <h4>Fecha:</h4>
                <p>{{ $fecha }}</p>
            </div>
        </div>

        <!-- Detalles -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 40%;">Producto</th>
                    <th style="width: 12%; text-align: right;">Cantidad</th>
                    <th style="width: 13%; text-align: center;">Unidad</th>
                    <th style="width: 15%; text-align: right;">Precio Unit.</th>
                    <th style="width: 20%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @if(is_array($detalles) && count($detalles) > 0)
                    @foreach($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle['producto'] ?? $detalle['descripcion'] ?? '-' }}</td>
                            <td class="text-right">{{ $detalle['cantidad'] ?? 0 }}</td>
                            <td class="text-center">{{ $detalle['unidad'] ?? '' }}</td>
                            <td class="text-right">B/. {{ number_format($detalle['precio_unitario'] ?? 0, 2, '.', '') }}</td>
                            <td class="text-right">B/. {{ number_format($detalle['subtotal'] ?? 0, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center" style="color: #999;">Sin detalles</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totals">
            <div class="totals-row">
                <div class="label">Total:</div>
                <div class="value">B/. {{ number_format($total, 2, '.', '') }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Factura emitida {{ now()->format('d/m/Y H:i') }}</p>
            <p>Gracias por su compra</p>
        </div>
    </div>
</body>
</html>
