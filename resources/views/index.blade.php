@extends('template')
@section('header')
    <script>
        $(document).ready(function() {
            $('.btn-add-to-cart').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                let productId = $(this).data('id');
                let no_s = $(this).data('nos');
                let clickedButton = $(this);

                if (!isUserLoggedIn()) {
                    showLoginPopover(clickedButton);
                } else {
                    addToCart(productId, no_s, clickedButton);
                }
            });

            function isUserLoggedIn() {
                return {{ auth()->check() ? 'true' : 'false' }};
            }

            function addToCart(productId, no_s, clickedButton) {
                let token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: "POST",
                    url: "/cart/check-stock",
                    data: {
                        id: productId,
                        no_s: no_s,
                        _token: token
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.can_add) {
                            addProductToCart(productId, no_s, token);
                        } else {
                            showMaxStockPopover(clickedButton);
                        }
                    },
                    error: function() {
                        alert('Ocurrió un error al verificar el stock. Intente de nuevo.');
                    }
                });
            }

            function addProductToCart(productId, no_s, token) {
                $.ajax({
                    type: "POST",
                    url: "/cart/add",
                    data: {
                        id: productId,
                        no_s: no_s,
                        _token: token
                    },
                    dataType: "json",
                    success: function(response) {
                        updateCartCount();
                    },
                    error: function(data) {
                        if (data.status === 401) {
                            showLoginPopover(clickedButton);
                        } else {
                            alert(
                                'Ocurrió un error al añadir el producto al carrito. Intente de nuevo.'
                                );
                        }
                    }
                });
            }

            function updateCartCount() {
                loadCartItems();
            }

            function showLoginPopover(button) {
                button.popover({
                    content: 'Por favor inicie sesión para agregar el producto al carrito.',
                    placement: 'bottom',
                    trigger: 'focus',
                    customClass: 'popover-danger bg-danger fw-bold'
                }).popover('show');
            }

            function showMaxStockPopover(button) {
                button.popover({
                    content: 'No puedes añadir más productos de los que hay en stock.',
                    placement: 'bottom',
                    trigger: 'focus',
                    customClass: 'popover-warning bg-warning fw-bold'
                }).popover('show');
            }
        });
    </script>
@endsection
@section('body')
    <div id="cookieConsent" class="cookie-banner fixed-bottom p-3 d-none" style="z-index: 9999;">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="cookie-message">
                <span>Este sitio utiliza cookies para garantizar que obtenga la mejor experiencia en nuestro sitio web.
                </span>
            </div>
            <button id="acceptCookies" class="btn btn-info">Aceptar</button>
        </div>
    </div>

    <style>
        .cookie-banner {
            display: none;
            background-color: #26d2b6 !important;
            color: #005f7f;
            padding: 15px;
            font-size: 14px;
        }

        .cookie-banner .btn {
            font-weight: bold;
            background-color: #005f7f;
            border: none;
            color: white;
        }

        .cookie-banner .btn:hover {
            background-color: #005f7f;
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
                cookieBanner.classList.add('d-block');
            }

            acceptCookiesBtn.addEventListener('click', function() {
                localStorage.setItem('cookiesAccepted', 'true');
                cookieBanner.classList.remove('d-block');
                cookieBanner.classList.add('d-none');
            });
        });
    </script>
    @include('partials.itemspp')
@endsection
