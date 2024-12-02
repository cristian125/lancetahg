<?php

namespace App\Http\Controllers;

use App\Services\GoogleMerchantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleMerchantController extends Controller
{
    protected $merchantService;

    public function __construct(GoogleMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    public function exportProducts(Request $request)
    {
        try {
            // Log: Inicio del proceso
            Log::info('Inicio del método exportProducts');
        
            // Valida el token pasado como parámetro
            $token = $request->query('token');
            $expectedToken = env('EXPORT_PRODUCTS_TOKEN');
        
            if (!$expectedToken || $token !== $expectedToken) {
                Log::warning('Token no válido', ['token_recibido' => $token]);
                return response()->json(['error' => 'Token no válido'], 401);
            }
        
            // Log: Token válido
            Log::info('Token validado correctamente');
        
            // Consulta para obtener productos activos
            $products = DB::table('itemsdb')
                ->join('inventario', 'itemsdb.no_s', '=', 'inventario.no_s')
                ->select(
                    'itemsdb.id as productId', // Aquí seleccionamos el campo id
                    'itemsdb.no_s as offerId',
                    'itemsdb.nombre as title',
                    'itemsdb.descripcion as description',
                    'itemsdb.precio_unitario as price',
                    'inventario.cantidad_disponible as availability'
                )
                ->where('itemsdb.activo', 1)
                ->get();
        
            // Log: Cantidad de productos obtenidos
            Log::info('Productos obtenidos de la base de datos', ['cantidad' => $products->count()]);
        
            $responses = [];
        
            foreach ($products as $product) {
                // Log: Producto en proceso
                Log::info('Procesando producto', ['offerId' => $product->offerId]);
               
                $productData = [
                    'offerId' => $product->offerId,
                    'title' => $product->title,
                    'description' => $product->description,
                    // Usamos el ID del producto en la URL
                    'link' => 'https://new.lancetahg.com.mx/producto/' . $product->productId,
                    'imageLink' => route('producto.imagen', ['id' => $product->productId]),
                    'contentLanguage' => 'es',
                    'targetCountry' => 'MX',
                    'channel' => 'online',
                    'availability' => $product->availability > 0 ? 'in stock' : 'out of stock',
                    'condition' => 'new',
                    'price' => [
                        'value' => $product->price,
                        'currency' => 'MXN',
                    ],
                ];
        
                try {
                    $response = $this->merchantService->insertProduct($productData);
                    $responses[] = $response;
        
                    // Log: Producto enviado con éxito
                    Log::info('Producto enviado a Google Merchant', ['offerId' => $product->offerId]);
                } catch (\Exception $e) {
                    $responses[] = ['error' => $e->getMessage(), 'product' => $productData];
        
                    // Log: Error al enviar el producto
                    Log::error('Error al enviar producto', [
                        'offerId' => $product->offerId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        
            // Log: Fin del proceso
            Log::info('Fin del método exportProducts', ['productos_enviados' => count($responses)]);
        
            return response()->json($responses);

        } catch (\Exception $e) {
            // Manejo de errores globales (e.g., archivo de credenciales no encontrado)
            Log::error('Error en exportProducts', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
