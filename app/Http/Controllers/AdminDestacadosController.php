<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDestacadosController extends Controller
{
    /**
     * Mostrar el formulario para seleccionar productos destacados.
     */
    public function showDestacadosForm()
    {
        // Obtener todos los productos activos desde itemsdb
        $productos = DB::table('itemsdb')
            ->where('activo', 1)
            ->select('id', 'no_s', 'nombre')
            ->get();
    
        // Obtener los productos destacados en el orden guardado en la tabla 'destacados'
        $productosDestacados = DB::table('itemsdb')
            ->join('destacados', 'itemsdb.no_s', '=', 'destacados.no_s')
            ->orderBy('destacados.orden')  // Ordenar según el campo 'orden'
            ->select('itemsdb.no_s', 'itemsdb.nombre')
            ->get();
    
        // Convertir los productos destacados a un array de 'no_s' para usar en la vista
        $destacados = $productosDestacados->pluck('no_s')->toArray();
        
        // Retornar la vista del formulario de destacados
        return view('admin.destacados_form', compact('productos', 'destacados'));
    }
    
    /**
     * Guardar los productos destacados seleccionados.
     */
    public function guardarDestacados(Request $request)
    {
        // Validar que se han seleccionado productos
        $request->validate([
            'productos' => 'required|array', // Validar que haya productos seleccionados
            'productos.*' => 'string|exists:itemsdb,no_s', // Validar que los productos existan en la tabla itemsdb
            'ordenes' => 'required|array' // Validar que los productos tengan un orden asignado
        ]);

        try {
            // Iniciar la transacción
            DB::beginTransaction();

            // Eliminar los registros actuales en la tabla 'destacados'
            DB::table('destacados')->delete();

            // Insertar los productos seleccionados con su orden
            foreach ($request->productos as $index => $no_s) {
                DB::table('destacados')->insert([
                    'no_s' => $no_s,
                    'orden' => $request->ordenes[$index], // Guardar el orden
                    'created_at' => now()
                ]);
            }

            // Confirmar la transacción
            DB::commit();

            return redirect()->back()->with('success', 'Productos destacados actualizados correctamente.');

        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar los productos destacados.');
        }
    }
}


