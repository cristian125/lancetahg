<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Método para consultar productos y precios desde APIs externas y actualizar itemsdb directamente.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchItems(Request $request)
    {
        // Validación de la solicitud
        $validatedData = $request->validate([
            'api_key' => 'required|string',
            // Puedes agregar más validaciones según sea necesario
        ]);

        // Validar si la API key es correcta
        if ($validatedData['api_key'] !== env('EXTERNAL_API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Ejecutar las funciones de actualización
            $this->fetchProductos($request);
            $this->fetchInventory($request);
            $this->fetchDiscounts($request);
        } catch (Exception $ex) {
            // Manejo de errores
            Log::error('Error en fetchItems:', ['error' => $ex->getMessage()]);
            return response()->json(['message' => 'No se pudo actualizar.'], 500);
        }
        

        return response()->json(['message' => 'Productos y precios actualizados exitosamente.'], 200);
    }

    /**
     * Método para consultar productos y precios desde APIs externas y actualizar itemsdb.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchProductos(Request $request)
    {
        $apiKey = $request->query('api_key');

        if ($apiKey !== env('EXTERNAL_API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $urlProductos = env('EXTERNAL_API_BASE_URL') . "?accion=PRODUCTOS&token=" . env('EXTERNAL_API_TOKEN');
        $urlPrecios = env('EXTERNAL_API_BASE_URL') . "?accion=PRECIOS&token=" . env('EXTERNAL_API_TOKEN');

        $responseProductos = Http::get($urlProductos);
        if ($responseProductos->failed()) {
            $this->logApiImport('failed', 'Error al conectar con la API de productos.', $responseProductos->body());
            return response()->json(['error' => 'Error al conectar con la API de productos.'], 500);
        }

        $responsePrecios = Http::get($urlPrecios);
        if ($responsePrecios->failed()) {
            $this->logApiImport('failed', 'Error al conectar con la API de precios.', $responsePrecios->body());
            return response()->json(['error' => 'Error al conectar con la API de precios.'], 500);
        }

        $productosData = $responseProductos->json();
        $preciosData = $responsePrecios->json();

        // Validar si la API devolvió productos
        if (empty($productosData)) {
            Log::warning('La API no devolvió productos. No se realizaron cambios en los productos existentes.');
            return response()->json(['message' => 'La API no devolvió productos. No se realizaron cambios.'], 200);
        }

        $preciosMap = [];
        foreach ($preciosData as $precio) {
            if (isset($precio['no'])) { // Validar que el índice 'no' existe
                $preciosMap[$precio['no']] = $precio['precio'];
            } else {
                Log::warning('Precio omitido por falta de índice "no".', ['precio' => $precio]);
            }
        }

        DB::beginTransaction();

        // Guardar los productos activos provenientes de la API
        $productosDesdeApi = [];

        foreach ($productosData as $producto) {
            if (!isset($producto['no']) || !isset($preciosMap[$producto['no']])) {
                Log::warning('Producto omitido por falta de "no" o precio.', ['producto' => $producto]);
                continue;
            }

            $numeroSerie = $producto['no'];
            $productosDesdeApi[] = $numeroSerie;

            // Reunir los datos necesarios para actualizar `itemsdb`
            $updateData = [
                'precio_unitario' => $preciosMap[$numeroSerie],
                'nombre_bc' => $producto['desc'] ?? 'Sin descripción',
                'unidad_medida_venta' => $producto['unidad'] ?? 'N/A',
                'cod_categoria_producto' => $producto['categoria'] ?? 'N/A',
                'cod_division' => $producto['division'] ?? 'N/A',
                'codigo_de_producto_minorista' => $producto['prod_min'] ?? 'N/A',
                'grupo_iva' => $producto['g_iva'] ?? 'N/A',
                'proveedor' => $producto['prov'] ?? 'N/A',
                'proveedor_nombre' => $producto['prov_nombre'] ?? 'N/A',
                'activo' => 1, // Marcar como activo
            ];

            // Calcular precio_unitario_IVAinc si aplica
            if ($updateData['grupo_iva'] === 'IVA16') {
                $updateData['precio_unitario_IVAinc'] = $updateData['precio_unitario'] * 1.16;
            } else {
                $updateData['precio_unitario_IVAinc'] = $updateData['precio_unitario'];
            }

            // Actualizar o insertar en `itemsdb`
            DB::table('itemsdb')->updateOrInsert(
                ['no_s' => $numeroSerie],
                $updateData
            );

            Log::info('Producto importado y activado:', [
                'no_s' => $numeroSerie,
                'nombre_bc' => $updateData['nombre_bc'],
            ]);
        }

        // Desactivar productos que no están en la API solo si llegaron productos desde la API
        if (!empty($productosDesdeApi)) {
            DB::table('itemsdb')
                ->whereNotIn('no_s', $productosDesdeApi)
                ->update(['activo' => 0]);
        }
        
        DB::commit();
        $this->logApiImport('success', 'Productos importados y actualizados exitosamente en itemsdb.');
        $this->desactivarProductosInvalidos();
         // Llamas a la función
    $this->desactivarProductosSinCubage();

        return response()->json(['message' => 'Productos y precios actualizados exitosamente.'], 200);
    }

    protected function fetchInventory(Request $request)
    {
        $urlExistencias = env('EXTERNAL_API_BASE_URL') . "?accion=EXISTENCIAS&token=" . env('EXTERNAL_API_TOKEN');
        $responseExistencias = Http::timeout(450)->get($urlExistencias);

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
    }

    protected function fetchDiscounts(Request $request)
    {
        $urlDescuentos = env('EXTERNAL_API_BASE_URL') . "?accion=DESCUENTOS&token=" . env('EXTERNAL_API_TOKEN');
        $responseDescuentos = Http::get($urlDescuentos);

        if ($responseDescuentos->failed()) {
            $this->logApiImport('failed', 'Error al conectar con la API de descuentos.', $responseDescuentos->body());
            return response()->json(['error' => 'Error al conectar con la API de descuentos.'], 500);
        }

        $descuentosData = $responseDescuentos->json();

        DB::beginTransaction();

        // Establecer todos los descuentos en `itemsdb` a 0
        DB::table('itemsdb')->update(['descuento' => 0]);

        foreach ($descuentosData as $descuento) {
            $no_s = $descuento['no'];
            $valorDescuento = $descuento['descuento'];

            // Actualizar el descuento en `itemsdb`
            $updated = DB::table('itemsdb')->where('no_s', $no_s)->update([
                'descuento' => $valorDescuento,
                'modificada_por' => 'api',
                'fecha_modificacion' => now(),
            ]);

            if ($updated) {
                Log::info('Descuento actualizado en itemsdb:', [
                    'no_s' => $no_s,
                    'descuento' => $valorDescuento,
                ]);
            } else {
                Log::warning('No se pudo actualizar el descuento en itemsdb para no_s:', ['no_s' => $no_s]);
            }
        }
        $this->desactivarProductosSinImagen();
        DB::commit();
        $this->logApiImport('success', 'Descuentos importados y actualizados exitosamente en itemsdb.');
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
            'updated_at' => now(),
        ]);
    }
    
protected function desactivarProductosInvalidos()
{
    // Seleccionar productos que no cumplen los requisitos
    $productosInvalidos = DB::table('itemsdb')
        ->whereNull('nombre') // Nombre vacío
        ->orWhereNull('costo_unitario') // Costo unitario vacío
        ->orWhereNull('precio_unitario') // Precio unitario vacío
        ->orWhereNull('precio_unitario_IVAinc') // Precio con IVA vacío
        ->select('no_s') // Seleccionar identificadores
        ->get();

    if ($productosInvalidos->isEmpty()) {
        Log::info('No se encontraron productos inválidos para desactivar.');
        return;
    }

    // Actualizar los productos inválidos para marcarlos como inactivos
    DB::table('itemsdb')
        ->whereIn('no_s', $productosInvalidos->pluck('no_s'))
        ->update(['activo' => 0]);

    // Log de productos desactivados
    Log::info('Productos desactivados por datos incompletos:', [
        'productos' => $productosInvalidos->pluck('no_s'),
    ]);
}



protected function desactivarProductosSinImagen()
{
    // Obtener todos los productos activos
    $productosActivos = DB::table('itemsdb')
        ->select('no_s')
        ->where('activo', 1)
        ->get();

    $productosSinImagen = [];

    // Verificar si cada producto tiene una imagen en la carpeta
    foreach ($productosActivos as $producto) {
        $no_s = $producto->no_s;
        $rutaImagen = "itemsview/{$no_s}/{$no_s}.jpg";

        if (!Storage::disk('public')->exists($rutaImagen)) {
            $productosSinImagen[] = $no_s;
        }
    }

    // Si no se encontraron productos sin imágenes, registrar y salir
    if (empty($productosSinImagen)) {
        Log::info('No se encontraron productos sin imágenes para desactivar.');
        return;
    }

    // Desactivar productos sin imágenes
    DB::table('itemsdb')
        ->whereIn('no_s', $productosSinImagen)
        ->update(['activo' => 0]);

    Log::info('Productos desactivados por falta de imágenes:', [
        'productos' => $productosSinImagen,
    ]);
}
protected function desactivarProductosSinCubage()
{
    // 1. Identificar productos que sí están en items_unidades con cubage = 0 o NULL
    $productosCubageCero = DB::table('items_unidades')
        ->whereNull('cubage')
        ->orWhere('cubage', '=', 0)
        ->pluck('item_no');

    // 2. Identificar productos que ni siquiera tienen fila en items_unidades
    //    (leftJoin con itemsdb, donde items_unidades.item_no es null)
    $productosSinFila = DB::table('itemsdb')
        ->leftJoin('items_unidades', 'itemsdb.no_s', '=', 'items_unidades.item_no')
        ->whereNull('items_unidades.item_no') 
        ->pluck('itemsdb.no_s');

    // Combinas ambas colecciones
    $productosInvalidos = $productosCubageCero->merge($productosSinFila)->unique();

    if ($productosInvalidos->isEmpty()) {
        Log::info('No se encontraron productos sin cubage o sin fila para desactivar.');
        return;
    }

    // 3. Desactivar en itemsdb
    DB::table('itemsdb')
        ->whereIn('no_s', $productosInvalidos)
        ->update(['activo' => 0]);

    Log::info('Productos desactivados por no tener cubage o no tener fila en items_unidades:', [
        'productos' => $productosInvalidos,
    ]);
}



}
