<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CarouselController extends Controller
{
    // Mostrar las imágenes del carrusel en el área de administración
    public function index()
    {
        // Escanear la carpeta de almacenamiento en busca de nuevas imágenes
        $this->syncCarouselImages();

        // Obtener las rutas de las imágenes del carrusel desde la base de datos
        $carouselImages = DB::table('carousel_images')
            ->orderBy('order')
            ->get();

        return view('admin.carousel_settings', compact('carouselImages'));
    }

    // Método para sincronizar las imágenes del directorio con la base de datos
    protected function syncCarouselImages()
    {
        // Obtener todas las imágenes que ya están en la base de datos
        $existingImages = DB::table('carousel_images')->pluck('image_path')->toArray();

        // Obtener las imágenes que están en la carpeta 'carousel'
        $filesInStorage = Storage::disk('public')->files('carousel');

        // Filtrar solo las imágenes que no están en la base de datos
        $newImages = array_diff($filesInStorage, $existingImages);

        // Insertar las nuevas imágenes en la base de datos
        foreach ($newImages as $newImage) {
            DB::table('carousel_images')->insert([
                'image_path' => $newImage,
                'created_at' => now(),
            ]);
        }
    }

    public function updateCarouselImages(Request $request)
    {
        // Validar solo si se han seleccionado imágenes y enlaces (si es necesario)
        $request->validate([
            'carousel_images.*',
            'product_link' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $image) {
                // Guardar la imagen en el storage en la carpeta 'carousel'
                $imagePath = $image->store('carousel', 'public');

                // Guardar la ruta de la imagen y el enlace del producto en la base de datos
                DB::table('carousel_images')->insert([
                    'image_path' => $imagePath,
                    'product_link' => $request->input('product_link', null), // Guarda el enlace o deja null
                    'created_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.carousel_settings')->with('success', 'Imágenes y enlaces actualizados correctamente.');
    }


    // Activar o desactivar una imagen del carrusel
    public function toggleCarouselImage($id)
    {
        // Obtener la imagen de la base de datos
        $image = DB::table('carousel_images')->where('id', $id)->first();

        if ($image) {
            // Cambiar el estado de la imagen (activar/desactivar)
            $newStatus = $image->active ? 0 : 1;

            DB::table('carousel_images')
                ->where('id', $id)
                ->update(['active' => $newStatus]);

            return redirect()->route('admin.carousel_settings')->with('success', 'Estado de la imagen actualizado correctamente.');
        }

        return redirect()->route('admin.carousel_settings')->with('error', 'Imagen no encontrada.');
    }
    // Eliminar una imagen del carrusel
    public function deleteCarouselImage($id)
    {
        // Obtener la imagen de la base de datos
        $image = DB::table('carousel_images')->where('id', $id)->first();

        if ($image) {
            // Eliminar la imagen del storage
            Storage::disk('public')->delete($image->image_path);

            // Eliminar el registro de la base de datos
            DB::table('carousel_images')->where('id', $id)->delete();

            return redirect()->route('admin.carousel_settings')->with('success', 'Imagen eliminada correctamente.');
        }

        return redirect()->route('admin.carousel_settings')->with('error', 'Imagen no encontrada.');
    }



    public function updateCarouselImageLink(Request $request, $id)
    {
        $request->validate([
            'product_link' => 'nullable|string|max:255',
        ]);

        DB::table('carousel_images')
            ->where('id', $id)
            ->update(['product_link' => $request->input('product_link', null)]);

        return redirect()->route('admin.carousel_settings')->with('success', 'Enlace actualizado correctamente.');
    }

    public function updateCarouselOrder(Request $request)
    {
        $orderedIds = $request->input('orderedIds');

        foreach ($orderedIds as $index => $id) {
            DB::table('carousel_images')
                ->where('id', $id)
                ->update(['order' => $index + 1]); // Guardar el nuevo orden
        }

        return response()->json(['success' => true]);
    }




    

/*
/*
/*     //////   GRID
/*
*/
    




    // Mostrar las imágenes del carrusel 2x2 en el área de administración
    public function indexGrid()
    {
        // Obtener las rutas de las imágenes 2x2 desde la base de datos
        $gridImages = DB::table('grid_images')->get();

        return view('admin.grid_settings', compact('gridImages'));
    }

    public function updateGridImages(Request $request)
    {
        // Validar que se suban imágenes y que se ingrese un número de serie
        $request->validate([
            'grid_images' => 'required',
            'grid_images.*',
            'no_s' => 'required' // El usuario ingresa el número de serie (no_s)
        ]);

        // Depurar para verificar los datos recibidos
        // dd($request->all());

        // Buscar el id del producto en la tabla itemsdb usando el no_s
        $producto = DB::table('itemsdb')->where('no_s', $request->input('no_s'))->first();

        if (!$producto) {
            return redirect()->route('admin.grid_settings')->with('error', 'Número de serie no encontrado en la base de datos.');
        }

        if ($request->hasFile('grid_images')) {
            // Recorrer las imágenes y guardarlas en storage y en la base de datos
            foreach ($request->file('grid_images') as $image) {
                // Guardar la imagen en el storage en la carpeta 'grid'
                $imagePath = $image->store('grid', 'public');

                // Guardar la ruta de la imagen en la base de datos con el id del producto
                DB::table('grid_images')->insert([
                    'image_path' => $imagePath,
                    'product_id' => $producto->id, // Usar el id encontrado
                    'created_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.grid_settings')->with('success', 'Imágenes del contenedor 2x2 actualizadas correctamente.');
    }



    // Activar o desactivar una imagen del contenedor 2x2
    public function toggleGridImage($id)
    {
        // Obtener la imagen de la base de datos
        $image = DB::table('grid_images')->where('id', $id)->first();

        if ($image) {
            // Cambiar el estado de la imagen (activar/desactivar)
            $newStatus = $image->active ? 0 : 1;

            DB::table('grid_images')
                ->where('id', $id)
                ->update(['active' => $newStatus]);

            return redirect()->route('admin.grid_settings')->with('success', 'Estado de la imagen actualizado correctamente.');
        }

        return redirect()->route('admin.grid_settings')->with('error', 'Imagen no encontrada.');
    }

    // Eliminar una imagen del contenedor 2x2
    public function deleteGridImage($id)
    {
        // Obtener la imagen de la base de datos
        $image = DB::table('grid_images')->where('id', $id)->first();

        if ($image) {
            // Eliminar la imagen del storage
            Storage::disk('public')->delete($image->image_path);

            // Eliminar el registro de la base de datos
            DB::table('grid_images')->where('id', $id)->delete();

            return redirect()->route('admin.grid_settings')->with('success', 'Imagen eliminada correctamente.');
        }

        return redirect()->route('admin.grid_settings')->with('error', 'Imagen no encontrada.');
    }
}
