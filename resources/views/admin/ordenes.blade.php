@extends('admin.template')

@section('content')
    <div class="container">
        <h1 class="mb-4">Órdenes de Pedido</h1>

        <!-- Formulario de búsqueda -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control shadow-sm"
                    placeholder="Buscar por Número de Orden, Usuario o Método de Envío"
                    value="{{ request()->input('search') }}">
                <button type="submit" class="btn btn-primary shadow-sm">Buscar</button>
            </div>
        </form>

        <!-- Tabla de órdenes -->
        <table class="table table-hover text-center align-middle shadow-sm rounded">
            <thead class="table-primary">
                <tr>
                    <th>Número de Orden</th>
                    <th>Usuario</th>
                    <th>Total</th>
                    <th>Subtotal (sin envío)</th>
                    <th>Costo de Envío</th>
                    <th>Descuento aplicado</th>
                    {{-- <th>Total con IVA</th> --}}
                    <th>Método de Envío</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @if ($ordenes->isEmpty())
                    <tr>
                        <td colspan="10" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                @else
                    @foreach ($ordenes as $orden)
                        <tr onclick="location.href='{{ route('admin.orders.show', ['orderId' => $orden->order_id]) }}'"
                            class="clickable-row bg-light">
                            <td class="fw-bold text-primary">{{ $orden->order_number }}</td>
                            <td>{{ $orden->user_name }}</td>
                            <td class="text-success">${{ number_format($orden->total, 2) }}</td>
                            <td>${{ number_format($orden->subtotal_sin_envio, 2) }}</td>
                            <td>${{ number_format($orden->shipping_cost, 2) }}</td>
                            <td>${{ number_format($orden->discount, 2) }}</td>
                            {{-- <td>${{ number_format($orden->total_con_iva, 2) }}</td> --}}
                            <td>{{ $orden->shipment_method }}</td>
                            <td>{{ $orden->order_created_at }}</td>
                            <td>
                                <a href="{{ route('admin.order.pdf', ['orderId' => $orden->order_id]) }}"
                                    class="btn btn-sm btn-outline-primary no-click shadow-sm">
                                    Descargar PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $ordenes->appends(['search' => request()->input('search')])->links() }}
        </div>
    </div>
    <style>
        /* Estilo general de la tabla */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
    
        .table th,
        .table td {
            vertical-align: middle;
        }
    
        .table-primary {
            background-color: #007bff !important;
            color: white;
            font-weight: bold;
        }
    
        .table-hover tbody tr:hover {
            background-color: #eaf4fc;
        }
    
        .clickable-row {
            cursor: pointer;
            transition: all 0.3s ease;
        }
    
        .clickable-row:hover {
            background-color: #d6ebff;
            transform: scale(1.01);
        }
    
        /* Botones */
        .btn-outline-primary {
            border: 1px solid #007bff;
            color: #007bff;
            transition: all 0.3s ease;
        }
    
        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }
    
        /* Input de búsqueda */
        .form-control {
            border-radius: 20px;
        }
    
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    </style>
    

{{-- 
    <style>
        .table td,
        .table th {
            vertical-align: middle;
            /* Centrar verticalmente */
        }

        .image-container {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Cambia el color al pasar el mouse */
        .clickable-row:hover {
            background-color: #f1f3f5;
            /* Color más claro */
        }

        /* Asegurar que los botones y enlaces no disparen el evento */
        .no-click {
            pointer-events: auto !important;
        }
    </style> --}}

@endsection
