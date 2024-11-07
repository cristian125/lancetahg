<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class UserAddressController extends Controller
{
    public function getAllUserAddresses(): JsonResponse
    {
        // Obtener las direcciones donde status = 2
        $addresses = DB::table('users_address')
            ->join('users', 'users.id', '=', 'users_address.user_id')
            ->join('users_data', 'users_data.user_id', '=', 'users.id')
            ->where('users_address.status_bc', 1)
            ->select(
                'users_address.id',
                'users.id as id_customer',
                DB::raw('"0" as id_manufacturer'),
                DB::raw('"0" as id_supplier'),
                DB::raw('"0" as id_warehouse'),
                DB::raw('"145" as id_country'),
                DB::raw('"65" as id_state'),
                'users_address.nombre as alias',
                DB::raw('"" as company'),
                'users_data.nombre as firstname',
                DB::raw('TRIM(CONCAT(users_data.apellido_paterno, " ", users_data.apellido_materno)) as lastname'),
                DB::raw('COALESCE(users_data.rfc, "") as vat_number'),
                DB::raw('CONCAT(users_address.calle, " ", users_address.no_ext, IFNULL(CONCAT(" Int ", users_address.no_int), "")) as address1'),
                'users_address.colonia as address2',
                'users_address.codigo_postal as postcode',
                'users_address.municipio as city',
                'users_address.entre_calles as other',
                'users_data.telefono as phone',
                'users_data.telefono as phone_mobile',
                DB::raw('"" as dni'),
                DB::raw('"0" as deleted'),
                DB::raw('DATE_FORMAT(users_address.created_at, "%Y-%m-%d %H:%i:%s") as date_add'),
                DB::raw('DATE_FORMAT(users_address.updated_at, "%Y-%m-%d %H:%i:%s") as date_upd'),
                DB::raw('"CW000001" as lscode_cliente'),
                'users_address.estado'
            )
            ->get();

        return response()->json(['addresses' => $addresses]);
    }
}
