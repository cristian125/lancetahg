@php
    use App\Http\Controllers\FooterController;

    $modalActivo = $modalActivo ?? 0;
    $modalImagen = $modalImagen ?? '';
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('SITE_NAME', 'Lanceta HG') }}</title>

    <link rel="shortcut icon" href="{{ asset('storage/img/favicon.ico') }}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- JAVASCRIPT 3.7.1 -->
    <script src="{{ asset('js/jquery/jquery-3.7.1.min.js') }}"></script>

    <!-- BOOTSTRAP 5.3 -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-grid.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-reboot.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-utilities.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-icons.min.css') }}">


    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>

    <!-- FONT AWESOME 6.6.0 -->
    <script src="{{ asset('js/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/brands.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/fontawesome.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/regular.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/solid.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/v4-shims.min.js') }}"></script>

    <!-- CoreUI Time Picker CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui/dist/css/coreui.min.css" rel="stylesheet">

    <!-- CoreUI Time Picker JS -->
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui/dist/js/coreui.bundle.min.js"></script>

    <!-- Owl Carousel -->
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.css') }}">
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>

    <!-- Personalizados -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/megamenu.css') }}" rel="stylesheet">
    <link href="{{ asset('css/carrito.css') }}" rel="stylesheet">
    <link href="{{ asset('css/cartdetailview.css') }}" rel="stylesheet">

    <script src="{{ asset('js/vistaitem.js') }}"></script>
    <script src="{{ asset('js/search-items.js') }}"></script>
    <script src="{{ asset('js/megamenu.js') }}"></script>
    <script src="{{ asset('js/carrito.js') }}"></script>
    <script src="{{ asset('js/wheelzoom.js') }}"></script>

    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/principal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/footer.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vistaitem.css') }}" rel="stylesheet">
    <!-- Include the jQuery Cookie plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recapcha.site_key') }}"></script>

    @yield('header')

    <!-- Tu script personalizado -->
    <script>
        $(document).ready(function() {
            var modalClosed = $.cookie('modalClosed');

            if (!modalClosed) {
                @if ($modalActivo && $modalImagen)
                    $('#modalAviso').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#modalAviso').modal('show');
                @endif
            }

            // Al cerrar el modal, crear la cookie de sesión
            $('#modalAviso').on('hidden.bs.modal', function() {
                $.cookie('modalClosed', 'true', {
                    path: '/'
                }); // Cookie de sesión
                // console.log('Cookie de sesión creada: ' + $.cookie('modalClosed'));
            });

            // Crear la cookie al hacer clic en el botón de cerrar
            $('[data-bs-dismiss="modal"]').on('click', function() {
                $(this).closest('.modal').modal('hide');
                $.cookie('modalClosed', 'true', {
                    path: '/'
                }); // Cookie de sesión
                // console.log('Cookie de sesión creada al hacer clic en cerrar: ' + $.cookie('modalClosed'));
            });
        });
    </script>

    <!-- Estilos personalizados -->
    <style>
        .modal .close:hover {
            background-color: #ff0000;
            color: #fff;
            transform: scale(1.2);
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.8);
        }
    </style>
</head>

<body>

    @isset($_GET['m'])
        @if ($_GET['m'] != 0)
            @include('partials.navbar')
        @endif
    @else
        @include('partials.navbar')
    @endisset

    @if ($modalActivo && $modalImagen)
        <!-- Modal de aviso si está activo -->
        <div id="modalAviso" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="margin-top: 10vh;">
                <div class="modal-content p-0" style="border: none; background: transparent;">
                    <button type="button" class="btn btn-danger close" data-bs-dismiss="modal" aria-label="Close"
                        style="margin-right: 1.5em">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <img src="{{ asset('storage/' . $modalImagen) }}" alt="Aviso Modal"
                        style="width: 100%; display: block; border-radius: 10px;">
                </div>
            </div>
        </div>
    @endif

    @yield('body')

    @isset($_GET['m'])
        @if ($_GET['m'] != 0)
            @include('partials.footer')
        @endif
    @else
        {!! FooterController::render() !!}
    @endisset

</body>

</html>
