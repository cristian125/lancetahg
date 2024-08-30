$(document).ready(function () {
    $('#search').on('keyup change', function (e) {
        var searchWord = $(this).val();
        clearTimeout($(this).data('timer'));

        if (e.key == 'Enter' || e.key == 'NumpadEnter') {
            e.preventDefault();
            // Redirigir a la página de resultados con el término de búsqueda
            window.location.href = '/search-result?search=' + encodeURIComponent(searchWord);
            return;
        }

        if (searchWord.trim().length > 3) {
            $(this).data('timer', setTimeout(function () {
                search(searchWord);
            }, 1000));
        }
    });

    $('#btnsearch').on('click', function (e) {
        var searchWord = $('#search').val();
        e.preventDefault();
        // Redirigir a la página de resultados con el término de búsqueda
        window.location.href = '/search-result?search=' + encodeURIComponent(searchWord);
    });

    $('#contenedorsup').on('mouseover', function () {
        //$('#search-result').remove();
    });

    $('body').on('keyup', function (e) {
        if (e.key == 'Escape') {
            $('#search-result').remove();
            $.when($('#search-result').remove()).then(function () {
                $('#search').val('');
                $('#search').change();
            });
        }
    });
});

function search(word) {
    $.ajax({
        type: "GET",
        url: "/search",
        data: {
            "search": word,
        },
        dataType: "json",
        success: function (data) {
            $('#item-default span').html(data.length);
            $('#search-result').remove();

            $('#items-search .container li').remove();

            let div = '<div class="container">';
            $.each(data, function() {
                let codigoProducto = ('000000' + this.no_s).slice(-6); // Asegura que el código tenga 6 dígitos

                // Definir la ruta a la carpeta del producto
                let carpetaProducto = "/storage/itemsview/" + codigoProducto;
                // Definir la ruta de la imagen principal dentro de la carpeta
                let imagenPrincipal = carpetaProducto + "/" + codigoProducto + ".jpg";
                // Definir la ruta de la imagen por defecto
                let defaultImagePath = "/storage/itemsview/default.jpg";
                
                // Verificar si la imagen principal existe dentro de la carpeta
                if (!imageExists(imagenPrincipal)) {
                    imagenPrincipal = defaultImagePath;
                }

                div += '<li class="input-group" style="width:100%; margin-bottom: 10px;">';
                div += '<a href="/producto/' + this.id + '" class="d-flex align-items-center" style="text-decoration: none; color: #333; background-color: #f8f9fa; border-radius: 8px; padding: 10px; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">';
                div += '    <div class="col-md-2">';
                div += '        <div class="item-img" style="background-image: url(' + imagenPrincipal + '); background-size: cover; background-position: center; border-radius: 8px; height: 75px; width: 75px;"></div>';
                div += '    </div>';
                div += '    <div class="col-md-6">';
                div += '        <p style="margin: 0; font-size: 14px;"><strong>Código:</strong> ' + this.no_s + '<br /> <strong>Unidad:</strong> ' + this.unidad_medida_venta + '<br />' + this.descripcion + '</p>';
                div += '    </div>';
                div += '    <div class="col-md-2 text-center">';
                div += '        <p style="margin: 0; font-size: 16px; font-weight: bold; color: #005f7f;">$' + this.precio_unitario_IVAinc + '</p>';
                div += '    </div>';
                div += '    <div class="col-md-2 text-center d-flex align-items-center justify-content-center">';
                div += '        <button class="btn btn-primary btn-add-to-cart" data-id="' + this.id + '" style="background-color: #007bff; border: none; border-radius: 4px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">';
                div += '            <i class="bi bi-cart-plus" style="font-size: 18px; color: white;"></i>';
                div += '        </button>';
                div += '    </div>';
                div += '</a>';
                div += '</li>';
            });
            div += '</div>';
            $('#items-search').prepend(div);

            // Añadir el evento de clic para los resultados
            $('.item-result').on('click', function () {
                let url = $(this).attr('data-url');
                window.location.href = url;
            });

            // Añadir el evento de clic para "Agregar al Carrito"
            attachAddToCartEvent();
        },
        error: function () {
            $('.search-result').remove();
        }
    });
}

function imageExists(url) {
    let img = new Image();
    img.src = url;
    return img.complete && img.naturalWidth !== 0;
}

// Función para manejar el evento de "Agregar al Carrito"
function attachAddToCartEvent() {
    $('.btn-add-to-cart').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation(); // Evita que el clic en el botón redirija a la página del producto
        let btnclicked = this;
        let id = $(this).data('id');
        let token = $('meta[name="csrf-token"]').attr('content'); // Captura el token CSRF desde la metaetiqueta

        $.ajax({
            type: "POST",
            url: "/cart/add",
            data: {
                id: id,
                _token: token // Enviar el token CSRF con la solicitud
            },
            dataType: "json",
            success: function (response) {
                loadCartItems(); // Actualizar el carrito
            },
            error: function (data) {
                if(data.status == 401) {
                    $(btnclicked).attr('data-bs-container', 'body');
                    $(btnclicked).attr('data-bs-placement', 'bottom');
                    $(btnclicked).attr('data-bs-trigger', 'focus');
                    $(btnclicked).attr('data-bs-toggle', 'popover');
                    $(btnclicked).attr('data-bs-custom-class', 'popover-danger bg-danger fw-bold');
                    $(btnclicked).attr('data-bs-content', 'Por favor inicie sesión para agregar el producto al carrito.');
                    $('[data-bs-toggle="popover"]').popover('show');
                    $('[data-bs-toggle="popover"]').on('hidden.bs.collapse', function() {
                        $('[data-bs-toggle="popover"]').removeAttr('data-bs-container');
                        $('[data-bs-toggle="popover"]').removeAttr('data-bs-placement');
                        $('[data-bs-toggle="popover"]').removeAttr('data-bs-toggle');
                        $('[data-bs-toggle="popover"]').removeAttr('data-bs-custom-class');
                        $('[data-bs-toggle="popover"]').removeAttr('data-bs-content');
                        $('[data-bs-toggle="popover"]').popover('dispose');
                    });
                    $('#loginDropdown').mouseover();
                } else {
                    alert('Ocurrió un error al añadir el producto al carrito. Intente de nuevo.');
                }
            }
        });
    });
}
