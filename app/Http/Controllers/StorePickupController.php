<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorePickupController extends ProductController
{
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
            ->where('status', '1') // Carrito activo
            ->first();

        return $cart ? $cart->id : null;
    }
    public function handleStorePickup(Request $request, $userId)
    {
        // Obtener las tiendas desde la base de datos
        $tiendas = DB::table('tiendas')->get();
    
        // Obtener la configuración del calendario desde la base de datos para la tienda seleccionada
        $calendarioConfig = DB::table('config_calendario')
            ->where('tienda_id', $tiendas->first()->id)
            ->first();
    
        // Filtrar productos no elegibles
        $nonEligibleStorePickup = collect();
        if ($this->cart_id) {
            $nonEligibleStorePickup = DB::table('cart_items')
                ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
                ->where('cart_items.cart_id', $this->cart_id)
                ->where('itemsdb.allow_store_pickup', 0)
                ->select('itemsdb.nombre as product_name', 'itemsdb.no_s as product_code')
                ->get();
        }
    
        return [
            'tiendas' => $tiendas,
            'calendarioConfig' => $calendarioConfig,
            'nonEligibleStorePickup' => $nonEligibleStorePickup,
        ];
    }
    
    public function saveStorePickup(Request $request)
    {
        $userId = auth()->id();
        $cartId = DB::table('carts')
        ->where('user_id', $userId)
        ->where('status', 1) // Asegura que el carrito tenga status 1
        ->value('id');
    
        if (!$cartId) {
            return redirect()->route('cart.show')->with('error', 'Carrito no encontrado para el usuario actual.');
        }
    
        // Verificar si se seleccionó una tienda, si no, asignar la primera tienda disponible
        $storeId = $request->input('store_id') ?: DB::table('tiendas')->value('id');
    
        if (!$storeId) {
            return redirect()->route('cart.show')->with('error', 'No hay tiendas disponibles.');
        }
    
        // Obtener información de la tienda seleccionada
        $store = DB::table('tiendas')->where('id', $storeId)->first();
        if (!$store) {
            return redirect()->route('cart.show')->with('error', 'La tienda seleccionada no existe.');
        }
    
        // Eliminar cualquier registro existente en `cart_shippment` para este `cart_id`
        DB::table('cart_shippment')->where('cart_id', $cartId)->delete();
    
        // Utilizar valores predeterminados válidos para `pickup_date` y `pickup_time`
        $pickupDate = null;
        $pickupTime = null;
    
        // Inserta el método de envío en la tabla `cart_shippment`
        DB::table('cart_shippment')->insert([
            'cart_id' => $cartId,
            'ShipmentMethod' => 'RecogerEnTienda',
            'no_s' => '999999',
            'description' => 'Recoger en Tienda',
            'unit_price' => 0,
            'discount' => 0,
            'final_price' => 0,
            'store_id' => $storeId,
            'pickup_date' => $pickupDate,
            'pickup_time' => $pickupTime,
            'quantity' => 1,
            'nombre' => $store->nombre,
            'calle' => $store->direccion,
            'no_int' => $store->no_int ?? '',
            'no_ext' => $store->no_ext ?? '',
            'entre_calles' => $store->entre_calles ?? '',
            'colonia' => $store->colonia ?? '',
            'municipio' => $store->municipio ?? '',
            'codigo_postal' => $store->codigo_postal ?? '00000',
            'pais' => $store->pais ?? 'México',
            'referencias' => 'Recoger en Tienda',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return redirect()->route('cart.show')->with('success', 'Método de envío seleccionado correctamente. La tienda se contactará contigo para coordinar la recogida.');
    }
    
    

    private function verificarExistenciasEnTiendas($productCodes, $tiendas)
    {
        // Construir la cadena de códigos de productos para la solicitud API
        $codigoProductos = implode('+', $productCodes);

        // URL de la API
        $url = "http://lan-ec.ddns.me:8084/exsapibc/{$codigoProductos}";

        // Hacer la solicitud a la API
        $response = file_get_contents($url);

        // Decodificar la respuesta JSON
        $data = json_decode($response, true);

        // Estructurar la respuesta
        $storeProductStock = [];

        foreach ($data as $item) {
            $storeCode = $item['a'];
            $productCode = $item['p'];
            $quantity = $item['c'];

            // Inicializar el array de tienda si no está configurado
            if (!isset($storeProductStock[$storeCode])) {
                $storeProductStock[$storeCode] = [];
            }

            // Agregar la cantidad de producto a la tienda
            $storeProductStock[$storeCode][$productCode] = $quantity;
        }

        // Filtrar tiendas que tienen existencias de todos los productos
        $tiendasConStock = collect();

        foreach ($tiendas as $tienda) {
            if (!$tienda->codigo_tienda) {
                continue;
            }

            // Saltar tiendas que no están en la respuesta de la API
            if (!isset($storeProductStock[$tienda->codigo_tienda])) {
                continue;
            }

            $hasAllProducts = true;

            foreach ($productCodes as $productCode) {
                // Verificar si todos los productos están disponibles en la tienda
                if (!isset($storeProductStock[$tienda->codigo_tienda][$productCode]) || $storeProductStock[$tienda->codigo_tienda][$productCode] <= 0) {
                    $hasAllProducts = false;
                    break;
                }
            }

            if ($hasAllProducts) {
                $tiendasConStock->push($tienda);
            }
        }

        return $tiendasConStock;
    }

    public function ajaxVerificarExistencias(Request $request)
    {
        $userId = Auth::id();

        // Obtener el ID del carrito del usuario desde la tabla `carts`
        $cartId = DB::table('carts')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->value('id');

        if (!$cartId) {
            return response()->json(['tiendas' => [], 'error' => 'No se encontró el carrito para el usuario.']);
        }

        // Obtener los productos del carrito usando el cart_id
        $cartItems = DB::table('cart_items')
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
            ->where('cart_items.cart_id', $cartId)
            ->select('itemsdb.no_s as product_code')
            ->get();

        // Convertir los elementos del carrito a una colección y extraer los códigos de productos
        $productCodes = $cartItems->pluck('product_code')->map(function ($code) {
            return str_pad($code, 6, "0", STR_PAD_LEFT);
        })->toArray();

        // Obtener las tiendas con el código de tienda
        $tiendas = DB::table('tiendas')
            ->whereNotNull('codigo_tienda')
            ->get();

        // Verificar existencias en las tiendas usando la función
        $tiendasDisponibles = $this->verificarExistenciasEnTiendas($productCodes, $tiendas);

        // Si no hay tiendas disponibles, enviar una respuesta indicando el error
        if ($tiendasDisponibles->isEmpty()) {
            return response()->json([
                'tiendas' => [],
                'mensaje' => 'Lo sentimos, no hay tiendas disponibles con stock para todos los productos.',
            ]);
        }

        // Preparar la respuesta con las tiendas disponibles
        $tiendasArray = $tiendasDisponibles->map(function ($tienda) {
            return [
                'id' => $tienda->id,
                'nombre' => $tienda->nombre,
                'direccion' => $tienda->direccion,
                'telefono' => $tienda->telefono,
                'horario_semana' => $tienda->horario_semana,
                'horario_sabado' => $tienda->horario_sabado,
                'google_maps_url' => $tienda->google_maps_url,
            ];
        });

        return response()->json(['tiendas' => $tiendasArray]);
    }
}
