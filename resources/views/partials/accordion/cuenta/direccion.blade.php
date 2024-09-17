<div class="accordion-item">
    <h2 class="accordion-header" id="accDirecciones">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#Direcciones"
            aria-expanded="true" aria-controls="Direcciones">
            <h5 class="title text-center fw-bold"><i class="fa fa-house"></i> Direcciones</h5>
        </button>
    </h2>
    <div id="Direcciones" class="accordion-collapse collapse" aria-labelledby="accDirecciones"
        data-bs-parent="#accordionAccount">
        <div class="accordion-body">
            <div class="row p-2">
                <button id="addDireccion" class="btn btn-lanceta col-sm-2"><i class="fa fa-plus"></i> Nueva
                    dirección</button>
                {{-- 
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
                                        
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav> 
                --}}
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
                            <li id="direccion{{ $direccion->id }}" data-id="{{ $direccion->id }}"
                                class="list-group-item justify-content-between align-items-start">
                                <div class="row px-3">
                                    <div class="col-sm-12">
                                        <div class="fw-bold form-control bg-lanceta row">
                                            <div class="col-sm-8">
                                                <span><i
                                                        class="fa fa-location-dot"></i>&nbsp;&nbsp;&nbsp;{{ $direccion->nombre }}</span>
                                            </div>
                                            <div class="col-sm-4 text-right d-flex flex-row-reverse">
                                                <button class="btn btn-danger m-1 btn-delete-address"><i
                                                        class="fa fa-trash"></i></button>
                                                <button class="btn btn-warning m-1 btn-edit-address"><i
                                                        class="fa fa-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="form-control bg-light row text-lowercase">
                                            <p class="mb-1" style="text-transform:initial;">{{ $direccion->calle }}
                                                Int {{ $direccion->no_int }} Ext {{ $direccion->no_ext }},
                                                {{ $direccion->colonia }}, {{ strtolower($direccion->municipio) }},
                                                {{ $direccion->codigo_postal }},{{ $direccion->estado }},
                                                {{ $direccion->pais }}.</p>
                                            @if ($direccion->predeterminada == true)
                                                <p class="mb-1 text-success fwt-italic alert">Dirección predeterminada
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-1">

                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
