<div id="loader" class="loader" style="display: none;"></div>
@php

@endphp
<div class="container mt-5" id="unique-cart-container">
    @if ($cartItems->isEmpty())
        <div class="empty-cart text-center" id="unique-empty-cart">
            <img src="{{ asset('storage/iconos/carrito_vacio.png') }}" alt="Carrito vacío" class="img-fluid mb-4"
                style="max-width: 200px;">
            <p>No hay productos en su carrito. <a href="{{ url('/') }}">Continúe comprando</a></p>
        </div>
    @else
        <h1 class="mb-4 text-center" id="unique-cart-title">Su Carrito de Compras</h1>
        @if (
            $nonEligibleLocalShipping->isNotEmpty() ||
                $nonEligiblePaqueteriaShipping->isNotEmpty() ||
                $nonEligibleStorePickup->isNotEmpty() ||
                $nonEligibleCobrarShipping->isNotEmpty())
            <div class="alert alert-warning" role="alert">
                <strong>Atención:</strong>
                Uno o más productos en su carrito no son elegibles para los siguientes métodos de envío:
                <ul>
                    @if ($nonEligibleLocalShipping->isNotEmpty())
                        <li><strong>Envío Local</strong></li>
                    @endif
                    @if ($nonEligiblePaqueteriaShipping->isNotEmpty())
                        <li><strong>Envío por Paquetería</strong></li>
                    @endif
                    @if ($nonEligibleStorePickup->isNotEmpty())
                        <li><strong>Recoger en Tienda</strong></li>
                    @endif
                    @if ($nonEligibleCobrarShipping->isNotEmpty())
                        <li><strong>Envío por Cobrar</strong></li>
                    @endif
                </ul>
                Revise los productos para elegir un método de envío adecuado.
            </div>
        @endif

        @if (!$tieneDirecciones)
            <div class="alert alert-warning">
                <strong>No tiene direcciones registradas, porfavor agregue una dirección.</strong>
                <a href="{{ route('cuenta', ['section' => 'Direcciones']) }}">Haz clic aquí para agregar una
                    dirección.</a>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="alert alert-info" role="alert">
            <strong>Nota:</strong> Si realiza cambios en su carrito (como añadir o eliminar productos), el método
            de
            envío seleccionado será eliminado. Por favor, seleccione un nuevo método de envío antes de proceder al
            pago.
        </div>

        <div id="mensaje-sin-tiendas" class="alert alert-danger" style="display: none;">

        </div>
        @if (!$shippmentExists)
            <div id="general-shipping-block" class="mb-4 p-4 bg-white rounded shadow-lg border border-warning">
                <div class="mb-4 p-3 bg-white rounded shadow-sm position-relative" style="border: 1px solid #dee2e6;">
                    <h5 class="mb-3 text-primary"><i class="bi bi-truck"></i> Seleccione método de envío</h5>
                    <div class="form-group position-relative">
                        <select id="shipping-type-selector" class="form-select custom-select">
                            <option value="" hidden>(Seleccione una opción)</option>
                            @foreach ($envios as $envio)
                                <option value="{{ $envio['name'] }}" data-price="{{ $envio['price'] }}">
                                    {{ $envio['name'] }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>

                <div id="store-pickup-block" class="mb-4 p-3 bg-white rounded shadow-sm" style="display: none;">
                    @include('partials.envios.storepickup')
                </div>

                <div id="local-shipping-block" class="mb-4 p-3 bg-white rounded shadow-sm" style="display: none;">
                    @include('partials.envios.local')
                </div>

                <div id="paqueteria-shipping-block" class="mb-4 p-3 bg-white rounded shadow-sm" style="display: none;">
                    @include('partials.envios.paqueteexpress')
                </div>

                <div id="cobrar-shipping-block" class="mb-4 p-3 bg-white rounded shadow-sm" style="display: none;">
                    @include('partials.envios.cobrar')
                </div>
            </div>
        @endif
        <div class="unique-cart-items" id="unique-cart-items">
            @if ($nonEligibleItems->isNotEmpty())
                <div class="non-eligible-items ">
                    <h3>Productos no elegibles para el método de envío seleccionado:</h3>
                    @foreach ($nonEligibleItems as $item)
                        <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm bg-light">

                            <div class="col-12 col-md-2 text-center mb-3 mb-md-0">
                                <div class="image-container1">
                                    @if (isset($item->id))
                                    <a href="{{ url('/producto/' . $item->id . '-' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($item->product_name))) }}">
                                            <img src="{{ asset($item->image) }}" class="product-image1"
                                                alt="{{ $item->description }}">
                                        </a>
                                    @else
                                        <img src="{{ asset($item->image) }}" class="product-image1"
                                            alt="{{ $item->description }}">
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-10 d-flex flex-column justify-content-between">

                                <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                                    <h5 class="mb-2">{{ $item->product_name }}</h5>
                                </div>

                                <div class="non-eligible-message" id="unique-non-eligible-message">
                                    <p class="unique-non-eligible-header text-danger mb-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        No se puede enviar el producto con el envío seleccionado.
                                    </p>
                                    <p class="unique-non-eligible-reminder text-success mb-1">
                                        Este producto permanecerá en su carrito para que pueda comprarlo más adelante con otra forma de envio.
                                    </p>
                                    <p class="unique-non-eligible-info text-success mb-1">
                                        Solo los productos elegibles se sumarán al total y serán procesados en esta
                                        orden.
                                    </p>
                                </div>
                                <style>
                                    #unique-non-eligible-message {
                                        padding: 15px;
                                        background-color: #ffebee;

                                        border: 1px solid #f5c6cb;
                                        border-radius: 8px;
                                        margin-bottom: 15px;
                                    }

                                    .unique-non-eligible-header {
                                        color: #d9534f;
                                        font-weight: bold;
                                    }

                                    .unique-non-eligible-info {
                                        color: #f0ad4e;
                                    }

                                    .unique-non-eligible-reminder {
                                        color: #5cb85c;
                                    }
                                </style>

                                @php
                                    $allowedMethods = [];
                                    if ($item->allow_local_shipping) {
                                        $allowedMethods[] = '<strong>Envío Local</strong>';
                                    }
                                    if ($item->allow_paqueteria_shipping) {
                                        $allowedMethods[] = '<strong>Envío por Paquetería</strong>';
                                    }
                                    if ($item->allow_store_pickup) {
                                        $allowedMethods[] = '<strong>Recoger en Tienda</strong>';
                                    }
                                    if ($item->allow_cobrar_shipping) {
                                        $allowedMethods[] = '<strong>Envío por Cobrar</strong>';
                                    }
                                @endphp

                                @if (!empty($allowedMethods))
                                    <p class="text-success mb-1">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Puede enviar este producto utilizando: {!! implode(', ', $allowedMethods) !!}.
                                    </p>
                                @else
                                    <p class="text-danger mb-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        Este producto no está disponible para ningún método de envío.
                                    </p>
                                @endif

                                @if ($eligibleCartItems->isNotEmpty())
                                    <p class="text-muted">
                                        De igual forma puede proceder con los productos restantes en su carrito
                                        elegibles con el método de envío que ya seleccionó.
                                    </p>
                                @else
                                    <div class="alert text-center">
                                        <strong>No hay ningún producto disponible en su carrito para el método de envío
                                            seleccionado.</strong>
                                        <p>Por favor, elija un método de envío diferente o modifique los productos en su
                                            carrito.</p>
                                        <button class="btn btn-info btn-m remove-shipping  ">
                                            <i class="bi bi-trash"></i> Eliminar envío y seleccionar otro método de
                                            envío disponible.
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            @if ($eligibleCartItems->isNotEmpty())
                <h3>Los productos de su carrito:</h3>
                @foreach ($eligibleCartItems as $item)
                    <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm">
                        <div class="col-12 col-md-2 text-center mb-3 mb-md-0">
                            <div class="image-container1">
                                @if (isset($item->id))
                                {{-- @php
                                    dd($item);
                                @endphp --}}
                                <a href="{{ url('/producto/' . $item->id . '-' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($item->product_name))) }}">

                                        <img src="{{ asset($item->image) }}" class="product-image1"
                                            alt="{{ $item->description }}">
                                    </a>
                                @else
                                    <img src="{{ asset($item->image) }}" class="product-image1"
                                        alt="{{ $item->description }}">
                                @endif
                            </div>
                        </div>

                        <div class="col-12 col-md-10 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                                <h5 class="mb-2">
                                    {{ str_pad($item->product_code, 6, '0', STR_PAD_LEFT) }} -
                                 
                                    <a href="{{ url('/producto/' . $item->id . '-' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($item->product_name))) }}"class="text-decoration-none text-dark">
                                        {{ $item->product_name }}
                                    </a>
                                </h5>

                                <a href="#" class="btn btn-danger btn-sm remove-from-cart1"
                                    data-nos="{{ $item->product_code ?? $item->no_s }}">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-12 col-md-4 mb-3 mb-md-0">
                                    <div class="d-flex flex-column flex-md-row align-items-center">
                                        <strong class="me-md-2">Cantidad:</strong>
                                        <div class="quantity-controls d-flex align-items-center">
                                            <input type="number" name="quantity"
                                                class="form-control text-center quantity-input me-2"
                                                style="width: 80px;" value="{{ $item->quantity }}" min="1"
                                                max="{{ $item->available_quantity }}"
                                                data-product-code="{{ $item->product_code ?? $item->no_s }}"
                                                data-max-quantity="{{ $item->available_quantity }}">
                                            <button type="button" class="btn btn-primary btn-sm update-quantity-btn"
                                                data-product-code="{{ $item->product_code ?? $item->no_s }}">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-12 col-md-4 mb-3 mb-md-0">
                                    @php
                                        $precioDescontado =
                                            ($item->unit_price - $item->unit_price * ($item->discount / 100)) *
                                            (1 + $item->vat);
                                    @endphp
                                    <p class="mb-0 unique-product-price1 bg-light p-2 rounded text-md-center">
                                        @if ($item->discount > 0)
                                            <span style="text-decoration: line-through; color: #888;">
                                                ${{ number_format($item->unit_price * (1 + $item->vat), 2, '.', ',') }}
                                                MXN
                                            </span><br>
                                            <span style="font-size: 1.2em; color: #28a745; font-weight: bold;">
                                                ${{ number_format($precioDescontado, 2, '.', ',') }} MXN
                                            </span>
                                        @else
                                            <span style="font-size: 1.2em; color: #333; font-weight: bold;">
                                                ${{ number_format($item->unit_price * (1 + $item->vat), 2, '.', ',') }}
                                                MXN
                                            </span>
                                        @endif
                                        <small class="text-muted d-block">Precio unitario con IVA incluido</small>
                                    </p>
                                </div>

                                <div class="col-12 col-md-4">
                                    <p class="mb-0 unique-product-total-price1 bg-light p-2 rounded text-md-center">
                                        <strong>Total:</strong>
                                        ${{ number_format($precioDescontado * $item->quantity, 2, '.', ',') }} MXN
                                    </p>
                                </div>
                            </div>

                            @if ($item->discount > 0)
                                <div class="mt-2">
                                    <span class="badge bg-danger">Descuento aplicado del:
                                        {{ $item->discount }}%</span>
                                </div>
                            @endif

                            <div class="mt-2">
                                @if ($item->grupo_iva === 'IVA16')
                                    <span class="badge bg-success">Este producto tiene IVA 16%</span>
                                @elseif ($item->grupo_iva === 'IVA0')
                                    <span class="badge bg-warning text-dark">Este producto tiene IVA 0</span>
                                @endif
                            </div>

                            @if (
                                !$item->allow_local_shipping ||
                                    !$item->allow_paqueteria_shipping ||
                                    !$item->allow_store_pickup ||
                                    !$item->allow_cobrar_shipping)
                                <div class="mt-2">

                                    @if (!$item->allow_local_shipping)
                                        <p class="text-danger mb-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            Este producto no está disponible para <strong>Envío Local</strong>.
                                        </p>
                                    @endif
                                    @if (!$item->allow_paqueteria_shipping)
                                        <p class="text-danger mb-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            Este producto no está disponible para <strong>Envío por Paquetería</strong>.
                                        </p>
                                    @endif
                                    @if (!$item->allow_store_pickup)
                                        <p class="text-danger mb-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            Este producto no está disponible para <strong>Recoger en Tienda</strong>.
                                        </p>
                                    @endif
                                    @if (!$item->allow_cobrar_shipping)
                                        <p class="text-danger mb-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            Este producto no está disponible para <strong>Envío por Cobrar</strong>.
                                        </p>
                                    @endif

                                    @php
                                        $allowedMethods = [];
                                        if ($item->allow_local_shipping) {
                                            $allowedMethods[] = '<strong>Envío Local</strong>';
                                        }
                                        if ($item->allow_paqueteria_shipping) {
                                            $allowedMethods[] = '<strong>Envío por Paquetería</strong>';
                                        }
                                        if ($item->allow_store_pickup) {
                                            $allowedMethods[] = '<strong>Recoger en Tienda</strong>';
                                        }
                                        if ($item->allow_cobrar_shipping) {
                                            $allowedMethods[] = '<strong>Envío por Cobrar</strong>';
                                        }
                                    @endphp

                                    @if (!empty($allowedMethods))
                                        <p class="text-success bg-warning mb-1">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Este producto está disponible para: {!! implode(', ', $allowedMethods) !!}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
            @if ($shippmentExists && $shippment->ShipmentMethod === 'EnvioPorCobrar')
                <div class="unique-cart-item row mb-4 p-4 border-bottom shadow-sm rounded bg-cobrar-envio">
                    <div class="col-12 col-md-2 text-center mb-3 mb-md-0">
                        <img src="{{ asset('storage/img/envio_entrega/cobrar.png') }}"
                            class="img-thumbnail cobrar-envio-img" alt="Envío por Cobrar">
                    </div>
                    <div class="col-12 col-md-10">
                        <h5 class="text-uppercase text-dark fw-bold">Envío por Cobrar</h5>
                        <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                            <p class="text-muted"><strong>Cantidad:</strong> 1 Envío</p>
                            <p class="text-primary fw-bold p-2 bg-light rounded text-center cobrar-envio-costo">
                                <strong>Costo de Envío:</strong> A pagar al recibir
                            </p>
                            <button class="btn btn-danger btn-sm remove-shipping ms-auto">
                                <i class="bi bi-trash"> Eliminar envío y <br> seleccionar otro método</i>
                            </button>
                        </div>

                        <div class="shipping-info mt-3 p-3 bg-white border-start border-info rounded">
                            <p class="text-muted mb-1"><i class="bi bi-info-circle me-2"></i><strong>Estado del
                                    Envío:</strong> Por Confirmar</p>
                            <p class="text-dark mb-1"><i class="bi bi-clock me-2"></i><strong>Tiempo de
                                    entrega:</strong> 2 a 7 días hábiles</p>
                            <p class="text-danger mb-1"><i
                                    class="bi bi-exclamation-circle me-2"></i><strong>Nota:</strong> El costo de envío
                                aún no a sido determinado y será cobrado al momento de la entrega por la empresa de
                                paquetería.</p>
                            <p class="text-secondary mt-2"><i
                                    class="bi bi-person-check me-2"></i><strong>Aviso:</strong> Asegúrese de que
                                alguien esté disponible para recibir y pagar el envío.</p>
                        </div>
                    </div>
                </div>
            @endif
            @if ($shippmentExists && $shippment->ShipmentMethod === 'EnvioLocal')
                @php
                    $shippingCostSinIVA = $shippingCostIVA / 1.16;
                @endphp
                <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm rounded bg-cobrar-envio">
                    <div class="item-image-wrapper col-12 col-md-2 text-center mb-3 mb-md-0">
                        <img src="{{ asset('storage/img/envio_entrega/265.jpg') }}" class="img-thumbnail"
                            alt="Envío" style="object-fit: cover; width: 100%; height: 100%;">
                    </div>
                    <div class="col-12 col-md-10 d-flex flex-column">
                        <h5 class="mb-2 text-uppercase" style="font-weight: 600; color: #000000;">ENVÍO LOCAL</h5>
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex flex-column flex-md-row align-items-md-center w-100">
                                <p class="mb-2 mb-md-0 me-4 text-muted"><strong>Cantidad:</strong> 1 Envío</p>
                                <p class="mb-2 mb-md-0 unique-product-price bg-light p-2 rounded me-4 text-primary flex-grow-1"
                                    style="font-weight: 600;">
                                    <strong>Precio unitario:</strong>
                                    ${{ number_format($shippment->final_price - $shippingCostSinIVA, 2, '.', ',') }} MXN
                                </p>
                                <p class="mb-2 mb-md-0 unique-product-total-price bg-light p-2 rounded text-primary flex-grow-1"
                                    style="font-weight: 600;">
                                    <strong>Total con IVA:</strong>
                                    ${{ number_format($shippment->final_price, 2, '.', ',') }} MXN
                                </p>
                                <div class="unique-item-actions2 ms-auto">
                                    <button class="btn btn-danger btn-sm remove-shipping">
                                        <i class="bi bi-trash"> Eliminar envío y <br> seleccionar otro método</i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="shipping-info mt-3 p-2 bg-light border-start border-primary">
                            <p class="mb-1 text-dark" style="font-weight: 500;">
                                <i class="bi bi-clock-history me-2"></i>
                                <strong>Tiempo de entrega:</strong> 1 a 3 días hábiles.
                            </p>
                            <p class="mb-1 text-danger" style="font-weight: 500;">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <strong>Nota:</strong> Los tiempos de entrega pueden sufrir un retraso debido a la
                                saturación de envíos que presenten los repartidores.
                            </p>
                            <p class="mb-0 text-secondary" style="font-weight: 500;">
                                <i class="bi bi-arrow-repeat me-2"></i>
                                <strong>Costo por reenvío:</strong> $200.00 MXN
                            </p>
                            <p class="mt-2 text-secondary" style="font-weight: 500;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Aviso:</strong> Lanceta HG no es responsable de llevar los artículos a un
                                piso superior o inferior, y no incluye desempaque o instalación.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            @if ($shippmentExists && $shippment->ShipmentMethod === 'RecogerEnTienda')
                <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm rounded bg-cobrar-envio">
                    <div class="item-image-wrapper col-12 col-md-2 text-center mb-3 mb-md-0">
                        <img src="{{ asset('storage/img/envio_entrega/229.jpg') }}" class="img-thumbnail"
                            alt="Recoger en Tienda" style="object-fit: cover; width: 100%; height: 100%;">
                    </div>
                    <div class="col-12 col-md-10 d-flex flex-column">
                        <h5 class="mb-2 text-uppercase" style="font-weight: 600; color: #000000;">RECOGER EN
                            TIENDA</h5>
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <p class="mb-2 mb-md-0 text-success bg-light p-2 rounded me-4 text-primary flex-grow-1"
                                style="font-weight: 600;">
                                Este Método de Entrega No Genera Costo de Envío
                            </p>
                            <div class="unique-item-actions2 ms-auto">
                                <button class="btn btn-danger btn-sm remove-shipping">
                                    <i class="bi bi-trash"> Eliminar envío y <br> seleccionar otro método</i>
                                </button>
                            </div>
                        </div>



                        <div class="shipping-info mt-3 p-2 bg-light border-start border-primary">
                            <p class="mb-1 text-dark" style="font-weight: 500;">
                                <i class="bi bi-shop me-2"></i>
                                <strong>Tienda de Recogida:</strong>
                                {{ $shippment->store_name ?? 'No especificado' }}
                            </p>
                            <p class="mb-1 text-dark" style="font-weight: 500;">
                                <i class="bi bi-geo-alt me-2"></i>
                                <strong>Dirección:</strong>
                                {{ $shippment->store_address ?? 'No especificada' }}
                            </p>
                            <div class="mb-1 text-dark" style="font-weight: 500;">
                                <i class="bi bi-calendar-check me-2"></i>
                                <strong>Fecha de Recogida:</strong>
                                @if (!empty($shippment->pickup_date))
                                    {{ $shippment->pickup_date }}
                                @else
                                    <span class="text-muted text-danger">La fecha de recogida se confirmará pronto</span>
                                @endif
                            </div>
                            <div class="mb-1 text-dark" style="font-weight: 500;">
                                <i class="bi bi-clock me-2 "></i>
                                <strong>Hora de Recogida:</strong>
                                @if (!empty($shippment->pickup_time))
                                    {{ $shippment->pickup_time }}
                                @else
                                    <span class="text-muted text-danger">La hora de recogida se confirmará pronto</span>
                                @endif
                            </div>

                            <p class="mt-2 text-secondary" style="font-weight: 500;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Aviso:</strong> Por favor, asegúrate de llevar una identificación válida
                                para recoger tu pedido.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            @if ($shippmentExists && $shippment->ShipmentMethod === 'EnvioPorPaqueteria')
                <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm rounded bg-cobrar-envio">
                    <div class="item-image-wrapper col-12 col-md-2 text-center mb-3 mb-md-0">
                        <img src="{{ asset('storage/img/envio_entrega/paqueteexpress.jpg') }}" class="img-thumbnail"
                            alt="Envío por Paquetería" style="object-fit: cover; width: 100%; height: 100%;">
                    </div>
                    <div class="col-12 col-md-10 d-flex flex-column">
                        <h5 class="mb-2 text-uppercase" style="font-weight: 600; color: #000000;">ENVÍO POR
                            PAQUETERÍA</h5>
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex flex-column flex-md-row align-items-md-center w-100">
                                <p class="mb-2 mb-md-0 me-4 text-muted"><strong>Cantidad:</strong> 1 Envío</p>
                                <div class="me-4 flex-grow-1"></div>
                                <p class="mb-2 mb-md-0 unique-product-total-price bg-light p-2 rounded text-primary flex-grow-1"
                                    style="font-weight: 600;">
                                    <strong>Total con IVA:</strong>
                                    ${{ number_format($shippment->final_price, 2, '.', ',') }} MXN
                                    @php
                                        
                                    @endphp
                                </p>
                                <div class="unique-item-actions2 ms-auto">
                                    <button class="btn btn-danger btn-sm remove-shipping">
                                        <i class="bi bi-trash"> Eliminar envío y <br> seleccionar otro método</i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="shipping-info mt-3 p-2 bg-light border-start border-primary">
                            <p class="mb-1 text-dark" style="font-weight: 500;">
                                <i class="bi bi-truck me-2"></i>
                                <strong>Tiempo de entrega:</strong> 2 a 5 días hábiles.
                            </p>
                            <p class="text-danger mb-1" style="font-weight: 500;">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <strong>Nota:</strong> Los tiempos de entrega pueden variar dependiendo de la
                                disponibilidad y saturación del servicio.
                            </p>
                            <p class="mt-2 text-secondary" style="font-weight: 500;">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Aviso:</strong> La entrega se realiza directamente en la dirección
                                seleccionada. Asegúrate de que alguien esté disponible para recibir el paquete.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if ($eligibleCartItems->isEmpty())
            <div class="alert alert-danger text-center">
                <h4><i class="bi bi-exclamation-triangle-fill"></i> No hay productos disponibles para el método de
                    envío seleccionado.</h4>
                <p>Por favor, elija un método de envío diferente o modifique los productos en su carrito.</p>
                <button class="btn btn-warning btn-lg remove-shipping ms-auto bigger-button shadow-lg p-3 ">
                    <i class="bi bi-trash"></i> Eliminar envío y seleccionar otro método de envío disponible.
                </button>
            </div>
        @endif
        <div class="unique-cart-summary p-4 bg-white rounded shadow-sm mt-4 border">
            <h4 class="mb-4 text-primary fw-bold">Resumen de su compra</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Subtotal de productos(sin IVA):</span>
                        <span class="fw-bold text-dark">${{ number_format($subtotalSinIVA, 2, '.', ',') }}
                            MXN</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Descuento aplicado(sin IVA):</span>
                        <span class="fw-bold text-success">- ${{ number_format($totalDescuentoSinIVA, 2, '.', ',') }}
                            MXN</span>
                    </div>

                    {{-- <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Costo de Envío (sin IVA):</span>
                        @if ($shippmentExists && $shippment->ShipmentMethod === 'EnvioPorCobrar')
                            <p class="text-danger fw-bold">* El costo de envío aún no ha sido calculado y será cobrado
                                al momento de la entrega.</p>
                        @else
                            <span class="fw-bold text-dark">${{ number_format($shippingCostSinIVA, 2, '.', ',') }}
                                MXN</span>
                        @endif
                    </div> --}}
                    {{--
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total sin IVA:</span>
                        <span class="fw-bold text-dark">${{ number_format($totalSinIVA, 2, '.', ',') }} MXN</span>
                    </div> --}}

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">IVA (16%):</span>
                        <span class="fw-bold text-dark">${{ number_format($ivaTotal, 2, '.', ',') }} MXN</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0 text-dark fw-bold">Total a pagar:</h4>
                        <h4 class="mb-0 text-primary fw-bold">${{ number_format($totalFinal, 2, '.', ',') }} MXN</h4>
                    </div>
                </div>

                <div class="col-md-6">
                    @if (!$shippmentExists)
                        <div id="shipping-alert" class="alert alert-warning alert-shake">
                            <strong>Por favor, seleccione un método de envío para proceder al pago.</strong>
                        </div>
                    @endif
                    @if ($shippmentExists)
                        <div class="card border border-info shadow-sm" style="background-color: #f0f4f8;">
                            <div class="card-body p-4">
                                <form id="paymentForm" action="{{ url('/cart/proceed-to-payment') }}"
                                    method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="contactName" class="form-label fw-bold text-primary">Nombre de
                                            Contacto</label>
                                        <input type="text" id="contactName" name="contactName"
                                            class="form-control border-primary" maxlength="50"
                                            value="{{ old('contactName', $contactName ?? $user->name) }}"
                                            placeholder="(Obligatorio)">
                                        <div class="invalid-feedback">Por favor, proporcione un nombre completo de
                                            contacto.</div>
                                    </div>


                                    <div class="form-group mb-3">
                                        <label for="contactPhone" class="form-label fw-bold text-primary">Teléfono de
                                            Contacto</label>
                                        <input type="text" id="contactPhone" name="contactPhone"
                                            class="form-control border-primary" maxlength="15"
                                            value="{{ old('contactPhone', $user->phone ?? '') }}"
                                            placeholder="(Obligatorio)">
                                        <div class="invalid-feedback">Por favor, proporcione un teléfono de contacto
                                            válido.</div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <hr class="my-4">
            @if ($eligibleCartItems->isNotEmpty())
                <div class="total-section p-3 bg-light rounded d-flex justify-content-between align-items-center mb-4"
                    style="background-color: #f8f9fa; border-left: 5px solid #007bff;">
                    <h4 class="mb-0 text-dark fw-bold">Total a pagar:</h4>
                    <h4 class="mb-0 text-primary fw-bold">${{ number_format($totalFinal, 2, '.', ',') }} MXN</h4>
                </div>
                @if ($shippmentExists && $shippment->ShipmentMethod === 'EnvioPorCobrar')
                    <p class="text-danger fw-bold">* El costo de envío será cobrado al momento de la entrega.</p>
                @endif

                @if ($shippmentExists)
                    <div class="d-grid gap-2 mt-4">
                        <form id="checkoutForm" action="{{ url('/cart/proceed-to-payment') }}" method="POST">
                            @csrf
                            <button type="button" id="proceedToPaymentBtn"
                                class="btn btn-lg btn-success shadow-lg btn-block font-weight-bold">
                                <i class="bi bi-cash-coin me-2"></i> Continuar al Pago
                            </button>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>

<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Términos y Condiciones</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;" id="termsContent">
                @include('partials.tyc')
            </div>
            <div class="modal-footer">
                <button type="button" id="acceptTermsBtn" class="btn btn-primary" disabled>Aceptar y Proceder al
                    Pago</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($(".alert-shake").length > 0) {
            $(".alert-shake").fadeOut(500).fadeIn(500).fadeOut(500).fadeIn(500);

            function shakeElement(element) {
                var interval = 100;
                var distance = 10;
                var times = 4;

                $(element).css('position', 'relative');
                for (var i = 0; i < (times + 1); i++) {
                    $(element).animate({
                        left: ((i % 2 == 0 ? distance : distance * -1))
                    }, interval);
                }
                $(element).animate({
                    left: 0
                }, interval);
            }
            shakeElement(".alert-shake");
            setInterval(function() {
                shakeElement(".alert-shake");
            }, 5000);
        }
    });

    $(document).ready(function() {
        $('.remove-shipping').on('click', function(e) {
            e.preventDefault();

            removeShipping();
        });

        function removeShipping() {
            $.ajax({
                type: "POST",
                url: "/cart/remove-shipping",
                data: {
                    _token: $('meta[name="csrf-token"]').attr(
                        'content')
                },
                success: function(response) {
                    console.log('Método de envío eliminado');
                    localStorage.setItem('scrollToShippingSelector', 'true');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error al eliminar el método de envío:', error);
                    alert('Hubo un error al eliminar el método de envío.');
                }
            });
        }

        if (localStorage.getItem('scrollToShippingSelector') === 'true') {

            var shippingSelector = $('#general-shipping-block');
            if (shippingSelector.length) {
                $('html, body').animate({
                    scrollTop: shippingSelector.offset().top -
                        150
                }, 250);
            }
            localStorage.removeItem('scrollToShippingSelector');
        }

    });

    $(document).ready(function() {
        if (localStorage.getItem('showShippingAlert') === 'true') {
            const alertElement = document.getElementById('shipping-alert');
            alertElement.classList.remove('d-none');
            window.scrollTo(0, 0);
            localStorage.removeItem('showShippingAlert');
        }
    });
</script>

<script>
    $(document).ready(function() {
        // const quantityControls = document.querySelectorAll('.quantity-controls');
        // let maxQuantity = parseInt($('.quantity-increase').data('max-quantity'), 10);
        // let productCode = $('.quantity-decrease').data('product.code');
        // let quantity = $('.quantity-input').val();
        // $('.quantity-increase').on('click', function() {
        //     let currentValue = parseInt(quantity, 10);
        //     if (currentValue < maxQuantity) {
        //         updateCartQuantity(1, productCode);
        //     }
        // });

        // function updateButtonStates(currentValue) {
        //     if (currentValue <= 1) {
        //         decreaseButton.disabled = true;
        //     } else {
        //         decreaseButton.disabled = false;
        //     }

        //     if (currentValue >= maxQuantity) {
        //         increaseButton.disabled = true;
        //     } else {
        //         increaseButton.disabled = false;
        //     }
        // }
        // quantityControls.forEach(control => {
        //     // const decreaseButton = control.querySelector('.quantity-decrease');
        //     // const increaseButton = control.querySelector('.quantity-increase');
        //     // const quantityInput = control.querySelector('.quantity-input');




        //     updateButtonStates(parseInt(quantityInput.value, 10));
        //     decreaseButton.addEventListener('click', function() {
        //         let currentValue = parseInt(quantityInput.value, 10);
        //         if (currentValue > 1) {
        //             updateCartQuantity(-1, productCode);
        //         } else if (currentValue === 1) {
        //             removeCartItem(productCode);
        //         }
        //     });

        //     increaseButton.addEventListener('click', function() {

        //     });


        //     quantityInput.addEventListener('change', function() {
        //         updateButtonStates(parseInt(quantityInput.value, 10));
        //     });
        // });

        function updateCartQuantity(quantityChange, productCode) {
            $.ajax({
                type: 'POST',
                url: '/cart/update-quantity',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    quantity: quantityChange,
                    product_code: productCode
                },
                success: function(response) {
                    removeShipping();
                    location.reload();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Hubo un error al intentar actualizar la cantidad.');
                }
            });
        }

        function removeCartItem(productCode) {
            $.ajax({
                type: "POST",
                url: "/cart/remove",
                data: {
                    no_s: productCode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    removeShipping();
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error al eliminar el ítem del carrito:', error);
                    alert('Error al eliminar el ítem del carrito.');
                }
            });
        }

        function removeShipping() {
            $.ajax({
                type: "POST",
                url: "/cart/remove-shipping",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Método de envío eliminado');

                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error al eliminar el método de envío:', error);
                    alert('Error al eliminar el método de envío.');
                }
            });
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('.quantity-input').on('change', function() {
            var productCode = $(this).data('product-code');
            var maxQuantity = parseInt($(this).data('max-quantity'), 10);
            var newQuantity = parseInt($(this).val(), 10);

            if (newQuantity == 0) {
                $(this).parents('.unique-cart-item').find('.remove-from-cart1').click();
                return;
            }



            if (newQuantity > maxQuantity) {
                $(this).val(maxQuantity);
                // console.error('La cantidad máxima permitida es ' + maxQuantity);
                updateQuantity(productCode, maxQuantity);
            } else {
                // Actualizar la cantidad en el servidor
                updateQuantity(productCode, newQuantity);
            }
        });
        $('.remove-from-cart1').on('click',function(){
            let no = $(this).data('nos');
            removeItem(no);
        });

        function removeItem(itemNoS) {
            $.ajax({
                type: "POST",
                url: "/cart/remove",
                data: {
                    no_s: itemNoS,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Item removed:', response);
                    localStorage.setItem('showShippingAlert', 'true');
                    location
                        .reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error al eliminar el ítem del carrito:',
                        error);
                    alert('Error al eliminar el ítem del carrito.');
                }
            });
        }

        $('.update-quantity-btn').on('click', function() {
            $(this).parent().find('.quantity-input').change();
        });

        function updateQuantity(productCode, quantity) {
            $.ajax({
                type: 'POST',
                url: '/cart/update-quantity',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    quantity: quantity,
                    product_code: productCode
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else if (!response.success && response.maxQuantity) {
                        showError(response.message);
                        $(`input[data-product-code="${productCode}"]`).val(response.maxQuantity)
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    // alert('Hubo un error al intentar actualizar la cantidad.');
                }
            });
        }

        function showError(message) {
            const errorAlert = document.createElement('div');
            errorAlert.classList.add('alert', 'alert-danger');
            errorAlert.textContent = message;
            document.getElementById('unique-cart-container').prepend(errorAlert);
            setTimeout(() => errorAlert.remove(), 3000);
        }
    });
    // document.addEventListener('DOMContentLoaded', function() {
    //     document.querySelectorAll('.quantity-input').forEach(input => {
    //         input.addEventListener('change', function() {
    //             const productCode = this.getAttribute('data-product-code');
    //             const maxQuantity = parseInt(this.getAttribute('data-max-quantity'), 10);
    //             const newQuantity = parseInt(this.value, 10);

    //             if (newQuantity > maxQuantity) {
    //                 this.value = maxQuantity;
    //                 showError('La cantidad máxima disponible es ' + maxQuantity);
    //                 return;
    //             }

    //             updateQuantity(productCode, newQuantity);
    //         });
    //     });
    //     function updateQuantity(productCode, quantity) {
    //         $.ajax({
    //             type: 'POST',
    //             url: '/cart/update-quantity',
    //             data: {
    //                 _token: $('meta[name="csrf-token"]').attr('content'),
    //                 quantity: quantity,
    //                 product_code: productCode
    //             },
    //             success: function(response) {
    //                 if (response.success) {
    //                     location.reload();
    //                 } else if (!response.success && response.maxQuantity) {
    //                     showError(response.message);
    //                     document.querySelector(`input[data-product-code="${productCode}"]`).value =
    //                         response.maxQuantity;
    //                 }
    //             },
    //             error: function(xhr) {
    //                 console.error(xhr.responseText);
    //                 alert('Hubo un error al intentar actualizar la cantidad.');
    //             }
    //         });
    //     }

    //     function showError(message) {
    //         const errorAlert = document.createElement('div');
    //         errorAlert.classList.add('alert', 'alert-danger');
    //         errorAlert.textContent = message;
    //         document.getElementById('unique-cart-container').prepend(errorAlert);
    //         setTimeout(() => errorAlert.remove(), 3000);
    //     }

    // });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const storePickupBlock = document.getElementById('store-pickup-block');
        const localShippingBlock = document.getElementById('local-shipping-block');
        const paqueteriaShippingBlock = document.getElementById('paqueteria-shipping-block');
        const cobrarShippingBlock = document.getElementById('cobrar-shipping-block');
        const shippingTypeSelector = document.getElementById('shipping-type-selector');
        const loader = document.getElementById('loader');

        shippingTypeSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const shippingName = selectedOption.value;
            storePickupBlock.style.display = 'none';
            localShippingBlock.style.display = 'none';
            paqueteriaShippingBlock.style.display = 'none';
            cobrarShippingBlock.style.display = 'none';
            loader.style.display = 'block';

            if (shippingName === 'Recoger en Tienda') {
                verificarExistenciasEnTiendas();
            } else if (shippingName === 'Envío Local') {
                localShippingBlock.style.display = 'block';
                loader.style.display = 'none';
            } else if (shippingName === 'Envío por Paquetería') {
                paqueteriaShippingBlock.style.display = 'block';
                loader.style.display = 'none';
            } else if (shippingName === 'Envío por Cobrar') {
                cobrarShippingBlock.style.display = 'block';
                loader.style.display = 'none';
            }
        });
    });
    
    function verificarExistenciasEnTiendas() {
        fetch('{{ route('verificarExistencias') }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                const tiendaSelector = document.getElementById('tienda-selector');
                const mensajeSinTiendas = document.getElementById('mensaje-sin-tiendas');
                tiendaSelector.innerHTML = '';

                if (data.tiendas.length > 0) {
                    data.tiendas.forEach(tienda => {
                        const option = document.createElement('option');
                        option.value = tienda.id;
                        option.textContent = `${tienda.nombre} - ${tienda.direccion}`;
                        option.setAttribute('data-direccion', tienda.direccion);
                        option.setAttribute('data-telefono', tienda.telefono);
                        option.setAttribute('data-horario-semana', tienda.horario_semana);
                        option.setAttribute('data-horario-sabado', tienda.horario_sabado);
                        option.setAttribute('data-google-maps-url', tienda.google_maps_url);
                        tiendaSelector.appendChild(option);
                    });

                    tiendaSelector.dispatchEvent(new Event('change'));
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('store-pickup-block').style.display = 'block';
                    mensajeSinTiendas.style.display = 'none';
                } else {

                    const noTiendasOption = document.createElement('option');
                    noTiendasOption.value = '';
                    noTiendasOption.textContent = 'No hay tiendas disponibles con todos los productos';
                    noTiendasOption.disabled = true;
                    tiendaSelector.appendChild(noTiendasOption);
                    mensajeSinTiendas.innerHTML =
                        '<strong>Atención:</strong> No hay tiendas disponibles para los productos en su carrito.';
                    mensajeSinTiendas.style.display = 'block';
                    document.getElementById('loader').style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error al verificar existencias en tiendas:', error);
                document.getElementById('loader').style.display = 'none';
            });


    }


    document.addEventListener('DOMContentLoaded', function() {
        const tiendaSelector = document.getElementById('tienda-selector');
        const direccionElement = document.querySelector('.store-address');
        const telefonoElement = document.querySelector('.store-phone');
        const horarioElement = document.querySelector('.store-hours');
        const mapaIframe = document.querySelector('.store-map');

        tiendaSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value !== '') {
                direccionElement.innerText = selectedOption.getAttribute('data-direccion');
                telefonoElement.innerText = selectedOption.getAttribute('data-telefono');
                horarioElement.innerHTML = `
                    Lunes a Viernes: ${selectedOption.getAttribute('data-horario-semana')}<br>
                    Sábado: ${selectedOption.getAttribute('data-horario-sabado')}
                `;

                mapaIframe.src = selectedOption.getAttribute('data-google-maps-url');
            }
        });
    });


    document.addEventListener('DOMContentLoaded', function() {
        const tiendaSelector = document.getElementById('tienda-selector');
        const pickupDateInput = document.getElementById('pickup-date');
        const pickupTimeInput = document.getElementById('pickup-time');

        const storeIdInput = document.getElementById('store_id');
        const pickupDateHiddenInput = document.getElementById('pickup_date_hidden');
        const pickupTimeHiddenInput = document.getElementById('pickup_time_hidden');


        tiendaSelector.addEventListener('change', function() {
            storeIdInput.value = tiendaSelector.value;
        });

        pickupDateInput.addEventListener('change', function() {
            pickupDateHiddenInput.value = pickupDateInput.value;
        });

        pickupTimeInput.addEventListener('change', function() {
            pickupTimeHiddenInput.value = pickupTimeInput.value;
        });
    });


    // document.addEventListener('DOMContentLoaded', function() {
    //     document.querySelectorAll('.remove-from-cart1').forEach(function(button) {
    //         button.addEventListener('click', function(e) {
    //             e.preventDefault();

    //         });
    //     });
    // });


    $(document).ready(function() {
        $('.remove-shipping').on('click', function(e) {
            e.preventDefault();
            removeShipping();
        });

        function removeShipping() {
            let token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: "POST",
                url: "/cart/remove-shipping",
                data: {
                    _token: token
                },
                success: function(response) {
                    console.log('Método de envío eliminado');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error al eliminar el método de envío:', error);
                    alert('Hubo un error al eliminar el método de envío.');
                }
            });
        }
    });
</script>



<script>
    $(document).ready(function() {
        function validateFields() {
            var contactName = $('#contactName').val().trim();
            var contactPhone = $('#contactPhone').val().trim();
            if (contactName === '' || contactPhone === '') {
                $('#proceedToPaymentBtn').prop('disabled', true);
            } else {
                $('#proceedToPaymentBtn').prop('disabled',
                    false);
            }
        }

        $('#contactName, #contactPhone').on('input', function() {
            validateFields();
        });

        $('#proceedToPaymentBtn').on('click', function(e) {
            e.preventDefault();

            var contactName = $('#contactName').val().trim();
            var contactPhone = $('#contactPhone').val().trim();

            if (contactPhone === '' || contactName === '') {

                if (contactPhone === '') {
                    $('#contactPhone').addClass('is-invalid');
                    $('#contactPhone').next('.invalid-feedback').show();
                } else {
                    $('#contactPhone').removeClass('is-invalid');
                    $('#contactPhone').next('.invalid-feedback').hide();
                }

                if (contactName === '') {
                    $('#contactName').addClass('is-invalid');
                    $('#contactName').next('.invalid-feedback').show();
                } else {
                    $('#contactName').removeClass('is-invalid');
                    $('#contactName').next('.invalid-feedback').hide();
                }

            } else {
                $('#contactName').removeClass('is-invalid');
                $('#contactPhone').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                $('#termsModal').modal('show');
            }
        });
    });
</script>



<script>
    $(document).ready(function() {
        $('#termsContent').on('scroll', function() {
            var $termsContent = $(this);
            var scrollTop = $termsContent.scrollTop();
            var innerHeight = $termsContent.innerHeight();
            var scrollHeight = $termsContent[0].scrollHeight;
            console.log('Scroll Top:', scrollTop);
            console.log('Inner Height:', innerHeight);
            console.log('Scroll Height:', scrollHeight);
            if (scrollTop + innerHeight >= scrollHeight -
                10) {
                $('#acceptTermsBtn').prop('disabled', false);
            }
        });

        $('#acceptTermsBtn').on('click', function() {
            localStorage.setItem('termsAccepted', 'true'); // Para referencia local
            $('#termsModal').modal('hide');
            $('#paymentForm').submit();
        });


        $('#termsModal').on('hidden.bs.modal', function() {
            $('#termsContent').scrollTop(0);
            $('#acceptTermsBtn').prop('disabled', true);
        });
    });
</script>
<script>
    $(document).ready(function() {
        function gentleShake(selector, times = 2, distance = 5, speed = 200) {
            for (let i = 0; i < times; i++) {
                $(selector)
                    .animate({
                        marginLeft: `-${distance}px`
                    }, speed)
                    .animate({
                        marginLeft: `${distance}px`
                    }, speed);
            }
            $(selector).animate({
                marginLeft: '0px'
            }, speed);
        }

        gentleShake('.non-eligible-message', 2, 5, 200);
        setInterval(function() {
            gentleShake('.non-eligible-message', 2, 5, 200);
        }, 2000);
    });
</script>

<style>
    .image-container1 {
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .image-container1:hover {
        transform: scale(1.1);

    }

    .text-primary {
        color: #007bff;
    }

    .bg-warning {
        background-color: #fff3cd !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .font-weight-bold {
        font-weight: bold !important;
    }

    .list-group-item {
        cursor: pointer;
        padding: 15px;
        border-radius: 5px;
        transition: background-color 0.2s;
    }

    .list-group-item:hover {
        background-color: #f1f1f1;
    }

    .list-group-item input[type="radio"] {
        margin-right: 10px;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        display: none;
    }
</style>


<style>
    .loader {
        z-index: 9999;
        position: relative;
        width: 6em;
        height: 6em;
        transform: rotate(165deg);
    }

    .loader:before,
    .loader:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        display: block;
        width: 1.5em;
        height: 1.5em;
        border-radius: 0.75em;
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
            width: 1.5em;
            box-shadow: 3em -1.5em rgba(225, 20, 98, 0.75), -3em 1.5em rgba(111, 202, 220, 0.75);

        }

        35% {
            width: 6em;
            box-shadow: 0 -1.5em rgba(225, 20, 98, 0.75), 0 1.5em rgba(111, 202, 220, 0.75);
        }

        70% {
            width: 1.5em;
            box-shadow: -3em -1.5em rgba(225, 20, 98, 0.75), 3em 1.5em rgba(111, 202, 220, 0.75);
        }

        100% {
            box-shadow: 3em -1.5em rgba(225, 20, 98, 0.75), -3em 1.5em rgba(111, 202, 220, 0.75);
        }
    }

    @keyframes after6 {
        0% {
            height: 1.5em;
            box-shadow: 1.5em 3em rgba(61, 184, 143, 0.75), -1.5em -3em rgba(233, 169, 32, 0.75);
        }

        35% {
            height: 6em;
            box-shadow: 1.5em 0 rgba(61, 184, 143, 0.75), -1.5em 0 rgba(233, 169, 32, 0.75);
        }

        70% {
            height: 1.5em;
            box-shadow: 1.5em -3em rgba(61, 184, 143, 0.75), -1.5em 3em rgba(233, 169, 32, 0.75);
        }

        100% {
            box-shadow: 1.5em 3em rgba(61, 184, 143, 0.75), -1.5em -3em rgba(233, 169, 32, 0.75);
        }
    }

    .loader {
        position: absolute;
        top: calc(50% - 3em);
        left: calc(50% - 3em);
    }

    #proceedToPaymentBtn {
        background-color: #28a745;
        border-color: #28a745;
        font-size: 1.25rem;
        padding: 15px 30px;
        text-transform: uppercase;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: inline-block;
        width: 30%;
    }
    @media (max-width: 900px) {
        #proceedToPaymentBtn {
            background-color: #28a745;
        border-color: #28a745;
        font-size: 1.25rem;
        padding: 15px 30px;
        text-transform: uppercase;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: inline-block;
        width: 100%;
    }
}


    #proceedToPaymentBtn:hover {
        background-color: #218838;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }


    #proceedToPaymentBtn i {
        font-size: 1.5rem;
        margin-right: 10px;
    }


    .alert-shake {
        background-color: #ffe69c;
        border: 2px solid #fad400e1;
    }

    .non-eligible-message {
        background-color: #f8d7da;
        padding: 15px;
        border: 2px solid #f5c6cb;
        border-radius: 5px;
        position: relative;
        margin-bottom: 15px;
    }

    .non-eligible-message .text-danger {
        font-weight: bold;
    }

    .bg-cobrar-envio {
        background-color: #e0f7fa;
        border-left: 5px solid #26a69a;
    }

    #general-shipping-block {
        background-color: #f7f9fc;
        border: 1px solid #ffc107;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    #general-shipping-block h5 {
        font-weight: 600;
        color: #007bff;
    }

    .form-group .form-select.custom-select {
        background-color: #ffffff;
        border: 2px solid #ced4da;
        padding: 0.75rem;
        font-size: 1rem;
        color: #495057;
        font-weight: 500;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.05);
        transition: border-color 0.3s, box-shadow 0.3s;
        appearance: none;
        position: relative;
    }

    .form-group .form-select.custom-select:focus {
        border-color: #007bff;
        box-shadow: 0px 0px 8px rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .form-group i.custom-chevron {
        font-size: 1.25rem;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #007bff;
        pointer-events: none;
    }

    .mb-4.p-3.bg-white.rounded.shadow-sm {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0px 6px 16px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    #general-shipping-block div .form-group {
        margin-bottom: 1rem;
    }

    #general-shipping-block select.form-select:hover {
        background-color: #f0f4f8;
        border-color: #b0b8c2;
    }

    .bigger-button {
        font-size: 1rem;
        padding: 0.50rem 1.25rem;
        border-radius: 8px;
        line-height: 1.2;
    }
</style>
