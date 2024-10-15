@extends('template')

@section('body')
    <div class="header">
        <h1>Pedido ID: {{ $order->id }}</h1>
        <p><strong>Total con IVA:</strong> ${{ number_format($order->total_con_iva, 2) }}</p>
        <p><strong>Fecha de Creación:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <h2>Productos:</h2>
    <table>
        <thead>
            <tr>
                <th>Producto ID</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Sub Total</th>
                <th>Descuento Aplicado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order_items as $item)
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

    <h3>Resumen del Pedido:</h3>
    <p><strong>Total de Productos:</strong> ${{ number_format($order->subtotal_sin_envio, 2) }}</p>
    <p><strong>Descuento Total:</strong> ${{ number_format($totalDescuento, 2) }}</p>
    <p><strong>Total con IVA:</strong> ${{ number_format($order->total_con_iva, 2) }}</p>

@endsection