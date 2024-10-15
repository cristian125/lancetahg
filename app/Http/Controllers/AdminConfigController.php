<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminConfigController extends Controller
{
    // Manejar tanto GET para mostrar el formulario como POST para actualizar
    public function configurarMantenimiento(Request $request)
    {
        if ($request->isMethod('post')) {
            // Si el método es POST, actualizamos el valor de mantenimiento
            $mantenimiento = $request->input('mantenimiento') == 'true' ? 'true' : 'false';

            // Actualizar el valor en la base de datos
            DB::statement("UPDATE configuraciones SET value = ? WHERE name = 'mantenimiento'", [$mantenimiento]);

            // Redirigir con un mensaje de éxito
            return redirect()->route('configadmin')->with('success', 'El estado de mantenimiento ha sido actualizado correctamente.');
        }

        // Si el método es GET, mostramos la configuración actual
        $mantenimiento = DB::selectOne("SELECT value FROM configuraciones WHERE name = 'mantenimiento'");

        // Si no se encuentra el registro de mantenimiento, devolver un valor por defecto
        $mantenimiento = $mantenimiento ? $mantenimiento->value : 'false';

        // Pasar el valor a la vista configuracion.blade.php
        return view('admin.config', ['mantenimiento' => $mantenimiento]);
    }
}
