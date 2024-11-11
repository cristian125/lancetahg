<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class OrderDetailsController extends Controller
{
    public function show(Request $request)
    {
        // Verificar si se pasa el filtro de id_order
        $orderId = $request->query('filter')['id_order'] ?? null;

        if (!$orderId) {
            return response()->json(['error' => 'ID de pedido no proporcionado.'], 400);
        }

        // Buscar la orden en la base de datos
        $order = DB::table('orders')->where('id', $orderId)->first();

        if (!$order) {
            return response()->json(['error' => 'Pedido no encontrado.'], 404);
        }

        // Obtener los detalles de los productos de la tabla 'order_items'
        $orderItems = DB::table('order_items')->where('order_id', $order->id)->get();

        // Estructura básica del XML
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><prestashop/>');
        $xml->addAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');

        $orderDetails = $xml->addChild('order_details');

        foreach ($orderItems as $item) {
            $orderDetail = $orderDetails->addChild('order_detail');

            $orderDetail->addChild('id', "<![CDATA[ {$item->id} ]]>");

            $orderDetail->addChild('id_order', "<![CDATA[ {$order->id} ]]>")
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/orders/' . $order->id);

            $orderDetail->addChild('product_id', "<![CDATA[ {$item->product_id} ]]>")
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/products/' . $item->product_id);

            $orderDetail->addChild('product_attribute_id', "<![CDATA[ 0 ]]>")
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/combinations/0');

            $orderDetail->addChild('product_quantity_reinjected', "<![CDATA[ 0 ]]>");

            $orderDetail->addChild('group_reduction', "<![CDATA[ 0.00 ]]>");

            $orderDetail->addChild('discount_quantity_applied', "<![CDATA[ 0 ]]>");

            $orderDetail->addChild('download_hash');

            $orderDetail->addChild('download_deadline', "<![CDATA[ 0000-00-00 00:00:00 ]]>");

            $orderDetail->addChild('id_order_invoice', "<![CDATA[ 0 ]]>");

            $orderDetail->addChild('id_warehouse', "<![CDATA[ 0 ]]>");

            $orderDetail->addChild('id_shop', "<![CDATA[ 1 ]]>");

            $orderDetail->addChild('id_customization', "<![CDATA[ 0 ]]>");

            $orderDetail->addChild('product_name', "<![CDATA[ {$item->description} ]]>");

            $orderDetail->addChild('product_quantity', "<![CDATA[ {$item->quantity} ]]>");

            $orderDetail->addChild('product_quantity_in_stock', "<![CDATA[ {$item->quantity} ]]>");

            $orderDetail->addChild('product_quantity_return', "<![CDATA[ 0 ]]>");

            $orderDetail->addChild('product_quantity_refunded', "<![CDATA[ 0 ]]>");

            // Unit price without discount
            $orderDetail->addChild('product_price', "<![CDATA[ {$item->unit_price} ]]>");

            // Discount percentage
            $orderDetail->addChild('reduction_percent', "<![CDATA[ {$item->discount} ]]>");

            // Precio sin descuento (unit_price) y con descuento (total_price)
            $discountAmount = $item->unit_price * ($item->discount / 100);
            $orderDetail->addChild('reduction_amount', "<![CDATA[ {$discountAmount} ]]>");

            $orderDetail->addChild('reduction_amount_tax_incl', "<![CDATA[ {$discountAmount} ]]>");

            $orderDetail->addChild('reduction_amount_tax_excl', "<![CDATA[ {$discountAmount} ]]>");

            $orderDetail->addChild('product_quantity_discount', "<![CDATA[ 0.000000 ]]>");

            $orderDetail->addChild('product_ean13');

            $orderDetail->addChild('product_isbn');

            $orderDetail->addChild('product_upc');

            $orderDetail->addChild('product_mpn');

            // Product reference from the API (equivalent to product_id)
            $orderDetail->addChild('product_reference', "<![CDATA[ {$item->product_id} ]]>");

            $orderDetail->addChild('product_supplier_reference', "<![CDATA[ RG.9401 ]]>");

            $orderDetail->addChild('product_weight', "<![CDATA[ 0.225000 ]]>");

            $orderDetail->addChild('tax_computation_method', "<![CDATA[ 0 ]]>");

            // Handling IVA as text (IVA16 or IVA0)
            $orderDetail->addChild('id_tax_rules_group', "<![CDATA[ 1  ]]>");

            $orderDetail->addChild('ecotax', "<![CDATA[ 0.000000 ]]>");

            $orderDetail->addChild('ecotax_tax_rate', "<![CDATA[ 0.000 ]]>");

            $orderDetail->addChild('download_nb', "<![CDATA[ 0 ]]>");

            // Handling prices with and without tax
            $orderDetail->addChild('unit_price_tax_incl', "<![CDATA[ {$item->unit_price} ]]>");

            $orderDetail->addChild('unit_price_tax_excl', "<![CDATA[ {$item->unit_price} ]]>");

            $orderDetail->addChild('total_price_tax_incl', "<![CDATA[ {$item->total_price} ]]>");

            $orderDetail->addChild('total_price_tax_excl', "<![CDATA[ {$item->total_price} ]]>");

            $orderDetail->addChild('total_shipping_price_tax_excl', "<![CDATA[ 0.000000 ]]>");

            $orderDetail->addChild('total_shipping_price_tax_incl', "<![CDATA[ 0.000000 ]]>");

            $orderDetail->addChild('purchase_supplier_price', "<![CDATA[ 24.540000 ]]>");

            $orderDetail->addChild('original_product_price', "<![CDATA[ {$item->unit_price} ]]>");

            $orderDetail->addChild('original_wholesale_price', "<![CDATA[ 24.540000 ]]>");

            $orderDetail->addChild('total_refunded_tax_excl', "<![CDATA[ 0.000000 ]]>");

            $orderDetail->addChild('total_refunded_tax_incl', "<![CDATA[ 0.000000 ]]>");

            // Agregar asociación de impuestos
            $associations = $orderDetail->addChild('associations');
            $taxes = $associations->addChild('taxes');
            $taxes->addAttribute('nodeType', 'tax');
            $taxes->addAttribute('api', 'taxes');
            $tax = $taxes->addChild('tax');
            $tax->addAttribute('xlink:href', 'http://lancetapruebas.com/api/taxes/');
            $tax->addChild('id');
        }



        // Convertir el XML a una respuesta con formato y saltos de línea
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return response($dom->saveXML(), 200)->header('Content-Type', 'application/xml');
    }
}
