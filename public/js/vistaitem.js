

document.addEventListener('DOMContentLoaded', function() {
    const loginDropdown = document.getElementById('login-dropdown');
    if (loginDropdown) {
        const dropdownMenu = document.querySelector('#login-dropdown .dropdown-menu');

        // Mantener el dropdown abierto mientras se interactúa con él
        loginDropdown.addEventListener('mouseover', function() {
            dropdownMenu.classList.add('show');
            loginDropdown.classList.add('show');
        });

        loginDropdown.addEventListener('mouseout', function(event) {
            if (!loginDropdown.contains(event.relatedTarget)) {
                dropdownMenu.classList.remove('show');
                loginDropdown.classList.remove('show');
            }
        });

        // Prevenir que el dropdown se cierre al interactuar con las sugerencias de autocompletado
        dropdownMenu.addEventListener('mouseover', function(event) {
            event.stopPropagation();
        });

        dropdownMenu.addEventListener('mouseover', function() {
            dropdownMenu.classList.add('show');
            loginDropdown.classList.add('show');
        });

        dropdownMenu.addEventListener('mouseout', function(event) {
            if (!loginDropdown.contains(event.relatedTarget)) {
                dropdownMenu.classList.remove('show');
                loginDropdown.classList.remove('show');
            }
        });
    }
});

$(document).ready(function () {
    // Inicializar wheelzoom en la imagen principal
    wheelzoom(document.querySelectorAll('#main-image'), { zoom: 0.2, maxZoom: 4 });

    // Función para manejar el zoom in y zoom out usando los botones
    function handleZoom(deltaZoom) {
        const img = $('#main-image')[0];
        const currentBgSize = window.getComputedStyle(img).backgroundSize.split(' ');
        const bgWidth = parseFloat(currentBgSize[0]);
        const bgHeight = parseFloat(currentBgSize[1]);

        const initialWidth = img.offsetWidth;
        const initialHeight = img.offsetHeight;

        // Limitar el decremento de zoom para no ir más allá del tamaño original
        if (deltaZoom < 0 && (bgWidth <= initialWidth + 100 || bgHeight <= initialHeight + 1)) {
            // Restablecer al tamaño original si se supera el límite
            img.dispatchEvent(new CustomEvent('wheelzoom.reset'));
        } else {
            img.dispatchEvent(new CustomEvent('wheelzoom.zoomInOut', { detail: { deltaZoom: deltaZoom }}));
        }
    }

    // Manejar el evento de clic en el botón de zoom in
    $('#zoom-in').on('click', function () {
        handleZoom(0.2); // Aumentar el zoom en un 20%
    });

    // Manejar el evento de clic en el botón de zoom out
    $('#zoom-out').on('click', function () {
        handleZoom(-0.2); // Disminuir el zoom en un 20%
    });

    // Cambiar la imagen principal al pasar el mouse sobre una miniatura
    $('.item-thumb').on('mouseover', function () {
        var newSrc = $(this).data('image');
        
        // Destruir la instancia actual de wheelzoom
        $('#main-image')[0].dispatchEvent(new CustomEvent('wheelzoom.destroy'));

        // Cambiar la imagen principal
        $('#main-image').attr('src', newSrc);
        
        // Volver a aplicar wheelzoom en la nueva imagen
        wheelzoom(document.querySelectorAll('#main-image'), { zoom: 0.2, maxZoom: 3 });
    });

    // Evitar clic derecho en las miniaturas y la imagen principal
    $('.item-thumb, #main-image-container').on('contextmenu', function(e) {
        e.preventDefault();
    });

    // Evitar clics en las miniaturas
    $('.item-thumb').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
    });

    $(document).ready(function () {
        
        function addToCart(productId) {
            let token = $('meta[name="csrf-token"]').attr('content'); 
    
            $.ajax({
                type: "POST",
                url: "/cart/add",  
                data: {
                    id: productId,
                    _token: token
                },
                dataType: "json",
                success: function (response) {
                    
                    
                    updateCartCount();
                },
                error: function (data) {
                    if (data.status === 401) {
                        showLoginPopover();  
                    } else {
                        alert('Ocurrió un error al añadir el producto al carrito. Intente de nuevo.');
                    }
                }
            });
        }
        
        // Función para manejar la eliminación de un producto del carrito
    function removeFromCart(productId) {
        let token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            type: "POST",
            url: "/cart/remove",  
            data: {
                id: productId,
                _token: token
            },
            dataType: "json",
            success: function (response) {
                alert('Producto eliminado del carrito con éxito');
                updateCartCount();
            },
            error: function (data) {
                alert('Ocurrió un error al eliminar el producto del carrito. Intente de nuevo.');
            }
        });
    }
    
        $(document).on('click', '#add-to-cart', function (e) {
            e.preventDefault();
            e.stopPropagation();  
            let productId = $(this).data('id');
            addToCart(productId);  
        });
    
        
        function showLoginPopover() {
            $('#add-to-cart').popover({
                content: 'Por favor inicie sesión para agregar el producto al carrito.',
                placement: 'bottom',
                trigger: 'focus',
                customClass: 'popover-danger bg-danger fw-bold'
            }).popover('show');
        }
    
        
        function updateCartCount() {
            $.ajax({
                type: "GET",
                url: "/get-cart-items",
                dataType: "json",
                success: function(data) {
                    let totalItems = 0;
        
                    $.each(data.items, function(index, item) {
                        totalItems += item.quantity;
                    });
        
                    // Update the cart item count in the UI
                    if (totalItems > 0) {
                        $('#cart-item-count').text(totalItems).show();
                    } else {
                        $('#cart-item-count').hide();
                    }
                },
                error: function() {
                    console.log('Error al cargar los items del carrito.');
                }
            });
        }
        
    });
    
});
