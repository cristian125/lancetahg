@extends('template')
@section('header')
    <title>Error 404 - No encontrado</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .error-container {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .error-image {
            width: 100%;
            height: 100%;
            min-height: 350px;
            background-image: url('{{ asset('storage/img/error/error-404.gif') }}');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .error-description {
            font-size: 18px;
            color: #666;
        }
    </style>
@endsection
@section('body')
    @php
        use App\Http\Controllers\ProductosDestacadosController;
        $mantenimiento = ProductosDestacadosController::checkMaintenance();
    @endphp
    @if ($mantenimiento == true)
        <script>
            location.href = "{{ route('mantenimento') }}";
        </script>
    @endif
    <div class="error-container">
        <div class="error-image"></div>
        <h2 class="error-message">Error 404 - No encontrado</h2>
        <p class="error-description">Lo sentimos, pero la página que estás buscando no existe o no se encuentra disponible en
            este momento.</p>
        <p><a href="/">Volver a la página principal</a></p>
    </div>
@endsection
