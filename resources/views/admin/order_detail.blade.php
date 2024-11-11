@extends('admin.template')

@section('content')
<div class="container">
    <h1 class="mb-5 text-center">Detalle de la Orden #{{ $orden->order_number }}</h1>

    <!-- Información de la Orden -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>Información de la Orden</h4>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6"><strong>Número de Orden:</strong> {{ $orden->order_number }}</div>
                <div class="col-md-6"><strong>Fecha de Creación:</strong> {{ $orden->order_created_at }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Método de Envío:</strong> {{ $orden->shipment_method }}</div>
                <div class="col-md-6"><strong>Total con IVA:</strong> ${{ number_format($orden->total_con_iva, 2) }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Total:</strong> ${{ number_format($orden->total, 2) }}</div>
                <div class="col-md-4"><strong>Subtotal (sin envío):</strong> ${{ number_format($orden->subtotal_sin_envio, 2) }}</div>
                <div class="col-md-4"><strong>Costo de Envío:</strong> ${{ number_format($orden->shipping_cost, 2) }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Descuento aplicado:</strong> ${{ number_format($orden->discount, 2) }}</div>
            </div>
            <hr>
            <!-- Mostrar la dirección completa -->
            <h5><strong>Dirección de Envío:</strong></h5>
            <p>
                {{ $orden->shipping_address ?? '' }}
                @if ($orden->no_ext) No. Ext: {{ $orden->no_ext }} @endif
                @if ($orden->no_int) No. Int: {{ $orden->no_int }} @endif
                <br>
                {{ $orden->colonia ?? '' }}<br>
                {{ $orden->municipio ?? '' }}<br>
                {{ $orden->codigo_postal ?? '' }}<br>
                {{ $orden->pais ?? '' }}
            </p>
        </div>
    </div>

    <!-- Información del Usuario -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h4>Información del Usuario</h4>
        </div>
        <div class="card-body">
            <p><strong>Nombre de Usuario:</strong> {{ $orden->user_name }}</p>
            <p><strong>Email:</strong> {{ $orden->user_email }}</p>
            @if ($orden->nombre)
                <p><strong>Nombre Completo:</strong> {{ $orden->nombre }} {{ $orden->apellido_paterno }} {{ $orden->apellido_materno }}</p>
                <p><strong>Teléfono:</strong> {{ $orden->telefono }}</p>
                <p><strong>Correo Alternativo:</strong> {{ $orden->correo }}</p>
            @endif
        </div>
    </div>

    <!-- Detalles de los Ítems -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4>Productos en la Orden</h4>
        </div>
        <div class="card-body">
            <table class="table table-hover table-striped text-center align-middle">
                <thead>
                    <tr>
                        <th>Miniatura</th>
                        <th>ID del Ítem</th>
                        <th>ID del Producto</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Precio Total</th>
                        <th>Descuento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>
                                <div class="image-container shadow-sm mx-auto" style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden;">
                                    <a href="{{ route('producto.detalle', ['id' => $item->real_product_id]) }}">
                                        <img src="{{ route('producto.imagen', ['id' => $item->product_id]) }}" alt="Imagen del producto"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                </div>
                            </td>
                            <td>{{ $item->item_id }}</td>
                            <td>{{ $item->product_id }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->total_price, 2) }}</td>
                            <td>${{ number_format($item->discount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detalles de Pago -->
    @if ($pago)
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h4>Detalles de Pago</h4>
            </div>
            <div class="card-body">
                <p><strong>Total Cobrado:</strong> ${{ number_format($pago->chargetotal, 2) }}</p>
                <p><strong>Tipo de Solicitud:</strong> {{ $pago->request_type }}</p>
                <p><strong>Transacción Procesada:</strong> {{ $pago->txtn_processed }}</p>
                <p><strong>Marca de Tarjeta:</strong> {{ $pago->ccbrand }}</p>
                <p><strong>Número de Tarjeta:</strong> **** **** **** {{ substr($pago->cardnumber, -4) }}</p>
            </div>
        </div>
    @endif

    <!-- Botón para descargar el PDF -->
    <div class="text-center mt-4">
        <a href="{{ route('admin.order.pdf', ['orderId' => $orden->order_id]) }}" class="btn btn-primary btn-lg">
            Descargar PDF de la Orden
        </a>
    </div>
</div>

<style>
    .image-container {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .card {
        margin-bottom: 20px;
    }
</style>
@endsection
