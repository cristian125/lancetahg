<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GuiasController extends Controller
{
    public function guiasearch(Request $request)
    {
        // Verificar que la API key proporcionada coincida con EXTERNAL_API_KEY en .env
        $apiKey = $request->input('api_key') ?? $request->header('x-api-key');
        if ($apiKey !== env('EXTERNAL_API_KEY')) {
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'failed',
                'message' => 'Unauthorized access attempt',
                'error_details' => 'Invalid API key provided'
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Consultar la API externa usando el token desde el .env
        $response = Http::get('http://app.lancetahg.com/api/lancetaweb', [
            'accion' => 'GUIAS',
            'token' => env('EXTERNAL_API_TOKEN')
        ]);

        // Verificar si la consulta fue exitosa
        if ($response->successful()) {
            $guiasData = $response->json();

            // Verificar si la respuesta es un array para evitar errores
            if (!is_array($guiasData)) {
                DB::table('api_import_logs')->insert([
                    'request_time' => now(),
                    'status' => 'failed',
                    'message' => 'Invalid data format from API',
                    'error_details' => 'The response from the API was not an array'
                ]);
                return response()->json(['error' => 'Invalid data format from API'], 500);
            }

            foreach ($guiasData as $guia) {
                // Extraer los datos de la API
                $noFacr = $guia['No_'];
                $orderNo = $guia['Order No_'];
                $noGuia = $guia['No_ Guía'];

                // Verificar si el registro ya existe en la base de datos
                $existingGuia = DB::table('guias')->where('order_no', $orderNo)->first();

                if ($existingGuia) {
                    // Si ya existe, actualizamos los datos de `no_guia` y `no_facr`
                    DB::table('guias')
                        ->where('order_no', $orderNo)
                        ->update([
                            'no_facr' => $noFacr,
                            'no_guia' => $noGuia,
                            'updated_at' => now()
                        ]);
                } else {
                    // Si no existe, insertamos un nuevo registro
                    DB::table('guias')->insert([
                        'no_facr' => $noFacr,
                        'order_no' => $orderNo,
                        'no_guia' => $noGuia,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Registrar el log de éxito
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'success',
                'message' => 'Datos de guías procesados correctamente.',
                'error_details' => null
            ]);

            return response()->json(['message' => 'Datos de guías procesados correctamente.']);
        } else {
            // Registrar el log de error si la consulta falla
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'failed',
                'message' => 'No se pudo obtener los datos de la API externa.',
                'error_details' => 'Error ' . $response->status() . ': ' . $response->body()
            ]);

            return response()->json(['error' => 'No se pudo obtener los datos de la API externa.'], 500);
        }
    }


    public function statusUpdate(Request $request)
    {
        // Verificar que la API key proporcionada coincida con EXTERNAL_API_KEY en .env
        $apiKey = $request->input('api_key') ?? $request->header('x-api-key');
        if ($apiKey !== env('EXTERNAL_API_KEY')) {
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'failed',
                'message' => 'Unauthorized access attempt',
                'error_details' => 'Invalid API key provided'
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Consultar la API externa usando el token desde el .env
        $response = Http::get('http://app.lancetahg.com/api/lancetaweb', [
            'accion' => 'STATUS',
            'token' => env('EXTERNAL_API_TOKEN')
        ]);

        // Verificar si la consulta fue exitosa
        if ($response->successful()) {
            $statusData = $response->json();

            // Verificar si la respuesta es un array para evitar errores
            if (!is_array($statusData)) {
                DB::table('api_import_logs')->insert([
                    'request_time' => now(),
                    'status' => 'failed',
                    'message' => 'Invalid data format from API',
                    'error_details' => 'The response from the API was not an array'
                ]);
                return response()->json(['error' => 'Invalid data format from API'], 500);
            }

            foreach ($statusData as $status) {
                // Extraer los datos de la API
                $orderNo = $status['Order No_'];
                $type = $status['Type'];

                // Verificar si el registro ya existe en la base de datos
                $existingGuia = DB::table('guias')->where('order_no', $orderNo)->first();

                if ($existingGuia) {
                    // Si ya existe, actualizamos el campo 'type'
                    DB::table('guias')
                        ->where('order_no', $orderNo)
                        ->update([
                            'type' => $type,
                            'updated_at' => now()
                        ]);
                } else {
                    // Si no existe, insertamos un nuevo registro con 'order_no' y 'type'
                    DB::table('guias')->insert([
                        'order_no' => $orderNo,
                        'type' => $type,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Registrar el log de éxito
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'success',
                'message' => 'Datos de estado de órdenes procesados correctamente.',
                'error_details' => null
            ]);

            return response()->json(['message' => 'Datos de estado de órdenes procesados correctamente.']);
        } else {
            // Registrar el log de error si la consulta falla
            DB::table('api_import_logs')->insert([
                'request_time' => now(),
                'status' => 'failed',
                'message' => 'No se pudo obtener los datos de la API externa.',
                'error_details' => 'Error ' . $response->status() . ': ' . $response->body()
            ]);

            return response()->json(['error' => 'No se pudo obtener los datos de la API externa.'], 500);
        }
    }
}
