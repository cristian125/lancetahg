<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;


class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.email'); // Mostrar la vista de solicitud de correo
    }



    public function sendResetLinkEmail(Request $request)
{
    // Validar el correo electrónico ingresado
    $request->validate(['email' => 'required|email']);

    // Verificar si el correo existe en la tabla users
    $user = DB::table('users')->where('email', $request->email)->first();

    if ($user) {
        // Generar el token para restablecer la contraseña
        $token = sha1(time());

        // Guardar el token en la tabla password_resets
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Guardar la solicitud en password_reset_requests
        DB::table('password_reset_requests')->insert([
            'email' => $user->email,
            'token' => $token,
            'ip_address' => $request->ip(), // IP de la solicitud
            'user_agent' => $request->header('User-Agent'), // Navegador y dispositivo
            'request_time' => now(),
        ]);

        // Enviar el correo
        Mail::send([], [], function ($message) use ($user, $token) {
            $message->to($user->email)
                    ->subject('Enlace para restablecer contraseña')
                    ->setBody('Haz clic en este enlace para restablecer tu contraseña: ' . url('/reset-password/' . $token));
        });
    }

    // Devolver la misma vista con un mensaje de éxito
    return back()->with('status', 'Si el correo está asociado a una cuenta, te enviaremos un enlace para restablecer tu contraseña.');
}

    
}