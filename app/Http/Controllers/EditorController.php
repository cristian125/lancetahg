<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EditorController extends Controller
{
    // Mostrar la lista de páginas para seleccionar cuál editar
    public function listPages()
    {
        // Obtener todas las páginas de la base de datos
        $pages = DB::table('pages')->get();

        // Retornar la vista 'admin.pages' con todas las páginas
        return view('admin.pages', compact('pages'));
    }

    // Mostrar el formulario para crear una nueva página
    public function createPage()
    {
        return view('admin.create_page');
    }

    // Guardar la nueva página
    public function storePage(Request $request)
    {
        // Validar los campos del formulario
        $request->validate([
            'title' => 'required|string|max:190',
            'slug' => 'required|string|max:190|unique:pages,slug',
        ]);

        // Crear la nueva página en la base de datos
        $pageId = DB::table('pages')->insertGetId([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'content' => '', // Inicialmente vacío
            'created_by' => auth()->user()->name, // Cambia esto si tienes otro campo para el usuario
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirigir al editor para editar el contenido de la nueva página
        return redirect()->route('admin.editor', $pageId)->with('success', 'Página creada correctamente. Ahora puedes editar su contenido.');
    }

    // Mostrar la página en el editor
    public function showEditor($id)
    {
        // Obtener la página de la base de datos directamente
        $page = DB::table('pages')->where('id', $id)->first();

        // Verificar si la página existe
        if (!$page) {
            return redirect()->route('admin.pages.list')->with('error', 'Página no encontrada.');
        }

        // Retornar la vista 'admin.editor' pasando la página obtenida
        return view('admin.editor', compact('page'));
    }

    public function saveContent(Request $request, $id)
    {
        // Validar los campos del formulario
        $request->validate([
            'title' => 'required|string|max:190',
            'slug' => 'required|string|max:190|unique:pages,slug,' . $id,
            'content' => 'required'
        ]);

        // Actualizar los datos directamente en la base de datos
        DB::table('pages')->where('id', $id)->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'content' => $request->input('content'),
            'updated_by' => auth()->user()->name, // Cambia esto si tienes otro campo para el usuario
            'updated_at' => now(),
        ]);

        // Redirigir al editor con un mensaje de éxito
        return redirect()->route('admin.editor', $id)->with('success', 'Contenido guardado correctamente.');
    }



    public function deletePage($id)
    {
        // Verificar si la página existe
        $page = DB::table('pages')->where('id', $id)->first();

        if (!$page) {
            return redirect()->route('admin.pages.list')->with('error', 'Página no encontrada.');
        }

        // Eliminar la página de la base de datos
        DB::table('pages')->where('id', $id)->delete();

        // Redirigir a la lista de páginas con un mensaje de éxito
        return redirect()->route('admin.pages.list')->with('success', 'Página eliminada correctamente.');
    }
}
