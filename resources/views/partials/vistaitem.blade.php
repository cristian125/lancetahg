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
                        <!-- Mostrar el nombre del producto correctamente -->
                        <h4 class="product-title text-dark mb-3">{{ $producto->no_s }} - {{ $producto->nombre }}</h4>

                        <!-- Información adicional en filas alineadas -->
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
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="12" cy="12" r="10" fill="#38A169" />
                                                    <path d="M10 15.5l-3-3 1.41-1.42L10 12.67l5.59-5.59L17 8l-7 7z" fill="white" />
                                                </svg>
                                            </span>
                                            <span class="stock-message">{{ $mensajeStock }}</span>
                                        </div>
                                    @else
                                        <div class="custom-alert">
                                            <div class="alert-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"
                                                          fill="#E53E3E" />
                                                </svg>
                                            </div>
                                            <div class="alert-message">No disponible.</div>
                                        </div>
                                        <p class="contact-info">
                                            <i class="me-1 fa fa-phone"></i> Llame al 55-5578-1958 para preguntar por su disponibilidad.
                                        </p>
                                    @endif
                                </div>
                                
                            </div>
                            
                        </div>

                        <div class="mb-3">
                            @if ($producto->descuento > 0)
                                <span
                                    class="h5 text-muted text-decoration-line-through">${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
                                    MXN</span>
                                <span
                                    class="h4 text-danger">${{ number_format($producto->precio_con_descuento, 2, '.', ',') }}
                                    MXN</span>
                                <span class="badge bg-success ms-2">Oferta: {{ $producto->descuento }}% de
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
                                <!-- Mostrar la descripción del grupo -->
                                <select id="product-variants-{{ $loop->index }}"
                                    class="form-select product-variant-select">
                                    <!-- Producto Actual -->
                                    @foreach ($atributosProducto->where('grupo_descripcion', $grupoDescripcion) as $atributoActual)
                                        <option value="{{ $producto->id }}" selected>
                                            {{ $atributoActual->atributo_nombre }} (Actual)</option>
                                    @endforeach
                                    <!-- Otras opciones -->
                                    @foreach ($productos as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->atributo_nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach


                        @if ($cantidadDisponible > 0)
                            <div class="row mb-4">
                                <div class="col-md-4 col-6 mb-3">
                                    <label class="mb-2 d-block">Cantidad</label>
                                    <div class="input-group mb-3" style="width: 170px;">
                                        <button id="btnremoveqty" class="btn btn-primary border border-secondary px-3"
                                            type="button">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input id="qty" name="quantity" type="number"
                                            class="form-control text-center border border-secondary" value="1"
                                            min="1" max="{{ $cantidadDisponible }}" />
                                        <button id="btnaddqty" class="btn btn-primary border border-secondary px-3"
                                            type="button">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <div class="toast-container position-fixed bottom-0 end-0 p-3"
                                        style="z-index: 1055;">
                                        <div id="stockAlertToast"
                                            class="toast align-items-center text-bg-danger border-0" role="alert"
                                            aria-live="assertive" aria-atomic="true">
                                            <div class="d-flex">
                                                <div class="toast-body">
                                                    No se puede añadir esa cantidad porque supera el límite de stock.
                                                </div>
                                                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                                    data-bs-dismiss="toast" aria-label="Close"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                        @else
                            <p class="text-danger"></p>
                        @endif



                        <hr />
                        <div class="row">
                            <p class="fw-bold">Descripción: </p>

                            <!-- Mostrar la descripción del producto -->
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

    <!-- Modal -->
    <div class="modal fade" id="enviosModal" tabindex="-1" aria-labelledby="enviosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enviosModalLabel">Información de Envío</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se incluirá la página de envíos -->
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
                        <a href="{{ url('/producto/' . $recomendado->id) }}" class="text-decoration-none text-dark">
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
                                <!-- Mostrar nombre y descripción de productos relacionados -->
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
        const addButton = document.getElementById('btnaddqty');
        const removeButton = document.getElementById('btnremoveqty');
        const addToCartButton = document.getElementById('add-to-cart');
        const maxQty = parseInt(qtyInput.getAttribute('max'));

        // Inicializar Toast de Bootstrap
        const stockAlertToast = new bootstrap.Toast(document.getElementById('stockAlertToast'));

        // Función para actualizar el estado del botón "Añadir al carrito"
        function updateAddToCartButton() {
            const currentQty = parseInt(qtyInput.value) || 0;
            addToCartButton.disabled = currentQty < 1 || isNaN(currentQty);
        }

        // Aumentar la cantidad, respetando el límite de stock
        addButton.addEventListener('click', () => {
            let currentQty = parseInt(qtyInput.value) || 1;
            if (currentQty < maxQty) {
                qtyInput.value = currentQty + 1;
                stockAlertToast.hide(); // Ocultar el mensaje de stock si es necesario
            } else if (currentQty === maxQty) {
                // Si la cantidad actual ya es igual al máximo, no mostrar el mensaje
                qtyInput.value = maxQty;
                stockAlertToast.hide();
            } else {
                stockAlertToast.show();
            }
            updateAddToCartButton();
        });


        // Reducir la cantidad, respetando el mínimo de 1
        removeButton.addEventListener('click', () => {
            let currentQty = parseInt(qtyInput.value) || 1;
            if (currentQty > 1) {
                qtyInput.value = currentQty - 1;
                stockAlertToast.hide();
            } else {
                qtyInput.value = 1; // No permite cantidades menores a 1
            }
            updateAddToCartButton();
        });

        // Validación al ingresar manualmente la cantidad
        qtyInput.addEventListener('input', () => {
            let currentQty = parseInt(qtyInput.value);
            if (currentQty > maxQty) {
                stockAlertToast.show();
                qtyInput.value = maxQty;
            } else if (currentQty < 1 || isNaN(currentQty)) {
                qtyInput.value = 1;
            }
            updateAddToCartButton();
        });

        // Inicialización de estado del botón de añadir al carrito
        updateAddToCartButton();
    });
</script>



<script>
    $(document).ready(function() {
        var itemsCount = $("#related-products-slider .item").length;

        if (itemsCount >= 5) {
            // Inicializa Owl Carousel solo si hay 5 o más productos
            $("#related-products-slider").owlCarousel({
                loop: true, // Activa el bucle para permitir movimiento continuo
                margin: 10,
                nav: true, // Activa los controles de navegación
                dots: true, // Muestra los puntos de navegación
                autoplay: true, // Activa la reproducción automática
                autoplayTimeout: 3000, // Tiempo entre desplazamientos automáticos (en milisegundos)
                autoplayHoverPause: true, // Pausa el desplazamiento automático al pasar el cursor por encima
                items: 5, // Número máximo de elementos visibles
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
            // Si hay menos de 5 productos, centrarlos manualmente
            $("#related-products-slider").addClass("center-items");
            if (itemsCount === 1) {
                // Centra un solo elemento
                $("#related-products-slider .item").css("margin", "0 auto");
            } else if (itemsCount === 2) {
                // Distribuye dos elementos
                $("#related-products-slider .item").css({ "margin-left": "10%", "margin-right": "10%" });
            } else if (itemsCount === 3) {
                // Distribuye tres elementos
                $("#related-products-slider .item").css({ "margin-left": "5%", "margin-right": "5%" });
            } else if (itemsCount === 4) {
                // Distribuye cuatro elementos
                $("#related-products-slider .item").css({ "margin-left": "2.5%", "margin-right": "2.5%" });
            }
        }

        // Cambio de variante de producto
        document.querySelectorAll('.product-variant-select').forEach(function(select) {
            select.addEventListener('change', function() {
                var selectedProductId = this.value;
                window.location.href = '/producto/' + selectedProductId;
            });
        });
    });
</script>

<style>
    /* Estilo para centrar los elementos manualmente */
    .center-items {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px; /* Ajuste de espacio entre elementos */
    }
</style>


<style>
/* Estilos específicos para el carrusel de productos relacionados */


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
