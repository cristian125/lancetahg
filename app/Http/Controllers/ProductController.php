<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductosDestacadosController;
use App\Http\Controllers\ShippingCobrarController;
use App\Http\Controllers\ShippingLocalController;
use App\Http\Controllers\ShippingPaqueteriaController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CartController;

class ProductController extends Controller
{
    public function show($id, $slug = null, Request $request)
    {

        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }

        $producto = DB::table('itemsdb')->where('id', $id)->whereIn('activo', [1, 2])->first();

        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }

        $nombreProveedor = $producto->proveedor_nombre ?? ' ';

        $this->removeShipping($request);

        $inventario = DB::table('inventario')->where('no_s', $producto->no_s)->first();
        $cantidadDisponible = $inventario ? $inventario->cantidad_disponible : 0;

        $userId = auth()->id();
        $cantidadEnCarrito = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('carts.user_id', $userId)
            ->where('cart_items.no_s', $producto->no_s)
            ->sum('cart_items.quantity');

        $cantidadDisponible -= $cantidadEnCarrito;

        $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
        $imagenPrincipal = asset("storage/itemsview/default.jpg");

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
        $mensajeStock = $cantidadDisponible > 0
            ? "Disponibles: {$cantidadDisponible} en stock"
            : "No disponible para venta en línea. <br> Llame al 55-5578-1958 para preguntar por su disponibilidad.";
        $claseStock = $cantidadDisponible > 0 ? 'text-success' : 'text-danger';
        $botonDeshabilitado = $cantidadDisponible > 0 ? '' : 'disabled';

        $division = DB::table('divisiones')->where('codigo_division', $producto->cod_division)->first();
        $division = $division ? $division->descripcion : 'N/A';

        $categoria = DB::table('categorias_divisiones')->where('cod_categoria_producto', $producto->cod_categoria_producto)->first();
        $categoria = $categoria ? $categoria->descripcion : 'N/A';

        $grupoMinorista = DB::table('grupos_minorista')->where('codigo_de_producto_minorista', $producto->codigo_de_producto_minorista)->first();
        $codigoMinorista = $grupoMinorista ? $grupoMinorista->numeros_serie : 'N/A';

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

        if (empty($producto->descripcion)) {
            $productosRelacionadosAtributos = DB::table('producto_atributo')
                ->join('itemsdb', 'producto_atributo.producto_id', '=', 'itemsdb.id')
                ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
                ->where('producto_atributo.producto_id', '!=', $producto->id)
                ->whereIn('itemsdb.id', $productosRelacionados->pluck('id'))
                ->where('itemsdb.activo', '!=', 0)
                ->whereNotNull('itemsdb.descripcion')
                ->select('itemsdb.descripcion', 'itemsdb.id')
                ->first();

            if ($productosRelacionadosAtributos) {
                $producto->descripcion = $productosRelacionadosAtributos->descripcion;
                DB::table('itemsdb')->where('id', $producto->id)->update(['descripcion' => $producto->descripcion]);
            } else {

                $producto->descripcion = 'Descripción no disponible';
            }
        }

        $groupedProducts = [];
        foreach ($atributosProducto as $atributo) {
            $productosRelacionadosAtributos = DB::table('producto_atributo')
                ->join('itemsdb', 'producto_atributo.producto_id', '=', 'itemsdb.id')
                ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
                ->where('atributos.grupo_id', $atributo->grupo_id)
                ->where('itemsdb.id', '!=', $producto->id)
                ->where('itemsdb.activo', '!=', 0)
                ->select('itemsdb.id', 'itemsdb.descripcion', 'atributos.nombre as atributo_nombre', 'atributos.grupo_id')
                ->distinct()
                ->get();

            $groupedProducts[$atributo->grupo_descripcion] = $productosRelacionadosAtributos;
        }

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
            'groupedProducts',
            'nombreProveedor'
        ));
    }

    public function getDivisiones()
    {

        $divisiones = DB::table('divisiones')->select('id', 'descripcion')->get();

        return response()->json($divisiones);
    }

    public function getCategorias(Request $request, $cat)
    {

        $categorias = DB::table('categorias')->select('id', 'descripcion')->get();

        return response()->json($categorias);
    }

    public function showGrupo($division, $grupo, $categoria)
    {
        $productos = DB::table('itemsdb')
            ->select('id', 'nombre', 'descripcion', 'precio_unitario_IVAinc', 'no_s')
            ->where('cod_division', $division)
            ->where('cod_categoria_producto', $grupo)
            ->where('codigo_de_producto_minorista', $categoria)
            ->where('activo', 1)
            ->get();

        return view('search-result-view', compact('productos'));
    }

    public function getCartItems()
    {
        $userId = auth()->id();

        $cartItems = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
            ->where('carts.user_id', $userId)
            ->where('carts.status',1)
            ->where('itemsdb.activo', 1)
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
            $tieneItemsActivosDivision = DB::table('itemsdb')
                ->where('cod_division', $division->codigo_division)
                ->where('activo', 1)
                ->exists();

            if (!$tieneItemsActivosDivision) {
                continue;
            }

            $resultado[$division->codigo_division] = [
                'nombre' => $division->descripcion,
                'subcategorias' => [],
            ];

            $categorias = $categoriasDivisiones->where('cod_division', $division->codigo_division);

            foreach ($categorias as $categoria) {

                $tieneItemsActivosCategoria = DB::table('itemsdb')
                    ->where('cod_division', $division->codigo_division)
                    ->where('cod_categoria_producto', $categoria->cod_categoria_producto)
                    ->where('activo', 1)
                    ->exists();

                if (!$tieneItemsActivosCategoria) {
                    continue;
                }

                $resultado[$division->codigo_division]['subcategorias'][$categoria->cod_categoria_producto] = [
                    'nombre' => $categoria->descripcion,
                    'subsubcategorias' => [],
                ];

                $subcategorias = $gruposMinorista->where('cod_categoria_producto', $categoria->cod_categoria_producto);

                foreach ($subcategorias as $subcategoria) {

                    $tieneItemsActivosSubcategoria = DB::table('itemsdb')
                        ->where('cod_division', $division->codigo_division)
                        ->where('cod_categoria_producto', $categoria->cod_categoria_producto)
                        ->where('codigo_de_producto_minorista', $subcategoria->codigo_de_producto_minorista)
                        ->where('activo', 1)
                        ->exists();

                    if (!$tieneItemsActivosSubcategoria) {
                        continue;
                    }

                    $resultado[$division->codigo_division]['subcategorias'][$categoria->cod_categoria_producto]['subsubcategorias'][] = [
                        'texto' => $subcategoria->numeros_serie,
                        'codigo' => $subcategoria->codigo_de_producto_minorista,
                    ];
                }

                if (empty($resultado[$division->codigo_division]['subcategorias'][$categoria->cod_categoria_producto]['subsubcategorias'])) {
                    unset($resultado[$division->codigo_division]['subcategorias'][$categoria->cod_categoria_producto]);
                }
            }

            if (empty($resultado[$division->codigo_division]['subcategorias'])) {
                unset($resultado[$division->codigo_division]);
            }
        }

        return response()->json($resultado);
    }

    public function removeFromCart(Request $request)
    {
        $userId = auth()->id();
        $no_s = $request->input('no_s');

        $cart = DB::table('carts')->where('user_id', $userId)->where('status',1)->orderBy('id','asc')->first();
        if (!$cart) {
            return response()->json(['error' => 'No se encontró un carrito para el usuario.'], 404);
        }

        DB::table('cart_items')
            ->where('cart_id', $cart->id)
            ->where('no_s', $no_s)
            ->delete();

        DB::table('cart_shippment')->where('cart_id', $cart->id)->delete();

        return response()->json(['message' => 'Producto eliminado y método de envío eliminado.']);
    }

    public function addToCart(Request $request)
    {
        $userId = auth()->id();
        $requestedQuantity = $request->input('quantity', 1);

        $cart = DB::table('carts')->where('user_id', $userId)->where('status',1)->first();
        if (!$cart) {
            $cartId = DB::table('carts')->insertGetId([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $cartId = $cart->id;
        }

        $producto = DB::table('itemsdb')->where('id', $request->id)->first();
        $inventario = DB::table('inventario')->where('no_s', $producto->no_s)->first();

        if (!$producto || !$inventario || $inventario->cantidad_disponible <= 0) {
            return response()->json(['error' => 'Producto no disponible o stock agotado'], 400);
        }

        if ($requestedQuantity > $inventario->cantidad_disponible) {
            return response()->json([
                'error' => 'Inventario insuficiente',
                'suggested_quantity' => $inventario->cantidad_disponible,
            ], 400);
        }

        $cartItem = DB::table('cart_items')
            ->where('cart_id', $cartId)
            ->where('no_s', $producto->no_s)
            ->first();

        $descuento = $producto->descuento ?? 0;
        $precioConDescuento = $producto->precio_unitario_IVAinc - ($producto->precio_unitario_IVAinc * ($descuento / 100));

        $tasa_iva = 0;

        if ($producto->grupo_iva == 'IVA16') {
            $tasa_iva = 0.16;
        }

        if ($cartItem) {

            $newQuantity = $cartItem->quantity + $requestedQuantity;
            if ($newQuantity > $inventario->cantidad_disponible) {
                return response()->json([
                    'error' => 'Inventario insuficiente',
                    'suggested_quantity' => $inventario->cantidad_disponible - $cartItem->quantity,
                ], 400);
            }
            DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $producto->no_s)
                ->update(['quantity' => $newQuantity]);
        } else {

            DB::table('cart_items')->insert([
                'cart_id' => $cartId,
                'no_s' => $producto->no_s,
                'description' => $producto->nombre,
                'unit_price' => $producto->precio_unitario_IVAinc,
                'final_price' => $precioConDescuento,
                'discount' => $descuento,
                'quantity' => $requestedQuantity,
                'vat'=>$tasa_iva,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('cart_shippment')->where('cart_id', $cartId)->delete();

        return response()->json(['message' => 'Producto añadido al carrito con descuento y método de envío eliminado.']);
    }

    public function addMultipleToCart(Request $request)
    {
        try {
            $userId = auth()->id();
            $no_s = $request->input('no_s');
            $quantity = $request->input('quantity');

            $cart = DB::table('carts')->where('user_id', $userId)->where('status',1)->first();

            if (!$cart) {
                $cartId = DB::table('carts')->insertGetId([
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $cartId = $cart->id;
            }
            $cartItem = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $no_s)
                ->first();

            $producto = DB::table('itemsdb')->where('no_s', $no_s)->first();
            if (!$producto) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $inventario = DB::table('inventario')->where('no_s', $producto->no_s)->first();
            $cantidadDisponible = $inventario ? $inventario->cantidad_disponible : 0;

            $cantidadEnCarrito = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('no_s', $no_s)
                ->sum('quantity');
            $cantidadRequerida = $cantidadEnCarrito + $quantity;

            if ($cantidadDisponible < $cantidadRequerida) {
                return response()->json(['error' => 'No puedes añadir más de este producto. Stock insuficiente.'], 400);
            }
            $tasa_iva = 0;

            if ($producto->grupo_iva == 'IVA16') {
                $tasa_iva = 0.16;
            }

            if ($cartItem) {
                DB::table('cart_items')
                    ->where('cart_id', $cartId)
                    ->where('no_s', $no_s)
                    ->increment('quantity', $quantity);
            } else {
                DB::table('cart_items')->insert([
                    'cart_id' => $cartId,
                    'no_s' => $no_s,
                    'description' => $producto->nombre,
                    'unit_price' => $producto->precio_unitario_IVAinc,
                    'discount' => $producto->descuento,
                    'final_price' => $producto->descuento > 0 ? $producto->precio_con_descuento : $producto->precio_unitario_IVAinc,
                    'quantity' => $quantity,
                    'vat' => $tasa_iva,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $cantidadRestante = $cantidadDisponible - ($cantidadEnCarrito + $quantity);

            return response()->json([
                'message' => 'Producto añadido al carrito correctamente',
                'stock_restante' => $cantidadRestante,
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al añadir el producto al carrito. ' . $e->getMessage()], 500);
        }
    }
    public function updateQuantity(Request $request)
    {
        $userId = auth()->id();
        $productCode = $request->input('product_code');
        $newQuantity = (int) $request->input('quantity');

        $cart = DB::table('carts')->where('user_id', $userId)->where('status',1)->orderBy('id','asc')->first();
        $cartId = $cart->id;

        $cartItem = DB::table('cart_items')
            ->where('cart_id', $cartId)
            ->where('no_s', $productCode)
            ->first();

        $availableQuantity = DB::table('inventario')
            ->where('no_s', $productCode)
            ->value('cantidad_disponible');

        if ($cartItem) {
            if ($newQuantity > $availableQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock disponible.',
                    'maxQuantity' => $availableQuantity,
                ]);
            }

            if ($newQuantity < 1) {
                $newQuantity = 1;
            }

            DB::table('cart_items')
                ->where('id', $cartItem->id)
                ->update(['quantity' => $newQuantity]);

            DB::table('cart_shippment')->where('cart_id', $cartId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada exitosamente.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado en el carrito.',
                'currentQuantity' => 0,
            ]);
        }
    }

    public function search(Request $request, $division = null, $grupo = null, $categoria = null)
    {

        $mantenimiento = ProductosDestacadosController::checkMaintenance();
        if ($mantenimiento == 'true') {
            return redirect(route('mantenimento'));
        }

        $maxPriceInDatabase = DB::table('itemsdb')->max('precio_unitario_IVAinc');
        $precioFinalExpression = DB::raw('CASE WHEN descuento > 0 THEN precio_con_descuento ELSE precio_unitario_IVAinc END');
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
            ->where('activo', 1);

        if ($request->has('search') && trim($request->input('search')) !== '') {
            $search = str_replace(' ','%',$request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', $search . '%')
                    ->orWhere('nombre', 'like', '%' . $search . '%')
                    ->orWhere('no_s', 'like', $search . '%')
                    ->orWhere('no_s', 'like', '%' . $search . '%');
            })
                ->orderByRaw("(CASE
                    WHEN nombre LIKE ? THEN 1
                    WHEN nombre LIKE ? THEN 2
                    ELSE 3
                END)", [$search . '%', '%' . $search . '%']);
        }

        if ($division) {
            $query->where('cod_division', $division);

            if ($grupo) {
                $query->where('cod_categoria_producto', $grupo);
            }

            if ($categoria) {
                $query->where('codigo_de_producto_minorista', $categoria);
            }

            $divisionDesc = DB::table('divisiones')->where('codigo_division', $division)->value('descripcion');
            $grupoDesc = $grupo ? DB::table('categorias_divisiones')->where('cod_categoria_producto', $grupo)->value('descripcion') : null;
            $categoriaDesc = $categoria ? strtoupper(DB::table('grupos_minorista')->where('codigo_de_producto_minorista', $categoria)->value('numeros_serie')) : null;
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

        if ($request->has('min_price') || $request->has('max_price')) {
            $minPrice = $request->input('min_price', 0);
            $maxPrice = $request->input('max_price', $maxPriceInDatabase);
            $query->whereBetween($precioFinalExpression, [$minPrice, $maxPrice]);
        }

        $sortOffer = $request->input('sort_offer');
        $sortPrice = $request->input('sort_price');
        $sortName = $request->input('sort_name');

        if ($sortOffer) {
            $onOfferExpression = DB::raw('CASE WHEN descuento > 0 THEN 1 ELSE 0 END');
            $query->orderBy($onOfferExpression, $sortOffer == 'asc' ? 'asc' : 'desc');
        }

        if ($sortPrice) {
            $query->orderBy($precioFinalExpression, $sortPrice == 'asc' ? 'asc' : 'desc');
        }

        if ($sortName) {
            $query->orderBy('nombre', $sortName == 'asc' ? 'asc' : 'desc');
        }

        if (!$sortOffer && !$sortPrice && !$sortName) {
            $query->orderBy('nombre', 'asc');
        }

        $productos = $query->paginate(16)->appends($request->all());

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

        return view('search-result-view', [
            'productos' => $productos,
            'criterioBusqueda' => $criterioBusqueda,
            'criteriosBusqueda' => $criteriosBusqueda,
            'division' => $division,
            'grupo' => $grupo,
            'categoria' => $categoria,
            'maxPriceInDatabase' => $maxPriceInDatabase,
        ]);
    }

    public function ajaxSearch(Request $request)
    {
        $search = str_replace(' ','%',$request->input('search'));
        $query = DB::table('itemsdb')
            ->select('id', 'nombre', 'descripcion', 'precio_unitario_IVAinc', 'precio_con_descuento', 'descuento', 'no_s', 'unidad_medida_venta')
            ->where('activo', 1);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('no_s', 'like', '%' . $search . '%');
            });
        }

        $productos = $query->take(10)->get();

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
        $direcciones = DB::table('users_address')->where('user_id', $userId)->get();
        $tieneDirecciones = $direcciones->isNotEmpty();
        $contactName = $user->name;
        if ($userData) {
            $contactName = $userData->nombre . ' ' . $userData->apellido_paterno . ' ' . $userData->apellido_materno;
        }

        $cartId = CartController::getId();

        if (!$cartId) {
            return redirect()->route('home');
        }

        $cartItems = DB::table('cart_items')
            ->join('itemsdb', 'cart_items.no_s', '=', 'itemsdb.no_s')
            ->join('inventario', 'cart_items.no_s', '=', 'inventario.no_s')
            ->where('cart_items.cart_id', $cartId)
            ->where('itemsdb.activo', 1)
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
                'itemsdb.allow_cobrar_shipping',
                'itemsdb.grupo_iva'
            )
            ->get();

        foreach ($cartItems as $item) {
            $codigoProducto = str_pad($item->product_code, 6, "0", STR_PAD_LEFT);
            $imagePath = "storage/itemsview/{$codigoProducto}/{$codigoProducto}.jpg";

            if (file_exists(public_path($imagePath))) {
                $item->image = $imagePath;
            } else {
                $item->image = 'storage/itemsview/default.jpg';
            }
        }

        $shippment = DB::table('cart_shippment')
            ->leftJoin('tiendas', 'cart_shippment.store_id', '=', 'tiendas.id')
            ->where('cart_id', $cartId)
            ->select('cart_shippment.*', 'tiendas.nombre as store_name', 'tiendas.direccion as store_address')
            ->first();

        $shippmentExists = $shippment !== null;
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

            $eligibleProductCodes = $eligibleCartItems->pluck('product_code')->all();
            $nonEligibleItems = $cartItems->reject(function ($item) use ($eligibleProductCodes) {
                return in_array($item->product_code, $eligibleProductCodes);
            });
        } else {

            $eligibleCartItems = $cartItems;
            $nonEligibleItems = collect();
        }

        $totalPrice = $eligibleCartItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });

        $subtotalSinIVA = $totalPrice / 1.16;
        $totalDescuento = $eligibleCartItems->sum(function ($item) {
            return $item->unit_price * $item->quantity * ($item->discount / 100);
        });

        $iva = $subtotalSinIVA * 0.16;
        $shippingCostIVA = $shippment->final_price ?? 0.00;
        
        if ($shippmentExists && $shippment->ShipmentMethod === 'EnvioPorCobrar') {
            $shippingCostIVA = 0.00;
        }

        $totalFinal = $subtotalSinIVA + $iva + $shippingCostIVA;
        $activeShippingMethods = DB::table('shipping_methods')
            ->where('is_active', 1)
            ->get();

        $envios = [];

        foreach ($activeShippingMethods as $method) {
            $price = 0.00;

            if ($method->name === 'EnvioLocal') {
                $price = 250.00;
            } elseif ($method->name === 'EnvioPorPaqueteria') {
                $price = 500.00;
            } elseif ($method->name === 'RecogerEnTienda') {
                $price = 0.00;
            } elseif ($method->name === 'EnvioPorCobrar') {
                $price = 0.00;
            }

            $envios[] = [
                'name' => $method->display_name,
                'value' => $method->name,
                'price' => $price,
            ];
        }

        $noShippingMethodsAvailable = empty($envios);
        $shippingLocalController = new ShippingLocalController();
        $localShippingData = $shippingLocalController->handleLocalShipping($request, $userId, $totalPrice);
        $shippingPaqueteriaController = new ShippingPaqueteriaController();
        $paqueteriaShippingData = $shippingPaqueteriaController->handlePaqueteriaShipping($request, $userId, $totalPrice);
        $storePickupController = new StorePickupController();
        $storePickupData = $storePickupController->handleStorePickup($request, $userId);
        $shippingCobrarController = new ShippingCobrarController();
        $cobrarShippingData = $shippingCobrarController->handleCobrarShipping($request, $userId, $totalPrice);

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
        
        $subtotalProductosSinIVA = $eligibleCartItems->sum(function ($item) {
            $unitPriceSinIVA = $item->final_price / 1.16;

            return $unitPriceSinIVA * $item->quantity;
        });
    

        $totalDescuentoSinIVA = $eligibleCartItems->sum(function ($item) {
            $unitPriceSinIVA = ($item->final_price / 1.16)*($item->quantity);
   
            $discountAmount = $unitPriceSinIVA * ($item->discount / 100) * $item->quantity;
   
            return $discountAmount;
            
        });


        $shippingCostSinIVA = $shippingCostIVA / 1.16;
        $totalSinIVA = $subtotalProductosSinIVA - $totalDescuentoSinIVA + $shippingCostSinIVA;
        
        $ivaTotal = $totalSinIVA * 0.16;

        $totalFinal = $totalSinIVA + $ivaTotal;

        return view('carrito', [
            'cartItems' => $cartItems,
            'eligibleCartItems' => $eligibleCartItems,
            'nonEligibleItems' => $nonEligibleItems,
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
            'shippment' => $shippment,
            'shippmentExists' => $shippmentExists,
            'nonEligibleLocalShipping' => $nonEligibleLocalShipping,
            'nonEligiblePaqueteriaShipping' => $nonEligiblePaqueteriaShipping,
            'nonEligibleStorePickup' => $nonEligibleStorePickup,
            'nonEligibleCobrarShipping' => $nonEligibleCobrarShipping,
            'noShippingMethodsAvailable' => $noShippingMethodsAvailable,
            'contactName' => $contactName,
            'tieneDirecciones' => $tieneDirecciones,
            'subtotalProductosSinIVA' => $subtotalProductosSinIVA,
            'totalDescuentoSinIVA' => $totalDescuentoSinIVA,
            'shippingCostSinIVA' => $shippingCostSinIVA,
            'totalSinIVA' => $totalSinIVA,
            'ivaTotal' => $ivaTotal,
            'totalFinal' => $totalFinal,
        ]);
    }

    public function removeShipping(Request $request)
    {
        $userId = auth()->id();

        $cartId = DB::table('carts')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->value('id');

        DB::table('cart_shippment')
            ->where('cart_id', $cartId)
            ->delete();

        return response()->json(['success' => true]);
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
