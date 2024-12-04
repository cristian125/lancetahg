@extends('admin.index')

@section('content')
<div class="container">
    <h2>Crear Nuevo Grupo</h2>

    <form action="{{ route('admin.grupos.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nombre">Nombre del grupo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}">
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción del Grupo</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Crear Grupo</button>
    </form>

    
</div>
@endsection
