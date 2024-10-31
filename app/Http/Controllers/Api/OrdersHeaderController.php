<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class OrdersHeaderController extends Controller
{
    /**
     * Mostrar las órdenes con estado específico (por defecto 2).
     */


    public function showOrdersWithState(Request $request)
    {
        // Obtener todas las órdenes con `current_state = 2`
        $orders = DB::table('orders')
            ->where('current_state', 2)
            ->get();

        // Crear un array para almacenar los datos en el formato requerido
        $orderData = [
            "orders" => []
        ];

        foreach ($orders as $order) {
            // Obtener los items relacionados en `order_items`
            $orderItems = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            // Formatear la orden
            $orderFormatted = [
                "id" => $order->id,
                "id_address_delivery" => "29317",
                "id_address_invoice" => "23475",
                "id_cart" => "214294",
                "id_currency" => "1",
                "id_lang" => "2",
                "id_customer" => "19345",
                "id_carrier" => "370",
                "current_state" => (string)$order->current_state,
                "module" => "firstdata",
                "invoice_number" => "0",
                "invoice_date" => "0000-00-00 00:00:00",
                "delivery_number" => "0",
                "delivery_date" => "0000-00-00 00:00:00",
                "valid" => "1",
                "date_add" => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                "date_upd" => Carbon::parse($order->updated_at)->format('Y-m-d H:i:s'),
                "shipping_number" => "",
                "id_shop_group" => "1",
                "id_shop" => "1",
                "secure_key" => "038cb3d73d80798fe094c5e7fdf74b13",
                "payment" => "Tarjeta Bancaria",
                "recyclable" => "0",
                "gift" => "0",
                "gift_message" => "",
                "mobile_theme" => "0",
                "total_discounts" => (string)$order->discount,
                "total_discounts_tax_incl" => (string)$order->discount,
                "total_discounts_tax_excl" => (string)$order->discount,
                "total_paid" => (string)$order->total_con_iva,
                "total_paid_tax_incl" => (string)$order->total_con_iva,
                "total_paid_tax_excl" => (string)$order->subtotal_sin_envio,
                "total_paid_real" => "0.000000",
                "total_products" => (string)$order->subtotal_sin_envio,
                "total_products_wt" => (string)($order->subtotal_sin_envio + $order->shipping_cost),
                "total_shipping" => (string)$order->shipping_cost,
                "total_shipping_tax_incl" => (string)$order->shipping_cost,
                "total_shipping_tax_excl" => (string)$order->shipping_cost,
                "carrier_tax_rate" => "0.000",
                "total_wrapping" => "0.000000",
                "total_wrapping_tax_incl" => "0.000000",
                "total_wrapping_tax_excl" => "0.000000",
                "round_mode" => "2",
                "round_type" => "2",
                "conversion_rate" => "1.000000",
                "reference" => $order->oid,
                "lscode_forma_pago" => "TARJ DEB",
                "transaction_id" => "3316333115",
                "card_number" => "**** 5499",
                "card_brand" => "MASTERCARD",
                "card_holder" => "Victoria Cruz Gomez",
                "amount" => null,
                "lscode_cliente" => "",
                "tipo_entrega" => "DOMICILIO",
                "opcion_envio" => "",
                "associations" => [
                    "order_rows" => []
                ],
            ];

            // Agregar cada item en `order_rows`
            foreach ($orderItems as $item) {
                $orderRow = [
                    "id" => (string)$item->id,
                    "product_id" => (string)$item->product_id,
                    "product_attribute_id" => "0",
                    "product_quantity" => (string)$item->quantity,
                    "product_name" => $item->description,
                    "product_reference" => $item->product_id,
                    "product_ean13" => "",
                    "product_upc" => "",
                    "product_price" => (string)$item->unit_price,
                    "unit_price_tax_incl" => (string)($item->unit_price * 1.16),
                    "unit_price_tax_excl" => (string)$item->unit_price,
                ];

                // Añadir `orderRow` al array `order_rows` de `orderFormatted`
                $orderFormatted['associations']['order_rows'][] = $orderRow;
            }

            // Agregar `orderFormatted` al array `orders`
            $orderData['orders'][] = $orderFormatted;
        }

        // Devolver los datos de las órdenes en formato JSON
        return response()->json($orderData);
    }


public function showOrderDetails(Request $request)
{
    // Obtener el id de la orden desde el request
    $orderId = $request->input('filter.id_order');

    // Consultar la base de datos para obtener los detalles de los productos de la orden especificada
    $orderItemsQuery = DB::table('order_items');

    // Filtrar por order_id si está disponible
    if ($orderId) {
        $orderItemsQuery->where('order_id', $orderId);
    }

    // Obtener los items de la base de datos
    $orderItems = $orderItemsQuery->get();

    // Obtener el valor actual de `order_detail_increment` desde configuraciones y actualizarlo
    $currentIncrement = DB::table('configuraciones')->value('order_detail_increment');
    
    // Preparar los detalles de los productos
    $orderDetails = ["order_details" => []];

    foreach ($orderItems as $item) {
        // Incrementar el `id` y actualizar `order_detail_increment` en `configuraciones`
        $currentIncrement++;
        DB::table('configuraciones')->update(['order_detail_increment' => $currentIncrement]);

        // Agregar el detalle del producto al arreglo principal
        $orderDetails['order_details'][] = [
            "id" => (string)$currentIncrement,
            "id_order" => (string)$item->order_id,
            "product_id" => (string)$item->product_id,
            "product_attribute_id" => "0", // Ficticio
            "product_quantity_reinjected" => "0",
            "group_reduction" => "0.00",
            "discount_quantity_applied" => "0",
            "download_hash" => "",
            "download_deadline" => "0000-00-00 00:00:00",
            "id_order_invoice" => "0",
            "id_warehouse" => "0",
            "id_shop" => "1",
            "id_customization" => "0",
            "product_name" => $item->description,
            "product_quantity" => (string)$item->quantity,
            "product_quantity_in_stock" => "1",
            "product_quantity_return" => "0",
            "product_quantity_refunded" => "0",
            "product_price" => (string)$item->unit_price,
            "reduction_percent" => "0.00",
            "reduction_amount" => (string)$item->discount,
            "reduction_amount_tax_incl" => (string)$item->discount,
            "reduction_amount_tax_excl" => (string)$item->discount,
            "product_quantity_discount" => "0.000000",
            "product_ean13" => "",
            "product_isbn" => null,
            "product_upc" => "",
            "product_mpn" => null,
            "product_reference" => $item->product_id, // Ficticio
            "product_supplier_reference" => "326787", // Ficticio
            "product_weight" => "0.120000", // Ficticio
            "tax_computation_method" => "0",
            "id_tax_rules_group" => "1",
            "ecotax" => "0.000000",
            "ecotax_tax_rate" => "0.000",
            "download_nb" => "0",
            "unit_price_tax_incl" => (string)($item->unit_price * 1.16),
            "unit_price_tax_excl" => (string)$item->unit_price,
            "total_price_tax_incl" => (string)($item->total_price * 1.16),
            "total_price_tax_excl" => (string)$item->total_price,
            "total_shipping_price_tax_excl" => "0.000000",
            "total_shipping_price_tax_incl" => "0.000000",
            "purchase_supplier_price" => "88.530000", // Ficticio
            "original_product_price" => (string)$item->unit_price,
            "original_wholesale_price" => "88.530000", // Ficticio
            "total_refunded_tax_excl" => "0.000000",
            "total_refunded_tax_incl" => "0.000000",
            "associations" => [
                "taxes" => [
                    [
                        "id" => null
                    ]
                ]
            ]
        ];
    }

    // Devolver los detalles de la orden en formato JSON
    return response()->json($orderDetails);
}


}
