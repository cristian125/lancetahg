<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GuiasController extends Controller
{
    public function guiasearch(Request $request)
    {

        $apiKey = $request->input('api_key') ?? $request->header('x-api-key');
        if ($apiKey !== env('EXTERNAL_API_KEY')) {
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'failed',
                'message' => 'Unauthorized access attempt',
                'error_details' => 'Invalid API key provided',
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = Http::get('http://app.lancetahg.com/api/lancetaweb', [
            'accion' => 'GUIAS',
            'token' => env('EXTERNAL_API_TOKEN'),
        ]);

        if ($response->successful()) {
            $guiasData = $response->json();

            if (!is_array($guiasData)) {
                DB::table('api_import_logs')->insert([
                    'request_time' => now(),
                    'status' => 'failed',
                    'message' => 'Invalid data format from API',
                    'error_details' => 'The response from the API was not an array',
                ]);
                return response()->json(['error' => 'Invalid data format from API'], 500);
            }

            foreach ($guiasData as $guia) {

                $noFacr = $guia['No_'];
                $orderNo = $guia['Order No_'];
                $noGuia = $guia['No_ Guía'];

                $existingGuia = DB::table('guias')->where('order_no', $orderNo)->first();

                if ($existingGuia) {

                    DB::table('guias')
                        ->where('order_no', $orderNo)
                        ->update([
                            'no_facr' => $noFacr,
                            'no_guia' => $noGuia,
                            'updated_at' => now(),
                        ]);
                } else {

                    DB::table('guias')->insert([
                        'no_facr' => $noFacr,
                        'order_no' => $orderNo,
                        'no_guia' => $noGuia,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'success',
                'message' => 'Datos de guías procesados correctamente.',
                'error_details' => null,
            ]);

            return response()->json(['message' => 'Datos de guías procesados correctamente.']);
        } else {

            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'failed',
                'message' => 'No se pudo obtener los datos de la API externa.',
                'error_details' => 'Error ' . $response->status() . ': ' . $response->body(),
            ]);

            return response()->json(['error' => 'No se pudo obtener los datos de la API externa.'], 500);
        }
    }

    public function statusUpdate(Request $request)
    {
        $apiKey = $request->input('api_key') ?? $request->header('x-api-key');
        if ($apiKey !== env('EXTERNAL_API_KEY')) {
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'failed',
                'message' => 'Unauthorized access attempt',
                'error_details' => 'Invalid API key provided',
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = Http::get('http://app.lancetahg.com/api/lancetaweb', [
            'accion' => 'STATUS',
            'token' => env('EXTERNAL_API_TOKEN'),
        ]);

        if ($response->successful()) {
            $statusData = $response->json();

            if (!is_array($statusData)) {
                DB::table('api_import_logs')->insert([
                    'request_time' => now(),
                    'status' => 'failed',
                    'message' => 'Invalid data format from API',
                    'error_details' => 'The response from the API was not an array',
                ]);
                return response()->json(['error' => 'Invalid data format from API'], 500);
            }

            foreach ($statusData as $status) {
                $orderNo = $status['Order No_'];
                $type = $status['Type'];

                $order = DB::table('orders')->where('order_number', $orderNo)->first();

                if ($order) {
                    $existingGuia = DB::table('guias')->where('order_no', $orderNo)->first();

                    if ($existingGuia) {
                        // Actualiza la guía existente
                        DB::table('guias')
                            ->where('order_no', $orderNo)
                            ->update([
                                'type' => $type,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Inserta una nueva fila si no existe
                        DB::table('guias')->insert([
                            'order_no' => $orderNo,
                            'type' => $type,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    Log::warning("El pedido con order_no $orderNo no existe en la tabla orders.");
                }
            }

            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'success',
                'message' => 'Datos de estado de órdenes procesados correctamente.',
                'error_details' => null,
            ]);

            return response()->json(['message' => 'Datos de estado de órdenes procesados correctamente.']);
        } else {
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'failed',
                'message' => 'No se pudo obtener los datos de la API externa.',
                'error_details' => 'Error ' . $response->status() . ': ' . $response->body(),
            ]);

            return response()->json(['error' => 'No se pudo obtener los datos de la API externa.'], 500);
        }
    }


    
    protected function sendOrderStatusUpdateEmail($order)
    {

        $user = DB::table('users')->where('id', $order->user_id)->first();

        if (!$user) {

            Log::warning('No se encontró el usuario para el pedido ID: ' . $order->id);
            return;
        }

        $orderItems = DB::table('order_items')
            ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s')
            ->select('order_items.*', 'itemsdb.no_s', 'itemsdb.nombre as product_name')
            ->where('order_items.order_id', $order->id)
            ->get();

        $order_shippment = DB::table('order_shippment')->where('order_id', $order->id)->first();
        $shipmentMethod = DB::table('shipping_methods')->where('name', $order->shipment_method)->value('display_name');
        $order->shipment_method_display = $shipmentMethod ?? 'N/A';

        $data = [
            'order' => $order,
            'orderItems' => $orderItems,
            'user' => $user,
            'order_shippment' => $order_shippment,
        ];

        Mail::send('emails.order_status_updated', $data, function ($message) use ($user, $order) {
            $message->to($user->email)
                ->subject('Actualización de estado de tu pedido #' . $order->order_number . ' - LANCETA HG');
        });
    }

}
