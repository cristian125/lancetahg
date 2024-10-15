@extends('admin.template')

@section('content')
<div class="container mt-5">
    <h2>Agregar Nuevo Enlace al Footer</h2>

    <!-- Mostrar mensajes de error -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('footer_links.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Título:</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>URL:</label>
            <input type="text" name="url" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Columna:</label>
            <input type="number" name="column_number" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Posición:</label>
            <input type="number" name="position" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Visible:</label>
            <select name="visibility" class="form-control" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success mt-3">Guardar</button>
        <a href="{{ route('footer_links.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
