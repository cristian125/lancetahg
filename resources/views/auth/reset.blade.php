@extends('template')

@section('header')
    <title>Restablecer Contraseña</title>
@endsection

@section('body')
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="row w-100">
            <div class="col-md-6 offset-md-3">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-center">Nueva Contraseña</h3>
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group mb-4">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="correo@gmail.com" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="password">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="password_confirmation">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Restablecer Contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
