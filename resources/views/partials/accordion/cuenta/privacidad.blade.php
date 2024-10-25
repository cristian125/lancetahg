<div class="accordion-item" id="privacidad">
    <h2 class="accordion-header" id="accPrivacidad">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#colPrivacidad"
            aria-expanded="False" aria-controls="colPrivacidad">
            <h5 class="title text-center fw-bold"><i class="fa fa-shield-halved"></i> Privacidad</h5>
        </button>
    </h2>
    <div id="colPrivacidad" class="accordion-collapse collapse" aria-labelledby="accPrivacidad"
        data-bs-parent="#accordionAccount">
        <div class="accordion-body">
            <form id="promoEmailForm" action="{{ route('cuenta.promociones.actualizar') }}" method="POST">
                @csrf

                <!-- Pregunta para recibir promociones -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="recibir_promociones" class="form-label fw-bold">¿Deseas recibir anuncios y promociones en su correo electrónico?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recibir_promociones" id="promociones_si" value="1" {{ old('recibir_promociones', $userData->recibir_promociones ?? '') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="promociones_si">
                                Sí, deseo recibir anuncios y promociones.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recibir_promociones" id="promociones_no" value="0" {{ old('recibir_promociones', $userData->recibir_promociones ?? '') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="promociones_no">
                                No, prefiero no recibir anuncios ni promociones.
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botón Guardar -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-floppy-disk"></i> Guardar Preferencias
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
