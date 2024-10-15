<?php




namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $email = $request->input('email');
        $ip_address = $request->input('ip_address');

        // Verificar si el correo ya existe
        $exists = DB::table('newsletter_subs')
            ->where('email', $email)
            ->exists();

        // Si no existe, insertarlo
        if (!$exists) {
            DB::table('newsletter_subs')->insert([
                'email' => $email,
                'ip_address' => $ip_address,
                'subscribed_at' => now(),
            ]);
        }

        // Devolver respuesta de éxito aunque no se haya insertado
        return response()->json(['message' => 'Suscripción exitosa'], 200);
    }


    // Mostrar lista de suscriptores
    public function show()
    {
        // Obtener todas las suscripciones desde la tabla `newsletter_subs`
        $suscriptores = DB::table('newsletter_subs')->get();

        return view('admin.newsletter', compact('suscriptores'));
    }

    // Cambiar estado de una suscripción (activar/desactivar)
    public function toggleSubscription($id)
    {
        $subscriber = DB::table('newsletter_subs')->where('id', $id)->first();
        if ($subscriber) {
            $newStatus = $subscriber->is_active ? 0 : 1;

            // Actualizar el estado de la suscripción
            DB::table('newsletter_subs')->where('id', $id)->update(['is_active' => $newStatus]);

            return redirect()->route('newsletter.show')->with('success', 'Estado de la suscripción actualizado correctamente.');
        }

        return redirect()->route('newsletter.show')->with('error', 'Suscriptor no encontrado.');
    }

    // Eliminar suscripción
    public function destroy($id)
    {
        DB::table('newsletter_subs')->where('id', $id)->delete();
        return redirect()->route('newsletter.show')->with('success', 'Suscriptor eliminado correctamente.');
    }
}
