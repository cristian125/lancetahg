@extends('template')
@section('body')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Ocultar el navbar y el footer
            $('.navbar').hide();
            $('footer').hide();
            
            // Botón para redirigir fuera del iframe
            $('#goHomeBtn').click(function() {
                window.top.location.href = "{{ route('home') }}"; // Redirigir fuera del iframe
            });
        });
    </script>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center">
                    <div class="card-header bg-success text-white">
                        <h3>¡Pago Realizado con Éxito!</h3>
                    </div>
                    <div class="card-body">
                        <p class="lead">Tu pago ha sido procesado correctamente.</p>
                        <p>Gracias por tu compra.</p>

                        <!-- Botón para volver al inicio -->
                        <button id="goHomeBtn" class="btn btn-primary mt-3">Volver al Inicio</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Esperar 1 segundo antes de hacer la petición
        setTimeout(function() {
            // Obtener el CSRF token para la petición AJAX
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Hacer la petición AJAX para procesar la orden
            fetch("{{ route('process.order') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    // Puedes pasar más datos aquí si es necesario
                    responseData: @json(session('responseData'))
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Orden procesada correctamente.');
                } else {
                    console.error('Error al procesar la orden:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }, 1000);  // Espera de 1 segundo
    </script>
@endsection
