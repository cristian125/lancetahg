@extends('template')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4">Proveedores</h1>
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6">
            
            <img src="{{ asset('storage/img/proveedores/proveedores.jpg') }}" alt="Proveedores" class="img-fluid">
        </div>
        <div class="col-md-6">
            <p>
                Nuestros proveedores son parte fundamental de nuestra marca, ya que con ellos buscamos siempre actualizar y mejorar el abanico de productos que ofrecemos hacia nuestros clientes.
            </p>
            <h3>Ya es proveedor</h3>
            <p>
                Formato para someter a revisión facturas de pedidos solicitados a nuestros proveedores
            </p>
            <a href="{{ asset('storage/pdf/contra_recibo.pdf') }}" class="btn btn-success mb-3">Contra Recibo para proveedores</a>
            <h3>Quiere ser proveedor</h3>
            <p>
                Mandar una carta de presentación y catálogo de productos al correo: 
                <a href="mailto:gerencia_compras@lancetahg.com">gerencia_compras@lancetahg.com</a>, 
                o también puede llamarnos para concertar una cita a nuestro teléfono 55-5578-1959.
            </p>
        </div>
    </div>
</div>
@endsection
