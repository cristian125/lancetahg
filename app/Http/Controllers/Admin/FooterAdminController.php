<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FooterAdminController extends Controller
{
    // Mostrar la lista de enlaces
    public function index()
    {
        $footerLinks = DB::table('footer_links')
            ->orderBy('column_number')  
            ->orderBy('position')
            ->get();

        return view('admin.footer_links.index', ['footerLinks' => $footerLinks]);
    }

    // Mostrar el formulario para crear un nuevo enlace
    public function create()
    {
        return view('admin.footer_links.create');
    }

    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'title' => 'required',
            'url' => 'required',
            'column_number' => 'required|integer',
            'position' => 'required|integer',
            'visibility' => 'required|boolean',
        ]);
    
        // Insertar el nuevo enlace en la base de datos
        DB::table('footer_links')->insert([
            'title' => $request->input('title'),
            'url' => $request->input('url'),
            'column_number' => $request->input('column_number'),
            'position' => $request->input('position'),
            'visibility' => $request->input('visibility'),
        ]);
    
        return redirect()->route('footer_links.index')->with('success', 'Enlace creado exitosamente.');
    }
    
    

    // Mostrar el formulario para editar un enlace existente
    public function edit($id)
    {
        $link = DB::table('footer_links')->where('id', $id)->first();

        return view('admin.footer_links.edit', ['link' => $link]);
    }

    // Actualizar un enlace existente en la base de datos
    public function update(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'title' => 'required',
            'url' => 'required',
            'column_number' => 'required|integer',
            'position' => 'required|integer',
            'visibility' => 'required|boolean',
        ]);

        // Actualizar el enlace
        DB::table('footer_links')->where('id', $id)->update([
            'title' => $request->input('title'),
            'url' => $request->input('url'),
            'column_number' => $request->input('column_number'),
            'position' => $request->input('position'),
            'visibility' => $request->input('visibility'),
        ]);

        return redirect()->route('footer_links.index')->with('success', 'Enlace actualizado exitosamente.');
    }

    // Eliminar un enlace de la base de datos
    public function destroy($id)
    {
        DB::table('footer_links')->where('id', $id)->delete();

        return redirect()->route('footer_links.index')->with('success', 'Enlace eliminado exitosamente.');
    }
}
