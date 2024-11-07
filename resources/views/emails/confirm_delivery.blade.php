<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido en Proceso - LANCETA HG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h4 {
            color: #0056b3;
        }
        .order-details, .product-details {
            margin-bottom: 20px;
        }
        .product {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .product:last-child {
            border-bottom: none;
        }
        .product-name {
            font-weight: bold;
            color: #333;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
        }
        .notice {
            font-size: 12px;
            color: #888;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>LANCETA HG</h1>
        <h2>Tu Pedido Está en Proceso de Envío</h2>
        <p>Hola,</p>
        <p>Tu pedido #{{ $order->id }} ha sido recibido y está en proceso de confirmación. Te notificaremos cuando el pedido esté listo para ser enviado a tu dirección.</p>

        <div class="order-details">
            <h4>Detalles del Pedido</h4>
            <p><strong>ID de Pedido:</strong> {{ $order->id }}</p>
            <p><strong>Total con IVA:</strong> ${{ number_format($order->total_con_iva, 2) }} MXN</p>
        </div>

        <div class="product-details">
            <h4>Productos en el Pedido</h4>
            @foreach ($orderItems as $item)
                <div class="product">
                    <p class="product-name">{{ $item->no_s }} - {{ $item->product_name }} (x{{ $item->quantity }})</p>
                    <p>Precio Total: ${{ number_format($item->total_price, 2) }} MXN</p>
                </div>
            @endforeach
        </div>

        <div class="total">
            <p>Total del Pedido: ${{ number_format($order->total_con_iva, 2) }} MXN</p>
        </div>

        <div class="notice">
            <p>Este es un correo generado automáticamente por el sistema de <strong>LANCETA HG</strong>. Por favor, no responda a este correo.</p>
        </div>
    </div>
</body>
</html>
