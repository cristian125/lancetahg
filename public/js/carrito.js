document.addEventListener("DOMContentLoaded", function () {
    loadCartItems();
});

function loadCartItems() {
    $.ajax({
        type: "GET",
        url: "/get-cart-items", // Endpoint que devuelve los items del carrito
        dataType: "json",
        success: function (data) {
            let cartItemsList = "";
            let totalItems = 0;

            $.each(data.items, function (index, item) {
                // Verificar si el producto tiene un descuento
                let discountBadge = "";
                if (item.discount > 0) {
                    discountBadge = `<span class="badge bg-danger">¡Descuento del ${item.discount}%!</span>`;
                }

                cartItemsList += `
                <div class="cart-item row" style="display: flex; align-items: center; border-bottom: 1px solid #ddd; padding: 10px 0; margin-bottom: 10px;">
                    <div style="flex: 1; display: flex; align-items: center;">
                        <a href="/producto/${item.id}" class="list-group-item list-group-item-action form-control" style="display: flex; align-items: center; padding: 10px; background-color: #f8f9fa; border-radius: 10px; transition: background-color 0.3s ease, box-shadow 0.3s ease; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                            <div class="item-img" data-img="/producto/img/${item.no_s}" style="min-height: 64px; width: 80px; background-image:url(/producto/img/${item.no_s}); background-position: center; background-repeat: no-repeat; background-size: cover; border-radius: 5px;"></div>
                            <span style="padding-left: 15px; font-size: 16px; color: #333; flex-grow: 1;">
                                ${item.description}
                                <br />
                                <small style="color: #999;">
                                    ${item.quantity} x $${item.price}
                                </small>
                                ${discountBadge}
                            </span>
                        </a>
                    </div>
                    <button class="btn btn-danger remove-from-cart" data-nos="${item.no_s}" style="margin-right: 15px; background-color: #e74c3c; border: none; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 18px; transition: background-color 0.3s ease;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                `;
                totalItems += item.quantity;
            });

            // Actualizar el contenido del carrito
            $("#cart-items").html(cartItemsList);

            // Asignar evento para eliminar ítems del carrito
            $(".remove-from-cart").on("click", function (e) {
                e.preventDefault();
                const no_s = $(this).data("nos");
                $.ajax({
                    type: "POST",
                    url: "/cart/remove",
                    data: {
                        no_s: no_s,
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        console.log(response);
                        loadCartItems(); // Recargar el carrito
                        
                    },
                    error: function () {
                        alert("Error al eliminar el ítem del carrito.");
                    },
                });
            });

            // Actualizar el contador de items en el icono del carrito
            if (totalItems > 0) {
                $("#cart-item-count").text(totalItems).show();
            } else {
                $("#cart-item-count").hide();
            }
        },
        error: function () {
            console.log("Error al cargar los items del carrito.");
        },
    });
}
