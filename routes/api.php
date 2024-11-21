<?php

use App\Http\Controllers\Api\OrderDetailsController;
use App\Http\Controllers\Api\OrdersHeaderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\GuiasController;
use App\Http\Controllers\OrderController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
// Ruta para los headers de las órdenes (estado 2)
Route::get('/orders', [OrdersHeaderController::class, 'showOrdersWithState']);
Route::get('/order_details', [OrdersHeaderController::class, 'showOrderDetails'])->name('order.details');
// Route::group(['middleware' => 'auth.basic1'], function () {
    
    
// });

// Route::get('/orders', [OrdersHeaderController::class, 'SendOrderstoBC']);
// Route::get('/orders', [OrdersHeaderController::class, 'showOrdersWithState'])->middleware('auth.basic')->name('api.orders');

// Ruta para los detalles de una orden específica
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/fetch-items', [ProductController::class, 'fetchItems']);

Route::get('/addresses', [UserAddressController::class, 'getAllUserAddresses']);


Route::get('/status-update', [GuiasController::class, 'statusUpdate'])->name('status.update');

Route::get('/guias', [GuiasController::class, 'guiasearch']);


Route::get('/storepickup', [OrderController::class, 'getStorePickupOrderHeaders']);
Route::get('/storepickup/{orderNumber}', [OrderController::class, 'getOrderItemsByOrderNumber']);

Route::post('/update-delivery', [OrderController::class, 'updateDelivery'])->name('update.delivery');
