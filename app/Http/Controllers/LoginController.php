<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentar iniciar sesión
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redireccionar a la página principal con el usuario autenticado
            return redirect()->intended('/');
        }

        // Si falla la autenticación, redireccionar de nuevo con un error
        return redirect()->route('home')->WithErrors(['email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
