<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class AdminOrderController extends Controller
{

    public function show($orderId)
    {
        // Obtener los datos de la orden junto con la información del usuario y del envío
        $orden = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('users_data', 'users.id', '=', 'users_data.user_id')
            ->leftJoin('order_shippment', 'orders.id', '=', 'order_shippment.order_id')
            ->where('orders.id', $orderId)
            ->select(
                'orders.id as order_id',
                'orders.order_number',
                'orders.total',
                'orders.subtotal_sin_envio',
                'orders.shipping_cost',
                'orders.discount',
                'orders.total_con_iva',
                'orders.shipment_method',
                'orders.created_at as order_created_at',
                'users.name as user_name',
                'users.email as user_email',
                'users_data.nombre',
                'users_data.apellido_paterno',
                'users_data.apellido_materno',
                'users_data.telefono',
                'users_data.correo',
                // Campos de dirección de envío
                'order_shippment.shipping_address',
                'order_shippment.no_int',
                'order_shippment.no_ext',
                'order_shippment.colonia',
                'order_shippment.municipio',
                'order_shippment.pais',
                'order_shippment.codigo_postal',
                'order_shippment.nombre_contacto',
                'order_shippment.telefono_contacto',
                'order_shippment.email_contacto'
            )
            ->first();
    
        if (!$orden) {
            abort(404, 'Orden no encontrada.');
        }
    
        // Obtener los detalles de los productos de la orden y buscar el ID real de cada producto en itemsdb
        $items = DB::table('order_items')
            ->where('order_id', $orderId)
            ->select(
                'id as item_id',
                'product_id',
                'description',
                'quantity',
                'unit_price',
                'total_price',
                'discount',
                'iva_rate'
            )
            ->get();
    
        foreach ($items as $item) {
            // Buscar el ID del producto en la tabla itemsdb usando el product_id como no_s
            $item->real_product_id = DB::table('itemsdb')
                ->where('no_s', $item->product_id)
                ->value('id'); // Obtenemos el ID real del producto
        }
    
        // Obtener los detalles de pago
        $pago = DB::table('order_payment')
            ->where('order_id', $orderId)
            ->select(
                'chargetotal',
                'request_type',
                'txtn_processed',
                'ccbrand',
                'cardnumber'
            )
            ->first();
    
        return view('admin.order_detail', compact('orden', 'items', 'pago'));
    }
    
    
    

    public function index(Request $request)
    {
        $search = $request->input('search', '');  
        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.id as order_id',
                'orders.order_number', 
                'orders.total',
                'orders.subtotal_sin_envio',
                'orders.shipping_cost',
                'orders.discount',
                'orders.total_con_iva',
                'orders.shipment_method',
                'orders.created_at as order_created_at', 
                'users.name as user_name'
            )
            ->orderByDesc('orders.created_at'); // Ordenar por la fecha de creación de manera descendente
    
        if (!empty($search)) {
            $query->where('orders.order_number', 'like', "%{$search}%")
                ->orWhere('users.name', 'like', "%{$search}%")
                ->orWhere('orders.shipment_method', 'like', "%{$search}%");
        }
    
        $ordenes = $query->paginate(5);
        
        foreach ($ordenes as $orden) {
            $orden->items = DB::table('order_items')
                ->where('order_id', $orden->order_id)
                ->select(
                    'id as item_id',
                    'product_id',
                    'quantity',
                    'unit_price',
                    'total_price',
                    'discount'
                )
                ->get();
    
            foreach ($orden->items as $item) {
                $realProduct = DB::table('itemsdb')
                    ->where('no_s', $item->product_id) 
                    ->first();
    
                if ($realProduct) {
                    $item->real_product_id = $realProduct->id;  
                } else {
                    $item->real_product_id = null;  
                }
            }
        }
    
        return view('admin.ordenes', compact('ordenes', 'search'));
    }
    



    public function downloadOrderPdf($orderId)
    {
        // Obtener los datos de la orden junto con la información del usuario y del pedido
        $orden = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('users_data', 'users.id', '=', 'users_data.user_id')  
            ->where('orders.id', $orderId)
            ->select(
                'orders.id as order_id',
                'orders.order_number', 
                'orders.total',
                'orders.subtotal_sin_envio',
                'orders.shipping_cost',
                'orders.discount',
                'orders.total_con_iva',
                'orders.shipping_address',     // Asegúrate de que este campo exista en `orders`
                'orders.shipment_method',      // Asegúrate de que este campo exista en `orders`
                'orders.created_at as order_created_at',
                'users_data.nombre',
                'users_data.apellido_paterno',
                'users_data.apellido_materno',
                'users_data.telefono',
                'users_data.correo'
            )
            ->first();
    


        // Obtener los detalles de los productos de la orden
        $items = DB::table('order_items')
            ->where('order_id', $orderId)
            ->select(
                'id as item_id',
                'product_id',
                'description',
                'quantity',
                'unit_price',
                'total_price',
                'discount',
                'iva_rate'
            )
            ->get();
    


        // Obtener los detalles de pago
        $pago = DB::table('order_payment')
            ->where('order_id', $orderId)
            ->select(
                'chargetotal',
                'request_type',
                'txtn_processed',
                'ccbrand',
                'cardnumber'
            )
            ->first();
    


        // Calcular el descuento total aplicado
        $descuentoTotal = 0;
        foreach ($items as $item) {
            $descuentoTotal += ($item->discount / 100) * $item->unit_price * $item->quantity;
        }
    


        // Generar el PDF
        $html = view('admin.order_pdf', compact('orden', 'items', 'descuentoTotal', 'pago'))->render();
        require_once storage_path('app/public/dompdf/autoload.inc.php');
    
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        return $dompdf->stream('pedido_' . $orden->order_number . '.pdf');
    }
    
    
}
