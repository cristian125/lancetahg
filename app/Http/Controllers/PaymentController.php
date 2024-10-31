<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PaymentController extends Controller
{



    private function logPaymentRequest(array $responseData, string $requestType)
    {

        $oid = $responseData['oid'] ?? null;
        $transaction = DB::table('payment_transactions')->where('oid', $oid)->first();


        $userId = $transaction->user_id ?? null;

        DB::table('payment_requests_log')->insert([
            'user_id' => $userId,
            'request_type' => $requestType,
            'oid' => $oid,
            'txndate_processed' => isset($responseData['txndate_processed']) ? date('Y-m-d H:i:s', strtotime($responseData['txndate_processed'])) : null,
            'ccbin' => $responseData['ccbin'] ?? null,
            'timezone' => $responseData['timezone'] ?? null,
            'processor_network_information' => $transaction->processor_network_information ?? $responseData['processor_network_information'] ?? null,
            'cccountry' => $responseData['cccountry'] ?? null,
            'expmonth' => $responseData['expmonth'] ?? null,
            'hash_algorithm' => $responseData['hash_algorithm'] ?? null,
            'endpointTransactionId' => $transaction->endpointTransactionId ?? $responseData['endpointTransactionId'] ?? null,
            'currency' => $responseData['currency'] ?? null,
            'processor_response_code' => $transaction->processor_response_code ?? $responseData['processor_response_code'] ?? null,
            'chargetotal' => $responseData['chargetotal'] ?? null,
            'terminal_id' => $transaction->terminal_id ?? $responseData['terminal_id'] ?? null,
            'associationResponseCode' => $transaction->associationResponseCode ?? $responseData['associationResponseCode'] ?? null,
            'approval_code' => $responseData['approval_code'] ?? null,
            'expyear' => $responseData['expyear'] ?? null,
            'response_hash' => $responseData['response_hash'] ?? null,
            'response_code_3dsecure' => $responseData['response_code_3dsecure'] ?? null,
            'tdate' => $responseData['tdate'] ?? null,
            'installments_interest' => $responseData['installments_interest'] ?? null,
            'associationResponseMessage' => $transaction->associationResponseMessage ?? $responseData['associationResponseMessage'] ?? null,
            'bname' => $responseData['bname'] ?? null,
            'ccbrand' => $responseData['ccbrand'] ?? null,
            'refnumber' => $transaction->refnumber ?? $responseData['refnumber'] ?? null,
            'txntype' => $responseData['txntype'] ?? null,
            'paymentMethod' => $responseData['paymentMethod'] ?? null,
            'txndatetime' => isset($responseData['txndatetime']) ? date('Y-m-d H:i:s', strtotime(str_replace('-', ' ', $responseData['txndatetime']))) : null,
            'cardnumber' => $responseData['cardnumber'] ?? null,
            'ipgTransactionId' => $responseData['ipgTransactionId'] ?? null,
            'fail_reason' => $responseData['fail_reason'] ?? null,
            'status' => $responseData['status'] ?? null,
            'created_at' => now(),
        ]);
    }
    private function formatCompleteAddress($shippment)
    {
        // Crear un array para almacenar los componentes de la dirección
        $addressComponents = [];

        if (!empty($shippment->shipping_address)) {
            $addressComponents[] = $shippment->shipping_address;
        }

        if (!empty($shippment->no_ext)) {
            $addressComponents[] = "No. Ext: {$shippment->no_ext}";
        }

        if (!empty($shippment->no_int)) {
            $addressComponents[] = "No. Int: {$shippment->no_int}";
        }

        if (!empty($shippment->entre_calles)) {
            $addressComponents[] = "Entre Calles: {$shippment->entre_calles}";
        }

        if (!empty($shippment->colonia)) {
            $addressComponents[] = "Colonia: {$shippment->colonia}";
        }

        if (!empty($shippment->municipio)) {
            $addressComponents[] = "Municipio: {$shippment->municipio}";
        }

        if (!empty($shippment->pais)) {
            $addressComponents[] = "País: {$shippment->pais}";
        }

        if (!empty($shippment->codigo_postal)) {
            $addressComponents[] = "Código Postal: {$shippment->codigo_postal}";
        }

        if (!empty($shippment->referencias)) {
            $addressComponents[] = "Referencias: {$shippment->referencias}";
        }

        // Concatenar los componentes con comas
        $completeAddress = implode(', ', $addressComponents);

        // Asegurarse de que no exceda los 255 caracteres
        return substr($completeAddress, 0, 255);
    }

    public function handleSuccess(Request $request)
    {
        try {
            // Obtener los datos de la respuesta del request
            $responseData = $request->all();

            // Validar el hash de la respuesta para asegurarse de que sea válido
            if (!$this->validateResponseHash($responseData)) {
                Log::warning('Hash de respuesta inválido:', $responseData);
                return redirect()->route('payment.fail')->with('error', 'Respuesta inválida. Por favor, contacte con soporte.');
            }

            // Registrar la solicitud del pago como éxito
            $this->logPaymentRequest($responseData, 'success');

            // Obtener el OID (identificador del pedido)
            $oid = $responseData['oid'];

            // Buscar la transacción en la base de datos utilizando el OID
            $transaction = DB::table('payment_transactions')->where('oid', $oid)->first();

            if (!$transaction) {
                return redirect()->route('cart.show')->with('error', 'No se pudo encontrar la transacción.');
            }

            $userId = $transaction->user_id;

            if (!$userId) {
                return redirect()->route('login')->with('error', 'Debe iniciar sesión para completar el proceso de compra.');
            }

            // Buscar el envío pendiente del usuario
            $shippment = DB::table('shippments')->where('user_id', $userId)->where('status', 'pending')->first();

            if (!$shippment) {
                return redirect()->route('cart.show')->with('error', 'No se encontraron detalles del envío.');
            }

            // Obtener los artículos del envío
            $shippmentItems = DB::table('shippment_items')->where('shippment_id', $shippment->id)->get();

            if ($shippmentItems->isEmpty()) {
                return redirect()->route('cart.show')->with('error', 'No se encontraron productos para procesar.');
            }

            // Calcular el subtotal
            $subtotal = $shippmentItems->sum(function ($item) {
                return $item->final_price * $item->quantity;
            });

            // Calcular el descuento total
            $totalDiscount = $shippmentItems->sum(function ($item) {
                return ($item->unit_price * $item->quantity) * ($item->discount / 100);
            });

            $shippingCost = $shippment->shipping_cost_IVA;
            $totalConIva = $subtotal + $shippingCost;

            // Concatenar toda la dirección
            $completeAddress = $this->formatCompleteAddress($shippment);

            // Insertar la orden en la tabla `orders` con la dirección completa
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'oid' => $oid,
                'total' => $transaction->chargetotal,
                'shipping_address' => $completeAddress, // Dirección completa
                'shipping_cost' => $shippingCost,
                'discount' => $totalDiscount,
                'shipment_method' => $shippment->shipping_method,
                'subtotal_sin_envio' => $subtotal,
                'total_con_iva' => $totalConIva,
                'created_at' => now(),
                'updated_at' => now(),
                'current_state' => 2 // Asegúrate de establecer este campo si es necesario
            ]);

            // Verificar el estado del pago (APROBADO o FALLADO)
            $paymentStatus = $responseData['status'] ?? 'FALLADO';

            // Convertir la fecha `txndate_processed` al formato MySQL (YYYY-MM-DD HH:MM:SS)
            $txndateProcessed = isset($responseData['txndate_processed']) ?
                Carbon::createFromFormat('d/m/y h:i:s A', $responseData['txndate_processed'])->format('Y-m-d H:i:s') :
                null;  // Si no existe, asignar NULL

            // Insertar el registro de pago en `order_payment`
            DB::table('order_payment')->insert([
                'order_id' => $orderId,
                'chargetotal' => $transaction->chargetotal,
                'request_type' => $responseData['txntype'] ?? 'unknown',  // Cambia 'unknown' por el valor de txntype
                'txtn_processed' => $txndateProcessed,  // Ahora se inserta la fecha convertida o NULL
                'timezone' => $responseData['timezone'] ?? 'UTC',
                'processor_network_information' => $responseData['processor_network_information'] ?? null,
                'associationResponseMessage' => $responseData['associationResponseMessage'] ?? null,
                'ccbrand' => $responseData['ccbrand'] ?? null,
                'refnumber' => $responseData['refnumber'] ?? null,
                'cardnumber' => $responseData['cardnumber'] ?? null,
                'ipgTransactionId' => $responseData['ipgTransactionId'] ?? null,
                'fail_reason' => $responseData['fail_reason'] ?? null,
                'status' => $responseData['status'] == 'APROBADO' ? 'APROBADO' : 'FALLADO',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Si el pago fue aprobado, continuar con el proceso
            if ($paymentStatus === 'APROBADO') {
                // Insertar detalles del envío en `order_shippment` con la dirección completa
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
                    'shipping_address' => $completeAddress, // Dirección completa
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
                    'nombre_contacto' => $shippment->nombre_contacto,
                    'telefono_contacto' => $shippment->telefono_contacto,
                    'email_contacto' => $shippment->email_contacto,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Actualizar la historia del pedido en `order_history`
                DB::table('order_history')->where('order_id', $oid)->update([
                    'status' => 3,
                    'status_2_payment_process_at' => now(),
                    'status_3_paid_at' => now(),
                    'updated_at' => now()
                ]);

                // Procesar cada producto del envío
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
                        'updated_at' => now()
                    ]);

                    // Actualizar inventario
                    DB::table('inventario')->where('no_s', $item->no_s)->decrement('cantidad_disponible', $item->quantity);
                }

                // Vaciar el carrito
                DB::table('cart_items')->where('cart_id', $shippment->cart_id)->delete();

                if (DB::table('cart_items')->where('cart_id', $shippment->cart_id)->count() == 0) {
                    DB::table('carts')->where('id', $shippment->cart_id)->delete();
                }

                DB::table('shippments')->where('id', $shippment->id)->update(['status' => 'completed']);

                // Enviar correo de confirmación de la orden
                $tienda = DB::table('tiendas')->where('id', $shippment->store_id)->first();
                $correoTienda = $tienda->correo ?? 'aaronorozr@gmail.com';

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

                // Redirigir al éxito del pago
                return redirect()->route('payment.success')->with('message', '¡Pago realizado con éxito! Pedido creado correctamente.');
            } else {
                // Si el pago falló
                return redirect()->route('payment.fail')->with('error', 'El pago falló. Por favor, inténtalo de nuevo.');
            }
        } catch (\Exception $e) {
            Log::error('Error en handleSuccess: ' . $e->getMessage());
            return redirect()->route('payment.fail')->with('error', 'Ocurrió un error al procesar tu pedido. Por favor, inténtalo de nuevo.');
        }
    }






    public function handleSuccess4(Request $request)
    {
        $responseData = $request->all();


        $this->logPaymentRequest($responseData, 'success');


        if (!$this->validateResponseHash($responseData)) {
            Log::warning('Hash de respuesta inválido:', $responseData);
            return redirect()->route('payment.fail')->with('error', 'Respuesta inválida. Por favor, contacte con soporte.');
        }


        $oid = $responseData['oid'];

        $transaction = DB::table('payment_transactions')->where('oid', $oid)->first();

        if (!$transaction) {
            return redirect()->route('cart.show')->with('error', 'No se pudo encontrar la transacción.');
        }

        $userId = $transaction->user_id;

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para completar el proceso de compra.');
        }

        $shippment = DB::table('shippments')->where('user_id', $userId)->where('status', 'pending')->first();

        if (!$shippment) {
            return redirect()->route('cart.show')->with('error', 'No se encontraron detalles del envío.');
        }

        $shippmentItems = DB::table('shippment_items')->where('shippment_id', $shippment->id)->get();

        if ($shippmentItems->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'No se encontraron productos para procesar.');
        }

        $subtotal = $shippmentItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });


        $totalDiscount = $shippmentItems->sum(function ($item) {
            return ($item->unit_price * $item->quantity) * ($item->discount / 100);
        });

        $shippingCost = $shippment->shipping_cost_IVA;
        $totalConIva = $subtotal + $shippingCost;

        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'oid' => $oid,
            'total' => $transaction->chargetotal,
            'shipping_address' => $shippment->shipping_address,
            'shipping_cost' => $shippingCost,
            'discount' => $totalDiscount,
            'shipment_method' => $shippment->shipping_method,
            'subtotal_sin_envio' => $subtotal,
            'total_con_iva' => $totalConIva,
            'created_at' => now(),
            'updated_at' => now()
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
            'shipping_address' => $shippment->shipping_address,
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
            'nombre_contacto' => $shippment->nombre_contacto,
            'telefono_contacto' => $shippment->telefono_contacto,
            'email_contacto' => $shippment->email_contacto,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ]);


        DB::table('order_history')->where('order_id', $oid)->update([
            'status' => 3,
            'status_2_payment_process_at' => now(),
            'status_3_paid_at' => now(),
            'updated_at' => now()
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
                'updated_at' => now()
            ]);

            DB::table('inventario')->where('no_s', $item->no_s)->decrement('cantidad_disponible', $item->quantity);
        }


        DB::table('cart_items')->where('cart_id', $shippment->cart_id)->delete();

        if (DB::table('cart_items')->where('cart_id', $shippment->cart_id)->count() == 0) {
            DB::table('carts')->where('id', $shippment->cart_id)->delete();
        }

        DB::table('shippments')->where('id', $shippment->id)->update(['status' => 'completed']);

        $tienda = DB::table('tiendas')->where('id', $shippment->store_id)->first();
        $correoTienda = $tienda->correo ?? 'aaronorozr@gmail.com';

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
                ->cc(['sistemas@lancetahg.com'])
                ->bcc('soporte@lancetahg.com')
                ->subject('Nueva orden de pedido #' . $orderId);
        });

        return redirect()->route('payment.success')->with('message', '¡Pago realizado con éxito! Pedido creado correctamente.');
    }



    public function handleSuccess2(Request $request)
    {
        $responseData = $request->all();

        // Llamar a la función para guardar el log del request
        $this->logPaymentRequest($responseData, 'success');

        // Validar el hash de la respuesta
        if (!$this->validateResponseHash($responseData)) {
            Log::warning('Hash de respuesta inválido:', $responseData);
            return redirect()->route('payment.fail')->with('error', 'Respuesta inválida. Por favor, contacte con soporte.');
        }

        // Obtener el OID de la respuesta
        $oid = $responseData['oid'];

        // Buscar la transacción en la base de datos usando el OID
        $transaction = DB::table('payment_transactions')->where('oid', $oid)->first();

        if (!$transaction) {
            return redirect()->route('cart.show')->with('error', 'No se pudo encontrar la transacción.');
        }

        // Obtener el user_id desde la transacción
        $userId = $transaction->user_id;

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para completar el proceso de compra.');
        }

        // Obtener el shippment correspondiente al usuario y OID
        $shippment = DB::table('shippments')->where('user_id', $userId)->where('status', 'pending')->first();

        if (!$shippment) {
            return redirect()->route('cart.show')->with('error', 'No se encontraron detalles del envío.');
        }

        // Obtener los items del shippment
        $shippmentItems = DB::table('shippment_items')->where('shippment_id', $shippment->id)->get();

        if ($shippmentItems->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'No se encontraron productos para procesar.');
        }

        // Calcular el subtotal de los productos del shippment
        $subtotal = $shippmentItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });

        // Sumar los descuentos de todos los productos
        $totalDiscount = $shippmentItems->sum(function ($item) {
            return ($item->unit_price * $item->quantity) * ($item->discount / 100);
        });

        $shippingCost = $shippment->shipping_cost_IVA;
        $totalConIva = $subtotal + $shippingCost;

        // Crear la orden
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'oid' => $oid,
            'total' => $transaction->chargetotal,
            'shipping_address' => $shippment->shipping_address,
            'shipping_cost' => $shippingCost,
            'discount' => $totalDiscount,
            'shipment_method' => $shippment->shipping_method,
            'subtotal_sin_envio' => $subtotal,
            'total_con_iva' => $totalConIva,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insertar productos en order_items y descontar inventario
        foreach ($shippmentItems as $item) {
            $finalPrice = $item->unit_price - ($item->unit_price * ($item->discount / 100));
            $totalPrice = $finalPrice * $item->quantity;

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $item->no_s,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $totalPrice,
                'discount' => $item->discount,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('inventario')->where('no_s', $item->no_s)->decrement('cantidad_disponible', $item->quantity);
        }

        // Eliminar los productos comprados del carrito
        DB::table('cart_items')->where('cart_id', $shippment->cart_id)->delete();

        // Eliminar el carrito si no quedan ítems
        if (DB::table('cart_items')->where('cart_id', $shippment->cart_id)->count() == 0) {
            DB::table('carts')->where('id', $shippment->cart_id)->delete();
        }

        // Actualizar el estado del shippment a 'completed'
        DB::table('shippments')->where('id', $shippment->id)->update(['status' => 'completed']);

        // Obtener la tienda seleccionada
        $tienda = DB::table('tiendas')->where('id', $shippment->store_id)->first();
        $correoTienda = $tienda->correo ?? 'aaronorozr@gmail.com';

        $orderItems = DB::table('order_items')
            ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s')
            ->select('order_items.*', 'itemsdb.no_s', 'itemsdb.nombre as product_name')  // Incluyendo no_s y nombre
            ->where('order_id', $orderId)
            ->get();


        // Obtener la orden
        $order = DB::table('orders')->where('id', $orderId)->first();

        // Obtener la fecha y hora de recogida del shippment
        $pickupDate = $shippment->pickup_date;
        $pickupTime = $shippment->pickup_time;


        // Enviar el correo con los detalles del pedido
        Mail::send('emails.order', compact('order', 'orderItems', 'pickupDate', 'pickupTime'), function ($message) use ($correoTienda, $orderId) {
            $message->to($correoTienda)
                ->cc(['sistemas@lancetahg.com'])  // Añadir correos adicionales en CC
                ->bcc('soporte@lancetahg.com')   // Puedes añadir otros correos en BCC si lo deseas
                ->subject('Nueva orden de pedido #' . $orderId);
        });

        return redirect()->route('payment.success')->with('message', '¡Pago realizado con éxito! Pedido creado correctamente.');
    }



    public function handleFail(Request $request)
    {

        $responseData = $request->all();

        $this->logPaymentRequest($responseData, 'fail');

        $errorCodes = [];
        try {
            $errorCodes = json_decode(file_get_contents(storage_path('app/response_codes.json')), true);
        } catch (\Exception $e) {

            return redirect()->route('payment.fail')->with('error', 'Error al procesar la respuesta. Por favor, contacte con soporte.');
        }


        if (!$this->validateResponseHash($responseData)) {
            return redirect()->route('payment.fail')->with('error', 'Respuesta inválida. Por favor, contacte con soporte.');
        }


        $errorCode = $responseData['fail_rc'] ?? '00';

        $errorDetails = $errorCodes[$errorCode] ?? [
            'error_msg_translation' => 'Error desconocido.',
            'severity' => 'Desconocida',
        ];

        \Log::info('Transacción fallida', [
            'oid' => $responseData['oid'] ?? 'No OID',
            'error_code' => $errorCode,
            'error_message' => $errorDetails['error_msg_translation'],
            'fail_reason' => $responseData['fail_reason'] ?? 'No especificado',
        ]);


        return redirect()->route('payment.fail')->with('error', 'Transacción rechazada. ' . $errorDetails['error_msg_translation']);
    }





    private function validateResponseHash(array $responseData): bool
    {

        $hashData = [
            $responseData['approval_code'] ?? '',
            $responseData['chargetotal'] ?? '',
            $responseData['currency'] ?? '',
            $responseData['txndatetime'] ?? '',
            env('PAYMENT_STORENAME')
        ];


        $hashString = implode('|', $hashData);


        $secretKey = env('PAYMENT_SECRET_KEY');
        $generatedHash = base64_encode(hash_hmac('sha256', $hashString, $secretKey, true));


        return $generatedHash === $responseData['response_hash'];
    }


    public function processOrder(Request $request)
    {
        $responseData = $request->input('responseData');


        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['success' => false, 'error' => 'No se pudo autenticar al usuario.']);
        }

        $cartId = DB::table('carts')->where('user_id', $userId)->value('id');
        $cartItems = DB::table('cart_items')->where('cart_id', $cartId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'El carrito está vacío.']);
        }


        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'total' => $responseData['chargetotal'],
            'shipping_address' => 'Dirección del usuario',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        foreach ($cartItems as $item) {

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $item->no_s,
                'quantity' => $item->quantity,
                'unit_price' => $item->final_price,
                'total_price' => $item->final_price * $item->quantity,
            ]);


            DB::table('inventario')
                ->where('no_s', $item->no_s)
                ->decrement('cantidad_disponible', $item->quantity);
        }


        DB::table('cart_items')->where('cart_id', $cartId)->delete();
        DB::table('carts')->where('id', $cartId)->delete();

        return response()->json(['success' => true, 'message' => 'Pedido procesado correctamente.']);
    }
}
