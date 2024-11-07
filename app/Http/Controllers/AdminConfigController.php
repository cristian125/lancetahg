<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminConfigController extends Controller
{

    public function configurarMantenimiento(Request $request)
    {
        if ($request->isMethod('post')) {

            $mantenimiento = $request->input('mantenimiento') == 'true' ? 'true' : 'false';
            DB::statement("UPDATE configuraciones SET value = ? WHERE name = 'mantenimiento'", [$mantenimiento]);
            return redirect()->route('configadmin')->with('success', 'El estado de mantenimiento ha sido actualizado correctamente.');
        }

        $mantenimiento = DB::selectOne("SELECT value FROM configuraciones WHERE name = 'mantenimiento'");
        $mantenimiento = $mantenimiento ? $mantenimiento->value : 'false';
        return view('admin.config', ['mantenimiento' => $mantenimiento]);
    }
}
