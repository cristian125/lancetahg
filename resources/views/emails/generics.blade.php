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
        .product-quantity, .product-price {
            color: #666;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
        }
        .shipping-address {
            color: #555;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo h1 {
            font-size: 24px;
            font-weight: bold;
            color: #0056b3;
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
        <!-- Logo de la Empresa -->
        <div class="logo">
            <h1>LANCETA HG</h1>
        </div>

        <!-- Título del Correo -->
        <h2>Pedido en Proceso</h2>
        <p>Hola,</p>
        <p>Tu pedido #{{ $order->id }} ha sido recibido y está siendo procesado. Te enviaremos una confirmación adicional cuando el pedido esté listo para ser recogido o enviado.</p>

        <!-- Detalles del Pedido -->
        <div class="order-details">
            <h4>Detalles del Pedido</h4>
            <p><strong>ID de Pedido:</strong> {{ $order->id }}</p>
            <p><strong>Total con IVA:</strong> ${{ number_format($order->total_con_iva, 2) }} MXN</p>
            <p><strong>Método de Envío:</strong> {{ $order->shipment_method }}</p>
            <p><strong>Costo de Envío:</strong> ${{ number_format($order->shipping_cost, 2) }} MXN</p>
            <p><strong>Fecha de Recogida:</strong> {{ $pickupDate }}</p>
            <p><strong>Hora de Recogida:</strong> {{ $pickupTime }}</p>
        </div>

        <!-- Productos del Pedido -->
        <div class="product-details">
            <h4>Productos en el Pedido</h4>
            @foreach ($orderItems as $item)
                <div class="product">
                    <p class="product-name">{{ $item->no_s }} - {{ $item->product_name }} (x{{ $item->quantity }})</p>
                    <p class="product-quantity">Cantidad: {{ $item->quantity }}</p>
                    <p class="product-price">Precio Unitario: ${{ number_format($item->unit_price, 2) }} MXN</p>
                    <p class="product-price">Precio Total: ${{ number_format($item->total_price, 2) }} MXN</p>
                </div>
            @endforeach
        </div>

        <!-- Dirección de Envío -->
        <div class="shipping-address">
            <h4>Dirección de Envío</h4>
            <p>{{ $order->shipping_address }}</p>
        </div>

        <!-- Total del Pedido -->
        <div class="total">
            Total del Pedido: ${{ number_format($order->total_con_iva, 2) }} MXN
        </div>

        <!-- Aviso de Correo Automático -->
        <div class="notice">
            <p>Este es un correo generado automáticamente por el sistema de <strong>LANCETA HG</strong>. Por favor, no responda a este correo.</p>
        </div>
    </div>
</body>
</html>
