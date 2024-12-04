@extends('admin.template')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Título principal -->
        <div class="col-12 text-center mb-4">
            <h1 class="display-4 text-primary font-weight-bold">Panel de Administración</h1>
            <p class="lead text-secondary">Gestión de API's y Logs del Sistema</p>
        </div>
    </div>

    <!-- Botones de acciones -->
    <div class="row mb-5">
        <div class="col-md-3 col-sm-6 mb-3">
            <button id="updateProductsBtn" class="btn btn-primary btn-block shadow">
                <i class="fas fa-sync-alt"></i> Actualizar Productos
            </button>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <button id="updateGuiasBtn" class="btn btn-secondary btn-block shadow">
                <i class="fas fa-book"></i> Actualizar Guías
            </button>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <button id="updateStatusBtn" class="btn btn-success btn-block shadow">
                <i class="fas fa-tasks"></i> Actualizar Estado de Órdenes
            </button>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <button id="exportProductsBtn" class="btn btn-warning btn-block shadow">
                <i class="fas fa-upload"></i> Exportar a Google Merchant
            </button>
        </div>
    </div>

    <!-- Loader personalizado -->
    <div id="loader" class="d-none text-center mb-4">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>

    <!-- Mensaje de resultados -->
    <div id="resultMessage" class="alert d-none text-center"></div>

    <!-- Tabla de Logs -->
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4 text-secondary">Historial de Logs</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover shadow">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Estado</th>
                            <th>Mensaje</th>
                            <th>Detalles de Error</th>
                            <th>Fecha de Petición</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        @include('admin.partials.logs_table', ['logs' => $logs])
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4" id="paginationLinks">
                {{ $logs->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Estilos -->
<style>
    .btn {
        font-size: 1rem;
        font-weight: bold;
        padding: 0.75rem;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    .table thead th {
        font-size: 0.875rem;
    }

    .table tbody td {
        font-size: 0.875rem;
    }
    .badge-success{
        color: rgb(46, 161, 55)
    }
    .badge-danger{
        color: rgb(160, 0, 0)
    }

</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        const externalApiKey = "{{ env('EXTERNAL_API_KEY') }}";

        // Botón Actualizar Productos
        $('#updateProductsBtn').on('click', function (e) {
            e.preventDefault();
            triggerUpdate('{{ route('admin.fetchItems') }}', '#updateProductsBtn', 'Productos actualizados correctamente.');
        });

        // Botón Actualizar Guías
        $('#updateGuiasBtn').on('click', function (e) {
            e.preventDefault();
            triggerUpdate('{{ route('admin.fetchGuias') }}', '#updateGuiasBtn', 'Guías actualizadas correctamente.', {
                api_key: externalApiKey
            });
        });

        // Botón Actualizar Estado
        $('#updateStatusBtn').on('click', function (e) {
            e.preventDefault();
            triggerUpdate('{{ route('status.update') }}', '#updateStatusBtn', 'Estados de órdenes actualizados correctamente.', {
                api_key: externalApiKey
            });
        });

        // Botón Exportar Productos
        $('#exportProductsBtn').on('click', function (e) {
            e.preventDefault();
            const exportUrl = "{{ env('EXPORT_PRODUCTS_URL') }}";
            const exportToken = "{{ env('EXPORT_PRODUCTS_TOKEN') }}";
            const url = `${exportUrl}?token=${exportToken}`;
            triggerExport(url, '#exportProductsBtn', 'Productos exportados correctamente.');
        });

        // Función para ejecutar actualizaciones
        function triggerUpdate(url, buttonSelector, successMessage, data = {}) {
            $('#loader').removeClass('d-none');
            $(buttonSelector).attr('disabled', true);
            $('#resultMessage').hide();

            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                success: function (response) {
                    $('#loader').addClass('d-none');
                    $(buttonSelector).attr('disabled', false);
                    $('#resultMessage').removeClass('alert-danger').addClass('alert alert-success')
                        .html(`<i class="fas fa-check-circle"></i> ${successMessage}`).fadeIn();
                    fetchLogs();
                },
                error: function (xhr) {
                    $('#loader').addClass('d-none');
                    $(buttonSelector).attr('disabled', false);
                    $('#resultMessage').removeClass('alert-success').addClass('alert alert-danger')
                        .html('<i class="fas fa-times-circle"></i> Error: ' + (xhr.responseJSON?.error || 'No se pudo realizar la actualización.')).fadeIn();
                }
            });
        }

        // Función para ejecutar exportación
        function triggerExport(url, buttonSelector, successMessage) {
            $('#loader').removeClass('d-none');
            $(buttonSelector).attr('disabled', true);
            $('#resultMessage').hide();

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#loader').addClass('d-none');
                    $(buttonSelector).attr('disabled', false);
                    $('#resultMessage').removeClass('alert-danger').addClass('alert alert-success')
                        .html(`<i class="fas fa-check-circle"></i> ${successMessage}`).fadeIn();
                    fetchLogs();
                },
                error: function (xhr) {
                    $('#loader').addClass('d-none');
                    $(buttonSelector).attr('disabled', false);
                    $('#resultMessage').removeClass('alert-success').addClass('alert alert-danger')
                        .html('<i class="fas fa-times-circle"></i> Error: ' + (xhr.responseJSON?.error || 'No se pudo realizar la exportación.')).fadeIn();
                }
            });
        }

        // Función para actualizar logs
        function fetchLogs(page = 1) {
            $.get("{{ route('admin.itemsData') }}?page=" + page, function (data) {
                $('#logsTableBody').html(data.tableHtml);
                $('#paginationLinks').html(data.paginationHtml);
            });
        }

        // Paginación
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            fetchLogs(page);
        });
    });
</script>
@endsection
