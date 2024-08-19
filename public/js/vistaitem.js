document.addEventListener('DOMContentLoaded', function () {
    const selectElements = document.querySelectorAll('.variante-select');
    const mainImage = document.getElementById('main-image');
    const mainImageLink = document.getElementById('main-image-link');
    const priceElement = document.getElementById('precio');
    const thumbnailLinks = document.querySelectorAll('.item-thumb');
    const baseImage = mainImage.src; // Mantener la imagen base al iniciar
    const basePrice = parseFloat(priceElement.textContent); // Mantener el precio base al iniciar

    // Función para actualizar la vista del producto
    function updateProductView() {
        let selectedPrice = basePrice;

        // Iterar a través de los selectores de variantes
        selectElements.forEach(select => {
            const selectedOption = select.options[select.selectedIndex];
            const optionPrice = parseFloat(selectedOption.getAttribute('data-precio'));

            if (!isNaN(optionPrice)) {
                selectedPrice = optionPrice; // Actualizar el precio solo si es válido
            }
        });

        // Actualizar el precio
        priceElement.textContent = selectedPrice + ' MXN';
    }

    // Función para actualizar detalles del producto basados en la imagen seleccionada
    function updateDetailsForImage(selectedImage) {
        let found = false;

        selectElements.forEach(select => {
            Array.from(select.options).forEach(option => {
                if (option.getAttribute('data-imagen') === selectedImage) {
                    select.value = option.value;

                    const optionPrice = parseFloat(option.getAttribute('data-precio'));
                    if (!isNaN(optionPrice)) {
                        priceElement.textContent = optionPrice + ' MXN';
                        found = true;
                    }
                }
            });
        });

        // Actualizar la vista si se encontró una coincidencia
        if (found) {
            updateProductView();
        }
    }

    // Actualizar vista cuando se selecciona una miniatura
    thumbnailLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();  // Evitar que se abra el enlace
            const selectedImage = link.getAttribute('href');
            mainImage.src = selectedImage;
            mainImageLink.href = selectedImage;

            // Actualizar el precio y otros detalles según la imagen seleccionada
            updateDetailsForImage(selectedImage);
        });
    });

    // Actualizar vista cuando se cambia una variante
    selectElements.forEach(select => {
        select.addEventListener('change', function () {
            updateProductView();
        });
    });

    // Inicializar la vista con la primera opción seleccionada
    updateProductView();
});
