@extends('admin.template')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Bloque para cambiar contraseña -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Cambiar Contraseña</h4>
                </div>
                <div class="card-body p-4">
                    <!-- Mostrar mensaje de éxito si existe -->
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Mostrar errores de validación -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Formulario para cambiar la contraseña -->
                    <form action="{{ route('admin.changePassword') }}" method="POST">
                        @csrf

                        <!-- Contraseña actual -->
                        <div class="form-group mb-3">
                            <label for="current_password" class="form-label fw-bold">Contraseña Actual</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Ingrese su contraseña actual" required>
                        </div>

                        <!-- Nueva contraseña -->
                        <div class="form-group mb-3">
                            <label for="new_password" class="form-label fw-bold">Nueva Contraseña</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Ingrese una nueva contraseña" required>
                        </div>

                        <!-- Confirmar nueva contraseña -->
                        <div class="form-group mb-3">
                            <label for="new_password_confirmation" class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="Confirme la nueva contraseña" required>
                        </div>

                        <!-- Botón de enviar -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bloque para mostrar datos del usuario -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg-secondary text-white text-center">
                    <h4 class="mb-0">Detalles del Usuario</h4>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold">Nombre:</h5>
                    <p>{{ Auth::guard('admin')->user()->name }}</p>

                    <h5 class="fw-bold">Email:</h5>
                    <p>{{ Auth::guard('admin')->user()->email }}</p>

                    <h5 class="fw-bold">Permisos:</h5>
                    <ul class="list-unstyled">
                        @if(Auth::guard('admin')->user()->role === 'superusuario')
                            <li><i class="fas fa-check-circle text-success"></i> Superusuario</li>
                        @elseif(Auth::guard('admin')->user()->role === 'editor')
                            <li><i class="fas fa-check-circle text-success"></i> Editor</li>
                        @else
                            <li><i class="fas fa-check-circle text-success"></i> Usuario Regular</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
