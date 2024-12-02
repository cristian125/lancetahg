<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        // Obtener todos los banners
        $bannerImages = DB::table('banner_images')->get();

        return view('admin.banner_settings', compact('bannerImages'));
    }

    public function uploadBanner(Request $request)
    {
        $request->validate([
            'banner_image',
            'device' => 'required|in:desktop,mobile',
        ]);

        if ($request->hasFile('banner_image')) {
            // Almacenar la imagen
            $imagePath = $request->file('banner_image')->store('img/banner', 'public');

            // Desactivar banners activos existentes para el dispositivo seleccionado
            DB::table('banner_images')->where('device', $request->device)->update(['active' => 0]);

            // Insertar el nuevo banner
            DB::table('banner_images')->insert([
                'image_path' => $imagePath,
                'device' => $request->device,
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.banner_settings')->with('success', 'Imagen subida correctamente.');
        }

        return redirect()->route('admin.banner_settings')->with('error', 'Error al subir la imagen.');
    }

    public function toggleBanner($id)
    {
        $image = DB::table('banner_images')->where('id', $id)->first();

        if ($image) {
            $newStatus = $image->active ? 0 : 1;

            if ($newStatus == 1) {
                // Desactivar otros banners del mismo dispositivo
                DB::table('banner_images')->where('device', $image->device)->update(['active' => 0]);
            }

            // Actualizar el estado del banner seleccionado
            DB::table('banner_images')->where('id', $id)->update(['active' => $newStatus]);

            return redirect()->route('admin.banner_settings')->with('success', 'Imagen actualizada correctamente.');
        }

        return redirect()->route('admin.banner_settings')->with('error', 'Imagen no encontrada.');
    }

    public function deleteBanner($id)
    {
        $image = DB::table('banner_images')->where('id', $id)->first();

        if ($image) {
            // Eliminar el archivo de almacenamiento
            Storage::disk('public')->delete($image->image_path);

            // Eliminar el registro de la base de datos
            DB::table('banner_images')->where('id', $id)->delete();

            return redirect()->route('admin.banner_settings')->with('success', 'Imagen eliminada correctamente.');
        }

        return redirect()->route('admin.banner_settings')->with('error', 'Imagen no encontrada.');
    }
}
