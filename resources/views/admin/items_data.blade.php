@extends('admin.template')

@section('content')
    <div class="container mt-5">
        <h1 class="card-title text-center mb-4 font-weight-bold text-primary">Administración de Productos y Logs</h1>
        <p class="text-center text-secondary mb-4">Gestion de API's</p>

        <div class="d-flex justify-content-center mb-4">
            <button id="updateProductsBtn" class="btn btn-outline-primary btn-lg mx-2 d-flex align-items-center">
                <i class="fas fa-sync-alt mr-2"></i> <span id="updateText">Actualizar Productos</span>
            </button>
            <button id="updateGuiasBtn" class="btn btn-outline-secondary btn-lg mx-2 d-flex align-items-center">
                <i class="fas fa-book mr-2"></i> <span id="updateGuiasText">Actualizar Guías</span>
            </button>
            <button id="updateStatusBtn" class="btn btn-outline-success btn-lg mx-2 d-flex align-items-center">
                <i class="fas fa-tasks mr-2"></i> <span id="updateStatusText">Actualizar Estado de Órdenes</span>
            </button>
        </div>


        <!-- Loader personalizado -->
        <div id="loader" class="d-none">
            <div class="wrapper">
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="shadow"></div>
                <div class="shadow"></div>
                <div class="shadow"></div>
            </div>
        </div>

        <div id="resultMessage" class="alert mt-3 text-center d-none"></div>

        <h2 class="text-center mt-5">Logs de Peticiones</h2>
        <table class="table table-bordered table-hover mt-3">
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

        <div class="d-flex justify-content-center mt-4" id="paginationLinks">
            {{ $logs->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper {
            width: 200px;
            height: 60px;
            position: relative;
            z-index: 1;
            margin: 0 auto;
        }

        .circle {
            width: 20px;
            height: 20px;
            position: absolute;
            border-radius: 50%;
            background-color: #0056b3;
            left: 15%;
            transform-origin: 50%;
            animation: circle7124 .5s alternate infinite ease;
        }

        @keyframes circle7124 {
            0% {
                top: 60px;
                height: 5px;
                border-radius: 50px 50px 25px 25px;
                transform: scaleX(1.7);
            }

            40% {
                height: 20px;
                border-radius: 50%;
                transform: scaleX(1);
            }

            100% {
                top: 0%;
            }
        }

        .circle:nth-child(2) {
            left: 45%;
            animation-delay: .2s;
        }

        .circle:nth-child(3) {
            left: auto;
            right: 15%;
            animation-delay: .3s;
        }

        .shadow {
            width: 20px;
            height: 4px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.9);
            position: absolute;
            top: 62px;
            transform-origin: 50%;
            z-index: -1;
            left: 15%;
            filter: blur(1px);
            animation: shadow046 .5s alternate infinite ease;
        }

        @keyframes shadow046 {
            0% {
                transform: scaleX(1.5);
            }

            40% {
                transform: scaleX(1);
                opacity: .7;
            }

            100% {
                transform: scaleX(.2);
                opacity: .4;
            }
        }

        .shadow:nth-child(4) {
            left: 45%;
            animation-delay: .2s
        }

        .shadow:nth-child(5) {
            left: auto;
            right: 15%;
            animation-delay: .3s;
        }
    </style>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {

            const externalApiKey = "{{ env('EXTERNAL_API_KEY') }}";


            $('#updateProductsBtn').on('click', function(e) {
                e.preventDefault();
                triggerUpdate('{{ route('admin.fetchItems') }}', '#updateProductsBtn',
                    'Productos actualizados correctamente.');
            });


            $('#updateGuiasBtn').on('click', function(e) {
                e.preventDefault();
                triggerUpdate('{{ route('admin.fetchGuias') }}', '#updateGuiasBtn',
                    'Guías actualizadas correctamente.', {
                        api_key: externalApiKey
                    });
            });


            $('#updateStatusBtn').on('click', function(e) {
                e.preventDefault();
                triggerUpdate('{{ route('status.update') }}', '#updateStatusBtn',
                    'Estados de órdenes actualizados correctamente.', {
                        api_key: externalApiKey
                    });
            });


            function triggerUpdate(url, buttonSelector, successMessage, data = {}) {

                $('#loader').removeClass('d-none');
                $(buttonSelector).hide();
                $('#resultMessage').hide();

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#loader').addClass('d-none');
                        $(buttonSelector).show();
                        $('#resultMessage').removeClass('alert-danger').addClass('alert alert-success')
                            .html(`<i class="fas fa-check-circle"></i> ${successMessage}`)
                            .fadeIn();
                        fetchLogs();
                    },
                    error: function(xhr) {
                        $('#loader').addClass('d-none');
                        $(buttonSelector).show();
                        $('#resultMessage').removeClass('alert-success').addClass('alert alert-danger')
                            .html('<i class="fas fa-times-circle"></i> Error: ' + (xhr.responseJSON
                                ?.error || 'No se pudo realizar la actualización.'))
                            .fadeIn();
                    }
                });
            }


            function fetchLogs(page = 1) {
                $.get("{{ route('admin.itemsData') }}?page=" + page, function(data) {
                    $('#logsTableBody').html(data.tableHtml);
                    $('#paginationLinks').html(data.paginationHtml);
                });
            }


            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const page = $(this).attr('href').split('page=')[1];
                fetchLogs(page);
            });
        });
    </script>
@endsection
