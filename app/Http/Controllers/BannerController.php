<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Muestra la página de administración de la imagen del banner
    public function index()
    {
        $bannerImage = DB::table('banner_images')->where('active', 1)->first();
        $allImages = DB::table('banner_images')->get();

        return view('admin.banner_settings', compact('bannerImage', 'allImages'));
    }

    // Cargar una nueva imagen del banner
    public function uploadBanner(Request $request)
    {
        $request->validate([
            'banner_image',
        ]);

        if ($request->hasFile('banner_image')) {
            // Guardar la imagen en el almacenamiento
            $imagePath = $request->file('banner_image')->store('img/baner_principal', 'public');

            // Guardar la ruta en la base de datos
            DB::table('banner_images')->insert([
                'image_path' => $imagePath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.banner_settings')->with('success', 'Imagen subida correctamente.');
        }

        return redirect()->route('admin.banner_settings')->with('error', 'Error al subir la imagen.');
    }

    public function toggleBanner($id)
    {
        // Obtener la imagen seleccionada
        $image = DB::table('banner_images')->where('id', $id)->first();
    
        if ($image) {
            // Si la imagen está activa, la desactiva. Si está desactivada, activa solo esa imagen.
            $newStatus = $image->active ? 0 : 1;
    
            // Desactivar todas las imágenes y activar la seleccionada
            DB::table('banner_images')->update(['active' => 0]); // Desactivar todas
            DB::table('banner_images')->where('id', $id)->update(['active' => $newStatus]);
    
            return redirect()->route('admin.banner_settings')->with('success', 'Imagen actualizada correctamente.');
        }
    
        return redirect()->route('admin.banner_settings')->with('error', 'Imagen no encontrada.');
    }
    
    // Eliminar una imagen del banner
    public function deleteBanner($id)
    {
        // Obtener la imagen
        $image = DB::table('banner_images')->where('id', $id)->first();

        if ($image) {
            // Eliminar la imagen del almacenamiento
            Storage::disk('public')->delete($image->image_path);

            // Eliminar el registro de la base de datos
            DB::table('banner_images')->where('id', $id)->delete();

            return redirect()->route('admin.banner_settings')->with('success', 'Imagen eliminada correctamente.');
        }

        return redirect()->route('admin.banner_settings')->with('error', 'Imagen no encontrada.');
    }
}
