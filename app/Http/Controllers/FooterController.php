<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class FooterController extends Controller
{
    public static function render()
    {
        // Obtener los enlaces visibles, ordenados por columna y posición
        $footerLinks = DB::table('footer_links')
            ->where('visibility', 1)
            ->orderBy('column_number')
            ->orderBy('position')
            ->get();

        // Pasar $footerLinks a la vista
        return view('partials.footer', ['footerLinks' => $footerLinks])->render();
    }
}
