@extends('template')

@section('body')
<div class="container my-5 d-flex justify-content-center">
    <div class="col-lg-10">
        <h1 class="text-center mb-4 display-4">Quiénes somos</h1>

        <div class="row mb-5 align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ asset('storage/img/nosotros/nosotros1.jpeg') }}" class="img-fluid rounded shadow-lg" alt="Imagen de Lanceta HG">
            </div>
            <div class="col-lg-6">
                <h2 class="mb-3">Historia</h2>
                <p class="lead">Lanceta S.A., es creada en el año 1977, como una empresa dedicada a la venta de productos de curación, instrumental y equipos médicos, con un concepto tradicional de venta al sector gobierno y empresas particulares.</p>
                <p>En 1990, se convierte en el <strong>PRIMER AUTOSERVICIO MÉDICO EN AMÉRICA</strong>, brindando atención directa a médicos y pacientes, y cambiando su razón social a Lanceta HG S.A. de C.V.</p>
            </div>
        </div>

        <div class="row mb-5 align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <img src="{{ asset('storage/img/nosotros/nosotros2.jpeg') }}" class="img-fluid rounded shadow-lg" alt="Interior de la tienda Lanceta HG">
            </div>
            <div class="col-lg-6 order-lg-1">
                <h2 class="mb-3">Concepto</h2>
                <p class="lead">Las tiendas Lanceta HG ofrecen un innovador concepto de autoservicio, permitiendo la venta directa de productos médicos, material de curación, instrumental, muebles, y más.</p>
                <p>A través de un servicio autónomo, los clientes pueden estar en contacto directo con los productos y realizar una selección informada. <strong>Con más de 5,000 productos de 400 marcas</strong>, nuestras relaciones con proveedores nos permiten ofrecer precios altamente competitivos.</p>

                <h2 class="mt-5 mb-3">Compromiso</h2>
                <p class="lead">Entendemos las necesidades del profesional de la medicina, así como de los pacientes que requieren productos de alta calidad, con precios competitivos y abastecimiento confiable. Nos comprometemos a mantener altos estándares de calidad en servicio y atención.</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-12">
                <h2 class="mb-3">Características</h2>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> Experiencia</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> Profesionalismo</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> Responsabilidad</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> Trato humano</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> Excelentes instalaciones</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> Trabajo en equipo</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> ¡Y muchas ganas de atenderle!</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
