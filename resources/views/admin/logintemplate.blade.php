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

    {{-- BOOTSTRAP 5.3 --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-grid.min.css') }}" />    
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-reboot.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-utilities.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-icons.min.css') }}" >

    <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap/popper.min.js') }}"></script>

    {{-- FONT AWESOME 6.6.0 --}}
    @if (env('APP_DEBUG')==true)    
    {{-- <script src="{{ asset('js/fontawesome/conflict-detection.min.js') }}" ></script>     --}}
    @endif
    <script src="{{ asset('js/fontawesome/all.min.js') }}" ></script>
    <script src="{{ asset('js/fontawesome/brands.min.js') }}" ></script>    
    <script src="{{ asset('js/fontawesome/fontawesome.min.js') }}" ></script>
    <script src="{{ asset('js/fontawesome/regular.min.js') }}" ></script>
    <script src="{{ asset('js/fontawesome/solid.min.js') }}" ></script>
    <script src="{{ asset('js/fontawesome/v4-shims.min.js') }}" ></script>

    {{-- Estilos personalizados --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

    <style>
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
            padding-top: 56px; /* Altura del navbar */
        }


    </style>

    @yield('header')
</head>

<body>


    <!-- Contenido Principal -->


            @yield('content')



</body>

</html>
