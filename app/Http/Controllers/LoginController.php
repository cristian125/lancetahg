<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Si la validación falla
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Intentar iniciar sesión
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // Respuesta exitosa en formato JSON para AJAX
            if ($request->ajax()) {
                return response()->json(['success' => 'Inicio de sesión exitoso'], 200);
            }

            return redirect()->intended('/');
        }

        // Si la autenticación falla
        if ($request->ajax()) {
            return response()->json(['errors' => ['email' => ['Las credenciales proporcionadas no son correctas.']]], 422);
        }

        return back()->withErrors(['email' => 'Las credenciales proporcionadas no son correctas.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
