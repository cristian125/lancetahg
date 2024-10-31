<!--
<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head> -->
<!-- Banner de Cookies -->
<!-- Banner de Cookies -->
<div id="cookieConsent" class="cookie-banner fixed-bottom p-3 d-none" style="z-index: 9999;">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="cookie-message">
            <span>Este sitio utiliza cookies para garantizar que obtenga la mejor experiencia en nuestro sitio
                web.</span>
        </div>
        <button id="acceptCookies" class="btn btn-info">Aceptar</button>
    </div>
</div>

<style>
    .cookie-banner {
        background-color: #26d2b6 !important;
        /* Color del fondo */
        color: #005f7f;
        padding: 15px;
        font-size: 14px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Sombra ligera para destacar */
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .cookie-banner .btn {
        font-weight: bold;
        background-color: #005f7f;
        border: none;
        color: white;
    }

    .cookie-banner .btn:hover {
        background-color: #004f6f;
    }

    .cookie-message a {
        color: #005f7f !important;
        text-decoration: underline;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const cookieBanner = document.getElementById('cookieConsent');
        const acceptCookiesBtn = document.getElementById('acceptCookies');

        // Comprobar si el usuario ya ha aceptado las cookies
        if (!localStorage.getItem('cookiesAccepted')) {
            // Mostrar el banner si no ha sido aceptado antes
            cookieBanner.classList.remove('d-none');
            cookieBanner.classList.add('d-flex');
        }

        // Al hacer clic en "Aceptar"
        acceptCookiesBtn.addEventListener('click', function() {
            localStorage.setItem('cookiesAccepted', 'true');
            cookieBanner.classList.remove('d-flex');
            cookieBanner.classList.add('d-none');
        });
    });
</script>

<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-custom navbar-dark">
    <div class="container justify-content-center">
        <a class="navbar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
            <img src="{{ asset('storage/logos/logolhg.png') }}" alt="Logo" style="height: 30px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item dropdown megamenu" id="categoryMenuItem">
                    <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdownMenuLink" role="button"
                        aria-expanded="false">
                        <i class="bi bi-list"></i> Categorías
                    </a>
                    <div class="dropdown-menu megamenu-content" aria-labelledby="navbarDropdownMenuLink">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Categorías</h5>
                                    <ul id="categoria-menu" class="list-unstyled">
                                        <!-- Aquí se insertarán las categorías desde el script JavaScript -->
                                    </ul>
                                </div>
                                <div class="col-md-9">
                                    <!-- Subcategorías se insertarán aquí por JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <!-- Buscador en la barra de navegación -->
                <li class="nav-item me-2 w-100">
                    <form action="{{ route('product.search') }}" method="GET" class="d-flex position-relative w-100"
                        role="search" id="custom-search-form">
                        <input id="custom-search" name="search" class="form-control me-2 w-100" type="search"
                            placeholder="Buscar productos..." aria-label="Search" autocomplete="off">

                        <button id="btnsearch" class="btn btn-outline-success g-recaptcha"
                            data-sitekey="{{ config('recapcha.site_key') }}" data-callback='onSubmit'
                            data-action='custom-search-form' type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        <div id="custom-search-results" class="position-absolute bg-white w-100 shadow rounded mt-1 p-2"
                            style="display:none; max-height: 400px; overflow-y: auto; z-index: 999; width: 200%;">
                            <ul id="custom-results-list" class="list-unstyled mb-0"></ul>
                        </div>
                    </form>
                </li>

                @if (Auth::check() == true)
                    @php
                        // Obtener los datos del usuario desde la tabla users_data
                        $userData = DB::table('users_data')->where('user_id', Auth::id())->first();
                    @endphp
                    <!-- Mostrar el nombre del usuario con tratamiento si está autenticado -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill me-2"></i>
                            <!-- Mostrar el tratamiento si existe, seguido del nombre -->
                            @if ($userData && !empty($userData->tratamiento))
                                {{ $userData->tratamiento }} {{ $userData->nombre }} {{ $userData->apellido_paterno }}
                            @else
                                {{ Auth::user()->name }}
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-4 shadow-lg border-0 rounded"
                            aria-labelledby="userDropdown" style="min-width: 300px;">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <!-- Botón para la cuenta -->
                                <div id="account-button-container" class="row">
                                    <a id="account-btn" href="/cuenta"
                                        class="btn btn-dark btn-block d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person-circle me-2"></i> Cuenta
                                    </a>
                                </div>

                                <!-- Botón para "Mis Pedidos" -->
                                <div id="orders-button-container" class="row">
                                    <a id="orders-btn" href="{{ route('myorders') }}"
                                        class="btn btn-secondary btn-block d-flex align-items-center justify-content-center">
                                        <i class="bi bi-box-seam me-2"></i> Mis Pedidos
                                    </a>
                                </div>

                                <!-- Botón para "Mis Direcciones" -->
                                <div id="addresses-button-container" class="row">
                                    <a id="addresses-btn" href="{{ route('cuenta', ['section' => 'Direcciones']) }}"
                                        class="btn btn-info btn-block d-flex align-items-center justify-content-center">
                                        <i class="bi bi-geo-alt me-2"></i> Direcciones
                                    </a>
                                </div>

                                <!-- Botón para "Datos Personales" -->
                                <div id="personal-data-button-container" class="row">
                                    <a id="personal-data-btn" href="{{ route('cuenta', ['section' => 'colDatos']) }}"
                                        class="btn btn-warning btn-block d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-person me-2"></i> Datos Personales
                                    </a>
                                </div>
                                <!-- Botón para "Ayuda" con estilo personalizado -->
                                <div id="help-button-container" class="row">
                                    <a id="help-btn" href="/page/ayuda"
                                        class="btn btn-light btn-outline-primary btn-block d-flex align-items-center justify-content-center ">
                                        <i class="bi bi-question-circle me-2"></i> Ayuda
                                    </a>
                                </div>
                                <!-- Botón para cerrar sesión -->
                                <div id="logout-button-container" class="row">
                                    <button id="logout-btn" type="submit"
                                        class="btn btn-danger w-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                    </button>
                                </div>
                            </form>
                        </div>


                    </li>
                @else
                    <!-- Mostrar el formulario de inicio de sesión si no está autenticado -->
                    <li class="nav-item dropdown" id="login-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button">
                            <i class="bi bi-person-fill"></i> Iniciar Sesión
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="loginDropdown">
                            <form action="{{ route('login') }}" method="POST" id="login-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="correo@gmail.com" autocomplete="off" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Contraseña" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                                <div class="mt-3">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <a href="{{ route('password.email') }}">¿Olvidó su contraseña?</a>
                                        </li>
                                        <li class="list-group-item">
                                            <a href="{{ route('register') }}">Crear Cuenta</a>
                                        </li>
                                    </ul>
                                </div>

                            </form>
                        </div>
                    </li>
                @endif
                <div id="btnCart" class="nav-item dropdown position-relative d-flex align-items-center">
                    <a class="nav-link" href="#" id="navbarDropdownCart" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('storage/iconos/carrito.png') }}" alt="Carrito de Compras" style="height: 30px;">
                    </a>
                    <span id="cart-item-count" class="badge bg-danger ms-2" style="display: none; font-size: 12px;">0</span> <!-- Contador de items -->

                    <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="navbarDropdownCart">
                        <div id="cart-items" class="list-group">
                        </div>
                        <div class="d-grid gap-2 mt-2">
                            <button id="verCarrito" class="btn btn-primary d-flex align-items-center justify-content-center">
                                <i class="bi bi-bag-check-fill me-2"></i> Ver Carrito
                            </button>
                        </div>

                        <form id="verCarritoForm" action="{{ route('cart.show') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="recaptcha_token" id="recaptchaToken">
                        </form>
                    </div>
                </div>

            </ul>
        </div>
    </div>
</nav>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadCategorias();
    });

    function loadCategorias() {
        $.ajax({
            type: "GET",
            url: "/get-categorias",
            dataType: "json",
            success: function(data) {
                let categoriasList = '';
                let subcategoriaContent = '';

                $.each(data, function(codigoDivision, division) {
                    let safeCodigo = codigoDivision.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g,
                        '');
                    let urlBase = `/categorias/${encodeURIComponent(codigoDivision)}`;

                    categoriasList += `
                    <li>
                        <a class="dropdown-item category-item" href="${urlBase}"
                            data-url="${urlBase}" data-target="#division-${safeCodigo}">
                            ${division.nombre}
                        </a>
                    </li>`;

                    subcategoriaContent += `
                    <div id="division-${safeCodigo}" class="row submenu" style="display: none;">
                        <h5>${division.nombre}</h5>
                        <div class="row">`;

                    const categorias = division.subcategorias;
                    const totalCategorias = Object.keys(categorias).length;
                    const quarter = Math.ceil(totalCategorias / 4);

                    let count = 0;
                    let colIndex = 0;

                    $.each(categorias, function(codCategoria, categoria) {
                        if (count % quarter === 0) {
                            if (colIndex > 0) {
                                subcategoriaContent += '</ul></div>';
                            }
                            subcategoriaContent +=
                                '<div class="col-md-3"><ul class="list-unstyled">';
                            colIndex++;
                        }

                        // Agregar opción de categoría general
                        let urlCategoria = `${urlBase}/${encodeURIComponent(codCategoria)}`;
                        subcategoriaContent +=
                            `<li><a href="${urlCategoria}"><strong>${categoria.nombre}</strong></a><ul class="ms-3">`;

                        $.each(categoria.subsubcategorias, function(i, subsubcategoria) {
                            let urlProducto =
                                `${urlCategoria}/${encodeURIComponent(subsubcategoria.codigo)}`;
                            subcategoriaContent +=
                                `<li><a href="${urlProducto}">${subsubcategoria.texto}</a></li>`;
                        });

                        subcategoriaContent += '</ul></li>';
                        count++;
                    });

                    subcategoriaContent += '</ul></div>';
                    subcategoriaContent += '</div>';
                    subcategoriaContent += '</div>';
                });

                $('#categoria-menu').html(categoriasList);
                $('.col-md-9').html(subcategoriaContent);

                initializeSubmenuBehavior();
            },
            error: function() {
                console.log('Error al cargar las categorías y subcategorías.');
                $('#categoria-menu').html(
                    '<li><a class="dropdown-item">Error al cargar categorías</a></li>');
                $('.col-md-9').html(
                    '<div class="row submenu"><div class="col-md-12"><p>No se pudieron cargar las subcategorías.</p></div></div>'
                );
            }
        });
    }

    function initializeSubmenuBehavior() {
        document.querySelectorAll('.category-item').forEach(function(categoryItem) {
            categoryItem.addEventListener('mouseenter', function() {
                document.querySelectorAll('.submenu').forEach(function(submenu) {
                    submenu.style.display = 'none'; // Ocultar todas las subcategorías
                });

                var target = categoryItem.getAttribute('data-target');
                if (target) {
                    document.querySelector(target).style.display =
                        'block'; // Mostrar la subcategoría correspondiente
                }
            });
        });

        document.querySelectorAll('.megamenu-content').forEach(function(menu) {
            menu.addEventListener('mouseleave', function() {
                document.querySelectorAll('.submenu').forEach(function(submenu) {
                    submenu.style.display =
                        'none'; // Ocultar todas las subcategorías cuando se sale del megamenú
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const loginDropdown = document.getElementById('login-dropdown');
        const loginToggle = loginDropdown.querySelector('.nav-link.dropdown-toggle');
        const dropdownMenu = loginDropdown.querySelector('.dropdown-menu');

        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');


        loginToggle.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            toggleDropdown();
        });


        document.addEventListener('click', function(event) {
            const dropdownRect = dropdownMenu.getBoundingClientRect();
            const distance = calculateDistance(event.clientX, event.clientY, dropdownRect);

            if (!loginDropdown.contains(event.target) && distance > 300) {
                dropdownMenu.classList.remove('show');
                loginDropdown.classList.remove('show');
            }
        });

        function toggleDropdown() {
            const isOpen = dropdownMenu.classList.contains('show');
            if (isOpen) {
                dropdownMenu.classList.remove('show');
                loginDropdown.classList.remove('show');
            } else {
                dropdownMenu.classList.add('show');
                loginDropdown.classList.add('show');
            }
        }

        function calculateDistance(x, y, rect) {
            const dx = Math.max(rect.left - x, x - rect.right, 0);
            const dy = Math.max(rect.top - y, y - rect.bottom, 0);
            return Math.sqrt(dx * dx + dy * dy);
        }

        // Evitar que el dropdown se cierre al interactuar con el campo de texto
        emailField.addEventListener('click', function(event) {
            event.stopPropagation();
            showDropdown();
        });

        passwordField.addEventListener('click', function(event) {
            event.stopPropagation();
            showDropdown();
        });

        // Función para mostrar el dropdown
        function showDropdown() {
            dropdownMenu.classList.add('show');
            loginDropdown.classList.add('show');
        }

        // Prevenir que el dropdown se cierre al interactuar con las sugerencias de autocompletado
        emailField.addEventListener('input', function() {
            showDropdown();
        });

        // Evitar el cierre al mover el mouse fuera del dropdown
        dropdownMenu.addEventListener('mouseleave', function(event) {
            if (event.relatedTarget && !dropdownMenu.contains(event.relatedTarget)) {
                // No cerrar el dropdown si el mouse se mueve hacia el borde
                showDropdown();
            }
        });
    });
</script>

{{-- <script>
    $(document).ready(function() {
        let typingTimer;
        const typingInterval = 300; // 0.3 segundos de espera para mejor experiencia

        // Configurar la acción de búsqueda al escribir
        $('#custom-search').on('keyup', function(e) {
            // Si se presiona Enter (código de tecla 13), no hacer nada y permitir que el formulario se envíe
            if (e.keyCode === 13) {
                return; // Permite que el formulario se envíe
            }

            clearTimeout(typingTimer);
            let searchWord = $(this).val().trim();

            if (searchWord.length > 2) {
                typingTimer = setTimeout(function() {
                    executeSearch(searchWord);
                }, typingInterval);
            } else {
                $('#custom-search-results').hide();
            }
        });

        // Manejar el evento submit del formulario (cuando se presiona Enter)
        // $('#custom-search-form').on('submit', function(e) {
        //     e.preventDefault(); // Prevenir el comportamiento predeterminado del formulario

        //     let searchWord = $('#custom-search').val().trim();

        //     if (searchWord.length > 0) {
        //         executeSearch(searchWord);
        //     }
        // });

        // Manejar el clic en el botón de búsqueda
        $('#btnsearch').on('click', function() {
            let searchWord = $('#custom-search').val().trim();

            if (searchWord.length > 0) {
                executeSearch(searchWord);
            }
        });

        // Cerrar el cuadro de búsqueda al hacer clic fuera
        $(document).click(function(e) {
            if (!$(e.target).closest('#custom-search-results, #custom-search').length) {
                $('#custom-search-results').hide();
            }
        });

        // Ejecutar búsqueda AJAX
        function executeSearch(query) {
            $.ajax({
                type: "GET",
                url: "/ajax-search",
                data: {
                    search: query
                },
                dataType: "json",
                success: function(data) {
                    displaySearchResults(data);
                },
                error: function() {
                    $('#custom-results-list').empty();
                    $('#custom-search-results').hide();
                }
            });
        }

        // Mostrar resultados de búsqueda
        function displaySearchResults(products) {
            const $resultsList = $('#custom-results-list');
            $resultsList.empty(); // Limpiar resultados anteriores

            if (products.length === 0) {
                $resultsList.append('<li class="text-center">No se encontraron productos.</li>');
            } else {
                products.forEach(function(product) {
                    let productHTML = `
                <li class="d-flex align-items-center mb-2" style="width: 100%;">
                    <a href="/producto/${product.id}" class="d-flex w-100 text-decoration-none text-dark">
                        <img src="${product.imagen_principal}" class="item-img me-2" alt="Producto">
                        <div class="custom-product-info d-flex justify-content-between w-100">
                            <div>
                                <p class="mb-0"><strong>${product.nombre}</strong></p>
                                <p class="mb-0">Código: ${product.no_s}</p>
                                ${product.descuento > 0 ? `<p class="mb-0 text-danger"><del>$${product.precio_unitario_IVAinc}</del> <strong>$${product.precio_final}</strong></p>` : `<p class="mb-0">Precio: $${product.precio_final}</p>`}
                            </div>
                            <button class="btn btn-primary btn-sm add-to-cart-btn" data-id="${product.id}"><i class="fas fa-cart-plus"></i></button>
                        </div>
                    </a>
                </li>`;
                    $resultsList.append(productHTML);
                });

                $('#custom-search-results').show();
            }

            // Manejar la adición al carrito
            $('.add-to-cart-btn').on('click', function(e) {
                e.preventDefault(); // Prevenir la redirección
                const productId = $(this).data('id');
                addToCart(productId);
            });
        }

        // Función para añadir al carrito y recargar la página
        function addToCart(productId) {
            $.ajax({
                type: "POST",
                url: "/cart/add",
                data: {
                    id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    window.location.reload(); // Recargar la página para actualizar el carrito
                },
                error: function() {
                    alert("Error al añadir el producto al carrito.");
                }
            });
        }
    });
</script> --}}

<script>
    $(document).ready(function() {
        let typingTimer;
        const typingInterval = 300; // 0.3 segundos de espera para mejor experiencia

        // Configurar la acción de búsqueda al escribir
        $('#custom-search').on('keyup', function(e) {
            // Si se presiona Enter (código de tecla 13), no hacer nada y permitir que el formulario se envíe
            if (e.keyCode === 13) {
                return; // Permite que el formulario se envíe
            }

            clearTimeout(typingTimer);
            let searchWord = $(this).val().trim();

            if (searchWord.length > 2) {
                typingTimer = setTimeout(function() {
                    executeSearch();
                }, typingInterval);
            } else {
                $('#custom-search-results').hide();
            }
        });

        // Manejar el clic en el botón de búsqueda
        $('#btnsearch').on('click', function() {
            let searchWord = $('#custom-search').val().trim();

            if (searchWord.length > 0) {
                executeSearch1();
            }
        });

        // Cerrar el cuadro de búsqueda al hacer clic fuera
        $(document).click(function(e) {
            if (!$(e.target).closest('#custom-search-results, #custom-search').length) {
                $('#custom-search-results').hide();
            }
        });

        // Ejecutar búsqueda AJAX
        function executeSearch(query) {
            $.ajax({
                type: "GET",
                url: "{{ route('product.search') }}",
                data: {
                    search: query,
                },
                dataType: "json",
                success: function(data) {
                    displaySearchResults(data);
                },
                error: function() {
                    $('#custom-results-list').empty();
                    $('#custom-search-results').hide();
                }
            });
        }

        // Mostrar resultados de búsqueda
        function displaySearchResults(products) {
            const $resultsList = $('#custom-results-list');
            $resultsList.empty(); // Limpiar resultados anteriores

            if (products.length === 0) {
                $resultsList.append('<li class="text-center">No se encontraron productos.</li>');
            } else {
                products.forEach(function(product) {
                    let productHTML = `
                    <li class="d-flex align-items-center mb-2" style="width: 100%;">
                        <a href="/producto/${product.id}" class="d-flex w-100 text-decoration-none text-dark">
                            <img src="${product.imagen_principal}" class="item-img me-2" alt="Producto">
                            <div class="custom-product-info d-flex justify-content-between w-100">
                                <div>
                                    <p class="mb-0"><strong>${product.nombre}</strong></p>
                                    <p class="mb-0">Código: ${product.no_s}</p>
                                    ${product.descuento > 0 ? `<p class="mb-0 text-danger"><del>$${product.precio_unitario_IVAinc}</del> <strong>$${product.precio_final}</strong></p>` : `<p class="mb-0">Precio: $${product.precio_final}</p>`}
                                </div>
                                <button class="btn btn-primary btn-sm add-to-cart-btn" data-id="${product.id}"><i class="fas fa-cart-plus"></i></button>
                            </div>
                        </a>
                    </li>`;
                    $resultsList.append(productHTML);
                });

                $('#custom-search-results').show();
            }

            // Manejar la adición al carrito
            $('.add-to-cart-btn').on('click', function(e) {
                e.preventDefault(); // Prevenir la redirección
                const productId = $(this).data('id');
                addToCart(productId);
            });
        }

        // Función para añadir al carrito y recargar la página
        function addToCart(productId) {
            $.ajax({
                type: "POST",
                url: "/cart/add",
                data: {
                    id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    window.location.reload(); // Recargar la página para actualizar el carrito
                },
                error: function() {
                    alert("Error al añadir el producto al carrito.");
                }
            });
        }
    });
</script>
<script>
    document.getElementById('verCarrito').addEventListener('click', function (e) {
        e.preventDefault();
        grecaptcha.execute('{{ config('services.recapcha.site_key') }}', { action: 'ver_carrito' }).then(function (token) {
            document.getElementById('recaptchaToken').value = token;
            document.getElementById('verCarritoForm').submit();
        });
    });
</script>

<style>
    #custom-search {
        width: 550px !important;
    }

    #custom-search-results {
        border: 1px solid #005f7fea;
        max-width: 92% !important;
        background: #00B398;
        max-height: 400px;
        overflow-y: auto;
        z-index: 999;
        position: absolute;
        top: 33px;
        text-align: left !important;
    }

    /* Personalizar la barra de desplazamiento */
    #custom-search-results::-webkit-scrollbar {
        width: 10px;
        /* Ancho del scrollbar */
    }

    #custom-search-results::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #custom-search-results::-webkit-scrollbar-thumb {
        background-color: #007bff;
        border-radius: 10px;
    }

    #custom-search-results::-webkit-scrollbar-thumb:hover {
        background-color: #0056b3;
        /* Color al pasar el mouse */
    }


    #custom-results-list {
        max-height: 400px;

    }

    #custom-results-list::-webkit-scrollbar {
        width: 8px;
        /* Ancho de la barra de desplazamiento */
    }

    #custom-results-list::-webkit-scrollbar-thumb {
        background-color: #007bff;
        /* Color del scrollbar */
        border-radius: 10px;
        /* Bordes redondeados para el scrollbar */
    }

    #custom-results-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        /* Fondo del track del scrollbar */
    }

    #custom-results-list li {
        padding: 10px;
        border-bottom: 1px solid #005f7fea;
        text-align: left !important;
    }

    #custom-results-list li:hover {
        background-color: #009688;
        /* Cambia el color al pasar sobre los resultados */
        color: white;
        /* Asegúrate que el texto también cambie a blanco */
    }

    .item-img {
        width: 80px;
        /* Aumenta el tamaño de la miniatura */
        height: 80px;
        object-fit: cover;
        margin-right: 20px;
        /* Mayor margen entre la imagen y los detalles */
    }

    .product-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        font-size: 16px;
        /* Texto más grande para mejor legibilidad */
    }

    .custom-product-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        font-size: 16px;
        white-space: normal;
        /* Permitir el ajuste de texto en varias líneas */
        overflow: hidden;
    }

    .custom-product-info div {
        max-width: calc(100% - 120px);
        /* Ajuste para el espacio del botón */
        margin-right: 15px;
    }

    .add-to-cart-btn {
        background-color: #d61d1d;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        /* Ajusta el padding para hacerlo más pequeño */
        width: 50px;
        /* Reduce el ancho del botón */
        height: 40px;
        /* Ajusta la altura del botón */
        text-align: center;
        flex-shrink: 0;
        transition: background-color 0.3s ease;
        display: flex;
        float: left;
        align-items: center;
        justify-content: center;
    }

    .add-to-cart-btn i {
        font-size: 1.2rem;
        /* Ajusta el tamaño del icono dentro del botón */
    }

    .add-to-cart-btn:hover {
        background-color: #0056b3;
    }


    .item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-right: 20px;
    }

    .custom-results-list li {
        padding: 10px;
        border-bottom: 1px solid #005f7fea;
    }

    .custom-results-list li:hover {
        background-color: #009688;
        color: white;
    }
</style>
