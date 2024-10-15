<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckAdminRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        // Obtener el ID del administrador autenticado
        $adminId = Auth::guard('admin')->id();

        if (!$adminId) {
            // Si no está autenticado, redirigir al login
            return redirect()->route('admin.login');
        }

        // Obtener el rol del administrador desde la base de datos
        $adminRole = DB::table('admins')->where('id', $adminId)->value('role');


if (!$adminRole || !in_array($adminRole, $roles)) {
    return redirect()->route('admin.dashboard')->withErrors('No tienes permisos para acceder a esta sección.');
}


        return $next($request);
    }
}
