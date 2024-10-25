<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductosDestacadosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends ProductController
{

    public function proceedToPayment(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            return redirect()->route('login');
        }

        // Obtener el ID del carrito del usuario
        $cartId = DB::table('carts')
            ->where('user_id', $userId)
            ->value('id');

        // Obtener los detalles del envío desde `cart_shippment`
        $shippmentDetails = DB::table('cart_shippment')
            ->where('cart_id', $cartId)
            ->first();

        if (!$shippmentDetails) {
            return redirect()->back()->with('error', 'No se han encontrado detalles del envío.');
        }

        // Obtener los items del carrito con sus restricciones de envío
        $cartItems = DB::table('cart_items')
        ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
        ->join('inventario', 'cart_items.no_s', '=', 'inventario.no_s')
        ->where('cart_items.cart_id', $cartId)
        ->select(
            'cart_items.*',
            'inventario.cantidad_disponible',
            'itemsdb.allow_local_shipping',
            'itemsdb.allow_paqueteria_shipping',
            'itemsdb.allow_store_pickup',
            'itemsdb.allow_cobrar_shipping', // Añadir este campo
            'itemsdb.nombre as product_name'
        )
        ->get();
    

        // Verificar el stock de los productos en el carrito
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->cantidad_disponible) {
                return redirect()->back()->with('error', "No hay suficiente stock para el producto {$item->product_name}.");
            }
        }

        // Obtener el método de envío seleccionado
        $metodoEnvio = $shippmentDetails->ShipmentMethod;

        // Filtrar los items elegibles según el método de envío
        $eligibleCartItems = $cartItems->filter(function ($item) use ($metodoEnvio) {
            if ($metodoEnvio === 'EnvioLocal') {
                return $item->allow_local_shipping == 1;
            } elseif ($metodoEnvio === 'EnvioPorPaqueteria') {
                return $item->allow_paqueteria_shipping == 1;
            } elseif ($metodoEnvio === 'RecogerEnTienda') {
                return $item->allow_store_pickup == 1;
            } elseif ($metodoEnvio === 'EnvioPorCobrar') {
                return $item->allow_cobrar_shipping == 1; // Añadir esta condición
            }
            return false;
        });
        


        

        // Obtener los productos no elegibles
        $eligibleProductNos = $eligibleCartItems->pluck('no_s')->all();
        $nonEligibleItems = $cartItems->reject(function ($item) use ($eligibleProductNos) {
            return in_array($item->no_s, $eligibleProductNos);
        });

        // Si no hay productos elegibles, regresar con error
        if ($eligibleCartItems->isEmpty()) {
            return redirect()->back()->with('error', 'No hay productos elegibles para el método de envío seleccionado.');
        }

        // Recalcular el total basado en los productos elegibles
        $totalPrice = $eligibleCartItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });

        $shippingCost = floatval($shippmentDetails->shippingcost_IVA);

        // Calcular el total con IVA
        $totalPriceIVA = $totalPrice + $shippingCost;

        // Validaciones específicas para "RecogerEnTienda"
        $storeId = null;
        $pickupDate = null;
        $pickupTime = null;

        if ($metodoEnvio === 'RecogerEnTienda') {
            $pickupDate = new \DateTime($shippmentDetails->pickup_date);
            $pickupTime = new \DateTime($shippmentDetails->pickup_time);

            $dayOfWeek = $pickupDate->format('N');
            $hour = (int) $pickupTime->format('H');

            // Verificar si el día seleccionado es domingo
            if ($dayOfWeek == 7) {
                return redirect()->back()->with('error', 'No puedes seleccionar domingos para la recogida en tienda.');
            }

            // Verificar los horarios permitidos según el día de la semana
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                if ($hour < 10 || $hour > 18) {
                    return redirect()->back()->with('error', 'La hora seleccionada para recoger en tienda es inválida. Recuerda que los horarios son de 10:00 a 18:00 de lunes a viernes.');
                }
            } elseif ($dayOfWeek == 6) { // Sábado
                if ($hour < 10 || $hour > 15) {
                    return redirect()->back()->with('error', 'La hora seleccionada para recoger en tienda es inválida. Recuerda que los horarios son de 10:00 a 15:00 los sábados.');
                }
            }

            // Verificar que la hora es en punto (sin minutos intermedios)
            if ($pickupTime->format('i') !== '00') {
                return redirect()->back()->with('error', 'La hora seleccionada para recoger en tienda debe ser una hora exacta (sin minutos intermedios).');
            }

            // Comparar fechas sin tener en cuenta la hora
            $currentDate = new \DateTime();
            $currentDate->setTime(0, 0);
            $pickupDate->setTime(0, 0);

            // Verificar que no se seleccione el mismo día
            if ($pickupDate <= $currentDate) {
                return redirect()->back()->with('error', 'No puedes seleccionar el mismo día para la recogida en tienda.');
            }

            // Asignar el store_id del envío
            $storeId = $shippmentDetails->store_id;
        }

        // Verificar si ya existe un envío previo para el usuario
        $existingShippment = DB::table('shippments')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->first();

        // Eliminar el envío previo si existe
        if ($existingShippment) {
            DB::table('shippment_items')
                ->where('shippment_id', $existingShippment->id)
                ->delete();

            DB::table('shippments')
                ->where('id', $existingShippment->id)
                ->delete();
        }

        // Obtener el nombre de contacto y teléfono desde el formulario o usar los valores predeterminados del usuario
        $contactName = $request->input('contactName', auth()->user()->name);
        $contactPhone = $request->input('contactPhone', auth()->user()->phone);

        // Crear el nuevo shippment utilizando los datos de `cart_shippment`
        $shippmentId = DB::table('shippments')->insertGetId([
            'user_id' => $userId,
            'cart_id' => $cartId,
            'store_id' => $storeId,
            'shipping_method' => $shippmentDetails->ShipmentMethod,
            'shipping_cost' => $shippmentDetails->unit_price,
            'shipping_cost_IVA' => $shippingCost,
            'subtotal_sin_envio' => $totalPrice,
            'total_con_IVA' => $totalPriceIVA,
            'shipping_address' => $shippmentDetails->calle . ' ' . $shippmentDetails->no_ext,
            'no_int' => $shippmentDetails->no_int,
            'no_ext' => $shippmentDetails->no_ext,
            'entre_calles' => $shippmentDetails->entre_calles,
            'colonia' => $shippmentDetails->colonia,
            'municipio' => $shippmentDetails->municipio,
            'codigo_postal' => $shippmentDetails->codigo_postal,
            'pais' => $shippmentDetails->pais,
            'referencias' => $shippmentDetails->referencias,
            'cord_x' => $shippmentDetails->cord_x,
            'cord_y' => $shippmentDetails->cord_y,
            'nombre_contacto' => $contactName,
            'telefono_contacto' => $contactPhone,
            'email_contacto' => $request->user()->email,
            'pickup_date' => $shippmentDetails->pickup_date ?? null,
            'pickup_time' => $shippmentDetails->pickup_time ?? null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        // Insertar los productos elegibles en la tabla `shippment_items`
        foreach ($eligibleCartItems as $item) {
            $description = $item->product_name;

            if (is_null($description)) {
                $description = 'Descripción no disponible';
            }

            DB::table('shippment_items')->insert([
                'shippment_id' => $shippmentId,
                'no_s' => $item->no_s,
                'description' => $description,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'final_price' => $item->final_price,
                'quantity' => $item->quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Opcional: Informar al usuario si hubo productos no elegibles
        if ($nonEligibleItems->isNotEmpty()) {
            $nombresProductosNoElegibles = $nonEligibleItems->pluck('product_name')->implode(', ');
            return redirect('/checkout')->with('success', 'El pedido ha sido validado. Algunos productos no fueron incluidos porque no son elegibles para el método de envío seleccionado: ' . $nombresProductosNoElegibles);
        }

        if ($metodoEnvio === 'EnvioPorCobrar') {
            $shippingCost = 0; // El costo de envío se cobrará al entregar
        } else {
            $shippingCost = floatval($shippmentDetails->shippingcost_IVA);
        }
        
        return redirect('/checkout')->with('success', 'El pedido ha sido validado. Procede al pago.');
    }

    public function showCheckout(Request $request)
    {
        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }
    
        $userId = auth()->id();
    
        if (!$userId) {
            return redirect()->route('login');
        }
    
        // Obtener el envío pendiente del usuario desde la tabla 'shippments'
        $shippment = DB::table('shippments')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->select(
                'id',
                'cart_id',
                'no_ext',
                'entre_calles',
                'colonia',
                'municipio',
                'pais',
                'telefono_contacto',
                'shipping_address',
                'codigo_postal',
                'nombre_contacto',
                'shipping_cost_IVA',
                'shipping_method',
                'store_id',
                'shipping_cost',
                'pickup_date',
                'pickup_time'
            )
            ->first();
    
        if (!$shippment) {
            return view('checkout', ['error' => 'No se han encontrado detalles del envío.']);
        }
    
        // Obtener detalles de la tienda si el envío es para recoger en tienda
        $storeDetails = null;
        if ($shippment->shipping_method === 'RecogerEnTienda') {
            $storeDetails = DB::table('tiendas')
                ->where('id', $shippment->store_id)
                ->first();
        }
    
        // Obtener los items del carrito, incluyendo el campo grupo_iva
        $cartItems = DB::table('shippment_items')
            ->join('itemsdb', 'shippment_items.no_s', '=', 'itemsdb.no_s')
            ->where('shippment_id', $shippment->id)
            ->select(
                'shippment_items.no_s',
                'shippment_items.description',
                'shippment_items.quantity',
                'shippment_items.unit_price',
                'shippment_items.final_price',
                'shippment_items.discount',
                'itemsdb.unidad_medida_venta as unidad',
                'itemsdb.grupo_iva' // Incluir el campo grupo_iva
            )
            ->get();
    
        // Calcular los totales de productos con y sin IVA
        $totalConIVA = $cartItems->filter(function ($item) {
            return $item->grupo_iva === 'IVA16';
        })->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });
    
        $totalSinIVA = $cartItems->filter(function ($item) {
            return $item->grupo_iva === 'IVA0';
        })->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });
    
        // Calcular los totales
        $shippingCost = $shippment->shipping_cost_IVA;
    
        $totalPriceItems = $cartItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });
    
        // Calcular subtotal sin IVA
        $subtotalSinIVA = $totalPriceItems / 1.16;
    
        // Calcular el IVA total (16%)
        $ivaTotal = $subtotalSinIVA * 0.16;
    
        // Calcular el total final con IVA y envío
        $totalFinal = $totalPriceItems + $shippingCost;
    
        // Generar un 'oid' único
        $oid = uniqid('C-', true);
    
        // Guardar la transacción de pago
        DB::table('payment_transactions')->insert([
            'user_id' => $userId,
            'cart_id' => $shippment->cart_id,
            'oid' => $oid,
            'chargetotal' => number_format($totalFinal, 2, '.', ''),
            'checkoutoption' => 'combinedpage',
            'currency' => '484',
            'hash_algorithm' => 'HMACSHA256',
            'parentUri' => url('/checkout'),
            'responseFailURL' => url('/payment/callback/fail'),
            'responseSuccessURL' => url('/payment/callback/success'),
            'storename' => env('PAYMENT_STORENAME'),
            'timezone' => 'America/Mexico_City',
            'txndatetime' => now()->format('Y:m:d-H:i:s'),
            'txntype' => 'sale',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    
        // Insertar estado 1 (Confirmación de pedido) en order_history
        DB::table('order_history')->insert([
            'order_id' => $oid,
            'status' => 1, // Confirmación de pedido
            'status_1_confirmation_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Generar hash y preparar datos del formulario de pago
        $paymentData = [
            'oid' => $oid,
            'chargetotal' => number_format($totalFinal, 2, '.', ''),
            'checkoutoption' => 'combinedpage',
            'currency' => '484',
            'hash_algorithm' => 'HMACSHA256',
            'parentUri' => url('/checkout'),
            'responseFailURL' => url('/payment/callback/fail'),
            'responseSuccessURL' => url('/payment/callback/success'),
            'storename' => env('PAYMENT_STORENAME'),
            'timezone' => 'America/Mexico_City',
            'txndatetime' => now()->format('Y:m:d-H:i:s'),
            'txntype' => 'sale',
        ];
    
        ksort($paymentData);
        $secretKey = env('PAYMENT_SECRET_KEY');
        $hashString = implode('|', $paymentData);
        $hash = base64_encode(hash_hmac('sha256', $hashString, $secretKey, true));
        $paymentData['hashExtended'] = $hash;
    
        // Retornar la vista con las variables calculadas
        return view('checkout', [
            'shippment' => $shippment,
            'cartItems' => $cartItems,
            'subtotalSinIVA' => $subtotalSinIVA,
            'ivaTotal' => $ivaTotal,
            'totalPriceItems' => $totalPriceItems,
            'totalConIVA' => $totalConIVA, // Total con IVA
            'totalSinIVA' => $totalSinIVA, // Total sin IVA
            'shippingCost' => $shippingCost,
            'totalFinal' => $totalFinal,
            'error' => null,
            'paymentData' => $paymentData,
            'storeDetails' => $storeDetails
        ]);
    }
    
    public function updatePaymentMethod(Request $request)
{
    $userId = auth()->id();

    if (!$userId) {
        return response()->json(['success' => false, 'message' => 'Usuario no autenticado']);
    }

    $paymentMethod = $request->input('payment_method');

    if (!$paymentMethod) {
        return response()->json(['success' => false, 'message' => 'No se recibió el método de pago']);
    }

    // Obtener el 'oid' de la transacción de pago más reciente del usuario
    $paymentTransaction = DB::table('payment_transactions')
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$paymentTransaction) {
        return response()->json(['success' => false, 'message' => 'No se encontró una transacción de pago para el usuario']);
    }

    $oid = $paymentTransaction->oid;

    // Actualizar el método de pago en 'order_history'
    DB::table('order_history')
        ->where('order_id', $oid)
        ->update([
            'payment_method' => $paymentMethod,
            'updated_at' => now(),
        ]);

    // Devolver una respuesta exitosa
    return response()->json(['success' => true]);
}

    
    
}
