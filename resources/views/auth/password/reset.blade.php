@extends('template')

@section('body')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center font-weight-bold">
                    {{ __('Restablecer Contraseña') }}
                </div>
                <div class="card-body px-4 py-5">
                    @if (!isset($token))
                        <div class="alert alert-danger text-center">
                            {{ __('El enlace de restablecimiento de contraseña es inválido o ha expirado.') }}
                        </div>
                    @else
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group mb-4">
                                <label for="password" class="form-label">{{ __('Nueva Contraseña') }}</label>
                                <input id="password" type="password" class="form-control form-control-lg" name="password" required autofocus>
                            </div>

                            <div class="form-group mb-4">
                                <label for="password-confirm" class="form-label">{{ __('Confirmar Nueva Contraseña') }}</label>
                                <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Restablecer Contraseña') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
