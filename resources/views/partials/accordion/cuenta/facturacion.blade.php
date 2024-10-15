<div class="accordion-item">
    <h2 class="accordion-header" id="accFacturacion">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#colFacturacion"
            aria-expanded="False" aria-controls="colFacturacion">
            <h5 class="title text-center fw-bold"><i class="fa fa-file-invoice-dollar"></i> Datos Fiscales</h5>
        </button>
    </h2>
    <div id="colFacturacion" class="accordion-collapse collapse" aria-labelledby="accFacturacion"
        data-bs-parent="#accordionAccount">
        <div class="accordion-body">
            <div class="row">
                <form id="frmFacturacion" action="{{ route('cuenta.facturacion.actualizar') }}" method="POST">
                    @csrf

                    <!-- Dirección de Facturación Seleccionada -->
                    @if($direccion_facturacion)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Dirección de Facturación Seleccionada:</label>
                                <p class="border p-2">
                                    {{ $direccion_facturacion->calle }} {{ $direccion_facturacion->no_ext }},
                                    {{ $direccion_facturacion->colonia }}, {{ $direccion_facturacion->municipio }},
                                    {{ $direccion_facturacion->estado }} - {{ $direccion_facturacion->codigo_postal }},
                                    {{ $direccion_facturacion->pais }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-danger">No has seleccionado una dirección de facturación. Por favor selecciona una.</p>
                    @endif

                    <!-- Nombre o Razón Social -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="razon_social" class="form-label fw-bold">Nombre o Razón Social:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-building"></i>
                                </span>
                                <input type="text" id="razon_social" name="razon_social" class="form-control"
                                    value="{{ old('razon_social', $userData->razon_social ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- RFC -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="rfc" class="form-label fw-bold">RFC:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-id-card"></i>
                                </span>
                                <input type="text" id="rfc" name="rfc" class="form-control"
                                    value="{{ old('rfc', $userData->rfc ?? '') }}" required maxlength="13">
                            </div>
                        </div>
                    </div>

                    <!-- Régimen Fiscal -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="regimen_fiscal" class="form-label fw-bold">Régimen Fiscal:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-file-contract"></i>
                                </span>
                                <input type="text" id="regimen_fiscal" name="regimen_fiscal" class="form-control"
                                    value="{{ old('regimen_fiscal', $userData->regimen_fiscal ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Uso del CFDI -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="uso_cfdi" class="form-label fw-bold">Uso del CFDI:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-file-alt"></i>
                                </span>
                                <input type="text" id="uso_cfdi" name="uso_cfdi" class="form-control"
                                    value="{{ old('uso_cfdi', $userData->uso_cfdi ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Botón Guardar -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-lanceta w-100">
                                <i class="fa fa-floppy-disk"></i> Guardar Datos fiscales
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
