@extends('admin.layout')

@section('content')
<div class="container mt-5">
    <h2>Editar Enlace del Footer</h2>

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

    <form action="{{ route('admin.footer_links.update', $link->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Título:</label>
            <input type="text" name="title" class="form-control" value="{{ $link->title }}" required>
        </div>
        <div class="form-group">
            <label>URL:</label>
            <input type="text" name="url" class="form-control" value="{{ $link->url }}" required>
        </div>
        <div class="form-group">
            <label>Columna:</label>
            <input type="number" name="column_number" class="form-control" value="{{ $link->column_number }}" required>
        </div>
        <div class="form-group">
            <label>Posición:</label>
            <input type="number" name="position" class="form-control" value="{{ $link->position }}" required>
        </div>
        <div class="form-group">
            <label>Visible:</label>
            <select name="visibility" class="form-control" required>
                <option value="1" {{ $link->visibility ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ !$link->visibility ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success mt-3">Actualizar</button>
        <a href="{{ route('footer_links.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
