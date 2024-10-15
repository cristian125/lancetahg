@extends('template')

@section('body')

    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="display-4 font-weight-bold text-primary mb-5">Confirmación de Envío</h2>
            </div>
        </div>
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
            <!-- Div que contiene el iframe y el formulario de pago, dentro de un contenedor con sombra -->
            <div class="container mt-5">
                <div id="paymentSection" style="display: none;" class="shadow p-4">
                    <!-- Loader que se muestra mientras se carga el iframe -->
                    <div id="loader" class="loader"></div>
                    <div class="row">
                        <div class="col-12">
                            <h3 class="text-center mt-5">FORMULARIO DE PAGO</h3>
                            <iframe name="paymentFrame" id="paymentFrame" width="100%" height="800px"
                                style="border:none;"></iframe>
                            <form id="paymentForm" method="POST" action="{{ env('PAYMENT_URL') }}" target="paymentFrame">
                                @csrf
                                <input type="hidden" name="oid" value="{{ $paymentData['oid'] }}" />
                                <input type="hidden" name="chargetotal" value="{{ $paymentData['chargetotal'] }}" />
                                <input type="hidden" name="checkoutoption" value="{{ $paymentData['checkoutoption'] }}" />
                                <input type="hidden" name="currency" value="{{ $paymentData['currency'] }}" />
                                <input type="hidden" name="hash_algorithm" value="{{ $paymentData['hash_algorithm'] }}" />
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

                <!-- Resumen de Compra con productos -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg border-0 rounded">
                        <div class="card-header bg-success text-white text-uppercase font-weight-bold">
                            <i class="bi bi-receipt"></i> Resumen de Compra
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @foreach ($cartItems as $item)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                {{ $item->description }} ({{ $item->quantity }}x)
                                                <br>
                                                @if ($item->discount > 0)
                                                    <span class="text-danger">
                                                        Descuento: {{ $item->discount }}%
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="text-end">
                                                @if ($item->discount > 0)
                                                    <span class="text-muted" style="text-decoration: line-through;">
                                                        ${{ number_format($item->unit_price * $item->quantity, 2, '.', ',') }}
                                                        MXN
                                                    </span>
                                                    <br>
                                                    <strong
                                                        class="text-dark">${{ number_format($item->final_price * $item->quantity, 2, '.', ',') }}
                                                        MXN</strong>
                                                @else
                                                    <strong>${{ number_format($item->final_price * $item->quantity, 2, '.', ',') }}
                                                        MXN</strong>
                                                @endif
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Total de Productos:</strong>
                                    <strong>${{ number_format($totalPriceItems, 2, '.', ',') }} MXN</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between bg-light text-primary">
                                    <strong>Costo de Envío:</strong>
                                    <strong>${{ number_format($shippingCost, 2, '.', ',') }} MXN</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between font-weight-bold bg-warning">
                                    <strong>Total Final:</strong>
                                    <strong class="text-danger">${{ number_format($totalFinal, 2, '.', ',') }} MXN</strong>
                                </li>
                            </ul>
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
                                @if ($shippment->shipping_method === 'RecogerEnTienda')
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
                                        <strong>Fecha de Recogida:</strong> {{ $shippment->pickup_date }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Hora de Recogida:</strong> {{ $shippment->pickup_time }}
                                    </li>
                                    <li class="list-group-item"><strong>Nombre de Contacto:</strong>
                                        {{ $shippment->nombre_contacto }}</li>
                                    <li class="list-group-item"><strong>Teléfono de Contacto:</strong>
                                        {{ $shippment->telefono_contacto }}</li>
                                @else
                                    <!-- Mostrar los detalles de envío a domicilio -->
                                    <li class="list-group-item"><strong>Dirección:</strong>
                                        {{ $shippment->shipping_address }} {{ $shippment->no_ext }}</li>
                                    @if (!empty($shippment->no_int))
                                        <li class="list-group-item"><strong>Número Interior:</strong>
                                            {{ $shippment->no_int }}</li>
                                    @endif
                                    <li class="list-group-item"><strong>Entre Calles:</strong>
                                        {{ $shippment->entre_calles }}</li>
                                    <li class="list-group-item"><strong>Colonia:</strong> {{ $shippment->colonia }}</li>
                                    <li class="list-group-item"><strong>Municipio:</strong> {{ $shippment->municipio }}
                                    </li>
                                    <li class="list-group-item"><strong>Código Postal:</strong>
                                        {{ $shippment->codigo_postal }}</li>
                                    <li class="list-group-item"><strong>País:</strong> {{ $shippment->pais }}</li>
                                    <li class="list-group-item"><strong>Nombre de Contacto:</strong>
                                        {{ $shippment->nombre_contacto }}</li>
                                    <li class="list-group-item"><strong>Teléfono de Contacto:</strong>
                                        {{ $shippment->telefono_contacto }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>



            </div>


            <!-- Botón para Proceder al Pago -->
            <div class="row">
                <div class="col-12 text-center">
                    <button id="proceedToPaymentBtn" class="btn btn-success btn-lg mt-4">
                        Proceder al Pago
                    </button>
                </div>
            </div>
        @endif
    </div>




    <script>
        document.getElementById('proceedToPaymentBtn').addEventListener('click', function() {
            // Ocultar todo el contenido excepto el iframe y el formulario de pago
            document.querySelectorAll('.container > .row').forEach(function(row) {
                row.style.display = 'none';
            });

            // Mostrar la sección de pago con el iframe y el loader
            document.getElementById('paymentSection').style.display = 'block';
            document.getElementById('loader').style.display = 'block'; // Mostrar el loader

            // Opcionalmente, desplazar la página hacia abajo
            window.scrollTo({
                top: document.getElementById('paymentSection').offsetTop,
                behavior: 'smooth'
            });

            // Enviar automáticamente el formulario de pago
            document.getElementById('paymentForm').submit();
        });

        // Detectar cuándo el iframe ha terminado de cargar y ocultar el loader
        document.getElementById('paymentFrame').onload = function() {
            document.getElementById('loader').style.display = 'none'; // Ocultar el loader cuando el iframe haya cargado
        };

        window.addEventListener("message", receiveMessage, false);

        function receiveMessage(event) {
            if (event.origin !==
                "{{ parse_url(env('PAYMENT_URL'), PHP_URL_SCHEME) }}://{{ parse_url(env('PAYMENT_URL'), PHP_URL_HOST) }}")
                return;
            var elementArr = event.data.elementArr;
            forwardForm(event.data, elementArr);
        }

        function forwardForm(responseObj, elementArr) {
            var newForm = document.createElement("form");
            newForm.setAttribute('method', 'post');
            newForm.setAttribute('action', responseObj.redirectURL);
            newForm.setAttribute('id', 'newForm');
            newForm.setAttribute('name', 'newForm');
            document.body.appendChild(newForm);

            for (var i = 0; i < elementArr.length; i++) {
                var element = elementArr[i];
                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', element.name);
                input.setAttribute('value', element.value);
                newForm.appendChild(input);
            }

            newForm.submit();
        }
    </script>


    <style>
        /* From Uiverse.io by SchawnnahJ */
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
    </style>

@endsection
