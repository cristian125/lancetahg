<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtributoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $atributos = DB::table('atributos')
            ->join('grupos', 'atributos.grupo_id', '=', 'grupos.id')
            ->select('atributos.*', 'grupos.descripcion as grupo_descripcion')
            ->when($search, function ($query, $search) {
                return $query->where('atributos.nombre', 'like', '%' . $search . '%')
                             ->orWhere('grupos.descripcion', 'like', '%' . $search . '%');
            })
            ->paginate(10);
    
        return view('admin.atributos.index', compact('atributos', 'search'));
    }
    

    public function create()
    {
        $grupos = DB::table('grupos')->select('id', 'descripcion')->get();
        return view('admin.atributos.create', compact('grupos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'nombre' => 'required|string|max:255',
        ]);

        DB::table('atributos')->insert([
            'grupo_id' => $request->input('grupo_id'),
            'nombre' => $request->input('nombre'),
        ]);

        return redirect()->route('admin.atributos.index')->with('success', 'Atributo creado exitosamente.');
    }

    public function edit($id)
    {
        $atributo = DB::table('atributos')->where('id', $id)->first();
        if (!$atributo) {
            return redirect()->route('admin.atributos.index')->withErrors('Atributo no encontrado.');
        }
        $grupos = DB::table('grupos')->select('id', 'descripcion')->get();
        return view('admin.atributos.edit', compact('atributo', 'grupos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'nombre' => 'required|string|max:255',
        ]);

        DB::table('atributos')->where('id', $id)->update([
            'grupo_id' => $request->input('grupo_id'),
            'nombre' => $request->input('nombre'),
        ]);

        return redirect()->route('admin.atributos.index')->with('success', 'Atributo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        // Eliminar el atributo y sus relaciones
        DB::table('atributos')->where('id', $id)->delete();
        DB::table('producto_atributo')->where('atributo_id', $id)->delete();

        return redirect()->route('admin.atributos.index')->with('success', 'Atributo eliminado exitosamente.');
    }
}
