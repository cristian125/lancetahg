<!DOCTYPE html>
<html>

<head>
    <title>Nuevo Pedido para su Tienda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }

        h2 {
            color: #007bff;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .order-container {
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            overflow-x: auto;
            /* Habilita desplazamiento horizontal */
        }

        .order-header {
            background-color: #f8f9fa;
            padding: 10px;
        }

        .order-items table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .order-items th,
        .order-items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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


    <div class="container">
        <div class="logo" style="background-color: #005f7f; padding: 20px; border-radius: 10px;">
            <img src="{{ $message->embed(storage_path('app/public/logos/logolhg.png')) }}" alt="LANCETA HG"
                style="width: 200px; display: block; margin: 0 auto;">
        </div>
        <h2>Nuevo Pedido para su Tienda</h2>
        <p>Le informamos que se ha generado un nuevo pedido web con el número
            <strong>#{{ $orderData->order_number }}</strong>, el cual debe ser procesado en su tienda.</p>

        <h3>Detalles del Pedido:</h3>
        <div class="order-container">
            <div class="order-header">
                <p>
                    <strong>Fecha de Creación:</strong> {{ \Carbon\Carbon::parse($orderData->created_at)->format('d/m/Y H:i') }}<br>
                    <strong>Método de Envío:</strong> 
                    @switch($orderData->shipment_method)
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
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Número de Serie</th>
                                <th>Producto</th>

                                <th>Cantidad</th>
                                <th>Precio Unitario (sin IVA)</th>
                                <th>Precio Unitario (con IVA)</th>
                                <th>Subtotal (sin IVA)</th>
                                <th>Subtotal (con IVA)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $subtotalSinIVA = 0;
                                $subtotalConIVA = 0;
                            @endphp
                            @foreach ($orderItemsData as $item)
                                @php
                                    $precioUnitarioSinIVA = $item->unit_price;
                                    $precioUnitarioConIVA = $item->unit_price * (1 + $item->vat);
                                    $subtotalItemSinIVA = $precioUnitarioSinIVA * $item->quantity;
                                    $subtotalItemConIVA = $precioUnitarioConIVA * $item->quantity;
                                    $subtotalSinIVA += $subtotalItemSinIVA;
                                    $subtotalConIVA += $subtotalItemConIVA;
                                @endphp
                                <tr>
                                    <td>{{ $item->product_id }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($precioUnitarioSinIVA, 2) }}</td>
                                    <td>${{ number_format($precioUnitarioConIVA, 2) }}</td>
                                    <td>${{ number_format($subtotalItemSinIVA, 2) }}</td>
                                    <td>${{ number_format($subtotalItemConIVA, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="order-summary">
                    <p><strong>Total sin IVA:</strong> ${{ number_format($subtotalSinIVA, 2) }}</p>
                    <p><strong>Total con IVA:</strong> ${{ number_format($subtotalConIVA, 2) }}</p>
                </div>
            </div>
        </div>
        <h3>Datos del Cliente:</h3>
        <p>
            <strong>Nombre:</strong> {{ $userData->name }}<br>
            <strong>Correo Electrónico:</strong> {{ $userData->email }}<br>
            <strong>Nombre de Contacto:</strong> {{ $orderShippment->nombre_contacto ?? 'No disponible' }}<br>
            <strong>Teléfono de Contacto:</strong> {{ $orderShippment->telefono_contacto ?? 'No disponible' }}
        </p>
        <div class="note">
            <p>
                Por favor, le recomendamos apartar los productos en su tienda para garantizar su disponibilidad.
                Una vez que los productos estén listos, acceda al sistema de pedidos para visualizar los pedidos
                pendientes
                y asignar una fecha de entrega desde ahí.
            </p>
        </div>
        <div class="auto-generated">
            <p>Este correo ha sido generado automáticamente por el sistema de LANCETA HG. Favor de no responder a este
                correo.</p>
        </div>
    </div>
</body>

</html>
