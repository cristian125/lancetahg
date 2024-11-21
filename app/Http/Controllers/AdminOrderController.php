<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{

    public function show($orderId)
    {
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
            $item->real_product_id = DB::table('itemsdb')
                ->where('no_s', $item->product_id)
                ->value('id');
        }
    
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
    
        // Obtener el número de guía
        $trackingNumber = DB::table('guias')
            ->where('order_no', $orden->order_number)
            ->value('no_guia');
    
        // Obtener las URLs desde .env
        $trackingUrl = env('TRACKING_URL');
        $genLabelUrl = env('GEN_LABEL_URL');
    
        return view('admin.order_detail', compact('orden', 'items', 'pago', 'trackingNumber', 'trackingUrl', 'genLabelUrl'));
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
            ->orderByDesc('orders.created_at');

        if (!empty($search)) {
            $query->where('orders.order_number', 'like', "%{$search}%")
                ->orWhere('users.name', 'like', "%{$search}%")
                ->orWhere('orders.shipment_method', 'like', "%{$search}%");
        }

        $ordenes = $query->paginate(10);

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
                'orders.shipping_address',
                'orders.shipment_method',
                'orders.created_at as order_created_at',
                'users_data.nombre',
                'users_data.apellido_paterno',
                'users_data.apellido_materno',
                'users_data.telefono',
                'users_data.correo'
            )
            ->first();

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

        $descuentoTotal = 0;
        foreach ($items as $item) {
            $descuentoTotal += ($item->discount / 100) * $item->unit_price * $item->quantity;
        }

        $html = view('admin.order_pdf', compact('orden', 'items', 'descuentoTotal', 'pago'))->render();
        require_once storage_path('app/public/dompdf/autoload.inc.php');

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('pedido_' . $orden->order_number . '.pdf');
    }

}
