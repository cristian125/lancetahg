@php
    use App\Http\Controllers\FooterController;

    $modalActivo = $modalActivo ?? 0;
    $modalImagen = $modalImagen ?? '';
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <title>{{ env('SITE_NAME', 'Lanceta HG') }}</title>
    <link rel="shortcut icon" href="{{ asset('storage/img/favicon.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/jquery/jquery-3.7.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-grid.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-reboot.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-utilities.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-icons.min.css') }}">
    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/brands.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/fontawesome.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/regular.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/solid.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome/v4-shims.min.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui/dist/css/coreui.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui/dist/js/coreui.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.css') }}">
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recapcha.site_key') }}"></script>
    @yield('header')
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

            $('#modalAviso').on('hidden.bs.modal', function() {
                $.cookie('modalClosed', 'true', {
                    path: '/'
                });
            });

            $('[data-bs-dismiss="modal"]').on('click', function() {
                $(this).closest('.modal').modal('hide');
                $.cookie('modalClosed', 'true', {
                    path: '/'
                });
            });
        });
    </script>

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
    @php
        use App\Http\Controllers\ProductosDestacadosController;
        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == true) {
            return redirect(route('mantenimento'));
        }
    @endphp
    @if (request('m') !== '0')
        @include('partials.navbar')
    @endif
    @if ($modalActivo && $modalImagen)
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
<script>

document.addEventListener('selectstart', event => event.preventDefault());
document.addEventListener('dragstart', event => event.preventDefault());
document.addEventListener('contextmenu', event => event.preventDefault());
document.addEventListener('keydown', event => {
  if (
    event.key === 'F12' || 
    (event.ctrlKey && event.shiftKey && event.key === 'I') || 
    (event.ctrlKey && event.key === 'C') || 
    (event.ctrlKey && event.key === 'U') || 
    (event.ctrlKey && event.key === 'S')
  ) {
    event.preventDefault();
  }
});



</script>

</html>
