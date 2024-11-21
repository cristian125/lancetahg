@extends('template')

@section('body')
    <div class="container my-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-5">
                <h3 class="mb-4 text-center fw-bold text-secondary">Detalles del Pedido <span
                        class="text-primary">#{{ $order->order_number }}</span></h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="p-4 bg-light rounded-3 border">
                            <h5 class="mb-3 text-primary">Información de Envío</h5>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td><strong>Dirección de Envío:</strong></td>
                                        @if (isset($order_shippment))
                                            <td class="text-end text-dark">
                                                {{ $order_shippment->shipping_address . ' No. ' . $order_shippment->no_int . ' INT ' . $order_shippment->no_ext . ', ' . $order_shippment->colonia . ', ' . $order_shippment->municipio . ', ' . $order_shippment->codigo_postal . ', ' . $order_shippment->pais ?? 'N/A' }}
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
                                    @php
                                    @endphp

                                    @if ($trackingNumber)
                                        <tr>
                                            <td><strong>Número de Guía de pedido:</strong></td>
                                            <td class="text-end text-dark">{{ $trackingNumber }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                        </div>

                    </div>

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
                                        ],
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
                                            'icon' => 'bi-check-circle-fill',
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

                                @if ($order->shipment_method === 'RecogerEnTienda' && $order->delivery_date && $order->delivery_time)
                                    <li class="timeline-item">
                                        <span class="timeline-icon bg-success text-white">
                                            <i class="bi-check-circle-fill"></i>
                                        </span>
                                        <div class="timeline-content">
                                            <h6 class="mb-1" style="font-size: 1rem; color: #202020;"><strong> Completado
                                                    y asignado a fecha y hora de entrega </strong></h6>
                                            <p class="mb-1" style="font-size: 1.1rem; color: #333;">
                                                <span class="text-primary" style="font-size: 1rem;">Fecha de entrega:</span>
                                                <span
                                                    style="font-size: 1rem;">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</span><br>
                                                <span class="text-primary" style="font-size: 1rem;">Hora de entrega:</span>
                                                <span
                                                    style="font-size: 1rem;">{{ \Carbon\Carbon::parse($order->delivery_time)->format('H:i') }}</span>
                                            </p>
                                            <p class="mt-2 p-2 bg-light border border-info rounded text-danger"
                                                style="font-size: 0.8rem;">
                                                ⚠️ <strong>Recuerde llevar una identificación oficial para recoger su
                                                    pedido.</strong>
                                            </p>
                                        </div>
                                    </li>

                                @elseif ($orderHistory->status_3_paid_at && !$orderHistory->status_5_confirmed_at)
                                    <li class="timeline-item">
                                        <span class="timeline-icon bg-warning text-white">
                                            <i class="bi bi-hourglass-split"></i>
                                        </span>
                                        <div class="timeline-content">
                                            @if ($order->shipment_method === 'RecogerEnTienda')
                                                <h6 class="fw-bold mb-1">En Espera</h6>
                                                <p class="mb-0 text-muted">
                                                    Su paquete está en espera de asignación de fecha y hora de entrega. Le
                                                    notificaremos en su correo tan pronto como se programe el horario de
                                                    recogida en la tienda.
                                                </p>
                                            @else
                                                <h6 class="fw-bold mb-1">Completado</h6>
                                                <p class="mb-0 text-muted">En proceso...</p>
                                            @endif
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
                    {{-- Mostrar información de la tienda solo si el envío es "RecogerEnTienda" --}}
                    @if ($order->shipment_method === 'RecogerEnTienda' && $store)
                        <div class="col-md-12 mb-4">
                            <div class="p-4 bg-light rounded-3 border">
                                <h5 class="mb-3 text-primary">Información de la Tienda</h5>
                                <p><strong>Nombre:</strong> {{ $store->nombre }}</p>
                                <p><strong>Dirección:</strong> {{ $store->direccion }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($trackingNumber)
                        <div class="col-md-12 mb-4">
                            <div class="p-4 bg-warning rounded-3 border">
                                <h5 class="mb-3 text-primary">Número de Guía</h5>
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td><strong>Número de Guía de Pedido:</strong></td>
                                            <td class="text-end text-dark">{{ $trackingNumber }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-center">
                                                <a href="{{ $trackingUrl }}/{{ $trackingNumber }}" target="_blank"
                                                    class="btn btn-primary">
                                                    Rastrear Pedido
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
                <div class="p-4 bg-light rounded-3 border mb-4">
                    <h4 class="mb-4 text-primary">Productos del Pedido</h4>
                    @if ($order_items->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-secondary">
                                    <tr class="text-center">
                                        <th scope="col">Codigo</th>
                                        <th scope="col">Producto</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Precio Unitario</th>
                                        <th scope="col">Importe</th>
                                        <th scope="col">Descuento</th>
                                        <th scope="col">Iva</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php

                                        $subtotal_pedido = 0;
                                        $descuento_pedido = 0;
                                        $envio_pedido = round($order->shipping_cost / 1.16, 2);
                                        $envio_iva_pedido = round($envio_pedido * 0.16, 2);
                                        $iva_pedido = 0;
                                        $total_pedido = 0;
                                    @endphp
                                    @foreach ($order_items as $item)
                                        @php
                                            $subtotal_pedido += $item->amount;
                                            $descuento_pedido += $item->discount_amount;
                                            $iva_pedido += $item->vat_amount;
                                            $total_pedido = $item->final_price;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->product_id }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-end">${{ number_format($item->amount, 2) }}</td>
                                            <td class="text-end">${{ number_format($item->discount_amount, 2) }}</td>
                                            <td class="text-end">${{ number_format($item->vat_amount, 2) }}</td>
                                            <td class="text-end">
                                                ${{ number_format($item->amount - $item->discount_amount, 2) }}</td>
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
                    @if ($payment)
                        <div class="col-md-6 p-4 bg-light rounded-3 border mb-4">
                            <h5 class="mb-3 text-primary">Método de Pago</h5>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td><strong>Monto:</strong></td>
                                        <td class="text-end text-dark fw-bold">
                                            ${{ number_format($order->total_con_iva, 2) }}
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
                                        <td class="text-end text-dark">${{ number_format($subtotal_pedido, 2) }}
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
                                        <td class="text-end text-dark fw-bold">${{ number_format($iva_pedido, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total:</strong></td>
                                        <td class="text-end text-dark fw-bold">
                                            ${{ number_format($order->total_con_iva, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="text-center">
                    <a href="{{ route('myorders') }}" class="btn btn-secondary btn-lg rounded-3">
                        <i class="bi bi-arrow-left-circle me-2"></i> Volver a mis pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>

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
