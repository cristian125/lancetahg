<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShippingLocalController extends Controller
{
    private $id = 0;
    private $user_id = 0;
    private $TotalCart = 0;

    /**
     * Inicializa el controlador
     */
    public function __construct($id = 0)
    {
        if (Auth::check()) {
            if ($id !== null && $id > 0) {
                $this->user_id = $id;
            } else {
                $this->user_id = Auth::user()->id;
            }

            $this->id = $this->getIdCart();
            $this->TotalCart = $this->getTotalFromCart();
        }
    }

    private function getIdCart()
    {
        $queryCarts = DB::table('carts')
            ->select('id')
            ->where('user_id', $this->user_id)
            ->where('status', '2')
            ->first();

        return $queryCarts ? $queryCarts->id : null;
    }

    private function getTotalFromCart()
    {
        $total = 0;
        $queryCartItems = DB::table('cart_items')
            ->select('final_price', 'quantity')
            ->where('cart_id', $this->id)
            ->get();

        foreach ($queryCartItems as $queryCartItem) {
            $totalLine = floatval($queryCartItem->final_price) * intval($queryCartItem->quantity);
            $total += $totalLine;
        }

        return $total;
    }

    /**
     * Método para calcular el costo de envío de manera unificada
     */
    private function calculateShippingCost($totalPrice, $zona)
    {
        switch ($zona) {
            case '1A':
                $minimoCompra = 800;
                $costoEnvioBase = 150 + (150 * 0.16); // IVA 16%
                break;
            case '1B':
                $minimoCompra = 1500;
                $costoEnvioBase = 150 + (150 * 0.16);
                break;
            case '1C':
                $minimoCompra = 2000;
                $costoEnvioBase = 230 + (230 * 0.16);
                break;
            case '1D':
                $minimoCompra = 2500;
                $costoEnvioBase = 230 + (230 * 0.16);
                break;
            case '1E':
                $minimoCompra = 3000;
                $costoEnvioBase = 250 + (250 * 0.16);
                break;
            default:
                $minimoCompra = 0;
                $costoEnvioBase = 0;
                break;
        }

        if ($totalPrice >= $minimoCompra) {
            $costoEnvio = 0;
        } else {
            $costoEnvio = $costoEnvioBase * 1.66; // Aumento del costo en 66%
        }

        return [
            'costoEnvio' => round($costoEnvio, 2),
            'minimoCompra' => $minimoCompra,
        ];
    }

    /**
     * Actualizar el costo de envío basado en la dirección seleccionada
     */
    public function actualizarEnvio(Request $request)
    {
        if ($request->id > 0) {
            $this->__construct($request->id);
        }

        $direccionId = $request->direccion;
        $totalPrice = $this->TotalCart;

        // Obtener la dirección seleccionada
        $direccion = DB::table('users_address')->find($direccionId);

        if (!$direccion) {
            return response()->json(['error' => 'Dirección no encontrada.'], 404);
        }

        $codigoPostal = $direccion->codigo_postal;

        // Verificar si el código postal es válido para envío local
        $codigoPostalData = DB::table('codigos_postales')
            ->select('cp_restringido', 'zona_local')
            ->where('codigo', $codigoPostal)
            ->first();

        if ($codigoPostalData && $codigoPostalData->cp_restringido == 0) {
            $zona = $codigoPostalData->zona_local;

            // Calcular el costo de envío usando el método unificado
            $shippingData = $this->calculateShippingCost($totalPrice, $zona);
            $costoEnvio = $shippingData['costoEnvio'];

            // Retornar los datos relevantes en formato JSON
            return response()->json([
                'zona' => $zona,
                'costoEnvio' => $costoEnvio,
                'totalCart' => $totalPrice,
            ]);
        }

        return response()->json(['error' => 'Código postal no válido para envío local'], 400);
    }

    /**
     * Añadir el método de envío al carrito
     */
    public function addShippingMethod(Request $request)
    {
        try {
            $cartId = $request->input('cart_id');
            $methodName = $request->input('metodo');
            $direccionId = $request->input('direccion');
    
            // Verificar que los datos necesarios están presentes
            if (!$cartId || !$methodName || !$direccionId) {
                return response()->json(['success' => false, 'error' => 'Faltan datos requeridos.']);
            }
    
            // Consulta los elementos del carrito
            $cartItems = DB::table('cart_items')->where('cart_id', $cartId)->get();
    
            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'error' => 'No se encontraron elementos en el carrito.']);
            }
    
            // Obtener el total del carrito
            $totalPrice = $cartItems->sum(function ($item) {
                return $item->final_price * $item->quantity;
            });
    
            // Consultar la dirección seleccionada
            $direccion = DB::table('users_address')->where('id', $direccionId)->first();
            if (!$direccion) {
                return response()->json(['success' => false, 'error' => 'Dirección no encontrada.']);
            }
    
            // Calcular el costo de envío
            $codigoPostalData = DB::table('codigos_postales')
                ->select('cp_restringido', 'zona_local')
                ->where('codigo', $direccion->codigo_postal)
                ->first();
    
            if (!$codigoPostalData || $codigoPostalData->cp_restringido != 0) {
                return response()->json(['success' => false, 'error' => 'Código postal no válido para envío local']);
            }
    
            $zona = $codigoPostalData->zona_local;
    
            // Calcular el costo de envío usando el método unificado
            $shippingData = $this->calculateShippingCost($totalPrice, $zona);
            $costoEnvio = $shippingData['costoEnvio']; // Precio con IVA
            $unitPriceSinIVA = $costoEnvio / 1.16; // Precio sin IVA
            $ivaLiteral = $costoEnvio - $unitPriceSinIVA; // IVA literal
    
            // Inserción en la tabla `cart_shippment`
            DB::table('cart_shippment')->insert([
                'cart_id' => $cartId,
                'ShipmentMethod' => $methodName,
                'no_s' => '999998', // Código de producto especial para el envío
                'description' => "Envío",
                'unit_price' => round($unitPriceSinIVA, 2), // Precio sin IVA
                'discount' => 0,
                'final_price' => $costoEnvio, // Precio con IVA
                'shippingcost_IVA' => round($ivaLiteral, 2), // IVA literal
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
            \Log::error('Error al agregar el método de envío: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    

    public function handleLocalShipping()
    {
        $userId = $this->user_id;
        $totalPrice = $this->TotalCart;
    
        // Obtener todas las direcciones del usuario
        $direcciones = DB::table('users_address')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();
    
        // Inicializar variables
        $direccionValida = false;
        $costoEnvio = 0;
        $minimoCompra = 0;
    
        // Recorrer las direcciones para validar si alguna es válida
        foreach ($direcciones as $direccion) {
            $codigoPostal = $direccion->codigo_postal;
    
            // Verificar si el código postal es válido para envío local
            $codigoPostalData = DB::table('codigos_postales')
                ->where('codigo', $codigoPostal)
                ->where('cp_restringido', 0)
                ->first();
    
            if ($codigoPostalData) {
                $direccion->esLocal = true;
                $direccionValida = true;
    
                $zona = $codigoPostalData->zona_local;
    
                // Calcular el costo de envío usando el método unificado
                $shippingData = $this->calculateShippingCost($totalPrice, $zona);
                $direccion->costoEnvio = $shippingData['costoEnvio'];
            } else {
                $direccion->esLocal = false;
                $direccion->costoEnvio = 0;
            }
        }
    
        // Filtrar productos no elegibles para Envío Local
        $nonEligibleLocalShipping = DB::table('cart_items')
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
            ->where('cart_items.cart_id', $this->id)
            ->where('itemsdb.allow_local_shipping', 0) // Productos no elegibles
            ->select('itemsdb.nombre as product_name', 'itemsdb.no_s as product_code')
            ->get();
    
        // Retornar los datos relevantes como array
        return [
            'direcciones' => $direcciones,
            'direccionValida' => $direccionValida,
            'costoEnvio' => $costoEnvio,
            'totalCart' => $totalPrice,
            'minimoCompra' => $minimoCompra,

        ];
    }
    
    

}
