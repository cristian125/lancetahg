@extends('admin.template')

@section('content')
<div class="container shadow p-3 mb-5 bg-white rounded">
        <h1 class="my-4 text-primary">Detalle del Log de Pago #{{ $log->id }}</h1>

        <a href="{{ route('admin.payment_logs.index') }}" class="btn btn-outline-primary mb-4">
            ← Volver a los Logs
        </a>

        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-info-circle"></i> Información General
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{ $log->id }}</td>
                        </tr>
                        <tr>
                            <th>Usuario</th>
                            <td>{{ $log->user_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Request</th>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($log->request_type) }}</span></td>
                        </tr>
                        <tr>
                            <th>Estado</th>
                            <td>
                                @if ($log->status == 'APROBADO')
                                    <span class="badge bg-success">{{ $log->status }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $log->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Creación</th>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="accordion" id="paymentLogDetails">

            <!-- Detalles de la Transacción -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingProcessor">
                    <button class="accordion-button text-white bg-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTransaction" aria-expanded="false" aria-controls="collapseTransaction">
                        <i class="fas fa-credit-card"></i> Detalles de la Transacción
                    </button>
                </h2>
                <div id="collapseTransaction" class="accordion-collapse collapse" aria-labelledby="headingTransaction"
                    data-bs-parent="#paymentLogDetails">
                    <div class="accordion-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>OID</th>
                                    <td>{{ $log->oid }}</td>
                                </tr>
                                <tr>
                                    <th>Monto Total</th>
                                    <td><span class="text-success">${{ number_format($log->chargetotal, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Moneda</th>
                                    <td>{{ $log->currency }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo de Transacción</th>
                                    <td>{{ $log->txntype }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Procesada</th>
                                    <td>{{ $log->txndate_processed ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha y Hora de Transacción</th>
                                    <td>{{ $log->txndatetime }}</td>
                                </tr>
                                <tr>
                                    <th>Terminal ID</th>
                                    <td>{{ $log->terminal_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>ID de Transacción IPG</th>
                                    <td>{{ $log->ipgTransactionId }}</td>
                                </tr>
                                <tr>
                                    <th>Método de Pago</th>
                                    <td>{{ $log->paymentMethod }}</td>
                                </tr>
                                <tr>
                                    <th>Intereses en Cuotas</th>
                                    <td>{{ ucfirst($log->installments_interest) }}</td>
                                </tr>
                                <tr>
                                    <th>Zona Horaria</th>
                                    <td>{{ $log->timezone }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detalles de la Orden -->
            @if (isset($order))
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOrder">
                        <button class="accordion-button text-white bg-success" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOrder" aria-expanded="false" aria-controls="collapseOrder">
                            <i class="fas fa-box"></i> Detalles de la Orden
                        </button>
                    </h2>
                    <div id="collapseOrder" class="accordion-collapse collapse" aria-labelledby="headingOrder"
                        data-bs-parent="#paymentLogDetails">
                        <div class="accordion-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>OID</th>
                                        <td>{{ $order->oid }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total de la Orden</th>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dirección de Envío</th>
                                        <td>{{ $order->shipping_address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Método de Envío</th>
                                        <td>{{ $order->shipment_method }}</td>
                                    </tr>
                                    <tr>
                                        <th>Subtotal sin Envío</th>
                                        <td>${{ number_format($order->subtotal_sin_envio, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Descuento</th>
                                        <td>${{ number_format($order->discount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total con IVA</th>
                                        <td>${{ number_format($order->total_con_iva, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Costo de Envío</th>
                                        <td>${{ number_format($order->shipping_cost, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Creación</th>
                                        <td>{{ $order->created_at }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Productos en la Orden -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOrderItems">
                        <button class="accordion-button text-white bg-secondary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOrderItems" aria-expanded="false" aria-controls="collapseOrderItems">
                            <i class="fas fa-shopping-cart"></i> Productos en la Orden
                        </button>
                    </h2>
                    <div id="collapseOrderItems" class="accordion-collapse collapse"
                        aria-labelledby="headingOrderItems" data-bs-parent="#paymentLogDetails">
                        <div class="accordion-body">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Producto ID</th>
                                        <th>Nombre del Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario (Con Descuento)</th>
                                        <th>Precio Unitario (Sin Descuento)</th>
                                        <th>Descuento (Porcentaje y Valor)</th>
                                        <th>Total con Descuento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalProductos = 0; // Para calcular el total de los productos
                                        $descuentoTotal = 0; // Para sumar el total de los descuentos aplicados
                                    @endphp
                                    @foreach ($orderItems as $item)
                                        @php
                                            // El precio total ya incluye el descuento
                                            $precioTotalConDescuento = $item->total_price;

                                            // El descuento aplicado en valor monetario (Porcentaje * Precio unitario * Cantidad)
                                            $descuentoAplicado =
                                                ($item->discount / 100) * ($item->unit_price * $item->quantity);

                                            // Precio unitario sin descuento (inverso del porcentaje aplicado)
                                            $precioUnitarioSinDescuento =
                                                $item->unit_price / (1 - $item->discount / 100);

                                            // Sumar el total de productos y el descuento
                                            $totalProductos += $precioTotalConDescuento;
                                            $descuentoTotal += $descuentoAplicado;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->product_id }}</td>
                                            <td>{{ $item->product_name ?? 'Nombre no disponible' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <!-- Columna del precio unitario con descuento aplicado -->
                                            <td>${{ number_format($item->unit_price, 2) }}</td>
                                            <!-- Nueva columna del precio unitario sin descuento -->
                                            <td>${{ number_format($precioUnitarioSinDescuento, 2) }}</td>
                                            <td>
                                                {{ $item->discount }}%
                                                <br>
                                                <small> -${{ number_format($descuentoAplicado, 2) }} (total)</small>
                                            </td>
                                            <td>${{ number_format($precioTotalConDescuento, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td colspan="3"></td>
                                        <td><strong>Total de Productos (Sin Descuento):</strong></td>
                                        <td colspan="3">${{ number_format($totalProductos + $descuentoTotal, 2) }}</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td colspan="3"></td>
                                        <td><strong>Descuento Total Aplicado:</strong></td>
                                        <td colspan="3">-${{ number_format($descuentoTotal, 2) }}</td>
                                    </tr>
                                    <tr class="table-success">
                                        <td colspan="3"></td>
                                        <td><strong>Costo de Envío:</strong></td>
                                        <td colspan="3">${{ number_format($order->shipping_cost, 2) }}</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td colspan="3"></td>
                                        <td><strong>Total con IVA:</strong></td>
                                        <td colspan="3">${{ number_format($order->total_con_iva, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Respuesta del Procesador -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingProcessor">
                    <button class="accordion-button collapsed text-white bg-info" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseProcessor" aria-expanded="false" aria-controls="collapseProcessor">
                        <i class="fas fa-cogs"></i> Respuesta del Procesador
                    </button>
                </h2>
                <div id="collapseProcessor" class="accordion-collapse collapse" aria-labelledby="headingProcessor"
                    data-bs-parent="#paymentLogDetails">
                    <div class="accordion-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Código de Aprobación</th>
                                    <td>{{ $log->approval_code }}</td>
                                </tr>
                                <tr>
                                    <th>Mensaje de la Asociación</th>
                                    <td>{{ $log->associationResponseMessage ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Código de Respuesta del Procesador</th>
                                    <td>{{ $log->processor_response_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Código de Respuesta de la Asociación</th>
                                    <td>{{ $log->associationResponseCode ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Razón de Falla</th>
                                    <td>{{ $log->fail_reason ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Referencia</th>
                                    <td>{{ $log->refnumber ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Información Técnica -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTechnical">
                    <button class="accordion-button collapsed text-white bg-dark" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTechnical" aria-expanded="false" aria-controls="collapseTechnical">
                        <i class="fas fa-shield-alt"></i> Información Técnica
                    </button>
                </h2>
                <div id="collapseTechnical" class="accordion-collapse collapse" aria-labelledby="headingTechnical"
                    data-bs-parent="#paymentLogDetails">
                    <div class="accordion-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Hash de Respuesta</th>
                                    <td>{{ $log->response_hash }}</td>
                                </tr>
                                <tr>
                                    <th>Algoritmo de Hash</th>
                                    <td>{{ $log->hash_algorithm }}</td>
                                </tr>
                                <tr>
                                    <th>Código de Respuesta 3D Secure</th>
                                    <td>{{ $log->response_code_3dsecure }}</td>
                                </tr>
                                <tr>
                                    <th>Información de Red del Procesador</th>
                                    <td>{{ $log->processor_network_information ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Endpoint Transaction ID</th>
                                    <td>{{ $log->endpointTransactionId ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>TDate</th>
                                    <td>{{ $log->tdate }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div> <!-- Fin del Accordion -->
    </div>
@endsection
