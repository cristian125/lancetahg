<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use App\Http\Controllers\ShippingLocalController;
use App\Http\Controllers\ProductosDestacadosController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController; 
use App\Http\Controllers\AccountController; 
use App\Http\Controllers\StorePickupController;
use App\Http\Controllers\PaqueteExpressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShippingPaqueteriaController;


use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/***
 * Usuario sin autenticar
 */
Route::fallback(function(){ return response()->view('errors.404', [], 404); });

Route::group(['middleware' => ['web']], function() {
    Route::get('/', [ProductosDestacadosController::class, 'index'])->name('home');
    
    Route::get('/producto/{id}', [ProductController::class, 'show'])->name('producto.detalle');

   
    Route::get('/get-divisiones', [ProductController::class, 'getDivisiones']);
    Route::get('/get-categorias', [ProductController::class, 'getCategoriasConSubcategorias']);
    Route::get('/get-cart-items', [ProductController::class, 'getCartItems']);
    Route::get('/categorias/{division}/{categoriaProducto?}/{grupoMinorista?}', [ProductController::class, 'search']);


    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    // Rutas para el login
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/producto/img/{id}', [ProductController::class, 'getImage'])->name('producto.imagen');
    Route::get('/ajax-search', [ProductController::class, 'ajaxSearch'])->name('ajax.search');
    Route::get('/search-result', [ProductController::class, 'search'])->name('product.search');
    

//    Route::get('/categorias/{division}/{grupo}/{categoria}', [ProductController::class, 'searchAndDisplay']);


    
    // Rutas Function
    Route::get('/envio', function(){
        return view('envio');
    })->name('envios');

 

    Route::get('/coberturalocal', function(){
        return view('coberturalocal');
    })->name('coberturaloc');
    
    Route::get('/termycond', function(){
        return view('termycond');
    })->name('terminosyc');
    
    Route::get('/avisoprivacidad', function(){
        return view('avisopriv');
    })->name('avisoprivacidad');

    Route::get('/formasdepago', function(){
        return view('formasdepago');
    })->name('formasdepago');
    
    Route::get('/preguntas-frecuentes', function(){
        return view('preguntasfrec');
    })->name('preguntasfrec');

    Route::get('/sucursales', function(){
        return view('sucursales');
    })->name('sucursales');
    
    Route::get('/ventatelefonica', function(){
        return view('ventatelefonica');
    })->name('ventatelefonica');

    Route::get('/proveedores', function(){
        return view('proveedores');
    })->name('proveedores');

    Route::get('/promociones', function(){
        return view('promociones');
    })->name('promociones');

    Route::get('/bolsa-de-trabajo', function(){
        return view('bolsadetrabajo');
    })->name('bolsadetrabajo');

    Route::get('/nosotros', function(){
        return view('nosotros');
    })->name('nosotros');

    Route::get('/clientedistinguido', function(){
        return view('tarjetaclientedis');
    })->name('clientedistinguido');
});

/***
 * Cuentas
 */
Route::group(['middleware'=>['web','auth']],function(){
    Route::get('/cuenta', [AccountController::class,'index'])->name('cuenta');
    Route::post('/cuenta/direccion/agregar', [AccountController::class,'agregarDireccion'])->name('cuenta.direccion.agregar');
    Route::post('/cuenta/direccion/editar', [AccountController::class,'editarDireccion'])->name('cuenta.direccion.editar');
    Route::post('/cuenta/direccion/get', [AccountController::class,'obtenerDireccion'])->name('cuenta.direccion.obtenerdireccion');
    Route::post('/cuenta/direccion/obtener', [AccountController::class,'completeAddress'])->name('cuenta.direccion.obtener');
    Route::post('/cuenta/direccion/eliminar', [AccountController::class,'eliminarDirecciones'])->name('cuenta.direccion.eliminar');
    Route::post('/cuenta/cambiar-contraseña', [RegisterController::class,'change'])->name('cuenta.contraseña.actualizar');
});

/***
 * Usuario Autenticado
 */
Route::group(['middleware' => ['auth', 'web']], function() {
    Route::any('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/admin', [ProductosDestacadosController::class, 'index'])->name('admin');
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/carrito', [ProductController::class, 'showCart'])->name('cart.show');
    Route::post('/cart/add-multiple', [ProductController::class, 'addMultipleToCart']);
    Route::post('/cart/check-stock', [ProductosDestacadosController::class, 'checkStock']);
    Route::post('/cart/update-quantity', [ProductController::class, 'reduceQuantity'])->name('cart.reduceQuantity'); 
    Route::post('/cart/update', [ShippingLocalController::class, 'actualizarEnvio'])->name('cart.actualizarEnvio');
    Route::post('/cart/shipment', [ShippingLocalController::class, 'addShippingMethod'])->name('cart.updateMethod');
    Route::post('/cart/remove-shipping', [ProductController::class, 'removeShipping']);

    Route::post('/save-pickup-selection', function(Request $request) {
        $request->session()->put('selected_store_id', $request->input('store_id'));
        $request->session()->put('selected_pickup_date', $request->input('pickup_date'));
        $request->session()->put('selected_pickup_time', $request->input('pickup_time'));
    
        return response()->json(['success' => true]);
    })->name('save-pickup-selection');
    
    Route::post('/storepickup/save', [StorePickupController::class, 'saveStorePickup'])->name('storepickup.save');

    Route::post('/cart/proceed-to-payment', [CartController::class, 'proceedToPayment'])->name('cart.proceedToPayment');
    Route::post('/cart/actualizarEnvioPaqueteria', [ShippingPaqueteriaController::class, 'actualizarEnvioPaqueteria'])->name('cart.actualizarEnvioPaqueteria');
    Route::post('/cart/addPaqueteriaMethod', [ShippingPaqueteriaController::class, 'addPaqueteriaMethod'])->name('cart.addPaqueteriaMethod');


    Route::get('/checkout', function () {
        return view('checkout');
    })->name('checkout');








});

/***
 * PAQUETE EXPRESS ROUTES
*/
Route::group(['middleware' => ['auth', 'web']], function() 
{
    Route::post('/paqueteexpress/cotizar',[PaqueteExpressController::class,'showRequestCotizador'])->name('paqueteexpress.cotizar');
    Route::post('/paqueteexpress/solicitar',[PaqueteExpressController::class,'sendRequestCotizadorPaqueteExpress'])->name('paqueteexpress.solicitar');
});