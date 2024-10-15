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
        $userId = auth()->id(); 
        $cartId = DB::table('carts')->where('user_id', $userId)->value('id'); 
        
        if (!$cartId) {
            return redirect()->route('cart.show')->with('error', 'Carrito no encontrado para el usuario actual.');
        }
    
        // Verificar si se seleccionó una tienda, si no, asignar la primera tienda disponible
        $storeId = $request->input('store_id') ?: DB::table('tiendas')->value('id');
        
        if (!$storeId) {
            return redirect()->route('cart.show')->with('error', 'No hay tiendas disponibles.');
        }
    
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
            'final_price' => 0,  
            'store_id' => $storeId,  // Se guarda el store_id correctamente
            'pickup_date' => $pickupDate,  
            'pickup_time' => $pickupTime,  
            'quantity' => 1,  
            'nombre' => 'StorePickup',  
            'calle' => 'StorePickup', 
            'no_int' => 'StorePickup', 
            'no_ext' => 'StorePickup', 
            'entre_calles' => 'StorePickup', 
            'colonia' => 'StorePickup', 
            'municipio' => 'StorePickup', 
            'codigo_postal' => '00000', 
            'pais' => 'StorePickup', 
            'referencias' => 'StorePickup', 
            'status' => 1, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return redirect()->route('cart.show')->with('success', 'Método de envío seleccionado correctamente.');
    }
    
    
        
}
