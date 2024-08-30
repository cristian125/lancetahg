<div class="container">
    <h2>Resultados para: División: {{ $criterioBusqueda }}</h2>
    <div class="grid search">
        <div class="grid-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($productos as $producto)
                                    <tr onclick="window.location='{{ url('/producto/' . $producto->id) }}';" style="cursor: pointer;">
                                        <td class="number text-center">{{ $loop->iteration }}</td>
                                        <td class="image">
                                            @php
                                                // Ruta de la carpeta del producto
                                                $carpetaProducto = 'storage/itemsview/' . $producto->no_s;
                                                // Ruta de la imagen principal
                                                $imagePath = $carpetaProducto . '/' . $producto->no_s . '.jpg';
                                                // Ruta de la imagen por defecto
                                                $defaultImagePath = 'storage/itemsview/default.jpg';
                                                // Verificar si existe la imagen en la carpeta del producto
                                                $imageToShow = file_exists(public_path($imagePath)) ? $imagePath : $defaultImagePath;
                                            @endphp
                                            <img src="{{ asset($imageToShow) }}" alt="{{ $producto->descripcion_alias }}" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                        </td>
                                        <td class="product">
                                            <strong>{{ $producto->descripcion_alias }}</strong><br>
                                            {{ $producto->descripcion }}
                                        </td>
                                        <td class="price text-right">${{ number_format($producto->precio_unitario_IVAinc,2,'.',',') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<style>
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
    .table td {
        vertical-align: middle;
    }
    .image img {
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    .number {
        font-weight: bold;
        color: #005f7f;
    }
    .product strong {
        color: #005f7f;
    }
    .price {
        font-size: 1.2em;
        color: #f39c12;
    }
</style>
