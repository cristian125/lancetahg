    <div class="container mt-5" id="unique-cart-container">
        <h1 class="mb-4 text-center" id="unique-cart-title">Carrito de Compras</h1>
        @if ($cartItems->isEmpty())
            <div class="empty-cart text-center" id="unique-empty-cart">
                <img src="{{ asset('storage/iconos/carrito_vacio.png') }}" alt="Carrito vacío" class="img-fluid mb-4"
                    style="max-width: 200px;">
                <p>No hay productos en su carrito. <a href="{{ url('/') }}">Continúe comprando</a></p>
            </div>
        @else
            @if (count($Shippment) == 0)
                <div id="general-shipping-block" class="mb-4 p3 bg-ligh rounded shadow-sm">
                    <!-- Selector de tipo de envío -->
                    <div class="mb-4 p-3 bg-light rounded shadow-sm">
                        <h5 class="mb-3 text-primary"><i class="bi bi-truck"></i> Selecciona el tipo de envío</h5>
                        <div class="form-group">
                            <select id="shipping-type-selector" class="form-select">
                                <option value="">(Seleccione una opción)</option>
                                <option value="Recoger en Tienda" data-price="0.00">Recoger en Tienda (sin costo de
                                    envío)
                                </option>

                                <option value="Envío Local" data-price="250.00">Envío Local</option>

                                <option value="Envío por Paquetería" data-price="500.00">Paqueteexpress
                                </option>
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
                @foreach ($cartItems as $item)
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
                            <!-- Descripción y botón de eliminar -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                                <h5 class="mb-2">{{ $item->description }}</h5>
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
                                    <span class="badge bg-danger">Descuento aplicado del: {{ $item->discount }}%</span>
                                </div>
                            @endif
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

                        <!-- Detalles del envío -->
                        <div class="col-12 col-md-10 d-flex flex-column">
                            <h5 class="mb-2 text-uppercase" style="font-weight: 600; color: #000000;">RECOGER EN
                                TIENDA</h5>
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex flex-column flex-md-row align-items-md-center w-100">
                                    <p class="mb-2 mb-md-0 me-4 text-muted"><strong>Cantidad:</strong> 1 Recogida</p>
                                    <p class="mb-2 mb-md-0 unique-product-price bg-light p-2 rounded me-4 text-primary flex-grow-1"
                                        style="font-weight: 600;">
                                        <strong>Precio unitario:</strong>
                                        ${{ number_format($Shippment->first()->unit_price, 2, '.', ',') }} MXN
                                    </p>
                                    <p class="mb-2 mb-md-0 unique-product-total-price bg-light p-2 rounded text-primary flex-grow-1"
                                        style="font-weight: 600;">
                                        <strong>Total con IVA:</strong>
                                        ${{ number_format($Shippment->first()->final_price, 2, '.', ',') }} MXN
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
                                    <i class="bi bi-shop me-2"></i>
                                    <strong>Tienda de Recogida:</strong>
                                    {{ $Shippment->first()->store->nombre ?? 'No especificado' }}
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
                                    <strong>Aviso:</strong> Por favor, asegúrese de llevar una identificación válida
                                    para recoger su pedido.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif


                @if ($shippmentExists && $Shippment->first()->ShipmentMethod === 'EnvioPorPaqueteria')
                <div class="unique-cart-item row mb-4 p-3 border-bottom shadow-sm rounded" style="background-color: #26d2b6;">
                    <!-- Imagen del envío -->
                    <div class="item-image-wrapper col-12 col-md-2 text-center mb-3 mb-md-0">
                        <img src="{{ asset('storage/img/envio_entrega/paqueteexpress.jpg') }}" class="img-thumbnail"
                            alt="Envío por Paquetería" style="object-fit: cover; width: 100%; height: 100%;">
                    </div>
            
                    <!-- Detalles del envío -->
                    <div class="col-12 col-md-10 d-flex flex-column">
                        <h5 class="mb-2 text-uppercase" style="font-weight: 600; color: #000000;">ENVÍO POR PAQUETERÍA</h5>
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex flex-column flex-md-row align-items-md-center w-100">
                                <p class="mb-2 mb-md-0 me-4 text-muted"><strong>Cantidad:</strong> 1 Envío</p>
            
                                <!-- Este div vacío mantiene la estructura -->
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
                                <strong>Nota:</strong> Los tiempos de entrega pueden variar dependiendo de la disponibilidad y saturación del servicio.
                            </p>
                            

                            <p class="mt-2 text-secondary" style="font-weight: 500;">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Aviso:</strong> La entrega se realiza directamente en la dirección seleccionada. Asegúrate de que alguien esté disponible para recibir el paquete.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            

            </div>
            <div class="unique-cart-summary p-4 bg-white rounded shadow-sm mt-4" style="border: 1px solid #e0e0e0;">
                @php
                    // Calcula el subtotal sin IVA
                    $subtotalSinIVA = $totalPrice / 1.16;

                    // Calcula el total del descuento aplicado
                    $totalDescuento = $cartItems->sum(function ($item) {
                        return $item->unit_price * $item->quantity * ($item->discount / 100);
                    });

                    // Calcula el IVA sobre el subtotal sin IVA
                    $iva = $subtotalSinIVA * 0.16;

                    // Calcula el total final incluyendo el IVA
                    $totalFinal = $shippmentExists ? $totalPrice + $shippingCostIVA : $totalPrice;
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="font-weight: 600; color: #333;">Subtotal (sin IVA):</h5>
                    <h5 class="mb-0 text-dark" style="font-weight: 600;">
                        ${{ number_format($subtotalSinIVA, 2, '.', ',') }} MXN</h5>
                </div>

                @if ($totalDescuento > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0" style="font-weight: 500; color: #555;">Descuento aplicado:</h6>
                        <h6 class="mb-0 text-danger" style="font-weight: 500;">
                            -${{ number_format($totalDescuento, 2, '.', ',') }} MXN</h6>
                    </div>
                @endif

                @if ($shippmentExists)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0" style="font-weight: 500; color: #555;">IVA (16%):</h6>
                        <h6 class="mb-0 text-secondary" style="font-weight: 500;">
                            ${{ number_format($iva, 2, '.', ',') }} MXN</h6>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0" style="font-weight: 500; color: #555;">Costo de Envío (con IVA):</h6>
                        <h6 class="mb-0 text-secondary" style="font-weight: 500;">
                            ${{ number_format($shippingCostIVA, 2, '.', ',') }} MXN</h6>
                    </div>

                    <hr style="border-color: #e0e0e0;">

                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0" style="font-weight: bold; color: #007bff;">Total de su carrito:</h4>
                        <h4 class="mb-0 text-primary" style="font-weight: bold;">
                            ${{ number_format($totalFinal, 2, '.', ',') }} MXN</h4>
                    </div>
                @endif
                @if ($shippmentExists)
                    <div class="d-grid gap-2 mt-4">
                        <form action="{{ url('/cart/proceed-to-payment') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-cash-coin"></i> Proceder al Pago
                            </button>
                        </form>
                    </div>
                @endif




            </div>

        @endif
    </div>


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
                        console.log('Item removed:', response);
                        location.reload(); // Recargar la página para reflejar los cambios en el carrito
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al eliminar el ítem del carrito:', error);
                        alert('Error al eliminar el ítem del carrito.');
                    }
                });
            }
        });








        document.addEventListener('DOMContentLoaded', function() {
            // Manejar la eliminación del método de envío
            document.querySelector('.remove-shipping').addEventListener('click', function(e) {
                e.preventDefault();

                // Solicitud AJAX para eliminar el método de envío
                $.ajax({
                    type: "POST",
                    url: "/cart/remove-shipping",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Shipping removed:', response);
                        location.reload(); // Recargar la página para reflejar los cambios
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al eliminar el envío:', error);
                        alert('Error al eliminar el envío.');
                    }
                });
            });
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
    </style>
