<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ShippingLocalController;
use App\Http\Controllers\ShippingPaqueteriaController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function show($id)
    {
        // Obtener el producto por ID desde `itemsdb`
        $producto = DB::table('itemsdb')->where('id', $id)->first();

        // Verificar si se encontró el producto
        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }

        // Obtener la cantidad disponible del inventario
        $inventario = DB::table('inventario')->where('no_s', $producto->no_s)->first();
        $cantidadDisponible = $inventario ? $inventario->cantidad_disponible : 0;

        // Obtener la cantidad ya en el carrito del usuario
        $userId = auth()->id();
        $cantidadEnCarrito = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('carts.user_id', $userId)
            ->where('cart_items.no_s', $producto->no_s)
            ->sum('cart_items.quantity');

        // Calcular la cantidad realmente disponible para añadir al carrito
        $cantidadDisponible -= $cantidadEnCarrito;

        // Configuración para la imagen principal del producto detallado
        $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
        $imagenPrincipal = asset("storage/itemsview/default.jpg"); // Imagen por defecto

        // Inicializar el array de imágenes secundarias
        $imagenesSecundarias = [];

        // Verificar si existe una carpeta con imágenes
        $carpetaSecundarias = "itemsview/{$codigoProducto}";

        if (Storage::disk('public')->exists($carpetaSecundarias)) {
            // Obtener todas las imágenes dentro de la carpeta
            $imagenesEnCarpeta = Storage::disk('public')->files($carpetaSecundarias);

            // Procesar las imágenes encontradas
            foreach ($imagenesEnCarpeta as $imagen) {
                $nombreImagen = basename($imagen);

                if ($nombreImagen === "{$codigoProducto}.jpg") {
                    $imagenPrincipal = asset("storage/{$imagen}"); // Sobrescribir la imagen principal si se encuentra una específica
                } else if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $nombreImagen)) {
                    $imagenesSecundarias[] = asset("storage/{$imagen}");
                }
            }
        }

        // Recolectar imágenes miniaturas
        $imagenesMiniaturas = collect([$imagenPrincipal])->merge($imagenesSecundarias);

        // Generar mensaje y clases CSS para stock
        $mensajeStock = $cantidadDisponible > 0
        ? "{$cantidadDisponible} en stock"
        : "No hay stock disponible";
        $claseStock = $cantidadDisponible > 0 ? 'text-success' : 'text-danger';
        $botonDeshabilitado = $cantidadDisponible > 0 ? '' : 'disabled';

        // Obtener la división del producto
        $division = DB::table('divisiones')->where('codigo_division', $producto->cod_division)->first();

        // Obtener la categoría del producto
        $categoria = DB::table('categorias_divisiones')->where('cod_categoria_producto', $producto->cod_categoria_producto)->first();

        // Obtener el código minorista del producto
        $grupoMinorista = DB::table('grupos_minorista')->where('codigo_de_producto_minorista', $producto->codigo_de_producto_minorista)->first();

        // Obtener el numeros_serie si existe
        $numerosSerie = $grupoMinorista ? $grupoMinorista->numeros_serie : 'N/A';

        // Asegúrate de que las variables `division`, `categoria` y `codigoMinorista` estén definidas correctamente
        $division = $division ? $division->descripcion : 'N/A';
        $categoria = $categoria ? $categoria->descripcion : 'N/A';
        $codigoMinorista = $numerosSerie;

        // Obtener los productos específicos para el carrusel
        $productosRecomendados = DB::table('itemsdb')
            ->whereIn('no_s', ['001005', '016001', '016018', '016135', '019004', '022187', '022283', '031046', '031005', '045095'])
            ->get();

        // Asignar la imagen a cada producto en el carrusel
        foreach ($productosRecomendados as $productoRecomendado) {
            $codigoProductoRecomendado = str_pad($productoRecomendado->no_s, 6, "0", STR_PAD_LEFT);
            $rutaImagenRecomendada = "itemsview/{$codigoProductoRecomendado}/{$codigoProductoRecomendado}.jpg";

            if (Storage::disk('public')->exists($rutaImagenRecomendada)) {
                $productoRecomendado->imagen = asset("storage/{$rutaImagenRecomendada}");
            } else {
                $productoRecomendado->imagen = asset("storage/itemsview/default.jpg");
            }
        }

        // Retornar la vista con los datos calculados
        return view('index2', compact(
            'producto',
            'imagenPrincipal',
            'imagenesMiniaturas',
            'cantidadDisponible',
            'mensajeStock',
            'claseStock',
            'botonDeshabilitado',
            'division',
            'categoria',
            'codigoMinorista',
            'productosRecomendados', // Pasar los productos seleccionados a la vista
            'id'
        ));
    }

    public function getDivisiones()
    {
        // Obtener las divisiones desde la base de datos
        $divisiones = DB::table('divisiones')->select('id', 'descripcion')->get();

        // Retornar los datos como un JSON
        return response()->json($divisiones);
    }

    public function getCategorias(Request $request, $cat)
    {
        // Obtener las divisiones desde la base de datos
        $categorias = DB::table('categorias')->select('id', 'descripcion')->get();

        // Retornar los datos como un JSON
        return response()->json($categorias);
    }

    public function showGrupo($division, $grupo, $categoria)
    {
        $productos = DB::table('itemsdb')
            ->select('id', 'descripcion_alias', 'descripcion', 'precio_unitario_IVAinc', 'no_s')
            ->where('cod_division', $division)
            ->where('cod_categoria_producto', $grupo)
            ->where('codigo_de_producto_minorista', $categoria) // Ahora estás usando el código correcto
            ->get();

        // Aquí no usas `numeros_serie` para nada, solo el código minorista

        return view('search-result-view', compact('productos'));
    }

    public function getCartItems()
    {
        $userId = auth()->id(); // Obtener el ID del usuario autenticado

        // Obtener los items del carrito del usuario
        $cartItems = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s') // Asegura que haces join con la tabla de productos
            ->where('carts.user_id', $userId)
            ->select(
                'cart_items.no_s',
                'cart_items.description',
                'cart_items.unit_price as price',
                'cart_items.discount',
                'cart_items.final_price',
                'cart_items.quantity',
                'itemsdb.id' // Incluye el ID del producto en la selección
            )
            ->get();

        return response()->json(['items' => $cartItems]);
    }

    public function getCategoriasConSubcategorias()
    {
        $divisiones = DB::table('divisiones')->get();
        $categoriasDivisiones = DB::table('categorias_divisiones')->get();
        $gruposMinorista = DB::table('grupos_minorista')->get();

        $resultado = [];

        foreach ($divisiones as $division) {
            $resultado[$division->codigo_division] = [
                'nombre' => $division->descripcion,
                'subcategorias' => [],
            ];

            $categorias = $categoriasDivisiones->where('cod_division', $division->codigo_division);

            foreach ($categorias as $categoria) {
                $resultado[$division->codigo_division]['subcategorias'][$categoria->cod_categoria_producto] = [
                    'nombre' => $categoria->descripcion,
                    'subsubcategorias' => [],
                ];

                $subcategorias = $gruposMinorista->where('cod_categoria_producto', $categoria->cod_categoria_producto);

                foreach ($subcategorias as $subcategoria) {
                    $resultado[$division->codigo_division]['subcategorias'][$categoria->cod_categoria_producto]['subsubcategorias'][] = [
                        'texto' => $subcategoria->numeros_serie,
                        'codigo' => $subcategoria->codigo_de_producto_minorista,
                    ];
                }
            }
        }

        return response()->json($resultado);
    }

    public function removeFromCart(Request $request)
    {
        $userId = auth()->id(); // Obtener el ID del usuario autenticado
        $no_s = $request->input('no_s'); // Obtener el número de serie del producto

        // Obtener el carrito del usuario
        $cart = DB::table('carts')->where('user_id', $userId)->first();

        if (!$cart) {
            return response()->json(['message' => 'No se encontró un carrito para el usuario.'], 404);
        }

        // Encuentra el ítem del carrito correspondiente al número de serie del producto
        $cartItem = DB::table('cart_items')
            ->where('cart_id', $cart->id)
            ->where('no_s', $no_s)
            ->first();

        if ($cartItem) {
            DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->where('no_s', $no_s)
                ->delete();

            /*
        // Si deseas disminuir la cantidad en lugar de eliminar el ítem
        if ($cartItem->quantity > 1) {
        DB::table('cart_items')
        ->where('cart_id', $cart->id)
        ->where('no_s', $no_s)
        ->decrement('quantity');
        } else {
        DB::table('cart_items')
        ->where('cart_id', $cart->id)
        ->where('no_s', $no_s)
        ->delete();
        }
         */
        } else {
            return response()->json(['message' => 'Ítem no encontrado en el carrito.'], 404);
        }

        return response()->json(['message' => 'Ítem eliminado del carrito']);
    }
    public function addToCart(Request $request)
    {
        // Si la solicitud es parte de una búsqueda, no realizar ninguna acción
        if ($request->has('is_search') && $request->input('is_search') == true) {
            return response()->json(['message' => 'Búsqueda realizada, no se añadió al carrito'], 200);
        }

        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $userId = auth()->id();

        // Obtener el carrito del usuario, o crear uno nuevo si no existe
        $cart = DB::table('carts')->where('user_id', $userId)->first();

        if (!$cart) {
            $cartId = DB::table('carts')->insertGetId([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $cartId = $cart->id;
        }

        // Obtener el producto de la base de datos para obtener los detalles
        $producto = DB::table('itemsdb')->where('id', $request->id)->first();
        $inventario = DB::table('inventario')->where('no_s', $producto->no_s)->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        // Verificar la cantidad disponible en el inventario
        if ($inventario->cantidad_disponible <= 0) {
            return response()->json(['error' => 'Inventario agotado'], 400);
        }

        // Verificar si el producto ya está en el carrito
        $cartItem = DB::table('cart_items')
            ->where('cart_id', $cartId)
            ->where('no_s', $producto->no_s)
            ->first();

        if ($cartItem) {
            // Verificar si se supera la cantidad disponible en el inventario
            if ($cartItem->quantity + 1 > $inventario->cantidad_disponible) {
                return response()->json(['error' => 'Inventario insuficiente'], 400);
            }

            // Incrementar la cantidad si ya existe en el carrito
            DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $producto->no_s)
                ->increment('quantity');
        } else {
            // Insertar un nuevo item en el carrito
            DB::table('cart_items')->insert([
                'cart_id' => $cartId,
                'no_s' => $producto->no_s,
                'description' => $producto->descripcion_alias,
                'unit_price' => $producto->precio_unitario_IVAinc,
                'discount' => $producto->descuento,
                'final_price' => $producto->descuento > 0 ? $producto->precio_con_descuento : $producto->precio_unitario_IVAinc,
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Producto añadido al carrito'], 200);
    }

    public function addMultipleToCart(Request $request)
    {
        try {
            $userId = auth()->id();
            $no_s = $request->input('no_s');
            $quantity = $request->input('quantity');

            // Verificar si el usuario tiene un carrito existente
            $cart = DB::table('carts')->where('user_id', $userId)->first();

            if (!$cart) {
                $cartId = DB::table('carts')->insertGetId([
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $cartId = $cart->id;
            }

            // Verificar si el producto ya está en el carrito
            $cartItem = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $no_s)
                ->first();

            // Obtener detalles del producto desde la tabla itemsdb
            $producto = DB::table('itemsdb')->where('no_s', $no_s)->first();
            if (!$producto) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            // Obtener la cantidad disponible en inventario
            $inventario = DB::table('inventario')->where('no_s', $producto->no_s)->first();
            $cantidadDisponible = $inventario ? $inventario->cantidad_disponible : 0;

            // Obtener la cantidad ya en el carrito
            $cantidadEnCarrito = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $no_s)
                ->sum('quantity');

            // Verificar si se puede añadir la cantidad deseada al carrito
            if (($cantidadEnCarrito + $quantity) > $cantidadDisponible) {
                return response()->json(['error' => 'No puedes añadir más de este producto. Stock insuficiente.'], 400);
            }

            if ($cartItem) {
                // Incrementar la cantidad del producto si ya está en el carrito
                DB::table('cart_items')
                    ->where('cart_id', $cartId)
                    ->where('no_s', $no_s)
                    ->increment('quantity', $quantity);
            } else {
                // Insertar un nuevo producto en el carrito
                DB::table('cart_items')->insert([
                    'cart_id' => $cartId,
                    'no_s' => $no_s,
                    'description' => $producto->descripcion_alias,
                    'unit_price' => $producto->precio_unitario_IVAinc,
                    'discount' => $producto->descuento,
                    'final_price' => $producto->descuento > 0 ? $producto->precio_con_descuento : $producto->precio_unitario_IVAinc,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Calcular la cantidad restante de stock
            $cantidadRestante = $cantidadDisponible - ($cantidadEnCarrito + $quantity);

            return response()->json([
                'message' => 'Producto añadido al carrito correctamente',
                'stock_restante' => $cantidadRestante,
            ], 200);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al añadir el producto al carrito'], 500);
        }
    }

    public function updateQuantity(Request $request)
    {
        $userId = auth()->id();
        $productCode = $request->input('product_code');
        $newQuantity = (int) $request->input('quantity');

        // Consulta para verificar la cantidad disponible en el inventario
        $availableQuantity = DB::table('inventario')->where('no_s', $productCode)->value('cantidad_disponible');

        if ($newQuantity > $availableQuantity) {
            return redirect()->back()->with('error', 'No puedes añadir más de lo que hay en el inventario.');
        }

        // Actualiza la cantidad en el carrito
        DB::table('cart_items')
            ->where('cart_id', function ($query) use ($userId) {
                $query->select('id')
                    ->from('carts')
                    ->where('user_id', $userId)
                    ->limit(1);
            })
            ->where('no_s', $productCode)
            ->update(['quantity' => $newQuantity]);

        return redirect()->back()->with('success', 'Cantidad actualizada exitosamente.');
    }

    public function search(Request $request, $division = null, $grupo = null, $categoria = null)
    {
        // Obtener el precio máximo de la base de datos
        $maxPriceInDatabase = DB::table('itemsdb')->max('precio_unitario_IVAinc');

        // Definir la expresión para el precio final
        $precioFinalExpression = DB::raw('CASE WHEN descuento > 0 THEN precio_con_descuento ELSE precio_unitario_IVAinc END');

        // Iniciar la consulta base para los productos
        $query = DB::table('itemsdb')
            ->select(
                'id',
                'descripcion_alias',
                'descripcion',
                'precio_unitario_IVAinc',
                'precio_con_descuento',
                'descuento',
                'no_s',
                DB::raw('CASE WHEN descuento > 0 THEN precio_con_descuento ELSE precio_unitario_IVAinc END AS precio_final')
            );

        // Aplicar filtro por término de búsqueda si está presente
        if ($request->has('search') && trim($request->input('search')) !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', '%' . $search . '%')
                    ->orWhere('no_s', 'like', '%' . $search . '%');
            });
        }

        // Aplicar filtros por división, grupo y categoría si se han proporcionado
        if ($division) {
            $query->where('cod_division', $division);

            if ($grupo) {
                $query->where('cod_categoria_producto', $grupo);
            }

            if ($categoria) {
                $query->where('codigo_de_producto_minorista', $categoria);
            }

            // Obtener descripciones detalladas para mostrar en la vista
            $divisionDesc = DB::table('divisiones')->where('codigo_division', $division)->value('descripcion');
            $grupoDesc = $grupo ? DB::table('categorias_divisiones')->where('cod_categoria_producto', $grupo)->value('descripcion') : null;
            $categoriaDesc = $categoria ? strtoupper(DB::table('grupos_minorista')->where('codigo_de_producto_minorista', $categoria)->value('numeros_serie')) : null;

            // Formar el criterio de búsqueda con descripciones detalladas
            $criterioBusqueda = strtoupper($divisionDesc);
            $criteriosBusqueda[]=$divisionDesc;
            if ($grupoDesc) {
                $criterioBusqueda .= " / " . strtoupper($grupoDesc);
                array_push($criteriosBusqueda,$grupoDesc);
            }

            if ($categoriaDesc) {
                $criterioBusqueda .= " / " . strtoupper($categoriaDesc);
                array_push($criteriosBusqueda,$categoriaDesc);
            }
        } else {
            $criteriosBusqueda=[];
            $criterioBusqueda = $request->input('search', "Término de búsqueda no especificado");
        }

        // Aplicar filtro de precio si está presente en la solicitud
        if ($request->has('min_price') || $request->has('max_price')) {
            $minPrice = $request->input('min_price', 0);
            $maxPrice = $request->input('max_price', $maxPriceInDatabase);
            $query->whereBetween($precioFinalExpression, [$minPrice, $maxPrice]);
        }

        // Aplicar ordenamiento según los parámetros 'sort_offer', 'sort_price' y 'sort_name'
        $sortOffer = $request->input('sort_offer');
        $sortPrice = $request->input('sort_price');
        $sortName = $request->input('sort_name');

        if ($sortOffer) {
            // Creamos una expresión para determinar si el producto está en oferta
            $onOfferExpression = DB::raw('CASE WHEN descuento > 0 THEN 1 ELSE 0 END');
            $query->orderBy($onOfferExpression, $sortOffer == 'asc' ? 'asc' : 'desc');
        }

        if ($sortPrice) {
            $query->orderBy($precioFinalExpression, $sortPrice == 'asc' ? 'asc' : 'desc');
        }

        if ($sortName) {
            $query->orderBy('descripcion_alias', $sortName == 'asc' ? 'asc' : 'desc');
        }

        // Si no se especifica ningún ordenamiento, aplicar un orden por defecto
        if (!$sortOffer && !$sortPrice && !$sortName) {
            $query->orderBy('descripcion_alias', 'asc');
        }

        // Ejecutar la consulta y aplicar paginación
        $productos = $query->paginate(16)->appends($request->all());

        // Procesar los productos para añadir las imágenes y utilizar 'precio_final'
        foreach ($productos as $producto) {
            $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
            $carpetaProducto = "itemsview/{$codigoProducto}";
            $imagePath = "storage/{$carpetaProducto}/{$codigoProducto}.jpg";
            $defaultImagePath = 'storage/itemsview/default.jpg';

            if (Storage::disk('public')->exists("{$carpetaProducto}/{$codigoProducto}.jpg")) {
                $producto->imagen_principal = asset($imagePath);
            } else {
                $producto->imagen_principal = asset($defaultImagePath);
            }

            // 'precio_final' ya viene en el objeto $producto desde la consulta
        }

        // Retornar la vista con los productos encontrados
        return view('search-result-view', [
            'productos' => $productos,
            'criterioBusqueda' => $criterioBusqueda,
            'criteriosBusqueda' => $criteriosBusqueda,
            'division' => $division,
            'grupo' => $grupo,
            'categoria' => $categoria,
            'maxPriceInDatabase' => $maxPriceInDatabase, // Pasar el valor máximo a la vista
        ]);
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->input('search');
        $query = DB::table('itemsdb')
            ->select('id', 'descripcion_alias', 'descripcion', 'precio_unitario_IVAinc', 'precio_con_descuento', 'descuento', 'no_s', 'unidad_medida_venta'); // Añadir 'unidad_medida_venta'

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', '%' . $search . '%')
                    ->orWhere('no_s', 'like', '%' . $search . '%');
            });
        }

        $productos = $query->take(10)->get(); // Limita a 10 resultados para la búsqueda en tiempo real

        foreach ($productos as $producto) {
            $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
            $carpetaProducto = "itemsview/{$codigoProducto}";
            $imagePath = "storage/{$carpetaProducto}/{$codigoProducto}.jpg";
            $defaultImagePath = 'storage/itemsview/default.jpg';

            if (Storage::disk('public')->exists("{$carpetaProducto}/{$codigoProducto}.jpg")) {
                $producto->imagen_principal = asset($imagePath);
            } else {
                $producto->imagen_principal = asset($defaultImagePath);
            }

            if (isset($producto->descuento) && $producto->descuento > 0) {
                $producto->precio_final = $producto->precio_con_descuento;
            } else {
                $producto->precio_final = $producto->precio_unitario_IVAinc;
            }
        }

        return response()->json($productos);
    }

    public function showCart(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            return redirect()->route('login');
        }

        // Obtener el ID del carrito del usuario
        $cartId = DB::table('carts')
            ->where('user_id', $userId)
            ->value('id');

        // Consulta los elementos del carrito junto con la cantidad disponible en el inventario
        $cartItems = DB::table('cart_items')
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
            ->join('inventario', 'cart_items.no_s', '=', 'inventario.no_s') // Unión con la tabla de inventario
            ->where('cart_items.cart_id', $cartId)
            ->select(
                'itemsdb.id',
                'itemsdb.unidad_medida_venta as unidad',
                'itemsdb.descripcion as description',
                'cart_items.quantity',
                'cart_items.unit_price',
                'cart_items.discount',
                'cart_items.final_price',
                'itemsdb.no_s as product_code',
                'inventario.cantidad_disponible as available_quantity' // Añadir la cantidad disponible
            )
            ->get();

        // Consulta los datos de envío (un solo registro)
        $shippment = DB::table('cart_shippment')
            ->where('cart_id', $cartId)
            ->first();

        // Lógica para asignar las rutas de las imágenes de los productos
        foreach ($cartItems as $item) {
            $codigoProducto = str_pad($item->product_code, 6, "0", STR_PAD_LEFT);
            $imagePath = "storage/itemsview/{$codigoProducto}/{$codigoProducto}.jpg";

            if (file_exists(public_path($imagePath))) {
                $item->image = $imagePath; // Asignar la ruta relativa de la imagen
            } else {
                $item->image = 'storage/itemsview/default.jpg'; // Ruta de la imagen por defecto
            }
        }

        // Calcular el precio total del carrito sin incluir el ítem de envío
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });

        // Llamar al controlador de Envío Local
        $shippingLocalController = new ShippingLocalController();
        $localShippingData = $shippingLocalController->handleLocalShipping($request, $userId, $totalPrice);

        // Llamar al controlador de Envío por Paquetería
        $shippingPaqueteriaController = new ShippingPaqueteriaController();
        $paqueteriaShippingData = $shippingPaqueteriaController->handlePaqueteriaShipping($request, $userId, $totalPrice);

        // Definir los tipos de envío disponibles, incluyendo siempre el envío local y por paquetería
        $envios = [
            ['name' => 'Recoger en Tienda', 'price' => 0.00],
            ['name' => 'Envío por Paquetería', 'price' => 500.00],
            ['name' => 'Envío Local', 'price' => $localShippingData['costoEnvio'] ?? 0.00],
        ];

        $tipoEnvioSeleccionado = $request->input('tipo_envio', $envios[0]['name']);

        // Obtener datos para la opción de Recoger en Tienda
        $storePickupController = new StorePickupController();
        $storePickupData = $storePickupController->handleStorePickup($request, $userId);

        // Pasar todos los datos necesarios a la vista
        return view('carrito', [
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
            'envios' => $envios,
            'tipoEnvioSeleccionado' => $tipoEnvioSeleccionado,
            'localShippingData' => $localShippingData,
            'paqueteriaShippingData' => $paqueteriaShippingData,
            'storePickupData' => $storePickupData,
            'cartId' => $cartId,
            'Shippment' => $shippment ? collect([$shippment]) : collect(),
            'shippmentExists' => $shippment !== null,
            'shippingCostIVA' => $shippment->shippingcost_IVA ?? null,
        ]);
    }

    public function removeShipping(Request $request)
    {
        $userId = auth()->id();

        // Obtener el ID del carrito del usuario
        $cartId = DB::table('carts')
            ->where('user_id', $userId)
            ->value('id');

        // Eliminar el registro de envío asociado al carrito
        DB::table('cart_shippment')
            ->where('cart_id', $cartId)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function reduceQuantity(Request $request)
    {
        $userId = auth()->id(); // Obtener el ID del usuario autenticado
        $productCode = $request->input('product_code');
        $quantityChange = (int) $request->input('quantity');

        // Consulta para encontrar el ítem en el carrito
        $cartItem = DB::table('cart_items')
            ->where('cart_items.cart_id', function ($query) use ($userId) {
                $query->select('id')
                    ->from('carts')
                    ->where('user_id', $userId)
                    ->limit(1);
            })
            ->where('no_s', $productCode)
            ->first();

        // Consulta la cantidad disponible en el inventario
        $availableQuantity = DB::table('inventario')
            ->where('no_s', $productCode)
            ->value('cantidad_disponible');

        if ($cartItem) {
            // Determinar si el cambio es un incremento o un decremento
            $newQuantity = $cartItem->quantity + $quantityChange; // Usamos suma para la lógica

            // Si es decremento, debe restar la cantidad
            if ($quantityChange < 0) {
                $newQuantity = $cartItem->quantity - abs($quantityChange);
            }

            // Verificar que la nueva cantidad no sea mayor que el inventario disponible ni menor que 1
            if ($newQuantity > $availableQuantity) {
                $newQuantity = $availableQuantity;
            } elseif ($newQuantity < 1) {
                $newQuantity = 1; // Asegura que no se pueda reducir a menos de 1
            }

            // Actualizar la cantidad en la base de datos
            DB::table('cart_items')
                ->where('id', $cartItem->id)
                ->update(['quantity' => $newQuantity]);
        }

        return redirect()->back()->with('success', 'Cantidad actualizada exitosamente.');
    }

    public function getImage(Request $request)
    {
        $id = $request->id;
        $imgurl = "storage/itemsview/{$id}/{$id}.jpg";
        $default = "storage/itemsview/default.jpg";
        if (storage::disk('public')->has("itemsview/{$id}/{$id}.jpg") == true) {
            return response()->file($imgurl, ['Content-Type' => 'image/jpeg']);
        } else {
            return response()->file($default, ['Content-Type' => 'image/jpeg']);
        }
    }
}
