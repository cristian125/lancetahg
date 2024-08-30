@extends('template')

@section('body')
<div class="d-flex justify-content-center align-items-start" style="min-height: 100vh; padding-top: 50px;">
    <div class="container bg-white p-4 w-50 rounded shadow" style="max-width: 1000px;">
        <p class="text-center mb-4">A partir de la confirmación de su pago comenzará el procesamiento de su orden. Posteriormente, y sólo entonces, comenzará a correr el tiempo de envío y entrega del paquete.</p>

        <!-- Botones de navegación -->
        <ul class="nav nav-tabs justify-content-center" id="envioTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tienda-tab" data-bs-toggle="tab" data-bs-target="#tienda" type="button" role="tab" aria-controls="tienda" aria-selected="true">
                    <img src="/img/s/254.png" alt="" />
                    <p><span>Recoger</span> <span>en Tienda</span></p>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="local-tab" data-bs-toggle="tab" data-bs-target="#local" type="button" role="tab" aria-controls="local" aria-selected="false">
                    <img src="/img/s/265.png" alt="" />
                    <p><span>Envío</span> <span>Local</span></p>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cobrar-tab" data-bs-toggle="tab" data-bs-target="#cobrar" type="button" role="tab" aria-controls="cobrar" aria-selected="false">
                    <img src="/img/s/266.jpg" alt="" />
                    <p><span>Envío</span> <span>Por Cobrar</span></p>
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content mt-3" id="envioTabsContent">
            <!-- Recoger en Tienda -->
            <div class="tab-pane fade show active" id="tienda" role="tabpanel" aria-labelledby="tienda-tab">
                <div class="row">
                    <div class="col-md-6">
                        <img src="/storage/img/envio_entrega/mapa1.png" alt="" class="img-fluid"/>
                        <h4 class="mt-3"><i class="lnr lnr-chevron-right"></i> TIEMPO DE ENTREGA</h4>
                        <p><i class="icon-circle dosdias"></i> A partir de 3 horas después de haberse registrado el pedido.<br/>
                        <em>(Solo DÍAS LABORALES; lunes a sábado y dentro del horario de entrega).</em></p>
                    </div>
                    <div class="col-md-6">
                        <h4><i class="lnr lnr-chevron-right"></i> HORARIO DE ENTREGA*</h4>
                        <p>Lunes a viernes de 10 a 18 hr.<br />Sábados de 10 a 15 hr.</p>
                        <h4><i class="lnr lnr-chevron-right"></i> ¿DÓNDE RECOGER MI PEDIDO?</h4>
                        <p>Opción habilitada sólo para recoger en la <strong>Sucursal Hospital General</strong> en la Ciudad de México. <a href="/sucursales" title="Ver ubicación de la sucursal Hospital General" target="_blank">Ver ubicación.</a></p>
                        <h4><i class="lnr lnr-chevron-right"></i> ¿TIENE COSTO?</h4>
                        <p>Esta modalidad de entrega no tiene costo alguno.</p>
                    </div>
                </div>
                <div class="content-notas mt-3">
                    <ol class="asterisco">
                        <li>En el caso de que usted haya realizado la compra después de las 18:00 hr, considere que su pedido estará listo a partir de las 10:00 hr del día siguiente.</li>
                    </ol>
                </div>
            </div>

            <!-- Envío Local -->
            <div class="tab-pane fade" id="local" role="tabpanel" aria-labelledby="local-tab">
                <div class="row">
                    <div class="col-md-6">
                        <img src="/storage/img/envio_entrega/mapa2.png" alt="" class="img-fluid"/>
                        <h4 class="mt-3"><i class="lnr lnr-chevron-right"></i> TIEMPO DE ENTREGA</h4>
                        <p><i class="icon-circle dosdias"></i> De 1 a 2 días hábiles</p>
                    </div>
                    <div class="col-md-6">
                        <h4><i class="lnr lnr-chevron-right"></i> HORARIO DE ENTREGA*</h4>
                        <p>Horario abierto, entre 10 y 18 hr.</p>
                        <h4><i class="lnr lnr-chevron-right"></i> ¿TIENE COSTO?</h4>
                        <p>Sí, sólo cuando cumpla con el mínimo de compra para cada zona establecida. <a href="{{ route('coberturaloc') }}">Ver Cobertura Local</a></p>
                    </div>
                </div>
                <div class="content-notas mt-3">
                    <ol class="asterisco">
                        <li>No podemos garantizar una hora exacta de entrega, por lo que el cliente deberá estar al pendiente.</li>
                        <li>En este tipo de envío se realizará un primer intento de entrega y si el cliente no se encuentra para recibir el pedido, se reprogramará hasta el siguiente día que la unidad pase por la misma zona. Si el cliente no vuelve a encontrarse en el domicilio en el segundo intento de entrega, el paquete se regresará a la tienda de Hospital General donde el cliente tendrá que pasar a recogerlo o bien, si el cliente aún quiere recibir el paquete en su domicilio, deberá pagar el costo de envío correspondiente. <a class="link-fancy" href="/cobertura-envio-local-42?content_only=1" target="_blank" style="color: #ce4d16;">Ver tabla de cobertura-costos.</a></li>
                    </ol>
                </div>
            </div>

            <!-- Envío por Cobrar -->
            <div class="tab-pane fade" id="cobrar" role="tabpanel" aria-labelledby="cobrar-tab">
                <div class="row">
                    <div class="col-md-6">
                        <img src="/storage/img/envio_entrega/mapa3.png" alt="" class="img-fluid"/>
                        <h3 class="mt-3 text-center" style="color: #03587c; font-weight: bold;">Todos los envíos que realizamos al interior de la república en nuestra tienda web son operados por <span style="color: #bd2421;">empresas de mensajería externas.</span></h3>
                        <h4 class="mt-3"><i class="lnr lnr-chevron-right"></i> TIEMPO DE ENTREGA*</h4>
                        <p><i class="icon-circle dosdias"></i> Según el establecido por el transportista.</p>
                    </div>
                    <div class="col-md-6">
                        <h4 class="mt-3"><i class="lnr lnr-chevron-right"></i> HORARIO DE ENTREGA*</h4>
                        <p>Según el establecido por el transportista.</p>
                        <h4 class="mt-3"><i class="lnr lnr-chevron-right"></i> ¿TIENE COSTO?</h4>
                        <p>Sí. Según el que establezca el transportista.</p>
                        <h4 class="mt-3"><i class="lnr lnr-chevron-right"></i> CONDICIONES IMPORTANTES</h4>
                        <p>¡MUY IMPORTANTE! Al aceptar este tipo de envío, estará usted aceptando el tiempo, costo y condiciones que la empresa transportista manejará para el envío de su paquete, y por tal motivo, no se aceptará cancelación o devolución del pedido.</p>
                        <p>Válido únicamente en envíos nacionales.</p>
                    </div>
                </div>
                <div class="content-notas mt-3">
                    <ol class="asterisco">
                        <li>El tiempo de entrega sólo puede saberse hasta el momento de generar la guía con el transportista.</li>
                        <li>No podemos garantizar una hora exacta de entrega, debido a que la empresa de mensajería gestiona sus propias rutas.</li>
                        <li>El costo de envío sólo se sabrá hasta el momento de que se genere la guía de su paquete. Un ejecutivo de venta le compartirá la guía por correo electrónico para que rastree su paquete y liquide con el transportista el costo generado por el envío.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div id="legend" class="mt-4 text-center">
            <p><strong>La información aquí expresada es exclusiva para las compras realizadas en línea dentro de nuestra Tienda Web.</strong></p>
            <p>Para mayor información, puede consultar nuestros Términos y Condiciones, en el apartado <a href="{{ route('terminosyc') }}" class="text-primary">“Políticas Generales de Envío“</a></p>
        </div>
    </div>
</div>
@endsection
