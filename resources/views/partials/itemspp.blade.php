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

@if (Auth::check())
    @php
        // Obtener los datos del usuario desde la tabla users_data y la tabla de direcciones
        $userData = DB::table('users_data')->where('user_id', Auth::id())->first();
        $userAddress = DB::table('users_address')->where('user_id', Auth::id())->first();
    @endphp

    <div id="profile-steps" class="container">
        <!-- Paso 1: Verificar si faltan datos personales -->
        @if (!$userData || empty($userData->nombre) || empty($userData->apellido_paterno) || empty($userData->telefono))
            <div id="profile-step-1" class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                <strong>¡Atención!</strong> Completa tu <a
                    href="{{ route('cuenta', ['section' => 'colDatos']) }}">perfil</a> para mejorar su experiencia en el
                sitio.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @else
            <!-- Paso 2: Verificar si faltan direcciones, solo si los datos personales están completos -->
            @if (!$userAddress)
                <div id="profile-step-2" class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                    <strong>¡Falta un paso más!</strong> Agregue su <a
                        href="{{ route('cuenta', ['section' => 'Direcciones']) }}">dirección</a> para recibir productos
                    en casa.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endif
    </div>

    <script>
        $(document).ready(function() {
            let step1 = $('#profile-step-1');
            let step2 = $('#profile-step-2');

            // Mostrar el paso 1 (datos personales) primero si falta
            if (step1.length) {
                step1.fadeIn('slow', function() {
                    setTimeout(function() {
                        step1.fadeOut('slow', function() {
                            // Si falta la dirección, mostrar el paso 2 después de completar los datos personales
                            if (step2.length) {
                                step2.fadeIn('slow');
                            }
                        });
                    }, 8000); // Mostrar por 8 segundos
                });
            } else if (step2.length) {
                // Si los datos personales ya están completos pero falta la dirección, mostrar el paso 2
                step2.fadeIn('slow');
            }
        });
    </script>
@endif




<div id="contenedorsup" class="container d-flex justify-content-center">
    <div class="row">
        <!-- CARROUSEL -->
        @if (isset($carouselImages) && count($carouselImages) > 0)
            <div class="col-md-7 order-1 order-md-2 align-items-center d-flex justify-content-center">
                <div id="carouselExample" class="carousel slide carousel-custom-size" data-bs-ride="carousel"
                    data-bs-interval="3000">
                    <!-- Indicadores -->
                    <div class="carousel-indicators">
                        @foreach ($carouselImages as $index => $image)
                            <button type="button" data-bs-target="#carouselExample"
                                data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"
                                aria-current="true" aria-label="Slide {{ $index + 1 }}"></button>
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
        <!-- Grid de Imágenes -->
        <div class="col-12 order-2 order-md-1 col-md-5">
            <div class="row">
                @foreach ($gridImages as $gridImage)
                    <div class="col-6 mb-4 d-flex justify-content-center">
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

<div class="container mt-0">
    @foreach ($destacados->chunk(4) as $chunk)
        <div class="row mt-4">
            @foreach ($chunk as $producto)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="product-container " style="position: relative;">
                        @if ($producto->descuento > 0)
                            <div class="badge-offer-destacados">
                                <i class="fas fa-tags"></i> ¡Oferta!
                            </div>
                        @endif

                        <a href="{{ url('/producto/' . $producto->id.'-'. $producto->nombre) }}" class="text-decoration-none">
                            <div>
                            <img src="{{ $producto->imagen_principal }}" alt="{{ $producto->nombre }}">
                            </div>
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
                        <div class="product-info1">
                            <span>{{ ucwords(strtolower($producto->nombre)) }}<br />{{ ucwords(strtolower($producto->marca)) }}</span>
                            <span class="product-price">${{ number_format($producto->precio_final, 2, '.', ',') }} MXN</span>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>


<style>

    /* Estilos del carrusel */
    .carousel-custom-size {
        width: 100%;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .carousel-image {
        width: 100%;
        height: auto;
        object-fit: contain;
    }

    @media (min-width: 768px) {
        .carousel-custom-size {
            height: 380px;
        }

        .carousel-image {
            height: 100%;
        }
    }

    /* Estilos para las imágenes del grid */
    .offer-container img {
        width: 100%;
    }

</style>
