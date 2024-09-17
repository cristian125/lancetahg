@extends('template')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4">Promociones</h1>
    
    <h3 class="mb-4">Bases, condiciones y restricciones de las promociones vigentes</h3>

    <!-- Ofertas del Mes -->
    <div class="mb-5">
        <h4>Ofertas del Mes - JULIO</h4>
        <div class="text-right mb-4">
            <img src="{{ asset('storage/img/vistapromociones/ofertas.jpg') }}" alt="Ofertas de Julio" class="img-fluid" style="max-width: 75%; display: block; margin-right: auto;">
        </div>
        <ul>
            <li><strong>Vigencia:</strong> Del 1 al 31 de Julio de 2024.</li>
            <li>Válido en compras realizadas en tiendas físicas, call center o tienda web.</li>
            <li>No aplica con otras promociones, programas o descuentos.</li>
            <li>Cualquier accesorio se adquiere de forma separada.</li>
            <li>Los precios se encuentran en pesos mexicanos e incluyen IVA.</li>
            <li>Estos productos están sujetos a disponibilidad en cada punto de venta y hasta agotar existencias.</li>
            <li>Puede consultar disponibilidad en el 55-5578-1958.</li>
            <li>Cambios y devoluciones sujetos a términos y condiciones publicados en lancetahg.com.mx y/o en el ticket de compra.</li>
            <li>Las imágenes, características y especificaciones pueden variar respecto al producto real.</li>
            <li>Los precios de los productos están sujetos a cambio sin previo aviso.</li>
        </ul>
    </div>

    <!-- Promociones Productos DKT -->
    <div class="mb-5">
        <h4>Promociones Productos DKT</h4>
        <div class="text-right mb-4">
            <img src="{{ asset('storage/img/vistapromociones/ofertas2.png') }}" alt="Promociones Productos DKT" class="img-fluid" style="max-width: 75%; display: block; margin-right: auto;">
        </div>
        <p><strong>Vigencia y productos participantes:</strong> Hasta agotar existencias de cada promoción.</p>
        <ul>
            <li>Easy Care 2x1 - Código: 267022. <strong>Limitado a 180 piezas.</strong></li>
            <li>T320 Standard 2x1 - Código: 267040. <strong>Limitado a 1,500 piezas.</strong></li>
            <li>Dispositivo Intrauterino T Silvercare Mini + Dispositivo Easy Care de regalo. Código: 267024. <strong>Limitado a 400 piezas.</strong></li>
            <li>Dispositivo Intrauterino T Silvercare Mini + Dispositivo Easy Care de regalo. Código: 267025. <strong>Limitado a 400 piezas.</strong></li>
            <li>Aspirador Manual Endouterino AMEU + Jeringa de regalo. Código: 267012. <strong>Limitado a 45 piezas.</strong></li>
        </ul>
        <p>Válido en compras realizadas en tiendas físicas, call center o tienda web.</p>
    </div>

    <!-- Condiciones generales -->
    <div class="mb-5">
        <h4>Condiciones generales en todas las promociones y ofertas:</h4>
        <ul>
            <li>No aplican con otras promociones, programas o descuentos.</li>
            <li>Los precios se encuentran en pesos mexicanos e incluyen IVA.</li>
            <li>Los productos están sujetos a disponibilidad en cada punto de venta y hasta agotar existencias.</li>
            <li>Puede consultar disponibilidad de los productos en promoción llamando al teléfono: 55-5578-1958.</li>
            <li>Cambios y devoluciones sujetos a términos y condiciones publicados en lancetahg.com.mx y/o en el ticket de compra.</li>
            <li>Las imágenes, características y especificaciones pueden variar respecto al producto real.</li>
            <li>Los precios de los productos están sujetos a cambio sin previo aviso.</li>
        </ul>
    </div>
</div>
@endsection
