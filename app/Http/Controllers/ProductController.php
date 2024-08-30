<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function show($id)
{
    // Obtener el producto por ID desde `list_prod_min`
    $producto = DB::table('list_prod_min')->find($id);

    // Verificar si se encontró el producto
    if (!$producto) {
        abort(404, 'Producto no encontrado');
    }

    // Configuración para la imagen principal
    $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);  // Asegurar que el código tenga 6 dígitos
    $imagenPrincipal = asset("storage/itemsview/default.jpg");  // Imagen por defecto

    // Inicializar el array de imágenes secundarias
    $imagenesSecundarias = [];

    // Verificar si existe una carpeta con imágenes
    $carpetaSecundarias = "itemsview/{$codigoProducto}";

    if (Storage::disk('public')->exists($carpetaSecundarias)) {
        // Obtener todas las imágenes dentro de la carpeta
        $imagenesEnCarpeta = Storage::disk('public')->files($carpetaSecundarias);

        // Procesar las imágenes encontradas
        foreach ($imagenesEnCarpeta as $imagen) {
            // Obtener solo el nombre del archivo sin la ruta
            $nombreImagen = basename($imagen);

            // Verificar si es la imagen principal (sin guiones ni caracteres adicionales)
            if ($nombreImagen === "{$codigoProducto}.jpg" || $nombreImagen === "{$codigoProducto}.jpeg" || $nombreImagen === "{$codigoProducto}.png" || $nombreImagen === "{$codigoProducto}.gif") {
                $imagenPrincipal = asset("storage/{$imagen}");
            } else {
                // Si no es la imagen principal, añadirla a las imágenes secundarias
                if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $nombreImagen)) {
                    $imagenesSecundarias[] = asset("storage/{$imagen}");
                }
            }
        }
    }

    // Añadir la imagen principal a la lista de miniaturas para que sea la primera
    $imagenesMiniaturas = collect([$imagenPrincipal])->merge($imagenesSecundarias);

    // Retornar la vista con los datos del producto y las imágenes
    return view('index2', compact('id', 'producto', 'imagenPrincipal', 'imagenesMiniaturas'));
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
        $productos = DB::table('list_prod_min')
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
        $cartItems = DB::table('car_items')
            ->join('list_prod_min', 'car_items.product_id', '=', 'list_prod_min.id')
            ->where('user_id', $userId)
            ->select('list_prod_min.id','list_prod_min.no_s','list_prod_min.unidad_medida_venta as unidad','list_prod_min.descripcion as name', 'list_prod_min.descripcion_alias as description', 'car_items.quantity', 'list_prod_min.precio_unitario as price')
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
                    'texto' => $subcategoria->numeros_serie, // Este texto se mostrará en el menú
                    'codigo' => $subcategoria->codigo_de_producto_minorista, // Este código se usará en la búsqueda
                ];
            }
        }
    }

    return response()->json($resultado);
}


    public function getProductsByCategory($division, $categoriaProducto, $grupoMinorista)
    {
        // Obtener los productos que cumplen con las tres categorías usando los códigos correctos
        $productos = DB::table('list_prod_min')
            ->where('cod_division', $division)
            ->where('cod_categoria_producto', $categoriaProducto)
            ->where('codigo_de_producto_minorista', $grupoMinorista)
            ->select('id', 'descripcion_alias', 'descripcion', 'precio_unitario_IVAinc', 'no_s')
            ->get();

        $criterioBusqueda = "División: $division, Categoría: $categoriaProducto, Grupo Minorista: $grupoMinorista";

        // Retornar la vista con los productos encontrados
        return view('search-result-view', [
            'productos' => $productos,
            'criterioBusqueda' => $criterioBusqueda,
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $userId = auth()->id();
        $productId = $request->id;
        // Encuentra el ítem del carrito
        $cartItem = DB::table('car_items')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {

            DB::table('car_items')
                    ->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();

            /*
            if ($cartItem->quantity > 1) {
                // Disminuye la cantidad en 1
                DB::table('car_items')
                    ->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->decrement('quantity');
            } else {
                // Elimina el ítem del carrito si la cantidad es 1
                DB::table('car_items')
                    ->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();
            }
            */
        }

        return response()->json(['message' => 'Ítem eliminado del carrito']);
    }

    public function addToCart(Request $request)
    {
        $id = $request->id;

        $userId = auth()->id(); // Get the authenticated user's ID

        // Check if the product is already in the user's cart
        $cartItem = DB::table('car_items')
            ->where('user_id', $userId)
            ->where('product_id', $id)
            ->first();

        if ($cartItem !== null) {
            // If the product is already in the cart, increase the quantity
            DB::table('car_items')
                ->where('user_id', $userId)
                ->where('product_id', $id)
                ->increment('quantity');
        } else {
            // If the product is not in the cart, add it with an initial quantity of 1
            DB::table('car_items')->insert([
                'user_id' => $userId,
                'product_id' => $id,
                'quantity' => 1, // Initial quantity
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Producto añadido al carrito'], 200);
    }

    public function search(Request $request)
    {
        $search = str_replace(' ', '%', $request->search);
        // Aquí puedes implementar la búsqueda en la tabla `list_prod_min`
        /*
        $resultados = DB::table('list_prod_min')
        ->where('descripcion', 'like', '%' . $search . '%')
        ->orWhere('no_s', 'like', '%' . $search . '%')
        ->get();
         */
        $resultados = DB::select('SELECT * FROM list_prod_min WHERE descripcion like ? or no_s like ?', ['%' . $search . '%', '%' . $search . '%']);
        return response(json_encode($resultados), 200, ['Content-Type' => 'json']);
    }

    public function searchAndDisplay(Request $request, $division = null, $grupo = null, $categoria = null)
    {
        $query = DB::table('list_prod_min')
            ->select('id', 'descripcion_alias', 'descripcion', 'precio_unitario_IVAinc', 'no_s');
    
        // Si hay un término de búsqueda
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('descripcion', 'like', '%' . $search . '%')
                  ->orWhere('no_s', 'like', '%' . $search . '%');
            });
        }
    
        // Si la búsqueda es por categorías
        if ($division && $grupo && $categoria) {
            $query->where('cod_division', $division)
                  ->where('cod_categoria_producto', $grupo)
                  ->where('codigo_de_producto_minorista', $categoria);
        }
    
        $productos = $query->get();
    
        // Asignar la lógica de imágenes
        foreach ($productos as $producto) {
            $carpetaProducto = 'storage/itemsview/' . $producto->no_s;
            $imagePath = $carpetaProducto . '/' . $producto->no_s . '.jpg';
            $defaultImagePath = 'storage/itemsview/default.jpg';
    
            // Verificar si la imagen existe
            $producto->imagen_principal = Storage::disk('public')->exists($imagePath) ? asset($imagePath) : asset($defaultImagePath);
        }
    
        $criterioBusqueda = $request->input('search', "División: $division, Categoría: $grupo, Grupo Minorista: $categoria");
    
        return view('search-result-view', [
            'productos' => $productos,
            'criterioBusqueda' => $criterioBusqueda,
        ]);
    }
    



    public function showCart()
    {
        $userId = auth()->id(); // Obtener el ID del usuario autenticado

        // Consulta los elementos del carrito
        $cartItems = DB::table('car_items')
            ->join('list_prod_min', 'car_items.product_id', '=', 'list_prod_min.id')
            ->where('user_id', $userId)
            ->select(
                'list_prod_min.id', // Agregar el ID del producto
                'list_prod_min.unidad_medida_venta as unidad', // Agregar el ID del producto
                'list_prod_min.descripcion as name',
                'list_prod_min.descripcion_alias as description',
                'car_items.quantity',
                'list_prod_min.precio_unitario as price',
                'list_prod_min.no_s as product_code' // Usar el código del producto para determinar la imagen
            )
            ->get();

        // Recorre cada elemento del carrito y asigna la ruta de la imagen correcta
        foreach ($cartItems as $item) {
            $imagePath = public_path("storage/itemsview/{$item->product_code}.jpg");
            if (!file_exists($imagePath)) {
                $item->image = 'itemsview/default.jpg';
            } else {
                $item->image = "itemsview/{$item->product_code}.jpg";
            }
        }

        // Calcular el precio total del carrito
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view('carrito', compact('cartItems', 'totalPrice'));
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
