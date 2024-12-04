@extends('admin.index')

@section('content')
<div class="container">
    <h2>Editar Atributo</h2>

    <form action="{{ route('admin.atributos.update', $atributo->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre">Nombre del Atributo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $atributo->nombre }}" required>
        </div>
        <div class="form-group">
            <label for="grupo_id">Grupo</label>
            <select name="grupo_id" id="grupo_id" class="form-control" required>
                @foreach($grupos as $grupo)
                    <option value="{{ $grupo->id }}" {{ $grupo->id == $atributo->grupo_id ? 'selected' : '' }}>{{ $grupo->descripcion }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Actualizar Atributo</button>
    </form>
</div>
@endsection
