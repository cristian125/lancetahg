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
        <div class="row justify-content-center">
            @if (isset($carouselImages) && count($carouselImages) > 0)
                @if (isset($gridImages) && count($gridImages) > 0)
                    <div class="col-md-7 order-1 order-md-2 align-items-center d-flex justify-content-center mb-2 mb-md-0">
                @else
                    <!-- No hay imágenes en el grid, mantiene col-md-7 y añade mx-auto para centrar -->
                    <div class="col-md-7 order-1 order-md-2 align-items-center d-flex justify-content-center mb-2 mb-md-0 mx-auto">
                @endif
                    <div id="carouselExample" class="carousel slide carousel-custom-size" data-bs-ride="carousel"
                        data-bs-interval="3000">
                        <div class="carousel-indicators">
                            @foreach ($carouselImages as $index => $image)
                                <button type="button" data-bs-target="#carouselExample"
                                    data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"
                                    aria-current="{{ $index == 0 ? 'true' : 'false' }}"
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
        
            @if (isset($gridImages) && count($gridImages) > 0)
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
            @endif
        </div>
    </div>
    


    @if ($desktopBanner || $mobileBanner)
        <div class="container mt-4">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-12 text-center">
                 
                    @if ($desktopBanner)
                        <img src="{{ asset('storage/' . $desktopBanner->image_path) }}" alt="Banner Desktop" class="img-fluid d-none d-md-block">
                    @endif
                    @if ($mobileBanner)
                        <img src="{{ asset('storage/' . $mobileBanner->image_path) }}" alt="Banner Móvil" class="img-fluid d-block d-md-none">
                    @endif
                </div>
            </div>
        </div>
    @endif

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


        .carousel-indicators li {
            background-color: #005f7f; 
            width: 12px;
            height: 12px;
            border-radius: 50%; 
        }

        .carousel-indicators button {
            background-color: #444444 !important; 
        }

        .carousel-indicators .active {
            background-color: #70cbce !important; 
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #333; 
            border-radius: 50%; 
            width: 40px;
            height: 40px;
        }


        #item-centrado {
            font-size: 24px; 
            font-weight: bold; 
            color: #005f7f; /

            border-bottom: 2px solid #2396e2; /
            margin-top: 40px; 
            margin-bottom: 40px; 
        }

      
        .row.justify-content-center.align-items-center {
            margin: 20px; 
        }
        @media (max-width: 700px){

            .row.justify-content-center.align-items-center {
            margin: -20px; 
        }
        }

 
        .product-container {
            position: relative;
            overflow: hidden; 
            text-align: center; 
            padding: 10px; 
            background-color: #ffffff;
            border-radius: 10px; 
            transition: all 0.3s ease; 
            width: 280px; 
            height: 305px; 
            margin: 0 auto; 
        }


        .product-container img {
            width: 100% !important; 
            height: 200px !important; 
            object-fit: cover !important; 
            border-radius: 5px !important; 
            background-color: #f5f5f5; 
        }


        .product-info1 {
            color: #444 !important; 
            width: 100%;
            height: 35%;
            font-size: 15px !important; 
            line-height: 1 !important; 
            font-weight: 400 !important; 
           
            padding: 5px !important; 
            border-radius: 8px !important; 
            box-shadow: none !important; 
            text-align: center !important;
            text-transform: capitalize; 
        }

       
        .product-serial {
            font-size: 18px !important; 
            color: #00B398 !important; 
            font-weight: bold !important;
            margin-bottom: 5px !important;
            display: block !important; 
        }


        .product-container .product-price {
            font: 600 21px/26px "Open Sans", sans-serif;
            font-size: 20px !important; 
            color: #2396e2 !important; 
          
            background-color: transparent !important; 
            padding: 0 !important; 
            display: block !important; 
            text-align: center !important; 
            position: relative; 
            bottom: 1%; 
            left: 0; 
            right: 0; 
            text-shadow: none !important; 
            border: none !important; 
            letter-spacing: normal !important; /
            z-index: 1;
        }


        .product-container button {
            position: absolute;
            bottom: 10px; 
            left: 10px;
            right: 10px; 
            width: calc(100% - 20px); 
            padding: 10px;
            background-color: #ff9900; 
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }


        .product-container button:hover {
            background-color: #e67e22; 
        }


        .product-container:hover {
            transform: translateY(-5px); 
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); 
        }


        .product-container .overlay {
            position: absolute;
            margin-top: -10px;
            left: 50%; 
            top: 65%; 
            transform: translate(-50%, -50%); 
            background-color: #005f7f88; 
            color: #ffffff;
            padding: 9.5px 100%; 
            border-radius: 5px; 
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
            z-index: 50;
        }

        .product-container .overlay:hover {
            background-color: #005f7f;
        }


        .product-container .overlay-cart {
            position: absolute;
            left: 50%; 
            top: 83%; 
            transform: translate(-50%, -50%); 
            color: white;
            padding: 3% 100%; 
            border-radius: 5px;
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

       
        .product-container .overlay-info {
            display: none;
            position: absolute;
            left: 0; 
            right: 0; 
            top: 50.2%;
            transform: translateY(-50%); 
            background-color: #00B398; 
            color: white;
            height: 18%;
            padding: 5px 10px; 
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
            text-align: center;
            font-size: 11px; 
            line-height: 1.2; 
        }

       
        .product-container:hover .overlay-info {
            opacity: 1;
            visibility: visible;
        }

      
        .overlay-info p {
            margin: 2px 0;
        }

        .badge-offer {
            position: absolute;
            top: 10px;
            left: 10px; 
            background-color: #ff0000;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            z-index: 10; 
        }

        .badge-offer-destacados {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff0000;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            z-index: 10; 
        }

        .icon-small {
            width: 50px;  
            height: auto;  
        }

      
        .product-container.active .overlay,
        .product-container.active .overlay-cart,
        .product-container.active .overlay-info {
            opacity: 1;
            visibility: visible;
        }

        
        @media (max-width: 1067.98px) {
            .product-container .overlay,
            .product-container .overlay-cart,
            .product-container .overlay-info {
                opacity: 0;
                visibility: hidden;
            }

            
            .product-container .overlay,
            .product-container .overlay-cart,
            .product-container .overlay-info {
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
        }

        
        @media (hover: none) and (pointer: coarse) {
            .product-container > a {
                cursor: pointer;
            }
        }

       
        @media (max-width: 1067.98px) {
            .product-container {
                width: 100%; 
                max-width: 280px; 
                margin: 0 auto; 
            }
        }
    </style>

    
    <script>
        $(document).ready(function() {
           
            function isTouchDevice() {
                return window.matchMedia("(hover: none) and (pointer: coarse)").matches;
            }

            if (isTouchDevice()) { Manejar el evento 'click' en los enlaces de productos
                $('.product-container > a').on('click', function(e) {
                    var $productContainer = $(this).closest('.product-container');

                    if ($productContainer.hasClass('active')) {
                        
                        $productContainer.removeClass('active');
                        
                    } else {
                       
                        e.preventDefault();

                        
                        $('.product-container.active').removeClass('active');

                      
                        $productContainer.addClass('active');
                    }
                });

               
                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest('.product-container').length) {
                        $('.product-container.active').removeClass('active');
                    }
                });
            }
        });
    </script>

@endsection
