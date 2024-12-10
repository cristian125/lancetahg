<div class="accordion-item" id="accDirecciones">
    <h2 class="accordion-header" id="accDirecciones">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#Direcciones"
            aria-expanded="False" aria-controls="Direcciones">
            <h5 class="title text-center fw-bold"><i class="fa fa-house"></i> Direcciones</h5>
        </button>
    </h2>
    <div id="Direcciones" class="accordion-collapse collapse" aria-labelledby="accDirecciones"
        data-bs-parent="#accordionAccount">
        <div class="accordion-body">
            <div class="row p-2">
                <button id="addDireccion" class="btn btn-lanceta col-sm-2"><i class="fa fa-plus"></i> Nueva
                    dirección</button>

            </div>
            <div class="row">
                <ul class="list-group">
                    @if (count($direcciones) == 0)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="row" style="width: 100%;">
                                <p class="mb-1 text-center">Por el momento no tiene ninguna dirección
                                    registrada, porfavor agregue una nueva dirección.</p>
                            </div>
                        </li>
                    @else
                        @foreach ($direcciones as $direccion)
                            <li id="direccion{{ $direccion->id }}" data-id="{{ $direccion->id }}"
                                class="list-group-item justify-content-between align-items-start">
                                <div class="row px-3">
                                    <div class="col-sm-12">
                                        <div class="card shadow-sm p-4 mb-4 bg-white rounded">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <i class="fa fa-location-dot text-primary"></i>&nbsp;&nbsp;
                                                        <strong
                                                            class="direccion-nombre">{{ $direccion->nombre }}</strong>
                                                        <p class="text-muted mb-1">{{ $direccion->calle }} Int
                                                            {{ $direccion->no_int }} Ext {{ $direccion->no_ext }},
                                                            {{ $direccion->colonia }},
                                                            {{ strtolower($direccion->municipio) }},
                                                            {{ $direccion->codigo_postal }}, {{ $direccion->estado }},
                                                            {{ $direccion->pais }}.
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <button class="btn btn-danger btn-sm btn-delete-address"
                                                            title="Eliminar dirección">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-warning btn-sm btn-edit-address"
                                                            title="Editar dirección">
                                                            <i class="fa fa-pencil"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row mt-3">
                                                    <div class="col-12 col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input type="radio" name="direccion_predeterminada"
                                                                class="form-check-input radio-predeterminada styled-radio"
                                                                data-id="{{ $direccion->id }}"
                                                                id="direccionPred{{ $direccion->id }}"
                                                                {{ $direccion->predeterminada ? 'checked' : '' }}>
                                                            <label class="form-check-label label-predeterminada"
                                                                for="direccionPred{{ $direccion->id }}">
                                                                Dirección de Envío
                                                            </label>
                                                        </div>
                                                    </div>
                                                
                                                    <div class="col-12 col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input type="radio" name="direccion_facturacion"
                                                                class="form-check-input radio-facturacion styled-radio"
                                                                data-id="{{ $direccion->id }}"
                                                                id="direccionFact{{ $direccion->id }}"
                                                                {{ $direccion->facturacion ? 'checked' : '' }}>
                                                            <label class="form-check-label label-facturacion"
                                                                for="direccionFact{{ $direccion->id }}">
                                                                Dirección Fiscal
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>


{{-- <script>
    document.addEventListener("DOMContentLoaded", function() {

        // Mostrar mensaje de éxito
        function mostrarToast(mensaje) {
            const toast = document.createElement('div');
            toast.classList.add('toast-message');
            toast.innerText = mensaje;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'fadeout 0.5s';
                setTimeout(() => toast.remove(), 500);
            }, 2500);
        }

        // Mostrar el spinner de carga
        function mostrarLoader() {
            document.body.classList.add('loading');
        }

        // Ocultar el spinner de carga
        function ocultarLoader() {
            document.body.classList.remove('loading');
        }

        // Seleccionar como dirección predeterminada
        const radiosPredeterminada = document.querySelectorAll('.radio-predeterminada');
        radiosPredeterminada.forEach(radio => {
            radio.addEventListener('change', function() {
                const direccionID = this.getAttribute('data-id');
                mostrarLoader(); // Mostrar loader

                fetch('/setDireccionPredeterminada', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            direccion_predeterminada: direccionID
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        ocultarLoader(); // Ocultar loader
                        if (data.message ===
                            'Dirección predeterminada actualizada correctamente') {
                            mostrarToast('Dirección de envío actualizada correctamente.');
                        }
                    })
                    .catch(error => {
                        ocultarLoader(); // Ocultar loader
                        console.error('Error:', error);
                        mostrarToast('Error al actualizar la dirección.');
                    });
            });
        });

        // Seleccionar como dirección de facturación
        const radiosFacturacion = document.querySelectorAll('.radio-facturacion');
        radiosFacturacion.forEach(radio => {
            radio.addEventListener('change', function() {
                const direccionID = this.getAttribute('data-id');
                mostrarLoader(); // Mostrar loader

                fetch('/setDireccionFacturacion', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            direccion_facturacion: direccionID
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        ocultarLoader(); // Ocultar loader
                        if (data.message ===
                            'Dirección físcal actualizada correctamente') {
                            mostrarToast(
                                'Dirección físcal actualizada correctamente.');
                        }
                    })
                    .catch(error => {
                        ocultarLoader(); // Ocultar loader
                        console.error('Error:', error);
                        mostrarToast('Error al actualizar la dirección.');
                    });
            });
        });
    });

</script> --}}
<script>
    $(document).ready(function() {

        // Mostrar mensaje de éxito
        function mostrarToast(mensaje) {
            const $toast = $('<div>', {
                class: 'toast-message',
                text: mensaje
            });
            $('body').append($toast);

            setTimeout(function() {
                $toast.css('animation', 'fadeout 0.5s');
                setTimeout(function() {
                    $toast.remove();
                }, 500);
            }, 2500);
        }

        // Mostrar el spinner de carga
        function mostrarLoader() {
            $('body').addClass('loading');
        }

        // Ocultar el spinner de carga
        function ocultarLoader() {
            $('body').removeClass('loading');
        }

        // Seleccionar como dirección predeterminada
        $('.radio-predeterminada').on('change', function() {
            const direccionID = $(this).data('id');
            mostrarLoader();

            $.ajax({
                url: '/setDireccionPredeterminada',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify({
                    direccion_predeterminada: direccionID
                }),
                contentType: 'application/json',
                success: function(data) {
                    ocultarLoader();
                    if (data.message === 'Dirección predeterminada actualizada correctamente') {
                        mostrarToast('Dirección de envío actualizada correctamente.');
                    }
                },
                error: function(error) {
                    ocultarLoader();
                    console.error('Error:', error);
                    mostrarToast('Error al actualizar la dirección.');
                }
            });
        });

        // Seleccionar como dirección de facturación
        $('.radio-facturacion').on('change', function() {
            const direccionID = $(this).data('id');
            const id = $(this).attr('id');
            mostrarLoader();

            $.ajax({
                url: '/setDireccionFacturacion',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify({
                    direccion_facturacion: direccionID
                }),
                contentType: 'application/json',
                success: function(data) {
                    ocultarLoader();
                    if (data.message === 'Dirección físcal actualizada correctamente') {
                        mostrarToast('Dirección físcal actualizada correctamente.');
                    }
                    location.href = '{{ route("cuenta") }}?section=Direcciones#'+id;
                },
                error: function(error) {
                    ocultarLoader();
                    console.error('Error:', error);
                    mostrarToast('Error al actualizar la dirección.');
                }
            });
        });
    });
</script>

<style>

    /* Toast Message */
    .toast-message {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #26d2b6;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    @keyframes fadein {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeout {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    /* Loading Spinner */
    .loading::after {
        content: "";
        position: fixed;
        top: 50%;
        left: 50%;
        width: 40px;
        height: 40px;
        border: 5px solid rgba(0, 0, 0, 0.2);
        border-top: 5px solid #26d2b6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transform: translate(-50%, -50%);
        z-index: 10000;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Estilo para las tarjetas de dirección */
.card {
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease-in-out;
}

.card:hover {
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
    transform: translateY(-5px);
}

/* Estilo para el ícono de dirección */
.fa-location-dot {
    font-size: 1.5rem;
    color: #007bff;
}

.direccion-nombre {
    font-size: 1.1rem;
    font-weight: 600;
}

/* Estilo para las etiquetas de radio */
.label-predeterminada, .label-facturacion {
    font-size: 1rem;
    color: #5c5c5c;
    font-weight: 500;
}

/* Estilo para los radio buttons */
.styled-radio {
    width: 1.2rem;
    height: 1.2rem;
    margin-right: 8px;
    accent-color: #007bff;
}

/* Añadir hover a los botones */
.btn-delete-address, .btn-edit-address {
    transition: background-color 0.2s ease-in-out;
}

.btn-delete-address:hover {
    background-color: #ff4d4d;
}

.btn-edit-address:hover {
    background-color: #f4d03f;
}

/* Estilo para la línea divisoria */
hr {
    margin: 1rem 0;
    border: none;
    border-top: 1px solid #e0e0e0;
}

/* Ajuste de los radio buttons en pantallas pequeñas */
@media (max-width: 768px) {
    .form-check {
        width: 100%;               /* Ocupar todo el ancho */
        margin-bottom: 10px;      /* Separación entre radios */
        display: flex;
        align-items: center;
        justify-content: start;   /* Alinear a la izquierda */
    }

    .form-check-label {
        font-size: 1rem;
        margin-left: 10px;        /* Separación con el botón */
    }

    .styled-radio {
        width: 1.5rem;            /* Tamaño ajustado */
        height: 1.5rem;
        accent-color: #007bff;   /* Color personalizado */
    }
}
</style>
