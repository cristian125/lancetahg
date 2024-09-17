<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        
        if(Auth::check()==true)
        {
            return redirect()->route('home');
        }    
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if(Auth::check()==true)
        {
            return;
        }
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Autenticar al usuario después del registro
        auth()->login($user);

        // Redirigir a la página de inicio o a la que prefieras
        return redirect('/');
    }

    public function change(Request $request)
    {
        $this->validate($request, [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        $user = Auth::user();
          
        // Verificar la contraseña actual
        if (!Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual no es correcta.'],
            ]);
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return redirect()->route('cuenta')->with('status', 'Contraseña cambiada exitosamente.');
    }
}