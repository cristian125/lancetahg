<?php

namespace App\Http\Controllers;

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

        // Verificar el stock de los productos en el carrito
        $cartItems = DB::table('cart_items')
            ->join('inventario', 'cart_items.no_s', '=', 'inventario.no_s')
            ->where('cart_items.cart_id', $cartId)
            ->select(
                'cart_items.*',
                'inventario.cantidad_disponible'
            )
            ->get();

        foreach ($cartItems as $item) {
            if ($item->quantity > $item->cantidad_disponible) {
                return redirect()->back()->with('error', "No hay suficiente stock para el producto {$item->description}.");
            }
        }

        // Validación para "Recoger en Tienda"
        $storeId = null; // Variable para almacenar el store_id
        if ($shippmentDetails->ShipmentMethod === 'RecogerEnTienda') {
            $pickupDate = new \DateTime($shippmentDetails->pickup_date);
            $pickupTime = new \DateTime($shippmentDetails->pickup_time);

            $dayOfWeek = $pickupDate->format('N'); // Día de la semana (1=Lunes, 7=Domingo)
            $hour = (int) $pickupTime->format('H'); // Hora en formato 24 horas

            // Lunes a viernes de 10:00 a 18:00
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                if ($hour < 10 || $hour > 18) {
                    return redirect()->back()->with('error', 'La hora seleccionada para recoger en tienda es inválida. Recuerda que los horarios son de 10:00 a 18:00 de lunes a viernes.');
                }
            }
            // Sábado de 10:00 a 15:00
            elseif ($dayOfWeek == 6) {
                if ($hour < 10 || $hour > 15) {
                    return redirect()->back()->with('error', 'La hora seleccionada para recoger en tienda es inválida. Recuerda que los horarios son de 10:00 a 15:00 los sábados.');
                }
            }
            // No se permiten recogidas en domingo
            else {
                return redirect()->back()->with('error', 'No es posible recoger en tienda los domingos.');
            }

            // Verificar que la hora es en punto (sin minutos intermedios)
            if ($pickupTime->format('i') !== '00') {
                return redirect()->back()->with('error', 'La hora seleccionada para recoger en tienda debe ser una hora exacta (sin minutos intermedios).');
            }

            // Bloquear la selección del mismo día o día siguiente (excepto domingos)
            $currentDate = new \DateTime();
            $minPickupDate = (clone $currentDate)->modify('+1 day');

            if ($pickupDate < $minPickupDate) {
                return redirect()->back()->with('error', 'No puedes programar un pedido para el mismo día o para días anteriores. Elige al menos el día siguiente, excepto los domingos.');
            }

            // Asignar el store_id del envío
            $storeId = $shippmentDetails->store_id;
        }

        // El totalPrice ya incluye el IVA, por lo que no es necesario volver a calcularlo
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });
        $shippingCost = floatval($shippmentDetails->shippingcost_IVA);

        // Si el total ya incluye el IVA, simplemente lo tomamos tal cual
        $totalPriceIVA = $totalPrice; // Asumimos que `final_price` ya incluye el IVA

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

        // Crear el nuevo shippment utilizando los datos de `cart_shippment`
        $shippmentId = DB::table('shippments')->insertGetId([
            'user_id' => $userId,
            'cart_id' => $cartId,
            'store_id' => $storeId, // Añadir el store_id aquí
            'shipping_method' => $shippmentDetails->ShipmentMethod,
            'shipping_cost' => $shippmentDetails->unit_price,
            'shipping_cost_IVA' => $shippingCost,
            'subtotal_sin_envio' => $totalPrice - $shippingCost, // Subtotal sin el costo de envío
            'total_con_IVA' => $totalPrice, // Total con IVA
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
            'nombre_contacto' => $shippmentDetails->nombre,
            'telefono_contacto' => $shippmentDetails->telefono_contacto ?? '',
            'email_contacto' => $request->user()->email,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar los productos en la tabla `shippment_items`
        foreach ($cartItems as $item) {
            DB::table('shippment_items')->insert([
                'shippment_id' => $shippmentId,
                'no_s' => $item->no_s,
                'description' => $item->description,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'final_price' => $item->final_price,
                'quantity' => $item->quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirigir al checkout
        return redirect('/checkout')->with('success', 'El pedido ha sido validado. Procede al pago.');
    }

}
