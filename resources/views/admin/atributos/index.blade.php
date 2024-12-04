@extends('admin.index')

@section('content')
<div class="container">
    <h2>Atributos</h2>
    <p class="mb-4">
        Los atributos son características específicas que se asignan a los productos dentro de los grupos. 
        Si aún no has creado grupos, dirígete a la sección de 
        <a href="{{ route('admin.grupos.index') }}">gestión de grupos</a> 
        para configurarlos antes de agregar atributos.
    </p>
    <a href="{{ route('admin.atributos.create') }}" class="btn btn-primary mb-3">Crear Nuevo Atributo</a>

    <!-- Buscador -->
    <form method="GET" action="{{ route('admin.atributos.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar atributos..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($atributos->isEmpty())
        <p>No hay atributos registrados.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Grupo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($atributos as $atributo)
                <tr>
                    <td>{{ $atributo->id }}</td>
                    <td>{{ $atributo->nombre }}</td>
                    <td>{{ $atributo->grupo_descripcion }}</td>
                    <td>
                        <a href="{{ route('admin.atributos.edit', $atributo->id) }}" class="btn btn-sm btn-warning">Editar</a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        {{ $atributos->links() }}
    @endif

    <div class="mt-3">
        <a href="{{ route('admin.grupos.index') }}" class="btn btn-secondary">Ir a Grupos</a>
    </div>
</div>
@endsection
