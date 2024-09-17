@if(!empty($paqueteriaShippingData['direcciones']))
    <div class="container my-5">
        <div class="row">
            <!-- Columna izquierda: Seleccione su dirección -->
            <div class="col-md-6">
                <div id="direccion-selector-container-paqueteria" class="mb-4 p-4 bg-white rounded shadow-sm border">
                    <h5 class="mb-4 text-primary"><i class="bi bi-geo-alt-fill me-2"></i>Seleccione su dirección</h5>
                    <div class="form-group">
                        @foreach($paqueteriaShippingData['direcciones'] as $direccion)
                            <div class="form-check mb-3">
                                <input 
                                    class="form-check-input me-2 direccion-radio-paqueteria" 
                                    type="radio" 
                                    name="direccionEnvioPaqueteria"
                                    value="{{ $direccion->id }}"    
                                    data-codigopostal="{{ $direccion->codigo_postal }}" 
                                    data-costoenvio="{{ $direccion->costoEnvio }}">
                                <label class="form-check-label">
                                    <strong>{{ $direccion->nombre }}</strong><br>
                                    <span class="text-muted">
                                        {{ $direccion->calle }} {{ $direccion->no_ext }} {{ $direccion->no_int ? 'Int. ' . $direccion->no_int : '' }}, 
                                        {{ $direccion->colonia }}, {{ $direccion->municipio }}, {{ $direccion->codigo_postal }}, {{ $direccion->pais }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p id="mensaje-direccion-disponible-paqueteria" class="mt-3 text-success">
                        <i class="bi bi-check-circle-fill me-2"></i>Seleccione una dirección disponible para el envío por paquetería.
                    </p>
                </div>
            </div>

            <!-- Columna derecha: Información de Envío -->
            <div id="shipping-info-paqueteria" class="col-md-6">
                <div id="shipping-info-block-paqueteria" class="mb-4 p-4 bg-white rounded shadow-sm border">
                    <h5 class="mb-4 text-primary"><i class="bi bi-info-circle-fill me-2"></i>Información de Envío</h5>
                    <p id="shipping-info-text-paqueteria" class="mb-3 text-muted">
                        Por favor seleccione una dirección.
                    </p>
                    <p class="fw-bold"><strong>Total del carrito:</strong> ${{ number_format($paqueteriaShippingData['totalCart'], 2) }} MXN</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div id="direccion-selector-container-paqueteria" class="mb-4 p-4 bg-white rounded shadow-sm border text-center">
        <h5 class="mb-3 text-danger"><i class="bi bi-geo-alt-fill me-2"></i>No hay direcciones disponibles para envío por paquetería</h5>
        <p class="text-muted">Por favor, seleccione otro método de envío.</p>
    </div>
@endif

<script>
$(document).ready(function () {
    $('#direccion-selector-container-paqueteria input[name="direccionEnvioPaqueteria"]').on('change', function() {
        var direccionId = $(this).val();
        var cartId = "{{ $cartId }}";

        $.ajax({
            type: "POST",
            url: "{{ route('paqueteexpress.solicitar') }}",
            data: {
                id: '{{ auth()->id() }}',
                address_id: direccionId,
                _token: "{{ csrf_token() }}",
            },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    var shippingCostWithIVA = data.data.total;  // Obtener el costo total con IVA

                    $('#shipping-info-text-paqueteria').html('Costo del envío con IVA: $' + shippingCostWithIVA.toFixed(2) + ' MXN.');
                    
                    $('#SelectMethodPaqueteria').remove();
                    let div = '<div id="SelectMethodPaqueteria" class="mb-4 p-4 bg-white rounded shadow-sm border">' +
                              '<button id="addShippmentMethodPaqueteria" class="btn btn-primary form-control"><i class="fa fa-cart-arrow-down me-2"></i>Seleccionar</button>' +
                              '</div>';
                    $('#shipping-info-paqueteria').append(div);

                    $('#addShippmentMethodPaqueteria').on('click', function(){
                        $.ajax({
                            type: "POST",
                            url: "{{ route('cart.addPaqueteriaMethod') }}",
                            data: {
                                cart_id: cartId,
                                metodo: 'EnvioPorPaqueteria',
                                direccion: direccionId,
                                shipping_cost_with_iva: shippingCostWithIVA, // Enviar el costo con IVA
                                _token: "{{ csrf_token() }}",
                            },
                            dataType: 'json',
                            success: function (response) {
                                if(response.success) {
                                    $('#general-shipping-block').remove();
                                    location.reload();
                                } else {
                                    $('#shipping-info-text-paqueteria').html('<span class="text-danger">Error al guardar el método de envío: ' + response.error + '</span>');
                                }
                            },
                            error: function (error) {
                                $('#shipping-info-text-paqueteria').html('<span class="text-danger">Error en la solicitud AJAX.</span>');
                            }
                        });
                    });
                } else {
                    var errorMessage = '<span class="text-danger">Error al obtener el costo de envío: ' + data.error.descripcion + '</span>';
                    $('#shipping-info-text-paqueteria').html(errorMessage);
                    $('input[name="direccionEnvioPaqueteria"][value="' + direccionId + '"]').prop('disabled', true); // Deshabilitar la dirección con error
                }
            },
            error: function(error){
                $('#shipping-info-text-paqueteria').html('<span class="text-danger">Error en la solicitud AJAX.</span>');
                $('#SelectMethodPaqueteria').remove();
            }
        });
    });
});

</script>
