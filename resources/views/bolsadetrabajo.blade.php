@extends('template')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4">Bolsa de Trabajo</h1>
    <div class="text-center mb-4">
        <p>En Lanceta HG estamos buscando personas talentosas para formar parte de nuestro equipo de trabajo. Si le interesa unirse a esta gran familia envíe sus datos.</p>
        <p class="text-muted"><small>Los campos con <span class="text-danger">*</span> son requeridos</small></p>
    </div>
    <form method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nombre" class="form-label">Nombres: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="col-md-4">
                <label for="apellido_paterno" class="form-label">Apellido Paterno: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
            </div>
            <div class="col-md-4">
                <label for="apellido_materno" class="form-label">Apellido Materno:</label>
                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <div class="col-md-3">
                <label for="delegacion" class="form-label">Delegación o municipio donde vive: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="delegacion" name="delegacion" required>
            </div>
            <div class="col-md-3">
                <label for="estado" class="form-label">Estado: <span class="text-danger">*</span></label>
                <select class="form-select" id="estado" name="estado" required>
                    <option value="CDMX">CDMX</option>
                    <!-- Más opciones aquí -->
                </select>
            </div>
            <div class="col-md-3">
                <label for="telefono" class="form-label">Teléfono: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="correo" class="form-label">Correo Electrónico: <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="col-md-3">
                <label for="grado_estudios" class="form-label">Último grado de estudios: <span class="text-danger">*</span></label>
                <select class="form-select" id="grado_estudios" name="grado_estudios" required>
                    <option value="Secundaria">Secundaria</option>
                    <!-- Más opciones aquí -->
                </select>
            </div>
            <div class="col-md-3">
                <label for="genero" class="form-label">Género: <span class="text-danger">*</span></label>
                <select class="form-select" id="genero" name="genero" required>
                    <option value="Masculino">Masculino</option>
                    <!-- Más opciones aquí -->
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="puesto_interes" class="form-label">Puesto de interés: <span class="text-danger">*</span></label>
                <select class="form-select" id="puesto_interes" name="puesto_interes" required>
                    <option value="Auxiliar de Almacén">Auxiliar de Almacén</option>
                    <!-- Más opciones aquí -->
                </select>
            </div>
            <div class="col-md-6">
                <label for="cv" class="form-label">Adjuntar CV: <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="cv" name="cv" required>
            </div>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="privacidad" name="privacidad" required>
            <label class="form-check-label" for="privacidad">He leído y acepto el aviso de privacidad <span class="text-danger">*</span></label>
        </div>
        <button type="submit" class="btn btn-success">ENVIAR</button>
    </form>
</div>
@endsection
