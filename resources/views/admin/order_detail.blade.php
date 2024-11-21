{{-- @extends('admin.template')

@section('content')
    <div class="container">
        <h1 class="mb-5 text-center">Detalle de la Orden #{{ $orden->order_number }}</h1>
        <!-- Botón para regresar a la vista general de pedidos -->
        <div class="mb-4 text-start">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                ← Regresar a Pedidos
            </a>
        </div>

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
                    <div class="col-md-6"><strong>Total con IVA:</strong> ${{ number_format($orden->total_con_iva, 2) }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4"><strong>Total:</strong> ${{ number_format($orden->total, 2) }}</div>
                    <div class="col-md-4"><strong>Subtotal (sin envío):</strong>
                        ${{ number_format($orden->subtotal_sin_envio, 2) }}</div>
                    <div class="col-md-4"><strong>Costo de Envío:</strong> ${{ number_format($orden->shipping_cost, 2) }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Descuento aplicado:</strong> ${{ number_format($orden->discount, 2) }}
                    </div>
                </div>
                <hr>
                <!-- Mostrar la dirección completa -->
                <h5><strong>Dirección de Envío:</strong></h5>
                <p>
                    {{ $orden->shipping_address ?? '' }}
                    @if ($orden->no_ext)
                        No. Ext: {{ $orden->no_ext }}
                    @endif
                    @if ($orden->no_int)
                        No. Int: {{ $orden->no_int }}
                    @endif
                    <br>
                    {{ $orden->colonia ?? '' }}<br>
                    {{ $orden->municipio ?? '' }}<br>
                    {{ $orden->codigo_postal ?? '' }}<br>
                    {{ $orden->pais ?? '' }}
                </p>
            </div>
        </div>

        @if (!empty($trackingNumber))
        <div class="card mb-4 border-warning">
            <div class="card-body bg-warning text-dark">
                <h5 class="mb-3 text-primary">Número de Guía</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Número de Guía: {{ $trackingNumber }}</span>
                    <div>
                        <!-- Botón para rastrear -->
                        <a href="{{ $trackingUrl }}/{{ $trackingNumber }}" target="_blank"
                            class="btn btn-primary me-2">
                            Rastrear Pedido
                        </a>
                        <!-- Botón para generar etiquetas -->
                        <a href="{{ $genLabelUrl }}?trackingNoGen={{ $trackingNumber }}" target="_blank"
                            class="btn btn-secondary">
                            Generar Etiqueta de Rastreo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

        <!-- Información del Usuario -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h4>Información del Usuario</h4>
            </div>
            <div class="card-body">
                <p><strong>Nombre de Usuario:</strong> {{ $orden->user_name }}</p>
                <p><strong>Email:</strong> {{ $orden->user_email }}</p>
                @if ($orden->nombre)
                    <p><strong>Nombre Completo:</strong> {{ $orden->nombre }} {{ $orden->apellido_paterno }}
                        {{ $orden->apellido_materno }}</p>
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
                            <tr @if ($item->product_id == '999998') class="bg-light text-primary font-weight-bold" @endif>
                                <td>
                                    <div class="image-container shadow-sm mx-auto"
                                        style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden;">
                                        <a href="{{ route('producto.detalle', ['id' => $item->real_product_id]) }}">
                                            <img src="{{ route('producto.imagen', ['id' => $item->product_id]) }}"
                                                alt="Imagen del producto"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </a>
                                    </div>
                                </td>
                                <td>{{ $item->item_id }}</td>
                                <td>{{ $item->product_id }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    ${{ number_format($item->product_id == '999998' ? $item->unit_price + $item->iva_rate : $item->unit_price, 2) }}
                                </td>
                                <td>
                                    @if ($item->product_id == '999998')
                                        ${{ number_format(($item->unit_price + $item->iva_rate) * $item->quantity, 2) }}
                                    @else
                                        ${{ number_format($item->total_price, 2) }}
                                    @endif
                                </td>
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

        .bg-light.text-primary.font-weight-bold {
            background-color: #f8f9fa !important;
            color: #007bff !important;
            font-weight: bold;
        }
    </style>
@endsection --}}

@extends('admin.template')

@section('content')
<div class="container py-5">
    <h1 class=" text-center text-primary fw-bold">Detalle de la Orden #{{ $orden->order_number }}</h1>

    <!-- Botón para regresar -->
    <div class="text-start">
        <a href="{{ route('admin.orders.index') }}" class="btn shadow-sm btn-warning">
            ← Regresar a Pedidos
        </a>
    </div>

    <!-- Información de la Orden -->
    <div class="card shadow ">
        <div class="card-header bg-primary text-white">
            <h4 class="fw-bold mb-0">Información de la Orden</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6"><strong>Número de Orden:</strong> <span class="text-muted">{{ $orden->order_number }}</span></div>
                <div class="col-md-6"><strong>Fecha de Creación:</strong> <span class="text-muted">{{ $orden->order_created_at }}</span></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Método de Envío:</strong> <span class="text-muted">{{ $orden->shipment_method }}</span></div>
                <div class="col-md-6"><strong>Total con IVA:</strong> <span class="text-muted">${{ number_format($orden->total_con_iva, 2) }}</span></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Total:</strong> <span class="text-muted">${{ number_format($orden->total, 2) }}</span></div>
                <div class="col-md-4"><strong>Subtotal (sin envío):</strong> <span class="text-muted">${{ number_format($orden->subtotal_sin_envio, 2) }}</span></div>
                <div class="col-md-4"><strong>Costo de Envío:</strong> <span class="text-muted">${{ number_format($orden->shipping_cost, 2) }}</span></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Descuento Aplicado:</strong> <span class="text-muted">${{ number_format($orden->discount, 2) }}</span></div>
            </div>
            
            <hr>
            <!-- Dirección de Envío -->
            <h5 class="fw-bold">Dirección de Envío</h5>
            <p class="text-muted">
                {{ $orden->shipping_address ?? 'Sin dirección registrada' }}
                @if ($orden->no_ext) No. Ext: {{ $orden->no_ext }} @endif
                @if ($orden->no_int) No. Int: {{ $orden->no_int }} @endif
                <br>
                {{ $orden->colonia ?? '' }}<br>
                {{ $orden->municipio ?? '' }}<br>
                {{ $orden->codigo_postal ?? '' }}<br>
                {{ $orden->pais ?? '' }}
            </p>
                <!-- Botón para descargar el PDF -->
    <div class="text-center mt-5">
        <a href="{{ route('admin.order.pdf', ['orderId' => $orden->order_id]) }}" class="btn btn-primary btn-lg shadow">
            Descargar PDF de la Orden
        </a>
    </div>
        </div>
    </div>

    @if (!empty($trackingNumber))
    <!-- Número de Guía -->
    <div class="card shadow  border-warning">
        <div class="card-body bg-warning text-dark">
            <h5 class="text-primary fw-bold">Número de Guía</h5>
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Número de Guía: {{ $trackingNumber }}</span>
                <div>
                    <a href="{{ $trackingUrl }}/{{ $trackingNumber }}" target="_blank" class="btn btn-outline-primary me-2">
                        Rastrear Pedido
                    </a>
                    <a href="{{ $genLabelUrl }}?trackingNoGen={{ $trackingNumber }}" target="_blank" class="btn btn-outline-secondary">
                        Generar Etiqueta de Rastreo
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Información del Usuario -->
    <div class="card shadow ">
        <div class="card-header bg-secondary text-white">
            <h4 class="fw-bold mb-0">Información del Usuario</h4>
        </div>
        <div class="card-body">
            <p><strong>Nombre de Usuario:</strong> <span class="text-muted">{{ $orden->user_name }}</span></p>
            <p><strong>Email:</strong> <span class="text-muted">{{ $orden->user_email }}</span></p>
            @if ($orden->nombre)
            <p><strong>Nombre Completo:</strong> <span class="text-muted">{{ $orden->nombre }} {{ $orden->apellido_paterno }} {{ $orden->apellido_materno }}</span></p>
            <p><strong>Teléfono:</strong> <span class="text-muted">{{ $orden->telefono }}</span></p>
            <p><strong>Correo Alternativo:</strong> <span class="text-muted">{{ $orden->correo }}</span></p>
            @endif
        </div>
    </div>

    <!-- Detalles de los Ítems -->
    <div class="card shadow ">
        <div class="card-header bg-info text-white">
            <h4 class="fw-bold mb-0">Productos en la Orden</h4>
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="table-secondary">
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
                            <div class="image-container mx-auto" style="width: 60px; height: 60px;">
                                <a href="{{ route('producto.detalle', ['id' => $item->real_product_id]) }}">
                                    <img src="{{ route('producto.imagen', ['id' => $item->product_id]) }}" alt="Imagen del producto"
                                        class="img-fluid rounded shadow-sm">
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

    @if ($pago)
    <!-- Detalles de Pago -->
    <div class="card shadow mb-5">
        <div class="card-header bg-success text-white">
            <h4 class="fw-bold mb-0">Detalles de Pago</h4>
        </div>
        <div class="card-body">
            <p><strong>Total Cobrado:</strong> <span class="text-muted">${{ number_format($pago->chargetotal, 2) }}</span></p>
            <p><strong>Tipo de Solicitud:</strong> <span class="text-muted">{{ $pago->request_type }}</span></p>
            <p><strong>Transacción Procesada:</strong> <span class="text-muted">{{ $pago->txtn_processed }}</span></p>
            <p><strong>Marca de Tarjeta:</strong> <span class="text-muted">{{ $pago->ccbrand }}</span></p>
            <p><strong>Número de Tarjeta:</strong> <span class="text-muted">**** **** **** {{ substr($pago->cardnumber, -4) }}</span></p>
        </div>
    </div>
    @endif


</div>

<style>
    .card {
        border-radius: 12px;
    }

    .image-container img {
        object-fit: cover;
        height: 100%;
        width: 100%;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
