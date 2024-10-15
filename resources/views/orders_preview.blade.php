@extends('template')

@section('body')
<div class="container mt-5">
    <div class="card shadow-sm p-4 mb-5" style="border-radius: 8px; border: 1px solid #e3e3e3;">
        <h2 class="mb-4 text-center">Mis Pedidos</h2>

        @if($orders->isEmpty())
            <div class="alert alert-info text-center" role="alert">
                No tienes pedidos registrados.
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach($orders as $order)
                    <li class="list-group-item mb-4" style="border: 1px solid #e9ecef; border-radius: 8px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="order-info">
                                <h5 class="mb-2 font-weight-bold" style="color: #007bff;">Pedido ID: {{ $order->id }}</h5>
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
        @endif
    </div>
</div>

<style>
    /* Estilos adicionales para mejorar la apariencia */
    .order-info h5 {
        font-size: 1.25rem;
    }

    .order-info p {
        font-size: 0.95rem;
        color: #6c757d;
    }

    .list-group-item {
        background-color: #fff;
        padding: 20px;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>
@endsection
