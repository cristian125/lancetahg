<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function showPage($slug)
    {
        if ($slug == '') {
            abort(404);
            return;
        }
    
        // Busca el contenido en la base de datos
        $page = DB::table('pages')->where('slug', $slug)->first();
    
        if (!$page) {
            abort(404, 'Página no encontrada');
        }
    
        // Decodificar el contenido HTML escapado
        $page->content = html_entity_decode($page->content);
    
        return view('page', ['page' => $page,'slug'=>$slug]);
    }
    

    // public function showPrivacyPolicy()
    // {
    //     // Busca el contenido del aviso de privacidad en la base de datos
    //     $page = DB::table('pages')->where('slug', 'aviso-de-privacidad')->first();
    
    //     if (!$page) {
    //         abort(404, 'Página no encontrada');
    //     }
    
    //     return view('avisopriv', ['page' => $page]);
    // }
    
}

