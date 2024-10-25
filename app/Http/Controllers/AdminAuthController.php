<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

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
            // Si la autenticación es exitosa, redirigir al dashboard
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
        // Validar los datos del formulario de registro
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);
    
        // Insertar el nuevo administrador en la base de datos
        DB::table('admins')->insert([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role' => $request->input('role'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Redirigir a la página de login con un mensaje de éxito
        return redirect()->route('admin.login')->with('success', 'Administrador registrado exitosamente.');
    }
    

    // Método para mostrar la lista de administradores
    public function manageAdmins()
    {
        // Obtener el ID del administrador autenticado
        $adminId = Auth::guard('admin')->id();

        // Obtener el rol del administrador autenticado
        $currentAdminRole = DB::table('admins')->where('id', $adminId)->value('role');

        // Verificar si es superusuario
        if ($currentAdminRole !== 'superusuario') {
            return redirect()->route('admin.dashboard')->withErrors('No tiene permisos para acceder a esta sección.');
        }

        // Obtener todos los administradores
        $admins = DB::table('admins')->get();

        // Retornar la vista con la lista de administradores
        return view('admin.manage_admins', compact('admins'));
    }
    
    // Método para actualizar el rol de un administrador
    public function updateAdminRole(Request $request, $id)
    {
        // Obtener el ID del administrador autenticado
        $adminId = Auth::guard('admin')->id();

        // Obtener el rol del administrador autenticado
        $currentAdminRole = DB::table('admins')->where('id', $adminId)->value('role');

        // Verificar si es superusuario
        if ($currentAdminRole !== 'superusuario') {
            return redirect()->route('admin.dashboard')->withErrors('No tienes permisos para realizar esta acción.');
        }

        // Evitar cambiar el rol del superadministrador principal
        if ($id == 1) {
            return redirect()->route('admin.manage.admins')->withErrors('No puedes cambiar el rol del superadministrador principal.');
        }

        // Validar el nuevo rol
        $request->validate([
            'role' => 'required|string|in:superusuario,editor,viewer',
        ]);

        // Actualizar el rol del administrador en la base de datos
        DB::table('admins')->where('id', $id)->update([
            'role' => $request->input('role'),
            'updated_at' => now(),
        ]);

        // Redirigir de vuelta con mensaje de éxito
        return redirect()->route('admin.manage.admins')->with('success', 'Rol del administrador actualizado exitosamente.');
    }
}
