<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserOrderController extends Controller
{
    public function myOrders()
    {
        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }

        $userId = Auth::id();
        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
            
        $orders->map(function ($order) {
            $order->created_at = \Carbon\Carbon::parse($order->created_at);

            $order->is_new = $order->created_at->diffInMinutes(now()) < 120;
            return $order;
        });
        return view('orders_preview', compact('orders'));
    }

    public function orderDetails($orderId)
    {
        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }
    
        $userId = Auth::id();
        $order = DB::table('orders')
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    
        if ($order == null) {
            abort(404);
        }
    
        $isAssignedForPickup = ($order->shipment_method === 'RecogerEnTienda' && !is_null($order->delivery_date) && !is_null($order->delivery_time));
        $trackingNumber = DB::table('guias')
            ->where('order_no', $order->order_number)
            ->value('no_guia');
    
        $trackingUrl = env('TRACKING_URL'); // Obtener la URL base del archivo .env
    
        $order_shippment = DB::table('order_shippment')->where('order_id', $order->id)->first();
        $shipmentMethod = DB::table('shipping_methods')->where('name', $order->shipment_method)->value('display_name');
        $order->shipment_method_display = $shipmentMethod ?? 'N/A';
    
        // Obtener la información de la tienda si el envío es "RecogerEnTienda"
        $store = null;
        if ($order->shipment_method === 'RecogerEnTienda' && $order_shippment && $order_shippment->store_id) {
            $store = DB::table('tiendas')
                ->where('id', $order_shippment->store_id)
                ->select('nombre', 'direccion')
                ->first();
        }
    
        // Filtrar los productos según el método de envío
        $order_items_query = DB::table('order_items')
            ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s')
            ->select('order_items.*', 'itemsdb.nombre as product_name')
            ->where('order_items.order_id', $order->id);
    
        // Si el envío es "RecogerEnTienda", excluir el producto con product_id 999998
        if ($order->shipment_method === 'RecogerEnTienda') {
            $order_items_query->where('order_items.product_id', '!=', '999998');
        }
    
        $order_items = $order_items_query->get();
    
        $orderHistory = DB::table('order_history')->where('order_id', $order->oid)->first();
        $payment = DB::table('order_payment')->where('order_id', $order->id)->where('status', 'APROBADO')->first();
    
        $totalDescuento = 0;
        $totalIva = 0;
        $total = 0;
        foreach ($order_items as $item) {
            $totalDescuento += $item->discount_amount;
            $totalIva += $item->vat_amount;
            $total += $item->amount - $item->discount_amount + $item->vat_amount;
        }
    
        if ($isAssignedForPickup) {
            $order->status_label = 'Asignado a fecha de entrega';
            $order->status_icon = 'bi-calendar-check-fill';
            $order->status_color = 'info';
            $order->delivery_message = "Fecha de entrega: " . \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') .
                "<br>Hora de entrega: " . \Carbon\Carbon::parse($order->delivery_time)->format('H:i') .
                "<br>Recuerde llevar una identificación oficial para recoger su pedido.";
        } else {
            $order->status_label = 'Confirmado y Completado';
            $order->status_icon = 'bi-check2-circle';
            $order->status_color = 'success';
        }
    
        return view('order_details', compact(
            'order',
            'order_shippment',
            'order_items',
            'store', // Pasar la tienda a la vista solo si corresponde
            'totalDescuento',
            'orderHistory',
            'payment',
            'isAssignedForPickup',
            'trackingNumber',
            'trackingUrl'
        ));
    }
    

}
