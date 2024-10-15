<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PaymentLogController extends Controller
{
    public function index(Request $request)
    {
        // Capturar el término de búsqueda desde el request
        $search = $request->input('search');

        // Construir la consulta de logs
        $logsQuery = DB::table('payment_requests_log')
            ->when($search, function ($queryBuilder) use ($search) {
                $queryBuilder->where('oid', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc');

        // Paginar los resultados
        $logs = $logsQuery->paginate(10);

        // Devolver la vista con los logs y el término de búsqueda
        return view('admin.payment_logs.paymentlogs', compact('logs', 'search'));
    }

    public function show($id)
    {
        // Obtener el detalle de un log específico
        $log = DB::table('payment_requests_log')->where('id', $id)->first();
    
        if (!$log) {
            return redirect()->route('admin.payment_logs.index')->with('error', 'Registro no encontrado');
        }
    
        // Obtener la orden asociada al OID, permitir que si no tiene OID igual continúe mostrando la vista
        $order = DB::table('orders')->where('oid', $log->oid)->first();
    
        // Si no se encuentra la orden, seguir mostrando la vista, pero sin los datos de la orden
        $orderItems = collect(); // Definir una colección vacía si no hay orden
        if ($order) {
            // Obtener los productos de la orden desde la tabla order_items
            $orderItems = DB::table('order_items')
                            ->where('order_id', $order->id)
                            ->get();
    
            // Para cada item, buscar el nombre del producto en la tabla `itemsdb` usando `product_id` como `no_s`
            $orderItems = $orderItems->map(function ($item) {
                $product = DB::table('itemsdb')->where('no_s', $item->product_id)->first();
                $item->product_name = $product ? $product->nombre : 'Nombre no disponible';
                return $item;
            });
        }
    
        // Retornar la vista con el log, la orden y los productos, incluyendo los nombres
        return view('admin.payment_logs.show', compact('log', 'order', 'orderItems'));
    }
    
    
    
}
