@extends('admin.logintemplate')
@section('header')
    <style>
        body {
            background-color: #4d4855;
            background-image: %20linear-gradient(147deg, %20#4d4855%200%, %20#000000%2074%);
        }

        div.logo {
            position: absolute;
            float: left;
            width: 500px;
            height: 100px;
            left: 50%;
            margin-left: -250px;
            background: url('{{ asset('storage/logos/logolhg.png') }}') no-repeat;
            background-size: 100% auto;
            margin-top: 150px;
        }
    </style>
@endsection
@section('content')
    <div class="logo">
    </div>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="width: 400px;">
            <div class="card-body p-4">
                <h2 class="text-center">Registro</h2>
                @if ($errors->any())
                    <div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.register.post') }}" method="POST">
                    @csrf
                    <div>
                        <label for="name">Nombre:</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            required>
                    </div>
                    <div>
                        <label for="email">Correo electrónico:</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                            required>
                    </div>
                    <div>
                        <label for="password">Contraseña:</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                    </div>
                    <div>
                        <label for="password_confirmation">Confirmar contraseña:</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation"
                            required>
                    </div>
                                    <!-- Rol -->
                <div class="mb-3">
                    <label for="role" class="form-label fw-bold">Rol</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="superadmin">Superusuario</option>
                        <option value="editor">Editor</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
                    <div>&nbsp;</div>
                    <div>
                        <button type="submit" class="btn btn-primary form-control">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
