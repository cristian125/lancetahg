@extends('template')

@section('body')
<div class="container mt-5">
    <div class="card shadow p-5" style="border-radius: 10px; border: 1px solid #e0e0e0;">
        <h3 class="mb-4 text-center" style="font-weight: 600; color: #4A4A4A;">Detalles del Pedido ID: {{ $order->id }}</h3>

        <!-- Información del Pedido -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-block mb-4" style="background-color: #fafafa; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
                    <h5 class="mb-3 text-primary" style="font-weight: 500;">Resumen del Pedido</h5>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td class="text-right" style="font-weight: 600; color: #333;">${{ number_format($order->total, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Subtotal sin Envío:</strong></td>
                                <td class="text-right">${{ number_format($order->subtotal_sin_envio, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Descuento Total en Pedido:</strong></td>
                                <td class="text-right">${{ number_format($totalDescuento, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total con IVA:</strong></td>
                                <td class="text-right" style="font-weight: 600;">${{ number_format($order->total_con_iva, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-block mb-4" style="background-color: #fafafa; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
                    <h5 class="mb-3 text-primary" style="font-weight: 500;">Información de Envío</h5>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td><strong>Dirección de Envío:</strong></td>
                                <td class="text-right">{{ $order->shipping_address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Método de Envío:</strong></td>
                                <td class="text-right">{{ $order->shipment_method ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Costo de Envío:</strong></td>
                                <td class="text-right">${{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha de Creación:</strong></td>
                                <td class="text-right">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

<!-- Información del Estado del Pedido -->
@if($orderHistory)
    <div class="status-block mb-4 p-4" style="background-color: #f7f7f7; border-radius: 10px; border: 1px solid #ddd;">
        <h5 class="mb-3 text-primary" style="font-weight: 500;">Estado del Pedido</h5>
        <ul class="list-unstyled">
            <!-- Mostrar el método de pago seleccionado -->
            @if($orderHistory->payment_method)
                <li><strong>Método de Pago:</strong> {{ $orderHistory->payment_method }}</li>
            @endif
            @if($orderHistory->status_1_confirmation_at)
                <li><strong>Verificado:</strong> {{ \Carbon\Carbon::parse($orderHistory->status_1_confirmation_at)->format('d/m/Y H:i') }}</li>
            @endif
            @if($orderHistory->status_2_payment_process_at)
                <li><strong>En Proceso de Pago:</strong> {{ \Carbon\Carbon::parse($orderHistory->status_2_payment_process_at)->format('d/m/Y H:i') }}</li>
            @endif
            @if($orderHistory->status_3_paid_at)
                <li><strong>Pagado:</strong> {{ \Carbon\Carbon::parse($orderHistory->status_3_paid_at)->format('d/m/Y H:i') }}</li>
            @endif
            @if($orderHistory->status_4_rejected_at)
                <li><strong>Rechazado:</strong> {{ \Carbon\Carbon::parse($orderHistory->status_4_rejected_at)->format('d/m/Y H:i') }}</li>
            @endif
            @if($orderHistory->status_5_confirmed_at)
                <li><strong>Confirmado y Completado:</strong> {{ \Carbon\Carbon::parse($orderHistory->status_5_confirmed_at)->format('d/m/Y H:i') }}</li>
            @endif
        </ul>
    </div>
@else
    <div class="alert alert-warning mt-3" role="alert">
        No hay información de estado disponible para este pedido.
    </div>
@endif

        <!-- Mostrar productos de la orden -->
        <div class="products-block p-3 mt-4" style="background-color: #fafafa; border: 1px solid #ddd; border-radius: 10px;">
            <h4 class="mb-4 text-primary" style="font-weight: 500;">Productos del Pedido</h4>
            @if($order_items->isNotEmpty())
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
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
                                $descuentoEnDinero = ($item->discount / 100) * $item->unit_price;
                                $precioConDescuento = $item->unit_price - $descuentoEnDinero;
                            @endphp
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($precioConDescuento, 2) }}</td>
                                <td>${{ number_format($item->total_price, 2) }}</td>
                                <td>${{ number_format($descuentoEnDinero * $item->quantity, 2) }}</td>
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
        <a href="{{ route('myorders') }}" class="btn btn-secondary" style="padding: 12px 30px; border-radius: 8px; font-size: 16px;">Volver a mis pedidos</a>
    </div>
</div>

<style>
    .info-block, .status-block, .products-block {
        background-color: #fafafa;
        border-radius: 10px;
        border: 1px solid #ddd;
    }

    .table td {
        vertical-align: middle;
    }

    .table td.text-right {
        text-align: right;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 500;
    }

    .btn-secondary:hover {
        background-color: #565e64;
    }
</style>
@endsection
