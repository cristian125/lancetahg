@extends('admin.template')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center">Gestionar Productos Destacados</h2>

        <!-- Mostrar mensajes de éxito -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Mostrar mensajes de error -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filtros de búsqueda -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Selecciona los productos a destacar</h5>
                <div class="mb-3">
                    <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar por nombre o número de serie">
                </div>
            </div>
        </div>

        <!-- Formulario para seleccionar productos destacados -->
        <form action="{{ route('admin.destacados.guardar') }}" method="POST" id="destacadosForm">
            @csrf
            <div class="row">
                <div class="col-md-5">
                    <h5 class="text-primary">Productos Disponibles</h5>
                    <div class="form-group">
                        <select id="productosDisponibles" class="form-control" multiple style="height: 300px;">
                            @foreach($productos as $producto)
                                @if(!in_array($producto->no_s, $destacados)) <!-- Mostrar solo los no seleccionados -->
                                    <option value="{{ $producto->no_s }}">
                                        {{ $producto->no_s }} - {{ $producto->nombre }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 d-flex align-items-center justify-content-center flex-column">
                    <button type="button" id="addProduct" class="btn btn-success mb-2">&gt;&gt; Añadir</button>
                    <button type="button" id="removeProduct" class="btn btn-danger">&lt;&lt; Quitar</button>
                    <button type="button" id="moveUp" class="btn btn-secondary mt-4">&uarr; Subir</button>
                    <button type="button" id="moveDown" class="btn btn-secondary mt-2">&darr; Bajar</button>
                </div>

                <div class="col-md-5">
                    <h5 class="text-primary">Productos Seleccionados</h5>
                    <div class="form-group">
                        <select name="productos[]" id="productosSeleccionados" class="form-control" multiple style="height: 300px;">
                            @foreach($destacados as $no_s)
                                @php
                                    // Encontrar el producto en el array de productos usando el no_s
                                    $productoSeleccionado = $productos->firstWhere('no_s', $no_s);
                                @endphp
                                @if($productoSeleccionado)
                                    <option value="{{ $productoSeleccionado->no_s }}" selected>
                                        {{ $productoSeleccionado->no_s }} - {{ $productoSeleccionado->nombre }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Guardar Destacados</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productosDisponibles = document.getElementById('productosDisponibles');
            const productosSeleccionados = document.getElementById('productosSeleccionados');
            const addProductBtn = document.getElementById('addProduct');
            const removeProductBtn = document.getElementById('removeProduct');
            const moveUpBtn = document.getElementById('moveUp');
            const moveDownBtn = document.getElementById('moveDown');
            const buscarProducto = document.getElementById('buscarProducto');
            const destacadosForm = document.getElementById('destacadosForm');

            // Filtrar productos disponibles según la búsqueda
            buscarProducto.addEventListener('input', function () {
                const searchTerm = buscarProducto.value.toLowerCase();
                const options = productosDisponibles.options;

                for (let i = 0; i < options.length; i++) {
                    const optionText = options[i].text.toLowerCase();
                    options[i].style.display = optionText.includes(searchTerm) ? '' : 'none';
                }
            });

            // Función para mover productos seleccionados entre listas
            function moveSelectedOptions(sourceSelect, targetSelect) {
                const selectedOptions = Array.from(sourceSelect.selectedOptions);
                selectedOptions.forEach(option => {
                    targetSelect.appendChild(option);
                    option.selected = false;
                });
            }

            // Añadir productos seleccionados
            addProductBtn.addEventListener('click', function () {
                if (productosDisponibles.selectedOptions.length === 0) {
                    alert('Por favor selecciona al menos un producto para añadir.');
                    return;
                }
                moveSelectedOptions(productosDisponibles, productosSeleccionados);
            });

            // Quitar productos seleccionados
            removeProductBtn.addEventListener('click', function () {
                if (productosSeleccionados.selectedOptions.length === 0) {
                    alert('Por favor selecciona al menos un producto para quitar.');
                    return;
                }
                moveSelectedOptions(productosSeleccionados, productosDisponibles);
            });

            // Mover productos hacia arriba
            moveUpBtn.addEventListener('click', function () {
                const selectedOption = productosSeleccionados.selectedOptions[0];
                if (selectedOption && selectedOption.previousElementSibling) {
                    productosSeleccionados.insertBefore(selectedOption, selectedOption.previousElementSibling);
                } else {
                    alert('Selecciona un producto para mover hacia arriba.');
                }
            });

            // Mover productos hacia abajo
            moveDownBtn.addEventListener('click', function () {
                const selectedOption = productosSeleccionados.selectedOptions[0];
                if (selectedOption && selectedOption.nextElementSibling) {
                    productosSeleccionados.insertBefore(selectedOption.nextElementSibling, selectedOption);
                } else {
                    alert('Selecciona un producto para mover hacia abajo.');
                }
            });

            // Función para generar campos ocultos con el orden de los productos
            function generarCamposOrden() {
                const camposOrden = document.querySelectorAll('input[name="ordenes[]"]');
                camposOrden.forEach(campo => campo.remove());

                for (let i = 0; i < productosSeleccionados.options.length; i++) {
                    const inputOrden = document.createElement('input');
                    inputOrden.type = 'hidden';
                    inputOrden.name = 'ordenes[]';
                    inputOrden.value = i;
                    destacadosForm.appendChild(inputOrden);
                }
            }

            // Al enviar el formulario, seleccionar automáticamente todos los productos y asignar el orden
            destacadosForm.addEventListener('submit', function (e) {
                if (productosSeleccionados.options.length === 0) {
                    e.preventDefault();
                    alert('Debe seleccionar al menos un producto antes de guardar.');
                    return;
                }

                for (let i = 0; i < productosSeleccionados.options.length; i++) {
                    productosSeleccionados.options[i].selected = true;
                }

                generarCamposOrden();
            });
        });
    </script>
@endsection
