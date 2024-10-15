@extends('template') 

@section('body')
<div class="container mt-5">
    <h2>Mis Pedidos</h2>
    @if($orders->isEmpty())
        <p>No tienes pedidos registrados.</p>
    @else
        @foreach($orders as $order)
            <div class="card shadow p-4 mb-4">
                <h5>Pedido ID: {{ $order->id }} - Total: ${{ number_format($order->total, 2) }}</h5>
                <p><strong>Dirección de Envío:</strong> {{ $order->shipping_address }}</p>
                <p><strong>Método de Envío:</strong> {{ $order->shipment_method }}</p>
                <p><strong>Costo de Envío:</strong> ${{ number_format($order->shipping_cost, 2) }}</p>
                <p><strong>Subtotal sin Envío:</strong> ${{ number_format($order->subtotal_sin_envio, 2) }}</p>
                <p><strong>Total con IVA:</strong> ${{ number_format($order->total_con_iva, 2) }}</p>
                <p><strong>Fecha de Creación:</strong> {{ $order->created_at }}</p>

                <!-- Mostrar productos de la orden -->
                @if(isset($order_items[$order->id]) && $order_items[$order->id]->isNotEmpty())
                    <table class="table table-sm table-hover mt-3">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Precio Total</th>
                                <th>Descuento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order_items[$order->id] as $item)
                                <tr>
                                    <td>{{ $item->product_id }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                    <td>{{ $item->discount }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No hay productos asociados a este pedido.</p>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
