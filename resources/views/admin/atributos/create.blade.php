@extends('admin.index')

@section('content')
<div class="container">
    <h2>Crear Nuevo Atributo</h2>

    <form action="{{ route('admin.atributos.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre del Atributo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="grupo_id">Grupo</label>
            <select name="grupo_id" id="grupo_id" class="form-control" required>
                <option value="">Seleccione un grupo</option>
                @foreach($grupos as $grupo)
                    <option value="{{ $grupo->id }}">{{ $grupo->descripcion }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Crear Atributo</button>
    </form>
</div>
@endsection
