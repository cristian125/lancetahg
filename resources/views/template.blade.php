<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

    

    <link rel="shortcut icon" href="{{  asset('storage/img/favicon.ico') }}" type="image/x-icon">
    <title>{{ env('SITE_NAME','Lanceta HG') }}</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
    {{-- time picker --}}
    <!-- CoreUI Time Picker CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui/dist/css/coreui.min.css" rel="stylesheet">

    <!-- CoreUI Time Picker JS -->
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui/dist/js/coreui.bundle.min.js"></script>

    {{-- owlcarousel --}}
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.css') }}" >
    {{-- owl carousel --}}
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>

    {{--     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/brands.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/regular.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/solid.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/svg-with-js.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/v4-font-face.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/v4-shims.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/v5-font-face.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/brands.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/conflict-detection.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/fontawesome.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/regular.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/solid.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/v4-shims.min.js"></script>
     --}}
     
     {{-- owl carousel --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    {{-- PERSONALIZADOS --}}

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/megamenu.css') }}" rel="stylesheet">
    <link href="{{ asset('css/carrito.css') }}" rel="stylesheet">
    <link href="{{ asset('css/cartdetailview.css') }}" rel="stylesheet">
    {{-- <script src="{{ asset('js/bootstrap.esm.min.js') }}"></script> --}}
    
    <script src="{{ asset('js/vistaitem.js') }}"></script>
    <script src="{{ asset('js/search-items.js') }}"></script>
    <script src="{{ asset('js/megamenu.js') }}"></script>
    <script src="{{ asset('js/carrito.js') }}"></script>
    <script src="{{ asset('js/wheelzoom.js') }}"></script>


    {{-- <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script> --}}   
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/principal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/footer.css') }}" rel="stylesheet">
    
    <link href="{{ asset('css/vistaitem.css') }}" rel="stylesheet">
    @yield('header')
    <script>
        $(document).ready(function () {
            $('[data-bs-dismiss="modal"]').on('click',function(){
                $(this).closest('.modal').modal('hide');
            });
        });
    </script>
</head>
<body>
@isset($_GET['m'])
    @if($_GET['m']<>0)
        @include('partials.navbar')
    @endif
@else
    @include('partials.navbar')
@endisset    
@yield('body')
@isset($_GET['m'])
    @if($_GET['m']<>0)
        @include('partials.footer')
    @endif
@else
@include('partials.footer')
@endisset    
</body>
</html>

