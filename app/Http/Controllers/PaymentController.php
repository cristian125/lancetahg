<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{


    private function logPaymentRequest(array $responseData, string $requestType)
    {
        // Buscar la transacción en la base de datos usando el OID
        $oid = $responseData['oid'] ?? null;
        $transaction = DB::table('payment_transactions')->where('oid', $oid)->first();

        // Obtener el user_id si existe la transacción
        $userId = $transaction->user_id ?? null;

        // Guardar todos los datos del request en la tabla payment_requests_log
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

    public function handleSuccess(Request $request)
    {
        $responseData = $request->all();
    
        // Llamar a la función para guardar el log del request
        $this->logPaymentRequest($responseData, 'success');
    
        // Validar el hash de la respuesta
        if (!$this->validateResponseHash($responseData)) {
            Log::warning('Hash de respuesta inválido:', $responseData);
            return redirect()->route('payment.fail')->with('error', 'Respuesta inválida. Por favor, contacte con soporte.');
        }
    
        // Obtener el OID de la respuesta que se pasaron desde el success (esto viene en la respuesta de Fiserv)
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
    
        // Obtener los items del shippment (estos son los productos elegibles)
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
    
        $shippingCost = $shippment->shipping_cost_IVA; // Obtener el costo de envío desde el shippment
        $totalConIva = $subtotal + $shippingCost;
    
        // Crear una nueva orden e insertar todos los detalles importantes
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
    
        // Calcular el precio final y total para cada ítem
        foreach ($shippmentItems as $item) {
            // Calcular el precio final después del descuento
            $finalPrice = $item->unit_price - ($item->unit_price * ($item->discount / 100));
            $totalPrice = $finalPrice * $item->quantity;
    
            // Insertar en order_items sin 'final_price'
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $item->no_s,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                // 'final_price' => $finalPrice, // Comentamos o eliminamos esta línea
                'total_price' => $totalPrice,
                'discount' => $item->discount,
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            // Descontar el inventario
            DB::table('inventario')
                ->where('no_s', $item->no_s)
                ->decrement('cantidad_disponible', $item->quantity);
        }
    
        // Eliminar solo los productos comprados del carrito
        foreach ($shippmentItems as $item) {
            DB::table('cart_items')
                ->where('cart_id', $shippment->cart_id)
                ->where('no_s', $item->no_s)
                ->delete();
        }
    
        // Verificar si quedan ítems en el carrito
        $remainingCartItems = DB::table('cart_items')->where('cart_id', $shippment->cart_id)->count();
    
        if ($remainingCartItems == 0) {
            // Si no quedan ítems, eliminar el carrito
            DB::table('carts')->where('id', $shippment->cart_id)->delete();
        }
    
        // Actualizar el estado del shippment a 'completed'
        DB::table('shippments')->where('id', $shippment->id)->update(['status' => 'completed']);
    
        // Redirigir a la vista de éxito con un mensaje
        return redirect()->route('payment.success')->with('message', '¡Pago realizado con éxito! Pedido creado correctamente.');
    }
    
    public function handleFail(Request $request)
    {
        // Obtener todos los datos de la respuesta
        $responseData = $request->all();

        // Llamar a la función para guardar el log del request
        $this->logPaymentRequest($responseData, 'fail');

        // Cargar el archivo JSON con los códigos de error desde el almacenamiento
        $errorCodes = [];
        try {
            $errorCodes = json_decode(file_get_contents(storage_path('app/response_codes.json')), true);
        } catch (\Exception $e) {
            // Si ocurre algún error al cargar el archivo JSON
            return redirect()->route('payment.fail')->with('error', 'Error al procesar la respuesta. Por favor, contacte con soporte.');
        }

        // Validar el hash de la respuesta
        if (!$this->validateResponseHash($responseData)) {
            return redirect()->route('payment.fail')->with('error', 'Respuesta inválida. Por favor, contacte con soporte.');
        }

        // Obtener el código de error de la respuesta (fail_rc o error_code según los datos recibidos)
        $errorCode = $responseData['fail_rc'] ?? '00'; // Utiliza '00' si no se encuentra 'fail_rc'

        // Buscar el código de error en el JSON
        $errorDetails = $errorCodes[$errorCode] ?? [
            'error_msg_translation' => 'Error desconocido.',
            'severity' => 'Desconocida',
        ];

        // Registrar en el log los detalles de la respuesta fallida (opcional)
        \Log::info('Transacción fallida', [
            'oid' => $responseData['oid'] ?? 'No OID',
            'error_code' => $errorCode,
            'error_message' => $errorDetails['error_msg_translation'],
            'fail_reason' => $responseData['fail_reason'] ?? 'No especificado',
        ]);

        // Mostrar un mensaje de error basado en el JSON
        return redirect()->route('payment.fail')->with('error', 'Transacción rechazada. ' . $errorDetails['error_msg_translation']);
    }





    private function validateResponseHash(array $responseData): bool
    {
        // Obtener los valores necesarios de la respuesta de Fiserv y de la base de datos
        $hashData = [
            $responseData['approval_code'] ?? '',             // approval_code recibido de Fiserv
            $responseData['chargetotal'] ?? '',               // chargetotal recibido de Fiserv
            $responseData['currency'] ?? '',                  // currency recibido de Fiserv
            $responseData['txndatetime'] ?? '',               // txndatetime recibido de Fiserv
            env('PAYMENT_STORENAME')                          // storename de .env
        ];

        // Concatenar los valores separados por "|"
        $hashString = implode('|', $hashData);

        // Generar el hash usando HMAC SHA-256 con clave secreta
        $secretKey = env('PAYMENT_SECRET_KEY');
        $generatedHash = base64_encode(hash_hmac('sha256', $hashString, $secretKey, true));

        // Comparar el hash generado con el hash recibido (response_hash)
        return $generatedHash === $responseData['response_hash'];
    }


    public function processOrder(Request $request)
    {
        $responseData = $request->input('responseData');

        // Obtener el ID del usuario
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['success' => false, 'error' => 'No se pudo autenticar al usuario.']);
        }
        dd($userId);
        $cartId = DB::table('carts')->where('user_id', $userId)->value('id');
        $cartItems = DB::table('cart_items')->where('cart_id', $cartId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'El carrito está vacío.']);
        }

        // Crear una nueva orden
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'total' => $responseData['chargetotal'],  // Total desde la respuesta de Fiserv
            'shipping_address' => 'Dirección del usuario',  // Cambiar este valor según sea necesario
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insertar los productos en la tabla `order_items` y actualizar el inventario
        foreach ($cartItems as $item) {
            // Insertar en order_items
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $item->no_s, // Usar el "no_s" para identificar el producto
                'quantity' => $item->quantity,
                'unit_price' => $item->final_price,
                'total_price' => $item->final_price * $item->quantity,
            ]);

            // Descontar el inventario
            DB::table('inventario')
                ->where('no_s', $item->no_s)
                ->decrement('cantidad_disponible', $item->quantity);
        }

        // Limpiar el carrito
        DB::table('cart_items')->where('cart_id', $cartId)->delete();
        DB::table('carts')->where('id', $cartId)->delete();

        return response()->json(['success' => true, 'message' => 'Pedido procesado correctamente.']);
    }
}
