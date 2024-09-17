<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StorePickupController extends ProductController
{
    public function handleStorePickup(Request $request, $userId)
    {
        // Obtener las tiendas desde la base de datos
        $tiendas = DB::table('tiendas')->get();
        
        // Obtener la configuración del calendario desde la base de datos para la tienda seleccionada
        $calendarioConfig = DB::table('config_calendario')
            ->where('tienda_id', $tiendas->first()->id) // Asumiendo que seleccionas la primera tienda como ejemplo
            ->first();
    
        return [
            'tiendas' => $tiendas,
            'calendarioConfig' => $calendarioConfig,
        ];
    }

    public function saveStorePickup(Request $request)
    {

        
        $userId = auth()->id(); // Obtén el ID del usuario autenticado
        $cartId = DB::table('carts')->where('user_id', $userId)->value('id'); // Obtén el ID del carrito basado en el usuario autenticado
        
        if (!$cartId) {
            return redirect()->route('cart.show')->with('error', 'Carrito no encontrado para el usuario actual.');
        }
    
        $storeId = $request->input('store_id');
        $pickupDate = $request->input('pickup_date');
        $pickupTime = $request->input('pickup_time');
    
        // Inserta el método de envío en la tabla cart_shippment
        DB::table('cart_shippment')->insert([
            'cart_id' => $cartId,
            'ShipmentMethod' => 'RecogerEnTienda',
            'no_s' => '999999',  // Código de producto especial para "Recoger en Tienda"
            'description' => 'Recoger en Tienda',
            'unit_price' => 0,  // Asumimos que no hay costo para recoger en tienda
            'discount' => 0,
            'final_price' => 0,  // No hay cargo adicional
            'store_id' => $storeId,  // ID de la tienda seleccionada
            'pickup_date' => $pickupDate,  // Fecha seleccionada para recoger
            'pickup_time' => $pickupTime,  // Hora seleccionada para recoger
            'quantity' => 1,  // Siempre 1 para envío
            'nombre' => 'StorePickup',  
            'calle' => 'StorePickup', // Valor predeterminado para evitar errores
            'no_int' => 'StorePickup', // Valor predeterminado para evitar errores
            'no_ext' => 'StorePickup', // Valor predeterminado para evitar errores
            'entre_calles' => 'StorePickup', // Valor predeterminado para evitar errores
            'colonia' => 'StorePickup', // Valor predeterminado para evitar errores
            'municipio' => 'StorePickup', // Valor predeterminado para evitar errores
            'codigo_postal' => '00000', // Valor predeterminado para evitar errores
            'pais' => 'StorePickup', // Valor predeterminado para evitar errores
            'referencias' => 'StorePickup', // Valor predeterminado para evitar errores
            'status' => 1, // Establece un valor predeterminado para el campo `status`
            'created_at' => now(),
            'updated_at' => now(),
        ]);
 
        return redirect()->route('cart.show')->with('success', 'Método de envío seleccionado correctamente.');
    }
    

    
    
    
}
