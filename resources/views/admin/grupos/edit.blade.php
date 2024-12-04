@extends('admin.index')

@section('content')
<div class="container">
    <h2>Editar Grupo</h2>

    <form action="{{ route('admin.grupos.update', $grupo->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="descripcion">Descripción del Grupo</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{ $grupo->descripcion }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Actualizar Grupo</button>
    </form>
</div>
@endsection
