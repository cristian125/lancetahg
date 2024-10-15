@extends('admin.template')

@section('content')
<div class="container">
    <h2>Configuración del Banner</h2>

    <!-- Mostrar mensajes de éxito o error -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Formulario para subir una nueva imagen de banner -->
    <form action="{{ route('admin.upload_banner') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="banner_image" class="form-label">Subir Imagen del Banner</label>
            <input class="form-control" type="file" id="banner_image" name="banner_image" required>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Subir Imagen</button>
        </div>
    </form>

    <!-- Mostrar la imagen actualmente activa -->
    <h3>Imagen Activa del Banner</h3>
    @if ($bannerImage)
        <div class="text-center">
            <img src="{{ asset('storage/' . $bannerImage->image_path) }}" alt="Banner Activo" class="img-fluid">
        </div>
    @else
        <p>No hay una imagen activa en el banner.</p>
    @endif

    <!-- Lista de todas las imágenes del banner -->
    <h3>Gestionar Imágenes del Banner</h3>
    <div class="row">
        @foreach ($allImages as $image)
            <div class="col-md-3">
                <div class="image-container" style="border: 3px solid {{ $image->active ? 'green' : 'red' }};">
                    <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid" alt="Imagen del Banner">
                </div>

                <!-- Indicador del estado de la imagen -->
                <p class="text-center mt-2">
                    <span class="badge {{ $image->active ? 'bg-success' : 'bg-danger' }}">
                        {{ $image->active ? 'Activa' : 'Inactiva' }}
                    </span>
                </p>

                <!-- Botón para activar/desactivar la imagen -->
                <form action="{{ route('admin.toggle_banner', $image->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button class="btn {{ $image->active ? 'btn-warning' : 'btn-success' }} w-100" type="submit">
                        {{ $image->active ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>

                <!-- Botón para eliminar la imagen -->
                <form action="{{ route('admin.delete_banner', $image->id) }}" method="POST" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger w-100" type="submit">Eliminar</button>
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
