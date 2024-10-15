<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


class ProductosDestacadosController extends Controller
{

    
    public function index(Request $request)
    {
        if($this->checkMaintenance()==true)
        {
            return view('mantenimiento');
        }

        // Obtener los productos destacados seleccionados por el administrador y ordenarlos según la columna `orden`
        $productosDestacados = DB::table('itemsdb')
            ->join('destacados', 'itemsdb.no_s', '=', 'destacados.no_s')
            ->where('itemsdb.activo', 1)  // Mostrar solo productos activos
            ->orderBy('destacados.orden', 'asc')  // Ordenar por el campo `orden`
            ->select(
                'itemsdb.id',
                'itemsdb.no_s',
                'itemsdb.nombre',
                'itemsdb.descripcion',
                'itemsdb.cod_categoria_producto as marca',
                'itemsdb.precio_unitario_IVAinc',
                'itemsdb.precio_con_descuento',
                'itemsdb.descuento',
                'itemsdb.codigo_de_producto_minorista as codigo'
            )
            ->get();
    
        // Si hay menos de 20 productos seleccionados, completamos con productos aleatorios
        if ($productosDestacados->count() < 20) {
            $productosAleatorios = DB::table('itemsdb')
                ->whereNotIn('no_s', $productosDestacados->pluck('no_s'))  // Excluir los productos ya destacados
                ->where('activo', 1)
                ->inRandomOrder()
                ->limit(20 - $productosDestacados->count())
                ->select(
                    'id',
                    'no_s',
                    'nombre',
                    'descripcion',
                    'cod_categoria_producto as marca',
                    'precio_unitario_IVAinc',
                    'precio_con_descuento',
                    'descuento',
                    'codigo_de_producto_minorista as codigo'
                )
                ->get();
    
            $destacados = $productosDestacados->merge($productosAleatorios);
        } else {
            $destacados = $productosDestacados->take(20);
        }
    
        // Agregar la imagen principal y secundarias para cada producto
        foreach ($destacados as $producto) {
            // Seleccionar el precio a mostrar
            if ($producto->descuento > 0) {
                $producto->precio_final = $producto->precio_con_descuento;
            } else {
                $producto->precio_final = $producto->precio_unitario_IVAinc;
            }
    
            // Usar 'no_s' como identificador del producto
            $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
            $imagenPrincipal = asset("storage/itemsview/default.jpg"); // Imagen por defecto
            $imagenesSecundarias = [];
    
            // Verificar si existe una carpeta con imágenes para el producto
            $carpetaImagenes = "itemsview/{$codigoProducto}";
    
            if (Storage::disk('public')->exists($carpetaImagenes)) {
                // Obtener todas las imágenes dentro de la carpeta
                $imagenesEnCarpeta = Storage::disk('public')->files($carpetaImagenes);
    
                // Procesar las imágenes encontradas
                foreach ($imagenesEnCarpeta as $imagen) {
                    $nombreImagen = basename($imagen);
    
                    // Verificar si es la imagen principal
                    if ($nombreImagen === "{$codigoProducto}.jpg") {
                        $imagenPrincipal = asset("storage/{$imagen}");
                    } else {
                        // Añadir las imágenes secundarias
                        if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $nombreImagen)) {
                            $imagenesSecundarias[] = asset("storage/{$imagen}");
                        }
                    }
                }
            }
    
            // Añadir la imagen principal y secundarias al objeto producto
            $producto->imagen_principal = $imagenPrincipal;
            $producto->imagenes_secundarias = $imagenesSecundarias;
        }
        $carouselImages = DB::table('carousel_images')
        ->where('active', 1)
        ->orderBy('order')
        ->get();
    
        $gridImages = DB::table('grid_images')->where('active', 1)->get();
        $bannerImage = DB::table('banner_images')->where('active', 1)->first();




 
        
            // Obtener la configuración del modal desde la tabla `modal_config`
            $modalConfig = DB::table('modal_config')->first();
        
            // Verificar que el modalConfig no sea nulo y que tenga el campo 'is_active' correctamente definido
            $modalActivo = isset($modalConfig->is_active) ? $modalConfig->is_active : false;
        
            // Obtener la imagen del modal
            $modalImagen = isset($modalConfig->image_url) ? $modalConfig->image_url : null;
        

        
        // Retornar la vista con los productos destacados
        return view('index', compact('destacados', 'carouselImages', 'gridImages', 'bannerImage','modalActivo', 'modalImagen'));
    }
    /***
     * Se consulta si esta en mantenimiento 
     * */
    public static function checkMaintenance()
    {
        $configuraciones = DB::table('configuraciones')->where('name','mantenimiento')->first();
        if($configuraciones->value=='true')
        {
            return true;
        }
        return false;
    }


    public function maintenance()
    {
        return view('mantenimiento');
    }
    public function addToCart(Request $request)
    {
        try {
            $userId = auth()->id();
            $productId = $request->input('id');
            $no_s = $request->input('no_s');
            $quantity = 1; // Asumimos que siempre se añade 1 producto desde la vista de destacados

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
            $producto = DB::table('itemsdb')
                ->where('no_s', $no_s)
                ->where('activo', 1)  // Solo productos activos
                ->first();

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
                    'description' => $producto->descripcion,
                    'unit_price' => $producto->precio_unitario_IVAinc,
                    'discount' => $producto->descuento,
                    'final_price' => $producto->descuento > 0 ? $producto->precio_con_descuento : $producto->precio_unitario_IVAinc,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['message' => 'Producto añadido al carrito correctamente'], 200);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al añadir el producto al carrito'], 500);
        }
    }

    public function checkStock(Request $request)
    {
        $no_s = $request->input('no_s');
        $quantityToAdd = 1; // Asumiendo que se añade 1 producto a la vez

        // Obtener detalles del producto desde la tabla `inventario`
        $inventario = DB::table('inventario')
            ->join('itemsdb', 'inventario.no_s', '=', 'itemsdb.no_s')
            ->where('itemsdb.no_s', $no_s)
            ->where('itemsdb.activo', 1)  // Solo productos activos
            ->select('inventario.cantidad_disponible')
            ->first();


        if (!$inventario) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $cantidadDisponible = $inventario->cantidad_disponible;

        // Obtener la cantidad ya en el carrito
        $userId = auth()->id();
        $cart = DB::table('carts')->where('user_id', $userId)->first();
        $cantidadEnCarrito = 0;

        if ($cart) {
            $cantidadEnCarrito = DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->where('no_s', $no_s)
                ->sum('quantity');
        }

        // Verificar si se puede añadir la cantidad deseada al carrito
        if (($cantidadEnCarrito + $quantityToAdd) > $cantidadDisponible) {
            return response()->json(['can_add' => false]);
        } else {
            return response()->json(['can_add' => true]);
        }
    }

    public function search(Request $request)
    {
        $search = $request->search;
        // Implementa la búsqueda en la tabla `itemsdb`
        $resultados = DB::table('itemsdb')
            ->where('activo', 1)  // Solo productos activos
            ->where(function ($query) use ($search) {
                $query->where('descripcion', 'like', '%' . $search . '%')
                    ->orWhere('codigo_de_producto_minorista', 'like', '%' . $search . '%');
            })
            ->get();


        // Agregar la imagen principal y secundarias para cada resultado
        foreach ($resultados as $producto) {
            // Seleccionar el precio a mostrar
            if ($producto->descuento > 0) {
                $producto->precio_final = $producto->precio_con_descuento;
            } else {
                $producto->precio_final = $producto->precio_unitario_IVAinc;
            }

            $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
            $imagenPrincipal = asset("storage/itemsview/default.jpg"); // Imagen por defecto
            $imagenesSecundarias = [];

            // Verificar si existe una carpeta con imágenes para el producto
            $carpetaImagenes = "itemsview/{$codigoProducto}";

            if (Storage::disk('public')->exists($carpetaImagenes)) {
                // Obtener todas las imágenes dentro de la carpeta
                $imagenesEnCarpeta = Storage::disk('public')->files($carpetaImagenes);

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

            // Añadir la imagen principal y secundarias al objeto producto
            $producto->imagen_principal = $imagenPrincipal;
            $producto->imagenes_secundarias = $imagenesSecundarias;
        }

        return view('search_results', compact('resultados'));
    }
}
