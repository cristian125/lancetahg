@extends('admin.index')

@section('content')
<!-- Contenedor principal con padding -->
<div class="container-fluid py-5">
    <div class="container bg-white p-5 shadow rounded">
        <!-- Título de la página y formulario de búsqueda -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-primary">Gestión de Items</h3>

            <!-- Formulario de búsqueda -->
            <div class="d-flex">
                <form action="{{ route('admin.items.index') }}" method="GET" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar items..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>

                <!-- Botón para quitar la búsqueda, sólo si hay un filtro activo -->
                @if(request('search'))
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times"></i> Quitar búsqueda
                    </a>
                @endif
            </div>
        </div>

        <!-- Tabla responsiva de items -->
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>No S</th>
                        <th>Grupo ID</th>
                        <th>Nombre</th>
                        <th>Precio Unitario</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($items->count() > 0)
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->no_s }}</td>
                                <td>{{ $item->grupo_id ?? 'Sin grupo' }}</td>
                                <td>{{ $item->nombre }}</td>
                                <td>${{ number_format($item->precio_unitario, 2) }}</td>
                                <td>
                                    @if($item->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Botón para editar el item -->
                                    <a href="{{ route('admin.items.edit', $item->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Mostrar mensaje si no hay resultados -->
                        <tr>
                            <td colspan="7" class="text-center text-danger">No se encontraron resultados</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $items->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
<style>

    body {
        background: rgb(195,195,195);
        background: linear-gradient(90deg, rgba(195,195,195,1) 4%, rgba(227,227,227,1) 79%);
        }
    
    </style>
@endsection
