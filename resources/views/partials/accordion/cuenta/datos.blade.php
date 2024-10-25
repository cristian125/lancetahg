<div class="accordion-item" id="datos">
    <h2 class="accordion-header" id="accDatos">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colDatos"
            aria-expanded="false" aria-controls="colDatos">
            <h5 class="title text-center fw-bold"><i class="fa fa-user"></i> Datos Personales</h5>
        </button>
    </h2>
    <div id="colDatos" class="accordion-collapse collapse" aria-labelledby="accDatos"
        data-bs-parent="#accordionAccount">
        <div class="accordion-body">
            <div class="row">
                <form id="frmDatos" action="{{ route('update.datos') }}" method="post">
                    @csrf
                    <!-- Tratamiento -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="tratamiento" class="form-label fw-bold">¿Cómo desea que nos dirijamos a usted?</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-question-octagon-fill"></i>
                                </span>
                                <select name="tratamiento" id="tratamiento" class="form-select" required>
                                    <option value="" selected>Seleccione una opción</option>
                                    <option value="NA" {{ $userData && $userData->tratamiento == 'NA' ? 'selected' : '' }}>Ninguno</option>
                                    <option value="Sr." {{ $userData && $userData->tratamiento == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                    <option value="Sra." {{ $userData && $userData->tratamiento == 'Sra.' ? 'selected' : '' }}>Sra.</option>
                                    <option value="Dr." {{ $userData && $userData->tratamiento == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                    <option value="Dra." {{ $userData && $userData->tratamiento == 'Dra.' ? 'selected' : '' }}>Dra.</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Nombre y Apellidos -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label fw-bold">Nombre:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ $user->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_paterno" class="form-label fw-bold">Apellido Paterno:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-id-card"></i>
                                </span>
                                <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" placeholder="Apellido Paterno" value="{{ $userData ? $userData->apellido_paterno : '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_materno" class="form-label fw-bold">Apellido Materno:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-id-card"></i>
                                </span>
                                <input type="text" id="apellido_materno" name="apellido_materno" class="form-control" placeholder="Apellido Materno" value="{{ $userData ? $userData->apellido_materno : '' }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="email" class="form-label fw-bold">Correo Electrónico:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="telefono" class="form-label fw-bold">Número de Teléfono:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-mobile"></i>
                                </span>
                                <input type="tel" id="telefono" name="telefono" class="form-control" pattern="\d{10}" placeholder="Número Telefónico (10 dígitos)" value="{{ $userData ? $userData->telefono : '' }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fa fa-floppy-disk"></i> Guardar
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
        // Validación personalizada del formulario
        $('#frmDatos').on('submit', function(e) {
            // Prevenir el envío por defecto
            e.preventDefault();
            let valid = true;
            
            // Validar campo de tratamiento
            if ($('#tratamiento').val() === "") {
                valid = false;
                alert("Por favor, seleccione un tratamiento.");
                $('#tratamiento').focus();
                return false;
            }

            // Validar campo de nombre
            // if ($('#nombre').val().trim() === "") {
            //     valid = false;
            //     alert("El campo de nombre no puede estar vacío.");
            //     $('#nombre').focus();
            //     return false;
            // }

            // Validar campo de apellido paterno
            if ($('#apellido_paterno').val().trim() === "") {
                valid = false;
                alert("El campo de apellido paterno no puede estar vacío.");
                $('#apellido_paterno').focus();
                return false;
            }

            // Validar campo de apellido materno
            if ($('#apellido_materno').val().trim() === "") {
                valid = false;
                alert("El campo de apellido materno no puede estar vacío.");
                $('#apellido_materno').focus();
                return false;
            }

            // Validar campo de teléfono
            let telefono = $('#telefono').val();
            let phoneRegex = /^\d{10}$/;
            if (!phoneRegex.test(telefono)) {
                valid = false;
                alert("Por favor, ingrese un número de teléfono válido de 10 dígitos.");
                $('#telefono').focus();
                return false;
            }

            // Si todas las validaciones son correctas, enviar el formulario
            if (valid) {
                this.submit();
            }
        });
    });
</script>
