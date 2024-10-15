@extends('template')

@section('body')
<div class="container mt-5">
    <div class="card shadow-lg p-5" style="border-radius: 8px; border: 1px solid #e3e3e3;">
        <h3 class="mb-4 text-center">Detalles del Pedido ID: {{ $order->id }}</h3>

        <!-- Información del Pedido -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-block p-3 mb-4" style="border: 1px solid #ddd; border-radius: 6px;">
                    <h5 class="mb-3 text-primary">Resumen del Pedido</h5>
                    <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
                    <p><strong>Subtotal sin Envío:</strong> ${{ number_format($order->subtotal_sin_envio, 2) }}</p>
                    <p><strong>Descuento Total en Pedido:</strong> ${{ number_format($totalDescuento, 2) }}</p>

                    <p><strong>Total con IVA:</strong> ${{ number_format($order->total_con_iva, 2) }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-block p-3 mb-4" style="border: 1px solid #ddd; border-radius: 6px;">
                    <h5 class="mb-3 text-primary">Información de Envío</h5>
                    <p><strong>Dirección de Envío:</strong> {{ $order->shipping_address ?? 'N/A' }}</p>
                    <p><strong>Método de Envío:</strong> {{ $order->shipment_method ?? 'N/A' }}</p>
                    <p><strong>Costo de Envío:</strong> ${{ number_format($order->shipping_cost, 2) }}</p>
                    <p><strong>Fecha de Creación:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Mostrar productos de la orden -->
        <div class="products-block p-3 mt-4" style="border: 1px solid #ddd; border-radius: 6px;">
            <h4 class="mb-4 text-primary">Productos del Pedido</h4>
            @if($order_items->isNotEmpty())
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Miniatura</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario (con Descuento)</th>
                            <th>Sub Total</th>
                            <th>Descuento Aplicado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order_items as $item)
                            @php
                                // Calcular el descuento en dinero para este artículo
                                $descuentoEnDinero = ($item->discount / 100) * $item->unit_price;
                                // Calcular el precio unitario con descuento aplicado
                                $precioConDescuento = $item->unit_price - $descuentoEnDinero;
                            @endphp
                            <tr>
                                <td>
                                    <img src="{{ route('producto.imagen', ['id' => $item->product_id]) }}" alt="Producto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>{{ $item->product_id }}</td>
                                <td>{{ $item->quantity }}</td>
                                <!-- Precio unitario con descuento aplicado -->
                                <td>${{ number_format($precioConDescuento, 2) }}</td>
                                <!-- Mantener el precio total sin cambios -->
                                <td>${{ number_format($item->total_price, 2) }}</td>
                                <td>${{ number_format($descuentoEnDinero * $item->quantity, 2) }}</td> <!-- Mostrar el descuento total en dinero -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    No hay productos asociados a este pedido.
                </div>
            @endif
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('myorders') }}" class="btn btn-secondary" style="padding: 10px 20px; border-radius: 6px;">Volver a mis pedidos</a>
    </div>
</div>

<style>
    .info-block {
        background-color: #f9f9f9;
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .products-block {
        background-color: #ffffff;
        border-radius: 6px;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #565e64;
    }

    img {
        border: 1px solid #e3e3e3;
    }
</style>
@endsection
