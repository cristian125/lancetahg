@extends('template')
@section('header')
    <meta property="og:url" content="{{env('APP_URL')}}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ env('SITE_NAME','Lanceta HG') }}" />
    <meta property="og:description" content="{{ env('SITE_SLOGAN') }}" />
    <meta property="og:image" content="{{ asset('storage/logos/logolhg.png') }}" />
    <script>
        $(document).ready(function() {
            // Evento para agregar al carrito
            
            $('.btn-add-to-cart').on('click',function(e){
                e.preventDefault();
                e.stopPropagation();
                let productId = $(this).data('id'); // Usar el id del producto
                let no_s = $(this).data('nos'); // Mantener no_s si es necesario para otras funciones
                let clickedButton = $(this); // Guardar el botón que fue clicado
    
                // Verificar si el usuario está logueado
                if (!isUserLoggedIn()) {
                    showLoginPopover(clickedButton); // Mostrar popup de inicio de sesión
                } else {
                    addToCart(productId, no_s, clickedButton);
                }
            });
    
            
    
            function isUserLoggedIn() {
                // Aquí puedes implementar una verificación si el usuario está logueado
                // Ejemplo: verificando si hay una cookie de sesión o usando una variable PHP
                return {{ auth()->check() ? 'true' : 'false' }};
            }
    
            
            
            function addToCart(productId, no_s, clickedButton) {
                let token = $('meta[name="csrf-token"]').attr('content');
    
                // Verificar la cantidad disponible y la cantidad en el carrito
                $.ajax({
                    type: "POST",
                    url: "/cart/check-stock", // Nueva ruta para verificar el stock
                    data: {
                        id: productId,
                        no_s: no_s,
                        _token: token
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.can_add) {
                            // Si hay suficiente stock, agregar el producto al carrito
                            addProductToCart(productId, no_s, token);
                        } else {
                            // Mostrar un mensaje si no hay suficiente stock
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
                                'Ocurrió un error al añadir el producto al carrito. Intente de nuevo.');
                        }
                    }
                });
            }
    
            function updateCartCount() {
                loadCartItems(); // Actualiza el contador del carrito
            }
    
            function showLoginPopover(button) {
                // Muestra un popover si el usuario no está logueado
                button.popover({
                    content: 'Por favor inicie sesión para agregar el producto al carrito.',
                    placement: 'bottom',
                    trigger: 'focus',
                    customClass: 'popover-danger bg-danger fw-bold'
                }).popover('show');
            }
    
            function showMaxStockPopover(button) {
                // Mostrar un popover solo en el botón que fue clicado
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

@include('partials.itemspp')
@endsection