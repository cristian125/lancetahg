<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {

        if (Auth::check() == true) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }
    public function register(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear el usuario con 'email_verified' en 0
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'email_verified' => 0,
        ]);

        // Generar token único para verificación
        $token = Str::random(60);

        // Guardar el token en la tabla 'email_verifications'
        DB::table('email_verifications')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Enviar correo electrónico con el enlace de verificación
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
            // Registrar el error
            \Log::error('Error al enviar el correo de verificación: ' . $e->getMessage());

            // Manejar la respuesta en caso de error
            if ($request->ajax()) {
                return response()->json(['error' => 'No se pudo enviar el correo de verificación. Por favor, inténtalo de nuevo más tarde.'], 500);
            }
            return redirect('/')->with('error', 'No se pudo enviar el correo de verificación. Por favor, inténtalo de nuevo más tarde.');
        }

        // Responder adecuadamente según el tipo de solicitud
        if ($request->ajax()) {
            return response()->json(['success' => 'Te hemos enviado un correo electrónico para verificar tu cuenta. Por favor, revisa tu bandeja de entrada.']);
        } else {
            return redirect('/')->with('success', 'Te hemos enviado un correo electrónico para verificar tu cuenta. Por favor, revisa tu bandeja de entrada.');
        }
    }
    // public function resendVerificationEmail(Request $request)
    // {
    //     $email = $request->input('email');

    //     // Validar el email
    //     $request->validate([
    //         'email' => 'required|email',
    //     ]);

    //     $user = User::where('email', $email)->first();

    //     if (!$user) {
    //         return back()->withErrors(['email' => 'No existe una cuenta con este correo electrónico.']);
    //     }

    //     if ($user->email_verified) {
    //         return back()->with('success', 'Tu correo electrónico ya ha sido verificado. Puedes iniciar sesión.');
    //     }

    //     // Verificar si ya se envió un correo en las últimas 24 horas
    //     $verification = DB::table('email_verifications')->where('email', $email)->first();

    //     if ($verification && $verification->created_at > now()->subHours(24)) {
    //         return back()->with('error', 'Ya se ha enviado un correo de verificación recientemente. Por favor, revisa tu bandeja de entrada.');
    //     }

    //     // Generar un nuevo token
    //     $token = Str::random(60);

    //     // Actualizar o crear el registro en 'email_verifications'
    //     DB::table('email_verifications')->updateOrInsert(
    //         ['email' => $email],
    //         ['token' => $token, 'created_at' => now()]
    //     );

    //     // Enviar el correo de verificación
    //     $verificationLink = route('verify.email', ['token' => $token]);
    //     $data = [
    //         'user' => $user,
    //         'verificationLink' => $verificationLink,
    //     ];

    //     try {
    //         Mail::send('emails.verify_email', $data, function ($message) use ($user) {
    //             $message->to($user->email)->subject('Reenvío de verificación de correo electrónico');
    //         });
    //     } catch (\Exception $e) {
    //         \Log::error('Error al reenviar el correo de verificación: ' . $e->getMessage());
    //         return back()->with('error', 'No se pudo enviar el correo de verificación. Por favor, inténtalo de nuevo más tarde.');
    //     }

    //     return back()->with('success', 'Te hemos reenviado el correo de verificación. Por favor, revisa tu bandeja de entrada.');
    // }

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

    public function verifyEmail($token)
    {
        // Buscar el token en la tabla 'email_verifications'
        $verification = DB::table('email_verifications')->where('token', $token)->first();
    
        if (!$verification) {
            return redirect('/')->with('error', 'Token de verificación inválido.');
        }
    
        // Actualizar el campo 'email_verified' del usuario
        DB::table('users')->where('email', $verification->email)->update(['email_verified' => 1]);
    
        // Eliminar el registro de 'email_verifications'
        DB::table('email_verifications')->where('email', $verification->email)->delete();
    
        // Redirigir al home con un mensaje de éxito
        return redirect('/')->with('success', 'Correo electrónico verificado exitosamente. Ahora puedes iniciar sesión.');
    }
    
}
