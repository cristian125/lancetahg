<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ClienteDistinguidoController extends Controller
{

        // Mostrar la lista de trámites de cliente distinguido
        public function show()
        {
            // Obtener los datos de la tabla cliente_distinguido
            $clientes = DB::table('cliente_distinguido')->get();
    
            // Retornar la vista y pasarle los datos
            return view('admin.cliente_distinguido', ['clientes' => $clientes]);
        }
    public function store(Request $request)
    {
        // Validar los campos del formulario
        $validator = Validator::make($request->all(), [
            'tratamiento' => 'required|in:Sr.,Sra.,Srta.',
            'nombre' => 'required|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u|max:255',
            'curp' => 'required|alpha_num|size:18',
            'telefono' => 'required|digits:10',
            'email' => 'required|email|max:255',
            'dia' => 'required|numeric|between:1,31',
            'mes' => 'required|in:Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre',
            'año' => 'required|numeric|min:1900|max:' . date('Y'),
            'direccion' => 'required|max:255',
            'codigo_postal' => 'required|digits:5',
            'estado' => 'required|max:255',
            'municipio' => 'required|max:255',
            'colonia' => 'required|max:255',
            // 'identificacion' y 'comprobante' eliminados
            'terminos' => 'accepted',
            'privacidad' => 'accepted',
        ]);

        if ($validator->fails()) {
            // Registrar errores de validación
            Log::error('Errores de validación:', $validator->errors()->toArray());

            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Combinar la fecha de nacimiento
        try {
            $fechaNacimiento = $request->input('año') . '-' . $this->getNumeroMes($request->input('mes')) . '-' . str_pad($request->input('dia'), 2, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            Log::error('Error al construir la fecha de nacimiento: ' . $e->getMessage());
            return response()->json(['error' => 'Error al procesar la fecha de nacimiento.'], 500);
        }

        // Insertar los datos en la tabla cliente_distinguido
        try {
            DB::table('cliente_distinguido')->insert([
                'tratamiento' => $request->input('tratamiento'),
                'nombre' => $request->input('nombre'),
                'curp' => $request->input('curp'),
                'fecha_nacimiento' => $fechaNacimiento,
                'telefono' => $request->input('telefono'),
                'email' => $request->input('email'),
                'direccion' => $request->input('direccion'),
                'codigo_postal' => $request->input('codigo_postal'),
                'estado' => $request->input('estado'),
                'municipio' => $request->input('municipio'),
                'colonia' => $request->input('colonia'),
                // 'identificacion' y 'comprobante' eliminados
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Solicitud enviada correctamente.'], 200);
        } catch (\Exception $e) {
            Log::error('Error al insertar en la base de datos: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo crear el registro.'], 500);
        }
    }

    private function getNumeroMes($mes)
    {
        $meses = [
            'Enero' => '01',
            'Febrero' => '02',
            'Marzo' => '03',
            'Abril' => '04',
            'Mayo' => '05',
            'Junio' => '06',
            'Julio' => '07',
            'Agosto' => '08',
            'Septiembre' => '09',
            'Octubre' => '10',
            'Noviembre' => '11',
            'Diciembre' => '12',
        ];

        if (!isset($meses[$mes])) {
            throw new \Exception('Mes inválido: ' . $mes);
        }

        return $meses[$mes];
    }
}
