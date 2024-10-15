@extends('admin.template') <!-- Extiende el template base -->

@section('content')
    <style>
        /* Estilo para el fondo degradado */
        body {
        background: rgb(195,195,195);
        background: linear-gradient(90deg, rgba(195,195,195,1) 4%, rgba(227,227,227,1) 79%);
    }

        .welcome-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh;
        }

        .logo-container img {
            max-width: 100%;
            height: auto;
        }
    </style>

    <div class="welcome-container">
        <div class="logo-container">
            <img src="{{ asset('storage/logos/logolhg.png') }}" alt="Logo de Lanceta HG">
        </div>
    </div>
@endsection
