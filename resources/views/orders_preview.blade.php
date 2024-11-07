@extends('template')

@section('body')
<div class="container mt-5">
    <div class="card shadow-sm p-4 mb-5" style="border-radius: 12px; border: 1px solid #e3e3e3;">
        <h2 class="mb-4 text-center" style="color: #007bff;">Mis Pedidos</h2>

        @if($orders->isEmpty())
            <div class="alert alert-info text-center" role="alert">
                No tiene pedidos registrados.
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach($orders as $order)
                    <li class="list-group-item mb-4 {{ $order->is_new ? 'new-order' : '' }}" style="border: 1px solid #e9ecef; border-radius: 8px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="order-info">
                                <h5 class="mb-2 font-weight-bold" style="color: #007bff;">Pedido ID: {{ $order->order_number }}</h5>
                                <p class="mb-1"><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
                                <p class="mb-1"><strong>Fecha de Creación:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</p>
                                <p class="mb-0"><strong>Método de Envío:</strong> {{ $order->shipment_method ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <a href="{{ route('order.details', ['orderId' => $order->id]) }}" class="btn btn-primary" style="border-radius: 5px;">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Enlaces de paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    /* Estilo general del contenedor */
    .card {
        background-color: #f9f9f9;
        border-radius: 12px;
    }

    /* Estilo para pedidos recientes */
    .new-order {
        border: 6px solid #ffffff;
        box-shadow: 0px 0px 40px #00B398;
        transition: all 0.3s ease;
    }

    .new-order:hover {
        transform: translateY(-5px);
        box-shadow: 0px 0px 50px #00B398;
    }

    .order-info h5 {
        font-size: 1.25rem;
        color: #007bff;
    }

    .order-info p {
        font-size: 0.95rem;
        color: #6c757d;
    }

    .list-group-item {
        background-color: #fff;
        padding: 20px;
        transition: background-color 0.3s ease;
        border-radius: 8px;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 10px 20px;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Mejora del estilo de los mensajes de alerta */
    .alert-info {
        background-color: #e7f3fe;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
</style>
@endsection
