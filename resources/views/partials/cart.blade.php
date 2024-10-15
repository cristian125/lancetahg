    <div class="container mt-5" id="unique-cart-container">
        <h1 class="mb-4 text-center" id="unique-cart-title">Carrito de Compras</h1>

        @if (
            $nonEligibleLocalShipping->isNotEmpty() ||
                $nonEligiblePaqueteriaShipping->isNotEmpty() ||
                $nonEligibleStorePickup->isNotEmpty())
            <div class="alert alert-warning" role="alert">
                <strong>Atención:</strong>
                Uno o más productos en tu carrito no son elegibles para los siguientes métodos de envío:
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
                </ul>
                Revisa los productos para elegir un método de envío adecuado.
            </div>
        @endif
@if (!$tieneDirecciones)
    <div class="alert alert-warning">
        <strong>No tienes direcciones registradas.</strong> 
        <a href="{{ url('/cuenta') }}">Haz clic aquí para agregar una dirección.</a>
    </div>
@endif



        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($cartItems->isEmpty())
            <div class="empty-cart text-center" id="unique-empty-cart">
                <img src="{{ asset('storage/iconos/carrito_vacio.png') }}" alt="Carrito vacío" class="img-fluid mb-4"
                    style="max-width: 200px;">
                <p>No hay productos en su carrito. <a href="{{ url('/') }}">Continúe comprando</a></p>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <strong>Nota:</strong> Si realizas cambios en tu carrito (como añadir o eliminar productos), el método
                de
                envío seleccionado será eliminado. Por favor, selecciona un nuevo método de envío antes de proceder al
                pago.
            </div>
            @if (count($Shippment) == 0)
                <div id="general-shipping-block" class="mb-4 p3 bg-ligh rounded shadow-sm">
                    <!-- Selector de tipo de envío -->
                    <div class="mb-4 p-3 bg-light rounded shadow-sm">
                        <h5 class="mb-3 text-primary"><i class="bi bi-truck"></i> Seleccione método de envío</h5>
                        <div class="form-group">
                            <select id="shipping-type-selector" class="form-select">
                                <option value="">(Seleccione una opción)</option>
                                @foreach ($envios as $envio)
                                    <option value="{{ $envio['name'] }}" data-price="{{ $envio['price'] }}">
                                        {{ $envio['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Bloque que se despliega para Recoger en Tienda -->
                    <div id="store-pickup-block" class="mb-4 p-3 bg-light rounded shadow-sm" style="display: none;">
                        @include('partials.envios.storepickup')
                    </div>

                    <!-- Bloque que se despliega para Envío Local -->
                    <div id="local-shipping-block" class="mb-4 p-3 bg-light rounded shadow-sm" style="display: none;">
                        @include('partials.envios.local')
                    </div>

                    <!-- Bloque que se despliega para Envío por Paquetería -->
                    <div id="paqueteria-shipping-block" class="mb-4 p-3 bg-light rounded shadow-sm"
                        style="display: none;">
                        @include('partials.envios.paqueteexpress')
                    </div>
                </div>
            @endif

            <!-- El resto de la vista sigue igual -->
            <div class="unique-cart-items" id="unique-cart-items">
                @if ($nonEligibleItems->isNotEmpty())
                    <div class="non-eligible-items ">
                        <h3>Productos no elegibles para el método de envío seleccionado:</h3>
                        @foreach ($nonEligibleItems as $item)
                            <!-- Código para mostrar cada producto no elegible -->
                            <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm bg-light">
                                <!-- Puedes reutilizar el mismo código que usas para mostrar los productos, quizás con estilos o mensajes diferentes -->
                                <!-- Imagen del producto -->
                                <div class="col-12 col-md-2 text-center mb-3 mb-md-0">
                                    <div class="image-container1">
                                        @if (isset($item->id))
                                            <a href="{{ url('/producto/' . $item->id) }}">
                                                <img src="{{ asset($item->image) }}" class="product-image1"
                                                    alt="{{ $item->description }}">
                                            </a>
                                        @else
                                            <img src="{{ asset($item->image) }}" class="product-image1"
                                                alt="{{ $item->description }}">
                                        @endif
                                    </div>
                                </div>
                                <!-- Detalles del producto -->
                                <div class="col-12 col-md-10 d-flex flex-column justify-content-between">
                                    <!-- Nombre del producto y botón de eliminar -->
                                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                                        <h5 class="mb-2">{{ $item->product_name }}</h5>
                                        <a href="#" class="btn btn-danger btn-sm remove-from-cart1"
                                            data-nos="{{ $item->product_code ?? $item->no_s }}">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                    <!-- Mensaje indicando que el producto no es elegible -->
                                    <p class="text-danger mb-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        Este producto no es elegible para el método de envío seleccionado y no se ha
                                        incluido en el total.
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @foreach ($eligibleCartItems as $item)
                    <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm">
                        <!-- Imagen del producto -->
                        <div class="col-12 col-md-2 text-center mb-3 mb-md-0">
                            <div class="image-container1">
                                @if (isset($item->id))
                                    <a href="{{ url('/producto/' . $item->id) }}">
                                        <img src="{{ asset($item->image) }}" class="product-image1"
                                            alt="{{ $item->description }}">
                                    </a>
                                @else
                                    <img src="{{ asset($item->image) }}" class="product-image1"
                                        alt="{{ $item->description }}">
                                @endif
                            </div>
                        </div>

                        <!-- Detalles del producto -->
                        <div class="col-12 col-md-10 d-flex flex-column justify-content-between">
                            <!-- Nombre del producto y botón de eliminar -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                                <h5 class="mb-2">{{ $item->product_name }}</h5>
                                <!-- Aquí se usa el nombre en lugar de la descripción -->
                                <a href="#" class="btn btn-danger btn-sm remove-from-cart1"
                                    data-nos="{{ $item->product_code ?? $item->no_s }}">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>

                            <!-- Cantidad, precio unitario, y total -->
                            <div class="row">
                                <div class="col-12 col-md-3 d-flex align-items-center mb-2 mb-md-0">
                                    <strong>Cantidad:</strong>
                                    <div class="quantity-controls d-flex ms-2">
                                        <button type="button" class="btn btn-sm quantity-decrease custom-btn-minus"
                                            data-product-code="{{ $item->product_code ?? $item->no_s }}"
                                            data-max-quantity="{{ $item->available_quantity }}">-</button>
                                        <input type="text" name="quantity"
                                            class="form-control text-center quantity-input"
                                            value="{{ $item->quantity }}" min="1"
                                            max="{{ $item->available_quantity }}" readonly>
                                        <button type="button" class="btn btn-sm quantity-increase custom-btn-plus"
                                            data-product-code="{{ $item->product_code ?? $item->no_s }}"
                                            data-max-quantity="{{ $item->available_quantity }}">
                                            +
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    @php
                                        $precioDescontado =
                                            $item->unit_price - $item->unit_price * ($item->discount / 100);
                                    @endphp
                                    <p class="mb-0 unique-product-price1 bg-light p-2 rounded">
                                        @if ($item->discount > 0)
                                            <span style="text-decoration: line-through; color: #888;">
                                                ${{ number_format($item->unit_price, 2, '.', ',') }} MXN
                                            </span>
                                            <span style="font-size: 1.2em; color: #28a745; font-weight: bold;">
                                                ${{ number_format($precioDescontado, 2, '.', ',') }} MXN
                                            </span>
                                        @else
                                            <span style="font-size: 1.2em; color: #333; font-weight: bold;">
                                                ${{ number_format($item->unit_price, 2, '.', ',') }} MXN
                                            </span>
                                        @endif
                                        <small class="text-muted d-block">Precio unitario con IVA incluido</small>
                                    </p>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 unique-product-total-price1 bg-light p-2 rounded">
                                        <strong>Total:</strong>
                                        ${{ number_format($item->final_price * $item->quantity, 2, '.', ',') }} MXN
                                    </p>
                                </div>
                            </div>

                            <!-- Etiqueta de descuento -->
                            @if ($item->discount > 0)
                                <div class="mt-2">
                                    <span class="badge bg-danger">Descuento aplicado del:
                                        {{ $item->discount }}%</span>
                                </div>
                            @endif

                            <!-- Indicaciones sobre métodos de envío no disponibles -->
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
                            </div>
                        </div>
                    </div>
                @endforeach


                @if ($shippmentExists && $Shippment->first()->ShipmentMethod === 'EnvioLocal')
                    @php
                        $shippingCostSinIVA = $shippingCostIVA / 1.16;
                    @endphp
                    <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm rounded"
                        style="background-color: #26d2b6;">
                        <!-- Imagen del envío -->
                        <div class="item-image-wrapper col-12 col-md-2 text-center mb-3 mb-md-0">
                            <img src="{{ asset('storage/img/envio_entrega/265.jpg') }}" class="img-thumbnail"
                                alt="Envío" style="object-fit: cover; width: 100%; height: 100%;">
                        </div>

                        <!-- Detalles del envío -->
                        <div class="col-12 col-md-10 d-flex flex-column">
                            <h5 class="mb-2 text-uppercase" style="font-weight: 600; color: #000000;">ENVÍO LOCAL</h5>
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex flex-column flex-md-row align-items-md-center w-100">
                                    <p class="mb-2 mb-md-0 me-4 text-muted"><strong>Cantidad:</strong> 1 Envío</p>
                                    <p class="mb-2 mb-md-0 unique-product-price bg-light p-2 rounded me-4 text-primary flex-grow-1"
                                        style="font-weight: 600;">
                                        <strong>Precio unitario:</strong>
                                        ${{ number_format($shippingCostSinIVA, 2, '.', ',') }} MXN
                                    </p>
                                    <p class="mb-2 mb-md-0 unique-product-total-price bg-light p-2 rounded text-primary flex-grow-1"
                                        style="font-weight: 600;">
                                        <strong>Precio de envío con IVA:</strong>
                                        ${{ number_format($shippingCostIVA, 2, '.', ',') }} MXN
                                    </p>
                                    <div class="unique-item-actions2 ms-auto">
                                        <button class="btn btn-danger btn-sm remove-shipping">
                                            <i class="bi bi-trash"></i>
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

                @if ($shippmentExists && $Shippment->first()->ShipmentMethod === 'RecogerEnTienda')
    <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm rounded"
        style="background-color: #26d2b6;">
        <!-- Imagen del envío -->
        <div class="item-image-wrapper col-12 col-md-2 text-center mb-3 mb-md-0">
            <img src="{{ asset('storage/img/envio_entrega/229.jpg') }}" class="img-thumbnail"
                alt="Recoger en Tienda" style="object-fit: cover; width: 100%; height: 100%;">
        </div>

        <!-- Detalles de la recogida -->
        <div class="col-12 col-md-10 d-flex flex-column">
            <h5 class="mb-2 text-uppercase" style="font-weight: 600; color: #000000;">RECOGER EN TIENDA</h5>
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <p class="mb-2 mb-md-0 text-success bg-light p-2 rounded me-4 text-primary flex-grow-1"
                    style="font-weight: 600;">
                    <strong>Costo de Envío:</strong> Gratis
                </p>
                <div class="unique-item-actions2 ms-auto">
                    <button class="btn btn-danger btn-sm remove-shipping">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Información sobre la recogida -->
            <div class="shipping-info mt-3 p-2 bg-light border-start border-primary">
                <p class="mb-1 text-dark" style="font-weight: 500;">
                    <i class="bi bi-shop me-2"></i>
                    <strong>Tienda de Recogida:</strong> {{ $Shippment->first()->store_name ?? 'No especificado' }}
                </p>
                <p class="mb-1 text-dark" style="font-weight: 500;">
                    <i class="bi bi-geo-alt me-2"></i>
                    <strong>Dirección:</strong> {{ $Shippment->first()->store_address ?? 'No especificada' }}
                </p>
                <p class="mb-1 text-dark" style="font-weight: 500;">
                    <i class="bi bi-calendar-check me-2"></i>
                    <strong>Fecha de Recogida:</strong> {{ $Shippment->first()->pickup_date }}
                </p>
                <p class="mb-1 text-dark" style="font-weight: 500;">
                    <i class="bi bi-clock me-2"></i>
                    <strong>Hora de Recogida:</strong> {{ $Shippment->first()->pickup_time }}
                </p>
                <p class="mt-2 text-secondary" style="font-weight: 500;">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Aviso:</strong> Por favor, asegúrate de llevar una identificación válida para recoger tu pedido.
                </p>
            </div>
        </div>
    </div>
@endif

            
            
            
            

                @if ($shippmentExists && $Shippment->first()->ShipmentMethod === 'EnvioPorPaqueteria')
                    <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm rounded"
                        style="background-color: #26d2b6;">
                        <!-- Imagen del envío -->
                        <div class="item-image-wrapper col-12 col-md-2 text-center mb-3 mb-md-0">
                            <img src="{{ asset('storage/img/envio_entrega/paqueteexpress.jpg') }}"
                                class="img-thumbnail" alt="Envío por Paquetería"
                                style="object-fit: cover; width: 100%; height: 100%;">
                        </div>

                        <!-- Detalles del envío -->
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
                                        ${{ number_format($Shippment->first()->shippingcost_IVA, 2, '.', ',') }} MXN
                                    </p>
                                    <div class="unique-item-actions2 ms-auto">
                                        <button class="btn btn-danger btn-sm remove-shipping">
                                            <i class="bi bi-trash"></i>
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
            <div class="unique-cart-summary p-4 bg-white rounded shadow-sm mt-4 border">

                <h4 class="mb-4 text-primary fw-bold">Resumen de su compra</h4>

                <!-- Sección con columnas a la izquierda y derecha -->
                <div class="row">
                    <!-- Columna izquierda (Subtotal, Descuento, IVA, Envío) -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Subtotal (sin IVA):</span>
                            <span class="fw-bold text-dark">${{ number_format($subtotalSinIVA, 2, '.', ',') }}
                                MXN</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Descuento aplicado:</span>
                            <span class="fw-bold text-success">- ${{ number_format($totalDescuento, 2, '.', ',') }}
                                MXN</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">IVA (16%):</span>
                            <span class="fw-bold text-dark">${{ number_format($iva, 2, '.', ',') }} MXN</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Costo de Envío (con IVA):</span>
                            <span class="fw-bold text-dark">${{ number_format($shippingCostIVA, 2, '.', ',') }}
                                MXN</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm" style="background-color: #f0f4f8;">
                            <div class="card-body p-4">
                                <form id="paymentForm" action="{{ url('/cart/proceed-to-payment') }}"
                                    method="POST">
                                    @csrf
                                    <!-- Campo de nombre de contacto -->
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

                                    <!-- Campo de teléfono de contacto -->
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
                    </div>





                </div>

                <hr class="my-4">

                <!-- Sección destacada para el Total a pagar -->
                <div class="total-section p-3 bg-light rounded d-flex justify-content-between align-items-center mb-4"
                    style="background-color: #f8f9fa; border-left: 5px solid #007bff;">
                    <h4 class="mb-0 text-dark fw-bold">Total a pagar:</h4>
                    <h4 class="mb-0 text-primary fw-bold">${{ number_format($totalFinal, 2, '.', ',') }} MXN</h4>
                </div>

                @if ($shippmentExists)
                    <div class="d-grid gap-2 mt-4">
                        <form id="checkoutForm" action="{{ url('/cart/proceed-to-payment') }}" method="POST">
                            @csrf
                            <!-- Botón para proceder al pago -->
                            <button type="button" id="proceedToPaymentBtn" class="btn btn-lg btn-success shadow-sm">
                                <i class="bi bi-cash-coin"></i> Continuar
                            </button>
                            


                        </form>
                    </div>
                @endif
            </div>
        @endif
    </div>

<!-- Modal de Términos y Condiciones -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Términos y Condiciones</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;" id="termsContent">
                <!-- Incluir la vista parcial de términos y condiciones -->
                @include('partials.tyc')
            </div>
            <div class="modal-footer">
                <button type="button" id="acceptTermsBtn" class="btn btn-primary" disabled>Aceptar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>


    <script>
        $(document).ready(function() {
            // Efecto de parpadeo en la alerta
            $(".alert-shake").fadeOut(500).fadeIn(500).fadeOut(500).fadeIn(500);

            // Efecto de temblor o shake
            function shakeElement(element) {
                var interval = 100;
                var distance = 10;
                var times = 4;

                for (var i = 0; i < (times + 1); i++) {
                    $(element).animate({
                        left: ((i % 2 == 0 ? distance : distance * -1))
                    }, interval);
                }
                $(element).animate({
                    left: 0
                }, interval);
            }

            // Aplica el efecto de temblor a la alerta
            shakeElement(".alert-shake");

            // Repite el shake cada cierto tiempo (opcional)
            setInterval(function() {
                shakeElement(".alert-shake");
            }, 3000); // Cada 4 segundos
        });
        $(document).ready(function() {
            // Manejar el clic en el botón para eliminar el envío
            $('.remove-shipping').on('click', function(e) {
                e.preventDefault(); // Evitar la acción predeterminada del botón

                // Llamar a la función para eliminar el envío
                removeShipping();
            });

            function removeShipping() {
                $.ajax({
                    type: "POST",
                    url: "/cart/remove-shipping", // URL al controlador que maneja la eliminación del envío
                    data: {
                        _token: $('meta[name="csrf-token"]').attr(
                            'content') // Incluyendo el token CSRF para seguridad
                    },
                    success: function(response) {
                        console.log('Método de envío eliminado');
                        location.reload(); // Recargar la página para reflejar los cambios
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al eliminar el método de envío:', error);
                        alert('Hubo un error al eliminar el método de envío.');
                    }
                });
            }
        });


        $(document).ready(function() {
            // Verificar si se debe mostrar la alerta de envío
            if (localStorage.getItem('showShippingAlert') === 'true') {
                const alertElement = document.getElementById('shipping-alert');
                alertElement.classList.remove('d-none'); // Muestra la alerta
                window.scrollTo(0, 0); // Desplaza la página hacia arriba para que se vea
                localStorage.removeItem('showShippingAlert'); // Limpia el estado después de mostrarla
            }

            // Resto del código para actualizar cantidades, remover artículos, etc.
        });
    </script>


    <script>
        $(document).ready(function() {
            // Tu código para manejo de cantidades
            const quantityControls = document.querySelectorAll('.quantity-controls');

            quantityControls.forEach(control => {
                const decreaseButton = control.querySelector('.quantity-decrease');
                const increaseButton = control.querySelector('.quantity-increase');
                const quantityInput = control.querySelector('.quantity-input');
                const maxQuantity = parseInt(increaseButton.getAttribute('data-max-quantity'), 10);
                const productCode = decreaseButton.getAttribute(
                    'data-product-code'); // Obtener el product_code desde el botón

                // Función para actualizar los botones según la cantidad actual
                function updateButtonStates(currentValue) {
                    if (currentValue <= 1) {
                        decreaseButton.disabled = true;
                    } else {
                        decreaseButton.disabled = false;
                    }

                    if (currentValue >= maxQuantity) {
                        increaseButton.disabled = true;
                    } else {
                        increaseButton.disabled = false;
                    }
                }

                // Inicializar los estados de los botones
                updateButtonStates(parseInt(quantityInput.value, 10));

                decreaseButton.addEventListener('click', function() {
                    let currentValue = parseInt(quantityInput.value, 10);
                    if (currentValue > 1) {
                        updateCartQuantity(-1, productCode); // Resta 1 a la cantidad
                    } else if (currentValue === 1) {
                        // Si la cantidad es 1 y se presiona el botón de disminuir, eliminar el artículo
                        removeCartItem(productCode);
                    }
                });

                increaseButton.addEventListener('click', function() {
                    let currentValue = parseInt(quantityInput.value, 10);
                    if (currentValue < maxQuantity) {
                        updateCartQuantity(1, productCode); // Suma 1 a la cantidad
                    }
                });

                // Escuchar cambios en la cantidad para ajustar los botones
                quantityInput.addEventListener('change', function() {
                    updateButtonStates(parseInt(quantityInput.value, 10));
                });
            });

            function updateCartQuantity(quantityChange, productCode) {
                $.ajax({
                    type: 'POST',
                    url: '/cart/update-quantity', // Ajusta la URL al endpoint de tu controlador
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        quantity: quantityChange,
                        product_code: productCode // Asegurarse de enviar el product_code
                    },
                    success: function(response) {
                        removeShipping(); // Eliminar método de envío después de actualizar la cantidad
                        location.reload(); // Recargar la página para reflejar los cambios
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
                        removeShipping(); // Eliminar método de envío después de eliminar un ítem
                        location.reload(); // Recargar la página para reflejar los cambios en el carrito
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
                    url: "/cart/remove-shipping", // URL al controlador que elimina el envío
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Método de envío eliminado');

                        location.reload(); // Recargar la página para reflejar los cambios
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
        document.addEventListener('DOMContentLoaded', function() {
            const storePickupBlock = document.getElementById('store-pickup-block');
            const localShippingBlock = document.getElementById('local-shipping-block');
            const paqueteriaShippingBlock = document.getElementById('paqueteria-shipping-block');
            const shippingTypeSelector = document.getElementById('shipping-type-selector');

            // Manejar el cambio de tipo de envío
            shippingTypeSelector.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const shippingName = selectedOption.value;

                // Ocultar todos los bloques
                storePickupBlock.style.display = 'none';
                localShippingBlock.style.display = 'none';
                paqueteriaShippingBlock.style.display = 'none';

                // Mostrar el bloque correspondiente al tipo de envío seleccionado
                if (shippingName === 'Recoger en Tienda') {
                    storePickupBlock.style.display = 'block';
                } else if (shippingName === 'Envío Local') {
                    localShippingBlock.style.display = 'block';
                } else if (shippingName === 'Envío por Paquetería') {
                    paqueteriaShippingBlock.style.display = 'block';
                }
            });
        });



        document.addEventListener('DOMContentLoaded', function() {
            // Manejar la eliminación de ítems del carrito
            document.querySelectorAll('.remove-from-cart1').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const itemNoS = this.getAttribute('data-nos');

                    // Solicitud AJAX para eliminar el ítem del carrito
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
                                .reload(); // Recargar la página para reflejar los cambios en el carrito


                        },
                        error: function(xhr, status, error) {
                            console.error('Error al eliminar el ítem del carrito:',
                                error);
                            alert('Error al eliminar el ítem del carrito.');
                        }
                    });
                });
            });
        });
        $(document).ready(function() {
            // Manejar el clic en el botón para eliminar el envío
            $('.remove-shipping').on('click', function(e) {
                e.preventDefault(); // Evitar la acción predeterminada del botón

                // Llamar a la función para eliminar el envío
                removeShipping();
            });

            function removeShipping() {
                $.ajax({
                    type: "POST",
                    url: "/cart/remove-shipping", // URL al controlador que maneja la eliminación del envío
                    data: {
                        _token: $('meta[name="csrf-token"]').attr(
                            'content') // Incluyendo el token CSRF para seguridad
                    },
                    success: function(response) {
                        console.log('Método de envío eliminado');
                        location.reload(); // Recargar la página para reflejar los cambios
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
        // Habilitar o deshabilitar el botón "Continuar" según el valor de los campos de nombre y teléfono
        function validateFields() {
            var contactName = $('#contactName').val().trim();
            var contactPhone = $('#contactPhone').val().trim();
            
            // Verificar si ambos campos están completos
            if (contactName === '' || contactPhone === '') {
                $('#proceedToPaymentBtn').prop('disabled', true); // Deshabilitar el botón si falta algún campo
            } else {
                $('#proceedToPaymentBtn').prop('disabled', false); // Habilitar el botón si ambos campos están completos
            }
        }

        // Ejecutar la validación cuando se cambien los campos de nombre y teléfono
        $('#contactName, #contactPhone').on('input', function() {
            validateFields();
        });

        // Manejar el clic en el botón "Continuar"
        $('#proceedToPaymentBtn').on('click', function(e) {
            e.preventDefault(); // Evitar el comportamiento predeterminado

            var contactName = $('#contactName').val().trim();
            var contactPhone = $('#contactPhone').val().trim();

            if (contactPhone === '' || contactName === '') {
                // Mostrar errores si el teléfono o nombre está vacío
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
                // Si ambos campos están completos, elimina los errores y abre el modal
                $('#contactName').removeClass('is-invalid');
                $('#contactPhone').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                // Abrir el modal de términos y condiciones
                $('#termsModal').modal('show');
            }
        });
    });
</script>



    <script>
        $(document).ready(function() {
            // Verificar si se ha llegado al final del contenido
            $('#termsContent').on('scroll', function() {
                var $termsContent = $(this);
                var scrollTop = $termsContent.scrollTop();
                var innerHeight = $termsContent.innerHeight();
                var scrollHeight = $termsContent[0].scrollHeight;

                // Mostrar en consola los valores del scroll
                console.log('Scroll Top:', scrollTop);
                console.log('Inner Height:', innerHeight);
                console.log('Scroll Height:', scrollHeight);

                // Si el usuario ha llegado al final del contenido, habilitar el botón "Aceptar"
                if (scrollTop + innerHeight >= scrollHeight -
                    10) { // Restamos 10 píxeles para asegurar precisión
                    $('#acceptTermsBtn').prop('disabled', false);
                }
            });

            $('#acceptTermsBtn').on('click', function() {
    $('#termsModal').modal('hide'); // Cerrar el modal
    $('#paymentForm').submit(); // Enviar el formulario correcto
});


            // Reiniciar el estado del botón y el scroll cuando se cierre el modal
            $('#termsModal').on('hidden.bs.modal', function() {
                $('#termsContent').scrollTop(0); // Reiniciar el scroll al principio
                $('#acceptTermsBtn').prop('disabled', true); // Deshabilitar el botón nuevamente
            });
        });
    </script>

    <style>
        .image-container1 {
            overflow: hidden;
            /* Asegura que nada se desborde del contenedor */
            transition: transform 0.3s ease;
            /* Suaviza la animación del zoom */
        }

        .image-container1:hover {
            transform: scale(1.1);
            /* Ajusta el nivel de zoom al pasar el mouse sobre el contenedor */
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
