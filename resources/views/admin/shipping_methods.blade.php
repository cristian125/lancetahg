@extends('admin.index') <!-- Asegúrate de tener una plantilla de layout para administrador -->

@section('content')
<!-- Contenedor principal con fondo y espacio -->
<div class="container-fluid py-5">
    <div class="container bg-white p-5 shadow rounded">
        <!-- Título de la página -->
        <h1 class="text-center mb-4">Gestión de Métodos de Envío</h1>

        <!-- Mostrar mensaje de éxito si existe -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabla responsiva con estilo -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Método de Envío</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shippingMethods as $method)
                        <tr>
                            <td>{{ $method->display_name }}</td>
                            <td>
                                @if($method->is_active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <!-- Botón para activar/desactivar el método de envío -->
                                <button type="button" class="btn btn-sm {{ $method->is_active ? 'btn-danger' : 'btn-success' }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#confirmModal" 
                                        data-method-id="{{ $method->id }}" 
                                        data-method-status="{{ $method->is_active ? 0 : 1 }}">
                                    {{ $method->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas realizar este cambio en el método de envío?
            </div>
            <div class="modal-footer">
                <form id="confirmForm" action="{{ route('admin.shipping_methods.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="method_id" id="method-id">
                    <input type="hidden" name="is_active" id="method-status">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Al abrir el modal de confirmación, establecer los datos del método de envío
        var confirmModal = document.getElementById('confirmModal');
        confirmModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var methodId = button.getAttribute('data-method-id');
            var methodStatus = button.getAttribute('data-method-status');
            
            // Asignar los valores al formulario dentro del modal
            document.getElementById('method-id').value = methodId;
            document.getElementById('method-status').value = methodStatus;
        });
    });
</script>

<style>
    body {
        background: rgb(195,195,195);
        background: linear-gradient(90deg, rgba(195,195,195,1) 4%, rgba(227,227,227,1) 79%);
    }
</style>
@endsection
