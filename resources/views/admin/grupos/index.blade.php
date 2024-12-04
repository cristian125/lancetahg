@extends('admin.index')

@section('content')
<div class="container">
    <h2>Grupos de Productos</h2>
    <p class="mb-4">
        Antes de asignar atributos a los productos, es necesario crear grupos que los organicen. 
        Una vez que hayas creado los grupos, puedes ir a la sección de 
        <a href="{{ route('admin.atributos.index') }}">gestión de atributos</a> 
        para asignarles características específicas.
    </p>
    <a href="{{ route('admin.grupos.create') }}" class="btn btn-primary mb-3">Crear Nuevo Grupo</a>

    <!-- Buscador -->
    <form method="GET" action="{{ route('admin.grupos.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar grupos..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($grupos->isEmpty())
        <p>No hay grupos registrados.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupos as $grupo)
                <tr>
                    <td>{{ $grupo->id }}</td>
                    <td>{{ $grupo->descripcion }}</td>
                    <td>
                        <a href="{{ route('admin.grupos.edit', $grupo->id) }}" class="btn btn-sm btn-warning">Editar</a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        {{ $grupos->links() }}
    @endif

    <div class="mt-3">
        <a href="{{ route('admin.atributos.index') }}" class="btn btn-secondary">Ir a Atributos</a>
    </div>
</div>
@endsection
