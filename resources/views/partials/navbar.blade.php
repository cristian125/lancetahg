<nav class="navbar navbar-expand-lg navbar-custom navbar-dark">
    <div class="container justify-content-center">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('storage/logos/logolhg.png') }}" alt="Logo" style="height: 30px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item dropdown megamenu" id="categoryMenuItem">
                    <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdownMenuLink" role="button" aria-expanded="false">
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
                        <div class="dropdown ">
                            <input id="search" class="form-control me-2 dropdown-toggle" type="search" placeholder="¿Qué producto está buscando?" aria-label="Search" data-bs-toggle="dropdown" aria-expanded="true";>
                            <ul id="items-search" class="dropdown-menu dropdown-menu-dark">
                                <li id="item-default">Se han encontrado <span>0</span> resultados.</li>
                            </ul>
                        </div>
                        <button id="btnsearch" class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </li>
                @if(Auth::check()==true)
                    <!-- Mostrar el nombre del usuario si está autenticado -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill"></i> {{ Auth::user()->name }}                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="userDropdown" style="min-width: 300px;">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <a href="/cuenta" class="btn btn-primary form-control" ><i class="bi bi-person-circle"></i> Cuenta</a>
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
                        <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="loginDropdown" >
                            <form action="{{ route('login') }}" method="POST" id="login-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="correo@gmail.com" autocomplete="off" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                                <div class="mt-3">
                                    <ul class="list-group">
                                        <li class="list-group-item"><a href="#">¿Olvidó su contraseña?</a></li>
                                        <li class="list-group-item"><a href="{{ route('register') }}">Crear Cuenta</a></li>
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
                            <a href="{{ url('/carrito') }}" class="btn btn-primary">Ver Carrito</a>
                            <a href="{{ url('/checkout') }}" class="btn btn-success">Checkout</a>
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
                
                categoriasList += `<li><a class="dropdown-item category-item" href="#" data-url="${urlBase}" data-target="#division-${safeCodigo}">${division.nombre}</a></li>`;
                subcategoriaContent += `<div id="division-${safeCodigo}" class="row submenu" style="display: none;">`;
                subcategoriaContent += `<h5>${division.nombre}</h5>`;

                subcategoriaContent += '<div class="row">';
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
                        subcategoriaContent += '<div class="col-md-3"><ul class="list-unstyled">';
                        colIndex++;
                    }
                    subcategoriaContent += `<li><strong>${categoria.nombre}</strong><ul class="ms-3">`;

                    $.each(categoria.subsubcategorias, function(i, subsubcategoria) {
                        let urlProducto = `${urlBase}/${encodeURIComponent(codCategoria)}/${encodeURIComponent(subsubcategoria.codigo)}`;
                        subcategoriaContent += `<li><a href="${urlProducto}">${subsubcategoria.texto}</a></li>`;
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
            $('#categoria-menu').html('<li><a class="dropdown-item">Error al cargar categorías</a></li>');
            $('.col-md-9').html('<div class="row submenu"><div class="col-md-12"><p>No se pudieron cargar las subcategorías.</p></div></div>');
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
            document.querySelector(target).style.display = 'block'; // Mostrar la subcategoría correspondiente
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
        const dropdownMenu = loginDropdown.querySelector('.dropdown-menu');
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');

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
        dropdownMenu.addEventListener('mouseover', function(event) {
            showDropdown();
        });

        dropdownMenu.addEventListener('mouseout', function(event) {
            if (!dropdownMenu.contains(event.relatedTarget)) {
                dropdownMenu.classList.remove('show');
                loginDropdown.classList.remove('show');
            }
        });

        // Evitar que el dropdown se cierre cuando se interactúa con las sugerencias
        emailField.addEventListener('input', function() {
            showDropdown();
        });
    });
</script>

<script>

document.addEventListener('DOMContentLoaded', function() {
    loadCategoriasMobile();
});

function loadCategoriasMobile() {
    $.ajax({
        type: "GET",
        url: "/get-categorias",
        dataType: "json",
        success: function(data) {
            let categoriasList = '';

            $.each(data, function(codigoDivision, division) {
                let safeCodigo = codigoDivision.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
                let urlBase = `/categorias/${encodeURIComponent(codigoDivision)}`;

                categoriasList += `<li><a class="dropdown-item category-item-mobile" href="${urlBase}" data-target="#division-mobile-${safeCodigo}">${division.nombre}</a></li>`;
            });

            $('#mobile-categoria-menu').html(categoriasList);
            initializeMobileMenuBehavior();
        },
        error: function() {
            console.log('Error al cargar las categorías y subcategorías.');
            $('#mobile-categoria-menu').html('<li><a class="dropdown-item">Error al cargar categorías</a></li>');
        }
    });
}

function initializeMobileMenuBehavior() {
    document.querySelectorAll('.category-item-mobile').forEach(function(categoryItem) {
        categoryItem.addEventListener('click', function() {
            // Aquí puedes manejar el comportamiento de subcategorías en móviles si es necesario
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const categoryMenuItem = document.getElementById('categoryMenuItem');
    const mobileMenuToggle = document.getElementById('navbarDropdownMenuLink');
    
    categoryMenuItem.addEventListener('click', function(event) {
        if (window.innerWidth < 992) { // Detecta si la pantalla es menor a 992px
            //event.preventDefault(); // Evita el comportamiento predeterminado
            // Abre el nuevo menú para pantallas pequeñas
            mobileMenuToggle.setAttribute('data-bs-target', '#mobileNavbar'); 
            mobileMenuToggle.setAttribute('data-bs-toggle', 'collapse'); 
        } else {
            // Abre el megamenú en pantallas grandes
            mobileMenuToggle.setAttribute('data-bs-target', ''); 
            mobileMenuToggle.setAttribute('data-bs-toggle', 'dropdown');
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            // Resetea el comportamiento del menú para pantallas grandes
            mobileMenuToggle.setAttribute('data-bs-target', ''); 
            mobileMenuToggle.setAttribute('data-bs-toggle', 'dropdown');
        }
    });
});


</script>