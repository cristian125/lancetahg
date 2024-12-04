@if($productos->count() > 0)
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
            @foreach($productos as $producto)
                <tr>
                    <td>{{ str_pad($producto->no_s, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>${{ number_format($producto->precio_unitario, 2) }}</td>
                    <td>
                        @if($producto->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.items.edit', $producto->id) }}" class="btn btn-sm btn-primary">Editar</a>
                        <!-- Más acciones si es necesario -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else

@endif
