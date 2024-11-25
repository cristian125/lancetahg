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
        color: #005f7f;
        padding: 15px;
        font-size: 14px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        if (!localStorage.getItem('cookiesAccepted')) {
            cookieBanner.classList.remove('d-none');
            cookieBanner.classList.add('d-flex');
        }

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
        <div class="collapse navbar-collapse " id="navbarSupportedContent">
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
                                    </ul>
                                </div>
                                <div class="col-md-9">
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item me-2 w-100">
                    <form action="{{ route('product.search') }}" method="GET" class="d-flex position-relative w-100"
                    role="search" id="custom-search-form" onsubmit="return validateSearch();">
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
                        $userData = DB::table('users_data')->where('user_id', Auth::id())->first();
                    @endphp

                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-fill me-2"></i>
                        @if ($userData && $userData->tratamiento !== 'NA')
                            {{ $userData->tratamiento }} {{ $userData->nombre }} {{ $userData->apellido_paterno }}
                        @elseif ($userData)
                            {{ $userData->nombre }} {{ $userData->apellido_paterno }}
                        @else
                            {{ Auth::user()->name }}
                        @endif
                    </a>
                    
                        <div class="dropdown-menu dropdown-menu-end dropi p-4 shadow-lg border-0 rounded"
                            aria-labelledby="userDropdown" style="min-width: 300px;">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf

                                <div id="account-button-container" class="row">
                                    <a id="account-btn" href="/cuenta"
                                        class="btn btn-dark btn-block d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person-circle me-2"></i> Cuenta
                                    </a>
                                </div>

                                <div id="orders-button-container" class="row">
                                    <a id="orders-btn" href="{{ route('myorders') }}"
                                        class="btn btn-secondary btn-block d-flex align-items-center justify-content-center">
                                        <i class="bi bi-box-seam me-2"></i> Mis Pedidos
                                    </a>
                                </div>

                                <div id="addresses-button-container" class="row">
                                    <a id="addresses-btn" href="{{ route('cuenta', ['section' => 'Direcciones']) }}"
                                        class="btn btn-info btn-block d-flex align-items-center justify-content-center">
                                        <i class="bi bi-geo-alt me-2"></i> Direcciones
                                    </a>
                                </div>

                                <div id="personal-data-button-container" class="row">
                                    <a id="personal-data-btn" href="{{ route('cuenta', ['section' => 'colDatos']) }}"
                                        class="btn btn-warning btn-block d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-person me-2"></i> Datos Personales
                                    </a>
                                </div>

                                <div id="help-button-container" class="row">
                                    <a id="help-btn" href="/page/ayuda"
                                        class="btn btn-light btn-outline-primary btn-block d-flex align-items-center justify-content-center ">
                                        <i class="bi bi-question-circle me-2"></i> Ayuda
                                    </a>
                                </div>

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
                    <a class="nav-link" href="#" id="navbarDropdownCart" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('storage/iconos/carrito.png') }}" alt="Carrito de Compras"
                            style="height: 30px;">
                    </a>
                    <span id="cart-item-count" class="badge bg-danger ms-2"
                        style="display: none; font-size: 12px;">0</span> <!-- Contador de items -->
                    <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="navbarDropdownCart">
                        <div id="cart-items" class="list-group">
                        </div>
                        <div class="d-grid gap-2 mt-2">
                            <button id="verCarrito"
                                class="btn btn-primary d-flex align-items-center justify-content-center">
                                <i class="bi bi-bag-check-fill me-2"></i> Ver Carrito
                            </button>
                        </div>
                        <form id="verCarritoForm" action="{{ route('cart.show') }}" method="POST"
                            style="display: none;">
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
    function validateSearch() {
        const searchInput = document.getElementById('custom-search');
        const searchValue = searchInput.value.trim();

        if (searchValue === "") {
            
            searchInput.focus(); // Coloca el cursor en el campo de búsqueda
            return false; // Cancela el envío del formulario
        }

        return true; // Permite el envío del formulario si la validación es correcta
    }
</script>

<script>
    
    $(document).ready(function() {
        loadCategorias();

        function loadCategorias() {
            $.ajax({
                type: "GET",
                url: "/get-categorias",
                dataType: "json",
                success: function(data) {
                    let categoriasList = '';
                    let subcategoriaContent = '';
                    $.each(data, function(codigoDivision, division) {
                        let safeCodigo = codigoDivision.replace(/\s+/g, '_').replace(
                            /[^a-zA-Z0-9_]/g,
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
                                    '<div class="col-md-12"><ul class="list-unstyled">';
                                colIndex++;
                            }

                            let urlCategoria =
                                `${urlBase}/${encodeURIComponent(codCategoria)}`;
                            subcategoriaContent +=
                                `<li><a href="${urlCategoria}"><strong>${categoria.nombre}</strong></a><ul class="ms-3">`;

                            $.each(categoria.subsubcategorias, function(i,
                                subsubcategoria) {
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
        $('#login-dropdown .nav-link').on('click', function() {
            // console.log($(this).children('.dropdown-menu'));
            $(this).parent().children('.dropdown-menu').toggle();
        });

        function initializeSubmenuBehavior() {
    const categoryLinks = document.querySelectorAll(".category-item");

    categoryLinks.forEach((link) => {
        let isFirstClick = true; // Controlar si es el primer clic
        const targetSubmenu = document.querySelector(link.getAttribute("data-target"));

        // Para dispositivos móviles
        link.addEventListener("click", function (e) {
            if (window.innerWidth <= 900) { // Sólo para pantallas móviles
                e.preventDefault(); // Evitar redirección predeterminada

                if (isFirstClick) {
                    // Mostrar u ocultar el submenu en el primer clic
                    if (targetSubmenu) {
                        targetSubmenu.style.display = targetSubmenu.style.display === "block" ? "none" : "block";
                    }
                    isFirstClick = false; // Cambiar el estado
                } else {
                    // Segundo clic redirige
                    window.location.href = link.getAttribute("href");
                }
            }
        });

        // Para computadoras (hover abre submenu)
        link.addEventListener("mouseenter", function () {
            if (window.innerWidth > 900 && targetSubmenu) { // Solo en escritorio
                targetSubmenu.style.display = "block";
            }
        });

        // Ocultar el submenu cuando el mouse sale
        if (targetSubmenu) {
            targetSubmenu.addEventListener("mouseleave", function () {
                if (window.innerWidth > 900) { // Solo en escritorio
                    targetSubmenu.style.display = "none";
                }
            });
        }

        // Restablecer la lógica de clic al hacer clic fuera
        document.addEventListener("click", function (event) {
            if (!link.contains(event.target) && !targetSubmenu.contains(event.target)) {
                if (targetSubmenu) {
                    targetSubmenu.style.display = "none";
                }
                isFirstClick = true; // Restablecer clic en móviles
            }
        });
    });
}

    });
</script>

<script>
    $(document).ready(function() {
        let typingTimer;
        const typingInterval = 300; // Espera de 0.3 segundos

        // Configurar búsqueda AJAX al escribir en el input
        $('#custom-search').on('keyup', function(e) {
            if (e.keyCode === 13) return; // Permitir el envío de formulario con Enter

            clearTimeout(typingTimer);
            let searchWord = $(this).val().trim();

            if (searchWord.length > 2) {
                typingTimer = setTimeout(function() {
                    executeSearch(
                        searchWord); // Enviar el valor del input al ejecutar la búsqueda
                }, typingInterval);
            } else {
                $('#custom-search-results').hide();
            }
        });

        // Ejecutar búsqueda AJAX
        function executeSearch(query) {
            $.ajax({
                type: "GET",
                url: "{{ route('ajax.search') }}",
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
    $(document).ready(function() {
        $('#verCarrito').on('click', function(e) {
            e.preventDefault();
            grecaptcha.execute('{{ config('services.recapcha.site_key') }}', {
                action: 'ver_carrito'
            }).then(function(token) {
                $('#recaptchaToken').val(token);
                $('#verCarritoForm').submit();
            });
        });
    });
</script>

<style>
.megamenu-content {
    display: none;
    align-items: flex-start !important;
}

.submenu {
    display: flex;
    flex-direction: column;
    align-items: flex-start !important;
}

.megamenu-content .row {
    align-items: flex-start !important;
}

.megamenu-content .row > .col-md-3 {
    display: flex;
    flex-direction: column;
    align-items: flex-start !important;
    justify-content: flex-start !important;
}

.submenu ul {
    align-items: flex-start;
    justify-content: flex-start;
}


    #custom-search {
        width: 400px !important;
    }

    /* Para pantallas grandes (1200px o más) */
@media (min-width: 1200px) {
    #custom-search {
        width: 420px !important; /* Ancho mayor para pantallas grandes */
    }
}

/* Para pantallas medianas (entre 900px y 1199px) */
@media (max-width: 1199px) and (min-width: 900px) {
    #custom-search {
        width: 320px !important; /* Ajustar a un ancho moderado */
    }
}

/* Para pantallas pequeñas (entre 700px y 899px) */
@media (max-width: 899px) and (min-width: 700px) {
    #custom-search {
        width: 220px !important; /* Ajustar a un ancho más pequeño */
    }
}

/* Para pantallas muy pequeñas (menos de 700px) */
@media (max-width: 699px) {
    #custom-search {
        width: 120px !important; /* Reducir aún más el ancho */
    }
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


    #custom-search-results::-webkit-scrollbar {
        width: 10px;
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

    }


    #custom-results-list {
        max-height: 400px;

    }

    #custom-results-list::-webkit-scrollbar {
        width: 8px;

    }

    #custom-results-list::-webkit-scrollbar-thumb {
        background-color: #007bff;

        border-radius: 10px;

    }

    #custom-results-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #custom-results-list li {
        padding: 10px;
        border-bottom: 1px solid #005f7fea;
        text-align: left !important;
    }

    #custom-results-list li:hover {
        background-color: #009688;
        color: white;

    }

    .item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-right: 20px;
    }

    .product-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        font-size: 16px;
    }

    .custom-product-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        font-size: 16px;
        white-space: normal;
        overflow: hidden;
    }

    .custom-product-info div {
        max-width: calc(100% - 120px);
        margin-right: 15px;
    }

    .add-to-cart-btn {
        background-color: #d61d1d;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        width: 50px;
        height: 40px;
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

<style>


    /* Estilo para el navbar */
.navbar-custom {
    background-color: #005f7f;
    position: sticky; /* Navbar fijo en la parte superior */
    top: 0;
    z-index: 9999; /* Siempre visible sobre otros elementos */
    width: 100%;
}

/* Estilo para la sección colapsable del navbar */
.collapse-navbar {
    max-height: 400px; 
    overflow-y: auto;
    overflow-x: hidden; 
}

/* Scrollbar personalizado dentro del navbar */
.collapse-navbar::-webkit-scrollbar {
    width: 8px; /* Ancho del scrollbar */
}

.collapse-navbar::-webkit-scrollbar-thumb {
    background-color: #007bff; /* Color del scrollbar */
    border-radius: 4px; /* Bordes redondeados */
}

.collapse-navbar::-webkit-scrollbar-track {
    background: #f1f1f1; /* Fondo del contenedor del scrollbar */
}

/* Deshabilitar scroll de la página cuando el navbar está desplegado */
body.navbar-open {
    overflow: hidden; /* No permitir scroll de la página */
}

.dropi{
background: #005f7fea;
}
</style>


<script>

document.addEventListener("DOMContentLoaded", function () {
    const navbarToggler = document.querySelector(".navbar-toggler");
    const navbarCollapse = document.querySelector("#navbarSupportedContent");
    const body = document.body;
    const megamenuContent = document.querySelector(".megamenu-content");

    navbarToggler.addEventListener("click", function () {
        // Usamos un pequeño delay para esperar a que Bootstrap maneje la clase 'show'
        setTimeout(() => {
            const navbarIsOpen = navbarCollapse.classList.contains("show");
            if (navbarIsOpen) {
                body.classList.add("navbar-open"); // Deshabilitar scroll de la página
            } else {
                body.classList.remove("navbar-open"); // Habilitar scroll de la página
            }
        }, 300); // Ajusta este tiempo según la animación de Bootstrap
    });

    // Cerrar el navbar si haces clic fuera del menú
    document.addEventListener("click", function (e) {
        if (!navbarCollapse.contains(e.target) && !navbarToggler.contains(e.target)) {
            body.classList.remove("navbar-open");
            navbarCollapse.classList.remove("show"); // Cierra el navbar manualmente
        }
    });

    // Evitar que el scroll en el megamenu afecte el scroll del body
    if (megamenuContent) {
        megamenuContent.addEventListener('touchmove', function (e) {
            e.stopPropagation();
        }, { passive: false });

        megamenuContent.addEventListener('wheel', function (e) {
            e.stopPropagation();
        }, { passive: false });
    }

    // Manejar el scroll dentro del megamenu
    const handleScroll = (e) => {
        const { scrollTop, scrollHeight, clientHeight } = e.target;
        if (scrollTop + clientHeight >= scrollHeight) {
            e.target.scrollTop = scrollHeight - clientHeight;
        }
    };

    if (megamenuContent) {
        megamenuContent.addEventListener('scroll', handleScroll);
    }
});


</script>