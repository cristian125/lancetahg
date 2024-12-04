<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminAuthController extends Controller
{

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login')->withErrors('Las credenciales son incorrectas. Intente nuevamente.');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('status', 'Sesión cerrada correctamente.');
    }

    public function showRegisterForm()
    {
        return view('admin.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);
    
        $admin = new \App\Models\Admin();
        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->password = $request->input('password'); // No hashear aquí
        $admin->role = $request->input('role');
        $admin->save();
    
        return redirect()->route('admin.login')->with('success', 'Administrador registrado exitosamente.');
    }
    
    


    public function manageAdmins()
    {
        $adminId = Auth::guard('admin')->id();
        $currentAdminRole = DB::table('admins')->where('id', $adminId)->value('role');

        if ($currentAdminRole !== 'superusuario') {
            return redirect()->route('admin.dashboard')->withErrors('No tiene permisos para acceder a esta sección.');
        }

        $admins = DB::table('admins')->get();
        return view('admin.manage_admins', compact('admins'));
    }


    public function updateAdminRole(Request $request, $id)
    {

        $adminId = Auth::guard('admin')->id();
        $currentAdminRole = DB::table('admins')->where('id', $adminId)->value('role');

        if ($currentAdminRole !== 'superusuario') {
            return redirect()->route('admin.dashboard')->withErrors('No tienes permisos para realizar esta acción.');
        }

        if ($id == 1) {
            return redirect()->route('admin.manage.admins')->withErrors('No puedes cambiar el rol del superadministrador principal.');
        }

        $request->validate([
            'role' => 'required|string|in:superusuario,editor,viewer',
        ]);

        DB::table('admins')->where('id', $id)->update([
            'role' => $request->input('role'),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.manage.admins')->with('success', 'Rol del administrador actualizado exitosamente.');
    }
}
