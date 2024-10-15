@extends('admin.template')

@section('content')
<!-- Encabezado Profesional con Fondo de Degradado -->

    <div class="container">
        <h1 class="display-4">
            <i ></i>Logs de Pagos
        </h1>
    </div>


<!-- Contenedor Principal -->
<div class="container">
    <!-- Tarjeta para la Búsqueda -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.payment_logs.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0" id="search-icon">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por OID, Estado o Usuario..." value="{{ request('search') }}" aria-label="Buscar" aria-describedby="search-icon">
                    </div>
                </div>
                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.payment_logs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Quitar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjeta para la Tabla de Logs -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#ID</th>
                            <th>OID</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="{{ $log->status == 'FALLADO' ? 'log-failed' : '' }}">
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->oid }}</td>
                                <td>${{ number_format($log->chargetotal, 2) }}</td>
                                <td>
                                    @if($log->status == 'APROBADO')
                                        <span class="badge badge-success text-white">
                                            <i class="fas fa-check-circle me-1"></i>{{ $log->status }}
                                        </span>
                                    @elseif($log->status == 'FALLADO')
                                        <span class="badge badge-danger text-white">
                                            <i class="fas fa-times-circle me-1"></i>{{ $log->status }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary text-white">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $log->status }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $log->user_id ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <!-- Botón para ver detalles del log -->
                                    <a href="{{ route('admin.payment_logs.show', $log->id) }}" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <!-- Mostrar mensaje si no hay resultados -->
                            <tr>
                                <td colspan="7" class="text-center text-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>No se encontraron resultados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación Mejorada -->
    <div class="d-flex justify-content-center mt-4">
        {{ $logs->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
    $(document).ready(function () {
        // Inicializar tooltips de Bootstrap
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- CSS Personalizado -->
<style>
    body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Encabezado Profesional */
    header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
    }

    header h1 {
        font-weight: 700;
    }

    /* Tabla */
    .table th, .table td {
        vertical-align: middle;
    }

    .table thead th {
        background-color: #343a40;
        color: #fff;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.03);
    }

    /* Badges Personalizados */
    .badge-success {
        background-color: #28a745;
        border-radius: 0.25rem;
    }

    .badge-danger {
        background-color: #dc3545;
        border-radius: 0.25rem;
    }

    .badge-secondary {
        background-color: #6c757d;
        border-radius: 0.25rem;
    }

    /* Botones */
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: #fff;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-info:hover {
        background-color: #138496;
        color: #fff;
    }

    /* Paginación */
    .pagination li a, .pagination li span {
        color: #17a2b8;
    }

    .pagination li.active a, .pagination li.active span {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        header h1 {
            font-size: 1.8rem;
        }

        header p {
            font-size: 1rem;
        }
    }
</style>
@endsection
