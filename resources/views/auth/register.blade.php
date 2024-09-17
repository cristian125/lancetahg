@extends('template')
@section('header')
@endsection
@section('body')
<div class="container py-5">
    <div class="row justify-content-center">
        <!-- Registro de Usuario -->
        <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">Registro de Usuario</h3>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Ingresa tu nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Ingresa tu correo electrónico" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Crea una contraseña" required>
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirma tu contraseña" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Iniciar Sesión -->
        <div class="col-lg-5 col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-white text-center py-3">
                    <h3 class="mb-0">Iniciar Sesión</h3>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email-login" class="form-label">Correo Electrónico</label>
                            <input type="email" id="email-login" name="email" class="form-control" placeholder="Ingresa tu correo electrónico" required>
                        </div>
                        <div class="mb-4">
                            <label for="password-login" class="form-label">Contraseña</label>
                            <input type="password" id="password-login" name="password" class="form-control" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-block">Iniciar Sesión</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
