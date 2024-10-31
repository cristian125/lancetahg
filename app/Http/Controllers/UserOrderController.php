<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    public function myOrders()
    {
                // Verificar si el sitio está en mantenimiento
                $mantenimiento = ProductosDestacadosController::checkMaintenance();
                if ($mantenimiento == 'true') {
                    return redirect(route('mantenimento'));
                }
        
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();
    
        // Recuperar los pedidos del usuario con paginación
        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Paginar mostrando 10 pedidos por página
    
        // Transformar los pedidos con el método map
        $orders->map(function ($order) {
            $order->created_at = \Carbon\Carbon::parse($order->created_at);
            // Verificar si la orden tiene menos de 10 minutos de haber sido creada
            $order->is_new = $order->created_at->diffInMinutes(now()) < 1000;
            return $order;
        });
    
        // Pasar los pedidos a la vista
        return view('orders_preview', compact('orders'));
    }
    




    public function orderDetails($orderId)
    {
        // Verificar si el sitio está en mantenimiento
        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }
    
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();
    
        // Verificar si existe la orden con el ID y pertenece al usuario
        $order = DB::table('orders')
            ->where('id', $orderId)  // Usar ID para buscar el pedido
            ->where('user_id', $userId) // Asegurar que la orden pertenece al usuario
            ->first();
    
        if (!$order) {
            return redirect()->route('myorders')->with('error', 'Pedido no encontrado.');
        }
    
        // Obtener el display_name del método de envío
        $shipmentMethod = DB::table('shipping_methods')
            ->where('name', $order->shipment_method)
            ->value('display_name');
    
        // Asignar display_name al order para que esté disponible en la vista
        $order->shipment_method_display = $shipmentMethod ?? 'N/A';
    
        // Obtener los productos asociados a este pedido
        $order_items = DB::table('order_items')
            ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s') // Join con tabla de productos
            ->select('order_items.*', 'itemsdb.nombre as product_name') // Seleccionar campos necesarios
            ->where('order_items.order_id', $order->id)
            ->get();
    
        if ($order_items->isEmpty()) {
            return redirect()->route('myorders')->with('error', 'No hay productos asociados a este pedido.');
        }
    
        // Obtener el historial del pedido (status) usando el OID como order_id en la tabla `order_history`
        $orderHistory = DB::table('order_history')
            ->where('order_id', $order->oid) // Usar el OID de la tabla orders para buscar el historial en order_history
            ->first();
    
        // Obtener la información de pago si el pago fue aprobado
        $payment = DB::table('order_payment')
            ->where('order_id', $order->id)
            ->where('status', 'APROBADO') // Solo pagos aprobados
            ->first();
    
        // Calcular el descuento total en dinero
        $totalDescuento = 0;
        foreach ($order_items as $item) {
            $descuentoPorProducto = ($item->discount / 100) * $item->unit_price * $item->quantity;
            $totalDescuento += $descuentoPorProducto;
        }
    
        // Pasar los datos a la vista junto con el historial del pedido y el método de pago
        return view('order_details', compact('order', 'order_items', 'totalDescuento', 'orderHistory', 'payment'));
    }
    
    

    
    
}
