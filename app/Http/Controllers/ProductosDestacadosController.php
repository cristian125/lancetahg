<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductosDestacadosController extends Controller
{
    public function index()
    {
        // Obtener productos al azar desde la tabla `list_prod_min`
        $destacados = DB::table('list_prod_min')
            ->select('id', 'no_s', 'descripcion as nombre', 'cod_categoria_producto as marca', 'precio_unitario as precio_final', 'codigo_de_producto_minorista as codigo')
            ->inRandomOrder() // Selecciona productos de manera aleatoria
            ->limit(20)
            ->get();

        // Agregar la imagen principal y secundarias para cada producto
        foreach ($destacados as $producto) {
            // Usar 'no_s' como identificador del producto
            $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
            $imagenPrincipal = asset("storage/itemsview/default.jpg");  // Imagen por defecto
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

        // Retornar la vista con los productos seleccionados al azar y sus imágenes
        return view('index', compact('destacados'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        // Implementa la búsqueda en la tabla `list_prod_min`
        $resultados = DB::table('list_prod_min')
            ->where('descripcion', 'like', '%' . $search . '%')
            ->orWhere('codigo_de_producto_minorista', 'like', '%' . $search . '%')
            ->get();

        // Agregar la imagen principal y secundarias para cada resultado
        foreach ($resultados as $producto) {
            $codigoProducto = str_pad($producto->no_s, 6, "0", STR_PAD_LEFT);
            $imagenPrincipal = asset("storage/itemsview/default.jpg");  // Imagen por defecto
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
