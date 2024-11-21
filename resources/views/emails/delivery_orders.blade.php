<!DOCTYPE html> 
<html>
<head>
    <title>Información de su próxima entrega</title>
    <style>
        /* Estilos básicos para el correo */
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
        .order-summary p {
            margin: 5px 0;
        }
        .order-footer {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }
        .note {
            background-color: #fff3cd;
            padding: 10px;
            margin-top: 15px;
            border-left: 5px solid #ffeeba;
        }
    </style>
</head>
<body>
    <div style="max-width: 900px; margin: 0 auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: 8px;">
    <div class="logo" style="background-color: #005f7f; padding: 20px; border-radius: 10px;">
        <img src="{{ $message->embed(storage_path('app/public/logos/logolhg.png')) }}" alt="LANCETA HG" style="width: 200px; display: block; margin: 0 auto;">
    </div>
    
    <h2>Estimado(a) {{ $user->name }},</h2>
    <p>Le informamos que su pedido ha sido actualizado con la siguiente información de entrega:</p>

    <div class="order-container">
        <div class="order-header">
            <h3>Pedido #{{ $order->order_number }}</h3>
            <p>
                <strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}<br>
                <strong>Hora de entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_time)->format('H:i') }}
            </p>
            @if($store)
            <p>
                <strong>Lugar de Entrega:</strong> {{ $store->nombre ?? 'No disponible' }}<br>
                <strong>Dirección:</strong> {{ $store->direccion ?? 'No disponible' }}
            </p>
        @endif
        
        </div>
        <div class="order-items">
            <h4>Productos:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderItems as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @php
                $subtotal = $orderItems->sum(function($item) {
                    return $item->unit_price * $item->quantity;
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
        <div class="note">
            <p>Recuerde llevar una identificación oficial para recoger su pedido.</p>
        </div>
        <p>Gracias por confiar en nosotros.</p>
        <p>Saludos cordiales,<br>El equipo de LANCETA HG</p>
    </div>
    <div class="auto-generated" style="background-color: #f1f1f1; padding: 10px; margin-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 12px; color: #555;">
        <p>Este correo ha sido generado automáticamente por el sistema de LANCETA HG. Favor de no responder a este correo.</p>
    </div>
</div>
</body>
</html>
