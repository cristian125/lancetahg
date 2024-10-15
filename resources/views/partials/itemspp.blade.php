<div class="container p-1">
    @if ($errors->has('email'))
        <div class="alert alert-danger">
            {{ $errors->first('email') }}
            <button type="button" class="btn btn close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <script>
            $(document).ready(function() {
                setTimeout(() => {
                    $('.alert').fadeOut('slow');
                }, 5000);
            });
        </script>
    @endif
</div>

<div id="contenedorsup" class="container d-flex justify-content-center">
    <div class="row">
        <div class="container mt-4">
            <div class="row">
                @foreach ($gridImages as $gridImage)
                    <div class="col-md-6 mb-4 d-flex justify-content-center">
                        <div class="offer-container">
                            <a href="{{ url('/producto/' . $gridImage->product_id) }}">
                                <img src="{{ asset('storage/' . $gridImage->image_path) }}"
                                    alt="Imagen {{ $loop->iteration }}" class="img-fluid">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>


    <!-- CARROUSEL -->
    @if (isset($carouselImages) && count($carouselImages) > 0)
        <div class="col-md-7 order-1 order-md-2 align-items-center d-flex justify-content-center">
            <div id="carouselExample" class="carousel slide carousel-custom-size" data-bs-ride="carousel"
                data-bs-interval="3000">
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach ($carouselImages as $index => $image)
                        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="{{ $index }}"
                            class="{{ $index == 0 ? 'active' : '' }}" aria-current="true"
                            aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach ($carouselImages as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            @if (!empty($image->product_link))
                                <a href="{{ $image->product_link }}" target="_blank">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        class="d-block w-100 carousel-image" alt="...">
                                </a>
                            @else
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    class="d-block w-100 carousel-image" alt="...">
                            @endif
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    @endif

</div>
</div>

@if ($bannerImage)
    <div class="container mt-4">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12 text-center">
                <img src="{{ asset('storage/' . $bannerImage->image_path) }}" alt="Imagen Destacada" class="img-fluid">
            </div>
        </div>
    </div>
@endif



<!-- CONTENEDOR QUE DIVIDE LAS SECCIONES -->
<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-4 text-center" id="item-centrado">
            PRODUCTOS DESTACADOS
        </div>
    </div>
</div>

<div class="container mt-4">
    @foreach ($destacados->chunk(4) as $chunk)
        <div class="row mt-4">
            @foreach ($chunk as $producto)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="product-container" style="position: relative;">
                        @if ($producto->descuento > 0)
                            <div class="badge-offer-destacados">
                                <i class="fas fa-tags"></i> ¡Oferta!
                            </div>
                        @endif

                        <a href="{{ url('/producto/' . $producto->id) }}" class="text-decoration-none">
                            <img src="{{ $producto->imagen_principal }}" alt="{{ $producto->nombre }}">
                            <div class="overlay">
                                <div class="overlay-text">
                                    <i class="bi bi-eye"></i>
                                </div>
                            </div>
                        </a>
                        <div class="overlay-cart">
                            <button class="btn-add-to-cart" data-id="{{ $producto->id }}"
                                data-nos="{{ $producto->no_s }}">
                                <i class="bi bi-cart"></i>
                            </button>
                        </div>
                        <div class="overlay-info">
                            <p>{{ $producto->no_s }}</p>
                            <p>{{ $producto->nombre }}<br>{{ $producto->marca }}</p>
                        </div>
                        <div class="product-info">
                            <p class="product-serial">{{ $producto->no_s }}</p>
                            <p>{{ $producto->nombre }}<br />{{ $producto->marca }}</p>
                            <p class="product-price">${{ number_format($producto->precio_final, 2, '.', ',') }} MXN
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>


<style>
    /* Asegura que el carrusel tenga un tamaño consistente */
    .carousel-custom-size {
        width: 820px;
        height: 380px;
        /* Ajusta esta altura según tus necesidades */
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f8f8;
        /* Fondo para cualquier imagen que no llene el espacio */
        overflow: hidden;
        /* Para ocultar cualquier contenido desbordante */
    }

    /* Todas las imágenes dentro del carrusel deben ajustarse sin recortarse */
    .carousel-image {
        max-width: 100%;
        important ! max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        /* Las imágenes se ajustarán dentro del contenedor sin recortarse */
        object-position: center;
        /* Centra la imagen horizontal y verticalmente */
        background-color: #f8f8f8;
        /* Fondo de relleno si la imagen no ocupa todo el espacio */
    }
</style>
