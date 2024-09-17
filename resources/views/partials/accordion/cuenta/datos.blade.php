<div class="accordion-item">
    <h2 class="accordion-header" id="accDatos">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#colDatos"
            aria-expanded="true" aria-controls="colDatos">
            <h5 class="title text-center fw-bold"><i class="fa fa-user"></i> Datos Personales</h5>
        </button>
    </h2>
    <div id="colDatos" class="accordion-collapse collapse" aria-labelledby="accDatos"
        data-bs-parent="#accordionAccount">
        <div class="accordion-body">
            <div class="row">
                <form id="frmDatos" action="#" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <label for="tratamiento" class="input-group-text"><i
                                        class="bi bi-question-octagon-fill"></i> &nbsp;&nbsp;¿Como desea que nos
                                    dirijamos a
                                    usted?:</label>
                                <select name="tratamiento" id="tratamiento" class="form-control">
                                    <option value=""></option>
                                    <option value="NA">Ninguno</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="Sra.">Sra.</option>
                                    <option value="Dr.">Dr.</option>
                                    <option value="Dra.">Dra.</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group">
                                <label for="nombre" class="input-group-text"><i
                                        class="fa fa-user"></i>&nbsp;&nbsp;Nombre:</label>
                                <input type="text" id="nombre" name="nombre" class="form-control"
                                    placeholder="Nombres" value="{{ Auth::user()->name }}" required>
                                {{-- </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group"> --}}
                                {{-- <label for="apellido_paterno" class="input-group-text">Apellido Paterno:</label> --}}
                                <input type="text" id="apellido_paterno" name="apellido_paterno"
                                    placeholder="Apellido Paterno" class="form-control" required>
                                {{-- </div> --}}
                                {{-- </div> --}}
                                {{-- <div class="col-md-3"> --}}
                                {{-- <div class="input-group"> --}}
                                {{-- <label for="apellido_materno" class="input-group-text">Apellido Materno:</label> --}}
                                <input type="text" id="apellido_materno" name="apellido_materno"
                                    placeholder="Apellido Materno" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="email" class="input-group-text"><i class="fa fa-envelope"></i>&nbsp;&nbsp;Correo</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="{{ Auth::user()->email }}" placeholder="ej: correo@gmail.com" disabled="disabled">
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="telefono" class="input-group-text"><i class="fa fa-mobile"></i>&nbsp;&nbsp;Telefono</label>
                            <input type="tel" id="telefono" name="telefono" pattern="\d{10}" placeholder="Numero Telefonico (10 digitos)" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="row">
                        &nbsp;
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <button type="submit" class="btn btn-success form-control"> <i
                                    class="fa fa-floppy-disk"></i>
                                Enviar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
