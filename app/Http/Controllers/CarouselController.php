<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CarouselController extends Controller
{
    public function index()
    {
        $this->syncCarouselImages();
        $carouselImages = DB::table('carousel_images')
            ->orderBy('order')
            ->get();

        return view('admin.carousel_settings', compact('carouselImages'));
    }

    protected function syncCarouselImages()
    {

        $existingImages = DB::table('carousel_images')->pluck('image_path')->toArray();

        $filesInStorage = Storage::disk('public')->files('carousel');

        $newImages = array_diff($filesInStorage, $existingImages);

        foreach ($newImages as $newImage) {
            DB::table('carousel_images')->insert([
                'image_path' => $newImage,
                'created_at' => now(),
            ]);
        }
    }

    public function updateCarouselImages(Request $request)
    {

        $request->validate([
            'carousel_images.*',
            'product_link' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $image) {

                $imagePath = $image->store('carousel', 'public');

                DB::table('carousel_images')->insert([
                    'image_path' => $imagePath,
                    'product_link' => $request->input('product_link', null), 
                    'created_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.carousel_settings')->with('success', 'Imágenes y enlaces actualizados correctamente.');
    }


    public function toggleCarouselImage($id)
    {
        $image = DB::table('carousel_images')->where('id', $id)->first();

        if ($image) {
            $newStatus = $image->active ? 0 : 1;

            DB::table('carousel_images')
                ->where('id', $id)
                ->update(['active' => $newStatus]);

            return redirect()->route('admin.carousel_settings')->with('success', 'Estado de la imagen actualizado correctamente.');
        }

        return redirect()->route('admin.carousel_settings')->with('error', 'Imagen no encontrada.');
    }

    public function deleteCarouselImage($id)
    {

        $image = DB::table('carousel_images')->where('id', $id)->first();

        if ($image) {

            Storage::disk('public')->delete($image->image_path);
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
                ->update(['order' => $index + 1]); 
        }

        return response()->json(['success' => true]);
    }
 
/*
/*
/*     //////   GRID
/*
*/
    
    public function indexGrid()
    {
        $gridImages = DB::table('grid_images')->get();

        return view('admin.grid_settings', compact('gridImages'));
    }

    public function updateGridImages(Request $request)
    {
        $request->validate([
            'grid_images' => 'required',
            'grid_images.*',
            'no_s' => 'required' 
        ]);



        $producto = DB::table('itemsdb')->where('no_s', $request->input('no_s'))->first();

        if (!$producto) {
            return redirect()->route('admin.grid_settings')->with('error', 'Número de serie no encontrado en la base de datos.');
        }

        if ($request->hasFile('grid_images')) {

            foreach ($request->file('grid_images') as $image) {
                $imagePath = $image->store('grid', 'public');

                DB::table('grid_images')->insert([
                    'image_path' => $imagePath,
                    'product_id' => $producto->id, 
                    'created_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.grid_settings')->with('success', 'Imágenes del contenedor 2x2 actualizadas correctamente.');
    }

    public function toggleGridImage($id)
    {

        $image = DB::table('grid_images')->where('id', $id)->first();

        if ($image) {
            $newStatus = $image->active ? 0 : 1;

            DB::table('grid_images')
                ->where('id', $id)
                ->update(['active' => $newStatus]);

            return redirect()->route('admin.grid_settings')->with('success', 'Estado de la imagen actualizado correctamente.');
        }

        return redirect()->route('admin.grid_settings')->with('error', 'Imagen no encontrada.');
    }

    public function deleteGridImage($id)
    {
        $image = DB::table('grid_images')->where('id', $id)->first();

        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            DB::table('grid_images')->where('id', $id)->delete();

            return redirect()->route('admin.grid_settings')->with('success', 'Imagen eliminada correctamente.');
        }

        return redirect()->route('admin.grid_settings')->with('error', 'Imagen no encontrada.');
    }
}
