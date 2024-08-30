<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
}