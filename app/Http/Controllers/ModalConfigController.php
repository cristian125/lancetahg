<?php
// ModalConfigController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModalConfigController extends Controller
{
    public function showModalConfig()
    {
        // Obtener la configuración del modal desde la base de datos
        $modalConfig = DB::table('modal_config')->first();

        // Verificar si no hay registro en la tabla
        if (!$modalConfig) {
            // Crear un objeto vacío si no hay configuración para evitar errores
            $modalConfig = (object) [
                'image_url' => null,
                'is_active' => 0
            ];
        }

        // Retornar la vista con la configuración del modal
        return view('admin.modal_config', compact('modalConfig'));
    }

    public function saveModalConfig(Request $request)
    {
        // Validar que la imagen es opcional y 'is_active' es un booleano
        $request->validate([
            'image', // 2MB máximo
            'is_active' => 'boolean',
        ]);

        // Obtener la configuración actual del modal o crearla si no existe
        $modalConfig = DB::table('modal_config')->first();

        // Subir la imagen si se ha seleccionado una
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('modals', 'public');
        } else {
            $path = $modalConfig->image_url ?? null;
        }

        // Si no hay configuración previa, crear un nuevo registro
        if (!$modalConfig) {
            DB::table('modal_config')->insert([
                'image_url' => $path,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Actualizar la configuración existente
            DB::table('modal_config')->update([
                'image_url' => $path,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Configuración del modal actualizada.');
    }
}
