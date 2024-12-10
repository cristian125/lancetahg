<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        // Obtener los datos de la respuesta del request
        $responseData = $request->all();

        // Verificar el estado del pago (APROBADO o FALLADO)
        $paymentStatus = $responseData['status'] ?? 'FALLADO';

        // // // Validar el hash de la respuesta para asegurarse de que sea válido
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

    if ($transaction->processed == true) {
        // Si ya se ha procesado previamente, redirigir a una página de éxito o mostrar un mensaje.
        return redirect()->route('payment.success', ['oid' => $oid])
            ->with('message', 'Esta transacción ya fue procesada anteriormente.');
    }

        $userId = $transaction->user_id;
        $cartId = $transaction->cart_id;

        // Obtener el carrito activo del usuario (status = 1)
        $cart = DB::table('carts')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->first();

        // Buscar el envío pendiente del usuario
        $shippment = DB::table('cart_shippment')->where('cart_id', $cartId)->first();
        // $shippment1 = DB::table('shippments')->where('user_id', $userId)->first();

        if (!$shippment) {
            return redirect()->route('cart.show')->with('error', 'No se encontraron detalles del envío.');
        }
        // Obtener el correo electrónico del usuario
        $user = DB::table('users')->where('id', $userId)->first();
        $userEmail = $user->email;
        // Obtener los artículos del envío
        $shippmentItems = DB::table('shippment_items')->where('shippment_id', $shippment->id)->get();

        // Calcular el subtotal
        $subtotal = $shippmentItems->sum(function ($item) {
            return $item->final_price;
        });

        // Calcular el descuento total
        $totalDiscount = $shippmentItems->sum(function ($item) {
            return ($item->unit_price * $item->quantity) * ($item->discount / 100);
        });

        $shippingCost = $shippment->final_price;
        $totalConIva = $subtotal + $shippingCost;

        // Concatenar toda la dirección
        $completeAddress = $this->formatCompleteAddress($shippment);

        // **Obtener el número de pedido actual y actualizarlo**
        $orderNumberRow = DB::table('order_number_sequence')->first();

        if (!$orderNumberRow) {
            throw new \Exception('No se pudo obtener el número de pedido.');
        }

        $orderNumber = $orderNumberRow->current_order_number;

        // Incrementar el número de pedido para el siguiente uso
        DB::table('order_number_sequence')->update(['current_order_number' => $orderNumber + 1]);

        // Insertar la orden en la tabla `orders` con el número de pedido personalizado
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'oid' => $oid,
            'order_number' => $orderNumber, // Usamos el número de pedido personalizado
            'cart_id' => $cartId, // Aquí se pasa el cart_id del carrito activo
            'total' => $transaction->chargetotal,
            'shipping_address' => $completeAddress, // Dirección completa
            'shipping_cost' => $shippingCost,
            'discount' => $totalDiscount,
            'shipment_method' => $shippment->ShipmentMethod,
            'subtotal_sin_envio' => $subtotal,
            'total_con_iva' => $totalConIva,
            'created_at' => now(),
            'updated_at' => now(),
            'current_state' => 2, // Asegúrate de establecer este campo si es necesario
        ]);

        // Convertir la fecha `txndate_processed` al formato MySQL (YYYY-MM-DD HH:MM:SS)
        $txndateProcessed = isset($responseData['txndate_processed']) ?
        Carbon::createFromFormat('d/m/y h:i:s A', $responseData['txndate_processed'])->format('Y-m-d H:i:s') :
        null; // Si no existe, asignar NULL

        // Insertar el registro de pago en `order_payment`
        DB::table('order_payment')->insert([
            'order_id' => $orderId,
            'chargetotal' => $transaction->chargetotal,
            'request_type' => $responseData['txntype'] ?? 'unknown', // Cambia 'unknown' por el valor de txntype
            'txtn_processed' => $txndateProcessed, // Ahora se inserta la fecha convertida o NULL
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
            'updated_at' => now(),
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
                'shipping_method' => $shippment->ShipmentMethod,
                'shipping_cost' => $shippment->unit_price,
                'shipping_cost_IVA' => $shippment->shippingcost_IVA ?? 0,
                'subtotal_sin_envio' => $subtotal,
                'total_con_IVA' => $totalConIva,
                'shipping_address' => $shippment->calle,
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
                'email_contacto' => $userEmail ?? 0,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Actualizar la historia del pedido en `order_history`
            DB::table('order_history')->where('order_id', $oid)->update([
                'status' => 3,
                'status_2_payment_process_at' => now(),
                'status_3_paid_at' => now(),
                'updated_at' => now(),
            ]);

            // Obtener los artículos del envío
            $shippmentItems = DB::table('shippment_items')->where('shippment_id', $shippment->id)->get();
            // Procesar cada producto del envío
            foreach ($shippmentItems as $item) {
                // Obtener el cart_item correspondiente
                $cartItem = DB::table('cart_items')
                    ->where('cart_id', $cartId)
                    ->where('no_s', $item->no_s)
                    ->first();

                if ($cartItem) {
                    // Obtener el número único de la nueva configuración
                    $id_bc = DB::table('configuraciones')->where('name', 'order_item_increment')->value('value');

                    // Incrementar el número en la tabla de configuración
                    DB::table('configuraciones')->where('name', 'order_item_increment')->update(['value' => $id_bc + 1]);

                    // Insertar en order_items utilizando datos de shippment_items y cart_items
                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'id_bc' => $id_bc, // Número único
                        'product_id' => $item->no_s,
                        'description' => $cartItem->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'discount_amount' => $cartItem->discount_amount,
                        'amount' => $cartItem->amount,
                        'vat' => $cartItem->vat,
                        'vat_amount' => $cartItem->vat_amount,
                        'final_price' => $cartItem->final_price,
                        'total_price' => $cartItem->amount - $cartItem->discount_amount,
                        'discount' => $item->discount,
                        'iva_rate' => $cartItem->vat ?? 0,
                        'unidad_medida_venta' => $cartItem->unidad_medida_venta,
                        'length' => $unitDetails->length ?? null,
                        'width' => $unitDetails->width ?? null,
                        'depth' => $unitDetails->height ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Actualizar inventario
                    DB::table('inventario')->where('no_s', $item->no_s)->decrement('cantidad_disponible', $item->quantity);
                } else {
                    // Manejar el caso en que no se encuentre el cart_item
                    Log::warning("No se encontró cart_item para cart_id: $cartId y no_s: {$item->no_s}");
                }
            }
            $ItemFlete = DB::table('itemsdb')->where(['no_s' => '999998'])->first();

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $ItemFlete->no_s,
                'description' => $ItemFlete->nombre,
                'quantity' => 1,
                'unit_price' => $shippment->unit_price,
                'amount' => $shippment->unit_price,
                'total_price' => $totalConIva,
                'discount' => 0.00,
                'iva_rate' => 0.16,
                'vat' => 0.16,
                'vat_amount' => $shippment->unit_price * 0.16,
                'unidad_medida_venta' => $cartItem->unidad_medida_venta, // Añadido: unidad de medida
                'length' => 0.00 ?? null,
                'width' => 0.00 ?? null,
                'depth' => 0.00 ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Vaciar el carrito
            DB::table('carts')->where('id', $shippment->cart_id)->update(['status' => 2]);
            // DB::table('cart_items')->where('cart_id', $shippment->cart_id)->delete();

            // if (DB::table('cart_items')->where('cart_id', $shippment->cart_id)->count() == 0) {
            //     DB::table('carts')->where('id', $shippment->cart_id)->delete();
            // }

            DB::table('shippments')->where('id', $shippment->id)->update(['status' => 'completed']);

            // Enviar correo de confirmación de la orden
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

            // Obtener el correo electrónico del usuario
            $user = DB::table('users')->where('id', $userId)->first();
            $userEmail = $user->email;
            // Obtener los detalles del envío
            $orderShippment = DB::table('order_shippment')->where('order_id', $orderId)->first();

            // Enviar correo al cliente
            Mail::send('emails.order', compact('order', 'orderItems', 'pickupDate', 'pickupTime', 'user', 'orderShippment'), function ($message) use ($userEmail, $order) {
                $message->to($userEmail)
                    ->subject('Confirmación de tu pedido #' . $order->order_number . ' - LANCETA HG');
            });

            // Enviar correo a la tienda
            if ($shippment->ShipmentMethod === 'RecogerEnTienda') {
                $tienda = DB::table('tiendas')->where('id', $shippment->store_id)->first();
                $correoTienda = $tienda->correo ?? 'soporte@lancetahg.com';

                // Preparar datos para el correo a la tienda
                $orderData = $order;
                $orderItemsData = $orderItems->where('product_id', '!=', 999998); // Excluir el producto de flete
                $userData = $user;

                try {
                    Mail::send('emails.order_store', compact('orderData', 'orderItemsData', 'userData', 'tienda', 'orderShippment'), function ($message) use ($correoTienda, $order) {
                        $message->to($correoTienda)
                            ->subject('Nueva orden de pedido #' . $order->order_number . ' para su tienda');
                    });

                    Log::info('Correo enviado a la tienda: ' . $correoTienda);
                } catch (\Exception $e) {
                    Log::error('Error al enviar correo a la tienda: ' . $e->getMessage());
                }
            }

            
        // **Nuevo: Marcar la transacción como procesada**
        DB::table('payment_transactions')->where('oid', $oid)->update([
            'processed' => true,
            'updated_at' => now()
        ]);

            return view('success', compact('order', 'orderItems', 'pickupDate', 'pickupTime'))->with('message', '¡Pago realizado con éxito! Pedido creado correctamente.');
        }

    }

    public function handleSuccesssinserie(Request $request)
    {
        try {

            // Verificar el estado del pago (APROBADO o FALLADO)
            $paymentStatus = $responseData['status'] ?? 'FALLADO';
            // Obtener los datos de la respuesta del request
            $responseData = $request->all();

            // // Validar el hash de la respuesta para asegurarse de que sea válido
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
                'shipment_method' => $shippment->ShipmentMethod,
                'subtotal_sin_envio' => $subtotal,
                'total_con_iva' => $totalConIva,
                'created_at' => now(),
                'updated_at' => now(),
                'current_state' => 2, // Asegúrate de establecer este campo si es necesario
            ]);

            // Verificar el estado del pago (APROBADO o FALLADO)
            $paymentStatus = $responseData['status'] ?? 'FALLADO';

            // Convertir la fecha `txndate_processed` al formato MySQL (YYYY-MM-DD HH:MM:SS)
            $txndateProcessed = isset($responseData['txndate_processed']) ?
            Carbon::createFromFormat('d/m/y h:i:s A', $responseData['txndate_processed'])->format('Y-m-d H:i:s') :
            null; // Si no existe, asignar NULL

            // Insertar el registro de pago en `order_payment`
            DB::table('order_payment')->insert([
                'order_id' => $orderId,
                'chargetotal' => $transaction->chargetotal,
                'request_type' => $responseData['txntype'] ?? 'unknown', // Cambia 'unknown' por el valor de txntype
                'txtn_processed' => $txndateProcessed, // Ahora se inserta la fecha convertida o NULL
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
                'updated_at' => now(),
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
                    'shipping_method' => $shippment->ShipmentMethod,
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
                    'updated_at' => now(),
                ]);

                // Actualizar la historia del pedido en `order_history`
                DB::table('order_history')->where('order_id', $oid)->update([
                    'status' => 3,
                    'status_2_payment_process_at' => now(),
                    'status_3_paid_at' => now(),
                    'updated_at' => now(),
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
                        'updated_at' => now(),
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
                $correoTienda = $tienda->correo ?? 'soporte@lancetahg.com';

                $orderItems = DB::table('order_items')
                    ->join('itemsdb', 'order_items.product_id', '=', 'itemsdb.no_s')
                    ->select('order_items.*', 'itemsdb.no_s', 'itemsdb.nombre as product_name')
                    ->where('order_id', $orderId)
                    ->get();

                $order = DB::table('orders')->where('id', $orderId)->first();
                $pickupDate = $shippment->pickup_date;
                $pickupTime = $shippment->pickup_time;

                // Obtener el correo electrónico del usuario
                $user = DB::table('users')->where('id', $userId)->first();
                $userEmail = $user->email;
                // Enviar correo de confirmación al usuario con la plantilla específica
                Mail::send('emails.order', compact('order', 'orderItems', 'pickupDate', 'pickupTime'), function ($message) use ($userEmail) {
                    $message->to($userEmail)
                        ->subject('Confirmación de tu pedido - LANCETA HG');
                });

                if ($shippment->ShipmentMethod === 'RecogerEnTienda') {

                    Mail::send('emails.order', compact('order', 'orderItems', 'pickupDate', 'pickupTime'), function ($message) use ($correoTienda, $orderId) {
                        $message->to($correoTienda)
                            ->bcc('soporte@lancetahg.com')
                            ->subject('Nueva orden de pedido #' . $orderId);
                    });
                }

                // Redirigir al éxito del pago
                return redirect()->route('payment.success')->with('message', '¡Pago realizado con éxito! Pedido creado correctamente.');
            }
        } catch (\Exception $e) {
        }
    }

    public function handleFail(Request $request)
    {
        $responseData = $request->all();

        // Log de la transacción fallida
        $this->logPaymentRequest($responseData, 'fail');

        // Obtener los códigos de error desde config
        $errorCodes = config('response_codes');

        // Verifica si se cargó correctamente
        if (empty($errorCodes)) {
            \Log::error('No se cargaron los códigos de error desde la configuración.');
            $error = 'Error en la configuración de códigos de respuesta. Por favor, contacte con soporte.';
            return view('fail', compact('error', 'responseData'));
        }

        // Obtener el código de error y asegurarte de que no tenga espacios en blanco
        $errorCode = isset($responseData['fail_rc']) ? trim($responseData['fail_rc']) : '00';

        // Obtener detalles del código de error
        $errorDetails = $errorCodes[$errorCode] ?? [
            'error_msg_translation' => 'Error desconocido.',
            'severity' => 'Desconocida',
        ];

        // Registrar información para depuración
        \Log::info('Detalles del código de error', [
            'error_code' => $errorCode,
            'error_details' => $errorDetails,
        ]);

        // Determinar el mensaje de error para mostrar
        if (isset($errorDetails['severity'])) {
            if ($errorDetails['severity'] === 'Hard') {
                $error = 'Transacción rechazada. Por favor, contacte con su banco.';
            } elseif ($errorDetails['severity'] === 'Soft') {
                $error = 'Transacción rechazada. Por favor, inténtelo de nuevo.';
            } else {
                $error = 'Transacción rechazada. Por favor, inténtelo de nuevo.';
            }
        } else {
            $error = 'Error desconocido. Por favor, contacte con soporte.';
        }

        // Retornar la vista con el error
        return view('fail', compact('error', 'responseData'));

    }

    private function validateResponseHash(array $responseData): bool
    {

        $hashData = [
            $responseData['approval_code'] ?? '',
            $responseData['chargetotal'] ?? '',
            $responseData['currency'] ?? '',
            $responseData['txndatetime'] ?? '',
            env('PAYMENT_STORENAME'),
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
            'updated_at' => now(),
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
