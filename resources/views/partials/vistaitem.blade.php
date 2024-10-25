<section class="py-5">
    <div class="container">
        <div class="row gx-5">
            <aside class="col-lg-6 ">
                <div id="main-image-container" class="border rounded-4 mb-3 d-flex justify-content-center align-items-center" style="width: 100%; height: 400px; position: relative;">
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
                    <!-- Mostrar el nombre del producto correctamente -->
                    <h4 class="title text-dark">{{ $producto->no_s }} - {{ $producto->nombre }}</h4>
                    <div class="d-flex flex-row my-3">
                        <span class="stock-info {{ $claseStock }} ms-2">{{ $mensajeStock }}</span>
                    </div>
                    <div class="mb-3">
                        @if ($producto->descuento > 0)
                            <span
                                class="h5 text-muted text-decoration-line-through">${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
                                MXN</span>
                            <span
                                class="h4 text-danger">${{ number_format($producto->precio_con_descuento, 2, '.', ',') }}
                                MXN</span>
                            <span class="badge bg-success ms-2">Oferta: {{ $producto->descuento }}% de descuento</span>
                        @else
                            <span class="h5">${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
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

                    @foreach($groupedProducts as $grupoDescripcion => $productos)
                    <div class="mb-3">
                        <label for="product-variants-{{ $loop->index }}">{{ $grupoDescripcion }}:</label> <!-- Mostrar la descripción del grupo -->
                        <select id="product-variants-{{ $loop->index }}" class="form-select product-variant-select">
                            <!-- Producto Actual -->
                            @foreach($atributosProducto->where('grupo_descripcion', $grupoDescripcion) as $atributoActual)
                                <option value="{{ $producto->id }}" selected>{{ $atributoActual->atributo_nombre }} (Actual)</option>
                            @endforeach
                            <!-- Otras opciones -->
                            @foreach($productos as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->atributo_nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
                

                    <div class="row mb-4">
                        <div class="col-md-4 col-6 mb-3">
                            <label class="mb-2 d-block">Cantidad</label>
                            <div class="input-group mb-3" style="width: 170px;">
                                <button id="btnremoveqty" class="btn btn-primary border border-secondary px-3"
                                    type="button" {{ $botonDeshabilitado }}>
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input id="qty" type="text"
                                    class="form-control text-center border border-secondary" value="1"
                                    step="1" min="1" max="{{ $cantidadDisponible }}"
                                    {{ $botonDeshabilitado }} readonly />
                                <button id="btnaddqty" class="btn btn-primary border border-secondary px-3"
                                    type="button" {{ $botonDeshabilitado }}>
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <form action="{{ env('APP_URL') }}/producto/{{ $id }}" method="GET">
                        <button id="add-to-cart" data-id="{{ $producto->id }}" data-nos="{{ $producto->no_s }}"
                            class="btn btn-primary shadow-0" {{ $botonDeshabilitado }}>
                            <i class="me-1 fa fa-shopping-basket"></i> Añadir al carrito
                        </button>
                        @if (Auth::check())
                            <button id="show-cart" class="btn btn-danger shadow-0 p-2">
                                <i class="me-1 fa fa-eye"></i> Ver carrito
                            </button>
                        @endif
                    </form>

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
        <div id="related-products-slider" class="owl-carousel owl-theme">
            @foreach ($productosRelacionados as $recomendado)
                <div class="item">
                    <div class="product-card border rounded shadow-sm p-3 mb-5 bg-white rounded">
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
                            <div class="product-info mt-3 text-center">
                                <!-- Mostrar nombre y descripción de productos relacionados -->
                                <h6 class="fw-bold">{{ $recomendado->nombre }}</h6>
                                <p class="text-muted mb-0">
                                    @if (isset($recomendado->descuento) && $recomendado->descuento > 0)
                                        <span class="text-decoration-line-through">${{ number_format($recomendado->precio_unitario_IVAinc, 2, '.', ',') }} MXN</span>
                                        <span class="text-danger">${{ number_format($recomendado->precio_con_descuento, 2, '.', ',') }} MXN</span>
                                    @else
                                        <span>${{ number_format($recomendado->precio_unitario_IVAinc, 2, '.', ',') }} MXN</span>
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
    $(document).ready(function() {
        $("#related-products-slider").owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            autoplay: true, // Activa el autoplay
            autoplayTimeout: 2000, // Cambiar cada 2 segundos (2000 ms)
            items: 5, // Muestra 5 items por defecto
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
    });

    $('#qty').on('keypress', function(e) {
        e.preventDefault();
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.product-variant-select').forEach(function(select) {
            select.addEventListener('change', function() {
                var selectedProductId = this.value;
                // Redirigir a la página del producto seleccionado
                window.location.href = '/producto/' + selectedProductId;
            });
        });
    });
</script>
