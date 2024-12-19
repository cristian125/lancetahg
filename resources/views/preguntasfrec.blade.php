@extends('template')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4">Preguntas Frecuentes</h1>

    <ul class="nav nav-tabs mb-4" id="faqTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="lancetaHG-tab" data-bs-toggle="tab" data-bs-target="#lancetaHG" type="button" role="tab" aria-controls="lancetaHG" aria-selected="true">LancetaHG</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="registro-tab" data-bs-toggle="tab" data-bs-target="#registro" type="button" role="tab" aria-controls="registro" aria-selected="false">Registro / Cuenta</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="compra-tab" data-bs-toggle="tab" data-bs-target="#compra" type="button" role="tab" aria-controls="compra" aria-selected="false">Compra</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="envio-tab" data-bs-toggle="tab" data-bs-target="#envio" type="button" role="tab" aria-controls="envio" aria-selected="false">Envío</button>
        </li>
    </ul>

    <div class="tab-content" id="faqTabsContent">

        <div class="tab-pane fade show active" id="lancetaHG" role="tabpanel" aria-labelledby="lancetaHG-tab">
            <div class="accordion" id="accordionLancetaHG">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            ¿Es seguro comprar en Lanceta HG?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionLancetaHG">
                        <div class="accordion-body">
                            Si, comprar en Lanceta HG es completamente seguro, contamos con un certificado SSL de 256 bits para garantizar la confidencialidad de la información durante todo el proceso de compra, además contamos con el sello de confianza otorgado por la Asociación Mexicana de Internet. Para mayor información visite nuestra sección “Seguridad”.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Tengo muchas dudas ¿Cómo puedo contactarlos?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionLancetaHG">
                        <div class="accordion-body">
                            En Lanceta HG le apoyamos en todo momento durante el proceso de compra, rastreo o cambio de su pedido. Por favor llámenos al (55) 5578.1958 en cualquier punto de la república.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="registro" role="tabpanel" aria-labelledby="registro-tab">
            <div class="accordion" id="accordionRegistro">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            ¿Cómo me registro?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionRegistro">
                        <div class="accordion-body">
                            Registrarse es muy sencillo, es gratis y le tomará menos de dos minutos, tan solo de clic en “Crear cuenta” en el menú Iniciar sesión y llene el formulario.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            ¿Cómo cambio la dirección de correo electrónico de mi cuenta?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionRegistro">
                        <div class="accordion-body">
                            Es muy sencillo. Solo ingrese a su cuenta en el apartado de “Datos Personales”, ingrese el nuevo correo, escriba su contraseña actual y listo. Ver guía gráfica <a href="#">aquí</a>.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Olvidé mi contraseña, ¿ahora qué hago?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionRegistro">
                        <div class="accordion-body">
                            No se preocupe, tan solo entre en la página: <a href="#">www.lancetahg.com.mx/password</a> y proporcione el email registrado en su cuenta, le enviaremos un correo electrónico que le ayudará a restablecer su contraseña. Ver guía gráfica <a href="#">aquí</a>.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            ¿Qué es el Newsletter?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionRegistro">
                        <div class="accordion-body">
                            El Newsletter es un servicio gratuito con el cual recibirá artículos, noticias, productos nuevos, promociones y descuentos exclusivos directamente en su correo electrónico, manténgase informado en todo momento de todo lo que sucede en Lanceta HG.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            ¿Cómo me doy de baja del Newsletter?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#accordionRegistro">
                        <div class="accordion-body">
                            Puede cancelar su suscripción a nuestro Newsletter en el momento que lo desee, tan solo busque el enlace “Cancelar suscripción” en cualquiera de nuestros correos.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compra Tab -->
        <div class="tab-pane fade" id="compra" role="tabpanel" aria-labelledby="compra-tab">
            <div class="accordion" id="accordionCompra">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                            ¿Cómo realizo una compra en Lanceta HG?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive" data-bs-parent="#accordionCompra">
                        <div class="accordion-body">
                            Texto de relleno para la respuesta. Aquí puedes describir cómo realizar una compra.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            ¿Qué métodos de pago aceptan?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#accordionCompra">
                        <div class="accordion-body">
                            Texto de relleno para la respuesta. Aquí puedes describir los métodos de pago aceptados.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="envio" role="tabpanel" aria-labelledby="envio-tab">
            <div class="accordion" id="accordionEnvio">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEnvioOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEnvioOne" aria-expanded="false" aria-controls="collapseEnvioOne">
                            ¿Qué métodos de envío para entrega a domicilio manejan?
                        </button>
                    </h2>
                    <div id="collapseEnvioOne" class="accordion-collapse collapse" aria-labelledby="headingEnvioOne" data-bs-parent="#accordionEnvio">
                        <div class="accordion-body">
                            Ofrecemos diferentes opciones de envío a domicilio que incluyen paqueterías locales y nacionales. Las opciones específicas pueden variar según la región y el tipo de producto que está comprando.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEnvioTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEnvioTwo" aria-expanded="false" aria-controls="collapseEnvioTwo">
                            ¿Cuánto cuesta el envío de los productos a domicilio?
                        </button>
                    </h2>
                    <div id="collapseEnvioTwo" class="accordion-collapse collapse" aria-labelledby="headingEnvioTwo" data-bs-parent="#accordionEnvio">
                        <div class="accordion-body">
                            El costo del envío depende del método de envío elegido, el destino de entrega y el peso facturable del paquete. El costo total se calculará durante el proceso de pago.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEnvioThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEnvioThree" aria-expanded="false" aria-controls="collapseEnvioThree">
                            ¿Puedo comprar en línea y recoger en sucursal?
                        </button>
                    </h2>
                    <div id="collapseEnvioThree" class="accordion-collapse collapse" aria-labelledby="headingEnvioThree" data-bs-parent="#accordionEnvio">
                        <div class="accordion-body">
                            Sí, tenemos la modalidad de "Recoger en Tienda", donde usted puede comprar y a partir del día siguiente pasar por su pedido en la(s) sucursal(es) disponible(s) para esta modalidad.
                            <br><strong>Sucursal Hospital General:</strong> Dr. Villada #81 Col. Doctores, 06720, Cuauhtémoc, CDMX. Tel. 5578.1958. De clic aquí para ver su ubicación.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEnvioFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEnvioFour" aria-expanded="false" aria-controls="collapseEnvioFour">
                            ¿Cuál es la cobertura del servicio y el tiempo de entrega?
                        </button>
                    </h2>
                    <div id="collapseEnvioFour" class="accordion-collapse collapse" aria-labelledby="headingEnvioFour" data-bs-parent="#accordionEnvio">
                        <div class="accordion-body">
                            Ofrecemos servicio de entrega en toda la República Mexicana. El tiempo de entrega varía según la región y el método de envío elegido, generalmente oscila entre 1 y 7 días hábiles.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection