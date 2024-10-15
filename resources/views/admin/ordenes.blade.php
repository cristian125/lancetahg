@extends('admin.template')

@section('content')
<div class="container">
    <h1 class="mb-4">Órdenes de Pedido</h1>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por ID, Usuario o Método de Envío" value="{{ request()->input('search') }}">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>

    <!-- Tabla de órdenes -->
    <table class="table table-striped text-center align-middle">
        <thead>
            <tr>
                <th>ID de Orden</th>
                <th>Usuario</th>
                <th>Total</th>
                <th>Subtotal (sin envío)</th>
                <th>Costo de Envío</th>
                <th>Descuento aplicado</th>
                <th>Total con IVA</th>
                <th>Método de Envío</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @if($ordenes->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">No se encontraron resultados.</td>
                </tr>
            @else
            @foreach($ordenes as $orden)
            <tr>
                <td>{{ $orden->order_id }}</td>
                <td>{{ $orden->user_name }}</td>
                <td>${{ number_format($orden->total, 2) }}</td>
                <td>${{ number_format($orden->subtotal_sin_envio, 2) }}</td>
                <td>${{ number_format($orden->shipping_cost, 2) }}</td>
                <td>
                    @php
                        $descuentoDinero = ($orden->discount / 100) * $orden->subtotal_sin_envio;
                    @endphp
                    ${{ number_format($descuentoDinero, 2) }}
                </td>
                <td>${{ number_format($orden->total_con_iva, 2) }}</td>
                <td>{{ $orden->shipment_method }}</td>
                <td>{{ $orden->order_created_at }}</td>
                <td>
                    <!-- Botón de toggle para abrir/cerrar ítems -->
                    <button class="btn btn-info btn-sm toggle-items" data-target="#orderItems{{ $orden->order_id }}">
                        Ver Ítems
                    </button>
            
                    <!-- Botón para descargar el PDF -->
                    <a href="{{ route('admin.order.pdf', ['orderId' => $orden->order_id]) }}" class="btn btn-primary btn-sm">
                        Descargar PDF
                    </a>
                </td>
            </tr>
            
            <!-- Detalles de los ítems -->
            <tr>
                <td colspan="10">
                    <div class="order-items" id="orderItems{{ $orden->order_id }}" style="display: none;">
                        <table class="table table-bordered mt-3 text-center align-middle">
                            <thead>
                                <tr>
                                    <th>Miniatura</th>
                                    <th>ID del Ítem</th>
                                    <th>ID del Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Precio Total</th>
                                    <th>Descuento </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orden->items as $item)
                                <tr>
                                    <td>
                                        <!-- Contenedor de imagen con sombra, usamos el product_id para la imagen -->
                                        <div class="image-container shadow-sm mx-auto" style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden;">
                                            <a href="{{ route('producto.detalle', ['id' => $item->real_product_id]) }}">
                                                <img src="{{ route('producto.imagen', ['id' => $item->product_id]) }}" alt="Imagen del producto" style="width: 100%; height: 100%; object-fit: cover;">
                                            </a>
                                        </div>
                                    </td>
                                    <td>{{ $item->item_id }}</td>
                                    <td>{{ $item->product_id }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                    <td>
                                        @php
                                            $descuentoItem = ($item->discount / 100) * $item->unit_price * $item->quantity;
                                        @endphp
                                        ${{ number_format($descuentoItem, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            @endforeach
            
            @endif
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $ordenes->appends(['search' => request()->input('search')])->links() }}
    </div>
</div>

<!-- jQuery para manejar el toggle -->
<script>
    $(document).ready(function(){
        // Manejar el evento de mostrar/ocultar con jQuery
        $('.toggle-items').click(function(){
            var button = $(this);
            var target = $(button.data('target'));

            // Alternar visibilidad del contenido de los ítems
            target.toggle();

            // Cambiar el texto del botón según el estado de visibilidad
            if (target.is(':visible')) {
                button.text('Ocultar Ítems');
            } else {
                button.text('Ver Ítems');
            }
        });
    });
</script>

<style>
    .table td, .table th {
        vertical-align: middle; /* Asegura que los datos estén centrados verticalmente */
    }

    .image-container {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
@endsection
