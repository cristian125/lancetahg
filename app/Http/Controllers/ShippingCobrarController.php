<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class ShippingCobrarController extends Controller
{

    private $user_id = 0;
    private $cart_id = 0;

    public function __construct()
    {
        if (Auth::check()) {
            $this->user_id = Auth::id();
            $this->cart_id = $this->getIdCart();
        }
    }

    private function getIdCart()
    {
        $cart = DB::table('carts')
            ->select('id')
            ->where('user_id', $this->user_id)
            ->where('status', '1')
            ->first();

        return $cart ? $cart->id : null;
    }

    public function handleCobrarShipping(Request $request, $userId, $totalCart)
    {
        $direcciones = DB::table('users_address')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();
    
        foreach ($direcciones as $direccion) {
            $direccion->esValida = true; // Aplica tu lógica para validar la dirección
        }
    
        // Filtrar productos no elegibles
        $nonEligibleCobrarShipping = collect();
        if ($this->cart_id) {
            $nonEligibleCobrarShipping = DB::table('cart_items')
                ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
                ->where('cart_items.cart_id', $this->cart_id)
                ->where('itemsdb.allow_cobrar_shipping', 0)
                ->select('itemsdb.nombre as product_name', 'itemsdb.no_s as product_code')
                ->get();
        }
    
        return [
            'direcciones' => $direcciones,
            'totalCart' => $totalCart,
            'nonEligibleCobrarShipping' => $nonEligibleCobrarShipping,
        ];
    }
    
    

    public function actualizarEnvio(Request $request)
    {
        $direccionId = $request->input('direccion');
        $userId = $request->input('id');

        // Validar que la dirección pertenece al usuario y es válida
        $direccion = DB::table('users_address')
            ->where('id', $direccionId)
            ->where('user_id', $userId)
            ->where('status', 1)
            ->first();

        if ($direccion) {
            return response()->json([
                'success' => true,
                'message' => 'Dirección válida para Envío por Cobrar',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no válida',
            ]);
        }
    }
    public function addShippingMethod(Request $request)
    {
        try {
            $cartId = $request->input('cart_id');
            $methodName = $request->input('metodo');
            $direccionId = $request->input('direccion');
    
            if (!$cartId || !$methodName || !$direccionId) {
                return response()->json(['success' => false, 'error' => 'Faltan datos requeridos.']);
            }
    
            $direccion = DB::table('users_address')->where('id', $direccionId)->first();
            if (!$direccion) {
                return response()->json(['success' => false, 'error' => 'Dirección no encontrada.']);
            }
    
            // Eliminar cualquier método de envío existente para este carrito
            DB::table('cart_shippment')->where('cart_id', $cartId)->delete();
    
            // Inserción en la tabla `cart_shippment`
            DB::table('cart_shippment')->insert([
                'cart_id' => $cartId,
                'ShipmentMethod' => $methodName,
                'no_s' => '999999',
                'description' => "Envío por Cobrar",
                'unit_price' => 0,
                'discount' => 0,
                'final_price' => 0,
                'shippingcost_IVA' => 0,
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
                'predeterminada' => $direccion->predeterminada,
                'status' => $direccion->status,
                'cord_x' => $direccion->cord_x,
                'cord_y' => $direccion->cord_y,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
}
