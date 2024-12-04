<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $grupos = DB::table('grupos')
            ->when($search, function ($query, $search) {
                return $query->where('descripcion', 'like', '%' . $search . '%');
            })
            ->paginate(10);
    
        return view('admin.grupos.index', compact('grupos', 'search'));
    }
    
    public function create()
    {
        return view('admin.grupos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'nombre' => 'nullable|string|max:255', // O 'required' si es obligatorio
        ]);
        

        DB::table('grupos')->insert([
            'descripcion' => $request->input('descripcion'),
            'nombre' => $request->input('nombre', 'Sin nombre'), // Proporciona un valor predeterminado si no se envía
        ]);
        return redirect()->route('admin.grupos.index')->with('success', 'Grupo creado exitosamente.');
    }

    public function edit($id)
    {
        $grupo = DB::table('grupos')->where('id', $id)->first();
        if (!$grupo) {
            return redirect()->route('admin.grupos.index')->withErrors('Grupo no encontrado.');
        }
        return view('admin.grupos.edit', compact('grupo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        DB::table('grupos')->where('id', $id)->update([
            'descripcion' => $request->input('descripcion'),
        ]);

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo actualizado exitosamente.');
    }
    public function destroy($id)
    {
        // Eliminar los atributos asociados al grupo
        DB::table('atributos')->where('grupo_id', $id)->delete();
    
        // Ahora elimina el grupo
        DB::table('grupos')->where('id', $id)->delete();
    
        return redirect()->route('admin.grupos.index')->with('success', 'Grupo eliminado exitosamente.');
    }
    
}
