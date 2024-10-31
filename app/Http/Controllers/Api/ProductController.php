<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class ProductController extends Controller
{
    /**
     * Método para consultar productos y precios desde APIs externas y almacenarlos en la tabla items_data.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Método para consultar productos y precios desde APIs externas y almacenarlos en la tabla items_data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchItems(Request $request)
    {
        $apiKey = $request->query('api_key');
    
        if ($apiKey !== env('EXTERNAL_API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        try {
            $urlProductos = env('EXTERNAL_API_BASE_URL') . "?accion=PRODUCTOS&token=" . env('EXTERNAL_API_TOKEN');
            $urlPrecios = env('EXTERNAL_API_BASE_URL') . "?accion=PRECIOS&token=" . env('EXTERNAL_API_TOKEN');
    
            $responseProductos = Http::timeout(100)->get($urlProductos);
            if ($responseProductos->failed()) {
                $this->logApiImport('failed', 'Error al conectar con la API de productos.', $responseProductos->body());
                return response()->json(['error' => 'Error al conectar con la API de productos.'], 500);
            }
    
            $responsePrecios = Http::timeout(100)->get($urlPrecios);
            if ($responsePrecios->failed()) {
                $this->logApiImport('failed', 'Error al conectar con la API de precios.', $responsePrecios->body());
                return response()->json(['error' => 'Error al conectar con la API de precios.'], 500);
            }
    
            $productosData = $responseProductos->json();
            $preciosData = $responsePrecios->json();
    
            $preciosMap = [];
            foreach ($preciosData as $precio) {
                if (isset($precio['no'])) {  // Validar que el índice 'no' existe
                    $preciosMap[$precio['no']] = $precio['precio'];
                } else {
                    Log::warning('Precio omitido por falta de índice "no".', ['precio' => $precio]);
                }
            }
    
            DB::beginTransaction();
    
            // Desactivar todos los productos en `itemsdb`
            DB::table('itemsdb')->update(['activo' => 0]);

            // Procesar productos y activar los que vienen en la respuesta
            foreach ($productosData as $producto) {
                if (!isset($producto['no']) || !isset($preciosMap[$producto['no']])) {
                    Log::warning('Producto omitido por falta de "no" o precio.', ['producto' => $producto]);
                    continue;
                }
    
                $numeroSerie = $producto['no'];
    
                DB::table('items_data')->updateOrInsert(
                    ['no_s' => $numeroSerie],
                    [
                        'precio_unitario' => $preciosMap[$numeroSerie],
                        'nombre' => $producto['desc'] ?? 'Sin descripción',
                        'unidad_medida_venta' => $producto['unidad'] ?? 'N/A',
                        'cod_categoria_producto' => $producto['categoria'] ?? 'N/A',
                        'cod_division' => $producto['division'] ?? 'N/A',
                        'codigo_de_producto_minorista' => $producto['prod_min'] ?? 'N/A',
                        'grupo_iva' => $producto['g_iva'] ?? 'N/A',
                        'proveedor' => $producto['prov'] ?? 'N/A',
                        'proveedor_nombre' => $producto['prov_nombre'] ?? 'N/A'
                    ]
                );
    
                // Activar el producto en `itemsdb`
                DB::table('itemsdb')
                    ->updateOrInsert(
                        ['no_s' => $numeroSerie],
                        ['activo' => 1]
                    );
    
                Log::info('Producto importado y activado:', [
                    'no_s' => $numeroSerie,
                    'nombre' => $producto['desc'] ?? 'Sin descripción',
                ]);
            }
    
            DB::commit();
            $this->logApiImport('success', 'Productos, precios, descuentos e inventario importados exitosamente.');
    
            return response()->json(['message' => 'Productos y precios actualizados exitosamente.'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->logApiImport('failed', 'Error al importar productos y precios.', $e->getMessage());
            Log::error('Error al importar productos y precios: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Ocurrió un error: ' . $e->getMessage()], 500);
        }
    }
    
    




    protected function fetchInventory(Request $request)
    {
        try {
            $urlExistencias = env('EXTERNAL_API_BASE_URL') . "?accion=EXISTENCIAS&token=" . env('EXTERNAL_API_TOKEN');
            $responseExistencias = Http::timeout(10)->get($urlExistencias);

            if ($responseExistencias->failed()) {
                $this->logApiImport('failed', 'Error al conectar con la API de existencias.', $responseExistencias->body());
                return response()->json(['error' => 'Error al conectar con la API de existencias.'], 500);
            }

            $existenciasData = $responseExistencias->json();

            DB::beginTransaction();

            // Inicializar todas las existencias a 0 antes de actualizar con datos reales
            DB::table('inventario')->update(['cantidad_disponible' => 0]);

            foreach ($existenciasData as $existencia) {
                $no_s = $existencia['p'];
                $cantidadDisponible = $existencia['c'];

                DB::table('inventario')->updateOrInsert(
                    ['no_s' => $no_s],
                    [
                        'cantidad_disponible' => $cantidadDisponible,
                        'modificada_por' => 'api',
                        'fecha_modificacion' => now(),
                    ]
                );

                Log::info('Existencias actualizadas para producto:', [
                    'no_s' => $no_s,
                    'cantidad_disponible' => $cantidadDisponible,
                ]);
            }

            DB::commit();
            $this->logApiImport('success', 'Existencias importadas exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->logApiImport('failed', 'Error al importar existencias.', $e->getMessage());
            Log::error('Error al importar existencias: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }





    /**
     * Método para actualizar itemsdb basado en los datos de items_data.
     */
    protected function updateItemsDatabase()
    {
        try {
            DB::beginTransaction();

            // Desactivar productos en `itemsdb` que no están en `items_data`
            DB::table('itemsdb')
                ->whereNotIn('no_s', DB::table('items_data')->pluck('no_s'))
                ->update(['activo' => 0]);

            // Obtener datos de `items_data`
            $itemsData = DB::table('items_data')->get();

            foreach ($itemsData as $item) {
                // Calcular el precio con IVA (si aplica)
                $precioConIva = $item->precio_unitario;
                if ($item->grupo_iva === 'IVA16') {
                    $precioConIva *= 1.16;  // IVA 16%
                }

                // Actualizar `itemsdb` con los datos necesarios de `items_data`
                DB::table('itemsdb')->updateOrInsert(
                    ['no_s' => $item->no_s], // Clave única para coincidir registros
                    [
                        'precio_unitario' => $item->precio_unitario,
                        'precio_unitario_IVAinc' => $precioConIva,
                        'nombre' => $item->nombre,
                        'unidad_medida_venta' => $item->unidad_medida_venta,
                        'cod_categoria_producto' => $item->cod_categoria_producto,
                        'cod_division' => $item->cod_division,
                        'codigo_de_producto_minorista' => $item->codigo_de_producto_minorista,
                        'grupo_iva' => $item->grupo_iva,
                        'proveedor' => $item->proveedor,
                        'proveedor_nombre' => $item->proveedor_nombre,
                        'activo' => 1, // Marca como activo
                    ]
                );
            }

            DB::commit();
            Log::info('Actualización de itemsdb completada.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar items en la base de datos: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function fetchDiscounts(Request $request)
    {
        try {
            $urlDescuentos = env('EXTERNAL_API_BASE_URL') . "?accion=DESCUENTOS&token=" . env('EXTERNAL_API_TOKEN');
            $responseDescuentos = Http::timeout(10)->get($urlDescuentos);

            if ($responseDescuentos->failed()) {
                $this->logApiImport('failed', 'Error al conectar con la API de descuentos.', $responseDescuentos->body());
                return response()->json(['error' => 'Error al conectar con la API de descuentos.'], 500);
            }

            $descuentosData = $responseDescuentos->json();

            DB::beginTransaction();

            // Establecer todos los descuentos en `itemsdb` a 0
            DB::table('itemsdb')->update(['descuento' => 0]);

            // Limpiar la tabla `items_data_descuentos` antes de insertar los nuevos descuentos
            DB::table('items_data_descuentos')->truncate();

            foreach ($descuentosData as $descuento) {
                $no_s = $descuento['no'];
                $valorDescuento = $descuento['descuento'];

                // Insertar datos en la tabla `items_data_descuentos`
                DB::table('items_data_descuentos')->insert([
                    'id' => $descuento['id'],
                    'ofno' => $descuento['ofno'],
                    'ofdesc' => $descuento['ofdesc'],
                    'no_s' => $no_s,
                    'descuento' => $valorDescuento,
                    'fecha_ini' => $descuento['fecha_ini'],
                    'fecha_fin' => $descuento['fecha_fin'],
                    'fecha_creacion' => now(),
                ]);

                Log::info('Descuento registrado en items_data_descuentos:', [
                    'no_s' => $no_s,
                    'descuento' => $valorDescuento,
                ]);
            }

            // Actualizar `itemsdb` con los descuentos de `items_data_descuentos`
            DB::table('itemsdb')
                ->join('items_data_descuentos', 'itemsdb.no_s', '=', 'items_data_descuentos.no_s')
                ->update([
                    'itemsdb.descuento' => DB::raw('items_data_descuentos.descuento'),
                    'itemsdb.modificada_por' => DB::raw("'api'"),
                    'itemsdb.fecha_modificacion' => now(),
                ]);

            DB::commit();
            $this->logApiImport('success', 'Descuentos importados y actualizados exitosamente en itemsdb.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->logApiImport('failed', 'Error al importar descuentos.', $e->getMessage());
            Log::error('Error al importar descuentos: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }


    /**
     * Registra el resultado de la importación en la tabla api_import_logs.
     *
     */
    protected function logApiImport($status, $message, $errorDetails = null)
    {
        DB::table('api_import_logs')->insert([
            'status' => $status,
            'message' => $message,
            'error_details' => $errorDetails,
            'request_time' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
