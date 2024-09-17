@extends('template')

@section('body')
<div class="container my-5 d-flex justify-content-center">
    <div class="col-lg-10">
        <h1 class="text-center mb-4 display-4">Tarjeta Cliente Distinguido</h1>
        
        <!-- Sección con la imagen y el texto -->
        <div class="row mb-5 align-items-start"> <!-- Cambié align-items-center por align-items-start para que la imagen esté más arriba -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ asset('storage/img/tarjetaclientedis/clientedist.png') }}" class="img-fluid" alt="Tarjeta Cliente Distinguido" style="background-color: transparent; box-shadow: none;"> <!-- Eliminé el fondo blanco -->
            </div>
            <div class="col-lg-6">
                <p class="lead">En Lanceta HG agradecemos su lealtad, es por eso que hemos creado para usted el programa Cliente Distinguido, con el cual obtendrá un descuento inicial del 5% que se irá incrementando de acuerdo al promedio de compras que realice, además la tarjeta es válida en todas nuestras sucursales.</p>
                <p class="text-danger"><strong>*El Descuento de Cliente Distinguido, no es válido para compras en línea.</strong></p>
                
                <h3 class="mt-4">Beneficios:</h3>
                <ul>
                    <li>Descuento inicial del 5%</li>
                    <li>Descuento incremental por volumen conforme al promedio de compras trimestral.</li>
                    <li>La tarjeta de Cliente Distinguido es efectiva en cualquiera de las Tiendas Físicas y el Callcenter de Lanceta HG.</li>
                </ul>

                <h3 class="mt-4">Requisitos:</h3>
                <ul>
                    <li>Llenar el formulario y adjuntar la siguiente documentación:</li>
                    <li>Comprobante de domicilio.</li>
                    <li>Identificación oficial.</li>
                </ul>

                <h3 class="mt-4">Restricciones:</h3>
                <ul>
                    <li>Descuento válido únicamente al presentar la tarjeta físicamente en compras realizadas en tienda, o bien, dando su número de tarjeta, solamente si realiza pedido en callcenter.</li>
                    <li>Descuento no acumulable. No válido con otras promociones.</li>
                </ul>

                <h3 class="mt-4">Mecánica del trámite:</h3>
                <ul>
                    <li>El trámite de su tarjeta <strong>comenzará solo si envió la documentación completa</strong> y además, que el área administrativa haya validado los documentos.</li>
                    <li>Una vez cumplida la validación, la tarjeta estará lista dentro de 3 a 5 días hábiles.</li>
                    <li>En caso de no recibir notificación alguna dentro del periodo mencionado, favor de comunicarse al 55-5578-1958 para preguntar del estatus de su trámite.</li>
                </ul>

                <div class="text-center mt-5">
                <a href="#" class="btn btn-lanceta btn-lg">INICIAR TRÁMITE TARJETA CLIENTE DISTINGUIDO</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
