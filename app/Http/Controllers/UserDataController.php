<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDataController extends Controller
{
    public function update(Request $request)
    {
        // Retrieve the current authenticated user
        $user = Auth::user();
        
        // Check if the user's data already exists in the `users_data` table
        $userData = DB::table('users_data')->where('user_id', $user->id)->first();
        
        // Data to insert or update in the `users_data` table
        $data = [
            'user_id' => $user->id,
            'nombre' => $request->input('nombre'),
            'apellido_paterno' => $request->input('apellido_paterno'),
            'apellido_materno' => $request->input('apellido_materno'),
            'telefono' => $request->input('telefono'),
            'tratamiento' => $request->input('tratamiento'),
            'correo' => $user->email, // Assuming email should remain from the user record
            'updated_at' => now()
        ];
        
        // If user data exists, update it; otherwise, insert a new record
        if ($userData) {
            DB::table('users_data')->where('user_id', $user->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('users_data')->insert($data);
        }

        // Now also update the name field in the `users` table
        DB::table('users')->where('id', $user->id)->update([
            'name' => $request->input('nombre'),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Datos personales actualizados correctamente.');
    }
}
