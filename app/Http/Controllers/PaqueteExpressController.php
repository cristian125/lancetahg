<?php
namespace App\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaqueteExpressController extends ProductController
{
    private $id = 0;
    private $user_id = 0;
    private $TotalCart = 0;
    private $CostoEnvio = 0;
    private $CodigoPostalOrigen = '';
    private $ColoniaOrigen = '';

    private $peso_pedido = 0;
    private $volumen_pedido = 0;
    private $pesovolumetrico_pedido = 0;
    PRIVATE $peso_volumetrico = 0;
    private $largo_pedido = 0;
    private $ancho_pedido = 0;
    private $alto_pedido = 0;

    public function __construct($id = 0)
    {
        if (Auth::check()) {
            if ($id !== null) {
                $this->user_id = Auth::user()->id;
            } else {
                $this->user_id = $id;
            }

            $this->id = $this->getIdCart();
            $this->TotalCart = $this->getTotalFromCart();
            $this->CodigoPostalOrigen = env('LANCETA_CODIGO_POSTAL_ORIGEN', '06720');
            $this->ColoniaOrigen = env('LANCETA_COlONIA_ORIGEN', 'CUAUHTEMOC');
        }
    }

    private function getIdCart()
    {
        $queryCarts = DB::table('carts')->select('id')->where('user_id', $this->user_id)->get();
        return $queryCarts[0]->id;
    }

    private function getTotalFromCart()
    {

        $total = 0;
        $queryCartItems = DB::table('cart_items')->select('final_price', 'quantity')->where('cart_id', $this->id)->get();

        foreach ($queryCartItems as $queryCartItem) {
            $totalLine = floatval($queryCartItem->final_price) * intval($queryCartItem->quantity);
            $total += $totalLine;
        }

        return $total;
    }

    private function getDimensions()
    {

        $dimensions = DB::table('cart_items')
            ->join('items_unidades', 'cart_items.no_s', '=', 'items_unidades.item_no')
            ->select(
                'items_unidades.length',
                'items_unidades.width',
                'items_unidades.height',
                'items_unidades.weight',
                DB::raw('sum(cart_items.quantity) as quantity')
            )
            ->where('cart_items.cart_id', $this->id)
            ->groupBy(
                'items_unidades.length',
                'items_unidades.width',
                'items_unidades.height',
                'items_unidades.weight'
            )
            ->get();

        $totalLength = 0; // Largo total en cm
        $maxWidth = 0; // Ancho máximo en cm
        $maxHeight = 0; // Alto máximo en cm
        $totalWeight = 0; // Peso total en KG
        $totalVolume = 0; // Volumen total en m³
        $pesoVolumetrico = 0; // Peso volumétrico en m³

        foreach ($dimensions as $dimension) {
            // Dimensiones en cm
            $length = floatval($dimension->length);
            $width = floatval($dimension->width);
            $height = floatval($dimension->height);
            $weight = floatval($dimension->weight); // Peso del item
            $quantity = floatval($dimension->quantity);

            //Acumula el peso total sumando el peso de cada item multiplicado por su cantidad
            $totalWeight += $weight * $quantity;

            // Acumular el largo total sumando el largo de cada item multiplicado por su cantidad
            $totalLength += $length * $quantity;

            // Tomar el máximo ancho y alto (ya que no se suman como el largo)
            $maxWidth = max($maxWidth, $width);
            $maxHeight = max($maxHeight, $height);

            // Calcular volumen de este item en metros cúbicos (dimensiones en metros)
            $volumenItem = ($length / 100) * ($width / 100) * ($height / 100) * $quantity;

            // Sumar al volumen total
            $totalVolume += $volumenItem;
        }

        // Calcular el peso volumétrico (dividiendo el volumen total entre 5000)
        $pesoVolumetrico = $totalVolume / 5000;

        $this->peso_pedido = $totalWeight;
        $this->volumen_pedido = $totalVolume;
        $this->peso_volumetrico = $pesoVolumetrico;

        $this->largo_pedido = $totalLength;
        $this->ancho_pedido = $maxWidth;
        $this->alto_pedido = $maxHeight;
    }

    private function getRequestCotizador($id, $address_id = null)
    {
        $this->id = $id;
        $this->__construct($id);

        if ($address_id == null) {
            $direccion_envio = DB::table('cart_shippment')->where('cart_id', $id)->first();

            if ($direccion_envio == null) {
                return response()->json(['error' => 'No se tiene elegida ninguna dirección de envío'], 400);
            }
        } else {
            $direccion_envio = DB::table('users_address')->where('id', $address_id)->first();
        }
        // Obtener las dimensiones de los productos en el carrito
        $this->getDimensions();

        $request = [
            "header" => [
                "security" => [
                    "user" => env('PAQUETEEXPRESS_DEMO_USER'),
                    "password" => env('PAQUETEEXPRESS_DEMO_PASSWORD'),
                    "type" => 1,
                    "token" => env('PAQUETEEXPRESS_DEMO_TOKEN'),
                ],
                "device" => [
                    "appName" => 'Customer',
                    "type" => "Web",
                    "ip" => "",
                    "idDevice" => "",
                ],
                "target" => [
                    "module" => "QUOTER",
                    "version" => "1.0",
                    "service" => "quoter",
                    "uri" => "quotes",
                    "event" => "R",
                ],
                "output" => "JSON",
                "language" => null,
            ],
            "body" => [
                "request" => [
                    "data" => [
                        "clientAddrOrig" => [
                            "zipCode" => $this->CodigoPostalOrigen,
                            "colonyName" => $this->ColoniaOrigen,
                        ],
                        "clientAddrDest" => [
                            "zipCode" => $direccion_envio->codigo_postal,
                            "colonyName" => strtoupper($direccion_envio->colonia),
                        ],
                        "services" => [
                            "dlvyType" => "1",
                            "ackType" => "N",
                            "totlDeclVlue" => $this->TotalCart,
                            "invType" => "A",
                            "radType" => "1",
                        ],
                        "otherServices" => [
                            "otherServices" => [],
                        ],
                        "shipmentDetail" => [
                            "shipments" => [
                                "sequence" => 1,
                                "quantity" => 1,
                                "shpCode" => "2",
                                "weight" => $this->peso_pedido,
                                "longShip" => $this->largo_pedido,
                                "widthShip" => $this->ancho_pedido,
                                "highShip" => $this->alto_pedido,
                            ],
                        ],
                        "quoteServices" => [
                            "ALL",
                        ],
                    ],
                    "objectDTO" => null,
                ],
                "response" => null,
            ],
        ];

        return response($request);
    }

    private function getRequestCotizador1($id, $address_id = null)
    {
        $this->id = $id;
        $this->__construct($id);

        if ($address_id == null) {
            $direccion_envio = DB::table('cart_shippment')->where('cart_id', $id)->first();

            if ($direccion_envio == null) {
                return response()->json(['error' => 'No se tiene elegida ninguna dirección de envío'], 400);
            }
        } else {
            $direccion_envio = DB::table('users_address')->where('id', $address_id)->first();
        }
        // Obtener las dimensiones de los productos en el carrito
        $this->getDimensions();

        $shipments[] = [
            "sequence" => 1,
            "quantity" => 1,
            "shpCode" => "2",
            "weight" => $this->peso_pedido,
            "longShip" => $this->largo_pedido,
            "widthShip" => $this->ancho_pedido,
            "highShip" => $this->alto_pedido,
        ];

        $request = [
            "header" => [
                "security" => [
                    "user" => env('PAQUETEEXPRESS_DEMO_USER'),
                    "password" => env('PAQUETEEXPRESS_DEMO_PASSWORD'),
                    "type" => 1,
                    "token" => env('PAQUETEEXPRESS_DEMO_TOKEN'),
                ],
                "device" => [
                    "appName" => 'Customer',
                    "type" => "Web",
                    "ip" => "",
                    "idDevice" => "",
                ],
                "target" => [
                    "module" => "QUOTER",
                    "version" => "1.0",
                    "service" => "quoter",
                    "uri" => "quotes",
                    "event" => "R",
                ],
                "output" => "JSON",
                "language" => null,
            ],
            "body" => [
                "request" => [
                    "data" => [
                        "clientAddrOrig" => [
                            "zipCode" => $this->CodigoPostalOrigen,
                            "colonyName" => $this->ColoniaOrigen,
                        ],
                        "clientAddrDest" => [
                            "zipCode" => $direccion_envio->codigo_postal,
                            "colonyName" => $direccion_envio->colonia,
                        ],
                        "services" => [
                            "dlvyType" => "1",
                            "ackType" => "N",
                            "totlDeclVlue" => $this->TotalCart,
                            "invType" => "A",
                            "radType" => "1",
                        ],
                        "otherServices" => [
                            "otherServices" => [],
                        ],
                        "shipmentDetail" => [
                            "shipments" => $shipments,
                        ],
                        "quoteServices" => [
                            "ALL",
                        ],
                    ],
                    "objectDTO" => null,
                ],
                "response" => null,
            ],
        ];

        return response($request);
    }

    public function showRequestCotizador(Request $request)
    {
        
        $id = $request->id;
        $address_id = $request->id;

        // Obtener el response del método getRequestCotizador
        $response = $this->getRequestCotizador($id, $address_id);

        // Verificar si es un objeto de tipo Response o JSON
        if ($response instanceof \Illuminate\Http\Response) {
            // Si es una respuesta, devuélvela directamente
            return $response;
        }

        // Si es otra cosa (por ejemplo, un array o stdClass), devolverlo como JSON
        return response($response->getContent(), 200, ['Content-Type' => 'json']);
        
    }

    public function sendRequestCotizadorPaqueteExpress(Request $request)
    {
        $id = $request->id;
        $address_id = $request->address_id;

        // Obtener el response del método getRequestCotizador
        $response = $this->getRequestCotizador($id, $address_id);

        $contenido = $response->getContent();
        // Decodificar el contenido JSON a un array asociativo
        $data = json_decode($contenido, true);
        try {
            // Realizar la solicitud POST con el contenido JSON
            $respPE = Http::post(env('PAQUETEEXPRESS_DEMO_URL'), $data);

            // Verificar si la respuesta fue exitosa
            $respPE->throw(); // Lanzará una excepción si el código de estado no está en el rango 200-299

            // Obtener el cuerpo de la respuesta
            $responseBody = json_decode($respPE->body(), true);

            // Obtener el código de estado de la respuesta
            $statusCode = $respPE->status();

            $success = $responseBody['body']['response']['success'];

            if ($success == 'true') {
                $response = $responseBody['body']['response'];
                $amount = $response['data']['quotations'][0]['amount'];

                $subtotal = $amount['subTotlAmnt'];
                $impuestos = $amount['taxAmnt'];
                $total = $amount['totalAmnt'];

                $result = [
                    'success' => true,
                    'data' => [
                        'subtotal' => $subtotal,
                        'impuestos' => $impuestos,
                        'total' => $total,
                    ],
                    'error' => null,
                ];

                return response()->json($result);
            } else {
                $response = $responseBody['body']['response'];
                $messages = $response['messages'];
                foreach ($messages as $message) {
                    $code = $message['code'];
                    $message = $message['description'];
                    // $type = $message['typeError'];

                    return response()->json(['success' => false, 'data' => null, 'error' => ['code' => $code, 'descripcion' => $message]]);
                }
            }

            // Hacer algo con la respuesta (por ejemplo, devolverla a la vista)
            return response()->json($responseBody);
        } catch (RequestException $e) {
            // Manejar excepciones relacionadas con la solicitud HTTP
            // Puedes acceder al cuerpo de la respuesta de error con $e->response
            $errorResponse = $e->response ? $e->response->body() : 'No response';

            // Manejar el error como desees (por ejemplo, devolver un mensaje de error)
            return view('errors.error', [
                'code' => 'Error: ' . $e->getCode(),
                'errorMessage' => 'Error en la solicitud: ' . $e->getMessage(),
                'errorResponse' => $errorResponse,
            ]);
        }
    }
}
