<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ShippingPaqueteriaController extends Controller
{
    private $id = 0;
    private $user_id = 0;
    private $cart_id = 0;

    public function __construct()
    {
        if (Auth::check()) {
            $this->user_id = Auth::id();
            $this->cart_id = $this->getCartId();
        }
    }

    private function getCartId()
    {
        $cart = DB::table('carts')
            ->select('id')
            ->where('user_id', $this->user_id)
            ->where('status', '1') // Asegúrate de que '1' es el estado correcto
            ->first();

        return $cart ? $cart->id : null;
    }
    public function __autoload()
    {
        session_start();
    }

    public function handlePaqueteriaShipping(Request $request, $userId, $totalPrice)
    {
        $this->id = $userId;
        $direcciones = DB::table('users_address')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();
    
        $direccionValida = false;
        $costoEnvio = 500; 
        $minimoCompra = 2000; 
    
        foreach ($direcciones as $direccion) {
            $direccion->esPaqueteria = true;
            $direccionValida = true;
    
            if ($totalPrice >= $minimoCompra) {
                $direccion->costoEnvio = 0;
            } else {
                $direccion->costoEnvio = $costoEnvio;
            }
        }
    
        // Filtrar productos no elegibles
        $nonEligiblePaqueteriaShipping = collect();
        if ($this->cart_id) {
            $nonEligiblePaqueteriaShipping = DB::table('cart_items')
                ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
                ->where('cart_items.cart_id', $this->cart_id)
                ->where('itemsdb.allow_paqueteria_shipping', 0)
                ->select('itemsdb.nombre as product_name', 'itemsdb.no_s as product_code')
                ->get();
        }
    
        return [
            'direcciones' => $direcciones,
            'direccionValida' => $direccionValida,
            'costoEnvio' => $costoEnvio,
            'totalCart' => $totalPrice,
            'minimoCompra' => $minimoCompra,
            'nonEligiblePaqueteriaShipping' => $nonEligiblePaqueteriaShipping,
        ];
    }
    


    public function actualizarEnvioPaqueteria(Request $request)
    {
        $direccionId = intval($request->direccion);
        $user = DB::table('users_address')->select('user_id')->where('id', $direccionId)->first();

        if (!$user) {
            return response()->json(['error' => 'Dirección no encontrada'], 404);
        }

        $id = $user->user_id;
        $client = new Client();
        $url = route('api.orders'); 
        $token = csrf_token();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-CSRF-TOKEN' => $token,
            ],
            'form_params' => [
                'id' => $id,
                'address_id' => $direccionId,
                '_token' => $token
            ]
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        return response()->json($data);
    }

    public function addPaqueteriaMethod(Request $request)
    {
        try {
            $cartId = $request->input('cart_id');
            $direccionId = $request->input('direccion');
            $shippingCostWithIVA = $request->input('shipping_cost_with_iva'); 
        
            if (!$cartId || !$direccionId || !$shippingCostWithIVA) {
                return response()->json(['success' => false, 'error' => 'Faltan datos requeridos o costo de envío no proporcionado.']);
            }
        
            $direccion = DB::table('users_address')->where('id', $direccionId)->first();
            if (!$direccion) {
                return response()->json(['success' => false, 'error' => 'Dirección no encontrada.']);
            }
        
            $totalProductosConIVA = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->sum(DB::raw('final_price * quantity'));
    
            DB::table('cart_shippment')->insert([
                'cart_id' => $cartId,
                'ShipmentMethod' => 'EnvioPorPaqueteria',
                'no_s' => '999999', 
                'description' => "Envio por Paquetería",
                'unit_price' => round($shippingCostWithIVA/1.16,2),
                'discount' => 0, 
                'final_price' => $shippingCostWithIVA, 
                'shippingcost_IVA' => $shippingCostWithIVA-round($shippingCostWithIVA/1.16,2), 
                'quantity' => 1,
                'nombre' => $direccion->nombre,
                'calle' => $direccion->calle,
                'no_int' => $direccion->no_int,
                'no_ext' => $direccion->no_ext,
                'entre_calles' => $direccion->entre_calles,
                'colonia' => $direccion->colonia,
                'municipio' => $direccion->municipio,
                'codigo_postal' => $direccion->codigo_postal,
                'pais' => $direccion->pais,
                'referencias' => $direccion->referencias,
                'cord_x' => $direccion->cord_x,
                'cord_y' => $direccion->cord_y,
                'status' => 1, 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    
    
    
    
}
