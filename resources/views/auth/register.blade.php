
@extends('template')
@section('header')
@endsection
@section('body')
    <div class="container">
        <div class="row" style="margin:25px;padding:15px">
            <div class="col-md-5" style="border-right: 1px solid #004b61; margin:15px;">
                <h2 class="text-center">Registro de Usuario</h2>
                <hr>
                <form action="{{ route('register') }}" method="POST" >
                    @csrf
                    <div class="row">
                        <div class="input-group">
                            <label for="name" class="input-group-text col-md-4">Nombre:</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="email" class="input-group-text col-md-4">Correo Electrónico:</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="password" class="input-group-text col-md-4">Contraseña:</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="password_confirmation" class="input-group-text col-md-4">Confirmar:</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="row" style="padding:15px;"></div>
                    <div class="row">
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary form-control">Registrarse</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-5" style="margin:15px;">
                <h2 class="text-center">Iniciar Sesion</h2>
                <hr>
                <form action="{{ route('login') }}" method="POST" >
                    @csrf
                    <div class="row">
                        <div class="input-group">
                            <label for="email-login" class="input-group-text col-md-4">Correo Electrónico:</label>
                            <input type="email" id="email-login" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="password-login" class="input-group-text col-md-4">Contraseña:</label>
                            <input type="password" id="password-login" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="row" style="padding:15px;"></div>
                    <div class="row">
                        <div class="input-group">
                            <button type="submit" class="btn btn-warning form-control">Iniciar Sesión</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
