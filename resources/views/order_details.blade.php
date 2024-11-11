@extends('template')

@section('body')
    <div class="container my-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-5">
                <h3 class="mb-4 text-center fw-bold text-secondary">Detalles del Pedido <span
                        class="text-primary">#{{ $order->order_number }}</span></h3>

                <!-- Información del Pedido y Envío -->
                <div class="row">

                    <!-- Información de Envío -->
                    <div class="col-md-6 mb-4">
                        <div class="p-4 bg-light rounded-3 border">
                            <h5 class="mb-3 text-primary">Información de Envío</h5>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td><strong>Dirección de Envío:</strong></td>
                                        @if (isset($order_shippment))
                                            <td class="text-end text-dark">
                                                {{ $order_shippment->shipping_address . ' No. ' . $order_shippment->no_int . '  INT ' . $order_shippment->no_ext . ', ' . $order_shippment->colonia . ', ' . $order_shippment->municipio . ', ' . $order_shippment->codigo_postal . ', ' . $order_shippment->pais ?? 'N/A' }}
                                            </td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td><strong>Método de Envío:</strong></td>
                                        <td class="text-end text-dark">{{ $order->shipment_method_display ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Costo de Envío:</strong></td>
                                        <td class="text-end text-dark">${{ number_format($order->shipping_cost, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha de Creación:</strong></td>
                                        <td class="text-end text-dark">
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Información del Estado del Pedido -->
                    @if ($orderHistory)
                        <div class="col-md-6 p-4 bg-light rounded-3 border mb-4">
                            <h5 class="mb-4 text-primary">Estado del Pedido</h5>
                            <ul class="timeline">
                                @php

                                    $statuses = [
                                        'status_1_confirmation_at' => [
                                            'label' => 'Verificado',
                                            'icon' => 'bi-check-circle-fill',
                                            'color' => 'success',
                                        ],
                                        'status_2_payment_process_at' => [
                                            'label' => 'En Proceso de Pago',
                                            'icon' => 'bi-check-circle-fill',
                                            'color' => 'success',
                                        ], // Ícono cambiado aquí
                                        'status_3_paid_at' => [
                                            'label' => 'Pagado',
                                            'icon' => 'bi-check-circle-fill',
                                            'color' => 'success',
                                        ],
                                        'status_4_rejected_at' => [
                                            'label' => 'Rechazado',
                                            'icon' => 'bi-x-circle-fill',
                                            'color' => 'danger',
                                        ],
                                        'status_5_confirmed_at' => [
                                            'label' => 'Confirmado y Completado',
                                            'icon' => 'bi-check2-circle',
                                            'color' => 'success',
                                        ],
                                    ];
                                @endphp

                                @foreach ($statuses as $key => $status)
                                    @if (isset($orderHistory->$key) && !empty($orderHistory->$key))
                                        <li class="timeline-item">
                                            <span class="timeline-icon bg-{{ $status['color'] }} text-white">
                                                <i class="{{ $status['icon'] }}"></i>
                                            </span>
                                            <div class="timeline-content">
                                                <h6 class="fw-bold mb-1">{{ $status['label'] }}</h6>
                                                <p class="mb-0 text-muted">
                                                    {{ \Carbon\Carbon::parse($orderHistory->$key)->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach

                                <!-- Mostrar "Confirmado y Completado" como en proceso si el pedido está pagado pero no confirmado -->
                                @if ($orderHistory->status_3_paid_at && !$orderHistory->status_5_confirmed_at)
                                    <li class="timeline-item">
                                        <span class="timeline-icon bg-warning text-white">
                                            <i class="bi bi-hourglass-split"></i>
                                        </span>
                                        <div class="timeline-content">
                                            <h6 class="fw-bold mb-1">Confirmado y Completado</h6>
                                            <p class="mb-0 text-muted">En proceso...</p>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @else
                        <div class="col-md-6 p-4 bg-light rounded-3 border mb-4">
                            <div class="alert alert-warning mb-4" role="alert">
                                No hay información de estado disponible para este pedido.
                            </div>
                        </div>
                    @endif
                </div>
                <!-- Mostrar productos de la orden -->
                <div class="p-4 bg-light rounded-3 border mb-4">
                    <h4 class="mb-4 text-primary">Productos del Pedido</h4>
                    @if ($order_items->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-secondary">
                                    <tr>
                                        <th scope="col">Codigo</th>
                                        <th scope="col">Producto</th>
                                        <th scope="col" class="text-center">Cantidad</th>
                                        <th scope="col" class="text-end">Precio Unitario</th>
                                        <th scope="col" class="text-end">Importe</th>
                                        <th scope="col" class="text-end">Descuento</th>
                                        <th scope="col" class="text-end">Iva</th>
                                        <th scope="col" class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // dd($order_items);
                                        $subtotal_pedido = 0;
                                        $descuento_pedido = 0;
                                        $envio_pedido = round($order->shipping_cost/1.16,2);
                                        $envio_iva_pedido = round($envio_pedido*0.16,2);
                                        $iva_pedido = 0;
                                        $total_pedido = 0;
                                    @endphp
                                    @foreach ($order_items as $item)
                                        @php
                                            $descuentoEnDinero = ($item->discount / 100) * $item->unit_price;
                                            $precioConDescuento = $item->unit_price - $descuentoEnDinero;
                                            $precio_unitario = 0;
                                            $descuento = 0;
                                            $importe = 0;
                                            $tasa = 0;


                                            if($item->iva_rate=='IVA16')
                                            {
                                                $precio_unitario= round($item->unit_price/1.16,2);
                                                $descuento = round(($descuentoEnDinero/1.16),2)* $item->quantity;
                                                $tasa = 0.16;
                                            }
                                            else
                                            {
                                                $precio_unitario= $item->unit_price;
                                                $descuento = $descuentoEnDinero* $item->quantity;
                                                $tasa = 0;
                                            }

                                            $importe = $precio_unitario * $item->quantity;

                                            $iva  = ($importe-$descuento) * $tasa;

                                            $total = $importe - $descuento + $iva;

                                            $subtotal_pedido += $importe;
                                            $descuento_pedido += $descuento;
                                            $iva_pedido += $iva;
                                            $total_pedido += $total + $iva;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->product_id }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">${{ number_format($precio_unitario, 2) }}</td>
                                            <td class="text-end">${{ number_format($importe, 2) }}</td>
                                            <td class="text-end">${{ number_format($descuento , 2) }}</td>
                                            <td class="text-end">${{ number_format($iva, 2) }}</td>
                                            <td class="text-end">${{ number_format($total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    {{-- <tr>
                                        <td></td>
                                        <td>ENVIO</td>
                                        <td class="text-center">1</td>
                                        <td class="text-end">${{ number_format($envio_pedido, 2) }}</td>
                                        <td class="text-end">$0.00</td>
                                        <td class="text-end">${{ number_format($envio_pedido, 2) }}</td>
                                        <td class="text-end">${{ number_format($envio_iva_pedido, 2) }}</td>
                                        <td class="text-end">${{ number_format($order->shipping_cost, 2) }}</td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            No hay productos asociados a este pedido.
                        </div>
                    @endif
                </div>
                <div class="row">
                    <!-- Cuadro Exclusivo para Método de Pago -->
                    @if ($payment)
                        <div class="col-md-6 p-4 bg-light rounded-3 border mb-4">
                            <h5 class="mb-3 text-primary">Método de Pago</h5>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td><strong>Monto:</strong></td>
                                        <td class="text-end text-dark fw-bold">
                                            ${{ number_format($payment->chargetotal, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tipo de Transacción:</strong></td>
                                        <td class="text-end text-dark">{{ $payment->request_type }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha de Procesamiento:</strong></td>
                                        <td class="text-end text-dark">
                                            {{ \Carbon\Carbon::parse($payment->txtn_processed)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Número de Tarjeta:</strong></td>
                                        <td class="text-end text-dark">{{ $payment->cardnumber ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <!-- Resumen del Pedido -->
                    <div class="col-md-6 mb-4">
                        <div class="p-4 bg-light rounded-3 border">
                            <h5 class="mb-3 text-primary">Resumen del Pedido</h5>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    @php
                                        // dd($iva_pedido,$envio_iva_pedido);
                                    @endphp
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td class="text-end text-dark">${{ number_format($subtotal_pedido+$envio_pedido, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Descuento:</strong></td>
                                        <td class="text-end text-dark">${{ number_format($descuento_pedido, 2) }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td><strong>Envio:</strong></td>
                                        <td class="text-end text-dark">${{ number_format($envio_pedido, 2) }}</td>
                                    </tr> --}}
                                    <tr>
                                        <td><strong>Iva:</strong></td>
                                        <td class="text-end text-dark fw-bold">${{ number_format($iva_pedido+$envio_iva_pedido, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total:</strong></td>
                                        <td class="text-end text-dark fw-bold">${{ number_format($order->total, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Botón para Volver a Mis Pedidos -->
                <div class="text-center">
                    <a href="{{ route('myorders') }}" class="btn btn-secondary btn-lg rounded-3">
                        <i class="bi bi-arrow-left-circle me-2"></i> Volver a mis pedidos
                    </a>
                </div>


            </div>
        </div>
    </div>

    <!-- Estilos CSS Personalizados -->
    <style>
        body {
            background-color: #f5f7fa;
        }

        .text-primary {
            color: #007bff !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        .fw-semibold {
            font-weight: 600 !important;
        }

        .list-group-item {
            background-color: #f8f9fa;
            border: none;
        }

        .list-group-item:not(:last-child) {
            border-bottom: 1px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1.125rem;
            line-height: 1.5;
            border-radius: 0.3rem;
        }

        .bi {
            font-size: 1.25rem;
        }

        /* Estilos para la Línea de Tiempo */
        .timeline {
            list-style: none;
            padding-left: 0;
            position: relative;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 70px;
        }

        .timeline-icon {
            position: absolute;
            left: 15px;
            top: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .timeline-content {
            padding: 0 15px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        /* Loader Styles (si se necesita en futuras implementaciones) */
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #007bff;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsividad para dispositivos móviles */
        @media (max-width: 767.98px) {
            .timeline::before {
                left: 20px;
            }

            .timeline-item {
                padding-left: 60px;
            }

            .timeline-icon {
                left: 5px;
            }
        }
    </style>
@endsection
