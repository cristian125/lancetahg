<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductosDestacadosController;
use App\Http\Controllers\ShippingLocalController;
use App\Http\Controllers\ShippingPaqueteriaController;
use App\Http\Controllers\ShippingCobrarController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Importa Str aquí

class ProductController extends Controller
{





    public function show($id, $slug = null, Request $request)
    {
        // Verificar si el sitio está en mantenimiento
        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }

        // Obtener el producto por ID desde `itemsdb`
        $producto = DB::table('itemsdb')->where('id', $id)->where('activo', 1)->first();

        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }

        // Llamar al método `removeShipping` cuando se visualice el producto
        $this->removeShipping($request);

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

        $cantidadDisponible -= $cantidadEnCarrito;

        // Configuración para la imagen principal del producto
        $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
        $imagenPrincipal = asset("storage/itemsview/default.jpg");

        // Inicializar el array de imágenes secundarias
        $imagenesSecundarias = [];
        $carpetaSecundarias = "itemsview/{$codigoProducto}";
        if (Storage::disk('public')->exists($carpetaSecundarias)) {
            $imagenesEnCarpeta = Storage::disk('public')->files($carpetaSecundarias);
            foreach ($imagenesEnCarpeta as $imagen) {
                $nombreImagen = basename($imagen);
                if ($nombreImagen === "{$codigoProducto}.jpg") {
                    $imagenPrincipal = asset("storage/{$imagen}");
                } elseif (preg_match('/\.(jpg|jpeg|png|gif)$/i', $nombreImagen)) {
                    $imagenesSecundarias[] = asset("storage/{$imagen}");
                }
            }
        }

        $imagenesMiniaturas = collect([$imagenPrincipal])->merge($imagenesSecundarias);

        // Generar mensaje y clases CSS para stock
        $mensajeStock = $cantidadDisponible > 0 ? "{$cantidadDisponible} en stock" : "No hay stock disponible";
        $claseStock = $cantidadDisponible > 0 ? 'text-success' : 'text-danger';
        $botonDeshabilitado = $cantidadDisponible > 0 ? '' : 'disabled';

        // Obtener la división, categoría y grupo minorista
        $division = DB::table('divisiones')->where('codigo_division', $producto->cod_division)->first();
        $division = $division ? $division->descripcion : 'N/A';

        $categoria = DB::table('categorias_divisiones')->where('cod_categoria_producto', $producto->cod_categoria_producto)->first();
        $categoria = $categoria ? $categoria->descripcion : 'N/A';

        $grupoMinorista = DB::table('grupos_minorista')->where('codigo_de_producto_minorista', $producto->codigo_de_producto_minorista)->first();
        $codigoMinorista = $grupoMinorista ? $grupoMinorista->numeros_serie : 'N/A';

        // Obtener productos relacionados que están en el combobox (sin importar el atributo como color o tamaño)
        $productosRelacionados = DB::table('itemsdb')
            ->where('codigo_de_producto_minorista', $producto->codigo_de_producto_minorista)
            ->where('id', '!=', $producto->id)
            ->where('activo', 1)
            ->take(15)
            ->get();

        foreach ($productosRelacionados as $productoRelacionado) {
            $codigoProductoRelacionado = str_pad($productoRelacionado->no_s, 6, "0", STR_PAD_LEFT);
            $rutaImagenRecomendada = "itemsview/{$codigoProductoRelacionado}/{$codigoProductoRelacionado}.jpg";
            if (Storage::disk('public')->exists($rutaImagenRecomendada)) {
                $productoRelacionado->imagen = asset("storage/{$rutaImagenRecomendada}");
            } else {
                $productoRelacionado->imagen = asset("storage/itemsview/default.jpg");
            }
        }

        // Obtener los atributos del producto actual
        $atributosProducto = DB::table('producto_atributo')
            ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
            ->join('grupos', 'atributos.grupo_id', '=', 'grupos.id')
            ->where('producto_atributo.producto_id', $producto->id)
            ->select(
                'atributos.id as atributo_id',
                'atributos.nombre as atributo_nombre',
                'grupos.descripcion as grupo_descripcion',
                'grupos.id as grupo_id'
            )
            ->get();

        // Si el producto actual no tiene descripción, buscar en cualquier producto del combobox
        if (empty($producto->descripcion)) {
            $productosRelacionadosAtributos = DB::table('producto_atributo')
                ->join('itemsdb', 'producto_atributo.producto_id', '=', 'itemsdb.id')
                ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
                ->where('producto_atributo.producto_id', '!=', $producto->id)  // Excluir el producto actual
                ->whereIn('itemsdb.id', $productosRelacionados->pluck('id')) // Filtrar por productos en el combobox
                ->whereNotNull('itemsdb.descripcion')
                ->select('itemsdb.descripcion', 'itemsdb.id')
                ->first();

            // Si existe un producto relacionado con descripción, actualizar el producto actual
            if ($productosRelacionadosAtributos) {
                $producto->descripcion = $productosRelacionadosAtributos->descripcion;
                DB::table('itemsdb')->where('id', $producto->id)->update(['descripcion' => $producto->descripcion]);
            } else {
                // Si no hay descripción disponible, agregar un mensaje genérico
                $producto->descripcion = 'Descripción no disponible';
            }
        }

        // Obtener otros productos que comparten los mismos grupos de atributos
        $groupedProducts = [];
        foreach ($atributosProducto as $atributo) {
            $productosRelacionadosAtributos = DB::table('producto_atributo')
                ->join('itemsdb', 'producto_atributo.producto_id', '=', 'itemsdb.id')
                ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
                ->where('atributos.grupo_id', $atributo->grupo_id)
                ->where('itemsdb.id', '!=', $producto->id)
                ->select('itemsdb.id', 'itemsdb.descripcion', 'atributos.nombre as atributo_nombre', 'atributos.grupo_id')
                ->distinct()
                ->get();

            $groupedProducts[$atributo->grupo_descripcion] = $productosRelacionadosAtributos;
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
            'productosRelacionados',
            'id',
            'atributosProducto',
            'groupedProducts'
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
            ->select('id', 'nombre', 'descripcion', 'precio_unitario_IVAinc', 'no_s')
            ->where('cod_division', $division)
            ->where('cod_categoria_producto', $grupo)
            ->where('codigo_de_producto_minorista', $categoria)
            ->where('activo', 1) // Mostrar solo productos activos
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
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
            ->where('carts.user_id', $userId)
            ->where('itemsdb.activo', 1) // Mostrar solo productos activos
            ->select(
                'cart_items.no_s',
                'cart_items.description',
                'cart_items.unit_price as price',
                'cart_items.discount',
                'cart_items.final_price',
                'cart_items.quantity',
                'itemsdb.id'
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
        $userId = auth()->id();
        $no_s = $request->input('no_s');

        // Obtener el carrito del usuario
        $cart = DB::table('carts')->where('user_id', $userId)->first();
        if (!$cart) {
            return response()->json(['error' => 'No se encontró un carrito para el usuario.'], 404);
        }

        // Eliminar el producto del carrito
        DB::table('cart_items')
            ->where('cart_id', $cart->id)
            ->where('no_s', $no_s)
            ->delete();

        // Eliminar el método de envío **siempre** que se elimine un ítem del carrito
        DB::table('cart_shippment')->where('cart_id', $cart->id)->delete();

        return response()->json(['message' => 'Producto eliminado y método de envío eliminado.']);
    }

    public function addToCart(Request $request)
    {
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

        // Obtener el producto de la base de datos
        $producto = DB::table('itemsdb')->where('id', $request->id)->first();
        $inventario = DB::table('inventario')->where('no_s', $producto->no_s)->first();

        // Verificar si el producto existe y si hay stock
        if (!$producto || !$inventario || $inventario->cantidad_disponible <= 0) {
            return response()->json(['error' => 'Producto no disponible o stock agotado'], 400);
        }

        // Verificar si el producto ya está en el carrito
        $cartItem = DB::table('cart_items')
            ->where('cart_id', $cartId)
            ->where('no_s', $producto->no_s)
            ->first();

        // Calcular el descuento
        $descuento = $producto->descuento ?? 0; // Asegurarse de que se utiliza un valor predeterminado si no hay descuento
        $precioConDescuento = $producto->precio_unitario_IVAinc - ($producto->precio_unitario_IVAinc * ($descuento / 100));

        if ($cartItem) {
            // Incrementar la cantidad si ya está en el carrito
            if ($cartItem->quantity + 1 > $inventario->cantidad_disponible) {
                return response()->json(['error' => 'Inventario insuficiente'], 400);
            }
            DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $producto->no_s)
                ->increment('quantity');
        } else {
            // Insertar el nuevo producto en el carrito
            DB::table('cart_items')->insert([
                'cart_id' => $cartId,
                'no_s' => $producto->no_s,
                'description' => $producto->nombre,
                'unit_price' => $producto->precio_unitario_IVAinc,
                'final_price' => $precioConDescuento, // Aplicar el precio con descuento
                'discount' => $descuento, // Insertar el descuento en la columna 'discount'
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Eliminar el método de envío **siempre** que se añade un producto al carrito
        DB::table('cart_shippment')->where('cart_id', $cartId)->delete();

        return response()->json(['message' => 'Producto añadido al carrito con descuento y método de envío eliminado.']);
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
                    'description' => $producto->nombre,
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

        // Obtener el carrito del usuario
        $cartId = DB::table('carts')->where('user_id', $userId)->value('id');

        // Si la cantidad es 0, eliminar el ítem
        if ($newQuantity <= 0) {
            DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $productCode)
                ->delete();
        } else {
            // Actualizar la cantidad si es mayor que 0
            DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $productCode)
                ->update(['quantity' => $newQuantity]);
        }

        // Eliminar el método de envío **siempre** que se actualice la cantidad
        DB::table('cart_shippment')->where('cart_id', $cartId)->delete();

        return redirect()->back()->with('success', 'Cantidad actualizada y método de envío eliminado.');
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
                'nombre',
                'descripcion',
                'precio_unitario_IVAinc',
                'precio_con_descuento',
                'descuento',
                'no_s',
                DB::raw('CASE WHEN descuento > 0 THEN precio_con_descuento ELSE precio_unitario_IVAinc END AS precio_final')
            )
            ->where('activo', 1); // Mostrar solo productos activos

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
            $criteriosBusqueda[] = $divisionDesc;
            if ($grupoDesc) {
                $criterioBusqueda .= " / " . strtoupper($grupoDesc);
                array_push($criteriosBusqueda, $grupoDesc);
            }

            if ($categoriaDesc) {
                $criterioBusqueda .= " / " . strtoupper($categoriaDesc);
                array_push($criteriosBusqueda, $categoriaDesc);
            }
        } else {
            $criteriosBusqueda = [];
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
            $query->orderBy('nombre', $sortName == 'asc' ? 'asc' : 'desc');
        }

        // Si no se especifica ningún ordenamiento, aplicar un orden por defecto
        if (!$sortOffer && !$sortPrice && !$sortName) {
            $query->orderBy('nombre', 'asc');
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
            ->select('id', 'nombre', 'descripcion', 'precio_unitario_IVAinc', 'precio_con_descuento', 'descuento', 'no_s', 'unidad_medida_venta'); // Usar 'nombre' en lugar de 'descripcion' para el nombre real

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%') // Usar 'nombre' en lugar de 'descripcion' para la búsqueda
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

            // Verificar si el producto tiene descuento y calcular el precio final
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
        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }
    
        $userId = auth()->id();
    
        if (!$userId) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $userData = DB::table('users_data')->where('user_id', $userId)->first();
    
        // Verificar si el usuario tiene direcciones
        $direcciones = DB::table('users_address')->where('user_id', $userId)->get();
        $tieneDirecciones = $direcciones->isNotEmpty(); // Verifica si tiene al menos una dirección
    
        // Combinar el nombre y apellidos para el contacto
        $contactName = $user->name;
        if ($userData) {
            $contactName = $userData->nombre . ' ' . $userData->apellido_paterno . ' ' . $userData->apellido_materno;
        }
    
        // Obtener el ID del carrito del usuario
        $cartId = DB::table('carts')
            ->where('user_id', $userId)
            ->value('id');
    
        // Si no existe un carrito, redirigir al inicio
        if (!$cartId) {
            return redirect()->route('home');
        }
    
        // Consultar los elementos del carrito junto con las restricciones de envío y la cantidad disponible
        $cartItems = DB::table('cart_items')
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
            ->join('inventario', 'cart_items.no_s', '=', 'inventario.no_s')
            ->where('cart_items.cart_id', $cartId)
            ->where('itemsdb.activo', 1) // Mostrar solo productos activos
            ->select(
                'itemsdb.id',
                'itemsdb.unidad_medida_venta as unidad',
                'itemsdb.nombre as product_name',
                'itemsdb.descripcion as description',
                'cart_items.quantity',
                'cart_items.unit_price',
                'cart_items.discount',
                'cart_items.final_price',
                'itemsdb.no_s as product_code',
                'inventario.cantidad_disponible as available_quantity',
                'itemsdb.allow_local_shipping',
                'itemsdb.allow_paqueteria_shipping',
                'itemsdb.allow_store_pickup',
                'itemsdb.allow_cobrar_shipping', // Nueva columna
                'itemsdb.grupo_iva'
            )
            ->get();
    
        // Asignar las rutas de las imágenes de los productos
        foreach ($cartItems as $item) {
            $codigoProducto = str_pad($item->product_code, 6, "0", STR_PAD_LEFT);
            $imagePath = "storage/itemsview/{$codigoProducto}/{$codigoProducto}.jpg";
    
            if (file_exists(public_path($imagePath))) {
                $item->image = $imagePath;
            } else {
                $item->image = 'storage/itemsview/default.jpg';
            }
        }
    
        // Obtener detalles del envío si existen
        $shippment = DB::table('cart_shippment')
            ->leftJoin('tiendas', 'cart_shippment.store_id', '=', 'tiendas.id')
            ->where('cart_id', $cartId)
            ->select('cart_shippment.*', 'tiendas.nombre as store_name', 'tiendas.direccion as store_address')
            ->first();
    
        // Verificar si existe un método de envío
        $shippmentExists = $shippment !== null;
    
        // Obtener el tipo de envío seleccionado desde la tabla cart_shippment
        $tipoEnvioSeleccionado = $shippment ? $shippment->ShipmentMethod : null;
    
        if ($tipoEnvioSeleccionado) {
            $eligibleCartItems = $cartItems->filter(function ($item) use ($tipoEnvioSeleccionado) {
                if ($tipoEnvioSeleccionado === 'EnvioLocal') {
                    return $item->allow_local_shipping;
                } elseif ($tipoEnvioSeleccionado === 'EnvioPorPaqueteria') {
                    return $item->allow_paqueteria_shipping;
                } elseif ($tipoEnvioSeleccionado === 'RecogerEnTienda') {
                    return $item->allow_store_pickup;
                } elseif ($tipoEnvioSeleccionado === 'EnvioPorCobrar') {
                    return $item->allow_cobrar_shipping == 1;
                }
                
                return true;
            });
            // ...
        
        
    
            // Obtener los códigos de producto de los artículos elegibles
            $eligibleProductCodes = $eligibleCartItems->pluck('product_code')->all();
    
            // Filtrar los productos no elegibles
            $nonEligibleItems = $cartItems->reject(function ($item) use ($eligibleProductCodes) {
                return in_array($item->product_code, $eligibleProductCodes);
            });
        } else {
            // No hay método de envío seleccionado, incluir todos los productos
            $eligibleCartItems = $cartItems;
            $nonEligibleItems = collect(); // Colección vacía
        }
    
        // Calcular el precio total del carrito incluyendo IVA (descuento ya aplicado en final_price)
        $totalPrice = $eligibleCartItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });
    
        // Calcular el subtotal sin IVA
        $subtotalSinIVA = $totalPrice / 1.16;
    
        // Calcular el total del descuento aplicado
        $totalDescuento = $eligibleCartItems->sum(function ($item) {
            return $item->unit_price * $item->quantity * ($item->discount / 100);
        });
    
        // Calcular el IVA sobre el subtotal sin IVA
        $iva = $subtotalSinIVA * 0.16;
    
        // Obtener el costo de envío con IVA
        $shippingCostIVA = $shippment->shippingcost_IVA ?? 0.00;
    
        // Ajustar el costo de envío si el método es 'EnvioPorCobrar'
        if ($shippmentExists && $shippment->ShipmentMethod === 'EnvioPorCobrar') {
            $shippingCostIVA = 0.00;
        }
    
        // Calcular el total final incluyendo IVA y el envío
        $totalFinal = $subtotalSinIVA + $iva + $shippingCostIVA;
    
        // Obtener los métodos de envío activos desde la base de datos
        $activeShippingMethods = DB::table('shipping_methods')
            ->where('is_active', 1)
            ->get();
    
        // Definir los tipos de envío disponibles según las restricciones y los métodos activos
        $envios = [];
    
        foreach ($activeShippingMethods as $method) {
            $price = 0.00; // Precio por defecto
    
            // Asignar el precio y otros detalles según el método de envío
            if ($method->name === 'EnvioLocal') {
                $price = 250.00;
            } elseif ($method->name === 'EnvioPorPaqueteria') {
                $price = 500.00;
            } elseif ($method->name === 'RecogerEnTienda') {
                $price = 0.00;
            } elseif ($method->name === 'EnvioPorCobrar') {
                $price = 0.00; // El cliente paga al recibir
            }
    
            $envios[] = [
                'name' => $method->display_name,
                'value' => $method->name,
                'price' => $price,
            ];
        }
    
        // Si no hay métodos de envío disponibles, mostrar un mensaje de error
        $noShippingMethodsAvailable = empty($envios);
    
        // Obtener datos para cada método de envío
        $shippingLocalController = new ShippingLocalController();
        $localShippingData = $shippingLocalController->handleLocalShipping($request, $userId, $totalPrice);
    
        $shippingPaqueteriaController = new ShippingPaqueteriaController();
        $paqueteriaShippingData = $shippingPaqueteriaController->handlePaqueteriaShipping($request, $userId, $totalPrice);
    
        $storePickupController = new StorePickupController();
        $storePickupData = $storePickupController->handleStorePickup($request, $userId);
    
        // Instanciar el controlador de Envío por Cobrar
        $shippingCobrarController = new ShippingCobrarController();
        $cobrarShippingData = $shippingCobrarController->handleCobrarShipping($request, $userId, $totalPrice);
    
    // Filtrar productos no elegibles para ciertos tipos de envío (para mostrar alertas al usuario)
    $nonEligibleLocalShipping = $cartItems->filter(function ($item) {
        return empty($item->allow_local_shipping);
    });
    $nonEligiblePaqueteriaShipping = $cartItems->filter(function ($item) {
        return empty($item->allow_paqueteria_shipping);
    });
    $nonEligibleStorePickup = $cartItems->filter(function ($item) {
        return empty($item->allow_store_pickup);
    });
    $nonEligibleCobrarShipping = $cartItems->filter(function ($item) {
        return empty($item->allow_cobrar_shipping);
    });

        
    
        // Pasar todos los datos necesarios a la vista
        return view('carrito', [
            'cartItems' => $cartItems, // Todos los productos en el carrito
            'eligibleCartItems' => $eligibleCartItems, // Productos elegibles para el método de envío seleccionado
            'nonEligibleItems' => $nonEligibleItems, // Productos no elegibles
            'totalPrice' => $totalPrice,
            'subtotalSinIVA' => $subtotalSinIVA,
            'totalDescuento' => $totalDescuento,
            'iva' => $iva,
            'totalFinal' => $totalFinal,
            'shippingCostIVA' => $shippingCostIVA,
            'envios' => $envios,
            'tipoEnvioSeleccionado' => $tipoEnvioSeleccionado,
            'localShippingData' => $localShippingData,
            'paqueteriaShippingData' => $paqueteriaShippingData,
            'storePickupData' => $storePickupData,
            'cobrarShippingData' => $cobrarShippingData,
            'cartId' => $cartId,
            'shippment' => $shippment, // Pasamos el objeto $shippment
            'shippmentExists' => $shippmentExists,
            'nonEligibleLocalShipping' => $nonEligibleLocalShipping,
            'nonEligiblePaqueteriaShipping' => $nonEligiblePaqueteriaShipping,
            'nonEligibleStorePickup' => $nonEligibleStorePickup,
            'nonEligibleCobrarShipping' => $nonEligibleCobrarShipping,
            'noShippingMethodsAvailable' => $noShippingMethodsAvailable,
            'contactName' => $contactName, // Pasar el nombre de contacto
            'tieneDirecciones' => $tieneDirecciones,
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
        $userId = auth()->id();
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

        $availableQuantity = DB::table('inventario')
            ->where('no_s', $productCode)
            ->value('cantidad_disponible');

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantityChange;

            if ($quantityChange < 0) {
                $newQuantity = $cartItem->quantity - abs($quantityChange);
            }

            if ($newQuantity > $availableQuantity) {
                $newQuantity = $availableQuantity;
            } elseif ($newQuantity < 1) {
                $newQuantity = 1;
            }

            DB::table('cart_items')
                ->where('id', $cartItem->id)
                ->update(['quantity' => $newQuantity]);
        }

        // Verificar si el carrito está vacío después de actualizar la cantidad
        $remainingItems = DB::table('cart_items')
            ->where('cart_id', function ($query) use ($userId) {
                $query->select('id')
                    ->from('carts')
                    ->where('user_id', $userId)
                    ->limit(1);
            })
            ->count();

        if ($remainingItems === 0) {
            // Eliminar el método de envío si no hay productos
            DB::table('cart_shippment')
                ->where('cart_id', function ($query) use ($userId) {
                    $query->select('id')
                        ->from('carts')
                        ->where('user_id', $userId)
                        ->limit(1);
                })
                ->delete();
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
