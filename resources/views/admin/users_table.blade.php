@if($usuarios->count())
    <table class="table table-hover table-bordered">
        <thead class="thead-light">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nombre Completo</th>
                <th scope="col">Email</th>
                <th scope="col">Teléfono</th>
                <th scope="col" class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->nombre ?? $usuario->name }} {{ $usuario->apellido_paterno ?? '' }} {{ $usuario->apellido_materno ?? '' }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->telefono ?? 'N/A' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.showusers', $usuario->id) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

     <div class="d-flex justify-content-center">
        {!! $usuarios->links() !!}
    </div>
@else
    <div class="alert alert-warning" role="alert">
        No se encontraron usuarios.
    </div>
@endif
