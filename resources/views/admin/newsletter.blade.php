@extends('admin.template')

@section('content')
<div class="container my-5">
    <h1 class="text-center mb-4">Gestión de Suscriptores</h1>

    <!-- Mostrar mensajes de éxito o error -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Tabla de suscriptores -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>IP Address</th>
                <th>Fecha de suscripción</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suscriptores as $suscriptor)
                <tr>
                    <td>{{ $suscriptor->id }}</td>
                    <td>{{ $suscriptor->email }}</td>
                    <td>{{ $suscriptor->ip_address }}</td>
                    <td>{{ $suscriptor->subscribed_at }}</td>
                    <td>
                        @if($suscriptor->is_active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <!-- Botón para activar/desactivar -->
                        <form action="{{ route('newsletter.toggle', $suscriptor->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                @if($suscriptor->is_active)
                                    Desactivar
                                @else
                                    Activar
                                @endif
                            </button>
                        </form>

                        <!-- Botón para eliminar -->
                        <form action="{{ route('newsletter.destroy', $suscriptor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta suscripción?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
