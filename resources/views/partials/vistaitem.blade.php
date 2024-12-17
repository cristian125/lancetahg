<section class="py-5">
    <div class="container">
        <div class="row gx-5">
            <aside class="col-lg-6 ">
                <div id="main-image-container"
                    class="border rounded-4 mb-3 d-flex justify-content-center align-items-center"
                    style="width: 100%; height: 400px; position: relative;">
                    @if ($producto->descuento > 0)
                        <div class="badge-offer"><i class="fas fa-tags">
                            </i> ¡Oferta!
                        </div>
                    @endif
                    <img id="main-image" src="{{ $imagenPrincipal }}"
                        style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                    <div class="zoom-controls">
                        <button id="zoom-in">+</button>
                        <button id="zoom-out">-</button>
                    </div>
                </div>
                <div class="d-flex justify-content-center container">
                    <p class="fst-italic">Las imágenes de los productos son ilustrativas y pueden variar respecto al
                        artículo real.</p>
                </div>
                @if (count($imagenesMiniaturas) > 0)
                    <div class="d-flex justify-content-center mb-3">
                        @foreach ($imagenesMiniaturas as $imagen)
                            <div class="border mx-1 rounded-2 item-thumb" data-image="{{ $imagen }}"
                                style="width: 60px; height: 60px; background-image: url('{{ $imagen }}'); background-size: cover; background-position: center; cursor: pointer;">
                            </div>
                        @endforeach
                    </div>
                @endif
            </aside>
            <main class="col-lg-6">
                <div class="ps-lg-3">
                    <div class="product-header">
                        <h4 class="product-title text-dark mb-3">{{ $producto->no_s }} - {{ $producto->nombre }}</h4>
                        <div class="product-details">
                            <div class="row mb-2">
                                <div class="col-3 fw-bold text-secondary">Marca:</div>
                                <div class=>{{ $nombreProveedor }}</div>
                            </div>
                            <div class="row">
                                <div class="col-9">
                                    @if ($cantidadDisponible > 0)
                                        <div class="stock-indicator">
                                            <span class="stock-icon">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="12" cy="12" r="10" fill="#38A169" />
                                                    <path d="M10 15.5l-3-3 1.41-1.42L10 12.67l5.59-5.59L17 8l-7 7z"
                                                        fill="white" />
                                                </svg>
                                            </span>
                                            <span class="stock-message">{{ $mensajeStock }}</span>
                                        </div>
                                    @else
                                        <div class="custom-alert">
                                            <div class="alert-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"
                                                        fill="#E53E3E" />
                                                </svg>
                                            </div>
                                            <div class="alert-message">No disponible.</div>
                                        </div>
                                        <p class="contact-info">
                                            <i class="me-1 fa fa-phone"></i> Llame al 55-5578-1958 para preguntar por su
                                            disponibilidad.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            @if ($producto->descuento > 0)
                                <span
                                    class="h5 text-muted text-decoration-line-through">${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
                                    MXN</span><br>
                                <span
                                    class="h4 text-danger">${{ number_format($producto->precio_con_descuento, 2, '.', ',') }}
                                    MXN</span>
                                <span class="badge bg-success ms-2">{{ number_format($producto->descuento, 0) }}% de
                                    descuento</span>
                            @else
                                <span
                                    class="h5"><br>${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
                                    MXN</span>
                            @endif
                            <span class="text-muted" id="unidad_medida">/{{ $producto->unidad_medida_venta }}</span>
                        </div>

                        <div class="row">

                            <dt class="col-3">CATEGORÍA:</dt>
                            <dd class="col-9">
                                {{ mb_strtoupper($division, 'UTF-8') }} / {{ mb_strtoupper($categoria, 'UTF-8') }} /
                                {{ mb_strtoupper($codigoMinorista, 'UTF-8') }}
                            </dd>
                        </div>
                        <hr />

                        @foreach ($groupedProducts as $grupoDescripcion => $productos)
                            <div class="mb-3">
                                <label for="product-variants-{{ $loop->index }}">{{ $grupoDescripcion }}:</label>
                                <select id="product-variants-{{ $loop->index }}"
                                    class="form-select product-variant-select">
                                    @foreach ($atributosProducto->where('grupo_descripcion', $grupoDescripcion) as $atributoActual)
                                        <option value="{{ $producto->id }}" selected>
                                            {{ $atributoActual->atributo_nombre }} (Actual)</option>
                                    @endforeach
                                    @foreach ($productos as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->atributo_nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                        @if ($cantidadDisponible > 0)
                            <div class="row mb-4">
                                <div class="col-md-4 col-6 mb-3">
                                    @if (Auth::check())
                                        <label class="mb-2 d-block">Cantidad</label>
                                        <div class="input-group mb-3" style="width: 170px;">
                                            <div class="input-group mb-3" style="width: 170px;">
                                                <button
                                                    class="btn btn-primary border border-secondary px-3 btn-remove-qty"
                                                    type="button">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input id="qty" name="quantity" type="number"
                                                    class="form-control text-center border border-secondary"
                                                    value="1" min="1" max="{{ $cantidadDisponible }}" />
                                                <button class="btn btn-primary border border-secondary px-3 btn-add-qty"
                                                    type="button">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                    @endif
                                    <div class="toast-container position-fixed bottom-0 end-0 p-3"
                                        style="z-index: 1055;">
                                        <div id="stockAlertToast"
                                            class="toast align-items-center text-bg-danger border-0" role="alert"
                                            aria-live="assertive" aria-atomic="true">
                                            <div class="d-flex">
                                                <div class="toast-body">
                                                    No se puede añadir esa cantidad porque supera el límite de
                                                    stock.
                                                </div>
                                                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                                    data-bs-dismiss="toast" aria-label="Close"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php

                                $shippingRestrictions = [];
                                if (!$producto->allow_paqueteria_shipping) {
                                    $shippingRestrictions[] = 'Paquetería';
                                }

                                if (!$producto->allow_local_shipping) {
                                    $shippingRestrictions[] = 'Envío Local';
                                }

                                if (!$producto->allow_store_pickup) {
                                    $shippingRestrictions[] = 'Recojer en Tienda';
                                }
                                if (!$producto->allow_cobrar_shipping) {
                                    $shippingRestrictions[] = 'Envío por Cobrar';
                                }
                            @endphp

                            @if (count($shippingRestrictions) > 0)
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <svg class="flex-shrink-0 me-2" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.437-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1-2.002.001A1 1 0 0 1 8.002 11z" />
                                    </svg>
                                    <div>
                                        <strong>Atención:</strong> Este producto no está disponible para los siguientes
                                        tipos de envío:
                                        <ul class="mb-0">
                                            @foreach ($shippingRestrictions as $method)
                                                <li>{{ $method }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                            <div id="cart-notification" class="notification-popup" style="display: none;">
                                Producto añadido al carrito.
                            </div>

                            <form action="{{ env('APP_URL') }}/producto/{{ $id }}" method="GET">
                                <button id="add-to-cart" data-id="{{ $producto->id }}"
                                    data-nos="{{ $producto->no_s }}" class="btn btn-primary shadow-0">
                                    <i class="me-1 fa fa-shopping-basket"></i> Añadir al carrito
                                </button>

                                @if (Auth::check())
                                    <button id="show-cart" class="btn btn-danger shadow-0 p-2">
                                        <i class="me-1 fa fa-eye"></i> Ver carrito
                                    </button>
                                @endif
                            </form>

                            <div id="cart-notification" class="notification-popup" style="display: none;">
                                Producto añadido al carrito.
                            </div>
                        @else
                            <p class="text-danger"></p>
                        @endif
                        <hr />
                        <div class="row">
                            <p class="fw-bold">Descripción: </p>
                            <p>{!! $producto->descripcion !!}</p>
                        </div>
                        <hr />
                        <div class="row mb-4">
                            <div class="row">
                                <div class="col-sm-2 align-middle pt-2">
                                    <i class="fa-solid fa-credit-card" style="font-size: 48px;"></i>
                                </div>

                                <div class="col-sm-10">
                                    <h5>Pague con tarjetas de débito o crédito</h5>
                                    <img src="{{ asset('storage/img/cards-product-page.jpg') }}"
                                        alt="Formas de Pago aceptadas" style="width:300px;">
                                    <p>No aceptamos American Express</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2 align-middle pt-2">
                                    <i class="fa-solid fa-truck" style="font-size: 48px;"></i>
                                </div>
                                <div class="col-sm-10">
                                    <h5>Envío a todo México</h5>
                                    <p>Entrega de 2 a 5 días. <span class="fst-italic fw-bold">* Aplican
                                            restricciones.</span></p>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#enviosModal">Más sobre
                                        información sobre envío y entrega ></a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2 align-middle pt-2">
                                    <i class="fa-solid fa-store" style="font-size: 48px;"></i>
                                </div>
                                <div class="col-sm-10">
                                    <h5>Recoja en tienda</h5>
                                    <p>Compre en línea y pase a recoger a la tienda establecida en el mismo día. <span
                                            class="fst-italic fw-bold">* Aplican restricciones.</span></p>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#enviosModal">Más sobre
                                        información sobre envío y entrega ></a>
                                </div>
                            </div>
                        </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="enviosModal" tabindex="-1" aria-labelledby="enviosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enviosModalLabel">Información de Envío</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ route('envios') }}?m=0"
                        style="width: 100%; height: 500px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <h4 class="mb-4 text-center">Otros usuarios también se interesaron en estos productos similares:</h4>
        <div id="related-products-slider" class="owl-carousel owl-theme unique-carousel">
            @foreach ($productosRelacionados as $recomendado)
                <div class="item">
                    <div class="product-card2 bg-white rounded">

                        <a href="{{ url('/producto/' . $recomendado->id . '-' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($recomendado->nombre))) }}"
                            class="text-decoration-none text-dark">

                            <div class="image-container" style="position: relative; overflow: hidden;">
                                <img src="{{ $recomendado->imagen }}" alt="{{ $recomendado->descripcion }}"
                                    class="img-fluid rounded"
                                    style="width: 100%; height: 200px; object-fit: contain;">
                                @if (isset($recomendado->descuento) && $recomendado->descuento > 0)
                                    <div class="badge-offer"
                                        style="position: absolute; top: 10px; left: 10px; background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px;">
                                        <i class="fas fa-tags"></i> ¡Oferta!
                                    </div>
                                @endif
                            </div>
                            <div class="product-info1 mt-3 text-center">
                                <h6 class="fw-bold">{{ $recomendado->nombre }}</h6>
                                <p class="text-muted mb-0">
                                    @if (isset($recomendado->descuento) && $recomendado->descuento > 0)
                                        <span
                                            class="text-decoration-line-through">${{ number_format($recomendado->precio_unitario_IVAinc, 2, '.', ',') }}
                                            MXN</span>
                                        <span
                                            class="text-danger">${{ number_format($recomendado->precio_con_descuento, 2, '.', ',') }}
                                            MXN</span>
                                    @else
                                        <span>${{ number_format($recomendado->precio_unitario_IVAinc, 2, '.', ',') }}
                                            MXN</span>
                                    @endif
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyInput = document.getElementById('qty');
        const addButton = document.querySelector('.btn-add-qty');
        const removeButton = document.querySelector('.btn-remove-qty');
        const addToCartButton = document.getElementById('add-to-cart');
        const stockAlertToast = document.getElementById('stockAlertToast') ?
            new bootstrap.Toast(document.getElementById('stockAlertToast')) :
            null;

        const maxQty = parseInt(qtyInput.getAttribute('max')) || 1;
        const minQty = parseInt(qtyInput.getAttribute('min')) || 1;


        function sanitizeQuantity(value) {
            return Math.min(Math.max(value, minQty), maxQty);
        }


        function updateQuantity(change) {
            let currentQty = parseInt(qtyInput.value) || minQty;
            currentQty = sanitizeQuantity(currentQty + change);
            qtyInput.value = currentQty;

            if (stockAlertToast) {
                if (currentQty >= maxQty && change > 0) {
                    stockAlertToast.show();
                } else {
                    stockAlertToast.hide();
                }
            }

            updateAddToCartButton();
        }


        function updateAddToCartButton() {
            const currentQty = parseInt(qtyInput.value) || 0;
            addToCartButton.disabled = currentQty < minQty || currentQty > maxQty || isNaN(currentQty);
        }


        addButton.addEventListener('click', function() {
            updateQuantity(1);
        });

        removeButton.addEventListener('click', function() {
            updateQuantity(-1);
        });


        qtyInput.addEventListener('input', function() {
            const currentQty = parseInt(qtyInput.value) || minQty;
            qtyInput.value = sanitizeQuantity(currentQty);
            updateAddToCartButton();
        });


        updateAddToCartButton();
    });
</script>
<script>
    function addToCart(productId, no_s, quantity, currentStock) {
        let token = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            type: "POST",
            url: "/cart/add-multiple",
            data: {
                no_s: no_s,
                quantity: quantity,
                _token: token,
            },
            dataType: "json",
            success: function(response) {
                updateCartCount();
                loadCartItems();

                if (response.stock_restante !== undefined) {
                    $(".stock-info")
                        .text(`${response.stock_restante} en stock`)
                        .data("stock", response.stock_restante);

                    if (response.stock_restante <= 0) {
                        $("#add-to-cart").prop("disabled", true);
                        $(".stock-info")
                            .text("No hay stock disponible")
                            .removeClass("text-success")
                            .addClass("text-danger");
                    }
                }


                showNotification("Producto añadido al carrito.");
            },
            error: function(data) {
                if (data.status === 401) {
                    showLoginPopover();
                } else if (data.responseJSON && data.responseJSON.error) {

                    showMaxStockPopover(data.responseJSON.error);
                } else {
                    showMaxStockPopover();
                }
            },
        });
    }

    $("#add-to-cart").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        let productId = $(this).data("id");
        let no_s = $(this).data("nos");
        let quantity = parseInt($("#qty").val());
        let currentStock = parseInt($(".stock-info").data("stock"));

        addToCart(productId, no_s, quantity, currentStock);
    });

    $("#show-cart").on("click", function(e) {
        e.preventDefault();
        $(location).prop("href", "/carrito");
    });

    function showLoginPopover() {
        $("#add-to-cart")
            .popover({
                content: "Por favor inicie sesión para agregar el producto al carrito.",
                placement: "bottom",
                trigger: "focus",
                customClass: "popover-danger bg-danger fw-bold",
            })
            .popover("show");
    }

    function showMaxStockPopover(message = "No puedes añadir más productos de los que hay en stock.") {
        $("#add-to-cart")
            .popover({
                content: message, 
                placement: "bottom",
                trigger: "focus",
                customClass: "popover-warning bg-warning fw-bold",
            })
            .popover("show");


        setTimeout(() => {
            $("#add-to-cart").popover("dispose");
        }, 3000);
    }

    function updateCartCount() {
        $.ajax({
            type: "GET",
            url: "/get-cart-items",
            dataType: "json",
            success: function(data) {
                let totalItems = 0;

                $.each(data.items, function(index, item) {
                    totalItems += item.quantity;
                });

                if (totalItems > 0) {
                    $("#cart-item-count").text(totalItems).show();
                } else {
                    $("#cart-item-count").hide();
                }

            },
            error: function() {
                console.log("Error al cargar los items del carrito.");
            },
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyInput = document.getElementById('qty');
        const addToCartButton = document.getElementById('add-to-cart');
        const notification = document.getElementById('cart-notification');
        const stockMessage = document.querySelector('.stock-message');
        const stockIndicator = document.querySelector('.stock-indicator');

        notification.style.display = 'none';


        function showNotification(message) {
            notification.textContent = message;
            notification.classList.add('show');
            notification.style.display = 'block';


            setTimeout(() => {
                notification.classList.add('hide');
            }, 2000);


            setTimeout(() => {
                notification.style.display = 'none';
                notification.classList.remove('show', 'hide');
            }, 2500);
        }

    });
</script>



<script>
    $(document).ready(function() {
        var itemsCount = $("#related-products-slider .item").length;

        if (itemsCount >= 5) {

            $("#related-products-slider").owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplayHoverPause: true,
                items: 5,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 3
                    },
                    1000: {
                        items: 5
                    }
                }
            });
        } else {

            $("#related-products-slider").addClass("center-items");
            if (itemsCount === 1) {
                $("#related-products-slider .item").css("margin", "0 auto");
            } else if (itemsCount === 2) {
                $("#related-products-slider .item").css({
                    "margin-left": "10%",
                    "margin-right": "10%"
                });
            } else if (itemsCount === 3) {
                $("#related-products-slider .item").css({
                    "margin-left": "5%",
                    "margin-right": "5%"
                });
            } else if (itemsCount === 4) {
                $("#related-products-slider .item").css({
                    "margin-left": "2.5%",
                    "margin-right": "2.5%"
                });
            }
        }

        document.querySelectorAll('.product-variant-select').forEach(function(select) {
            select.addEventListener('change', function() {
                var selectedProductId = this.value;
                window.location.href = '/producto/' + selectedProductId;
            });
        });
    });
</script>

<style>
    .center-items {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
    }
</style>


<style>
    .custom-alert {
        display: flex;
        align-items: center;
        background-color: #fff4f4;
        border: 1px solid #f5c6c6;
        border-radius: 8px;
        padding: 10px 15px;
        color: #c53030;
        font-weight: 500;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 5px;
    }

    .alert-icon {
        margin-right: 10px;
    }

    .alert-icon svg {
        width: 20px;
        height: 20px;
    }

    .alert-message {
        font-size: 16px;
        line-height: 1.5;
    }

    .contact-info {
        margin-top: 8px;
        color: #d3762a;
        font-size: 16px;
        font-weight: 500;
        font-style: italic;
    }


    .contact-info a {
        color: #c53030;
        text-decoration: underline;
    }

    .stock-indicator {
        display: inline-flex;
        align-items: center;
        background-color: #e6f4ea;
        border: 1px solid #38a169;
        border-radius: 8px;
        padding: 8px 12px;
        color: #2f855a;
        font-weight: 500;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 5px;
    }

    .stock-icon {
        margin-right: 8px;
    }

    .stock-icon svg {
        width: 18px;
        height: 18px;
    }

    .stock-message {
        font-size: 16px;
    }
</style>
