@extends('admin.index')

@section('content')
<div class="container-fluid py-5">
    <div class="container bg-white p-5 shadow rounded">

        <!-- Mensaje de éxito (si existe) -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Mensajes de error de validación (si los hay) -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-primary">Gestión de Items</h3>

            <div class="d-flex">
                {{-- Formulario de búsqueda (ya existente) --}}
                <form action="{{ route('admin.items.index') }}" method="GET" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar items..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>

                @if(request('search'))
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times"></i> Quitar búsqueda
                    </a>
                @endif
            </div>
        </div>

        <!-- NUEVO: Formulario para actualizar order_number_sequence -->
        <div class="mb-4">
            <form action="{{ route('admin.orderSequence.update') }}" method="POST" class="d-flex align-items-center">
                @csrf
                <label for="order_number_sequence" class="me-2">Secuencia de Orden:</label>
                <input type="number" name="order_number_sequence" id="order_number_sequence"
                       class="form-control me-2" style="width:120px;"
                       value="{{ old('order_number_sequence', $currentOrderSeq) }}">
                <button type="submit" class="btn btn-primary">
                    Actualizar
                </button>
            </form>
        </div>

        <!-- Resto de tu tabla de Items -->
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>No S</th>
                        <th>Grupos</th>
                        <th>Nombre</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad Disponible</th>
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
                                <td>
                                    {{ $item->grupos_ids ? str_replace(',', ', ', $item->grupos_ids) : 'Sin Grupo' }}
                                </td>
                                <td>{{ $item->nombre }}</td>
                                <td>${{ number_format($item->precio_unitario, 2) }}</td>
                                <td>{{ $item->cantidad_disponible ?? 0 }}</td>
                                <td>
                                    @if($item->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.items.edit', $item->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center text-danger">No se encontraron resultados</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
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
