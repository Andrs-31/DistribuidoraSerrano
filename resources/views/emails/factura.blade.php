<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Estimado/a {{ $factura->cliente->nombre }}</h2>

    <p>
        Adjuntamos la factura N° 
        {{ str_pad($factura->num_factura, 6, '0', STR_PAD_LEFT) }}.
    </p>

    <p>
        Total: <strong>B/. {{ number_format($factura->total, 2) }}</strong>
    </p>

    <br>
    <p>Gracias por su preferencia.</p>

    <strong>Distribuidora Serrano</strong>
</body>
</html>