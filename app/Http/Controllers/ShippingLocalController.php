<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShippingLocalController extends ProductController
{
    private $id = 0;
    private $user_id = 0;
    private $TotalCart = 0;
    private $CostoEnvio = 0;

    /***
     * Inicializa el controlador
     */
    public function __construct($id = 0)
    {

        if (Auth::check()) {
            if ($id !== null) {
                $this->user_id = Auth::user()->id;
            } else {
                $this->user_id = $id;
            }

            $this->id = $this->getIdCart();
            $this->TotalCart = $this->getTotalFromCart();
        }
    }

    private function getIdCart()
    {
        $queryCarts = DB::table('carts')->select('id')->where('user_id', $this->user_id)->get();
        return $queryCarts[0]->id;
    }

    private function getTotalFromCart()
    {

        $total = 0;
        $queryCartItems = DB::table('cart_items')->select('final_price', 'quantity')->where('cart_id', $this->id)->get();

        foreach ($queryCartItems as $queryCartItem) {
            $totalLine = floatval($queryCartItem->final_price) * intval($queryCartItem->quantity);
            $total += $totalLine;
        }

        return $total;
    }
    /***
     * Comprueba codigo postal
     */

    public function handleLocalShipping(Request $request, $userId, $totalPrice)
    {
        // Obtener todas las direcciones del usuario
        $direcciones = DB::table('users_address')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();

        // Inicializar variables
        $direccionValida = false;
        $costoEnvio = 0;
        $minimoCompra = 0; // Asignar un valor inicial

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
                $direccionValida = true; // Marca que hay al menos una dirección válida

                // Determinar la zona y el costo de envío basado en la zona
                $zona = $codigoPostalData->zona_local;

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

                // Verificar si el total del carrito cumple con el mínimo de compra
                if ($totalPrice >= $minimoCompra) {
                    // Si cumple con el mínimo de compra, el envío es gratuito
                    $direccion->costoEnvio = 0;
                } else {
                    // Si no cumple, se cobra un costo de envío mayor
                    $direccion->costoEnvio = $costoEnvioBase * 1.66; // Aumento del costo en 66%
                }
            } else {
                $direccion->esLocal = false;
                $direccion->costoEnvio = 0; // O asigna un costo de envío predeterminado
            }
        }

        // Retornar los datos relevantes
        return [
            'direcciones' => $direcciones,
            'direccionValida' => $direccionValida,
            'costoEnvio' => $costoEnvio,
            'totalCart' => $totalPrice,
            'minimoCompra' => $minimoCompra,
        ];
    }

    public function actualizarEnvio(Request $request)
    {
        if ($request->id > 0) {
            $this->__construct($request->id);
        }

        $direccionId = $request->direccion;
        $totalPrice = $this->TotalCart;
        // Obtener la dirección seleccionada
        $direccion = DB::table('users_address')->find($direccionId);

        $codigoPostal = $direccion->codigo_postal;

        // Verificar si el código postal es válido para envío local
        $codigoPostalData = DB::table('codigos_postales')
            ->select('cp_restringido', 'zona_local')
            ->where('codigo', $codigoPostal)
            ->first();

        if ($codigoPostalData->cp_restringido == 0) {
            $zona = $codigoPostalData->zona_local;

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
                    $minimoCompra = -1;
                    $costoEnvioBase = -1;
                    break;
            }

            // Calcular el costo de envío
            $costoEnvio = ($totalPrice >= $minimoCompra) ? 0 : $costoEnvioBase;

            // Retornar los datos relevantes en formato JSON
            return response()->json([
                'zona' => $zona,
                'costoEnvio' => $costoEnvio,
                'totalCart' => $totalPrice,
            ]);
        }

        return response()->json(['error' => 'Código postal no válido para envío local2'], 400);
    }

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
                return response()->json(['success' => false, 'error' => 'Código postal no válido para envío local1']);
            }

            $zona = $codigoPostalData->zona_local;

            switch ($zona) {
                case '1A':
                    $minimoCompra = 800;
                    $costoEnvioBase = 150;
                    break;
                case '1B':
                    $minimoCompra = 1500;
                    $costoEnvioBase = 150;
                    break;
                case '1C':
                    $minimoCompra = 2000;
                    $costoEnvioBase = 230;
                    break;
                case '1D':
                    $minimoCompra = 2500;
                    $costoEnvioBase = 230;
                    break;
                case '1E':
                    $minimoCompra = 3000;
                    $costoEnvioBase = 250;
                    break;
                default:
                    $minimoCompra = 0;
                    $costoEnvioBase = 0;
                    break;
            }

            // Determinar si el costo de envío es 0 o se aplica un cargo adicional
            $costoEnvio = ($totalPrice >= $minimoCompra) ? 0 : $costoEnvioBase;

            // Calcular el costo de envío con IVA
            $shippingCostWithIVA = $costoEnvio * 1.16; // Supongamos que el IVA es del 16%

            // Inserción en la tabla `cart_shippment`
            DB::table('cart_shippment')->insert([
                'cart_id' => $cartId,
                'ShipmentMethod' => $methodName,
                'no_s' => '999998', // Código de producto especial para el envío
                'description' => "Envio",
                'unit_price' => 0,
                'discount' => 0,
                'final_price' => 0,
                'shippingcost_IVA' => $shippingCostWithIVA, // Guardar el costo de envío con IVA
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

    public function getShippingDetails($cartId)
    {
        // Consulta los detalles del envío para el carrito
        $shippment = DB::table('cart_shippment')
            ->where('cart_id', $cartId)
            ->first();

        if ($shippment) {
            // Prepara el envío como si fuera un ítem del carrito
            $shippingItem = [
                'id' => null,
                'unidad' => 'Envio',
                'description' => $shippment->description,
                'quantity' => 1,
                'unit_price' => $shippment->unit_price,
                'discount' => 0,
                'final_price' => $shippment->final_price,
                'product_code' => $shippment->no_s,
                'image' => 'storage/itemsview/shipping_icon.png', // Asegúrate de tener este icono
            ];

            return $shippingItem;
        }

        return null;
    }
    
}
