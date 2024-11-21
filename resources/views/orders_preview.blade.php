@extends('template')

@section('body')
<div class="container mt-5">
    <div class="card shadow-sm p-4 mb-5 rounded-3">
        <h2 class="mb-4 text-center text-primary">Mis Pedidos</h2>

        @if($orders->isEmpty())
            <div class="alert alert-info text-center py-4" role="alert">
                No tiene pedidos registrados.
            </div>
        @else
            <ul class="list-group">
                @foreach($orders as $order)
                    <li class="list-group-item mb-4 p-4 {{ $order->is_new ? 'new-order' : '' }}">
                        <div class="row align-items-center">
                            <!-- Información del pedido -->
                            <div class="col-md-8">
                                <h5 class="mb-2 fw-bold text-primary">
                                    Pedido ID: <span class="text-dark">{{ $order->order_number }}</span>
                                </h5>
                                <div class="d-flex flex-wrap">
                                    <div class="me-4">
                                        <p class="mb-1"><strong>Fecha de Creación:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</p>
                                        <p class="mb-1"><strong>Método de Envío:</strong> 
                                            <span class="shipment-method">
                                                @if ($order->shipment_method === 'EnvioPorPaqueteria')
                                                    Envío por Paquetería
                                                @elseif ($order->shipment_method === 'RecogerEnTienda')
                                                    Recoger en Tienda
                                                @elseif ($order->shipment_method === 'EnvioLocal')
                                                    Envío Local
                                                @elseif ($order->shipment_method === 'EnvioPorCobrar')
                                                    Envío por Cobrar
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="mb-0 total-container">
                                            <strong>Total:</strong> 
                                            <span class="total-price">
                                                ${{ number_format($order->total_con_iva, 2) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Botón para detalles -->
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('order.details', ['orderId' => $order->id]) }}" class="btn btn-outline-primary rounded-pill px-4 py-2 shadow-sm">
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
    /* Contenedor general */
    .card {
        background-color: #f9f9f9;
        border: 1px solid #e3e3e3;
    }

    /* Estilo para pedidos recientes */
    .new-order {
        border: 3px solid #00B398;
        box-shadow: 0 0 20px rgba(0, 179, 152, 0.5);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .new-order:hover {
        transform: scale(1.02);
        box-shadow: 0 0 30px rgba(0, 179, 152, 0.8);
    }

    /* Información del pedido */
    .order-info h5 {
        font-size: 1.3rem;
    }

    .order-info p {
        font-size: 1rem;
        color: #495057;
        margin: 0;
    }

    /* Métodos de envío */
    .shipment-method {
        padding: 0.3rem 0.6rem;
        background-color: #e7f3fe;
        color: #0c5460;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: bold;
    }

    /* Resaltar lista de pedidos */
    .list-group-item {
        background-color: #ffffff;
        border: 1px solid #e3e3e3;
        border-radius: 10px;
        padding: 20px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Contenedor del total */
    .total-container {
        background-color: #ffe134;
        border-radius: 8px;
    }

    .total-price {

        font-weight: bold;
        color: #212529;
    }

    /* Botón de detalles */
    .btn-outline-primary {
        padding: 10px 20px;
        font-size: 1rem;
        font-weight: bold;
        border: 2px solid #007bff;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        color: #fff;
    }

    /* Estilo de alertas */
    .alert-info {
        background-color: #e7f3fe;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
</style>
@endsection
