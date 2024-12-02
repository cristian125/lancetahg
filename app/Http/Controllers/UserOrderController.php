<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
    
        // Obtener el registro de guias primero
        $guiaRecord = DB::table('guias')->where('order_no', (string)$order->order_number)->first();
    
        // Determinar si necesitamos realizar la petición a la API
        $needsApiRequest = false;
        if (!$guiaRecord) {
            // No existe registro en guias, necesitamos hacer la petición
            $needsApiRequest = true;
        } elseif (is_null($guiaRecord->type)) {
            // Existe registro pero type es null, necesitamos actualizarlo
            $needsApiRequest = true;
        }
    
        if ($needsApiRequest) {
            // Solicitar el estado del pedido desde la API
            try {
                $statusResponse = Http::get('http://app.lancetahg.com/api/lancetaweb', [
                    'accion' => 'STATUS',
                    'token' => env('EXTERNAL_API_TOKEN'),
                    'order_no' => $order->order_number
                ]);
    
                if ($statusResponse->successful()) {
                    $statusData = $statusResponse->json();
    
                    if (is_array($statusData) && !empty($statusData)) {
                        $currentStatus = collect($statusData)
                            ->firstWhere('Order No_', $order->order_number);
    
                        if ($currentStatus) {
                            $type = $currentStatus['Type'];
                            $orderNumber = (string)$order->order_number; // Asegurar que sea string
    
                            if (!$guiaRecord) {
                                // Insertar una nueva fila si no existe
                                DB::table('guias')->insert([
                                    'order_no' => $orderNumber,
                                    'type' => $type,
                                    'status_5_date' => $type == 5 ? now() : null,
                                    'status_6_date' => $type == 6 ? now() : null,
                                    'status_7_date' => $type == 7 ? now() : null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            } else {
                                // Actualizar las fechas según las condiciones
                                $updateData = [];
    
                                if ($type == 5 && is_null($guiaRecord->status_5_date)) {
                                    $updateData['status_5_date'] = now();
                                }
                                if ($type == 6) {
                                    if (is_null($guiaRecord->status_6_date)) {
                                        $updateData['status_6_date'] = now();
                                    }
                                    if (is_null($guiaRecord->status_5_date)) {
                                        $updateData['status_5_date'] = now();
                                    }
                                }
                                if ($type == 7) {
                                    if (is_null($guiaRecord->status_7_date)) {
                                        $updateData['status_7_date'] = now();
                                    }
                                    if (is_null($guiaRecord->status_6_date)) {
                                        $updateData['status_6_date'] = now();
                                    }
                                    if (is_null($guiaRecord->status_5_date)) {
                                        $updateData['status_5_date'] = now();
                                    }
                                }
    
                                if (!empty($updateData)) {
                                    $updateData['type'] = $type; // Actualizar el tipo actual
                                    $updateData['updated_at'] = now();
                                    DB::table('guias')
                                        ->where('order_no', $orderNumber)
                                        ->update($updateData);
                                }
                            }
                        }
                    } else {
                        Log::info("No se encontró estado para order_no {$order->order_number}");
                    }
                } else {
                    Log::error("La API devolvió un error para order_no {$order->order_number}: " . $statusResponse->body());
                }
            } catch (\Exception $e) {
                Log::error("Error al conectar con la API para order_no {$order->order_number}: " . $e->getMessage());
            }
        }
    
        // Continuar con la lógica original del método
        $isAssignedForPickup = ($order->shipment_method === 'RecogerEnTienda' && !is_null($order->delivery_date) && !is_null($order->delivery_time));
        $trackingNumber = DB::table('guias')
            ->where('order_no', $order->order_number)
            ->value('no_guia');
    
        $trackingUrl = env('TRACKING_URL');
    
        $order_shippment = DB::table('order_shippment')->where('order_id', $order->id)->first();
        $shipmentMethod = DB::table('shipping_methods')->where('name', $order->shipment_method)->value('display_name');
        $order->shipment_method_display = $shipmentMethod ?? 'N/A';
    
        $guiaType = DB::table('guias')
            ->where('order_no', $order->order_number)
            ->value('type');
        $order->guia_type = $guiaType;
    
        $store = null;
        if ($order->shipment_method === 'RecogerEnTienda' && $order_shippment && $order_shippment->store_id) {
            $store = DB::table('tiendas')
                ->where('id', $order_shippment->store_id)
                ->select('nombre', 'direccion')
                ->first();
        }
    
        $order_items_query = DB::table('order_items')
            ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s')
            ->select('order_items.*', 'itemsdb.nombre as product_name')
            ->where('order_items.order_id', $order->id);
    
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
            'store',
            'totalDescuento',
            'orderHistory',
            'payment',
            'isAssignedForPickup',
            'trackingNumber',
            'trackingUrl'
        ));
    }
}
