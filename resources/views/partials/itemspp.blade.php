@extends('template')

@section('body')
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
                    }, 15000);
                });
            </script>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    </div>

    @if (Auth::check())
        @php
            $userData = DB::table('users_data')->where('user_id', Auth::id())->first();
            $userAddress = DB::table('users_address')->where('user_id', Auth::id())->first();
        @endphp

        <div id="profile-steps" class="container">
            @if (!$userData || empty($userData->nombre) || empty($userData->apellido_paterno) || empty($userData->telefono))
                <div id="profile-step-1" class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                    <strong>¡Atención!</strong> Completa tu <a
                        href="{{ route('cuenta', ['section' => 'colDatos']) }}">perfil</a> para mejorar su experiencia en el
                    sitio.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @else
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

                if (step1.length) {
                    step1.fadeIn('slow', function() {
                        setTimeout(function() {
                            step1.fadeOut('slow', function() {
                                if (step2.length) {
                                    step2.fadeIn('slow');
                                }
                            });
                        }, 8000);
                    });
                } else if (step2.length) {
                    step2.fadeIn('slow');
                }
            });
        </script>
    @endif

    <div id="contenedorsup" class="container">
        <div class="row">
            <!-- CARROUSEL -->
            @if (isset($carouselImages) && count($carouselImages) > 0)
                <div class="col-md-7 order-1 order-md-2 align-items-center d-flex justify-content-center mb-2 mb-md-0">
                    <div id="carouselExample" class="carousel slide carousel-custom-size" data-bs-ride="carousel"
                        data-bs-interval="3000">
                        <!-- Indicadores -->
                        <div class="carousel-indicators">
                            @foreach ($carouselImages as $index => $image)
                                <button type="button" data-bs-target="#carouselExample"
                                    data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"
                                    aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
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

            <!-- GRID DE PRODUCTOS -->
            <div class="col-12 order-2 order-md-1 col-md-5 mt-2 mt-md-4">
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

    <!-- Banners Desktop y Mobile -->
    @if ($desktopBanner || $mobileBanner)
        <div class="container mt-4">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-12 text-center">
                    <!-- Banner Desktop: Visible en pantallas medianas (md) y superiores -->
                    @if ($desktopBanner)
                        <img src="{{ asset('storage/' . $desktopBanner->image_path) }}" alt="Banner Desktop" class="img-fluid d-none d-md-block">
                    @endif

                    <!-- Banner Mobile: Visible en pantallas pequeñas (sm) y extra pequeñas (xs) -->
                    @if ($mobileBanner)
                        <img src="{{ asset('storage/' . $mobileBanner->image_path) }}" alt="Banner Móvil" class="img-fluid d-block d-md-none">
                    @endif
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

    <!-- Productos Destacados -->
    <div class="container mt-0">
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

                            <a href="{{ url('/producto/' . $producto->id . '-' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($producto->nombre))) }}"
                                class="text-decoration-none">
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
                                <p>{{ $producto->nombre }}<br><span class="marca">{{ $producto->marca }}</span></p>
                            </div>
                            <div class="product-info1">
                                <span class="nprod">{{ ucwords(strtolower($producto->nombre)) }}<br /><span
                                        class="marca">{{ ucwords(strtolower($producto->marca)) }}</span></span>
                                <span class="product-price">${{ number_format($producto->precio_final, 2, '.', ',') }}
                                    MXN</span>
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
                height: auto;
                width: 100%;
            }

            .carousel-image {
                height: 100%;
            }
        }

        .offer-container img {
            width: 100%;
        }

        .marca {
            color: #838383;
        }

        .nprod {
            color: #252525;
        }

        /* Estilo para los indicadores del carrusel */
        .carousel-indicators li {
            background-color: #005f7f; /* Color de los indicadores */
            width: 12px;
            height: 12px;
            border-radius: 50%; /* Indicadores como puntos */
        }

        .carousel-indicators button {
            background-color: #444444 !important; /* Color para los indicadores inactivos */
        }

        .carousel-indicators .active {
            background-color: #70cbce !important; /* Color del indicador activo */
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #333; /* Color de las flechas */
            border-radius: 50%; /* Forma redondeada */
            width: 40px;
            height: 40px;
        }

        /* Estilo para el contenedor que divide las secciones */
        #item-centrado {
            font-size: 24px; /* Tamaño de fuente grande para destacar el texto */
            font-weight: bold; /* Negrita para resaltar el texto */
            color: #005f7f; /* Color del texto */
            /* Espaciado interno superior e inferior */
            border-bottom: 2px solid #2396e2; /* Línea inferior para separación visual */
            margin-top: 40px; /* Margen superior para separación con el contenido anterior */
            margin-bottom: 40px; /* Margen inferior para separación con el contenido siguiente */
        }

        /* Estilo para el contenedor de la fila */
        .row.justify-content-center.align-items-center {
            margin: 20px; /* Espacio adicional entre esta fila y el contenido siguiente */
        }
        @media (max-width: 700px){

            .row.justify-content-center.align-items-center {
            margin: -20px; /* Espacio adicional entre esta fila y el contenido siguiente */
        }
        }

        /* Estilo general para los productos destacados */
        .product-container {
            position: relative; /* Necesario para que el botón y otros elementos se posicionen correctamente */
            overflow: hidden; /* Oculta cualquier cosa que se salga del contenedor */
            text-align: center; /* Centra todo el contenido dentro del contenedor */
            padding: 10px; /* Espaciado interno */
            background-color: #ffffff;
            border-radius: 10px; /* Bordes redondeados */
            transition: all 0.3s ease; /* Transición suave */
            width: 280px; /* Establece un ancho fijo por defecto */
            height: 305px; /* Establece una altura constante */
            margin: 0 auto; /* Centrar el contenedor horizontalmente */
        }

        /* Estilo para las imágenes */
        .product-container img {
            width: 100% !important; /* Asegura que la imagen use todo el ancho del contenedor */
            height: 200px !important; /* Fija la altura de la imagen para mantener proporción cuadrada */
            object-fit: cover !important; /* Cubre el contenedor con la imagen, recortando si es necesario */
            border-radius: 5px !important; /* Bordes redondeados para las imágenes */
            background-color: #f5f5f5; /* Fondo gris claro para mostrar mientras carga la imagen */
        }

        /* Estilo para la información del producto */
        .product-info1 {
            color: #444 !important; /* Color gris oscuro para facilitar la lectura */
            width: 100%;
            height: 35%;
            font-size: 15px !important; /* Tamaño de fuente más pequeño para la lectura */
            line-height: 1 !important; /* Espaciado entre líneas para mejor legibilidad */
            font-weight: 400 !important; /* Peso de fuente regular */
            /* background-color: #ffffff !important; /* Fondo blanco */
            padding: 5px !important; /* Espaciado interno */
            border-radius: 8px !important; /* Bordes redondeados */
            box-shadow: none !important; /* Sin sombra */
            text-align: center !important; /* Alinear texto al centro para una presentación más uniforme */
            text-transform: capitalize; /* Capitalizar texto */
        }

        /* Estilo para el número de serie */
        .product-serial {
            font-size: 18px !important; /* Tamaño de fuente más pequeño */
            color: #00B398 !important; /* Color azul para destacar */
            font-weight: bold !important; /* Negrita para enfatizar */
            margin-bottom: 5px !important; /* Espacio inferior */
            display: block !important; /* Mantiene el formato en bloque */
        }

        /* Estilo para el precio del producto */
        .product-container .product-price {
            font: 600 21px/26px "Open Sans", sans-serif;
            font-size: 20px !important; /* Tamaño de fuente adecuado */
            color: #2396e2 !important; /* Color azul oscuro para el precio */
            /* font-weight: bold !important; Negrita para destacar el precio */
            background-color: transparent !important; /* Elimina el fondo */
            padding: 0 !important; /* Sin espaciado interno */
            display: block !important; /* Para que el fondo se ajuste al texto */
            text-align: center !important; /* Centrar el texto */
            position: relative; /* Posiciona el precio de forma absoluta */
            bottom: 1%; /* Fija el precio a 10px del fondo del contenedor */
            left: 0; /* Alinea a la izquierda */
            right: 0; /* Alinea a la derecha */
            text-shadow: none !important; /* Sin sombra de texto */
            border: none !important; /* Sin bordes */
            letter-spacing: normal !important; /* Espaciado normal entre letras */
            z-index: 1;
        }

        /* Estilo para el botón */
        .product-container button {
            position: absolute;
            bottom: 10px; /* Espaciado desde el fondo del contenedor */
            left: 10px; /* Alineado a la izquierda */
            right: 10px; /* Alineado a la derecha */
            width: calc(100% - 20px); /* Ajusta el ancho para dar espacio a los márgenes */
            padding: 10px;
            background-color: #ff9900; /* Color naranja del botón */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Efecto hover para el botón */
        .product-container button:hover {
            background-color: #e67e22; /* Cambiar color del botón al pasar el mouse */
        }

        /* Efecto hover en el contenedor del producto */
        .product-container:hover {
            transform: translateY(-5px); /* Levantar el contenedor ligeramente */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Sombra más intensa */
        }

        /* Superposición para el botón "Ver" */
        .product-container .overlay {
            position: absolute;
            margin-top: -10px;
            left: 50%; /* Centra horizontalmente */
            top: 65%; /* Ajusta esta posición para mover el rectángulo más arriba */
            transform: translate(-50%, -50%); /* Centra el rectángulo */
            background-color: #005f7f88; /* Color azul con transparencia */
            color: #ffffff;
            padding: 9.5px 100%; /* Ajusta el tamaño del rectángulo */
            border-radius: 5px; /* Bordes redondeados */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
            z-index: 50;
        }

        .product-container .overlay:hover {
            background-color: #005f7f;
        }

        /* Superposición para el botón "Añadir al Carrito" */
        .product-container .overlay-cart {
            position: absolute;
            left: 50%; /* Centra horizontalmente */
            top: 83%; /* Ajusta esta posición para mover el rectángulo más abajo */
            transform: translate(-50%, -50%); /* Centra el rectángulo */
            color: white;
            padding: 3% 100%; /* Ajusta el tamaño del rectángulo */
            border-radius: 5px; /* Bordes redondeados */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
            z-index: 50;
        }

        .product-container:hover .overlay,
        .product-container:hover .overlay-cart {
            opacity: 1;
            visibility: visible;
        }

        .overlay-text {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        /* Estilo para el botón "Añadir al Carrito" en el overlay */
        .overlay-cart button {
            margin: 0;
            background-color: #00B39888;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .overlay-cart button:hover {
            background-color: #00B398;
        }

        /* Superposición adicional para mostrar la descripción y número de serie */
        .product-container .overlay-info {
            display: none;
            position: absolute;
            left: 0; /* Alinea el overlay al borde izquierdo del contenedor */
            right: 0; /* Alinea el overlay al borde derecho del contenedor */
            top: 50.2%; /* Centra verticalmente el overlay */
            transform: translateY(-50%); /* Ajusta la posición para mantenerlo centrado */
            background-color: #00B398; /* Fondo oscuro con transparencia */
            color: white;
            height: 18%;
            padding: 5px 10px; /* Espaciado interno reducido */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
            text-align: center; /* Centra el texto dentro del overlay */
            font-size: 11px; /* Tamaño de fuente más pequeño */
            line-height: 1.2; /* Reduce el espacio entre líneas */
        }

        /* Mostrar el overlay al hacer hover sobre el contenedor */
        .product-container:hover .overlay-info {
            opacity: 1;
            visibility: visible;
        }

        /* Ajustes específicos para el número de serie y la descripción */
        .overlay-info p {
            margin: 2px 0; /* Reduce el margen entre el número de serie y la descripción */
        }

        .badge-offer {
            position: absolute;
            top: 10px;
            left: 10px; /* Cambiado de right a left */
            background-color: #ff0000;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            z-index: 10; /* Asegura que el badge esté encima de otros elementos */
        }

        .badge-offer-destacados {
            position: absolute;
            top: 10px;
            right: 10px; /* Cambiado a la derecha */
            background-color: #ff0000;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            z-index: 10; /* Asegura que el badge esté encima de otros elementos */
        }

        .icon-small {
            width: 50px;  /* Ajusta este valor según el tamaño que desees */
            height: auto;  /* Mantén la proporción de la imagen */
        }

        /* Mostrar las superposiciones cuando el producto está activo */
        .product-container.active .overlay,
        .product-container.active .overlay-cart,
        .product-container.active .overlay-info {
            opacity: 1;
            visibility: visible;
        }

        /* Asegurar que las superposiciones no se muestren por defecto en móviles */
        @media (max-width: 1067.98px) {
            .product-container .overlay,
            .product-container .overlay-cart,
            .product-container .overlay-info {
                opacity: 0;
                visibility: hidden;
            }

            /* Transición suave para las superposiciones */
            .product-container .overlay,
            .product-container .overlay-cart,
            .product-container .overlay-info {
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
        }

        /* Opcional: Ajustar el cursor para indicar interactividad */
        @media (hover: none) and (pointer: coarse) {
            .product-container > a {
                cursor: pointer;
            }
        }

        /* Hacer que el contenedor sea responsivo en pantallas pequeñas */
        @media (max-width: 1067.98px) {
            .product-container {
                width: 100%; /* Ocupa todo el ancho disponible */
                max-width: 280px; /* Limita el ancho máximo para mantener la consistencia */
                margin: 0 auto; /* Centrar el contenedor */
            }
        }
    </style>

    <!-- Scripts adicionales al final de la plantilla -->
    <script>
        $(document).ready(function() {
            // Función mejorada para detectar dispositivos táctiles
            function isTouchDevice() {
                return window.matchMedia("(hover: none) and (pointer: coarse)").matches;
            }

            if (isTouchDevice()) {
                // Manejar el evento 'click' en los enlaces de productos
                $('.product-container > a').on('click', function(e) {
                    var $productContainer = $(this).closest('.product-container');

                    if ($productContainer.hasClass('active')) {
                        // Si ya está activo, permitir la navegación
                        $productContainer.removeClass('active');
                        // No se previene el comportamiento predeterminado, permitiendo la navegación
                    } else {
                        // Si no está activo, prevenir la navegación y activar el producto
                        e.preventDefault();

                        // Cerrar cualquier otro producto activo
                        $('.product-container.active').removeClass('active');

                        // Activar el producto seleccionado
                        $productContainer.addClass('active');
                    }
                });

                // Cerrar los productos activos al tocar fuera de ellos
                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest('.product-container').length) {
                        $('.product-container.active').removeClass('active');
                    }
                });
            }
        });
    </script>

@endsection
