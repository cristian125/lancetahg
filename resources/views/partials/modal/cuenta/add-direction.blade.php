<div id="modalAgregarDireccion" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-lanceta text-white" style="width:100%;">
                <h5 class="modal-title"><i class="fa fa-plus"></i> Agregar Dirección</h5>
                <button class="btn btn-danger close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmAgregarDireccion" action="{{ route('cuenta.direccion.agregar') }}" class="table"
                    method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <label for="nombre" class="input-group-text">Nombre: &nbsp;<i
                                        class="bi bi-info-circle"></i></label>
                                <input type="text" name="nombre" id="nombre" class="form-control"
                                    required="required" placeholder="Ej: Casa, Hospital, Clinica, Consultorio" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="calle" class="input-group-text">Calle:</label>
                                <input type="text" name="calle" id="calle" class="form-control"
                                    required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <label for="int" class="input-group-text">No Int: </label>
                                <input type="text" name="int" id="int" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <label for="ext" class="input-group-text">No Ext: </label>
                                <input type="text" name="ext" id="ext" class="form-control"
                                    required="required" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="colonia" class="input-group-text ">Colonia: </label>
                                <input type="text" name="colonia" id="colonia" class="form-control"
                                    required="required" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="entrecalles" class="input-group-text">Entre Calles: </label>
                                <input type="text" name="entrecalles" id="entrecalles" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="codigopostal" class="input-group-text">Código Postal: </label>
                                <input type="number" name="codigopostal" id="codigopostal" class="form-control"
                                    pattern="^\d{5}$" required="required" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="delegacion" class="input-group-text disabled">Municipio: </label>
                                <select name="delegacion" id="delegacion" class="form-control"
                                    required="required"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="estado" class="input-group-text disabled">Estado: </label>
                                <select name="estado" id="estado" class="form-control"
                                    required="required"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="pais" class="input-group-text disabled">País: </label>
                                <select name="pais" id="pais" class="form-control" required="required">
                                    <option value="MEX">México</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <label for="referencias" class="input-group-text">Referencias:</label>
                                <textarea name="referencias" id="referencias" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button id="btnCancelar" class="btn btn-danger form-control bg-danger text-white"
                                data-bs-dismiss="modal" aria-label="Close">
                                <i class="bi bi-x-octagon-fill"></i> Cancelar
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button id="btnGuardar" class="btn btn-lanceta form-control bg-primary text-white">
                                <i class="bi bi-floppy-fill"></i> Guardar
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#modalAgregarDireccion').on('hidden.bs.modal', function() {
            console.log('here');
        });
    });
</script>
