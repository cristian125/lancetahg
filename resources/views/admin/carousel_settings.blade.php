@extends('admin.template')

@section('content')
    <div class="container">
        <h2>Configuración del Carrusel</h2>

        <!-- Mostrar mensajes de éxito o error -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Formulario para subir nuevas imágenes al carrusel -->
        <form action="{{ route('admin.update_carousel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="carousel_images" class="form-label">Subir Imágenes del Carrusel</label>
                <input class="form-control" type="file" id="carousel_images" name="carousel_images[]" multiple required>
                <div id="imagePreviewContainer" class="row mt-3"></div>
            </div>
            <div class="mb-3">
                <label for="product_link" class="form-label">Enlace del Producto Asociado (opcional)</label>
                <input class="form-control" type="text" id="product_link" name="product_link"
                    placeholder="http://enlace-del-producto.com">
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Subir Archivo y Actualizar Carrusel</button>
            </div>
        </form>

        <!-- Mostrar las imágenes actuales del carrusel -->
        <h3>Imágenes Actuales del Carrusel</h3>
        <div class="row" id="carouselImagesContainer">
            @if (isset($carouselImages) && count($carouselImages) > 0)
                @foreach ($carouselImages as $image)
                <div class="col-md-3" data-id="{{ $image->id }}">
                        <div class="image-container" style="border: 3px solid {{ $image->active ? 'green' : 'red' }};">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid"
                                alt="Imagen del carrusel">
                        </div>

                        <!-- Mostrar el enlace en un campo de texto para modificarlo -->
                        <form action="{{ route('admin.update_carousel_image_link', $image->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="product_link_{{ $image->id }}" class="form-label">Enlace del Producto</label>
                                <input class="form-control" type="text" id="product_link_{{ $image->id }}"
                                    name="product_link" value="{{ $image->product_link ?? '' }}"
                                    placeholder="Agregar/Editar enlace">
                            </div>
                            <button type="submit" class="btn btn-secondary w-100">Actualizar Enlace</button>
                        </form>

                        <!-- Indicador del estado de la imagen -->
                        <p class="text-center mt-2">
                            <span class="badge {{ $image->active ? 'bg-success' : 'bg-danger' }}">
                                {{ $image->active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </p>

                        <!-- Botón para eliminar la imagen -->
                        <button class="btn btn-danger mt-2 w-100" data-bs-toggle="modal"
                            data-bs-target="#deleteModal{{ $image->id }}">Eliminar</button>

                        <!-- Modal de confirmación para eliminar -->
                        <div class="modal fade" id="deleteModal{{ $image->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $image->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $image->id }}">Confirmar
                                            Eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estás seguro de que deseas eliminar esta imagen?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <form action="{{ route('admin.delete_carousel_image', $image->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario para activar/desactivar imagen -->
                        <form action="{{ route('admin.toggle_carousel_image', $image->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn {{ $image->active ? 'btn-warning' : 'btn-success' }} mt-2 w-100"
                                type="submit">
                                {{ $image->active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            @else
                <p>No hay imágenes en el carrusel actualmente.</p>
            @endif
        </div>
    </div>

    <!-- Estilos adicionales -->

    <script>
        $(document).ready(function() {
            $('#carousel_images').on('change', function() {
                $('#imagePreviewContainer').empty();
                var files = $(this)[0].files;
                for (var i = 0; i < files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreviewContainer').append(
                            '<div class="col-md-3 mb-3">' +
                            '<img src="' + e.target.result +
                            '" class="img-fluid preview-image" />' +
                            '</div>'
                        );
                    }
                    reader.readAsDataURL(files[i]);
                }
            });
        });
    </script>

<!-- Asegúrate de que este script está incluido después de cargar jQuery y SortableJS -->
<!-- Incluye jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluye SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>
    $(document).ready(function() {
        var sortable = new Sortable(document.getElementById('carouselImagesContainer'), {
            animation: 150,
            ghostClass: 'blue-background-class',
            onEnd: function(evt) {
                var orderedIds = [];
                $('#carouselImagesContainer .col-md-3').each(function(index, element) {
                    orderedIds.push($(element).data('id')); // Obtén el ID de la imagen
                });

                // Enviar el nuevo orden al servidor
                $.ajax({
                    url: "{{ route('admin.update_carousel_order') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        orderedIds: orderedIds
                    },
                    success: function(response) {
                        alert('Orden guardado correctamente.');
                    },
                    error: function(xhr) {
                        alert('Error al guardar el orden.');
                    }
                });
            }
        });
    });
</script>



@endsection
