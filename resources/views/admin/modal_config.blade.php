@extends('admin.template')

@section('content')
    <div class="container mt-5">
        <h2>Configuración del Modal</h2>

        <!-- Mostrar el modal si está activo -->
        @if($modalConfig->is_active)
            <div class="alert alert-info">
                El modal está activo.
            </div>
        @else
            <div class="alert alert-warning">
                El modal está inactivo.
            </div>
        @endif

        <!-- Mostrar la imagen si existe -->
        @if($modalConfig->image_url)
            <div class="text-center mb-4">
                <!-- Ajustar tamaño de la imagen -->
                <img src="{{ asset('storage/' . $modalConfig->image_url) }}" alt="Imagen del modal" 
                     style="max-width: 50%; height: auto; object-fit: contain;">
            </div>
        @else
            <p>No hay imagen configurada para el modal.</p>
        @endif

        <!-- Formulario para subir una nueva imagen o activar/desactivar el modal -->
        <form action="{{ route('admin.modal_config.save') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="image">Seleccionar nueva imagen:</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>

            <div class="form-group mt-3">
                <label for="is_active">¿Activar el modal?</label>
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $modalConfig->is_active ? 'checked' : '' }}>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Guardar configuración</button>
        </form>
    </div>
@endsection
