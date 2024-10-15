@extends('template')

@section('body')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Restablecer Contraseña') }}</div>

                <div class="card-body">
                    <!-- Si no hay token, redirigir o mostrar error -->
                    @if (!isset($token))
                        <div class="alert alert-danger">
                            {{ __('El enlace de restablecimiento de contraseña es inválido o ha expirado.') }}
                        </div>
                    @else
                        <!-- Formulario para restablecer contraseña -->
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <!-- Campo de nueva contraseña -->
                            <div class="form-group">
                                <label for="password">{{ __('Nueva Contraseña') }}</label>
                                <input id="password" type="password" class="form-control" name="password" required autofocus>
                            </div>

                            <!-- Confirmar nueva contraseña -->
                            <div class="form-group">
                                <label for="password-confirm">{{ __('Confirmar Nueva Contraseña') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>

                            <!-- Botón para restablecer la contraseña -->
                            <button type="submit" class="btn btn-primary">
                                {{ __('Restablecer Contraseña') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
