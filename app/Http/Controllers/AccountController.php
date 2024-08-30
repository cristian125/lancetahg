<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class AccountController extends Controller
{
    function index(Request $request)
    {
        $user = Auth::user();
        $userID = $user->id;
        $user->address = $request->input('address');
        
        $direcciones = DB::table('users_address')->where('id',$userID)->get();

        return response()->view('cuenta',['direcciones'=>$direcciones]);
    }

    function agregarDireccion(Request $request)
    {
        $responseok = ['messaje'=>'OK'];
        return response()->json(json_encode($responseok),200);
    }

    function completeAddress(Request $request)
    {
        $codigoPostal = $request->codigopostal;
        $direcciones = DB::table('codigos_postales')->where('codigo',$codigoPostal)->get();
        return response()->json($direcciones,200);
    }
}