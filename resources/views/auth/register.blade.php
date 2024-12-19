@extends('template')
@section('header')
@endsection
@section('body')
<div class="container p-1">
    @if ($errors->has('email'))
        <div class="alert alert-danger">
            {{ $errors->first('email') }}
            <button type="button" class="btn btn close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <script>
            $(document).ready(function() {
                setTimeout(() => {
                    $('.alert').fadeOut('slow');
                }, 15000);
            });
        </script>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
</div>
    <div class="container py-5">
        <div class="row justify-content-center">

            <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h3 class="mb-0">Registro de Usuario</h3>
                    </div>
                    <div class="card-body p-4">
                        <form id="registerForm">
                            @csrf
                            <div id="register-error-messages" class="alert alert-danger d-none"></div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="Ingrese su nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="Ingrese su correo electrónico" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="Crea una contraseña" required>
                            </div>
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control" placeholder="Confirme su contraseña" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-warning text-white text-center py-3">
                        <h3 class="mb-0">Iniciar Sesión</h3>
                    </div>
                    <div class="card-body p-4">
                        <form id="loginForm">
                            @csrf
                            <div id="login-error-messages" class="alert alert-danger d-none"></div>

                            <div class="mb-3">
                                <label for="email-login" class="form-label">Correo Electrónico</label>
                                <input type="email" id="email-login" name="email" class="form-control"
                                    placeholder="Ingrese su correo electrónico" required>
                            </div>
                            <div class="mb-4">
                                <label for="password-login" class="form-label">Contraseña</label>
                                <input type="password" id="password-login" name="password" class="form-control"
                                    placeholder="Ingrese su contraseña" required>
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



    <!-- Script AJAX -->
    <script>
        $(document).ready(function() {
            // Manejo del formulario de registro
            $('#registerForm').on('submit', function(event) {
                event.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('register') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert(response.success);
                            window.location.href = '/';
                        } else if (response.error) {
                            $('#register-error-messages').html(response.error).removeClass(
                                'd-none');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '<ul>';
                        $.each(errors, function(key, value) {
                            errorMessages += '<li>' + value[0] + '</li>';
                        });
                        errorMessages += '</ul>';
                        $('#register-error-messages').html(errorMessages).removeClass('d-none');
                    }
                });
            });


            // Manejo del formulario de inicio de sesión
            $('#loginForm').on('submit', function(event) {
                event.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('login') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        window.location.href = '/';
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '<ul>';
                        $.each(errors, function(key, value) {
                            $.each(value, function(index, message) {
                                errorMessages += '<li>' + message + '</li>';
                            });
                        });
                        errorMessages += '</ul>';
                        $('#login-error-messages').html(errorMessages).removeClass('d-none');
                    }
                });
            });
        });
    </script>

@endsection
