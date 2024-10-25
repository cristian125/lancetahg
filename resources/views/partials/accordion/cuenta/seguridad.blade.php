<div class="accordion-item" id="seguridad">
    <h2 class="accordion-header" id="accSeguridad">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#colSeguridad"
            aria-expanded="true" aria-controls="colSeguridad">
            <h5 class="title text-center fw-bold"><i class="fa fa-user-shield"></i> Seguridad</h5>
        </button>
    </h2>
    <div id="colSeguridad" class="accordion-collapse collapse" aria-labelledby="accSeguridad"
        data-bs-parent="#accordionAccount">
        <div class="accordion-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="frmSeguridad" action="{{ route('cuenta.contraseña.actualizar') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <label for="current_password" class="input-group-text">Contraseña Actual:</label>
                                    <input type="password" name="current_password" id="current_password" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">                               
                                <div class="input-group">
                                    @error('new_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <label for="new_password" class="input-group-text">Nueva Contraseña:</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <label for="new_password_confirmation" class="input-group-text">Confirmar Nueva Contraseña:</label>
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <button class="btn btn-success form-control">
                                        <i class="fa fa-floppy-disk"></i>
                                        Guardar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
