@extends('admin.template')

@section('content')

    <div class="container mt-4">
        <a href="{{ route('admin.users') }}" class="btn btn-secondary mb-4">Volver a la lista de usuarios</a>
        <h1 class="mb-4">Detalles del Usuario</h1>

        <!-- Información Personal -->
        <div class="card mb-4">
            <div class="card-header">
                Información Personal
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> {{ $usuario->id }}</p>
                <p><strong>Nombre:</strong> {{ $usuario->nombre ?? $usuario->name }}</p>
                <p><strong>Apellido Paterno:</strong> {{ $usuario->apellido_paterno ?? 'N/A' }}</p>
                <p><strong>Apellido Materno:</strong> {{ $usuario->apellido_materno ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $usuario->email }}</p>
                <p><strong>Teléfono:</strong> {{ $usuario->telefono ?? 'N/A' }}</p>
                <p><strong>Tratamiento:</strong> {{ $usuario->tratamiento ?? 'N/A' }}</p>
                <p><strong>Correo Alternativo:</strong> {{ $usuario->correo ?? 'N/A' }}</p>

                <!-- Botones de Acciones Administrativas -->
                <div class="mt-3">
                    <button class="btn btn-warning" data-toggle="modal" data-target="#changePasswordModal">
                        <i class="fas fa-key"></i> Cambiar Contraseña
                    </button>
                    <button class="btn btn-danger" data-toggle="modal" data-target="#deleteUserModal">
                        <i class="fas fa-trash"></i> Eliminar Cuenta
                    </button>
                </div>
            </div>
        </div>

        <!-- Direcciones -->
        <div class="card mb-4">
            <div class="card-header">
                Direcciones
            </div>
            <div class="card-body">
                @if($direcciones)
                    <ul class="list-group">
                        @foreach($direcciones as $direccion)
                            <li class="list-group-item">
                                {{ $direccion->nombre }}, {{ $direccion->calle }} No. Ext {{ $direccion->no_ext ?? 'N/A' }}, No. Int {{ $direccion->no_int ?? 'N/A' }}, {{ $direccion->colonia }}, {{ $direccion->municipio }}, {{ $direccion->estado }}, {{ $direccion->pais }}, C.P. {{ $direccion->codigo_postal }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>El usuario no tiene direcciones registradas.</p>
                @endif
            </div>
        </div>

        <!-- Carritos -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                Carrito(s)
                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteCartModal">
                    <i class="fas fa-trash"></i> Eliminar Todos los Carritos
                </button>
            </div>
            <div class="card-body">
                @if($carts)
                    @foreach($carts as $cart)
                        <h5>Carrito ID: {{ $cart->id }}</h5>
                        @if(isset($cart_items[$cart->id]) && $cart_items[$cart->id])
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>No. S</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Precio Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart_items[$cart->id] as $item)
                                        <tr>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->no_s }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->unit_price, 2) }}</td>
                                            <td>${{ number_format($item->final_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>El carrito está vacío.</p>
                        @endif
                    @endforeach
                @else
                    <p>El usuario no tiene carritos registrados.</p>
                @endif
            </div>
        </div>


<!-- Envíos -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        Envíos
        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteShipmentModal">
            <i class="fas fa-trash"></i> Eliminar Todos los Envíos
        </button>
    </div>
    <div class="card-body">
        @if($envios)
            @foreach($envios as $envio)
                <h5>Envío ID: {{ $envio->id }}</h5>
                <ul>
                    <li><strong>Método de Envío:</strong> {{ $envio->ShipmentMethod }}</li>
                    <li><strong>Costo de Envío:</strong> ${{ number_format($envio->shippingcost_IVA, 2) }}</li>
                    <li><strong>Dirección de Envío:</strong> {{ $envio->calle }} No. {{ $envio->no_ext ?? 'N/A' }} Int. {{ $envio->no_int ?? 'N/A' }}, {{ $envio->colonia }}, {{ $envio->municipio }}, {{ $envio->codigo_postal }}, {{ $envio->pais }}</li>
                    <li><strong>Nombre de Contacto:</strong> {{ $envio->nombre }}</li>
                    <li><strong>Teléfono de Contacto:</strong> {{ $envio->telefono_contacto ?? 'N/A' }}</li>
                    <li><strong>Carrito Asociado:</strong> ID Carrito {{ $envio->cart_id }}</li>
                    <li><strong>Status:</strong> {{ $envio->status }}</li>
                </ul>
                <!-- Mostrar items del envío -->
                @if(isset($envio_items[$envio->id]) && $envio_items[$envio->id])
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>No. S</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Precio Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($envio_items[$envio->id] as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->no_s }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->final_price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Mostrar el costo total del envío + productos -->
                    <p><strong>Total del Envío (Productos + Envío):</strong> ${{ number_format($totals[$envio->id], 2) }}</p>
                @else
                    
                @endif
            @endforeach
        @else
            <p>El usuario no tiene envíos registrados.</p>
        @endif
    </div>
</div>


<!-- Órdenes -->
<div class="card mb-4">
    <div class="card-header">
        Órdenes del Usuario
    </div>
    <div class="card-body">
        @if($orders)
            @foreach($orders as $order)
                <h5>Orden ID: {{ $order->id }} - Total: ${{ number_format($order->total, 2) }}</h5>

                <!-- Mostrando detalles de envío -->
                <p><strong>Dirección de Envío:</strong> {{ $order->shipping_address ?? 'N/A' }}</p>
                <p><strong>Método de Envío:</strong> {{ $order->shipment_method ?? 'N/A' }}</p>
                <p><strong>Costo de Envío:</strong> ${{ number_format($order->shipping_cost ?? 0, 2) }}</p>

                <!-- Calcular el descuento total sumando los descuentos monetarios por producto -->
                @php
                    $descuentoTotal = 0;
                    foreach ($order_items[$order->id] as $item) {
                        if ($item->discount > 0) {
                            // Calcular el descuento monetario para este producto
                            $precioSinDescuento = $item->unit_price / (1 - ($item->discount / 100));
                            $descuentoMonetario = ($precioSinDescuento - $item->unit_price) * $item->quantity;
                            $descuentoTotal += $descuentoMonetario;
                        }
                    }
                @endphp
                <p><strong>Descuento Total:</strong> ${{ number_format($descuentoTotal, 2) }}</p>

                <p><strong>Subtotal sin Envío:</strong> ${{ number_format($order->subtotal_sin_envio ?? 0, 2) }}</p>
                <p><strong>Total con IVA:</strong> ${{ number_format($order->total_con_iva ?? $order->total, 2) }}</p>
                <p><strong>Fecha de Creación:</strong> {{ $order->created_at }}</p>

                <!-- Mostrar productos de la orden con descuento y precio sin descuento -->
                @if(isset($order_items[$order->id]) && $order_items[$order->id])
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>No. S</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Precio Total</th>
                                <th>Descuento (%)</th>
                                <th>Precio de 1 Item sin Desc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order_items[$order->id] as $item)
                                <tr>
                                    <td>{{ $item->product_id }}</td>
                                    <td>{{ $item->product_id }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                    <td>{{ number_format($item->discount, 2) }}%</td>
                                    <td>
                                        @php
                                            // Calcular el precio sin descuento por unidad
                                            $precioSinDescuento = $item->unit_price / (1 - ($item->discount / 100));
                                        @endphp
                                        ${{ number_format($precioSinDescuento, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No hay productos en esta orden.</p>
                @endif
            @endforeach
        @else
            <p>El usuario no tiene órdenes registradas.</p>
        @endif
    </div>
</div>

        <!-- Modal Cambiar Contraseña -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.users.changePassword', $usuario->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalLabel">Cambiar Contraseña</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="newPassword">Nueva Contraseña</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirmar Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Eliminar Usuario -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.users.delete', $usuario->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">Eliminar Cuenta</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>¿Estás seguro de que deseas eliminar esta cuenta? Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Eliminar Carritos -->
        <div class="modal fade" id="deleteCartModal" tabindex="-1" role="dialog" aria-labelledby="deleteCartModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.users.deleteCarts', $usuario->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteCartModalLabel">Eliminar Carritos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>¿Estás seguro de que deseas eliminar todos los carritos de este usuario?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Eliminar Envíos -->
        <div class="modal fade" id="deleteShipmentModal" tabindex="-1" role="dialog" aria-labelledby="deleteShipmentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.users.deleteShipments', $usuario->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteShipmentModalLabel">Eliminar Envíos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>¿Estás seguro de que deseas eliminar todos los envíos de este usuario?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
