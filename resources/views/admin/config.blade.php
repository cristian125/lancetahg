@extends('admin.template')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Estado de Mantenimiento del Sitio
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif


                    <div class="alert {{ $mantenimiento == 'true' ? 'alert-warning' : 'alert-info' }}">
                        <strong>Estado Actual:</strong> 
                        El sitio web está actualmente 
                        <strong>{{ $mantenimiento == 'true' ? 'EN MANTENIMIENTO' : 'ACTIVO' }}</strong>.
                    </div>

                    <form id="mantenimientoForm" action="{{ route('configadmin') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="mantenimiento">Cambiar Estado de Mantenimiento:</label>
                            <select name="mantenimiento" id="mantenimiento" class="form-control">
                                <option value="true" {{ $mantenimiento == 'true' ? 'selected' : '' }}>Activar Mantenimiento</option>
                                <option value="false" {{ $mantenimiento == 'false' ? 'selected' : '' }}>Desactivar Mantenimiento</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onclick="confirmarCambio()">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Cambio de Mantenimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Estás a punto de <span id="modalAction"></span> el sitio web.
                <p><strong>Estado actual:</strong> El sitio está actualmente {{ $mantenimiento == 'true' ? 'EN MANTENIMIENTO' : 'ACTIVO' }}.</p>
                ¿Estás seguro de que deseas proceder?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarCambio() {
        const mantenimiento = document.getElementById('mantenimiento').value;
        const actionText = (mantenimiento === 'true') ? 'activar el modo mantenimiento' : 'desactivar el modo mantenimiento';
        document.getElementById('modalAction').textContent = actionText;
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }

    function submitForm() {
        document.getElementById('mantenimientoForm').submit();
    }
</script>
@endsection
