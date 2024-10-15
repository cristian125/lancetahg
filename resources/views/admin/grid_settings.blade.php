@extends('admin.template')

@section('content')
<div class="container">
    <h2>Configuración del Contenedor 2x2</h2>

    <!-- Mostrar mensajes de éxito o error -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<!-- Formulario para subir nuevas imágenes -->
<form action="{{ route('admin.update_grid') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="grid_images" class="form-label">Subir Imágenes del Contenedor 2x2</label>
        <input class="form-control" type="file" id="grid_images" name="grid_images[]" multiple required>
    </div>
    <div class="mb-3">
        <label for="no_s" class="form-label">Número de Serie (no_s)</label>
        <input class="form-control" type="text" id="no_s" name="no_s" required>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Subir Archivo y Crear Rutas</button>
    </div>
</form>


    <!-- Mostrar las imágenes actuales del contenedor 2x2 -->
    <h3>Imágenes Actuales del Contenedor 2x2</h3>
    <div class="row">
        @foreach($gridImages as $image)
            <div class="col-md-3">
                <!-- Cambia el borde según si la imagen está activa o no -->
                <div class="image-container" style="border: 3px solid {{ $image->active ? 'green' : 'red' }};">
                    <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid" alt="Imagen del contenedor 2x2">
                </div>

                <!-- Indicador del estado de la imagen -->
                <p class="text-center mt-2">
                    <span class="badge {{ $image->active ? 'bg-success' : 'bg-danger' }}">
                        {{ $image->active ? 'Activa' : 'Inactiva' }}
                    </span>
                </p>

                <!-- Información del producto -->
                <p class="text-center">Producto ID: {{ $image->product_id }}</p>

                <!-- Botón para eliminar la imagen -->
                <button class="btn btn-danger mt-2 w-100" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $image->id }}">Eliminar</button>

                <!-- Modal de confirmación para eliminar -->
                <div class="modal fade" id="deleteModal{{ $image->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $image->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $image->id }}">Confirmar Eliminación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas eliminar esta imagen?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <form action="{{ route('admin.delete_grid_image', $image->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario para activar/desactivar imagen -->
                <form action="{{ route('admin.toggle_grid_image', $image->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button class="btn {{ $image->active ? 'btn-warning' : 'btn-success' }} mt-2 w-100" type="submit">
                        {{ $image->active ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>

<!-- Estilos adicionales -->
<style>
    .image-container {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
    }
</style>
@endsection
