<?php 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

    class ResetPasswordController extends Controller
    {
        // Mostrar el formulario de restablecimiento de contraseña
        public function showResetForm($token)
        {
            // Buscar el token en la base de datos
            $passwordReset = DB::table('password_resets')->where('token', $token)->first();

            // Verificar que el token exista y no haya expirado
            if (!$passwordReset) {
                return redirect('/')->withErrors(['token' => 'El enlace de restablecimiento es inválido o ha expirado.']);
            }

            // Eliminar el token actual para que no pueda reutilizarse
            DB::table('password_resets')->where('token', $token)->delete();

            // Crear un nuevo token para cambiar la contraseña
            $newToken = Str::random(60);

            // Guardar el nuevo token para el cambio de contraseña
            DB::table('password_resets')->insert([
                'email' => $passwordReset->email,
                'token' => $newToken,
                'created_at' => Carbon::now(),
            ]);

            // Enviar el nuevo token a la vista para cambiar la contraseña
            return view('auth.password.reset', ['token' => $newToken]);
        }

        public function reset(Request $request)
        {
            // Validar los campos del formulario
            $request->validate([
                'password' => 'required|confirmed',
                'token' => 'required'
            ]);
        
            // Buscar el token en la tabla `password_resets`
            $passwordReset = DB::table('password_resets')->where('token', $request->token)->first();
        
            // Verificar que el token sea válido
            if (!$passwordReset) {
                return back()->withErrors(['token' => 'El token es inválido o ha expirado.']);
            }
        
            // Actualizar la contraseña del usuario
            DB::table('users')->where('email', $passwordReset->email)->update([
                'password' => Hash::make($request->password)
            ]);
        
            // Actualizar la solicitud en la tabla `password_reset_requests`
            DB::table('password_reset_requests')->where('email', $passwordReset->email)->update([
                'completed' => 1,
                'completed_at' => now(),
            ]);
        
            // Eliminar el token de la tabla `password_resets` después de usarlo
            DB::table('password_resets')->where('token', $request->token)->delete();
        
            // Redirigir al usuario al inicio de sesión con un mensaje de éxito
            return redirect('/')->with('status', 'Tu contraseña ha sido restablecida con éxito.');
        }
        
    }
