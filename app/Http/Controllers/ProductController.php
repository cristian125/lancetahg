<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function show($id)
    {
        // Obtener el producto por ID
        $producto = DB::table('items')->find($id);

        // Verificar si se encontró el producto
        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }

        // Obtener la imagen del producto base
        $imagenBase = $producto->image_path ? $producto->image_path : 'itemsview/default.jpg';
        if (!Storage::disk('public')->exists($imagenBase)) {
            $imagenBase = 'itemsview/default.jpg';
        }
        $imagenPrincipal = asset("storage/{$imagenBase}");

        // Obtener las variantes del producto, si las hay
        $variantes = DB::table('item_variantes')->where('item_id', $id)->get();

        // Manejar las miniaturas de las imágenes, iniciando con la imagen base
        $imagenesMiniaturas = collect([$imagenPrincipal]);

        foreach ($variantes as $variante) {
            $rutaImagenVariante = $variante->image_path ? $variante->image_path : 'itemsview/default.jpg';
            if (!Storage::disk('public')->exists($rutaImagenVariante)) {
                $rutaImagenVariante = 'itemsview/default.jpg';
            }
            $imagenesMiniaturas->push(asset("storage/{$rutaImagenVariante}"));
        }

        return view('index2', compact('producto', 'imagenPrincipal', 'imagenesMiniaturas', 'variantes'));
    }
}
