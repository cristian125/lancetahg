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

    // public function exportProducts(Request $request)
    // {
    //     try {
    //         Log::info('Inicio del método exportProducts');

    //         $token = $request->query('token');
    //         $expectedToken = env('EXPORT_PRODUCTS_TOKEN');

    //         if (!$expectedToken || $token !== $expectedToken) {
    //             Log::warning('Token no válido', ['token_recibido' => $token]);
    //             return response()->json(['error' => 'Token no válido'], 401);
    //         }

    //         Log::info('Token validado correctamente');

    //         $products = DB::table('itemsdb')
    //         ->join('inventario', 'itemsdb.no_s', '=', 'inventario.no_s')
    //         ->select(
    //             'itemsdb.id as productId',
    //             'itemsdb.no_s as offerId',  // Alias para no_s
    //             'itemsdb.nombre as title',
    //             'itemsdb.descripcion as description',
    //             'itemsdb.precio_unitario as price',
    //             'inventario.cantidad_disponible as availability',
    //             'itemsdb.proveedor_nombre as marca'
    //         )
    //         ->where('itemsdb.activo', 1)  // Aquí usamos no_s en lugar de offerId
    //         ->get();

    //         Log::info('Productos obtenidos de la base de datos', ['cantidad' => $products->count()]);

    //         $productsData = [];

    //         foreach ($products as $product) {
    //             $productsData[] = [
    //                 'offerId' => $product->offerId,
    //                 'title' => $product->title,
    //                 'description' => $product->description,
    //                 'link' => 'https://new.lancetahg.com.mx/producto/' . $product->productId,
    //                 'imageLink' => 'https://new.lancetahg.com.mx/producto/img/' . $product->offerId,
    //                 'contentLanguage' => 'es',
    //                 'targetCountry' => 'MX',
    //                 'channel' => 'online',
    //                 'availability' => $product->availability > 0 ? 'in stock' : 'out of stock',
    //                 'condition' => 'new',
    //                 'price' => [
    //                     'value' => $product->price,
    //                     'currency' => 'MXN',
    //                 ],
    //                 'brand' => $product->marca,
    //             ];
    //         }

    //         // Divide el arreglo en lotes de 50 productos
    //         $batchSize = 500;
    //         $chunks = array_chunk($productsData, $batchSize);

    //         $responses = [];
    //         foreach ($chunks as $chunk) {
    //             $responses = array_merge($responses, $this->merchantService->insertProductsBatch($chunk));
    //         }

    //         Log::info('Fin del método exportProducts', ['productos_enviados' => count($responses)]);

    //         return response()->json($responses);
    //     } catch (\Exception $e) {
    //         Log::error('Error en exportProducts', ['error' => $e->getMessage()]);
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function exportProducts(Request $request)
    {
        try {
            Log::info('Inicio del método exportProducts');

            $token = $request->query('token');
            $expectedToken = env('EXPORT_PRODUCTS_TOKEN');

            if (!$expectedToken || $token !== $expectedToken) {
                Log::warning('Token no válido', ['token_recibido' => $token]);
                $this->logApiImport('failed', 'Token no válido');
                return response()->json(['error' => 'Token no válido'], 401);
            }

            Log::info('Token validado correctamente');

            $products = DB::table('itemsdb')
                ->join('inventario', 'itemsdb.no_s', '=', 'inventario.no_s')
                ->select(
                    'itemsdb.id as productId',
                    'itemsdb.no_s as offerId',
                    'itemsdb.nombre as title',
                    'itemsdb.descripcion as description',
                    'itemsdb.precio_unitario_IVAinc as price',
                    'inventario.cantidad_disponible as availability',
                    'itemsdb.proveedor_nombre as marca',
                    'itemsdb.codigo_de_producto_minorista as codigoMinorista'
                )
                ->where('itemsdb.activo', 1)
                ->get();

            Log::info('Productos obtenidos de la base de datos', ['cantidad' => $products->count()]);

            if ($products->isEmpty()) {
                $this->logApiImport('success', 'No hay productos disponibles para exportar');
                return response()->json(['message' => 'No hay productos disponibles para exportar']);
            }

            $productsData = [];
            foreach ($products as $product) {
                // Verificar si la descripción está vacía
                if (empty($product->description)) {
                    $relatedProduct = DB::table('itemsdb')
                        ->where('codigo_de_producto_minorista', $product->codigoMinorista)
                        ->where('id', '!=', $product->productId)
                        ->where('activo', 1)
                        ->whereNotNull('descripcion')
                        ->where('descripcion', '!=', '')
                        ->select('descripcion')
                        ->first();

                    if ($relatedProduct) {
                        $product->description = $relatedProduct->descripcion;
                        DB::table('itemsdb')->where('id', $product->productId)->update(['descripcion' => $product->description]);
                    } else {
                        $product->description = 'Descripción no disponible';
                    }
                }

                $productsData[] = [
                    'offerId' => $product->offerId,
                    'title' => $product->title,
                    'description' => $product->description,
                    'link' => 'https://new.lancetahg.com.mx/producto/' . $product->productId,
                    'imageLink' => 'https://new.lancetahg.com.mx/producto/img/' . $product->offerId,
                    'contentLanguage' => 'es',
                    'targetCountry' => 'MX',
                    'channel' => 'online',
                    'availability' => $product->availability > 0 ? 'in stock' : 'out of stock',
                    'condition' => 'new',
                    'price' => [
                        'value' => $product->price,
                        'currency' => 'MXN',
                    ],
                    'brand' => $product->marca,
                ];
            }

            $batchSize = 500;
            $chunks = array_chunk($productsData, $batchSize);
            $totalExported = 0;

            foreach ($chunks as $chunk) {
                $responses = $this->merchantService->insertProductsBatch($chunk);
                $totalExported += count($responses);
            }

            $this->logApiImport('success', "{$totalExported} productos exportados correctamente");

            Log::info('Fin del método exportProducts', ['productos_enviados' => $totalExported]);

            return response()->json(['message' => "{$totalExported} productos exportados a merchant correctamente"]);
        } catch (\Exception $e) {
            Log::error('Error en exportProducts', ['error' => $e->getMessage()]);
            $this->logApiImport('failed', 'Error al exportar productos', $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function logApiImport($status, $message, $errorDetails = null)
    {
        DB::table('api_import_logs')->insert([
            'status' => $status,
            'message' => $message,
            'error_details' => $errorDetails,
            'request_time' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}
