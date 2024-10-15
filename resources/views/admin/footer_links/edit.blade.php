@extends('admin.template')

@section('content')
<div class="container mt-5">
    <h2>Editar Enlace del Footer</h2>

    <!-- Mostrar mensajes de error -->
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>¡Ups! Hubo algunos problemas con tus entradas.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('footer_links.update', $link->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Título:</label>
            <input type="text" name="title" class="form-control" id="title" required value="{{ old('title', $link->title) }}">
        </div>
        <div class="form-group">
            <label for="url">URL:</label>
            <input type="text" name="url" class="form-control" id="url" required value="{{ old('url', $link->url) }}">
        </div>
        <div class="form-group">
            <label for="column_number">Columna:</label>
            <input type="number" name="column_number" class="form-control" id="column_number" required value="{{ old('column_number', $link->column_number) }}">
        </div>
        <div class="form-group">
            <label for="position">Posición:</label>
            <input type="number" name="position" class="form-control" id="position" required value="{{ old('position', $link->position) }}">
        </div>
        <div class="form-group">
            <label for="visibility">Visible:</label>
            <select name="visibility" class="form-control" id="visibility" required>
                <option value="1" {{ $link->visibility == 1 ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ $link->visibility == 0 ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success mt-3">Actualizar</button>
        <a href="{{ route('footer_links.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
