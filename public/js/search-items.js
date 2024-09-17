$(document).ready(function () {
    // Inicialmente ocultar el dropdown
    $('#dropdown-container').hide();

    let typingTimer;
    const typingInterval = 500; // 0.5 segundos para mayor receptividad

    setupSearchHandler();
    setupSearchButtonHandler();

    // Cerrar el dropdown cuando se haga clic fuera de él
    $(document).click(function (e) {
        if (!$(e.target).closest('#search').length && !$(e.target).closest('.dropdown-menu').length) {
            $('#dropdown-container').hide(); // Oculta el dropdown
        }
    });

    // Mostrar el dropdown y ejecutar búsqueda si hay texto cuando el input esté enfocado
    $('#search').focus(function () {
        const searchWord = $(this).val().trim();
        if (searchWord.length > 0) {
            executeSearch(searchWord);
        } else {
            $('#items-search-list').empty(); // Limpiar resultados anteriores
            $('#item-result-count').text('Se han encontrado 0 resultados.');
            $('#dropdown-container').show(); // Mostrar el dropdown
        }
    });

    function setupSearchHandler() {
        $('#search').on('keyup change', function (e) {
            e.preventDefault();
            clearTimeout(typingTimer);

            var searchWord = $(this).val();

            // Ejecutar la búsqueda cuando el usuario presione Enter
            if (e.key === 'Enter' || e.key === 'NumpadEnter') {
                  // Evitar cualquier acción predeterminada de "Enter"
                handleSearch();  // Gestionar la lógica de búsqueda
            } 
            else {
                typingTimer = setTimeout(function () {
                    if (searchWord.trim().length > 3) {
                        executeSearch(searchWord);
                        $('#dropdown-container').show();
                    } else {
                        $('#items-search-list').empty();
                        $('#item-result-count').text('Se han encontrado 0 resultados.');
                        $('#dropdown-container').show();
                    }
                }, typingInterval);
            }
        });
    }

    function setupSearchButtonHandler() {
        $('#btnsearch').on('click', function (e) {
            e.preventDefault();
            handleSearch();
        });
    }

    function handleSearch() {
        const searchWord = $('#search').val();
        if (searchWord.trim().length > 0) {
            redirectToSearchResults(searchWord);
        }
    }

    function redirectToSearchResults(searchWord) {
        window.location.href = '/search-result?search=' + encodeURIComponent(searchWord);
    }

    function executeSearch(word) {
        $.ajax({
            type: "GET",
            url: "/ajax-search",
            data: { search: word },
            dataType: "json",
            success: function (data) {
                displaySearchResults(data);
            },
            error: function () {
                $('#result-container').empty(); // Limpiar resultados anteriores
            }
        });
    }

    function displaySearchResults(data) {
        const $itemsList = $('#items-search-list');
        const $resultCount = $('#item-result-count');
    
        $itemsList.empty(); // Limpiar resultados anteriores
        $resultCount.text(`Se han encontrado ${data.length} resultados.`);
    
        if (data.length === 0) {
            $itemsList.append('<li class="text-center text-light">No se encontraron resultados.</li>');
            return;
        }
    
        data.forEach(function (item) {
            const productCode = ('000000' + item.no_s).slice(-6);
            var imageUrl = `/producto/img/${productCode}`;
            const defaultImagePath = "/storage/itemsview/default.jpg";
            const imageElement = `<div class="item-img" style="background-image: url(${imageUrl}); width: 75px; height: 75px; border-radius: 8px; background-size: cover; background-position: center;"></div>`;
    
            let newItem = `
                <a href="/producto/${item.id}" class="dropdown-item d-flex align-items-center item-template" style="text-decoration: none; color: inherit;">
                    <div class="row w-100">
                        <div class="col-md-2 d-flex align-items-center">
                            ${imageElement}
                        </div>
                        <div class="col-md-6 d-flex flex-column justify-content-center">
                            <p style="margin: 0; font-size: 14px;"><strong>Código:</strong> ${item.no_s}</p>
                            <p style="margin: 0; font-size: 14px;"><strong>Unidad:</strong> ${item.unidad_medida_venta}</p>
                            <p style="margin: 0; font-size: 14px;">${item.descripcion}</p>
                        </div>
                        <div class="col-md-2 d-flex flex-column justify-content-center align-items-center text-end">
                            <p class="product-price mb-0" style="font-size: 16px; font-weight: bold; color: #005f7f;">$${number_format(item.precio_unitario_IVAinc, 2)}</p>`;
    
            if (item.descuento > 0) {
                newItem += `
                            <p class="product-discount text-danger" style="margin: 0; font-size: 12px;"><del>$${number_format(item.precio_unitario_IVAinc, 2)}</del></p>
                            <span class="badge bg-danger">¡Oferta!</span>`;
            }
    
            newItem += `
                        </div>
                        <div class="col-md-2 d-flex align-items-center justify-content-center">
                            <button class="btn btn-primary btn-add-to-cart" data-id="${item.id}" style="background-color: #007bff; border: none; border-radius: 4px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-cart-plus" style="font-size: 18px; color: white;"></i>
                            </button>
                        </div>
                    </div>
                </a>`;
    
            $itemsList.append(newItem);
        });
    
        var scriptit =  `<script>
                            $("#items-search-list .btn-add-to-cart").on("click", function(e){ 
                                e.preventDefault(); 
                                var id = $(this).attr("data-id");
                                var token = $('meta[name="csrf-token"]').attr('content');
                                $.ajax({
                                    type: "POST",
                                    url: "/cart/add",
                                    data: {
                                        id: id,
                                        _token: token,
                                    },
                                    dataType: "json",
                                    success: function (response) {
                                        if (response.message === 'Producto añadido al carrito') {
                                            loadCartItems();
                                        }
                                    },
                                    error: function (data) {
                                        // Manejar errores aquí
                                    }
                                });
                            });
    
                            
                            $("#search").on("keydown", function(e) {
                                if (e.key === 'Enter' || e.key === 'NumpadEnter') {
                                    e.preventDefault();  // Prevenir la acción de añadir al carrito
                                    $('#search').focus(); // Mantener el foco en el campo de búsqueda
                                }
                            });
                        </script>`;
        
        $itemsList.append(scriptit);
    }
    
    
    // function attachAddToCartEvent() {
    //     // $('.btn-add-to-cart').off('click').on('click', function (e) {
        
    
    //     // Añadir un evento "keydown" para prevenir añadir al carrito con la tecla Enter
    //     /*
    //     $('.btn-add-to-cart').on('keydown', function (e) {
    //         if (e.key === 'Enter' || e.key === 'NumpadEnter') {
    //             e.preventDefault(); // Evita que se realice la acción de añadir al carrito
    //             return false; // Asegura que no se propague el evento
    //         }
    //     });
    //     */

    //     $('.btn-add-to-cart').on('click', function (e) {
    //         // Asegurarse de que el evento sea un verdadero click y no un evento simulado o indebido
    //         /*
    //         if (e.isTrigger || !e.originalEvent || (e.type === 'keydown' && (e.key === 'Enter' || e.key === 'NumpadEnter'))) {
    //             e.preventDefault(); // Evita que se realice la acción de añadir al carrito
    //             return; // Sale de la función
    //         }
    //         */
    //         e.preventDefault();
    //         e.stopPropagation();
    
    //         let btnclicked = $(this);
    //         let id = btnclicked.data('id');
    
    //         if (id === 'invalid') {
    //             return;
    //         }
    
    //         let token = $('meta[name="csrf-token"]').attr('content');
    
    //         $.ajax({
    //             type: "POST",
    //             url: "/cart/add",
    //             data: {
    //                 id: id,
    //                 _token: token
    //             },
    //             dataType: "json",
    //             success: function (response) {
    //                 if (response.message === 'Producto añadido al carrito') {
    //                     loadCartItems();
    //                 }
    //             },
    //             error: function (data) {
    //                 handleAddToCartError(data, btnclicked);
    //             }
    //         });
    //     });
    
    //     // Añadir un evento "click" preventivo en todo el documento para evitar que la búsqueda desencadene la acción de añadir
    //     /*
    //     $(document).on('click', function (e) {
    //         if (!$(e.target).closest('.btn-add-to-cart').length) {
    //             // Si el click no es sobre un botón de añadir al carrito, asegurar que no se añada nada
    //             $('.btn-add-to-cart').attr('data-id', 'invalid');
    //         }
    //     });
    //     */
    // }
    
    
});




function number_format(number, decimals) {
    return parseFloat(number).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}



/*******************/


