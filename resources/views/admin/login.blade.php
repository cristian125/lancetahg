@extends('admin.logintemplate')
@section('header')
    <style>
        body{
            background: rgb(0,0,0);
background: linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(61,61,61,1) 56%);
        }
        div.logo
        {
            position: absolute;
            float: left;
            width:500px;
            height:100px;
            left: 50%;
            margin-left:-250px ;
            background: url('{{ asset('storage/logos/logolhg.png') }}') no-repeat;
            background-size: 100% auto;
            margin-top:150px;
        }
    </style>
@endsection
@section('content')
    <div class="logo">
    </div>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="width: 400px;">
            <div class="card-body p-4">
                <h5 class="card-title text-center mb-4">Admin Panel</h5>
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
                <form action="{{ route('admin.login.post') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="email">Correo</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection