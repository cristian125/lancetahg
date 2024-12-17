@extends('admin.index')

@section('content')
<div class="container-fluid py-5">
    <div class="container bg-white p-5 shadow rounded">
        <h2 class="text-center mb-5">Administradores</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Registro de nuevos administradores -->
        <form action="{{ route('admin.register') }}" method="POST" class="mb-5">
            @csrf
            <h4>Registrar Nuevo Administrador</h4>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Nombre</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-4">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="col-md-4">
                    <label>Rol</label>
                    <select class="form-select" name="role" required>
                        <option value="superusuario">Superusuario</option>
                        <option value="editor">Editor</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Contraseña</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="col-md-6">
                    <label>Confirmar Contraseña</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrar Administrador</button>
        </form>

        <hr>

        <!-- Tabla de administradores -->
        <h4>Lista de Administradores</h4>
        <table class="table table-striped table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                <tr>
                    <td>{{ $admin->id }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ ucfirst($admin->role) }}</td>
                    <td class="text-center">
                        <form action="{{ route('admin.update.admin.role', $admin->id) }}" method="POST" class="d-inline">
                            @csrf
                            <select name="role" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                <option value="superusuario" {{ $admin->role == 'superusuario' ? 'selected' : '' }}>Superusuario</option>
                                <option value="editor" {{ $admin->role == 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="viewer" {{ $admin->role == 'viewer' ? 'selected' : '' }}>Viewer</option>
                            </select>
                        </form>

                        <form action="{{ route('admin.delete.admin', $admin->id) }}" method="POST" class="d-inline ms-2">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>

                        <form action="{{ route('admin.change.password', $admin->id) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            <input type="password" name="password" placeholder="Nueva Contraseña" required>
                            <input type="password" name="password_confirmation" placeholder="Confirmar" required>
                            <button type="submit" class="btn btn-warning btn-sm">Cambiar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
body {
    background: rgb(195,195,195);
    background: linear-gradient(90deg, rgba(195,195,195,1) 4%, rgba(227,227,227,1) 79%);
    }

</style>
@endsection
