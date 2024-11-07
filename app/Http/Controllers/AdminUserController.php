<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class AdminUserController extends Controller
{
    // Método para listar usuarios con búsqueda y paginación
    public function index(Request $request)
    {
        $query = DB::table('users')
            ->leftJoin('users_data', 'users.id', '=', 'users_data.user_id')
            ->select('users.*', 'users_data.nombre', 'users_data.apellido_paterno', 'users_data.apellido_materno', 'users_data.telefono');

        if ($request->has('id') && !empty($request->id)) {
            $query->where('users.id', $request->id);
        }
        if ($request->has('nombre') && !empty($request->nombre)) {
            $query->where('users_data.nombre', 'LIKE', '%' . $request->nombre . '%');
        }
        if ($request->has('apellido') && !empty($request->apellido)) {
            $query->where(function($q) use ($request) {
                $q->where('users_data.apellido_paterno', 'LIKE', '%' . $request->apellido . '%')
                  ->orWhere('users_data.apellido_materno', 'LIKE', '%' . $request->apellido . '%');
            });
        }
        if ($request->has('email') && !empty($request->email)) {
            $query->where('users.email', 'LIKE', '%' . $request->email . '%');
        }
        if ($request->has('telefono') && !empty($request->telefono)) {
            $query->where('users_data.telefono', 'LIKE', '%' . $request->telefono . '%');
        }


        $usuarios = $query->orderBy('users.id', 'desc')->paginate(10);
        $usuarios->appends($request->all());

        if ($request->ajax()) {
            return view('admin.users_table', compact('usuarios'))->render();
        }

        return view('admin.users', compact('usuarios'));
    }
    public function show($id)
    {
        $usuario = DB::select('SELECT * FROM users WHERE id = ?', [$id]);
    
        if (!$usuario) {
            return redirect()->route('admin.users')->withErrors('Usuario no encontrado.');
        }
    
        $usuario = $usuario[0]; 
        $usuario_data = DB::select('SELECT * FROM users_data WHERE user_id = ?', [$id]);
    
        if ($usuario_data) {
            $usuario_data = $usuario_data[0];
            $usuario->nombre = $usuario_data->nombre;
            $usuario->apellido_paterno = $usuario_data->apellido_paterno;
            $usuario->apellido_materno = $usuario_data->apellido_materno;
            $usuario->telefono = $usuario_data->telefono;
            $usuario->tratamiento = $usuario_data->tratamiento;
            $usuario->correo = $usuario_data->correo;
        }
    
        $direcciones = DB::select('SELECT * FROM users_address WHERE user_id = ?', [$id]);
        $carts = DB::select('SELECT * FROM carts WHERE user_id = ?', [$id]);
        $cart_items = [];
        foreach ($carts as $cart) {
            $items = DB::select('SELECT * FROM cart_items WHERE cart_id = ?', [$cart->id]);
            $cart_items[$cart->id] = $items;
        }
    
        $envios = DB::select('SELECT * FROM cart_shippment WHERE cart_id IN (SELECT id FROM carts WHERE user_id = ?)', [$id]);
        $orders = DB::select('
            SELECT o.*, 
                   cs.ShipmentMethod, 
                   cs.shippingcost_IVA, 
                   cs.calle, 
                   cs.no_ext, 
                   cs.no_int, 
                   cs.colonia, 
                   cs.municipio, 
                   cs.codigo_postal, 
                   cs.pais 
            FROM orders o
            LEFT JOIN carts c ON o.user_id = c.user_id
            LEFT JOIN cart_shippment cs ON c.id = cs.cart_id
            WHERE o.user_id = ?', [$id]);
    
        $order_items = [];
        foreach ($orders as $order) {
            $items = DB::select('SELECT * FROM order_items WHERE order_id = ?', [$order->id]);
            $order_items[$order->id] = $items;
        }
    
        return view('admin.showusers', compact('usuario', 'direcciones', 'carts', 'cart_items', 'envios', 'orders', 'order_items'));
    }
    
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
            'confirm_password' => 'required|same:new_password',
        ]);

        DB::table('users')->where('id', $id)->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.showusers', $id)->with('success', 'Contraseña actualizada correctamente.');
    }

    public function deleteUser($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return redirect()->route('admin.users')->with('success', 'Cuenta eliminada correctamente.');
    }

    public function deleteCarts($id)
    {
        DB::table('carts')->where('user_id', $id)->delete();
        return redirect()->route('admin.showusers', $id)->with('success', 'Carritos eliminados correctamente.');
    }

    public function deleteShipments($id)
    {
        DB::table('shippments')->where('user_id', $id)->delete();
        return redirect()->route('admin.showusers', $id)->with('success', 'Envíos eliminados correctamente.');
    }
}
