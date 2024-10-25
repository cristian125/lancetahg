<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ShippingPaqueteriaController extends Controller
{
    private $id = 0;

    public function __autoload()
    {
        session_start();
    }

    public function handlePaqueteriaShipping(Request $request, $userId, $totalPrice)
    {
        $this->id = $userId;
        // Obtener todas las direcciones del usuario
        $direcciones = DB::table('users_address')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();

        // Inicializar variables
        $direccionValida = false;
        $costoEnvio = 500; // Costo de envío por paquetería
        $minimoCompra = 2000; // Minimo de compra para envío gratuito

        // Recorrer las direcciones para validar si alguna es válida para paquetería
        foreach ($direcciones as $direccion) {
            $codigoPostal = $direccion->codigo_postal;

            // Suponiendo que todos los códigos postales son válidos para paquetería
            $direccion->esPaqueteria = true;
            $direccionValida = true;

            // Verificar si el total del carrito cumple con el mínimo de compra para envío gratuito
            if ($totalPrice >= $minimoCompra) {
                $direccion->costoEnvio = 0;
            } else {
                $direccion->costoEnvio = $costoEnvio;
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

    // public function actualizarEnvioPaqueteria1(Request $request)
    // {
    //     $direccionId = $request->direccion;
    //     $totalPrice = $request->totalCart;

    //     // Obtener la dirección seleccionada
    //     $direccion = DB::table('users_address')->find($direccionId);

    //     $codigoPostal = $direccion->codigo_postal;

    //     // Asumimos que todos los códigos postales son válidos para paquetería
    //     $costoEnvio = $totalPrice >= 2000 ? 0 : 500; // Envío gratuito si supera el mínimo

    //     // Retornar los datos relevantes en formato JSON
    //     return response()->json([
    //         'costoEnvio' => $costoEnvio,
    //         'totalCart' => $totalPrice,
    //     ]);
    // }

    public function actualizarEnvioPaqueteria(Request $request)
    {
        // Obtener datos del request
        $direccionId = intval($request->direccion);
        
    
        // Obtener user_id de la base de datos
        $user = DB::table('users_address')->select('user_id')->where('id', $direccionId)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Dirección no encontrada'], 404);
        }



        $id = $user->user_id;

        // Crear instancia del cliente Guzzle
        $client = new Client();
        
        // Definir la URL base y los parámetros de la consulta
        $url = route('api.orders'); 
        
        // Hacer la petición GET con parámetros
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

        // dd($response->getBody()->getContents());
        // Obtener el cuerpo de la respuesta
        $body = $response->getBody()->getContents();

        // Decodificar JSON si es necesario
        $data = json_decode($body, true);

        // Hacer algo con los datos
        return response()->json($data);
    }

    public function addPaqueteriaMethod(Request $request)
    {
        try {
            $cartId = $request->input('cart_id');
            $direccionId = $request->input('direccion');
            $shippingCostWithIVA = $request->input('shipping_cost_with_iva'); // Recibir el costo con IVA
        
            // Verificar que los datos necesarios están presentes
            if (!$cartId || !$direccionId || !$shippingCostWithIVA) {
                return response()->json(['success' => false, 'error' => 'Faltan datos requeridos o costo de envío no proporcionado.']);
            }
        
            // Obtener los detalles de la dirección
            $direccion = DB::table('users_address')->where('id', $direccionId)->first();
            if (!$direccion) {
                return response()->json(['success' => false, 'error' => 'Dirección no encontrada.']);
            }
        
            // Calcular el total de productos con IVA (sin incluir el envío)
            $totalProductosConIVA = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->sum(DB::raw('final_price * quantity'));
    
            // Inserción en la tabla `cart_shippment`
            DB::table('cart_shippment')->insert([
                'cart_id' => $cartId,
                'ShipmentMethod' => 'EnvioPorPaqueteria',
                'no_s' => '999999', // Código de producto especial para el envío por paquetería
                'description' => "Envio por Paquetería",
                'unit_price' => $totalProductosConIVA, // Precio total de los productos con IVA, pero sin envío
                'discount' => 0, // Puedes cambiar este valor si aplicas algún descuento
                'final_price' => $totalProductosConIVA, // Precio final sin incluir envío
                'shippingcost_IVA' => $shippingCostWithIVA, // Guardar el costo con IVA del envío
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
                'status' => 1, // Agregar este campo para solucionar el error
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
            return response()->json(['success' => true]);
        
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    
    
    
    
}
