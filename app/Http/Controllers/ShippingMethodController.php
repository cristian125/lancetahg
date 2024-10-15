<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingMethodController extends Controller
{
    public function showShippingMethods()
    {
        // Obtener los métodos de envío
        $shippingMethods = DB::table('shipping_methods')->get();

        return view('admin.shipping_methods', [
            'shippingMethods' => $shippingMethods,
        ]);
    }

    public function updateShippingMethod(Request $request)
    {
        $methodId = $request->input('method_id');
        $isActive = $request->input('is_active') ? 1 : 0;

        // Actualizar el estado del método de envío
        DB::table('shipping_methods')
            ->where('id', $methodId)
            ->update(['is_active' => $isActive]);

        // Opcional: Actualizar el estado de los shippments asociados
        if (!$isActive) {
            $methodName = DB::table('shipping_methods')
                ->where('id', $methodId)
                ->value('name');

            // Actualizar el estado de los shippments a 'cancelled'
            DB::table('shippments')
                ->where('shipping_method', $methodName)
                ->update(['status' => 'cancelled']);
        }

        return redirect()->route('admin.shipping_methods')->with('success', 'Método de envío actualizado correctamente.');
    }
}
