<!--
<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head> -->

<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-custom navbar-dark">
    <div class="container justify-content-center">
        <a class="navbar-brand" href="{{ url('/') }}">
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
                <li class="nav-item me-2">
                    <form class="d-flex" role="search">
                        <div class="dropdown">
                            <input id="search" class="form-control me-2 dropdown-toggle" type="search"
                                placeholder="¿Qué producto está buscando?" aria-label="Search" data-bs-toggle="dropdown"
                                aria-expanded="true">
                            <div id="dropdown-container" class="dropdown-menu dropdown-menu-dark">
                                <ul id="items-search-list" class="list-unstyled">
                                    <!-- Aquí se mostrarán los resultados generados por JavaScript -->
                                </ul>
                                <div id="item-result-count" class="text-center text-light bg-primary">
                                    <!-- Aquí se mostrará el mensaje de conteo de resultados -->
                                </div>
                            </div>
                        </div>
                        <button id="btnsearch" class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </li>
                
                
                
                
                
                
                
                
                
                
                
                @if (Auth::check() == true)
                    <!-- Mostrar el nombre del usuario si está autenticado -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill"></i> {{ Auth::user()->name }} </a>
                        <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="userDropdown"
                            style="min-width: 300px;">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <a href="/cuenta" class="btn btn-primary form-control"><i
                                            class="bi bi-person-circle"></i> Cuenta</a>
                                </div>
                                <div class="row">
                                    <button type="submit" class="btn btn-primary w-100">Cerrar Sesión</button>
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
                                        <li class="list-group-item"><a href="#">¿Olvidó su contraseña?</a></li>
                                        <li class="list-group-item"><a href="{{ route('register') }}">Crear Cuenta</a>
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
                            <a href="{{ url('/carrito') }}"
                                class="btn btn-primary d-flex align-items-center justify-content-center">
                                <i class="bi bi-bag-check-fill me-2"></i> Ver Carrito
                            </a>
                        </div>


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
                    let safeCodigo = codigoDivision.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
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
                    document.querySelector(target).style.display = 'block'; // Mostrar la subcategoría correspondiente
                }
            });
        });

        document.querySelectorAll('.megamenu-content').forEach(function(menu) {
            menu.addEventListener('mouseleave', function() {
                document.querySelectorAll('.submenu').forEach(function(submenu) {
                    submenu.style.display = 'none'; // Ocultar todas las subcategorías cuando se sale del megamenú
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
