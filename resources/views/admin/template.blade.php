<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

    <title>{{ env('SITE_NAME', 'Admin Panel') }}</title>

    {{-- JAVASCRIPT 3.7.1 --}}
    <script src="{{ asset('js/jquery/jquery-3.7.1.min.js') }}"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>


    {{-- BOOTSTRAP 5.3 --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-grid.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-reboot.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-utilities.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-icons.min.css') }}">

    <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap/popper.min.js') }}"></script>

    {{-- FONT AWESOME 6.6.0 --}}
    @if (env('APP_DEBUG') == true)
        {{-- <script src="{{ asset('js/fontawesome/conflict-detection.min.js') }}" ></script>     --}}
    @endif
    <script src="{{ asset('js/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/brands.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/fontawesome.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/regular.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/solid.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/v4-shims.min.js') }}"></script>

    {{-- Estilos personalizados --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

    <style>
        .nav-link[aria-expanded="true"] .fa-chevron-down {
    transform: rotate(180deg);
}

        /* Estilos personalizados para el sidebar */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #010202;
            color: #fff;
            transition: all 0.3s;
            z-index: 100;
        }

        #sidebar.collapsed {
            width: 80px;
        }

        #sidebar .nav-link {
            color: #adb5bd;
            padding: 15px;
        }

        #sidebar .nav-link.active {
            background-color: #495057;
            color: #fff;
        }

        #sidebar .nav-link i {
            margin-right: 10px;
        }

        #sidebar.collapsed .nav-link span {
            display: none;
        }

        #sidebar.collapsed .nav-link {
            text-align: center;
            padding: 15px 0;
        }

        #sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        /* Contenido principal */
        #content {
            margin-left: 250px;
            transition: all 0.3s;
        }

        #content.collapsed {
            margin-left: 80px;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 250px;
            width: calc(100% - 250px);
            transition: all 0.3s;
            z-index: 99;
        }

        .navbar.collapsed {
            left: 80px;
            width: calc(100% - 80px);
        }

        /* Para que el contenido no quede debajo del navbar */
        .content-wrapper {
            padding-top: 56px;
            /* Altura del navbar */
        }

        /* Ajustes para dispositivos móviles */
        @media (max-width: 768px) {
            #sidebar {
                left: -250px;
            }

            #sidebar.active {
                left: 0;
            }

            #sidebar.collapsed {
                left: -80px;
            }

            #content {
                margin-left: 0;
            }

            #content.collapsed {
                margin-left: 0;
            }

            .navbar {
                left: 0;
                width: 100%;
            }

            .navbar.collapsed {
                left: 0;
                width: 100%;
            }
        }
        /* Estilos personalizados para el scrollbar */
#sidebar::-webkit-scrollbar {
    width: 8px; /* Ancho del scrollbar */
}

#sidebar::-webkit-scrollbar-track {
    background: #f1f1f1; /* Color de fondo de la pista */
    border-radius: 10px;
}

#sidebar::-webkit-scrollbar-thumb {
    background-color: #007bff; /* Color del scrollbar */
    border-radius: 10px;
    border: 2px solid #f1f1f1; /* Espacio alrededor del scrollbar */
}

#sidebar::-webkit-scrollbar-thumb:hover {
    background-color: #0056b3; /* Color del scrollbar al pasar el mouse */
}

    </style>

    @yield('header')
</head>

<body>
<!-- Sidebar -->
<nav id="sidebar" class="d-flex flex-column" style="height: 100vh; overflow-y: auto;">
    <div class="p-3">
        <h4 class="mb-0 text-center">
            <i class="fas fa-user-shield"></i>
            <!-- Mantener la frase "Administrador" -->
            <span id="logo-text">Administrador</span>
        </h4>
    </div>

    <!-- Menú principal -->
    <ul class="nav flex-column" id="sidebarMenu">
        <!-- Nuevo elemento con el nombre del usuario autenticado que lleva a configuración -->
        <li class="nav-item">
            <a href="{{ route('admin.config') }}" class="nav-link">
                <i class="fas fa-user"></i>
                <span class="text-truncate">{{ Auth::guard('admin')->user()->name }}</span>
            </a>
        </li>

        <!-- Categoría: Gestión de Contenido -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#gestionContenido" role="button" aria-expanded="false" aria-controls="gestionContenido">
                <i class="fas fa-edit"></i>
                <span class="text-truncate">Gestión de Contenido</span>
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <div class="collapse" id="gestionContenido">
                <ul class="nav flex-column ms-3">
                    @if (Auth::guard('admin')->check() && in_array(Auth::guard('admin')->user()->role, ['superusuario', 'editor']))
                        <li class="nav-item">
                            <a href="{{ route('admin.items.index') }}" class="nav-link {{ request()->is('admin/items') ? 'active' : '' }}">
                                <i class="fas fa-box"></i>
                                <span class="text-truncate">Items</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.pages.list') }}" class="nav-link {{ request()->is('admin/pages') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i>
                                <span class="text-truncate">Páginas</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>

        <!-- Categoría: Administración -->
        @if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role === 'superusuario')
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" href="#administracion" role="button" aria-expanded="false" aria-controls="administracion">
                    <i class="fas fa-user-shield"></i>
                    <span class="text-truncate">Administración</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="administracion">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="{{ route('admin.manage.admins') }}" class="nav-link {{ request()->is('admin/manage-admins') ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i>
                                <span class="text-truncate">Gestión de Accesos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}">
                                <i class="fas fa-users"></i>
                                <span class="text-truncate">Gestionar Usuarios</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.shipping_methods') }}" class="nav-link {{ request()->is('admin/shipping-methods') ? 'active' : '' }}">
                                <i class="fas fa-shipping-fast"></i>
                                <span class="text-truncate">Métodos de Envío</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('newsletter.show') }}" class="nav-link">
                                <i class="fas fa-envelope"></i>
                                <span class="text-truncate">Suscriptores</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.orders.index') }}" class="nav-link">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="text-truncate">Pedidos de Compra</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.payment_logs.index') }}" class="nav-link {{ request()->is('admin/manage-admins') ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i>
                                <span class="text-truncate">Payment Logs</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Categoría: Configuración de Página -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#configuracionPagina" role="button" aria-expanded="false" aria-controls="configuracionPagina">
                <i class="fas fa-cogs"></i>
                <span class="text-truncate">Configuración de Página</span>
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <div class="collapse" id="configuracionPagina">
                <ul class="nav flex-column ms-3">
                    <li class="nav-item">
                        <a href="{{ route('footer_links.index') }}" class="nav-link">
                            <i class="fas fa-bars"></i>
                            <span class="text-truncate">Footer</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.carousel_settings') }}" class="nav-link">
                            <i class="fas fa-play-circle"></i>
                            <span class="text-truncate">Carrusel</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.grid_settings') }}" class="nav-link">
                            <i class="fas fa-th-large"></i>
                            <span class="text-truncate">Grid 2x2</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.banner_settings') }}" class="nav-link">
                            <i class="fas fa-bullhorn"></i>
                            <span class="text-truncate">Banner</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.destacados.form') }}" class="nav-link">
                            <i class="fas fa-star"></i>
                            <span class="text-truncate">Destacados</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.modal_config') }}" class="nav-link">
                            <i class="fas fa-window-maximize"></i>
                            <span class="text-truncate">Modal</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Configuración General -->
        <li class="nav-item">
            <a href="{{ route('configadmin') }}" class="nav-link">
                <i class="fas fa-tools"></i>
                <span class="text-truncate">Configuración General</span>
            </a>
        </li>
    </ul>

    <!-- Bloque inferior que contiene "Cerrar Sesión" -->
    <div class="mt-auto">
        <ul class="nav flex-column">
            <!-- Cerrar Sesión -->
            <li class="nav-item">
                <a href="{{ route('admin.logout') }}" class="nav-link"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="text-truncate">Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </div>
</nav>


<!-- Formulario oculto para el logout -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>





    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Botón para colapsar/expandir el sidebar -->
            <button class="btn btn-outline-light me-2" id="sidebarCollapse">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="#">Panel</a>
            <!-- Botón de colapso para la navegación en dispositivos móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Enlaces de navegación -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    @if (auth()->check())

                        <li class="nav-item">
                            <a href="{{ route('admin.config') }}" class="nav-link">
                                <i class="fas fa-cogs"></i>
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-user-circle"></i> Invitado</a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('admin.logout') }}" class="nav-link"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                         <i class="fas fa-sign-out-alt"></i>
                         <span class="text-truncate">Cerrar Sesión</span>
                     </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>


    <!-- Contenido Principal -->
    <div id="content">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Formulario de Cierre de Sesión -->
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts de jQuery y Bootstrap -->
    <script>
        // Funcionalidad para colapsar/expandir el sidebar y cambiar el texto del logo
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('collapsed');
            $('#content').toggleClass('collapsed');
            $('.navbar').toggleClass('collapsed');

            // Cambiar el texto del logo
            var logoText = $('#logo-text');
            if ($('#sidebar').hasClass('collapsed')) {
                logoText.text('LHG');
            } else {
                logoText.text('Administrador');
            }
        });
        $(document).ready(function() {
    $('.nav-link[data-bs-toggle="collapse"]').on('click', function() {
        var target = $(this).attr('href'); // ID del div colapsable
        $(target).collapse('toggle'); // Alternar el colapsable

        // Cerrar los otros submenús abiertos
        $('.collapse').not(target).collapse('hide');
    });
});

    </script>

    
</body>

</html>
