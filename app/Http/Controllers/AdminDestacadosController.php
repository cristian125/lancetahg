<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDestacadosController extends Controller
{

    public function showDestacadosForm()
    {

        $productos = DB::table('itemsdb')
            ->where('activo', 1)
            ->select('id', 'no_s', 'nombre')
            ->get();
    
        $productosDestacados = DB::table('itemsdb')
            ->join('destacados', 'itemsdb.no_s', '=', 'destacados.no_s')
            ->orderBy('destacados.orden')  
            ->select('itemsdb.no_s', 'itemsdb.nombre')
            ->where('itemsdb.activo', '!=', 0) 
            ->get();
    
        $destacados = $productosDestacados->pluck('no_s')->toArray();
        return view('admin.destacados_form', compact('productos', 'destacados'));
    }
    

    public function guardarDestacados(Request $request)
    {
        $request->validate([
            'productos' => 'required|array', 
            'productos.*' => 'string|exists:itemsdb,no_s', 
            'ordenes' => 'required|array' 
        ]);

        try {
            DB::beginTransaction();
            DB::table('destacados')->delete();
            foreach ($request->productos as $index => $no_s) {
                DB::table('destacados')->insert([
                    'no_s' => $no_s,
                    'orden' => $request->ordenes[$index], 
                    'created_at' => now()
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Productos destacados actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar los productos destacados.');
        }
    }
}


