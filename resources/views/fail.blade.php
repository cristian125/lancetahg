@extends('template')

@section('body')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.navbar').hide();
        $('footer').hide();
        
        $('#goHomeBtn').click(function() {
            window.top.location.href = "{{ route('cart.show') }}";
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
                    <!-- Mensaje principal -->
                    <p class="lead">{{ $error }}</p>


                    <!-- Botón de redirección -->
                    <button id="goHomeBtn" class="btn btn-primary mt-3">Volver al Carrito</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
