<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ProductosDestacadosController extends Controller
{
    public function index()
    {
        // Obtener los productos destacados desde la base de datos
        $destacados = DB::table('productos_destacados')
            ->join('items', 'productos_destacados.item_id', '=', 'items.id')
            ->select('items.id', 'items.nombre', 'items.marca', 'items.precio_final', 'items.codigo')
            ->limit(20) // Ajustar este límite según tus necesidades
            ->get();

        // Retornar la vista con los productos destacados
        return view('index', compact('destacados'));
    }
}
