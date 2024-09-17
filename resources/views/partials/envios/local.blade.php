@if(!empty($localShippingData['direcciones']))
    <div class="container my-5">
        <div class="row">
            <!-- Columna izquierda: Seleccione su dirección -->
            <div class="col-md-6">
                <div id="direccion-selector-container" class="mb-4 p-4 bg-white rounded shadow-sm border">
                    <h5 class="mb-4 text-primary"><i class="bi bi-geo-alt-fill me-2"></i>Seleccione su dirección</h5>
                    <div class="form-group">
                        @foreach($localShippingData['direcciones'] as $direccion)
                            <div class="form-check mb-3 {{ $direccion->esLocal ? '' : 'text-muted opacity-50 not-selectable' }}">
                                <input 
                                    class="form-check-input me-2 direccion-radio" 
                                    type="radio" 
                                    name="direccionEnvio"
                                    value="{{ $direccion->id }}"    
                                    data-codigopostal="{{ $direccion->codigo_postal }}" 
                                    data-costoenvio="{{ $direccion->costoEnvio }}" 
                                    @unless($direccion->esLocal)
                                    disabled="disabled"
                                    @endunless
                                >
                                <label class="form-check-label">
                                    <strong>{{ $direccion->nombre }}</strong><br>
                                    <span class="text-muted">
                                        {{ $direccion->calle }} {{ $direccion->no_ext }} {{ $direccion->no_int ? 'Int. ' . $direccion->no_int : '' }}, 
                                        {{ $direccion->colonia }}, {{ $direccion->municipio }}, {{ $direccion->codigo_postal }}, {{ $direccion->pais }}
                                    </span>                                    
                                    @unless($direccion->esLocal)
                                        <div class="text-danger mt-2">
                                            <i class="bi bi-x-circle-fill me-2"></i>
                                            <small>Dirección no disponible para envío local</small>
                                        </div>
                                    @endunless
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p id="mensaje-direccion-disponible" class="mt-3 text-success">
                        <i class="bi bi-check-circle-fill me-2"></i>Seleccione una dirección disponible para el envío local.
                    </p>
                </div>
            </div>

            <!-- Columna derecha: Información de Envío -->
            <div id="shipping-info" class="col-md-6">
                <div id="shipping-info-block" class="mb-4 p-4 bg-white rounded shadow-sm border">
                    <h5 class="mb-4 text-primary"><i class="bi bi-info-circle-fill me-2"></i>Información de Envío</h5>
                    <p id="shipping-info-text" class="mb-3 text-muted">
                        Por favor seleccione una dirección.
                    </p>
                    <p class="fw-bold"><strong>Total del carrito:</strong> ${{ number_format($localShippingData['totalCart'], 2) }} MXN</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div id="direccion-selector-container" class="mb-4 p-4 bg-white rounded shadow-sm border text-center">
        <h5 class="mb-3 text-danger"><i class="bi bi-geo-alt-fill me-2"></i>No hay direcciones disponibles para envío local</h5>
        <p class="text-muted">Por favor, seleccione otro método de envío.</p>
    </div>
@endif


<script>
$(document).ready(function () {
    $('#direccion-selector-container input[name="direccionEnvio"]').on('change', function() {
        var direccionId = $(this).val();
        var direccionSeleccionada = $(this).parent().find('strong').html();
        var calleYColonia = $(this).parent().find('span').html().trim();
        var totalAmount = "{{ $localShippingData['totalCart'] }}";
        var cartId = "{{ $cartId }}"; // Verifica que esta variable se esté pasando correctamente a la vista desde el controlador

        $.ajax({
            type: "POST",
            url: "{{ route('cart.actualizarEnvio') }}",
            data: {
                id: '{{ auth()->id() }}',
                'direccion': direccionId,
                '_token': "{{ csrf_token() }}",
            },
            dataType: "json",
            success: function (data) {
                var result = '';

                if (data.costoEnvio > 0) {
                    result = 'El costo de envío es: $' + data.costoEnvio.toFixed(2) + ' MXN debido a que no cumples con el mínimo de compra. <a href="/envio">Más información acerca de esto.</a>';
                } else {
                    result = 'El envío es gratuito ya que cumples con el mínimo de compra. <a href="/envio">Más información acerca de esto.</a>';
                }


                $('#shipping-info-text').html(result);
                $('#shipping-info-text strong').html('<strong>Total del carrito:</strong> $' + data.totalCart.toFixed(2) + ' MXN');
                
                $('#SelectMethod').remove();
                let div = '';
                div += '<div id="SelectMethod" class="mb-4 p-3 bg-light rounded shadow-sm">';
                div += '<button id="addShippmentMethod" class="btn btn-primary form-control"><i class="fa fa-cart-arrow-down"></i> Seleccionar</button>';
                div += '</div>';
                $('#shipping-info').append(div);

                $('#addShippmentMethod').on('click', function(){
                    $.ajax({
                        type: "POST",
                        url: "{{ route('cart.updateMethod') }}",
                        data: {
                            cart_id: cartId,
                            metodo: 'EnvioLocal',
                            direccion: direccionId,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                $('#general-shipping-block').remove();
                                location.reload();
                            } else {
                                alert('Error al guardar el método de envío: ' + response.error);
                            }
                        },
                        error: function (error) {
                            alert('Error en la solicitud AJAX.');
                        }
                    });
                });
            },
            error: function(error){
                $('#SelectMethod').remove();
            }
        });
    });
});


/*

document.addEventListener('DOMContentLoaded', function() {
    const direccionRadios = document.querySelectorAll('.direccion-radio');
    const mensajeDireccion = document.querySelector('#mensaje-direccion-disponible');
    const shippingInfoBlock = document.querySelector('#shipping-info-block');
    const totalCartElement = shippingInfoBlock.querySelector('#shipping-info-text');
    const totalCartPrice = shippingInfoBlock.querySelector('strong');


    

    direccionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const direccionId = this.value;
            const direccionSeleccionada = this.closest('label').querySelector('strong').textContent;
            const calleYColonia = this.closest('label').querySelector('div').childNodes[2].textContent.trim();

            // Actualiza el mensaje de dirección seleccionada
            mensajeDireccion.innerHTML = `<i class="bi bi-check-circle-fill"></i> Dirección seleccionada: ${direccionSeleccionada} ${calleYColonia}.`;
            mensajeDireccion.classList.remove('text-danger');
            mensajeDireccion.classList.add('text-success');

            // Hacer una solicitud AJAX para obtener el nuevo costo de envío
            fetch(`/actualizar-envio`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    direccion_id: direccionId,
                    total_cart: totalCartElement.innerText
                })
            })
            .then(response => response.json())
            .then(data => {
                
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
*/

</script>

<style>
.not-selectable .direccion-radio {
    pointer-events: none; /* Desactiva la posibilidad de hacer clic */
    cursor: not-allowed; /* Cambia el cursor a una señal de no permitido */
}

.not-selectable {
    opacity: 0.6; /* Reduce la opacidad para indicar que está deshabilitado */
}
.unique-cart-titleC {
font-size: 4px!important;

}
</style>
