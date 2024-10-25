<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class AccountController extends Controller
{
    
    public function index(Request $request)
    {
        $sectionToOpen = $request->get('section', null);  // Obtener la sección desde la URL
    
        // Obtener los datos necesarios
        $user = Auth::user();
        $userID = $user->id;
        $direcciones = $this->obtenerDirecciones($userID);
        $direccion_facturacion = DB::table('users_address')
            ->where('user_id', $userID)
            ->where('facturacion', 1)
            ->first();
        $userData = DB::table('users_data')->where('user_id', $userID)->first();
    
        // Archivos de modales y acordeones
        $files_modal = File::allFiles(resource_path('views/partials/modal/cuenta'));
        $files_accordion = File::allFiles(resource_path('views/partials/accordion/cuenta'));
    
        $modal_files = new \stdClass();
        $accordion_files = new \stdClass();
    
        $i = 0;
        foreach($files_modal as $file)
        {
            $modal_files->$i = str_replace('.blade.php', '', $file->getRelativePathname());
            $i++;
        }
        $i = 0;
        foreach($files_accordion as $file)
        {
            $accordion_files->$i = str_replace('.blade.php', '', $file->getRelativePathname());
            $i++;
        }

        // Pasar los datos a la vista
        return view('cuenta', [
            'direcciones' => $direcciones,
            'direccion_facturacion' => $direccion_facturacion,
            'modal_files' => $modal_files,
            'accordion_files' => $accordion_files,
            'user' => $user,             
            'userData' => $userData,
            'sectionToOpen' => $sectionToOpen // Pasar la sección a la vista
        ]);
    }
    
    
    

    public function agregarDireccion(Request $request)
    {
        $user = Auth::user();
        $userID = $user->id;
        $nombre = $request->nombre;
        $calle = $request->calle;
        $no_int = $request->int;
        $no_ext = $request->ext;
        $entre_calles = $request->entrecalles;
        $colonia = $request->colonia;
        $municipio = $request->delegacion;
        $codigo_postal = $request->codigopostal;
        $estado = $request->estado;
        $pais = $request->pais;
        $referencias = $request->referencias;
        $cord_x = $request->cord_x;
        $cord_y = $request->cord_y;

        $direccion = DB::table('users_address')->where(['user_id' => $userID, 'nombre' => strtoupper(trim($nombre))])->get();

        
        if (count($direccion) > 0) {
            return back()->withErrors([
                'Nombre' => 'El nombre de la dirección ya está dado de alta. Por favor ingrese otro distinto.',
            ]);
        } 
        else 
        {
            
            DB::table('users_address')->insert([
                'user_id' => $userID,
                'nombre' => $nombre,
                'calle' => $calle,
                'no_int' => $no_int,
                'no_ext' => $no_ext,
                'entre_calles' => $entre_calles,
                'colonia' => $colonia,
                'municipio' => $municipio,
                'codigo_postal' => $codigo_postal,
                'estado' => $estado,
                'pais' => $pais,
                'referencias' => $referencias,
                'cord_x' => $cord_x,
                'cord_y' => $cord_y,
                'created_by' => $userID,
            ]);
        }

        return Redirect::To('/cuenta');
    }

    public function obtenerDirecciones($userID)
    {
        $direcciones = DB::table('users_address')->where(['user_id' => $userID, 'status' => '1'])->get();

        return $direcciones;
    }

    public function obtenerDireccion(Request $request)
    {
        $addressID = $request->id;

        $direccion = DB::table('users_address')->where(['id' => $addressID, 'status' => '1'])->get();

        return $direccion;
    }

    public function editarDireccion(Request $request)
    {

        $id = $request->id;

        $user = Auth::user();
        $userID = $user->id;

        $nombre = $request->nombre;
        $calle = $request->calle;
        $no_int = $request->int;
        $no_ext = $request->ext;
        $entre_calles = $request->entrecalles;
        $colonia = $request->colonia;
        $municipio = $request->delegacion;
        $codigo_postal = $request->codigopostal;
        $pais = $request->pais;
        $estado = $request->estado;
        $referencias = $request->referencias;
        $cord_x = $request->cord_x;
        $cord_y = $request->cord_y;
        $fecha = Carbon::now();
        $direccion = DB::table('users_address')->where(['id' => $id])->get();
        
        if (count($direccion) > 0) 
        {
            $upd = DB::table('users_address')
            ->where('id',$id)
            ->update([
                'nombre' => $nombre,
                'calle' => $calle,
                'no_int' => $no_int,
                'no_ext' => $no_ext,
                'entre_calles' => $entre_calles,
                'colonia' => $colonia,
                'municipio' => $municipio,
                'codigo_postal' => $codigo_postal,
                'estado' => $estado,
                'pais' => $pais,
                'referencias' => $referencias,
                'cord_x' => $cord_x,
                'cord_y' => $cord_y,
                'updated_by' => $userID,
                'updated_at' => $fecha->toDateTimeString()
            ]);
            
            if ($upd === false) {
                // En caso de un error con la consulta (fallo de SQL)
                return back()->withErrors(['error' => 'No se pudo guardar la dirección. Intente de nuevo más tarde.']);
            } elseif ($upd === 0) {
                // Si no se afectó ninguna fila, pero la consulta fue exitosa
                return back()->withErrors(['info' => 'No se realizaron cambios, ya que los datos son los mismos.']);
            }
        } 
        else 
        {
            return back()->withErrors(['error' => 'No se pudo actualizar la dirección.']);
        }

        return redirect()->route('cuenta')->with(['status'=>'Se actualizo correctamente la dirección.']);
    }

    public function eliminarDirecciones(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userID = $user->id;

            $addressID = $request->id;
            $direcciones = DB::table('users_address')->where(['user_id' => $userID, 'id' => $addressID])->delete();

            return response()->json(['message' => 'OK'], 200);
        }
    }

    public function completeAddress(Request $request)
    {
        $codigoPostal = $request->codigopostal;
        $direcciones = DB::table('codigos_postales')->where('codigo', $codigoPostal)->get();
        return response()->json($direcciones, 200);
    }
    public function setDireccionPredeterminada(Request $request)
    {
        $user = Auth::user();
        $direccionID = $request->direccion_predeterminada;
    
        // Marcar todas las direcciones como no predeterminadas
        DB::table('users_address')
            ->where('user_id', $user->id)
            ->update(['predeterminada' => 0]);
    
        // Marcar la dirección seleccionada como predeterminada
        DB::table('users_address')  
            ->where('id', $direccionID)
            ->where('user_id', $user->id)
            ->update(['predeterminada' => 1]);
    
        return response()->json(['message' => 'Dirección predeterminada actualizada correctamente'], 200);
    }
    public function setDireccionFacturacion(Request $request)
{
    $user = Auth::user();
    $direccionID = $request->direccion_facturacion;

    // Marcar todas las direcciones como no de facturación
    DB::table('users_address')
        ->where('user_id', $user->id)
        ->update(['facturacion' => 0]);

    // Marcar la dirección seleccionada como de facturación
    DB::table('users_address')  
        ->where('id', $direccionID)
        ->where('user_id', $user->id)
        ->update(['facturacion' => 1]);

    return response()->json(['message' => 'Dirección de facturación actualizada correctamente'], 200);
}

public function actualizarDatosFacturacion(Request $request)
{
    $user = Auth::user();
    $userID = $user->id;

    // Validar los datos de facturación
    $request->validate([
        'razon_social' => 'required|string|max:255',
        'rfc' => 'required|string|max:13',
        'regimen_fiscal' => 'required|string|max:255',
        'uso_cfdi' => 'required|string|max:255',
    ]);

    // Actualizar los datos de facturación en la tabla users_data
    DB::table('users_data')->updateOrInsert(
        ['user_id' => $userID],
        [
            'razon_social' => $request->razon_social,
            'rfc' => $request->rfc,
            'regimen_fiscal' => $request->regimen_fiscal,
            'uso_cfdi' => $request->uso_cfdi,
            'updated_at' => Carbon::now()
        ]
    );

    return redirect()->route('cuenta')->with('status', 'Datos de facturación actualizados correctamente.');
}

public function actualizarPromociones(Request $request)
{
    $user = Auth::user();
    $email = $user->email; // Usamos el email del usuario autenticado
    $ipAddress = $request->ip();
    $suscripcion = $request->input('recibir_promociones');

    if ($suscripcion == 1) {
        // Si selecciona "Sí", insertar o activar el registro en 'newsletter_subs'
        DB::table('newsletter_subs')->updateOrInsert(
            ['email' => $email],
            [
                'ip_address' => $ipAddress,
                'subscribed_at' => now(),
                'is_active' => 1
            ]
        );
    } else {
        // Si selecciona "No", desactivar la suscripción si ya existe
        $registro = DB::table('newsletter_subs')->where('email', $email)->first();

        if ($registro) {
            DB::table('newsletter_subs')->where('email', $email)->update(['is_active' => 0]);
        }
    }

    return redirect()->route('cuenta')->with('status', 'Preferencias de promociones actualizadas correctamente.');
}

}
