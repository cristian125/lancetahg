<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class OrdersHeaderController extends Controller
{
    /**
     * Mostrar las órdenes con estado 2.
     */

    public function showOrdersWithState(Request $request)
    {

        // $orders = DB::table('orders')
        //     ->where('current_state', 2)
        //     ->get();


        $orders = DB::table('orders')
        ->where('current_state', 2)
        ->orderBy('created_at', 'desc')
        ->limit(6)
        ->get();


        $orderData = [
            "orders" => []
        ];

        foreach ($orders as $order) {

            $orderItems = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();


            $paymentMethod = DB::table('order_history')
                ->where('order_id', $order->id)
                ->value('payment_method');
            $paymentMethod = trim($paymentMethod);



            $lscodeFormaPago = "TARJ DEB";
            if ($paymentMethod === "Tarjeta de Crédito") {
                $lscodeFormaPago = "TARJ CRED";
            } elseif ($paymentMethod === "Monedero") {
                $lscodeFormaPago = "MONEDERO";
            }

   
            $totalPaidTaxIncl = $order->total_con_iva; 
            $totalPaidTaxExcl = 0;


            foreach ($orderItems as $item) {

                $product = DB::table('itemsdb')
                    ->where('no_s', $item->product_id)
                    ->first();


                if ($product && $product->grupo_iva === 'IVA16') {
                    $unitPriceExcl = $item->unit_price / 1.16; 
                } else {
                    $unitPriceExcl = $item->unit_price; 
                }


                $totalPaidTaxExcl += $unitPriceExcl * $item->quantity;
            }
            

            $orderFormatted = [
                "id" => $order->order_number,
                "id_address_delivery" => $order->order_number,
                "id_address_invoice" => $order->order_number,
                "id_cart" => $order->cart_id,
                "id_currency" => "1",
                "id_lang" => "2",
                "id_customer" => "0",
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
                "secure_key" => "238cb3d73d80798fe094c5e7fdf74b13",
                "payment" => "Tarjeta Bancaria",
                "recyclable" => "0",
                "gift" => "0",
                "gift_message" => "",
                "mobile_theme" => "0",
                "total_discounts" => "0",
                "total_discounts_tax_incl" => "0",
                "total_discounts_tax_excl" => "0",
                "total_paid" => (string)$totalPaidTaxIncl,
                "total_paid_tax_incl" => (string)$totalPaidTaxIncl,
                "total_paid_tax_excl" => (string)$totalPaidTaxIncl / 1.16,
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
                "lscode_forma_pago" => $lscodeFormaPago,
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


            foreach ($orderItems as $item) {
                $product = DB::table('itemsdb')
                    ->where('no_s', $item->product_id)
                    ->first();


                $unitPriceTaxExcl = ($product && $product->grupo_iva === 'IVA16')
                    ? $item->unit_price / 1.16
                    : $item->unit_price;
                
                $orderRow = [
                    "id" => (string)$item->id_bc,
                    "product_id" => (string)$item->product_id,
                    "product_attribute_id" => "0",
                    "product_quantity" => (string)$item->quantity,
                    "product_name" => $item->description,
                    "product_reference" => $item->product_id,
                    "product_ean13" => "",
                    "product_upc" => "",
                    "product_price" => (string)$item->unit_price,
                    "unit_price_tax_incl" => (string)$item->unit_price,
                    "unit_price_tax_excl" => (string)$item->total_price,
                ];

                $orderFormatted['associations']['order_rows'][] = $orderRow;
            }

            $orderData['orders'][] = $orderFormatted;
        }
        
        return response()->json($orderData);
    }




    public function showOrderDetails(Request $request)
    {
  
        $orderNumber = $request->input('filter.id_order');

        $order = DB::table('orders')->where(['order_number' => $orderNumber])->first();


        $orderItemsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id') 
            ->select('order_items.*', 'orders.order_number') 
            ->where('orders.order_number', $orderNumber); 


        $orderItems = $orderItemsQuery->get();


        $currentIncrement = DB::table('configuraciones')->value('order_detail_increment');


        $orderDetails = ["order_details" => []];


        foreach ($orderItems as $item) {

            $currentIncrement++;
            DB::table('configuraciones')->update(['order_detail_increment' => $currentIncrement]);


            $orderDetails['order_details'][] = [
                "id" => (string)$currentIncrement,
                "id_order" => (string)$item->order_number, 
                "product_id" => (string)$item->product_id,
                "product_attribute_id" => "0", 
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
                "reduction_percent" => (string)$item->discount,
                "reduction_amount" => (string)$item->discount_amount,
                "reduction_amount_tax_incl" => (string)$item->discount_amount*(1+$item->iva_rate),
                "reduction_amount_tax_excl" => (string)$item->discount_amount,
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
                "unit_price_tax_incl" => (string)($item->unit_price)*(1+$item->iva_rate),
                "unit_price_tax_excl" => (string)$item->unit_price,
                "total_price_tax_incl" => (string)($item->amount)*(1+$item->iva_rate),
                "total_price_tax_excl" => (string)$item->amount,
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
        
        return response()->json($orderDetails);
    }
}
