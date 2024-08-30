<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductosDestacadosController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController; 
use App\Http\Controllers\AccountController; 
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
    Route::get('/search', [ProductController::class, 'search'])->name('product.search');
    Route::get('/get-divisiones', [ProductController::class, 'getDivisiones']);
    Route::get('/get-categorias', [ProductController::class, 'getCategoriasConSubcategorias']);
    Route::get('/get-cart-items', [ProductController::class, 'getCartItems']);
    Route::get('/categorias/{division}/{categoriaProducto}/{grupoMinorista}', [ProductController::class, 'getProductsByCategory']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    // Rutas para el login
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/producto/img/{id}', [ProductController::class, 'getImage'])->name('producto.imagen');
    Route::get('/search-result', [ProductController::class, 'searchAndDisplay'])->name('product.searchAndDisplay');

    Route::get('/categorias/{division}/{grupo}/{categoria}', [ProductController::class, 'searchAndDisplay']);
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

});

/***
 * Cuentas
 */
Route::group(['middleware'=>['web','auth']],function(){
    Route::get('/cuenta', [AccountController::class,'index'])->name('cuenta');
    Route::post('/cuenta/agregar', [AccountController::class,'agregarDireccion'])->name('cuenta.agregar');
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


});

