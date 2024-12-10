<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductosDestacadosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends ProductController
{
    public static function getId()
    {
        $userId = auth()->id();
        $cartId = DB::table('carts')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->value('id');
        return $cartId;
    }

    public function proceedToPayment(Request $request)
    {

        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }

        $userId = auth()->id();

        if (!$userId) {
            return redirect()->route('login');
        }
        session(['terms_accepted' => true]);

        $cartId = $this->getId();

        $shippmentDetails = DB::table('cart_shippment')
            ->where('cart_id', $cartId)
            ->first();

        if (!$shippmentDetails) {
            return redirect()->back()->with('error', 'No se han encontrado detalles del envío.');
        }

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
                'itemsdb.allow_cobrar_shipping',
                'itemsdb.nombre as product_name'
            )
            ->get();

        foreach ($cartItems as $item) {
            if ($item->quantity > $item->cantidad_disponible) {
                return redirect()->back()->with('error', "No hay suficiente stock para el producto {$item->product_name}.");
            }
        }

        $metodoEnvio = $shippmentDetails->ShipmentMethod;

        $eligibleCartItems = $cartItems->filter(function ($item) use ($metodoEnvio) {
            if ($metodoEnvio === 'EnvioLocal') {
                return $item->allow_local_shipping == 1;
            } elseif ($metodoEnvio === 'EnvioPorPaqueteria') {
                return $item->allow_paqueteria_shipping == 1;
            } elseif ($metodoEnvio === 'RecogerEnTienda') {
                return $item->allow_store_pickup == 1;
            } elseif ($metodoEnvio === 'EnvioPorCobrar') {
                return $item->allow_cobrar_shipping == 1;
            }
            return false;
        });
        
        $eligibleProductNos = $eligibleCartItems->pluck('no_s')->all();
        $nonEligibleItems = $cartItems->reject(function ($item) use ($eligibleProductNos) {
            return in_array($item->no_s, $eligibleProductNos);
        });

        if ($eligibleCartItems->isEmpty()) {
            return redirect()->back()->with('error', 'No hay productos elegibles para el método de envío seleccionado.');
        }

        $totalPrice = $eligibleCartItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });

        $shippingCost = floatval($shippmentDetails->shippingcost_IVA);

        $totalPriceIVA = $totalPrice + $shippingCost;

        $storeId = null;
        $pickupDate = null;
        $pickupTime = null;

        if ($metodoEnvio === 'RecogerEnTienda') {
            $pickupDate = new \DateTime($shippmentDetails->pickup_date);
            $pickupTime = new \DateTime($shippmentDetails->pickup_time);

            $dayOfWeek = $pickupDate->format('N');
            $hour = (int) $pickupTime->format('H');

            if ($dayOfWeek == 7) {
                return redirect()->back()->with('error', 'No puedes seleccionar domingos para la recogida en tienda.');
            }

            $currentDate = new \DateTime();
            $currentDate->setTime(0, 0);
            $pickupDate->setTime(0, 0);

            $storeId = $shippmentDetails->store_id;
        }

        $existingShippment = DB::table('order_shippment')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->first();

        if ($existingShippment) {
            DB::table('shippment_items')
                ->where('shippment_id', $existingShippment->id)
                ->delete();

            DB::table('order_shippment')
                ->where('id', $existingShippment->id)
                ->delete();
        }

        $contactName = $request->input('contactName', auth()->user()->name);
        $contactPhone = $request->input('contactPhone', auth()->user()->phone);

        $shippmentUpdate = DB::table('cart_shippment')
            ->where('cart_id', $cartId)
            ->update(['contactName' => $contactName, 'contactPhone' => $contactPhone, 'contactEmail' => $request->user()->email]);

        $shippmentId = $shippmentDetails->id;

        $check = DB::table('shippment_items')->where('shippment_id', $shippmentId)->get();

        if (count($check) > 0) {
            DB::table('shippment_items')->where('shippment_id', $shippmentId)->delete();
        }

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

        if ($nonEligibleItems->isNotEmpty()) {
            $nombresProductosNoElegibles = $nonEligibleItems->pluck('product_name')->implode(', ');
            return redirect('/checkout')->with('success', 'El pedido ha sido validado. Algunos productos no fueron incluidos porque no son elegibles para el método de envío seleccionado: ' . $nombresProductosNoElegibles);
        }

        if ($metodoEnvio === 'EnvioPorCobrar') {
            $shippingCost = 0;
        } else {
            $shippingCost = floatval($shippmentDetails->shippingcost_IVA);
        }

        return redirect('/checkout')->with('success', 'El pedido ha sido validado. Procede al pago.');
    }

    public function showCheckout(Request $request)
    {
        // Verificar que los términos han sido aceptados
        if (!session('terms_accepted')) {
            return redirect('/carrito')->with('error', 'Debe aceptar los términos y condiciones para continuar.');
        }
            // Limpiar `terms_accepted` después de acceder
    session()->forget('terms_accepted');
        $cart = DB::table('carts')
            ->where('user_id', auth()->id())
            ->where('status', 1)
            ->first();

        if (!$cart) {
            return redirect('/carrito')->with('error', 'No tienes un carrito activo.');
        }

        $cartId = $cart->id;

        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimiento'));
        }

        $shippment = DB::table('cart_shippment')
            ->where('cart_id', $cartId)
            ->select(
                'id',
                'cart_id',
                'no_ext',
                'entre_calles',
                'colonia',
                'municipio',
                'pais',
                'contactPhone',
                'calle',
                'codigo_postal',
                'contactName',
                'contactEmail',
                'shippingcost_IVA',
                'final_price',
                'ShipmentMethod',
                'store_id',
                'unit_price',
                'pickup_date',
                'pickup_time'
            )
            ->first();

        if (!$shippment) {
            return view('checkout', ['error' => 'No se han encontrado detalles del envío.']);
        }

        $storeDetails = null;
        if ($shippment->ShipmentMethod === 'RecogerEnTienda') {
            $storeDetails = DB::table('tiendas')
                ->where('id', $shippment->store_id)
                ->first();
        }

        $allowedColumn = null;

        if ($shippment->ShipmentMethod === 'EnvioPorPaqueteria') {
            $allowedColumn = 'allow_paqueteria_shipping';
        } elseif ($shippment->ShipmentMethod === 'RecogerEnTienda') {
            $allowedColumn = 'allow_store_pickup';
        } elseif ($shippment->ShipmentMethod === 'EnvioLocal') {
            $allowedColumn = 'allow_local_shipping';
        } elseif ($shippment->ShipmentMethod === 'EnvioPorCobrar') {
            $allowedColumn = 'allow_cobrar_shipping';
        }

        if (!$allowedColumn) {
            return redirect('/carrito')->with('error', 'Método de envío no válido.');
        }

        $cartItems = DB::table('cart_items')
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
            ->where('cart_items.cart_id', $cartId)
            ->where("itemsdb.$allowedColumn", 1) // Filtrar ítems elegibles
            ->select(
                'cart_items.no_s',
                'cart_items.description',
                'cart_items.quantity',
                'cart_items.unit_price',
                'cart_items.vat',
                'cart_items.final_price',
                'cart_items.discount',
                'itemsdb.unidad_medida_venta as unidad',
                'itemsdb.grupo_iva'
            )
            ->get();

        $totalConIVA = $cartItems->filter(function ($item) {
            return $item->grupo_iva === 'IVA16';
        })->sum(function ($item) {
            return $item->final_price;
        });
        
        $totalSinIVA = $cartItems->filter(function ($item) {
            return $item->grupo_iva === 'IVA0';
        })->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });

        $shippingCost = $shippment->final_price;
        $totalPriceItems = $cartItems->sum(function ($item) {
            return $item->final_price;
        });

        $subtotalSinIVA = $totalPriceItems / 1.16;
        $ivaTotal = $subtotalSinIVA * 0.16;
        $totalFinal = $totalPriceItems + $shippingCost;

        $oid = uniqid('C-', true);

        DB::table('payment_transactions')->insert([
            'user_id' => auth()->id(),
            'cart_id' => $cartId,
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
            'updated_at' => now(),
        ]);

        DB::table('order_history')->insert([
            'order_id' => $oid,
            'status' => 1,
            'status_1_confirmation_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

        return view('checkout', [
            'shippment' => $shippment,
            'cartItems' => $cartItems,
            'subtotalSinIVA' => $subtotalSinIVA,
            'ivaTotal' => $ivaTotal,
            'totalPriceItems' => $totalPriceItems,
            'totalConIVA' => $totalConIVA,
            'totalSinIVA' => $totalSinIVA,
            'shippingCost' => $shippingCost,
            'totalFinal' => $totalFinal,
            'error' => null,
            'paymentData' => $paymentData,
            'storeDetails' => $storeDetails,
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

        $paymentTransaction = DB::table('payment_transactions')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$paymentTransaction) {
            return response()->json(['success' => false, 'message' => 'No se encontró una transacción de pago para el usuario']);
        }

        $oid = $paymentTransaction->oid;

        DB::table('order_history')
            ->where('order_id', $oid)
            ->update([
                'payment_method' => $paymentMethod,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function processCod(Request $request)
    {

        DB::beginTransaction();

        try {

            $userId = auth()->id();

            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Debes iniciar sesión para proceder al pedido.']);
            }

            $shippment = DB::table('shippments')
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->first();

            if (!$shippment) {
                return response()->json(['success' => false, 'message' => 'No se encontraron detalles del envío.']);
            }

            $shippmentItems = DB::table('shippment_items')
                ->where('shippment_id', $shippment->id)
                ->get();

            if ($shippmentItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No se encontraron productos para procesar.']);
            }

            $subtotal = $shippmentItems->sum(function ($item) {
                return $item->final_price * $item->quantity;
            });

            $totalDiscount = $shippmentItems->sum(function ($item) {
                return ($item->unit_price * $item->quantity) * ($item->discount / 100);
            });

            $shippingCost = $shippment->shipping_cost_IVA;
            $totalConIva = $subtotal + $shippingCost;
            $completeAddress = $this->formatCompleteAddress($shippment);

            $cartId = $this->getId();
            dd($cartId);

            $orderId = DB::table('orders')->insertGetId([
                'cart_id' => $cartId,
                'user_id' => $userId,
                'oid' => uniqid(),
                'total' => $totalConIva,
                'shipping_address' => $completeAddress,
                'shipping_cost' => $shippingCost,
                'discount' => $totalDiscount,
                'shipment_method' => $shippment->shipping_method,
                'subtotal_sin_envio' => $subtotal,
                'total_con_iva' => $totalConIva,
                'created_at' => now(),
                'updated_at' => now(),
                'current_state' => 0,
            ]);

            DB::table('order_shippment')->insert([
                'order_id' => $orderId,
                'user_id' => $userId,
                'cart_id' => $shippment->cart_id,
                'store_id' => $shippment->store_id,
                'pickup_date' => $shippment->pickup_date,
                'pickup_time' => $shippment->pickup_time,
                'shipping_method' => $shippment->shipping_method,
                'shipping_cost' => $shippment->shipping_cost,
                'shipping_cost_IVA' => $shippment->shipping_cost_IVA,
                'subtotal_sin_envio' => $subtotal,
                'total_con_IVA' => $totalConIva,
                'shipping_address' => $completeAddress,
                'no_int' => $shippment->no_int,
                'no_ext' => $shippment->no_ext,
                'entre_calles' => $shippment->entre_calles,
                'colonia' => $shippment->colonia,
                'municipio' => $shippment->municipio,
                'pais' => $shippment->pais,
                'referencias' => $shippment->referencias,
                'cord_x' => $shippment->cord_x,
                'cord_y' => $shippment->cord_y,
                'codigo_postal' => $shippment->codigo_postal,
                'nombre_contacto' => $shippment->contactName,
                'telefono_contacto' => $shippment->contactPhone,
                'email_contacto' => $shippment->contactEmail,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($shippmentItems as $item) {
                $productDetails = DB::table('itemsdb')->where('no_s', $item->no_s)->first();
                $unitDetails = DB::table('items_unidades')->where('item_no', $item->no_s)->first();
                $finalPrice = $item->unit_price - ($item->unit_price * ($item->discount / 100));
                $totalPrice = $finalPrice * $item->quantity;

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item->no_s,
                    'description' => $productDetails->nombre,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $totalPrice,
                    'discount' => $item->discount,
                    'iva_rate' => $productDetails->grupo_iva,
                    'length' => $unitDetails->length ?? null,
                    'width' => $unitDetails->width ?? null,
                    'depth' => $unitDetails->height ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('inventario')
                    ->where('no_s', $item->no_s)
                    ->decrement('cantidad_disponible', $item->quantity);
            }

            DB::table('cart_items')->where('cart_id', $shippment->cart_id)->delete();

            if (DB::table('cart_items')->where('cart_id', $shippment->cart_id)->count() == 0) {
                DB::table('carts')->where('id', $shippment->cart_id)->delete();
            }

            DB::table('shippments')->where('id', $shippment->id)->update(['status' => 'completed']);

            DB::table('order_history')->insert([
                'order_id' => $orderId,
                'status' => 3,
                'status_2_payment_process_at' => now(),
                'status_3_paid_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_payment')->insert([
                'order_id' => $orderId,
                'chargetotal' => $totalConIva,
                'request_type' => 'COD',
                'txtn_processed' => null,
                'timezone' => $shippment->timezone ?? 'UTC',
                'processor_network_information' => null,
                'associationResponseMessage' => null,
                'ccbrand' => null,
                'refnumber' => null,
                'cardnumber' => null,
                'ipgTransactionId' => null,
                'fail_reason' => null,
                'status' => 'COD',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $tienda = DB::table('tiendas')->where('id', $shippment->store_id)->first();
            $correoTienda = $tienda->correo ?? 'soporte@lancetahg.com';

            $orderItems = DB::table('order_items')
                ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s')
                ->select('order_items.*', 'itemsdb.no_s', 'itemsdb.nombre as product_name')
                ->where('order_id', $orderId)
                ->get();

            $order = DB::table('orders')->where('id', $orderId)->first();
            $pickupDate = $shippment->pickup_date;
            $pickupTime = $shippment->pickup_time;

            Mail::send('emails.order', compact('order', 'orderItems', 'pickupDate', 'pickupTime'), function ($message) use ($correoTienda, $orderId) {
                $message->to($correoTienda)
                    ->bcc('soporte@lancetahg.com')
                    ->subject('Nueva orden de pedido #' . $orderId);
            });

            DB::commit();

            return response()->json(['success' => true, 'message' => '¡Pedido creado con éxito!']);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Error en processCod: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al procesar tu pedido. Por favor, inténtalo de nuevo.']);
        }
    }

    private function formatCompleteAddress($shippment)
    {
        return trim("{$shippment->shipping_address} {$shippment->no_ext} {$shippment->no_int}, Entre Calles: {$shippment->entre_calles}, Colonia: {$shippment->colonia}, Municipio: {$shippment->municipio}, País: {$shippment->pais}, Código Postal: {$shippment->codigo_postal}");
    }
}
