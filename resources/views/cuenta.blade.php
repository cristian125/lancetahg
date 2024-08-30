@extends('template')
@section('header')
    <script>
        $(document).ready(function() {
            $('#addDireccion').on('click',function(){
                $('#modalAgregarDireccion').modal('show');
            });
            $('#btnCancelar').on('click', function(e) {
                e.preventDefault();
            });
            $('#btnGuardar').on('click', function(e) {
                e.preventDefault();
            });
        });
    </script>
@endsection
@section('body')
    @include('partials.modal.add-direction')
    <div class="container border rounded mt-3 p-3">
        <div class="accordion" id="accordionAccount">
            <div class="accordion-item">
                <h2 class="accordion-header" id="accDirecciones">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#Direcciones"
                        aria-expanded="true" aria-controls="Direcciones">
                        <h5 class="title text-center fw-bold"><i class="fa fa-house"></i> Direcciones</h5>
                    </button>
                </h2>
                <div id="Direcciones" class="accordion-collapse collapse show" aria-labelledby="accDirecciones"
                    data-bs-parent="#accordionAccount">
                    <div class="accordion-body p-0">
                        <div class="row m-0">
                            <nav class="navbar navbar-expand-lg bg-light">
                                <div class="container-fluid">
                                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#navDirecciones" aria-controls="navbarScroll" aria-expanded="false"
                                        aria-label="Toggle navigation">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navDirecciones">
                                        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll"
                                            style="--bs-scroll-height: 100px;">
                                            <li class="nav-item">
                                                <button id="addDireccion" class="btn btn-primary" ><i class="fa fa-plus"></i> Nueva dirección</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="row">
                            <ul class="list-group">
                                @if (count($direcciones) == 0)
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="row" style="width: 100%;">
                                            <p class="mb-1 text-center">Por el momento no tiene ninguna dirección
                                                registrada, porfavor agregue una nueva dirección.</p>
                                        </div>
                                    </li>
                                @else
                                    @foreach ($direcciones as $direccion)
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">{{ $direccion->nombre }}</div>
                                                <p class="mb-1">{{ $direccion->direccion }}</p>
                                                @if ($direccion->predeterminada == true)
                                                    <p class="mb-1 text-success fwt-italic">Predeterminada</p>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Accordion Item #2
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#accordionAccount">
                    <div class="accordion-body">
                        <strong>This is the second item's accordion body.</strong> It is hidden by default, until the
                        collapse plugin adds the appropriate classes that we use to style each element. These classes
                        control the overall appearance, as well as the showing and hiding via CSS transitions. You can
                        modify any of this with custom CSS or overriding our default variables. It's also worth noting that
                        just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit
                        overflow.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Accordion Item #3
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionAccount">
                    <div class="accordion-body">
                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the
                        collapse plugin adds the appropriate classes that we use to style each element. These classes
                        control the overall appearance, as well as the showing and hiding via CSS transitions. You can
                        modify any of this with custom CSS or overriding our default variables. It's also worth noting that
                        just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit
                        overflow.
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-3">

        </div>
        <hr />

    </div>
@endsection
