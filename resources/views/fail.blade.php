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
                    <div class="card-header bg-danger text-white">
                        <h3>Pago Rechazado</h3>
                    </div>
                    <div class="card-body">
                        <p class="lead">Lamentablemente, tu pago no ha podido ser procesado.</p>

                        @if (session('error'))
                            <p>{{ session('error') }}</p>
                        @else
                            <p>Su pago fue rechazado. Verifique con su banco y vuelva a intentarlo más tarde.</p>
                        @endif

                        @if (session('debug_info'))
                            <div class="alert alert-warning mt-4">
                                <h5>Información de depuración:</h5>
                                <pre>{{ session('debug_info') }}</pre>
                            </div>
                        @endif

                        <!-- Botón para volver al inicio -->
                        <button id="goHomeBtn" class="btn btn-primary mt-3">Volver al Inicio</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
