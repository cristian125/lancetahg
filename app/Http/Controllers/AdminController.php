<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Mostrar el formulario de cambio de contraseña
    public function showChangePasswordForm()
    {
        return view('admin.configuracion');
    }



    // Cambiar la contraseña
    public function changePassword(Request $request)
    {
        // Validar los campos
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Verificar si la contraseña actual es correcta
        if (!Hash::check($request->current_password, Auth::guard('admin')->user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Actualizar la nueva contraseña
        $admin = Auth::guard('admin')->user();
        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return back()->with('status', 'Contraseña actualizada correctamente.');
    }
}
