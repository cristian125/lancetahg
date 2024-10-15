<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    public function myOrders()
    {
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();
    
        // Recuperar los pedidos del usuario
        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                $order->created_at = \Carbon\Carbon::parse($order->created_at);
                return $order;
            });
    
        // Pasar los pedidos a la vista
        return view('orders_preview', compact('orders'));
    }
    
    public function orderDetails($orderId)
    {
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();
    
        // Obtener los detalles del pedido específico
        $order = DB::table('orders')
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    
        // Verificar si el pedido pertenece al usuario
        if (!$order) {
            return redirect()->route('myorders')->with('error', 'Pedido no encontrado.');
        }
    
        // Convertir el campo 'created_at' a un objeto Carbon
        $order->created_at = \Carbon\Carbon::parse($order->created_at);
    
        // Obtener los productos asociados a este pedido
        $order_items = DB::table('order_items')
            ->where('order_id', $orderId)
            ->get();

        // Calcular el descuento total en dinero
        $totalDescuento = 0;
        foreach ($order_items as $item) {
            // Calcular el descuento de cada artículo
            $descuentoPorProducto = ($item->discount / 100) * $item->unit_price * $item->quantity;
            $totalDescuento += $descuentoPorProducto;
        }

        // Pasar los datos a la vista junto con el descuento total
        return view('order_details', compact('order', 'order_items', 'totalDescuento'));
    }

    
}
