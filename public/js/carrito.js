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
                // Calcular el precio con IVA
                let priceWithVAT = (item.price * (1+item.vat)).toFixed(2);
                // Verificar si el producto tiene un descuento
                let discountBadge = "";
                if (item.discount > 0) {
                    discountBadge = `<span class="badge bg-danger">¡Descuento del ${item.discount}%!</span>`;
                }

                cartItemsList += `
                <a href="/producto/${item.id}" class="cart-item" style="text-decoration: none; color: inherit;">
                    <!-- Imagen del producto -->
                    <div class="item-img" style="background-image: url(/producto/img/${item.no_s});"></div>
            
                    <!-- Detalles del producto -->
                    <div class="item-details">
                        <span>${item.description}</span>
                        <small>${item.quantity} x $${priceWithVAT}</small>
                        ${discountBadge}
                    </div>
            
                    <!-- Botón de eliminar -->
                    <button class="remove-from-cart" data-nos="${item.no_s}" onclick="event.preventDefault();">
                        <i class="bi bi-trash"></i>
                    </button>
                </a>
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
