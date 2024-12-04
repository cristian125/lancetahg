<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('admin.configuracion');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (!Auth::guard('admin')->validate([
            'email' => $admin->email,
            'password' => $request->current_password,
        ])) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $admin->password = $request->new_password; // El mutador la hasheará
        $admin->save();

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Contraseña actualizada correctamente. Por favor, inicie sesión nuevamente.');
    }
}
