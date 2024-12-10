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
            $url = "http://app.lancetahg.com/api/lancetaweb?accion=STATUS&pedido={$orderNumber}&token=2235800b9050256bea993e14b2e06181";
            $response = Http::get($url);

            if ($response->successful()) {
                $statusData = $response->json();

                if (is_array($statusData) && !empty($statusData)) {
                    $currentStatus = collect($statusData)->firstWhere('Order No_', $orderNumber);

                    if ($currentStatus && array_key_exists('Type', $currentStatus)) {
                        $type = $currentStatus['Type'];

                        // Obtener registro actual si existe
                        $existingGuia = DB::table('guias')->where('order_no', $orderNumber)->first();

                        // Si el nuevo tipo es null y existe uno previo, conservarlo
                        if ($type === null && $existingGuia) {
                            $type = $existingGuia->type;
                        }

                        $type = (int) $type;

                        // Preparamos las fechas según las reglas
                        $status5Date = $existingGuia->status_5_date ?? null;
                        $status6Date = $existingGuia->status_6_date ?? null;
                        $status7Date = $existingGuia->status_7_date ?? null;

                        // Lógica para asignar fechas dependiendo del type recibido
                        if ($type >= 5) {
                            // Si no hay status_5_date, la seteamos
                            if (is_null($status5Date)) {
                                $status5Date = now();
                            }
                        }

                        if ($type >= 6) {
                            // Si no había fecha 5 y ahora llegó 6, ponemos ambas a now()
                            // (Pero ya hemos puesto 5 arriba si no existía)
                            if (is_null($status6Date)) {
                                $status6Date = now();
                            }
                        }

                        if ($type >= 7) {
                            if (is_null($status5Date)) {
                                $status5Date = now();
                            }
                            if (is_null($status6Date)) {
                                $status6Date = now();
                            }
                            if (is_null($status7Date)) {
                                $status7Date = now();
                            }
                        }

                        // Construimos el arreglo final de actualización
                        $updateData = [
                            'order_no' => $orderNumber,
                            'type' => $type,
                            'updated_at' => now(),
                            'status_5_date' => $status5Date,
                            'status_6_date' => $status6Date,
                            'status_7_date' => $status7Date,
                        ];

                        DB::table('guias')->updateOrInsert(
                            ['order_no' => (string) $orderNumber],
                            $updateData
                        );

                        Log::info("Estado actualizado para order_no {$orderNumber}: Type={$type}");
                    } else {
                        Log::info("No se encontró el estado para order_no {$orderNumber} o faltó el campo Type en la respuesta");
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
