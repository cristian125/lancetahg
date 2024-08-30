@extends('template')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4">Formas de Pago</h1>
    <div class="d-flex justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="text-center">
                <h2 class="mb-3" style="background-color: #00a4b8; color: white; padding: 10px;">Tarjetas Bancarias</h2>
                <div class="mb-4">
                    <!-- Aquí va la imagen del señor en la computadora -->
                    <img src="{{ asset('storage/img/formasdepago/tarjeta.png') }}" alt="Tarjetas Bancarias" class="img-fluid">
                </div>
                <h3 class="mb-3" style="color: #00a4b8;">Debito o Credito</h3>
                <div class="mb-4">
                    <!-- Aquí va la imagen de las tarjetas -->
                    <img src="{{ asset('storage/img/formasdepago/form_tarjeta.png') }}" alt="Tarjetas de Crédito" class="img-fluid">
                </div>
                <div class="row justify-content-center mb-4">
                    <div class="col-10 col-md-6 col-lg-8">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success"></i>
                                La opción más rápida y segura para iniciar el proceso de su pedido
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success"></i>
                                Su pedido comienza a procesarse de inmediato. El pago está sujeto a la autorización del banco emisor
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success"></i>
                                Control antifraude que asegura sus transacciones en nuestro portal
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-center text-muted">
                        La información aquí expresada es exclusiva para las compras realizadas en línea dentro de nuestra Tienda Web.
                        <br>
                        Para mayor información, puede consultar nuestros Términos y Condiciones, en el apartado <a href="{{ route('terminosyc') }}" class="text-primary">Políticas Generales de Pago</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
