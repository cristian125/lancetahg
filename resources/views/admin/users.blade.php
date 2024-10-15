@extends('admin.template')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Lista de Usuarios</h1>

    <!-- Barra de herramientas con el formulario de búsqueda -->
    <div class="mb-4">
        <form id="searchForm" action="{{ route('admin.users') }}" method="GET">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" name="id" class="form-control mb-2" placeholder="ID" value="{{ request('id') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" value="{{ request('nombre') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="apellido" class="form-control mb-2" placeholder="Apellido" value="{{ request('apellido') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="email" name="email" class="form-control mb-2" placeholder="Email" value="{{ request('email') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="telefono" class="form-control mb-2" placeholder="Teléfono" value="{{ request('telefono') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-block mb-2">
                                <i class="fas fa-undo"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Contenedor de la Tabla de Usuarios -->
    <div id="userTable">
        @include('admin.users_table')
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Función para obtener usuarios mediante AJAX
        function fetchUsers(page = 1) {
            var formData = $('#searchForm').serialize();
            $.ajax({
                url: '{{ route("admin.users") }}?page=' + page,
                type: 'GET',
                data: formData,
                beforeSend: function() {
                    // Mostrar un loader si lo deseas
                    $('#userTable').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div></div>');
                },
                success: function(data) {
                    $('#userTable').html(data);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        // Evento para inputs del formulario de búsqueda
        $('#searchForm input').on('keyup change', function() {
            fetchUsers();
        });

        // Evento para el formulario de búsqueda al enviarse
        $('#searchForm').on('submit', function(event) {
            event.preventDefault();
            fetchUsers();
        });

        // Evento para los enlaces de paginación
        $(document).on('click', '#userTable .pagination a', function(event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetchUsers(page);
        });
    });
</script>
@endsection
