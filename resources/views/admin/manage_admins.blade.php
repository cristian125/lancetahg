@extends('admin.index') <!-- Extiende el template base -->
    
@section('content')

<!-- Contenedor principal -->
<div class="container-fluid py-5">
    <div class="container bg-white p-5 shadow rounded">
        <!-- Título centralizado -->
        <h2 class="text-center mb-5">Administrar</h2>

        <!-- Mostrar mensaje de éxito si existe -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabla responsiva de administradores -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
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
                                @if($admin->id !== Auth::guard('admin')->id())
                                    <!-- Formulario para cambiar el rol -->
                                    <form action="{{ route('admin.update.admin.role', $admin->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <select name="role" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                            <option value="superusuario" {{ $admin->role == 'superusuario' ? 'selected' : '' }}>Superusuario</option>
                                            <option value="editor" {{ $admin->role == 'editor' ? 'selected' : '' }}>Editor</option>
                                            <option value="viewer" {{ $admin->role == 'viewer' ? 'selected' : '' }}>Viewer</option>
                                        </select>
                                    </form>
                                @else
                                    <!-- Mensaje cuando no se puede cambiar el rol de uno mismo -->
                                    <span class="text-muted">No puede cambiar su propio rol</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>
<style>

body {
    background: rgb(195,195,195);
    background: linear-gradient(90deg, rgba(195,195,195,1) 4%, rgba(227,227,227,1) 79%);
    }

</style>
@endsection
