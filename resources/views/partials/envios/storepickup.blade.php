<div class="mb-4 p-3 bg-light rounded shadow-sm">
    <h5 class="mb-3 text-primary"><i class="bi bi-shop"></i> Seleccione la tienda para recoger</h5>
    <div class="form-group">
        <select id="tienda-selector" class="form-select">
            @foreach ($storePickupData['tiendas'] as $tienda)
                <option value="{{ $tienda->id }}" data-direccion="{{ $tienda->direccion }}"
                    data-telefono="{{ $tienda->telefono }}" data-horario-semana="{{ $tienda->horario_semana }}"
                    data-horario-sabado="{{ $tienda->horario_sabado }}"
                    data-google-maps-url="{{ $tienda->google_maps_url }}">
                    {{ $tienda->nombre }} - {{ $tienda->direccion }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <strong>Dirección:</strong> <span
                class="store-address">{{ $storePickupData['tiendas']->first()->direccion }}</span><br>
            <strong>Teléfono:</strong> <span
                class="store-phone">{{ $storePickupData['tiendas']->first()->telefono }}</span><br>
            <strong>Horario:</strong> <span class="store-hours">
                Lunes a Viernes: {{ $storePickupData['tiendas']->first()->horario_semana }}<br>
                Sábado: {{ $storePickupData['tiendas']->first()->horario_sabado }}
            </span><br>
            <iframe src="{{ $storePickupData['tiendas']->first()->google_maps_url }}" width="540" height="360"
                style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="col-md-6">
            <!-- Calendario interactivo -->
            <div class="mb-4 p-3 bg-light rounded shadow-sm">
                <h5 class="mb-3 text-primary"><i class="bi bi-calendar"></i> Seleccione la fecha y hora para recoger su
                    pedido</h5>
                <div id="date-error" class="alert alert-danger text-center mb-2" style="display: none;"></div>
                <input type="date" id="pickup-date" class="form-control mb-3" placeholder="Seleccione fecha">
                <select id="pickup-time" class="form-select">
                    <!-- Las horas se generarán dinámicamente -->
                </select>

            </div>


            <!-- Sección de Pago (se oculta inicialmente) -->
            <div id="payment-section" class="p-4 bg-light rounded shadow-sm mt-4" style="display: none;">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 text-md-end mb-3 mb-md-0">
                        <h4 class="mb-2">Total a pagar:</h4>
                        <h4 class="text-primary mb-4">${{ number_format($totalPrice, 2, '.', ',') }} MXN</h4>
                    </div>
                    <div class="col-12 col-md-6 text-md-start text-center">
                        <form id="storePickupForm" action="{{ route('storepickup.save') }}" method="POST">
                            @csrf
                            <input type="hidden" name="store_id" id="store_id">
                            <input type="hidden" name="pickup_date" id="pickup_date_hidden">
                            <input type="hidden" name="pickup_time" id="pickup_time_hidden">
                            <button type="submit"
                                class="btn btn-primary btn-lg d-flex justify-content-center align-items-center w-50 mx-auto">
                                <i class="bi bi-check me-2"></i> Seleccionar
                            </button>
                        </form>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p class="mb-3">No hay cobro de envío en este método de entrega.
                        <a href="#" data-bs-toggle="modal" data-bs-target="#enviosModal">Más información acerca de
                            esto</a>
                    </p>
                    <img src="{{ asset('storage/img/formasdepago/form_tarjeta.png') }}" alt="Formas de Pago"
                        class="img-fluid" style="max-width: 150px;">
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="enviosModal" tabindex="-1" aria-labelledby="enviosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enviosModalLabel">Información de Envío</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Aquí se incluirá la página de envíos -->
                <iframe src="{{ route('envios') }}?m=0" style="width: 100%; height: 500px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tiendaSelector = document.getElementById('tienda-selector');
        const direccionElement = document.querySelector('.store-address');
        const telefonoElement = document.querySelector('.store-phone');
        const horarioElement = document.querySelector('.store-hours');
        const pickupDateInput = document.getElementById('pickup-date');
        const pickupTimeInput = document.getElementById('pickup-time');
        const dateErrorElement = document.getElementById('date-error');
        const paymentSection = document.getElementById('payment-section');

        // Esconder la sección de pago inicialmente
        paymentSection.style.display = 'none';

        tiendaSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            direccionElement.innerText = selectedOption.getAttribute('data-direccion');
            telefonoElement.innerText = selectedOption.getAttribute('data-telefono');
            horarioElement.innerHTML = `
                Lunes a Viernes: ${selectedOption.getAttribute('data-horario-semana')}<br>
                Sábado: ${selectedOption.getAttribute('data-horario-sabado')}
            `;
        });

        // Configuración del calendario interactivo
        pickupDateInput.addEventListener('input', function() {
            const selectedDate = new Date(this.value +
            'T00:00:00'); // Ajuste de fecha para evitar problemas de sincronización
            const day = selectedDate.getUTCDay(); // Obtener el día correcto usando UTC
            dateErrorElement.style.display = 'none'; // Ocultar el mensaje de error

            pickupTimeInput.innerHTML = ''; // Limpiar opciones previas

            let hours = [];
            if (day === 6) { // Sábado
                hours = generateHours('10:00', '15:00');
            } else if (day >= 1 && day <= 5) { // Lunes a Viernes
                hours = generateHours('10:00', '18:00');
            } else {
                dateErrorElement.textContent =
                    'No es posible recoger el día Domingo, por favor, elija otro día.';
                dateErrorElement.style.display = 'block';
                this.value = ''; // Restablecer la fecha si se selecciona un domingo
                return;
            }

            // Añadir las nuevas opciones al selector de hora
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Seleccione la hora';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            pickupTimeInput.appendChild(defaultOption);

            hours.forEach(hour => {
                const option = document.createElement('option');
                option.value = hour;
                option.textContent = hour;
                pickupTimeInput.appendChild(option);
            });

            // Bloquear la selección del mismo día, pero permitir el día siguiente
            const currentDate = new Date();
            const minDate = new Date(currentDate);
            minDate.setDate(currentDate.getDate());

            if (selectedDate <= minDate) {
                dateErrorElement.textContent = 'No puedes programar un pedido para el mismo día.';
                dateErrorElement.style.display = 'block';
                this.value = '';
                pickupTimeInput.innerHTML = '';
                paymentSection.style.display = 'none'; // Ocultar la sección de pago si hay un error
                return;
            }
        });

        // Mostrar la sección de pago cuando se seleccionen la fecha y la hora
        pickupTimeInput.addEventListener('change', function() {
            if (pickupDateInput.value && pickupTimeInput.value) {
                paymentSection.style.display = 'block'; // Mostrar la sección de pago
            } else {
                paymentSection.style.display =
                'none'; // Ocultar la sección de pago si no se ha seleccionado todo
            }
        });

        // Función para generar las opciones de hora en intervalos de una hora
        function generateHours(startTime, endTime) {
            const start = parseInt(startTime.replace(':', ''));
            const end = parseInt(endTime.replace(':', ''));
            const hours = [];

            for (let time = start; time <= end; time += 100) {
                let hour = time.toString().padStart(4, '0');
                hour = `${hour.slice(0, 2)}:${hour.slice(2)}`;
                hours.push(hour);
            }

            return hours;
        }
    });

    
    // Manejo de inputs ocultos y formulario
    document.addEventListener('DOMContentLoaded', function() {
        const tiendaSelector = document.getElementById('tienda-selector');
        const pickupDateInput = document.getElementById('pickup-date');
        const pickupTimeInput = document.getElementById('pickup-time');

        const storeIdInput = document.getElementById('store_id');
        const pickupDateHiddenInput = document.getElementById('pickup_date_hidden');
        const pickupTimeHiddenInput = document.getElementById('pickup_time_hidden');

        // Actualizar inputs ocultos cuando se cambia tienda, fecha o hora
        tiendaSelector.addEventListener('change', function() {
            storeIdInput.value = tiendaSelector.value;
        });

        pickupDateInput.addEventListener('change', function() {
            pickupDateHiddenInput.value = pickupDateInput.value;
        });

        pickupTimeInput.addEventListener('change', function() {
            pickupTimeHiddenInput.value = pickupTimeInput.value;
        });
    });
</script>
