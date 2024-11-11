<div class="accordion-item" id="facturación">
    <h2 class="accordion-header" id="accFacturacion">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#colFacturacion"
            aria-expanded="False" aria-controls="colFacturacion">
            <h5 class="title text-center fw-bold"><i class="fa fa-file-invoice-dollar"></i> Datos Fiscales</h5>
        </button>
    </h2>
    <div id="colFacturacion" class="accordion-collapse collapse" aria-labelledby="accFacturacion"
        data-bs-parent="#accordionAccount">
        <div class="accordion-body">

            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> <strong>Nota:</strong> Los datos proporcionados en esta sección se
                utilizarán únicamente para el envío y no para facturación, de acuerdo con las disposiciones de la <a
                    href="http://omawww.sat.gob.mx/tramitesyservicios/Paginas/documentos/Instructivo_ComplementoCartaPorte_Autotransporte_3.0.pdf"
                    target="_blank">Carta Porte 3.1 del SAT (Receptor, Apartado 1.1)</a>. En caso de no proporcionar
                esta información, la factura se emitirá bajo el concepto de Público en General.
            </div>

            <div class="row">
                <form id="frmFacturacion" action="{{ route('cuenta.facturacion.actualizar') }}" method="POST">

                    @csrf

                    @if ($direccion_facturacion)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Dirección Físcal Seleccionada:</label>
                                <p class="border p-2">
                                    {{ $direccion_facturacion->calle }} {{ $direccion_facturacion->no_ext }},
                                    {{ $direccion_facturacion->colonia }}, {{ $direccion_facturacion->municipio }},
                                    {{ $direccion_facturacion->estado }} - {{ $direccion_facturacion->codigo_postal }},
                                    {{ $direccion_facturacion->pais }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-danger">No has seleccionado una dirección de facturación. Por favor selecciona
                            una.</p>
                    @endif
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
                    <!-- Tipo de Persona -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="tipo_persona" class="form-label fw-bold">Tipo de Persona:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                                <select id="tipo_persona" name="tipo_persona" class="form-control" required>
                                    <option value="" disabled selected>Selecciona</option>
                                    <option value="fisica"
                                        {{ old('tipo_persona', $userData->tipo_persona ?? '') == 'fisica' ? 'selected' : '' }}>
                                        Física</option>
                                    <option value="moral"
                                        {{ old('tipo_persona', $userData->tipo_persona ?? '') == 'moral' ? 'selected' : '' }}>
                                        Moral</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="regimen_fiscal" class="form-label fw-bold">Régimen Fiscal:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-file-contract"></i></span>
                                <select id="regimen_fiscal" name="regimen_fiscal" class="form-control" required>
                                    <option value="{{ $regimen_fiscal_seleccionado->codigo }}">{{ $regimen_fiscal_seleccionado->codigo }} - {{ $regimen_fiscal_seleccionado->descripcion }}</option>
                                    {{-- <option value="">Selecciona un régimen</option> --}}
                                    <!-- Opciones para Persona Física -->
                                    {{-- @foreach ($regimenes_fiscales_fisica as $regimen)
                                        <option class="fisica-option" value="{{ $regimen->codigo }}"
                                            
                                            {{ old('regimen_fiscal', $userData->regimen_fiscal ?? '') == $regimen->codigo ? 'selected' : '' }}>
                                            {{ $regimen->codigo }} - {{ $regimen->descripcion }}
                                        </option>
                                    @endforeach --}}
                                    <!-- Opciones para Persona Moral -->
                                    {{-- @foreach ($regimenes_fiscales_moral as $regimen)
                                        <option class="moral-option" value="{{ $regimen->codigo }}"
                                            
                                            {{ old('regimen_fiscal', $userData->regimen_fiscal ?? '') == $regimen->codigo ? 'selected' : '' }}>
                                            {{ $regimen->codigo }} - {{ $regimen->descripcion }}
                                        </option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Uso del CFDI -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="uso_cfdi" class="form-label fw-bold">Uso del CFDI:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-file-alt"></i></span>
                                <select id="uso_cfdi" name="uso_cfdi" class="form-control" required>
                                    <option value="{{ $uso_de_cfdi_seleccionado->codigo }}">{{ $uso_de_cfdi_seleccionado->codigo }} - {{ $uso_de_cfdi_seleccionado->descripcion }}</option>
                                    {{-- <option value="">Selecciona un uso de CFDI</option> --}}
                                </select>
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
<script>
    $(document).ready(function() {
        // Cuando se selecciona el tipo de persona
        $('#tipo_persona').on('change', function() {
            const tipoPersona = $(this).val();
            
            // Limpiar y ocultar select de régimen fiscal y uso de CFDI
            $('#regimen_fiscal').empty().append('<option value="">Selecciona un régimen</option>');
            $('#uso_cfdi').empty().append('<option value="">Selecciona un uso de CFDI</option>');

            // Realizar la petición AJAX para obtener los regímenes fiscales
            if (tipoPersona) {
                $.ajax({
                    url: `/regimenes/${tipoPersona}`,
                    method: 'GET',
                    success: function(data) {
                        $('#regimen_fiscal').show(); // Mostrar el select de régimen fiscal
                        data.forEach(regimen => {
                            $('#regimen_fiscal').append(new Option(`${regimen.codigo} - ${regimen.descripcion}`, regimen.codigo));
                        });
                    },
                    error: function() {
                        alert('Error al cargar los regímenes fiscales.');
                    }
                });
            }
        });

        // Cuando se selecciona el régimen fiscal
        $('#regimen_fiscal').on('change', function() {
            const regimenFiscalId = $(this).val();
            
            // Limpiar y ocultar el select de uso de CFDI
            $('#uso_cfdi').empty().append('<option value="">Selecciona un uso de CFDI</option>').hide();

            // Realizar la petición AJAX para obtener los usos de CFDI
            if (regimenFiscalId) {
                $.ajax({
                    url: `/usos-cfdi/${regimenFiscalId}`,
                    method: 'GET',
                    success: function(data) {
                        $('#uso_cfdi').show(); // Mostrar el select de uso de CFDI
                        data.forEach(uso => {
                            $('#uso_cfdi').append(new Option(`${uso.codigo} - ${uso.descripcion}`, uso.codigo));
                        });
                    },
                    error: function() {
                        alert('Error al cargar los usos de CFDI.');
                    }
                });
            }
        });
    });
</script>
