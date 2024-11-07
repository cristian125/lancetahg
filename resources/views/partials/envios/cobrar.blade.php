@if (!empty($cobrarShippingData['direcciones']))
    <div class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <div id="direccion-container" class="mb-4 p-4 bg-white rounded shadow-sm border">
                    <h5 class="mb-4 text-primary"><i class="bi bi-geo-alt-fill me-2"></i>Seleccione su dirección</h5>
                    <div class="form-group">
                        @foreach ($cobrarShippingData['direcciones'] as $direccion)
                            <div class="form-check mb-3 {{ $direccion->esValida ? '' : 'text-muted opacity-50 no-select' }}">
                                <input class="form-check-input me-2 select-address-radio" type="radio" name="direccionEnvio"
                                    value="{{ $direccion->id }}" @unless ($direccion->esValida) disabled="disabled" @endunless>
                                <label class="form-check-label">
                                    <strong>{{ $direccion->nombre }}</strong><br>
                                    <span class="text-muted">
                                        {{ $direccion->calle }} {{ $direccion->no_ext }}
                                        {{ $direccion->no_int ? 'Int. ' . $direccion->no_int : '' }},
                                        {{ $direccion->colonia }}, {{ $direccion->municipio }},
                                        {{ $direccion->codigo_postal }}, {{ $direccion->pais }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p id="direccion-valida-notification" class="mt-3 text-success">
                        <i class="bi bi-check-circle-fill me-2"></i>Seleccione una dirección válida para Envío por Cobrar.
                    </p>
                </div>
            </div>

            <div id="envio-info" class="col-md-6">
                <div id="envio-info-bloque" class="mb-4 p-4 bg-white rounded shadow-sm border">
                    <h5 class="mb-4 text-primary"><i class="bi bi-info-circle-fill me-2"></i>Información de Envío</h5>
                    <p id="envio-info-texto" class="mb-3 text-muted">
                        Por favor seleccione una dirección.
                    </p>
                    <p class="fw-bold"><strong>Total del carrito:</strong>
                        ${{ number_format($cobrarShippingData['totalCart'], 2, '.', ',') }} MXN</p>
                </div>

            </div>
        </div>
    </div>
@else
    <div id="direccion-container" class="mb-4 p-4 bg-white rounded shadow-sm border text-center">
        <h5 class="mb-3 text-danger"><i class="bi bi-geo-alt-fill me-2"></i>No hay direcciones disponibles para Envío por Cobrar</h5>
        <p class="text-muted">Por favor, seleccione otro método de envío.</p>
    </div>
@endif

<script>
    $(document).ready(function() {

        $('#direccion-container input[name="direccionEnvio"]').on('change', function() {

            $('#envio-info-texto').html('El envío se realizará a la dirección seleccionada.');
            $('#confirm-envio-button-container').remove();
            let buttonContainer = '<div id="confirm-envio-button-container" class="mt-4">' +
                                  '<button id="confirmarEnvioButton" class="btn btn-primary"><i class="fa fa-check-circle"></i> Confirmar Método de Envío</button>' +
                                  '</div>';
            $('#envio-info').append(buttonContainer);
            $('#confirmarEnvioButton').on('click', function() {
                var cartId = "{{ $cartId }}";
                var direccionId = $('input[name="direccionEnvio"]:checked').val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('cart.updateMethod') }}",
                    data: {
                        cart_id: cartId,
                        metodo: 'EnvioPorCobrar',
                        direccion: direccionId,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#envio-info-bloque').remove();
                            location.reload();
                        } else {
                            alert('Error al guardar el método de envío: ' + response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la solicitud AJAX.');
                    }
                });
            });
        });
    });
</script>
