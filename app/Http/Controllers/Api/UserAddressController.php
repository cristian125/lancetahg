<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class UserAddressController extends Controller
{



    public function getAllUserAddresses(): JsonResponse
    {
        // Obtener las direcciones asociadas a los pedidos en orders y order_shippment
        $addresses = DB::table('order_shippment')
            ->join('orders', 'orders.id', '=', 'order_shippment.order_id')  // Ajuste aquí: se usa 'orders.id'
            ->join('users', 'users.id', '=', 'order_shippment.user_id')
            ->whereNotNull('orders.order_number')  
            ->select(
                'orders.order_number as id',  // Usar order_number como ID
                'order_shippment.user_id as id_customer',
                DB::raw('"0" as id_manufacturer'),
                DB::raw('"0" as id_supplier'),
                DB::raw('"0" as id_warehouse'),
                DB::raw('"145" as id_country'),
                DB::raw('"65" as id_state'),
                'orders.order_number as alias',
                DB::raw('"" as company'),
                'order_shippment.nombre_contacto as firstname',
                DB::raw('"" as lastname'),
                DB::raw('COALESCE(order_shippment.referencias, "") as vat_number'),
                DB::raw('CONCAT(order_shippment.shipping_address, " ", order_shippment.no_ext, IFNULL(CONCAT(" Int ", order_shippment.no_int), "")) as address1'),
                'order_shippment.colonia as address2',
                'order_shippment.codigo_postal as postcode',
                'order_shippment.municipio as city',
                'order_shippment.entre_calles as other',
                'order_shippment.telefono_contacto as phone',
                'order_shippment.telefono_contacto as phone_mobile',
                DB::raw('"" as dni'),
                DB::raw('"0" as deleted'),
                DB::raw('DATE_FORMAT(order_shippment.created_at, "%Y-%m-%d %H:%i:%s") as date_add'),
                DB::raw('DATE_FORMAT(order_shippment.updated_at, "%Y-%m-%d %H:%i:%s") as date_upd'),
                DB::raw('"CW000001" as lscode_cliente'),
                'order_shippment.pais as estado'
            )
            ->get();
    
        if ($addresses->isEmpty()) {
            return response()->json(['message' => 'No addresses found'], 404);
        }
    
        return response()->json(['addresses' => $addresses]);
    }
    
    
    
}
