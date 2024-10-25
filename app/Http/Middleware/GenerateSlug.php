<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateSlug
{
    public function handle(Request $request, Closure $next)
    {
        // Obtener el ID de la ruta
        $id = $request->route('id');

        // Si hay un ID pero no hay un slug en la URL
        if ($id && !$request->route('slug')) {
            // Buscar el producto en la base de datos
            $producto = DB::table('itemsdb')->where('id', $id)->where('activo', 1)->first();

            if ($producto) {
                // Generar el slug basado en el nombre del producto
                $slug = Str::slug($producto->nombre);
                
                // Redirigir a la URL correcta con el slug
                return redirect()->route('producto.redirect', ['id' => $id, 'slug' => $slug], 301);
            }
        }

        return $next($request);
    }
}
