@extends('template')

@section('body')

    <div class="container">

        <!-- Mostrar mensajes de éxito o error -->
        @if (session('message'))
            <div class="alert alert-success text-center">
                <i class="bi bi-check-circle-fill"></i> {{ session('message') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        @if (isset($error))
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}
            </div>
        @else
            <div class="container my-4 p-4 border rounded bg-light shadow-sm">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="display-4 font-weight-bold text-primary mb-5">Confirmación de Envío</h2>
                    </div>
                </div>
                <!-- Div que contiene el iframe y el formulario de pago, dentro de un contenedor con sombra -->
                <div class="container">
                    <div id="paymentSection" style="display: none;">
                        <!-- Loader que se muestra mientras se carga el iframe -->
                        <div id="loader" class="loader"></div>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="text-center mt-5 border border-success">FORMULARIO DE PAGO</h3>
                                <iframe name="paymentFrame" id="paymentFrame" width="100%" height="800px"
                                    style="border:none;"></iframe>
                                <form id="paymentForm" method="POST" action="{{ env('PAYMENT_URL') }}"
                                    target="paymentFrame">
                                    @csrf
                                    <input type="hidden" name="oid" value="{{ $paymentData['oid'] }}" />
                                    <input type="hidden" name="chargetotal" value="{{ $paymentData['chargetotal'] }}" />
                                    <input type="hidden" name="checkoutoption"
                                        value="{{ $paymentData['checkoutoption'] }}" />
                                    <input type="hidden" name="currency" value="{{ $paymentData['currency'] }}" />
                                    <input type="hidden" name="hash_algorithm"
                                        value="{{ $paymentData['hash_algorithm'] }}" />
                                    <input type="hidden" name="hashExtended" value="{{ $paymentData['hashExtended'] }}" />
                                    <input type="hidden" name="parentUri" value="{{ $paymentData['parentUri'] }}" />
                                    <input type="hidden" name="responseFailURL"
                                        value="{{ $paymentData['responseFailURL'] }}" />
                                    <input type="hidden" name="responseSuccessURL"
                                        value="{{ $paymentData['responseSuccessURL'] }}" />
                                    <input type="hidden" name="storename" value="{{ $paymentData['storename'] }}" />
                                    <input type="hidden" name="timezone" value="{{ $paymentData['timezone'] }}" />
                                    <input type="hidden" name="txndatetime" value="{{ $paymentData['txndatetime'] }}" />
                                    <input type="hidden" name="txntype" value="{{ $paymentData['txntype'] }}" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-lg border-0 rounded">
                            <div class="card-header bg-success text-white text-uppercase font-weight-bold">
                                <i class="bi bi-receipt"></i> Resumen de Compra
                            </div>
                            <div class="card-body">
                                <!-- Desglose de productos estilo ticket -->
                                @foreach ($cartItems as $item)
                                    <div class="mb-3 p-3 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong>Artículo (No_s):</strong>
                                            <span>{{ $item->no_s }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <strong>Descripción:</strong>
                                            <span>{{ $item->description }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <strong>Unidad:</strong>
                                            <span>{{ $item->unidad }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <strong>Cantidad:</strong>
                                            <span>{{ $item->quantity }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <strong>Importe:</strong>
                                            @if ($item->discount > 0)
                                                <span class="text-muted" style="text-decoration: line-through;">
                                                    ${{ number_format(($item->unit_price * $item->quantity)*(1+$item->vat), 2, '.', ',') }}
                                                    MXN
                                                </span>
                                                <br>
                                                <strong>${{ number_format($item->final_price, 2, '.', ',') }}
                                                    MXN</strong>
                                                <br><span class="text-danger">Descuento: {{ $item->discount }}%</span>
                                            @else
                                                <strong>${{ number_format($item->final_price, 2, '.', ',') }}
                                                    MXN</strong>
                                            @endif
                                        </div>

                                        <!-- Mostrar el valor del IVA -->
                                        <div class="d-flex justify-content-between">
                                            <strong>IVA:</strong>
                                            @if (isset($item->grupo_iva) && $item->grupo_iva === 'IVA16')
                                                <span class="badge bg-success">IVA 16%</span>
                                            @elseif (isset($item->grupo_iva) && $item->grupo_iva === 'IVA0')
                                                <span class="badge bg-warning text-dark">IVA 0%</span>
                                            @else
                                                <span class="badge bg-secondary">Sin IVA</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Resumen de totales generales en formato de columnas con estilo mejorado -->
                                <div class="table-responsive mt-4 shadow-sm">
                                    <table class="table table-hover table-striped table-borderless align-middle">
                                        <thead class="thead-light">
                                            <tr class="text-center bg-primary text-white">
                                                <th scope="col">Tipo de IVA</th>
                                                <th scope="col">Valor Neto</th>
                                                <th scope="col">IVA</th>
                                                <th scope="col">Importe Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Fila para productos con IVA 16% -->
                                            <tr class="text-center">
                                                <td><strong>IVA 16%</strong></td>
                                                <td>${{ number_format($totalConIVA / 1.16, 2, '.', ',') }} MXN</td>
                                                <td>${{ number_format($totalConIVA - $totalConIVA / 1.16, 2, '.', ',') }}
                                                    MXN
                                                </td>
                                                <td><strong>${{ number_format($totalConIVA, 2, '.', ',') }} MXN</strong>
                                                </td>
                                            </tr>

                                            <!-- Fila para productos con IVA 0% -->
                                            <tr class="text-center">
                                                <td><strong>IVA 0%</strong></td>
                                                <td>${{ number_format($totalSinIVA, 2, '.', ',') }} MXN</td>
                                                <td>$0.00 MXN</td>
                                                <td><strong>${{ number_format($totalSinIVA, 2, '.', ',') }} MXN</strong>
                                                </td>
                                            </tr>

                                            <!-- Fila para costo de envío -->
                                            <tr class="text-center">
                                                <td><strong>Costo de Envío</strong></td>
                                                <td></td>
                                                <td></td>
                                                @if ($shippment->ShipmentMethod === 'EnvioPorCobrar')
                                                    <td class="bg-warning text-dark text-center p-3 rounded shadow-sm">
                                                        <i class="bi bi-exclamation-circle-fill text-danger me-2"></i>
                                                        <strong>El costo de envío aún no a sido calculado y deberá ser
                                                            pagado al
                                                            recibirlo en su domicilio</strong>
                                                    </td>
                                                @else
                                                    <td><strong>${{ number_format($shippingCost, 2, '.', ',') }}
                                                            MXN</strong>
                                                    </td>
                                                @endif
                                            </tr>


                                            <!-- Fila para importe total final -->
                                            <tr class="text-center bg-warning font-weight-bold">
                                                <td><strong>Importe Total (con IVA)</strong></td>
                                                <td></td>
                                                <td></td>
                                                @if ($shippment->ShipmentMethod === 'EnvioPorCobrar')
                                                    <td class="text-danger">
                                                        <strong>${{ number_format($totalFinal - $shippingCost, 2, '.', ',') }}
                                                            MXN</strong>
                                                    </td>
                                                @else
                                                    <td class="text-danger">
                                                        <strong>${{ number_format($totalFinal, 2, '.', ',') }} MXN</strong>
                                                    </td>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles del Envío -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-lg border-0 rounded">
                            <div class="card-header bg-primary text-white text-uppercase font-weight-bold">
                                <i class="bi bi-truck"></i> Detalles del Envío
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @if ($shippment->ShipmentMethod === 'RecogerEnTienda')
                                        <!-- Mostrar los detalles de la tienda para recoger -->
                                        <li class="list-group-item">
                                            <strong>Este pedido es para recoger en tienda:</strong>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Nombre de la Tienda:</strong> {{ $storeDetails->nombre }}
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Dirección de la Tienda:</strong> {{ $storeDetails->direccion }}
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Teléfono de la Tienda:</strong> {{ $storeDetails->telefono }}
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Horario de Atención:</strong>
                                            Lunes a Viernes: {{ $storeDetails->horario_semana }}<br>
                                            Sábado: {{ $storeDetails->horario_sabado }}
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Fecha de Recogida:</strong> <span class="text-danger">La fecha de recogida se confirmará pronto.</span>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Hora de Recogida:</strong> <span class="text-danger">La hora de recogida se confirmará pronto.</span>
                                        </li>
                                        <li class="list-group-item"><strong>Nombre de Contacto:</strong> {{ $shippment->contactName }}</li>
                                        <li class="list-group-item"><strong>Teléfono de Contacto:</strong> {{ $shippment->contactPhone }}</li>
                                        
                                    @else
                                        <!-- Mostrar los detalles de envío a domicilio -->
                                        <li class="list-group-item"><strong>Dirección:</strong>
                                            {{ $shippment->calle }} {{ $shippment->no_ext }}</li>
                                        @if (!empty($shippment->no_int))
                                            <li class="list-group-item"><strong>Número Interior:</strong>
                                                {{ $shippment->no_int }}</li>
                                        @endif
                                        <li class="list-group-item"><strong>Entre Calles:</strong>
                                            {{ $shippment->entre_calles }}</li>
                                        <li class="list-group-item"><strong>Colonia:</strong> {{ $shippment->colonia }}
                                        </li>
                                        <li class="list-group-item"><strong>Municipio:</strong>
                                            {{ $shippment->municipio }}
                                        </li>
                                        <li class="list-group-item"><strong>Código Postal:</strong>
                                            {{ $shippment->codigo_postal }}</li>
                                        <li class="list-group-item"><strong>País:</strong> {{ $shippment->pais }}</li>
                                        <li class="list-group-item"><strong>Nombre de Contacto:</strong>
                                            {{ $shippment->contactName }}</li>
                                        <li class="list-group-item"><strong>Teléfono de Contacto:</strong>
                                            {{ $shippment->contactPhone }}</li>
                                        @if ($shippment->ShipmentMethod === 'EnvioPorCobrar')
                                            <li class="list-group-item bg-warning text-dark rounded shadow-sm">
                                                <i class="bi bi-exclamation-circle-fill text-danger me-2"></i>
                                                <strong>Nota Importante:</strong> El costo de envío será calculado y cobrado
                                                al
                                                momento de la entrega por la empresa de paquetería.
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para Proceder al Pago -->
                <div class="row">
                    <div class="col-12 text-center">
                        <button id="proceedToPaymentBtn" class="btn btn-primary btn-xl fw-normal shadow-lg py-3 px-5 my-4"
                                style="font-size: 1.5rem; border-radius: 8px;"
                                data-bs-toggle="modal" data-bs-target="#paymentMethodModal">
                            <i class="bi bi-credit-card me-2"></i> Proceder al Pago
                        </button>
                    </div>

                </div>
            </div>
        @endif
    </div>
    <!-- Modal para seleccionar el método de pago -->
    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccione Método de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body payment-modal">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action payment-option"
                            data-payment-method="Tarjeta de Débito">
                            <i class="bi bi-credit-card"></i> Tarjeta de Débito
                        </a>
                        <a href="#" class="list-group-item list-group-item-action payment-option"
                            data-payment-method="Tarjeta de Crédito">
                            <i class="bi bi-credit-card-2-front"></i> Tarjeta de Crédito
                        </a>
                        <a href="#" class="list-group-item list-group-item-action payment-option"
                            data-payment-method="Monedero">
                            <i class="bi bi-wallet2"></i> Monedero
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal para seleccionar el método de pago -->
    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel"
        aria-hidden="true">
        <!-- ... (contenido del modal) ... -->
    </div>

    <!-- Sección del iframe y formulario de pago -->
    <div class="container mt-5" id="paymentSection" style="display: none;">
        <!-- Loader que se muestra mientras se carga el iframe -->
        <div id="loader" class="loader"></div>
        <div class="row">
            <div class="col-12">
                <h3 class="text-center mt-5 border border-success">FORMULARIO DE PAGO</h3>
                <iframe name="paymentFrame" id="paymentFrame" width="100%" height="800px"
                    style="border:none;"></iframe>
                <form id="paymentForm" method="POST" action="{{ env('PAYMENT_URL') }}" target="paymentFrame">
                    @csrf
                    @isset($paymentData)
                    @foreach ($paymentData as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                    @endforeach
                    @endisset
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar la selección del método de pago en el modal
            document.querySelectorAll('.payment-option').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    var selectedPaymentMethod = this.getAttribute('data-payment-method');

                    // Cerrar el modal
                    var paymentMethodModal = bootstrap.Modal.getInstance(document.getElementById(
                        'paymentMethodModal'));
                    paymentMethodModal.hide();

                    // Enviar el método de pago al servidor
                    fetch("{{ route('update.payment.method') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                payment_method: selectedPaymentMethod
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mostrar la sección de pago con el iframe y el loader
                                document.querySelectorAll('.container > .row').forEach(function(
                                    row) {
                                    row.style.display = 'none';
                                });
                                document.getElementById('paymentSection').style.display =
                                    'block';
                                document.getElementById('loader').style.display = 'block';

                                // Enviar automáticamente el formulario de pago
                                document.getElementById('paymentForm').submit();
                            } else {
                                alert('Error al actualizar el método de pago: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Ocurrió un error al procesar su solicitud.');
                        });
                });
            });

            // Detectar cuándo el iframe ha terminado de cargar y ocultar el loader
            document.getElementById('paymentFrame').onload = function() {
                document.getElementById('loader').style.display = 'none';
            };
        });
    </script>

    <style>
        .loader {
            position: relative;
            width: 2.5em;
            height: 2.5em;
            transform: rotate(165deg);
        }

        .loader:before,
        .loader:after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            display: block;
            width: 0.5em;
            height: 0.5em;
            border-radius: 0.25em;
            transform: translate(-50%, -50%);
        }

        .loader:before {
            animation: before8 2s infinite;
        }

        .loader:after {
            animation: after6 2s infinite;
        }

        @keyframes before8 {
            0% {
                width: 0.5em;
                box-shadow: 1em -0.5em rgba(225, 20, 98, 0.75), -1em 0.5em rgba(111, 202, 220, 0.75);
            }

            35% {
                width: 2.5em;
                box-shadow: 0 -0.5em rgba(225, 20, 98, 0.75), 0 0.5em rgba(111, 202, 220, 0.75);
            }

            70% {
                width: 0.5em;
                box-shadow: -1em -0.5em rgba(225, 20, 98, 0.75), 1em 0.5em rgba(111, 202, 220, 0.75);
            }

            100% {
                box-shadow: 1em -0.5em rgba(225, 20, 98, 0.75), -1em 0.5em rgba(111, 202, 220, 0.75);
            }
        }

        @keyframes after6 {
            0% {
                height: 0.5em;
                box-shadow: 0.5em 1em rgba(61, 184, 143, 0.75), -0.5em -1em rgba(233, 169, 32, 0.75);
            }

            35% {
                height: 2.5em;
                box-shadow: 0.5em 0 rgba(61, 184, 143, 0.75), -0.5em 0 rgba(233, 169, 32, 0.75);
            }

            70% {
                height: 0.5em;
                box-shadow: 0.5em -1em rgba(61, 184, 143, 0.75), -0.5em 1em rgba(233, 169, 32, 0.75);
            }

            100% {
                box-shadow: 0.5em 1em rgba(61, 184, 143, 0.75), -0.5em -1em rgba(233, 169, 32, 0.75);
            }
        }

        .loader {
            position: absolute;
            top: calc(50% - 1.25em);
            left: calc(50% - 1.25em);
        }

        .payment-modal {
            background-color: #f7f9fc;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
        }

        .payment-modal .list-group-item {
            display: flex;
            align-items: center;
            border: none;
            background-color: #ffffff;
            margin-bottom: 8px;
            padding: 15px;
            border-radius: 6px;
            transition: transform 0.3s ease, background-color 0.3s ease;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.05);
        }

        .payment-modal .list-group-item i {
            font-size: 1.5rem;
            margin-right: 10px;
            color: #007bff;
        }

        .payment-modal .list-group-item:hover {
            background-color: #e9f2ff;
            transform: scale(1.02);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
        }

        .payment-modal .list-group-item:active {
            background-color: #d0e4ff;
            transform: scale(0.98);
        }

        .payment-modal .payment-option {
            color: #333;
            font-weight: 500;
        }
    </style>

@endsection
