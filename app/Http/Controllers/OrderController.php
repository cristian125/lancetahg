<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    private $validToken;

    public function __construct()
    {
        // Recupera el token válido desde el archivo de configuración o de entorno
        $this->validToken = env('STOREPICKUP_API_TOKEN', 'your-default-token');
    }

    private function isValidToken($token)
    {
        // Compara el token proporcionado con el token válido
        return $token === $this->validToken;
    }

    public function getStorePickupOrderHeaders(Request $request)
    {
        // Valida el token desde los parámetros de la URL
        $token = $request->query('token');
        if (!$this->isValidToken($token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Obtener las órdenes que tienen el método "RecogerEnTienda"
        $orders = DB::table('orders')
            ->join('order_shippment', 'orders.id', '=', 'order_shippment.order_id')
            ->join('tiendas', 'order_shippment.store_id', '=', 'tiendas.id')
            ->where('orders.shipment_method', 'RecogerEnTienda')
            ->whereNull('orders.delivery_date')
            ->whereNull('orders.delivery_time')
            ->select(
                'orders.order_number',
                'orders.created_at',
                'orders.id as order_id',
                'orders.user_id',
                'orders.total_con_IVA',
                'tiendas.codigo_tienda as store_code',
                'order_shippment.nombre_contacto', // Agrega el nombre de contacto
                'order_shippment.email_contacto', // Agrega el correo de contacto
                'order_shippment.telefono_contacto' // Agrega el teléfono de contacto
            )
            ->distinct()
            ->get();

        // Formatea la respuesta
        $formattedOrders = $orders->map(function ($order) {
            return [
                'order_number' => $order->order_number,
                'created_at' => $order->created_at,
                'order_id' => $order->order_id,
                'user_id' => $order->user_id,
                'total_con_IVA' => $order->total_con_IVA,
                'store_code' => $order->store_code,
                'nombre_contacto' => $order->nombre_contacto, // Incluye el nombre de contacto en la respuesta
                'email_contacto' => $order->email_contacto, // Incluye el correo de contacto en la respuesta
                'telefono_contacto' => $order->telefono_contacto, // Incluye el teléfono de contacto en la respuesta
            ];
        });

        return response()->json($formattedOrders);
    }

    public function getOrderItemsByOrderNumber(Request $request, $orderNumber)
    {
        // Valida el token desde los parámetros de la URL
        $token = $request->query('token');
        if (!$this->isValidToken($token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Obtener la orden desde el `order_number`
        $order = DB::table('orders')
            ->where('order_number', $orderNumber)
            ->select('id', 'delivery_date', 'delivery_time')
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Verificar si ya tiene `delivery_date` y `delivery_time`
        if (!is_null($order->delivery_date) && !is_null($order->delivery_time)) {
            return response()->json(['message' => 'Order items not available for this order'], 403);
        }

        // Obtener los items de la orden usando el ID de la orden, excluyendo el producto con product_id 999998
        $items = DB::table('order_items')
            ->where('order_id', $order->id)
            ->where('product_id', '!=', 999998) // Excluir el producto con product_id 999998
            ->select('product_id', 'description', 'quantity', 'unidad_medida_venta', 'unit_price', 'total_price')
            ->get();

        // Renombrar los campos en la respuesta JSON
        $formattedItems = $items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_measure' => $item->unidad_medida_venta,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ];
        });

        return response()->json($formattedItems);
    }

    public function updateDelivery(Request $request)
    {
        // Validar el token desde los parámetros de la URL
        $token = $request->query('token');
        if (!$this->isValidToken($token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Verificar que los campos requeridos estén presentes
        if (!$request->has(['order_number', 'delivery_date', 'delivery_time'])) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }

        // Obtener los valores
        $orderNumber = $request->input('order_number');
        $deliveryDate = $request->input('delivery_date');
        $deliveryTime = $request->input('delivery_time');

        try {
            $formattedDeliveryDate = Carbon::parse($deliveryDate)->format('Y-m-d');
            $formattedDeliveryTime = Carbon::parse($deliveryTime)->format('H:i:s');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date or time format'], 400);
        }

        // Buscar el pedido en la base de datos
        $order = DB::table('orders')
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Verificar que el pedido es "RecogerEnTienda" (opcional)
        if ($order->shipment_method !== 'RecogerEnTienda') {
            return response()->json(['error' => 'Order is not for store pickup'], 400);
        }

        // Actualizar el pedido
        $updated = DB::table('orders')
            ->where('order_number', $orderNumber)
            ->update([
                'delivery_date' => $formattedDeliveryDate,
                'delivery_time' => $formattedDeliveryTime,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return response()->json(['error' => 'Order not updated'], 500);
        }

        // Actualizar los campos en el objeto $order
        $order->delivery_date = $formattedDeliveryDate;
        $order->delivery_time = $formattedDeliveryTime;

        // Obtener el usuario asociado al pedido
        $user = DB::table('users')->where('id', $order->user_id)->first();

        if ($user) {
            // Preparar los datos para el correo
            $orderItems = DB::table('order_items')
                ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s')
                ->select('order_items.*', 'itemsdb.no_s', 'itemsdb.nombre as product_name')
                ->where('order_items.order_id', $order->id)
                ->where('order_items.product_id', '!=', 999998) // Excluir el producto con número 999998
                ->get();

            // Obtener información adicional del envío
            $orderShipment = DB::table('order_shippment')->where('order_id', $order->id)->first();

            // Obtener información de la tienda si es necesario
            $store = null;
            if ($orderShipment && $orderShipment->store_id) {
                $store = DB::table('tiendas')->where('id', $orderShipment->store_id)->first();
            }

            // Enviar el correo electrónico al usuario con todos los datos
            $this->sendDeliveryEmail($user, $order, $orderItems, $orderShipment, $store);
        } else {
            Log::warning('Usuario no encontrado para el ID: ' . $order->user_id);
        }

        return response()->json([
            'success' => 'Order delivery details updated',
            'updated_order' => $orderNumber,
        ]);
    }

    protected function sendDeliveryEmail($user, $order, $orderItems, $orderShipment, $store)
    {
        $data = [
            'user' => $user,
            'order' => $order,
            'orderItems' => $orderItems,
            'orderShipment' => $orderShipment,
            'store' => $store,
        ];

        try {
            Mail::send('emails.delivery_orders', $data, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Información de su próxima entrega - LANCETA HG');
            });

            Log::info('Correo enviado exitosamente al usuario ID: ' . $user->id);
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo electrónico al usuario ID: ' . $user->id . '. Error: ' . $e->getMessage());
        }
    }

}
