@extends('admin.template')

@section('content')
<div class="container mt-5">
    <h2>Gestión de Enlaces del Footer</h2>

    <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Botón para crear un nuevo enlace -->
    <a href="{{ route('footer_links.create') }}" class="btn btn-primary mb-3">Agregar Enlace</a>

    <!-- Tabla de enlaces -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>URL</th>
                <th>Columna</th>
                <th>Posición</th>
                <th>Visible</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($footerLinks as $link)
                <tr>
                    <td>{{ $link->title }}</td>
                    <td>{{ $link->url }}</td>
                    <td>{{ $link->column_number }}</td>
                    <td>{{ $link->position }}</td>
                    <td>{{ $link->visibility ? 'Sí' : 'No' }}</td>
                    <td>
                        <a href="{{ route('footer_links.edit', $link->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('footer_links.destroy', $link->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este enlace?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
