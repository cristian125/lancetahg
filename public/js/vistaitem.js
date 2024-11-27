$(document).ready(function () {
    // Inicializar wheelzoom en la imagen principal
    wheelzoom(document.querySelectorAll("#main-image"), {
        zoom: 0.2,
        maxZoom: 4,
    });

    // Función para manejar el zoom in y zoom out usando los botones
    function handleZoom(deltaZoom) {
        const img = $("#main-image")[0];
        const currentBgSize = window
            .getComputedStyle(img)
            .backgroundSize.split(" ");
        const bgWidth = parseFloat(currentBgSize[0]);
        const bgHeight = parseFloat(currentBgSize[1]);

        const initialWidth = img.offsetWidth;
        const initialHeight = img.offsetHeight;

        // Limitar el decremento de zoom para no ir más allá del tamaño original
        if (
            deltaZoom < 0 &&
            (bgWidth <= initialWidth + 100 || bgHeight <= initialHeight + 1)
        ) {
            // Restablecer al tamaño original si se supera el límite
            img.dispatchEvent(new CustomEvent("wheelzoom.reset"));
        } else {
            img.dispatchEvent(
                new CustomEvent("wheelzoom.zoomInOut", {
                    detail: { deltaZoom: deltaZoom },
                })
            );
        }
    }

    // Manejar el evento de clic en el botón de zoom in
    $("#zoom-in").on("click", function () {
        handleZoom(0.2); // Aumentar el zoom en un 20%
    });

    // Manejar el evento de clic en el botón de zoom out
    $("#zoom-out").on("click", function () {
        handleZoom(-0.2); // Disminuir el zoom en un 20%
    });

    // Cambiar la imagen principal al pasar el mouse sobre una miniatura
    $(".item-thumb").on("mouseover", function () {
        var newSrc = $(this).data("image");

        // Destruir la instancia actual de wheelzoom
        $("#main-image")[0].dispatchEvent(new CustomEvent("wheelzoom.destroy"));

        // Cambiar la imagen principal
        $("#main-image").attr("src", newSrc);

        // Volver a aplicar wheelzoom en la nueva imagen
        wheelzoom(document.querySelectorAll("#main-image"), {
            zoom: 0.2,
            maxZoom: 3,
        });
    });

    // Evitar clic derecho en las miniaturas y la imagen principal
    $(".item-thumb, #main-image-container").on("contextmenu", function (e) {
        e.preventDefault();
    });

    // Evitar clics en las miniaturas
    $(".item-thumb").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
    });

    // function addToCart(productId, no_s, quantity, currentStock) {
    //     let token = $('meta[name="csrf-token"]').attr("content");

    //     $.ajax({
    //         type: "POST",
    //         url: "/cart/add-multiple",
    //         data: {
    //             no_s: no_s,
    //             quantity: quantity,
    //             _token: token,
    //         },
    //         dataType: "json",
    //         success: function (response) {
    //             updateCartCount();
    //             loadCartItems();
                
    //             if (response.stock_restante !== undefined) {
    //                 $(".stock-info")
    //                     .text(`${response.stock_restante} en stock`)
    //                     .data("stock", response.stock_restante);
    //                 if (response.stock_restante <= 0) {
    //                     $("#add-to-cart").prop("disabled", true);
    //                     $(".stock-info")
    //                         .text("No hay stock disponible")
    //                         .removeClass("text-success")
    //                         .addClass("text-danger");
    //                 }
    //             }
                
    //         },
    //         error: function (data) {
    //             if (data.status === 401) {
    //                 showLoginPopover();
    //             } else {
    //                 showMaxStockPopover();
    //             }
    //         },
    //     });
    // }

    // $("#add-to-cart").on("click", function (e) {
    //     e.preventDefault();
    //     e.stopPropagation();
    //     let productId = $(this).data("id");
    //     let no_s = $(this).data("nos");
    //     let quantity = parseInt($("#qty").val());
    //     let currentStock = parseInt($(".stock-info").data("stock"));

    //     addToCart(productId, no_s, quantity, currentStock);
    // });

    // $("#show-cart").on("click", function (e) {
    //     e.preventDefault();
    //     $(location).prop("href", "/carrito");
    // });

    // function showLoginPopover() {
    //     $("#add-to-cart")
    //         .popover({
    //             content:
    //                 "Por favor inicie sesión para agregar el producto al carrito.",
    //             placement: "bottom",
    //             trigger: "focus",
    //             customClass: "popover-danger bg-danger fw-bold",
    //         })
    //         .popover("show");
    // }

    // function showMaxStockPopover() {
    //     $("#add-to-cart")
    //         .popover({
    //             content:
    //                 "No puedes añadir más productos de los que hay en stock.",
    //             placement: "bottom",
    //             trigger: "focus",
    //             customClass: "popover-warning bg-warning fw-bold",
    //         })
    //         .popover("show");
    // }

    // function updateCartCount() {
    //     $.ajax({
    //         type: "GET",
    //         url: "/get-cart-items",
    //         dataType: "json",
    //         success: function (data) {
    //             let totalItems = 0;

    //             $.each(data.items, function (index, item) {
    //                 totalItems += item.quantity;
    //             });

    //             if (totalItems > 0) {
    //                 $("#cart-item-count").text(totalItems).show();
    //             } else {
    //                 $("#cart-item-count").hide();
    //             }
                
    //         },
    //         error: function () {
    //             console.log("Error al cargar los items del carrito.");
    //         },
    //     });
    // }
});

// document.addEventListener("DOMContentLoaded", function () {
//     const qtyInput = document.getElementById("qty");
//     const addBtn = document.getElementById("btnaddqty");
//     const removeBtn = document.getElementById("btnremoveqty");
//     var maxQty = 0;

//     if (qtyInput !== null) {
//         maxQty = parseInt(qtyInput.getAttribute("max"));
//     }

//     if (addBtn !== null) {
//         addBtn.addEventListener("click", function () {
//             let currentQty = parseInt(qtyInput.value);
//             if (currentQty < maxQty) {
//                 qtyInput.value = currentQty + 1;
//             }
//         });
//     }
//     if (addBtn !== null) {
//         removeBtn.addEventListener("click", function () {
//             let currentQty = parseInt(qtyInput.value);
//             if (currentQty > 1) {
//                 qtyInput.value = currentQty - 1;
//             }
//         });
//     }
// });
