<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido - LANCETA HG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        h2 {
            color: #007bff;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-container {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }
        .order-header {
            background-color: #f8f9fa;
            padding: 10px;
        }
        .order-items {
            margin-top: 10px;
        }
        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-items th, .order-items td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .order-items th {
            background-color: #f1f1f1;
        }
        .order-summary {
            margin-top: 15px;
            text-align: right;
        }
        .note {
            background-color: #fff3cd;
            padding: 10px;
            margin-top: 15px;
            border-left: 5px solid #ffeeba;
        }
        .auto-generated {
            background-color: #f1f1f1;
            padding: 10px;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <div style="max-width: 900px; margin: 0 auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: 8px;">
        <div class="logo" style="background-color: #005f7f; padding: 20px; border-radius: 10px;">
            <img src="{{ $message->embed(storage_path('app/public/logos/logolhg.png')) }}" alt="LANCETA HG" style="width: 200px; display: block; margin: 0 auto;">
        </div>
        
        <h2>Estimado(a) {{ $user->name }},</h2>
        <p>Gracias por su pedido. A continuación, encontrará los detalles:</p>

        <h3>Detalles del Pedido</h3>
        <div class="order-container">
            <div class="order-header">
                <p>
                    <strong>Número de Pedido:</strong> {{ $order->order_number }}<br>
                    <strong>Fecha de Creación:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}<br>
                    <strong>Método de Envío:</strong> 
                    @switch($order->shipment_method)
                        @case('EnvioPorPaqueteria')
                            Envío por Paquetería
                            @break
                        @case('RecogerEnTienda')
                            Recoger en Tienda
                            @break
                        @case('EnvioLocal')
                            Envío Local
                            @break
                        @case('EnvioPorCobrar')
                            Envío por Cobrar
                            @break
                        @default
                            Método Desconocido
                    @endswitch
                </p>
            </div>
            <div class="order-items">
                <h4>Productos:</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Número de Producto</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                            @php
                                $discounted_price = $item->unit_price * (1 - ($item->discount / 100));
                                $item_subtotal = $discounted_price * $item->quantity;
                            @endphp
                            <tr>
                                <td>{{ $item->product_id }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td>${{ number_format($item_subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    
                </table>
                @php
                $subtotal = $orderItems->sum(function($item) {
                    $discounted_price = $item->unit_price * (1 - ($item->discount / 100));
                    return $discounted_price * $item->quantity;
                });
            
                $iva = $subtotal * 0.16; 
                $total = $subtotal + $iva;
            @endphp
            
                <div class="order-summary">
                    <p><strong>Subtotal:</strong> ${{ number_format($subtotal, 2) }}</p>
                    <p><strong>IVA (16%):</strong> ${{ number_format($iva, 2) }}</p>
                    <p><strong>Total del pedido:</strong> ${{ number_format($total, 2) }}</p>
                </div>
            </div>
        </div>

        @if ($order->shipment_method === 'RecogerEnTienda')
        <div class="note">
            <p>Estimado(a) cliente, le informamos que su pedido será preparado para recoger en la tienda correspondiente. En cuanto su pedido esté listo, recibirá un correo adicional confirmando la hora y día exactos de entrega. Le recordamos llevar una identificación oficial para verificar su identidad. Agradecemos su comprensión y preferencia.</p>
        </div>
        @elseif ($order->shipment_method === 'EnvioPorPaqueteria')
        <div class="note">
            <p>Su pedido será enviado por paquetería. Una vez que el número de guía esté disponible, podrá consultarlo en su cuenta, en la sección de pedidos. Agradecemos su paciencia y preferencia.</p>
        </div>
        @elseif ($order->shipment_method === 'EnvioLocal')
        <div class="note">
            <p>Su pedido será enviado localmente. Agradecemos su paciencia y preferencia.</p>
        </div>
        @elseif ($order->shipment_method === 'EnvioPorCobrar')
        <div class="note">
            <p>Estimado(a) cliente, le informamos que su pedido será enviado por cobrar. El precio del envío aún está pendiente de confirmación. Agradecemos su paciencia y preferencia.</p>
        </div>
        @endif

        <p>Gracias por confiar en nosotros.</p>
        <p>Saludos cordiales,<br>El equipo de LANCETA HG</p>
        <div class="auto-generated">
            <p>Este correo ha sido generado automáticamente por el sistema de LANCETA HG. Favor de no responder a este correo.</p>
        </div>
    </div>
</body>
</html>
