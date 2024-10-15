<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class AdminSession
{
    public function handle($request, Closure $next)
    {
        // Cambiar el nombre de la cookie de sesión para el admin
        Config::set('session.cookie', Config::get('session.cookie') . '_admin');

        return $next($request);
    }
}
