<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersHeaderController extends Controller
{
    /**
     * Mostrar las órdenes con estado específico (por defecto 2).
     */
    public function showOrdersWithState1(Request $request)
    {
        // Filtrar por el estado de la orden (current_state = 2 por defecto)
        $currentState = $request->query('filter')['current_state'] ?? 2;

        // Obtener las órdenes desde la base de datos con el estado actual
        $orders = DB::table('orders')
            ->where('current_state', $currentState)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['error' => 'No orders found with current state'], 404);
        }

        // Crear la estructura básica del XML
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><prestashop/>');
        $xml->addAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $ordersXml = $xml->addChild('orders');

        foreach ($orders as $order) {
            $orderXml = $ordersXml->addChild('order');

            // Añadir los campos de la tabla 'orders'
            $orderXml->addChild('id', "<! [CDATA[{$order->id}]] >");

            // Usar valores por defecto para los campos que no existen en la base de datos
            $orderXml->addChild('id_address_delivery', "<! [CDATA[0]] >"); // Valor por defecto 0
            $orderXml->addChild('id_address_invoice', "<! [CDATA[0]] >")
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/addresses/' . $order->id); // Puedes ajustar según sea necesario

            // Agregar más campos como el carrito, moneda, cliente, etc.
            $orderXml->addChild('id_cart', "<! [CDATA[1]] >"); // Simulamos con el id de la orden

            $orderXml->addChild('id_currency', "<! [CDATA[1]] >") // Puedes simular con un valor fijo
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/currencies/1');

            $orderXml->addChild('id_lang', "<! [CDATA[2]] >") // Valor fijo simulando el idioma
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/languages/2');

            $orderXml->addChild('id_customer', "<! [CDATA[ {$order->user_id} ]] >")
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/customers/' . $order->user_id);

            $orderXml->addChild('id_carrier', "<! [CDATA[223]] >") // Valor fijo para carrier
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/carriers/223');

            $orderXml->addChild('current_state', "<! [CDATA[ {$order->current_state} ]] >")
                ->addAttribute('xlink:href', 'http://lancetapruebas.com/api/order_states/' . $order->current_state);

            $orderXml->addChild('module', "<! [CDATA[firstdata]] >"); // Valor fijo para módulo de pago

            // Campos adicionales que deben tener valores predeterminados o ficticios
            $orderXml->addChild('invoice_number', "<! [CDATA[0]] >");
            $orderXml->addChild('invoice_date', "<! [CDATA[0000-00-00 00:00:00]] >");
            $orderXml->addChild('delivery_number', "<! [CDATA[0]] >");
            $orderXml->addChild('delivery_date', "<! [CDATA[0000-00-00 00:00:00]] >");
            $orderXml->addChild('valid', "<! [CDATA[1]] >");
            $orderXml->addChild('date_add', "<! [CDATA[ {$order->created_at} ]] >"); // Fecha de creación
            $orderXml->addChild('date_upd', "<! [CDATA[ {$order->updated_at} ]] >"); // Fecha de actualización
            $orderXml->addChild('shipping_number'); // Puede estar vacío
            $orderXml->addChild('id_shop_group', "<! [CDATA[1]] >"); // Valor predeterminado
            $orderXml->addChild('id_shop', "<! [CDATA[1]] >"); // Valor predeterminado

            // Seguro y otros campos
            $orderXml->addChild('secure_key', "<! [CDATA[21f003ace3ecdfda54bed9062d58c26e]] >");
            $orderXml->addChild('payment', "<! [CDATA[Tarjeta Bancaria]] >"); // Método de pago real

            // Otros campos pueden ser simulados según necesidad
            $orderXml->addChild('recyclable', "<! [CDATA[0]] >");
            $orderXml->addChild('gift', "<! [CDATA[0]] >");
            $orderXml->addChild('gift_message');
            $orderXml->addChild('mobile_theme', "<! [CDATA[1]] >");
            $orderXml->addChild('total_discounts', "<! [CDATA[0.000000]] >");
            $orderXml->addChild('total_discounts_tax_incl', "<! [CDATA[0.000000]] >");
            $orderXml->addChild('total_discounts_tax_excl', "<! [CDATA[0.000000]] >");
            $orderXml->addChild('total_paid', "<! [CDATA[ {$order->total} ]] >"); // Total real
            $orderXml->addChild('total_paid_tax_incl', "<! [CDATA[{$order->total_con_iva}]] >"); // Total con IVA
            $orderXml->addChild('total_paid_tax_excl', "<! [CDATA[{$order->total}]] >"); // Total sin IVA
            $orderXml->addChild('total_paid_real', "<! [CDATA[ {$order->total}]] >"); // Total real pagado
            $orderXml->addChild('total_products', "<! [CDATA[ {$order->subtotal_sin_envio}]] >"); // Subtotal sin envío
            $orderXml->addChild('total_products_wt', "<! [CDATA[{$order->total_con_iva}]] >"); // Total con IVA
            $orderXml->addChild('total_shipping', "<! [CDATA[ {$order->shipping_cost}]] >"); // Costo de envío real
            $orderXml->addChild('total_shipping_tax_incl', "<! [CDATA[{$order->shipping_cost}]] >"); // Costo de envío con IVA
            $orderXml->addChild('total_shipping_tax_excl', "<! [CDATA[{$order->shipping_cost}]] >"); // Costo de envío sin IVA
            $orderXml->addChild('carrier_tax_rate', "<! [CDATA[0.000]] >");
            $orderXml->addChild('total_wrapping', "<! [CDATA[0.000000]] >");
            $orderXml->addChild('total_wrapping_tax_incl', "<! [CDATA[0.000000]] >");
            $orderXml->addChild('total_wrapping_tax_excl', "<! [CDATA[0.000000]] >");
            $orderXml->addChild('round_mode', "<! [CDATA[2]] >");
            $orderXml->addChild('round_type', "<! [CDATA[2]] >");
            $orderXml->addChild('conversion_rate', "<! [CDATA[1.000000]] >");
            $orderXml->addChild('reference', "<! [CDATA[{$order->oid} ]] >");
            $orderXml->addChild('lscode_forma_pago', "<! [CDATA[TARJ DEB]] >"); // Valor simulado
            $orderXml->addChild('transaction_id', "<! [CDATA[0]] >"); // Valor simulado
            $orderXml->addChild('card_number', "<! [CDATA[0]] >"); // Valor simulado
            $orderXml->addChild('card_brand', "<! [CDATA[0]] >"); // Valor simulado
            $orderXml->addChild('card_holder', "<! [CDATA[0]] >"); // Valor simulado
            $orderXml->addChild('amount', "<! [CDATA[0]] >");
            $orderXml->addChild('lscode_cliente', "<! [CDATA[0]] >"); // Valor simulado
            $orderXml->addChild('tipo_entrega', "<! [CDATA[DOMICILIO]] >"); // Simulado
            $orderXml->addChild('opcion_envio', "<! [CDATA[ESTD]] >"); // Simulado

            // Agregar asociaciones de líneas del pedido
            $associations = $orderXml->addChild('associations');
            $orderRows = $associations->addChild('order_rows');
            $orderRows->addAttribute('nodeType', 'order_row');
            $orderRows->addAttribute('virtualEntity', 'true');
            $orderRows->addChild('order_row');

            // Puedes añadir más campos que sean necesarios según la estructura prestada.
        }

        // Convertir el XML a formato de cadena
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $xmldoc = $dom->saveXML();

        // Devolver el XML como respuesta
        return response($xmldoc, 200, ['Content-Type' => 'application/xml']);
    }

    // public function SendOrderstoBC(Request $request)
    // {
    //     $xmldoc = $this->showOrdersWithState($request);

    //     // Crear instancia del cliente Guzzle
    //     $client = new Client();

    //     // Definir la URL base y los parámetros de la consulta
    //     $url = env('');

    //     // Hacer la petición GET con parámetros
    //     $token = csrf_token();
    //     $response = $client->request('POST', $url, [
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //             'X-CSRF-TOKEN' => $token,
    //         ],
    //         'form_params' => [
    //             'id' => $id,
    //             'address_id' => $direccionId,
    //             '_token' => $token,
    //         ],
    //     ]);

    //     return response($xmldoc, 200, ['Content-Type' => 'text/xml']);
    // }




        public function showOrdersWithState(Request $request)
        {
            // Filtrar por el estado de la orden (current_state = 2 por defecto)
            $currentState = $request->query('filter')['current_state'] ?? 2;
    
            // Obtener las órdenes desde la base de datos con el estado actual
            $orders = DB::table('orders')
                ->where('current_state', $currentState)
                ->get();
    
            if ($orders->isEmpty()) {
                return response()->json(['error' => 'No orders found with current state'], 404);
            }
    
            // Crear la estructura básica del XML con DOMDocument
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->formatOutput = true;
    
            $prestashop = $dom->createElement('prestashop');
            $prestashop->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
            $dom->appendChild($prestashop);
    
            $ordersXml = $dom->createElement('orders');
            $prestashop->appendChild($ordersXml);
    
            foreach ($orders as $order) {
                $orderXml = $dom->createElement('order');
                $ordersXml->appendChild($orderXml);
    
                // Añadir campos de la tabla 'orders'
                $this->addCData($dom, $orderXml, 'id', trim($order->id)); // trim en cada campo para eliminar espacios
    
                // Campos simulados o valores por defecto
                $this->addCData($dom, $orderXml, 'id_address_delivery', '0');
                $this->addCData($dom, $orderXml, 'id_address_invoice', '0', 'http://lancetapruebas.com/api/addresses/' . trim($order->id));
                $this->addCData($dom, $orderXml, 'id_cart', '1', 'http://lancetapruebas.com/api/carts/1');
                $this->addCData($dom, $orderXml, 'id_currency', '1', 'http://lancetapruebas.com/api/currencies/1');
                $this->addCData($dom, $orderXml, 'id_lang', '2', 'http://lancetapruebas.com/api/languages/2');
                $this->addCData($dom, $orderXml, 'id_customer', trim($order->user_id), 'http://lancetapruebas.com/api/customers/' . trim($order->user_id));
                $this->addCData($dom, $orderXml, 'id_carrier', '223', 'http://lancetapruebas.com/api/carriers/223');
                $this->addCData($dom, $orderXml, 'current_state', trim($order->current_state), 'http://lancetapruebas.com/api/order_states/' . trim($order->current_state));
                $this->addCData($dom, $orderXml, 'module', 'firstdata');
    
                // Campos adicionales simulados
                $this->addCData($dom, $orderXml, 'invoice_number', '0');
                $this->addCData($dom, $orderXml, 'invoice_date', '0000-00-00 00:00:00');
                $this->addCData($dom, $orderXml, 'delivery_number', '0');
                $this->addCData($dom, $orderXml, 'delivery_date', '0000-00-00 00:00:00');
                $this->addCData($dom, $orderXml, 'valid', '1');
                $this->addCData($dom, $orderXml, 'date_add', trim($order->created_at));
                $this->addCData($dom, $orderXml, 'date_upd', trim($order->updated_at));
                $orderXml->appendChild($dom->createElement('shipping_number')); // Campo vacío
                $this->addCData($dom, $orderXml, 'id_shop_group', '1');
                $this->addCData($dom, $orderXml, 'id_shop', '1');
                $this->addCData($dom, $orderXml, 'secure_key', '21f003ace3ecdfda54bed9062d58c26e');
                $this->addCData($dom, $orderXml, 'payment', 'Tarjeta Bancaria');
    
                // Campos simulados adicionales
                $this->addCData($dom, $orderXml, 'recyclable', '0');
                $this->addCData($dom, $orderXml, 'gift', '0');
                $orderXml->appendChild($dom->createElement('gift_message')); // Campo vacío
                $this->addCData($dom, $orderXml, 'mobile_theme', '1');
                $this->addCData($dom, $orderXml, 'total_discounts', '0.000000');
                $this->addCData($dom, $orderXml, 'total_discounts_tax_incl', '0.000000');
                $this->addCData($dom, $orderXml, 'total_discounts_tax_excl', '0.000000');
                $this->addCData($dom, $orderXml, 'total_paid', trim($order->total));
                $this->addCData($dom, $orderXml, 'total_paid_tax_incl', trim($order->total_con_iva));
                $this->addCData($dom, $orderXml, 'total_paid_tax_excl', trim($order->total));
                $this->addCData($dom, $orderXml, 'total_paid_real', trim($order->total));
                $this->addCData($dom, $orderXml, 'total_products', trim($order->subtotal_sin_envio));
                $this->addCData($dom, $orderXml, 'total_products_wt', trim($order->total_con_iva));
                $this->addCData($dom, $orderXml, 'total_shipping', trim($order->shipping_cost));
                $this->addCData($dom, $orderXml, 'total_shipping_tax_incl', trim($order->shipping_cost));
                $this->addCData($dom, $orderXml, 'total_shipping_tax_excl', trim($order->shipping_cost));
                $this->addCData($dom, $orderXml, 'carrier_tax_rate', '0.000');
                $this->addCData($dom, $orderXml, 'total_wrapping', '0.000000');
                $this->addCData($dom, $orderXml, 'total_wrapping_tax_incl', '0.000000');
                $this->addCData($dom, $orderXml, 'total_wrapping_tax_excl', '0.000000');
                $this->addCData($dom, $orderXml, 'round_mode', '2');
                $this->addCData($dom, $orderXml, 'round_type', '2');
                $this->addCData($dom, $orderXml, 'conversion_rate', '1.000000');
                $this->addCData($dom, $orderXml, 'reference', trim($order->oid));
                $this->addCData($dom, $orderXml, 'lscode_forma_pago', 'TARJ DEB');
                $this->addCData($dom, $orderXml, 'transaction_id', '0');
                $this->addCData($dom, $orderXml, 'card_number', '0');
                $this->addCData($dom, $orderXml, 'card_brand', '0');
                $this->addCData($dom, $orderXml, 'card_holder', '0');
                $this->addCData($dom, $orderXml, 'amount', '0');
                $this->addCData($dom, $orderXml, 'lscode_cliente', '0');
                $this->addCData($dom, $orderXml, 'tipo_entrega', 'DOMICILIO');
                $this->addCData($dom, $orderXml, 'opcion_envio', 'ESTD');
    
                // Asociaciones de líneas de pedido simuladas
                $associations = $dom->createElement('associations');
                $orderXml->appendChild($associations);
    
                $orderRows = $dom->createElement('order_rows');
                $orderRows->setAttribute('nodeType', 'order_row');
                $orderRows->setAttribute('virtualEntity', 'true');
                $associations->appendChild($orderRows);
    
                $orderRow = $dom->createElement('order_row');
                $orderRows->appendChild($orderRow);
            }
    
            // Devolver el XML como respuesta
            // return response($dom->saveXML(), 200, ['Content-Type' => 'application/soap+xml;charset=UTF-8']);
            return response($dom->saveXML(), 200, ['Content-Type' => 'text/xml']);
        }
    
        private function addCData($dom, $parent, $name, $value, $xlink = null)
        {
            $element = $dom->createElement($name);
            $cdata = $dom->createCDATASection($value);     
            $element->appendChild($cdata);
    
            if ($xlink) {
                $element->setAttribute('xlink:href', $xlink);
            }
    
            $parent->appendChild($element);
        }

}
