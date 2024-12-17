<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    private function getGuia($pedido)
    {
        try {
            $guiasResponse = Http::get('http://app.lancetahg.com/api/lancetaweb', [
                'accion' => 'GUIAS',
                'pedido' => $pedido,
                'token' => '2235800b9050256bea993e14b2e06181',
            ]);

            if ($guiasResponse->successful()) {
                $guiasData = $guiasResponse->json();

                if (is_array($guiasData) && !empty($guiasData)) {
                    foreach ($guiasData as $guia) {
                        if (!empty($guia['No_ Guía'])) {
                            $noFacr = $guia['No_'];
                            $noGuia = $guia['No_ Guía'];

                            // Actualizar o insertar la guía sin condicionar
                            DB::table('guias')->updateOrInsert(
                                ['order_no' => (string) $pedido],
                                [
                                    'no_facr' => (string) $noFacr,
                                    'no_guia' => (string) $noGuia,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                ]
                            );

                            Log::info("Guía insertada/actualizada para el pedido {$pedido}: No_Facr={$noFacr}, No_Guia={$noGuia}");
                        } else {
                            Log::info("El número de guía está vacío para el pedido {$pedido}");
                        }
                    }
                } else {
                    Log::info("Respuesta de GUIAS no válida para el pedido {$pedido}");
                }
            } else {
                Log::error("Error en la API GUIAS para el pedido {$pedido}: " . $guiasResponse->body());
            }
        } catch (\Exception $e) {
            Log::error("Error al conectar con la API GUIAS para el pedido {$pedido}: " . $e->getMessage());
        }
    }

    // public function orderDetails($orderId)
    // {
    //     $mantenimiento = ProductosDestacadosController::checkMaintenance();
    //     if ($mantenimiento == 'true') {
    //         return redirect(route('mantenimento'));
    //     }

    //     $userId = Auth::id();
    //     $order = DB::table('orders')
    //         ->where('id', $orderId)
    //         ->where('user_id', $userId)
    //         ->first();

    //     if ($order == null) {
    //         abort(404);
    //     }

    //     // Obtener el registro de guias primero
    //     $guiaRecord = DB::table('guias')->where('order_no', (string)$order->order_number)->first();

    //     // Aquí removemos cualquier lógica condicional. Siempre haremos la petición.
    //     try {
    //         $statusResponse = Http::get('http://app.lancetahg.com/api/lancetaweb', [
    //             'accion' => 'STATUS',
    //             'token' => env('EXTERNAL_API_TOKEN'),
    //             'order_no' => $order->order_number
    //         ]);

    //         if ($statusResponse->successful()) {
    //             $statusData = $statusResponse->json();
    //             if (is_array($statusData) && !empty($statusData)) {
    //                 $currentStatus = collect($statusData)
    //                     ->firstWhere('Order No_', $order->order_number);

    //                 if ($currentStatus) {
    //                     $type = $currentStatus['Type'];
    //                     $orderNumber = (string)$order->order_number;

    //                     if (!$guiaRecord) {
    //                         // Insertar un nuevo registro en guias si no existe
    //                         DB::table('guias')->insert([
    //                             'order_no' => $orderNumber,
    //                             'type' => $type,
    //                             'status_5_date' => $type == 5 ? now() : null,
    //                             'status_6_date' => $type == 6 ? now() : null,
    //                             'status_7_date' => $type == 7 ? now() : null,
    //                             'created_at' => now(),
    //                             'updated_at' => now(),
    //                         ]);
    //                     } else {
    //                         // Actualizamos siempre con el type más reciente
    //                         $updateData = ['type' => $type, 'updated_at' => now()];

    //                         // Ajustar las fechas según el estado nuevo
    //                         if ($type >= 5 && is_null($guiaRecord->status_5_date)) {
    //                             $updateData['status_5_date'] = now();
    //                         }
    //                         if ($type >= 6 && is_null($guiaRecord->status_6_date)) {
    //                             $updateData['status_6_date'] = now();
    //                         }
    //                         if ($type >= 7 && is_null($guiaRecord->status_7_date)) {
    //                             $updateData['status_7_date'] = now();
    //                         }

    //                         DB::table('guias')
    //                             ->where('order_no', $orderNumber)
    //                             ->update($updateData);
    //                     }
    //                 }
    //             } else {
    //                 Log::info("No se encontró estado para order_no {$order->order_number}");
    //             }
    //         } else {
    //             Log::error("La API devolvió un error para order_no {$order->order_number}: " . $statusResponse->body());
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("Error al conectar con la API para order_no {$order->order_number}: " . $e->getMessage());
    //     }

    //     // Lógica original para mostrar la vista
    //     $isAssignedForPickup = ($order->shipment_method === 'RecogerEnTienda' && !is_null($order->delivery_date) && !is_null($order->delivery_time));
    //     $trackingNumber = DB::table('guias')
    //         ->where('order_no', $order->order_number)
    //         ->value('no_guia');

    //     $trackingUrl = env('TRACKING_URL');

    //     $order_shippment = DB::table('order_shippment')->where('order_id', $order->id)->first();
    //     $shipmentMethod = DB::table('shipping_methods')->where('name', $order->shipment_method)->value('display_name');
    //     $order->shipment_method_display = $shipmentMethod ?? 'N/A';

    //     $guiaType = DB::table('guias')
    //         ->where('order_no', $order->order_number)
    //         ->value('type');
    //     $order->guia_type = $guiaType;

    //     $store = null;
    //     if ($order->shipment_method === 'RecogerEnTienda' && $order_shippment && $order_shippment->store_id) {
    //         $store = DB::table('tiendas')
    //             ->where('id', $order_shippment->store_id)
    //             ->select('nombre', 'direccion')
    //             ->first();
    //     }

    //     $order_items_query = DB::table('order_items')
    //         ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s')
    //         ->select('order_items.*', 'itemsdb.nombre as product_name')
    //         ->where('order_items.order_id', $order->id);

    //     if ($order->shipment_method === 'RecogerEnTienda') {
    //         $order_items_query->where('order_items.product_id', '!=', '999998');
    //     }

    //     $order_items = $order_items_query->get();

    //     $orderHistory = DB::table('order_history')->where('order_id', $order->oid)->first();
    //     $payment = DB::table('order_payment')->where('order_id', $order->id)->where('status', 'APROBADO')->first();

    //     $totalDescuento = 0;
    //     $totalIva = 0;
    //     $total = 0;
    //     foreach ($order_items as $item) {
    //         $totalDescuento += $item->discount_amount;
    //         $totalIva += $item->vat_amount;
    //         $total += $item->amount - $item->discount_amount + $item->vat_amount;
    //     }

    //     if ($isAssignedForPickup) {
    //         $order->status_label = 'Asignado a fecha de entrega';
    //         $order->status_icon = 'bi-calendar-check-fill';
    //         $order->status_color = 'info';
    //         $order->delivery_message = "Fecha de entrega: " . \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') .
    //             "<br>Hora de entrega: " . \Carbon\Carbon::parse($order->delivery_time)->format('H:i') .
    //             "<br>Recuerde llevar una identificación oficial para recoger su pedido.";
    //     } else {
    //         $order->status_label = 'Confirmado y Completado';
    //         $order->status_icon = 'bi-check2-circle';
    //         $order->status_color = 'success';
    //     }

    //     return view('order_details', compact(
    //         'order',
    //         'order_shippment',
    //         'order_items',
    //         'store',
    //         'totalDescuento',
    //         'orderHistory',
    //         'payment',
    //         'isAssignedForPickup',
    //         'trackingNumber',
    //         'trackingUrl'
    //     ));
    // }

    public function updateOrderStatus($orderNumber)
    {
        try {
            $url = "http://app.lancetahg.com/api/lancetaweb?accion=STATUS&token=2235800b9050256bea993e14b2e06181";
            $response = Http::get($url, [
                'accion' => 'STATUS',
                'token' => '2235800b9050256bea993e14b2e06181',
                'pedido' => (string)$orderNumber
            ]);
            
            $data = json_decode($response->body(), true);
    
            if ($response->successful()) {
                $statusData = $response->json();

                if (is_array($statusData) && !empty($statusData)) {
                    // Filtramos todos los registros del mismo pedido
                    $dataForOrder = collect($statusData)->where('Order No_', $orderNumber);
    
                    if ($dataForOrder->isEmpty()) {
                        Log::info("No se encontraron estados para order_no {$orderNumber}");
                        return;
                    }
    
                    // Obtenemos el registro actual si existe
                    $existingGuia = DB::table('guias')->where('order_no', $orderNumber)->first();
    
                    // Inicializamos las fechas actuales si ya existen
                    $status5Date = $existingGuia->status_5_date ?? null;
                    $status6Date = $existingGuia->status_6_date ?? null;
                    $status7Date = $existingGuia->status_7_date ?? null;
    
                    // Procesamos cada registro del pedido para asignar las fechas correctas
                    foreach ($dataForOrder as $record) {
                        $type = $record['Type'] ?? null;
                        $fechaHora = $record['FechaHora'] ?? null;
    
                        // Si falta Type o FechaHora, saltamos
                        if (is_null($type) || is_null($fechaHora)) {
                            continue;
                        }
    
                        // Parseamos la fecha a un objeto Carbon
                        $fechaCarbon = \Carbon\Carbon::parse($fechaHora);
    
                        // Asignamos las fechas según el tipo
                        // La lógica aquí puede variar según si deseas siempre la primera fecha encontrada,
                        // o actualizarla cada vez que haya una nueva.
                        // En este ejemplo, si ya hay una fecha guardada no la sobrescribimos (podrías cambiar la lógica si gustas).
                        if ($type == 5 && is_null($status5Date)) {
                            $status5Date = $fechaCarbon;
                        } elseif ($type == 6 && is_null($status6Date)) {
                            $status6Date = $fechaCarbon;
                        } elseif ($type == 7 && is_null($status7Date)) {
                            $status7Date = $fechaCarbon;
                        }
                    }
    
                    // Obtenemos el Type más alto encontrado para el pedido
                    $maxType = $dataForOrder->max('Type') ?? null;
                    if (is_null($maxType) && $existingGuia) {
                        $maxType = $existingGuia->type; // Si no encontramos ninguno nuevo, mantenemos el actual
                    }
    
                    // Si no hay maxType, no se actualiza nada
                    if (!is_null($maxType)) {
                        $updateData = [
                            'order_no' => $orderNumber,
                            'type' => (int) $maxType,
                            'updated_at' => now(),
                            'status_5_date' => $status5Date,
                            'status_6_date' => $status6Date,
                            'status_7_date' => $status7Date,
                        ];
    
                        DB::table('guias')->updateOrInsert(
                            ['order_no' => (string) $orderNumber],
                            $updateData
                        );
    
                        Log::info("Estado actualizado para order_no {$orderNumber}: Type={$maxType}");
                    } else {
                        Log::info("No se pudo determinar el tipo para order_no {$orderNumber}");
                    }
                }
            } else {
                Log::error("Error en la API para order_no {$orderNumber}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Error al conectar con la API para order_no {$orderNumber}: " . $e->getMessage());
        }
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

        $this->getGuia($order->order_number);

        $this->updateOrderStatus($order->order_number);

        // Obtener el registro de guias primero
        $guiaRecord = DB::table('guias')->where('order_no', (string) $order->order_number)->first();

        // // 1) Consultar estado de la orden (STATUS)
        // try {
        //     $statusResponse = Http::get('http://app.lancetahg.com/api/lancetaweb', [
        //         'accion' => 'STATUS',
        //         'token' => env('EXTERNAL_API_TOKEN'),
        //         'order_no' => $order->order_number,
        //     ]);

        //     if ($statusResponse->successful()) {
        //         $statusData = $statusResponse->json();
        //         if (is_array($statusData) && !empty($statusData)) {
        //             $currentStatus = collect($statusData)->firstWhere('Order No_', $order->order_number);

        //             if ($currentStatus) {
        //                 $type = $currentStatus['Type'];
        //                 $orderNumber = (string) $order->order_number;

        //                 if (!$guiaRecord) {
        //                     // Insertar un nuevo registro en guias si no existe
        //                     DB::table('guias')->insert([
        //                         'order_no' => $orderNumber,
        //                         'type' => $type,
        //                         'status_5_date' => $type == 5 ? now() : null,
        //                         'status_6_date' => $type == 6 ? now() : null,
        //                         'status_7_date' => $type == 7 ? now() : null,
        //                         'created_at' => now(),
        //                         'updated_at' => now(),
        //                     ]);
        //                 } else {
        //                     // Actualizar con el type más reciente
        //                     $updateData = ['type' => $type, 'updated_at' => now()];

        //                     // Ajustar las fechas según el nuevo estado
        //                     if ($type >= 5 && is_null($guiaRecord->status_5_date)) {
        //                         $updateData['status_5_date'] = now();
        //                     }
        //                     if ($type >= 6 && is_null($guiaRecord->status_6_date)) {
        //                         $updateData['status_6_date'] = now();
        //                     }
        //                     if ($type >= 7 && is_null($guiaRecord->status_7_date)) {
        //                         $updateData['status_7_date'] = now();
        //                     }

        //                     DB::table('guias')
        //                         ->where('order_no', $orderNumber)
        //                         ->update($updateData);
        //                 }
        //             }
        //         } else {
        //             Log::info("No se encontró estado para order_no {$order->order_number}");
        //         }
        //     } else {
        //         Log::error("La API devolvió un error para order_no {$order->order_number}: " . $statusResponse->body());
        //     }
        // } catch (\Exception $e) {
        //     Log::error("Error al conectar con la API (STATUS) para order_no {$order->order_number}: " . $e->getMessage());
        // }

        // 2) Consultar la API de GUIAS para actualizar el no_guia siempre

        // Lógica original para mostrar la vista
        $isAssignedForPickup = ($order->shipment_method === 'RecogerEnTienda' && !is_null($order->delivery_date) && !is_null($order->delivery_time));

        // Ahora $trackingNumber estará siempre actualizado
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
        // Obtener registro de guias con fechas de estado
        $guiaRecord = DB::table('guias')
            ->where('order_no', $order->order_number)
            ->select('type', 'status_5_date', 'status_6_date', 'status_7_date')
            ->first();

// Pasar las fechas al objeto $order
        if ($guiaRecord) {
            $order->status_5_date = $guiaRecord->status_5_date ? \Carbon\Carbon::parse($guiaRecord->status_5_date)->format('d/m/Y H:i') : null;
            $order->status_6_date = $guiaRecord->status_6_date ? \Carbon\Carbon::parse($guiaRecord->status_6_date)->format('d/m/Y H:i') : null;
            $order->status_7_date = $guiaRecord->status_7_date ? \Carbon\Carbon::parse($guiaRecord->status_7_date)->format('d/m/Y H:i') : null;
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
