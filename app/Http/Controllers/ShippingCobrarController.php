<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingCobrarController extends Controller
{
    public function handleCobrarShipping($request, $userId, $totalPrice)
    {
        // Obtener todas las direcciones del usuario
        $direcciones = DB::table('users_address')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();

        // Validar las direcciones (puedes agregar lógica adicional si es necesario)
        foreach ($direcciones as $direccion) {
            $direccion->esValida = true; // Asumimos que todas las direcciones son válidas
        }

        return [
            'direcciones' => $direcciones,
            'totalCart' => $totalPrice,
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
