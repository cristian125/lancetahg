<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class BasicAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $username = '7CQUS5MSDJ9NIGHN8DEWGAZ68LKSQTBB'; // Cambia esto por el nombre de usuario
        $password = ''; // La contraseña proporcionada
        DB::table('req')->insert(['request'=>$request]);
        // Verifica la autenticación básica
        // if ($request->getUser() != $username || $request->getPassword() != $password) {
        if ($request->getUser() != $username ) {
            return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic']);
        }

        return $next($request);
    }



    
}
