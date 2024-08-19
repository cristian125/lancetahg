<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('SITE_NAME') }}</title>
    {{-- <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script> --}}
    <!--bootstrap 5.3-->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}" /> --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.rtl.min.css') }}" /> --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-reboot.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap-utilities.min.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('css/bootstrap-utilities.rtl.min.css') }}" /> --}}
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    {{-- <script src="{{ asset('js/bootstrap.esm.min.js') }}"></script> --}}
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/vistaitem.js') }}"></script>
    {{-- <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script> --}}   
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/principal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/footer.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vistaitem.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">




    @yield('header')
    <style>
        @media (width <= 900px) 
         {
             .navbar-toggler{
                 position: absolute;
                 float: right;
                 right: 15px;
                 top:15px;
                 transition: 2000ms;
                 transition-duration: 2000ms;
                 z-index: 1000;
             }
         }
     </style>
</head>
<body>
@include('partials.navbar')
@yield('body')



@include('partials.footer')
</body>
</html>

