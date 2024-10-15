@extends('admin.template')

@section('content')
<div class="container my-5">
    <h1 class="text-center mb-4">Trámites de Cliente Distinguido</h1>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tratamiento</th>
                <th>Nombre</th>
                <th>CURP</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Código Postal</th>
                <th>Estado</th>
                <th>Municipio</th>
                <th>Colonia</th>
                <th>Identificación</th>
                <th>Comprobante</th>
                <th>Fecha de Creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
            <tr>
                <td>{{ $cliente->id }}</td>
                <td>{{ $cliente->tratamiento }}</td>
                <td>{{ $cliente->nombre }}</td>
                <td>{{ $cliente->curp }}</td>
                <td>{{ $cliente->telefono }}</td>
                <td>{{ $cliente->email }}</td>
                <td>{{ $cliente->direccion }}</td>
                <td>{{ $cliente->codigo_postal }}</td>
                <td>{{ $cliente->estado }}</td>
                <td>{{ $cliente->municipio }}</td>
                <td>{{ $cliente->colonia }}</td>

                <td>{{ $cliente->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
