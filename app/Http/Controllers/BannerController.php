<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{

    public function index()
    {
        $bannerImage = DB::table('banner_images')->where('active', 1)->first();
        $allImages = DB::table('banner_images')->get();

        return view('admin.banner_settings', compact('bannerImage', 'allImages'));
    }


    public function uploadBanner(Request $request)
    {
        $request->validate([
            'banner_image',
        ]);

        if ($request->hasFile('banner_image')) {

            $imagePath = $request->file('banner_image')->store('img/baner_principal', 'public');

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
        $image = DB::table('banner_images')->where('id', $id)->first();
    
        if ($image) {
            $newStatus = $image->active ? 0 : 1;
    
            DB::table('banner_images')->update(['active' => 0]); 
            DB::table('banner_images')->where('id', $id)->update(['active' => $newStatus]);
    
            return redirect()->route('admin.banner_settings')->with('success', 'Imagen actualizada correctamente.');
        }
    
        return redirect()->route('admin.banner_settings')->with('error', 'Imagen no encontrada.');
    }
    

    public function deleteBanner($id)
    {

        $image = DB::table('banner_images')->where('id', $id)->first();

        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            DB::table('banner_images')->where('id', $id)->delete();

            return redirect()->route('admin.banner_settings')->with('success', 'Imagen eliminada correctamente.');
        }

        return redirect()->route('admin.banner_settings')->with('error', 'Imagen no encontrada.');
    }
}
