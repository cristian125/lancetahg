<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

        // Intentar obtener el usuario por email
        $user = User::where('email', $request->email)->first();

        // Si el usuario no existe
        if (!$user) {
            $message = 'La cuenta no existe. Por favor, regístrate.';
            if ($request->ajax()) {
                return response()->json(['errors' => ['email' => [$message]]], 422);
            }
            return back()->withErrors(['email' => $message])->withInput();
        }

        // Si el usuario no ha verificado su correo
        if (!$user->email_verified) {
            // Generar un nuevo token de verificación
            $token = Str::random(60);

            // Actualizar o crear el registro en 'email_verifications'
            DB::table('email_verifications')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );

            // Enviar el correo de verificación
            $verificationLink = route('verify.email', ['token' => $token]);
            $data = [
                'user' => $user,
                'verificationLink' => $verificationLink,
            ];

            try {
                Mail::send('emails.verify_email', $data, function ($message) use ($user) {
                    $message->to($user->email)->subject('Verifica tu correo electrónico');
                });
            } catch (\Exception $e) {
                \Log::error('Error al enviar el correo de verificación: ' . $e->getMessage());
            }

            $message = 'Debes verificar tu correo electrónico antes de iniciar sesión. Te hemos enviado un correo electrónico para verificar tu cuenta.';
            if ($request->ajax()) {
                return response()->json(['errors' => ['email' => [$message]]], 422);
            }
            return back()->withErrors(['email' => $message])->withInput();
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

        // Si las credenciales son incorrectas
        $message = 'Las credenciales proporcionadas no son correctas.';
        if ($request->ajax()) {
            return response()->json(['errors' => ['email' => [$message]]], 422);
        }

        return back()->withErrors(['email' => $message])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
