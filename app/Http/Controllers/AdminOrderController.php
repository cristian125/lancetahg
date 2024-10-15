<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');  // Asegúrate de que $search siempre tenga un valor

        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.id as order_id',
                'orders.total',
                'orders.subtotal_sin_envio',
                'orders.shipping_cost',
                'orders.discount',
                'orders.total_con_iva',
                'orders.shipment_method',
                'orders.created_at as order_created_at',
                'users.name as user_name'
            );

        // Búsqueda por ID de orden, usuario o método de envío
        if (!empty($search)) {
            $query->where('orders.id', 'like', "%{$search}%")
                ->orWhere('users.name', 'like', "%{$search}%")
                ->orWhere('orders.shipment_method', 'like', "%{$search}%");
        }

        // Paginación
        $ordenes = $query->paginate(5);

        // Para cada orden, obtenemos los ítems relacionados
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

            // Aquí realizamos la consulta a la tabla itemsdb para obtener el id correcto basado en el _no_s
            foreach ($orden->items as $item) {
                $realProduct = DB::table('itemsdb')
                    ->where('no_s', $item->product_id) // Coincidencia con el _no_s
                    ->first();

                // Añadir el ID real del producto al ítem
                if ($realProduct) {
                    $item->real_product_id = $realProduct->id;  // Guardar el id real en el ítem
                } else {
                    $item->real_product_id = null;  // Si no se encuentra, dejarlo como null
                }
            }
        }

        // Pasamos la variable $search a la vista
        return view('admin.ordenes', compact('ordenes', 'search'));
    }
    public function downloadOrderPdf($orderId)
    {
        // Obtener los detalles de la orden
        $orden = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('users_data', 'users.id', '=', 'users_data.user_id')  // Unión para obtener más información
            ->where('orders.id', $orderId)
            ->select(
                'orders.id as order_id',
                'orders.total',
                'orders.subtotal_sin_envio',
                'orders.shipping_cost',
                'orders.discount',
                'orders.total_con_iva',
                'orders.shipment_method',
                'orders.created_at as order_created_at',
                'users_data.nombre',
                'users_data.apellido_paterno',
                'users_data.apellido_materno',
                'users_data.telefono',
                'users_data.correo'
            )
            ->first();
    
        // Obtener los ítems de la orden
        $items = DB::table('order_items')
            ->where('order_id', $orderId)
            ->select(
                'id as item_id',
                'product_id',
                'quantity',
                'unit_price',
                'total_price',
                'discount'
            )
            ->get();
    
        // Calcular el total del descuento
        $descuentoTotal = 0;
        foreach ($items as $item) {
            $descuentoTotal += ($item->discount / 100) * $item->unit_price * $item->quantity;
        }
    
        // Pasar los datos a la vista para el PDF
        $html = view('admin.order_pdf', compact('orden', 'items', 'descuentoTotal'))->render();
    
        // Incluir los archivos de dompdf desde el directorio donde los descargaste
        require_once storage_path('app/public/dompdf/autoload.inc.php');
    
        // Usar Dompdf manualmente
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // Descargar el PDF
        return $dompdf->stream('pedido_' . $orden->order_id . '.pdf');
    }
    
}
