<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Pedido PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
        }
        .container {
            padding: 10px 20px;
            margin: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            padding-bottom: 5px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 12px;
            color: #333;
            margin: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            text-transform: uppercase;
        }
        .order-info, .product-info, .customer-info {
            width: 100%;
            margin-bottom: 10px;
            padding: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            padding: 5px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f1f1f1;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            font-size: 10px;
        }
        .summary-table th, .summary-table td {
            width: 50%;
        }
        .product-info th, .product-info td {
            text-align: center;
        }
        .no-border {
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p class="company-name">LANCETA HG</p>
            <h1>Detalles del Pedido</h1>
            <p><strong>ID de la Orden:</strong> {{ $orden->order_id }}</p>
        </div>

        <div class="customer-info">
            <h3 class="section-title">Información del Cliente</h3>
            <table>
                <tr>
                    <th>Nombre Completo</th>
                    <td>{{ $orden->nombre }} {{ $orden->apellido_paterno }} {{ $orden->apellido_materno }}</td>
                </tr>
                <tr>
                    <th>Teléfono</th>
                    <td>{{ $orden->telefono }}</td>
                </tr>
                <tr>
                    <th>Correo Electrónico</th>
                    <td>{{ $orden->correo }}</td>
                </tr>
                <tr>
                    <th>Fecha de Creación</th>
                    <td>{{ \Carbon\Carbon::parse($orden->order_created_at)->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="order-info">
            <h3 class="section-title">Resumen del Pedido</h3>
            <table class="summary-table">
                <tr>
                    <th>Total del Pedido</th>
                    <td>${{ number_format($orden->total, 2) }}</td>
                </tr>
                <tr>
                    <th>Subtotal sin Envío</th>
                    <td>${{ number_format($orden->subtotal_sin_envio, 2) }}</td>
                </tr>
                <tr>
                    <th>Costo de Envío</th>
                    <td>${{ number_format($orden->shipping_cost, 2) }}</td>
                </tr>
                <tr>
                    <th>Total con IVA</th>
                    <td>${{ number_format($orden->total_con_iva, 2) }}</td>
                </tr>
                <tr>
                    <th>Descuento Total Aplicado</th>
                    <td>-${{ number_format($descuentoTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="product-info">
            <h3 class="section-title">Detalles de los Productos</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID del Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                        <th>Descuento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->product_id }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->total_price, 2) }}</td>
                            <td>${{ number_format(($item->discount / 100) * $item->unit_price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
