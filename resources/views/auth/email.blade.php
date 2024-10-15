@extends('template')

@section('header')
    <title>Restablecer Contraseña</title>
    <style>
        body {
            background-color: #f7f8fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            /* Reducir espacio superior */
        }
        .card-header {

            background-color: #005f7f;
            color: rgb(255, 255, 255);
            text-align: center;
         
            font-size: 1.75rem;
            font-weight: 700;
            padding: 20px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .form-control {
            border-radius: 30px;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
        }
        .btn-primary {
            background-color: #005f7f;
            border-color: #005f7f;
            border-radius: 30px;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #3a6fdb;
            border-color: #3a6fdb;
        }
        .form-group label {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .text-muted {
            font-size: 0.9rem;
        }
        .alert-success {
            margin-bottom: 1rem;
        }
        .card-body p {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .icon-envelope {
            font-size: 5rem;
            color: #4c84ff;
        }
        .background-container {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(90%);
            min-height: 70vh; /* Reducir altura mínima */
        }
        .center-content {
            min-height: 70vh; /* Reducir altura mínima */
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Alinear hacia la parte superior */
            padding: 20px;
        }
        .card {
            margin-top: 20px; /* Reducir margen superior */
        }
    </style>
@endsection

@section('body')

@if (session('status'))
    <div class="alert alert-success text-center" role="alert">
        {{ session('status') }}
    </div>
@endif

<div class="background-container">
    <div class="container center-content">
        <div class="card">
            <div class="card-header">Recuperar Contraseña</div>
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-envelope-fill icon-envelope"></i>
                </div>
                <p class="text-center mb-4">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

                <form action="{{ route('password.email1') }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="email" class="text-secondary">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@correo.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Enviar Enlace</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
