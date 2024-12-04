@extends('admin.index')

@section('content')
    <script src="https://cdn.tiny.cloud/1/wvaefm1arxme7m88dltt36jyacb4oqanhvybh3pu7972u0ok/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
    <div class="container-fluid py-5">
        <h2 class="text-center mb-4">Modificar Item</h2>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <!-- Añadimos 'enctype' para permitir la subida de archivos -->
                        <form id="itemForm" action="{{ route('admin.items.update', $item->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <!-- Sección 5: Imágenes del Producto -->
                            <h4 class="mt-4 mb-3 text-primary">Imágenes del Producto</h4>
                            <div class="row">
                                <!-- Mostrar Imagen Principal -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Imagen Principal</label>
                                    <div class="image-upload-wrapper">
                                        @if ($mainImage)
                                            <img id="mainImagePreview" src="{{ $mainImage }}" alt="Imagen Principal"
                                                class="img-fluid rounded mb-2">
                                        @else
                                            <img id="mainImagePreview" src="{{ asset('storage/itemsview/default.jpg') }}"
                                                alt="Imagen Principal" class="img-fluid rounded mb-2">
                                        @endif
                                    </div>
                                </div>

                                <!-- Mostrar Imágenes Secundarias -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Imágenes Secundarias</label>
                                    <!-- Área de drop para arrastrar y soltar imágenes -->
                                    <div id="dropArea" class="drop-area p-3 border rounded text-center"
                                        style="border: 2px dashed #007bff; cursor: pointer;">
                                        <p class="text-muted">Arrastra y suelte sus imágenes aquí o haz clic para
                                            seleccionar</p>
                                        <input type="file" name="secondary_images[]" id="secondaryImagesInput"
                                            class="d-none" multiple>
                                    </div>

                                    <div class="secondary-images-container mt-3"
                                        style="max-height: 400px; overflow-y: auto;">
                                        <div class="row" id="secondaryImagesContainer">
                                            @if (count($secondaryImages) > 0)
                                                @foreach ($secondaryImages as $image)
                                                    <div class="col-3 mb-3 position-relative secondary-image-item">
                                                        <img src="{{ $image }}" alt="Imagen Secundaria"
                                                            class="img-fluid rounded w-100 h-auto">
                                                        <!-- Botón para eliminar la imagen -->
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 eliminar-imagen"
                                                            data-image="{{ $image }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        <!-- Mostrar "Hacer Principal" solo para imágenes JPG/JPEG -->
                                                        @if (preg_match('/\.jpg|jpeg$/i', $image))
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary position-absolute bottom-0 start-0 hacer-principal"
                                                                data-image="{{ $image }}">
                                                                <i class="fas fa-star"></i> Hacer Principal
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">No hay imágenes secundarias disponibles.</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- <!-- Input para subir nuevas imágenes secundarias (fallback para navegadores que no soportan drag-and-drop) -->
                                    <div class="custom-file mt-2">
                                        <input type="file" name="secondary_images[]" class="custom-file-input"
                                            id="secondaryImagesInputFallback" multiple>
                                        <label class="custom-file-label" for="secondaryImagesInputFallback">Seleccionar
                                            imágenes</label>
                                    </div> --}}
                                </div>

                            </div>

                            <!-- Sección 1: Información Básica -->
                            <h4 class="mb-3 text-primary">Información Básica</h4>
                            <div class="mb-4">
                                <h4 class="text-primary">Gestión de Grupos</h4>
                                <p>
                                    Los grupos permiten organizar tus productos según sus características comunes. Antes de
                                    asignar atributos
                                    a un producto, asegúrate de que su grupo correspondiente exista. Si no tienes grupos
                                    creados, puedes
                                    configurarlos ahora.
                                </p>
                                <a href="{{ route('admin.grupos.create') }}" class="btn btn-success">Crear Nuevo Grupo</a>
                                <a href="{{ route('admin.grupos.index') }}" class="btn btn-secondary">Ver Grupos
                                    Existentes</a>
                            </div>

                            @if ($grupoActual)
                                <h4 class="m mb-3">Contenido de tu grupo actual:
                                    {{ $grupoActual->descripcion }}</h4>
                                @if ($otrosProductosMismoGrupo->count() > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Número de Serie</th>
                                                <th>Nombre</th>
                                                <th>Precio Unitario</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($otrosProductosMismoGrupo as $producto)
                                                <tr>
                                                    <td>{{ str_pad($producto->no_s, 6, '0', STR_PAD_LEFT) }}</td>
                                                    <td>{{ $producto->nombre }}</td>
                                                    <td>${{ number_format($producto->precio_unitario, 2) }}</td>
                                                    <td>
                                                        @if ($producto->activo)
                                                            <span class="badge bg-success">Activo</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.items.edit', $producto->id) }}"
                                                            class="btn btn-sm btn-primary">Editar</a>
                                                        <!-- Puedes añadir más acciones si es necesario -->
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-muted">No hay otros productos en este grupo.</p>
                                @endif
                            @endif
                            <!-- Contenedor para la lista de productos -->
                            <div id="productos-lista">
                                @include('admin.partials.productos_lista', ['productos' => $productos])
                            </div>

                            <div class="row">
                                <!-- Número de Serie -->
                                <div class="col-md-4 mb-3">
                                    <label for="no_s" class="form-label fw-bold">Número de Serie</label>
                                    <input type="text" id="no_s" name="no_s" class="form-control border-primary"
                                        value="{{ $item->no_s }}" required>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-4 mb-3">
                                    <label for="activo" class="form-label fw-bold">Estado</label>
                                    <select id="activo" name="activo" class="form-select border-primary" required>
                                        <option value="1" {{ $item->activo ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ !$item->activo ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="no_proveedor" class="form-label fw-bold">Número de Proveedor</label>
                                    <select id="no_proveedor" name="no_proveedor" class="form-select border-primary">
                                        <option value="">Seleccione un proveedor</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{ $proveedor->no_ }}"
                                                {{ $proveedor->no_ == $item->no_proveedor ? 'selected' : '' }}>
                                                {{ str_pad($proveedor->no_, 3, '0', STR_PAD_LEFT) }} -
                                                {{ $proveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Sección para asignar atributos -->
                                <h4 class="mt-4 mb-3 text-primary">Atributos del Producto</h4>

                                @foreach ($atributos as $grupoId => $atributosGrupo)
                                    <div class="col-md-3 form-group mb-3 ">
                                        <label
                                            class="form-label fw-bold">{{ $atributosGrupo->first()->grupo_descripcion }}</label>
                                        <div class="atributos-container border rounded p-2">
                                            @foreach ($atributosGrupo as $atributo)
                                                <div class="form-check">
                                                    <input type="checkbox" name="atributos[]" value="{{ $atributo->id }}"
                                                        id="atributo_{{ $atributo->id }}"
                                                        {{ in_array($atributo->id, $atributosProducto) ? 'checked' : '' }}
                                                        class="form-check-input atributo-checkbox">

                                                    <label for="atributo_{{ $atributo->id }}"
                                                        class="form-check-label">{{ $atributo->nombre }}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Mostrar otros productos en este grupo -->
                                        @if (isset($productosPorGrupo[$grupoId]) && $productosPorGrupo[$grupoId]->count() > 0)
                                            <h5 class="mt-3">Productos en este grupo:</h5>
                                            <table class="table table-bordered otros-productos-table">
                                                <thead>
                                                    <tr>
                                                        <th>Número de Serie</th>
                                                        <th>Nombre</th>
                                                        <th>Precio Unitario</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($productosPorGrupo[$grupoId] as $producto)
                                                        <tr>
                                                            <td>{{ str_pad($producto->no_s, 6, '0', STR_PAD_LEFT) }}</td>
                                                            <td>{{ $producto->nombre }}</td>
                                                            <td>${{ number_format($producto->precio_unitario, 2) }}</td>
                                                            <td>
                                                                @if ($producto->activo)
                                                                    <span class="badge bg-success">Activo</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactivo</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.items.edit', $producto->item_id) }}"
                                                                    class="btn btn-sm btn-primary">Editar</a>
                                                                <!-- Puedes añadir más acciones si es necesario -->
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                        @endif
                                    </div>
                                @endforeach
                                <!-- Sección 6: Otros Productos en el Mismo Grupo -->
                            </div>

                            <!-- Sección 2: Detalles del Producto -->
                            <h4 class="mt-4 mb-3 text-primary">Detalles del Producto</h4>
                            <div class="row">
                                <!-- Nombre -->
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label fw-bold">Nombre</label>
                                    <input type="text" id="nombre" name="nombre"
                                        class="form-control border-primary" value="{{ $item->nombre }}" required>
                                </div>

                                <!-- Nombre BC -->
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_bc" class="form-label fw-bold">Nombre BC</label>
                                    <input type="text" id="nombre_bc" name="nombre_bc"
                                        class="form-control border-primary" value="{{ $item->nombre_bc }}">
                                </div>

                                <!-- Descripción -->
                                <div class="col-md-12 mb-3">
                                    <label for="descripcion" class="form-label fw-bold">Descripción</label>
                                    <textarea id="descripcion" name="descripcion" class="form-control border-primary" rows="4">{{ $item->descripcion }}</textarea>
                                </div>

                                <!-- Descripción Alias -->
                                <div class="col-md-12 mb-3">
                                    <label for="descripcion_alias" class="form-label fw-bold">Descripción Alias</label>
                                    <textarea id="descripcion_alias" name="descripcion_alias" class="form-control border-primary" rows="3">{{ $item->descripcion_alias }}</textarea>
                                </div>
                            </div>

                            <!-- Sección 3: Categorías y Clasificación -->
                            <h4 class="mt-4 mb-3 text-primary">Categorías y Clasificación</h4>
                            <div class="row">
                                <!-- División -->
                                <div class="col-md-4 mb-3">
                                    <label for="division" class="form-label fw-bold">División</label>
                                    <select id="division" name="cod_division" class="form-select border-primary">
                                        <option value="">Seleccione una división</option>
                                        @foreach ($divisiones as $division)
                                            <option value="{{ $division->codigo_division }}"
                                                {{ $divisionSeleccionada && $divisionSeleccionada->codigo_division == $division->codigo_division ? 'selected' : '' }}>
                                                {{ $division->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Categoría -->
                                <div class="col-md-4 mb-3">
                                    <label for="categoria" class="form-label fw-bold">Categoría</label>
                                    <select id="categoria" name="cod_categoria_producto"
                                        class="form-select border-primary">
                                        <option value="">Seleccione una categoría</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->cod_categoria_producto }}"
                                                {{ $categoriaSeleccionada && $categoriaSeleccionada->cod_categoria_producto == $categoria->cod_categoria_producto ? 'selected' : '' }}>
                                                {{ $categoria->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Grupo Minorista -->
                                <div class="col-md-4 mb-3">
                                    <label for="grupo_minorista" class="form-label fw-bold">Grupo Minorista</label>
                                    <select id="grupo_minorista" name="codigo_de_producto_minorista"
                                        class="form-select border-primary">
                                        <option value="">Seleccione un grupo minorista</option>
                                        @foreach ($gruposMinoristas as $grupo)
                                            <option value="{{ $grupo->codigo_de_producto_minorista }}"
                                                {{ $grupoMinoristaSeleccionado && $grupoMinoristaSeleccionado->codigo_de_producto_minorista == $grupo->codigo_de_producto_minorista ? 'selected' : '' }}>
                                                {{ $grupo->numeros_serie }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>



                            </div>

                            <!-- Sección 4: Precios y Costos -->
                            <h4 class="mt-4 mb-3 text-primary">Precios y Costos</h4>
                            <div class="row">
                                <!-- Costo Unitario -->
                                <div class="col-md-4 mb-3">
                                    <label for="costo_unitario" class="form-label fw-bold">Costo Unitario MXN</label>
                                    <input type="number" step="0.01" id="costo_unitario" name="costo_unitario"
                                        class="form-control border-primary" value="{{ $item->costo_unitario }}">
                                </div>

                                <!-- Precio Unitario -->
                                <div class="col-md-4 mb-3">
                                    <label for="precio_unitario" class="form-label fw-bold">Precio Unitario MXN</label>
                                    <input type="number" step="0.01" id="precio_unitario" name="precio_unitario"
                                        class="form-control border-primary" value="{{ $item->precio_unitario }}">
                                </div>

                                <!-- Precio Unitario IVA Incluido -->
                                <div class="col-md-4 mb-3">
                                    <label for="precio_unitario_IVAinc" class="form-label fw-bold">Precio Unitario (MXN
                                        IVA Incl.)</label>
                                    <input type="number" step="0.01" id="precio_unitario_IVAinc"
                                        name="precio_unitario_IVAinc" class="form-control border-primary"
                                        value="{{ $item->precio_unitario_IVAinc }}">
                                </div>

                                <!-- Descuento -->
                                <div class="col-md-4 mb-3">
                                    <label for="descuento" class="form-label fw-bold">Descuento (%)</label>
                                    <input type="number" step="0.01" id="descuento" name="descuento"
                                        class="form-control border-primary" value="{{ $item->descuento }}">
                                </div>

                                <!-- Precio con Descuento -->
                                <div class="col-md-4 mb-3">
                                    <label for="precio_con_descuento" class="form-label fw-bold">Precio con
                                        Descuento</label>
                                    <input type="number" step="0.01" id="precio_con_descuento"
                                        name="precio_con_descuento" class="form-control border-primary"
                                        value="{{ $item->precio_con_descuento }}">
                                </div>

                                <!-- Unidad de Medida de Venta -->
                                <div class="col-md-4 mb-3">
                                    <label for="unidad_medida_venta" class="form-label fw-bold">Unidad de Medida</label>
                                    <select id="unidad_medida_venta" name="unidad_medida_venta"
                                        class="form-select border-primary">
                                        <option value="">Seleccione una unidad de medida</option>
                                        @foreach ($unidadesMedida as $unidad)
                                            <option value="{{ $unidad->codigo }}"
                                                {{ $item->unidad_medida_venta == $unidad->codigo ? 'selected' : '' }}>
                                                {{ $unidad->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <!-- Opciones de Envío -->
                            <h4 class="mt-4 mb-3 text-primary">Opciones de Envío</h4>
                            <div class="form-group">
                                <label for="allow_local_shipping" class="form-label fw-bold">Permitir Envío Local</label>
                                <input type="checkbox" id="allow_local_shipping" name="allow_local_shipping"
                                    value="1" {{ $item->allow_local_shipping ? 'checked' : '' }}>
                            </div>
                            <div class="form-group">
                                <label for="allow_paqueteria_shipping" class="form-label fw-bold">Permitir Envío por
                                    Paquetería</label>
                                <input type="checkbox" id="allow_paqueteria_shipping" name="allow_paqueteria_shipping"
                                    value="1" {{ $item->allow_paqueteria_shipping ? 'checked' : '' }}>
                            </div>
                            <div class="form-group">
                                <label for="allow_store_pickup" class="form-label fw-bold">Permitir Recoger en
                                    Tienda</label>
                                <input type="checkbox" id="allow_store_pickup" name="allow_store_pickup" value="1"
                                    {{ $item->allow_store_pickup ? 'checked' : '' }}>
                            </div>
                            <div class="form-group">
                                <label for="allow_cobrar_shipping" class="form-label fw-bold">Permitir Envío por
                                    Cobrar</label>
                                <input type="checkbox" id="allow_cobrar_shipping" name="allow_cobrar_shipping"
                                    value="1" {{ $item->allow_cobrar_shipping ? 'checked' : '' }}>
                            </div>




                            <!-- Botón de enviar -->
                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropArea = document.getElementById('dropArea');
            const inputElement = document.getElementById('secondaryImagesInput');
            const secondaryImagesContainer = document.getElementById('secondaryImagesContainer');

            // Prevenir comportamiento por defecto en eventos de arrastre
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            // Cambiar estilo al arrastrar
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.add('highlight'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.remove('highlight'), false);
            });

            // Manejar la subida de imágenes al soltar
            dropArea.addEventListener('drop', handleDrop, false);
            dropArea.addEventListener('click', () => inputElement
                .click()); // Permitir clic para seleccionar archivos

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }

            function handleFiles(files) {
                [...files].forEach(file => {
                    previewFile(file); // Previsualizar la imagen
                    uploadImage(file); // Subir la imagen automáticamente
                });
            }

            // Previsualizar imagen mientras se carga
            function previewFile(file) {
                const reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onloadend = function() {
                    const imgHtml = `
                        <div class="col-3 mb-3 position-relative secondary-image-item">
                            <img src="${reader.result}" alt="Imagen Secundaria" class="img-fluid rounded w-100 h-auto">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 eliminar-imagen">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                    secondaryImagesContainer.insertAdjacentHTML('beforeend', imgHtml);
                }
            }

            // Subir imagen al servidor automáticamente
            function uploadImage(file) {
                const formData = new FormData();
                formData.append('secondary_images[]', file);
                formData.append('item_id', '{{ $item->id }}'); // ID del producto
                formData.append('_token', '{{ csrf_token() }}'); // Token CSRF

                fetch('{{ route('subir.imagen.secundaria') }}', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar la lista de imágenes secundarias después de la subida
                            secondaryImagesContainer.innerHTML = ''; // Limpiar el contenedor
                            data.image_urls.forEach(url => {
                                const imageHtml = `
                    <div class="col-3 mb-3 position-relative secondary-image-item">
                        <img src="${url}" alt="Imagen Secundaria" class="img-fluid rounded w-100 h-auto">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 eliminar-imagen" data-image="${url}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                                secondaryImagesContainer.insertAdjacentHTML('beforeend', imageHtml);
                            });

                            // Si hay una imagen principal nueva, actualizar la vista previa
                            if (data.main_image_url) {
                                const mainImagePreview = document.getElementById('mainImagePreview');
                                mainImagePreview.src = data.main_image_url;
                            }
                        } else {
                            alert('Error al subir las imágenes.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al subir las imágenes.');
                    });
            }

            // Eliminar imagen
            $(document).on('click', '.eliminar-imagen', function() {
                const imageUrl = $(this).data('image');

                if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
                    $.ajax({
                        url: '{{ route('eliminar.imagen') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            image: imageUrl,
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Imagen eliminada correctamente.');
                                location.reload();
                            }
                        },
                        error: function() {
                            alert('Ocurrió un error al eliminar la imagen.');
                        }
                    });
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtenemos todos los campos del formulario
            const form = document.getElementById('itemForm');
            const inputs = form.querySelectorAll('input, select, textarea');
            const statusMessage = document.getElementById('status-message');

            // Añadimos un event listener a cada campo
            inputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    let formData = new FormData(form);

                    // Mostrar el icono de carga
                    statusMessage.innerHTML = '<span class="loading">Guardando...</span>';

                    // Enviar la petición AJAX
                    fetch("{{ route('admin.items.update', $item->id) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData,
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            } else if (response.status === 422) {
                                return response.json().then((errors) => {
                                    let errorMessages = Object.values(errors.errors)
                                        .flat().join('<br>');
                                    statusMessage.innerHTML =
                                        '<span class="error">Error al guardar:<br>' +
                                        errorMessages + '</span>';
                                    setTimeout(() => {
                                        statusMessage.innerHTML = '';
                                    }, 5000);
                                });
                            } else {
                                throw new Error('Error en la respuesta de la red.');
                            }
                        })
                        .then(data => {
                            if (data && data.success) {
                                statusMessage.innerHTML =
                                    '<span class="success">Guardado exitosamente</span>';
                                setTimeout(() => {
                                    statusMessage.innerHTML = '';
                                }, 3000);
                            }
                        })
                        .catch(error => {
                            statusMessage.innerHTML =
                                '<span class="error">Error al guardar</span>';
                            console.error('Error:', error);
                            setTimeout(() => {
                                statusMessage.innerHTML = '';
                            }, 5000);
                        });
                });
            });



            // Preselección al cargar la página para divisiones y categorías
            let divisionSeleccionada = '{{ $divisionSeleccionada->codigo_division ?? '' }}';
            let categoriaSeleccionada = '{{ $categoriaSeleccionada->cod_categoria_producto ?? '' }}';
            let grupoMinoristaSeleccionado =
                '{{ $grupoMinoristaSeleccionado->codigo_de_producto_minorista ?? '' }}';

            if (divisionSeleccionada) {
                cargarCategorias(divisionSeleccionada, categoriaSeleccionada, grupoMinoristaSeleccionado);
            }

            $('#division').on('change', function() {
                let divisionId = $(this).val();
                cargarCategorias(divisionId, '', '');
            });

            $('#categoria').on('change', function() {
                let categoriaId = $(this).val();
                cargarGruposMinoristas(categoriaId, '');
            });

            function cargarCategorias(divisionId, categoriaSeleccionada, grupoMinoristaSeleccionado) {
                let categoriaSelect = $('#categoria');
                let grupoMinoristaSelect = $('#grupo_minorista');

                categoriaSelect.empty().append('<option value="">Seleccione una categoría</option>');
                grupoMinoristaSelect.empty().append('<option value="">Seleccione un grupo minorista</option>').prop(
                    'disabled', true);

                if (divisionId) {
                    categoriaSelect.prop('disabled', false);
                    $.get('/get-categorias/' + divisionId, function(data) {
                        $.each(data, function(index, categoria) {
                            categoriaSelect.append('<option value="' + categoria
                                .cod_categoria_producto + '"' +
                                (categoria.cod_categoria_producto == categoriaSeleccionada ?
                                    ' selected' : '') + '>' +
                                categoria.descripcion + '</option>');
                        });
                        if (categoriaSeleccionada) {
                            cargarGruposMinoristas(categoriaSeleccionada, grupoMinoristaSeleccionado);
                        }
                    });
                } else {
                    categoriaSelect.prop('disabled', true);
                }
            }

            function cargarGruposMinoristas(categoriaId, grupoMinoristaSeleccionado) {
                let grupoMinoristaSelect = $('#grupo_minorista');
                grupoMinoristaSelect.empty().append('<option value="">Seleccione un grupo minorista</option>');

                if (categoriaId) {
                    grupoMinoristaSelect.prop('disabled', false);
                    $.get('/get-grupos-minoristas/' + categoriaId, function(data) {
                        $.each(data, function(index, grupo) {
                            grupoMinoristaSelect.append('<option value="' + grupo
                                .codigo_de_producto_minorista + '"' +
                                (grupo.codigo_de_producto_minorista ==
                                    grupoMinoristaSeleccionado ? ' selected' : '') + '>' +
                                grupo.numeros_serie + '</option>');
                        });
                    });
                } else {
                    grupoMinoristaSelect.prop('disabled', true);
                }
            }

            // Vista previa de imagen principal
            $('#mainImageInput').on('change', function() {
                let fileInput = this;
                if (fileInput.files && fileInput.files[0]) {
                    let formData = new FormData();
                    formData.append('main_image', fileInput.files[0]);
                    formData.append('item_id', '{{ $item->id }}');
                    formData.append('_token', '{{ csrf_token() }}');

                    $.ajax({
                        url: '{{ route('subir.imagen.principal') }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#mainImagePreview').attr('src', response.image_url);
                                alert('Imagen principal subida exitosamente.');
                            }
                        },
                        error: function(xhr) {
                            alert('Ocurrió un error al subir la imagen principal.');
                        }
                    });
                }
            });

            // Subida y vista previa de imágenes secundarias
            $('#secondaryImagesInput').on('change', function() {
                let formData = new FormData();
                $.each(this.files, function(i, file) {
                    formData.append('secondary_images[]', file);
                });
                formData.append('item_id', '{{ $item->id }}');
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route('subir.imagen.secundaria') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#secondaryImagesContainer').empty();
                            $.each(response.image_urls, function(i, url) {
                                const imageHtml = `
                                    <div class="col-4 mb-3 position-relative secondary-image-item">
                                        <img src="${url}" alt="Imagen Secundaria" class="img-fluid rounded">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 eliminar-imagen" data-image="${url}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>`;
                                $('#secondaryImagesContainer').append(imageHtml);
                            });
                            alert('Imágenes subidas correctamente.');
                        }
                    },
                    error: function(xhr) {
                        let response = JSON.parse(xhr.responseText);
                        alert('Error al subir las imágenes: ' + response.errors
                            .secondary_images);
                    }
                });
            });

            // Establecer imagen como principal
            $(document).on('click', '.hacer-principal', function() {
                var imageUrl = $(this).data('image');
                $.ajax({
                    url: '{{ route('hacer.imagen.principal') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        image: imageUrl,
                        item_id: '{{ $item->id }}',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#mainImagePreview').attr('src', response.image_url);
                            alert('Imagen establecida como principal.');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Ocurrió un error al establecer la imagen principal.');
                    }
                });
            });
        });
    </script>



    <style>
        body {
            background: rgb(195, 195, 195);
            background: linear-gradient(90deg, rgba(195, 195, 195, 1) 4%, rgba(227, 227, 227, 1) 79%);
        }

        .image-upload-wrapper {
            position: relative;
            overflow: hidden;
        }

        .image-upload-wrapper img {
            width: 100%;
            height: auto;
        }

        .custom-file-input:lang(es)~.custom-file-label::after {
            content: "Buscar";
        }

        .secondary-images-container {
            max-height: 500px;
            overflow-y: hidden;
        }

        .secondary-image-item {
            position: relative;
            display: inline-block;
            margin-right: 10px;
            vertical-align: top;
        }

        .secondary-image-item img {
            width: 150px;
            height: auto;
        }

        .secondary-image-item .hacer-principal {
            bottom: 10px;
            left: 10px;
            z-index: 10;
        }

        .secondary-image-item .eliminar-imagen {
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        /* Asegura que el botón de "Hacer Principal" sea accesible y visible */
        .secondary-images-container {
            overflow-x: auto;
            white-space: nowrap;
        }

        .drop-area {
            position: relative;
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .drop-area.highlight {
            background-color: #e6f7ff;
        }

        .drop-area:hover {
            cursor: copy;
        }
    </style>
    <script>
        tinymce.init({
            selector: '#descripcion', // Solo en el campo de descripción
            language: 'es', // Idioma en español
            height: 400,
            plugins: 'lists link image code',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
            branding: false,
            setup: function(editor) {
                // Asegurarse de que el contenido se guarda aunque esté vacío
                editor.on('change', function() {
                    tinymce.triggerSave();
                });
            }
        });
    </script>


    <script>
        document.getElementById('btnCrearGrupo').addEventListener('click', function() {
            let descripcionGrupo = document.getElementById('descripcion_grupo').value.trim();

            if (descripcionGrupo === '') {
                alert('Por favor, ingresa una descripción para el grupo.');
                return;
            }

            // Enviar solicitud AJAX para crear el grupo
            fetch('{{ route('admin.createGrupo') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        descripcion_grupo: descripcionGrupo,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Agregar el nuevo grupo al selector y seleccionarlo
                        let grupoSelect = document.getElementById('grupo_id');
                        let option = document.createElement('option');
                        option.value = data.grupo_id;
                        option.text = descripcionGrupo;
                        option.selected = true;
                        grupoSelect.add(option);

                        // Limpiar el campo de texto
                        document.getElementById('descripcion_grupo').value = '';
                    } else {
                        alert('Error al crear el grupo.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al crear el grupo.');
                });
        });
    </script>
    <style>
        /* Estilos existentes */

        .atributos-container {
            max-height: 200px;
            max-width: 250px;
            /* Ajusta la altura máxima según tus necesidades */
            overflow-y: auto;
            /* Habilita el scroll vertical */
            padding: 10px;
            background-color: #f8f9fa;
            /* Color de fondo opcional */
        }

        .atributos-container .form-check {
            margin-bottom: 5px;
        }

        /* Estilos existentes */
    </style>
    <style>
        /* Estilos existentes */

        .atributos-container {
            max-height: 200px;
            /* Ajusta la altura máxima según tus necesidades */
            overflow-y: auto;
            /* Habilita el scroll vertical */
            padding: 10px;
            background-color: #f8f9fa;
            /* Color de fondo opcional */
        }

        .atributos-container .form-check {
            margin-bottom: 5px;
        }

        /* Estilos para la nueva tabla de otros productos */
        .otros-productos-table th,
        .otros-productos-table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.9em;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para obtener los IDs de los atributos seleccionados
            function getSelectedAtributos() {
                let selected = [];
                document.querySelectorAll('.atributo-checkbox:checked').forEach(function(checkbox) {
                    selected.push(checkbox.value);
                });
                return selected;
            }

            // Función para actualizar la lista de productos
            function updateProductosList() {
                let atributosSeleccionados = getSelectedAtributos();

                // Enviar solicitud AJAX al servidor
                fetch('{{ route('admin.obtenerProductosPorAtributos') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            atributos: atributosSeleccionados,
                            producto_id_actual: '{{ $item->id }}', // Para excluir el producto actual
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Actualizar el contenedor con la nueva lista de productos
                        document.getElementById('productos-lista').innerHTML = data.html;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            // Añadir event listeners a los checkboxes de atributos
            document.querySelectorAll('.atributo-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateProductosList();
                });
            });

            // Cargar la lista de productos inicialmente
            updateProductosList();
        });
    </script>

@endsection
