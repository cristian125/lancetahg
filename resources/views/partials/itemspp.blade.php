<div id="contenedorsup" class="container d-flex justify-content-center">
    <div class="row">
        <!-- CARROUSEL -->
        <div class="col-md-7 order-1 order-md-2 align-items-center d-flex justify-content-center">
            <div id="carouselExample" class="carousel slide carousel-custom-size" data-bs-ride="carousel" data-bs-interval="3000">
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="{{ asset('storage/carousel/loreip.jpg') }}" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('storage/carousel/lanz.png') }}" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('storage/carousel/test1.jpg') }}" class="d-block w-100" alt="...">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>

        <!-- LOS CUATRO CONTENEDORES DE 2X2 CON LAS OFERTAS -->
        <div class="col-md-5 order-2 order-md-1">
            <div class="row mt-4">
                <div class="col-6">
                    <div class="d-flex justify-content-center align-items-center p-2" style="height: 100%;">
                        <img src="{{ asset('storage/carousel/pruebacuadricula.jpg') }}" alt="Imagen 1" class="img-fluid">
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex justify-content-center align-items-center p-2" style="height: 100%;">
                        <img src="{{ asset('storage/carousel/pruebacuadricula.jpg') }}" alt="Imagen 2" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-6">
                    <div class="d-flex justify-content-center align-items-center p-2" style="height: 100%;">
                        <img src="{{ asset('storage/carousel/pruebacuadricula.jpg') }}" alt="Imagen 3" class="img-fluid">
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex justify-content-center align-items-center p-2" style="height: 100%;">
                        <img src="{{ asset('storage/carousel/pruebacuadricula.jpg') }}" alt="Imagen 4" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- CONTENEDOR QUE DIVIDE LAS SECCIONES --> 
<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-4 text-center" id="item-centrado">
            PRODUCTOS DESTACADOS
        </div>
    </div>
</div>

<div class="container mt-4">
    @foreach($destacados->chunk(4) as $chunk)
        <div class="row mt-4">
            @foreach($chunk as $producto)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="product-container">
                        <a href="{{ url('/producto/' . $producto->id) }}" class="text-decoration-none">
                            <!-- Usar la imagen principal calculada en el controlador -->
                            <img src="{{ $producto->imagen_principal }}" alt="{{ $producto->nombre }}">
                            <div class="overlay">
                                <div class="overlay-text">
                                    <i class="bi bi-eye"></i> <!-- Icono de ojo -->
                                </div>
                            </div>
                        </a>
                        <div class="overlay-cart">
                            <button class="btn-add-to-cart" data-id="{{ $producto->id }}">
                                <i class="bi bi-cart"></i> <!-- Icono de carrito de compras -->
                            </button>
                        </div>
                        <div class="overlay-info">
                            <p>{{ $producto->no_s }}</p> <!-- Número de serie -->
                            <p>{{ $producto->nombre }}<br>{{ $producto->marca }}</p> <!-- Descripción -->
                        </div>
                        <div class="product-info">
                            <p class="product-serial">{{ $producto->no_s }}</p>
                            <p>{{ $producto->nombre }}<br />{{ $producto->marca }}</p>
                            <p class="product-price">${{ $producto->precio_final }} MXN</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>


</div>




<script>

$(document).ready(function () {
    // Evento para agregar al carrito
    $(document).on('click', '.btn-add-to-cart', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let productId = $(this).data('id');
        addToCart(productId);  
    });

    function addToCart(productId) {
        let token = $('meta[name="csrf-token"]').attr('content'); 
    
        $.ajax({
            type: "POST",
            url: "/cart/add",  
            data: {
                id: productId,
                _token: token
            },
            dataType: "json",
            success: function (response) {

                updateCartCount();
            },
            error: function (data) {
                if (data.status === 401) {
                    showLoginPopover();  
                } else {
                    alert('Ocurrió un error al añadir el producto al carrito. Intente de nuevo.');
                }
            }
        });
    }

    function updateCartCount() {
        // Aquí deberías agregar la lógica para actualizar el contador del carrito.
        loadCartItems();  // Puedes usar esta función si ya la tienes definida
    }

    function showLoginPopover() {
        // Muestra un popover si el usuario no está logueado
        $('#add-to-cart').popover({
            content: 'Por favor inicie sesión para agregar el producto al carrito.',
            placement: 'bottom',
            trigger: 'focus',
            customClass: 'popover-danger bg-danger fw-bold'
        }).popover('show');
    }
});


</script>