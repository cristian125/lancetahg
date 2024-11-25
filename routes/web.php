<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminConfigController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDestacadosController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Api\GuiasController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClienteDistinguidoController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModalConfigController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaqueteExpressController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentLogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ProductosDestacadosController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ShippingCobrarController;
use App\Http\Controllers\ShippingLocalController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\ShippingPaqueteriaController;
use App\Http\Controllers\StorePickupController;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\UserOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

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


Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::group(['middleware' => ['web']], function () {
    Route::get('/paqueteruta', [PaqueteExpressController::class, 'getRequestCotizador'])->name('pq');


    Route::get('/', [ProductosDestacadosController::class, 'index'])->name('home');
    Route::any('/mantenimiento', [ProductosDestacadosController::class, 'maintenance'])->name('mantenimento');
    Route::get('/producto/{id}', [ProductController::class, 'show'])->name('producto.detalle');
    Route::get('/get-divisiones', [ProductController::class, 'getDivisiones']);
    Route::get('/get-categorias', [ProductController::class, 'getCategoriasConSubcategorias']);
    Route::get('/get-cart-items', [ProductController::class, 'getCartItems']);
    Route::get('/categorias/{division}/{categoriaProducto?}/{grupoMinorista?}', [ProductController::class, 'search']);
    Route::get('/get-divisiones', [ItemController::class, 'getDivisiones']);
    Route::get('/get-categorias/{divisionId}', [ItemController::class, 'getCategorias']);
    Route::get('/get-grupos-minoristas/{categoriaId}', [ItemController::class, 'getGruposMinoristas']);
    Route::get('/carousel', [CarouselController::class, 'index']);
    Route::post('/eliminar-imagen', [ItemController::class, 'eliminarImagen'])->name('eliminar.imagen');
    Route::post('/clientes-distinguido', [ClienteDistinguidoController::class, 'store'])->name('cliente.distinguido.store');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
    Route::post('/register', [RegisterController::class, 'register'])->name('register')->middleware('throttle:5,3');;
    Route::post('/login', [LoginController::class, 'login'])->name('login')->middleware('throttle:5,3');;
    Route::get('/producto/img/{id}', [ProductController::class, 'getImage'])->name('producto.imagen');
    Route::get('/ajax-search', [ProductController::class, 'ajaxSearch'])->name('ajax.search');
    Route::get('/search-result', [ProductController::class, 'search'])->name('product.search');
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
    //    Route::get('/categorias/{division}/{grupo}/{categoria}', [ProductController::class, 'searchAndDisplay']);
    Route::get('/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.email');
    Route::post('/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email1');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
    Route::get('/envio', function () {
        return view('envio');
    })->name('envios');
    Route::get('/coberturalocal', function () {
        return view('coberturalocal');
    })->name('coberturaloc');
    Route::get('/page/{slug}', [PageController::class, 'showPage'])->name('page.show');
    Route::get('/avisoprivacidad', [PageController::class, 'showPrivacyPolicy'])->name('avisoprivacidad');
    Route::get('/ayuda', function () {
        return view('ayuda');
    })->name('ayuda');
    Route::get('/formasdepago', function () {
        return view('formasdepago');
    })->name('formasdepago');
    Route::get('/preguntas-frecuentes', function () {
        return view('preguntasfrec');
    })->name('preguntasfrec');
    Route::get('/sucursales', function () {
        return view('sucursales');
    })->name('sucursales');
    Route::get('/ventatelefonica', function () {
        return view('ventatelefonica');
    })->name('ventatelefonica');
    Route::get('/proveedores', function () {
        return view('proveedores');
    })->name('proveedores');
    Route::get('/promociones', function () {
        return view('promociones');
    })->name('promociones');
    Route::get('/bolsa-de-trabajo', function () {
        return view('bolsadetrabajo');
    })->name('bolsadetrabajo');
    Route::get('/nosotros', function () {
        return view('nosotros');
    })->name('nosotros');
    Route::get('/clientedistinguido', function () {
        return view('tarjetaclientedis');
    })->name('clientedistinguido');
});

/***
 * Cuentas
 */
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/cuenta', [AccountController::class, 'index'])->name('cuenta');
    Route::post('/cuenta/direccion/agregar', [AccountController::class, 'agregarDireccion'])->name('cuenta.direccion.agregar');
    Route::post('/cuenta/direccion/editar', [AccountController::class, 'editarDireccion'])->name('cuenta.direccion.editar');
    Route::post('/cuenta/direccion/get', [AccountController::class, 'obtenerDireccion'])->name('cuenta.direccion.obtenerdireccion');
    Route::post('/cuenta/direccion/obtener', [AccountController::class, 'completeAddress'])->name('cuenta.direccion.obtener');
    Route::post('/cuenta/direccion/eliminar', [AccountController::class, 'eliminarDirecciones'])->name('cuenta.direccion.eliminar');
    Route::post('/cuenta/cambiar-contraseña', [RegisterController::class, 'change'])->name('cuenta.contraseña.actualizar');
    Route::post('/cuenta/direccion/facturacion', [AccountController::class, 'setDireccionFacturacion'])->name('cuenta.direccion.facturacion');
    Route::post('/cuenta/facturacion/actualizar', [AccountController::class, 'actualizarDatosFacturacion'])->name('cuenta.facturacion.actualizar');
    Route::post('/cuenta/promociones', [AccountController::class, 'actualizarPromociones'])->name('cuenta.promociones.actualizar');
    Route::post('/setDireccionPredeterminada', [AccountController::class, 'setDireccionPredeterminada']);
    Route::post('/setDireccionFacturacion', [AccountController::class, 'setDireccionFacturacion']);
});

/***
 * Usuario Autenticado
 */
Route::group(['middleware' => ['auth', 'web']], function () {
    Route::any('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/admin', [ProductosDestacadosController::class, 'index'])->name('admin');
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::any('/carrito', [ProductController::class, 'showCart'])->name('cart.show');
    Route::post('/cart/add-multiple', [ProductController::class, 'addMultipleToCart']);
    Route::post('/cart/check-stock', [ProductosDestacadosController::class, 'checkStock']);
    Route::post('/cart/update-quantity', [ProductController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::post('/cart/update', [ShippingLocalController::class, 'actualizarEnvio'])->name('cart.actualizarEnvio');
    Route::post('/cart/local-shipping/update', [ShippingLocalController::class, 'actualizarEnvio'])->name('cart.localShipping.update');
    Route::post('/cart/local-shipping/add', [ShippingLocalController::class, 'addShippingMethod'])->name('cart.localShipping.add');
    Route::post('/cart/shipment', [ShippingLocalController::class, 'addShippingMethod'])->name('cart.updateMethod1');
    Route::post('/cart/remove-shipping', [ProductController::class, 'removeShipping']);
    Route::post('/update-datos', [UserDataController::class, 'update'])->name('update.datos');
    Route::get('/verificar-existencias', [StorePickupController::class, 'ajaxVerificarExistencias'])->name('verificarExistencias');
    Route::get('/mis-pedidos', [UserOrderController::class, 'myOrders'])->name('myorders');
    Route::get('pedido/{orderId}', [UserOrderController::class, 'orderDetails'])->name('order.details');
    Route::get('/pedido/{orderId}/pdf', [UserOrderController::class, 'downloadOrderPdf'])->name('order.pdf');
    Route::post('/save-pickup-selection', function (Request $request) {
        $request->session()->put('selected_store_id', $request->input('store_id'));
        $request->session()->put('selected_pickup_date', $request->input('pickup_date'));
        $request->session()->put('selected_pickup_time', $request->input('pickup_time'));
        return response()->json(['success' => true]);
    })->name('save-pickup-selection');
    Route::post('/storepickup/save', [StorePickupController::class, 'saveStorePickup'])->name('storepickup.save');
    Route::post('/cart/proceed-to-payment', [CartController::class, 'proceedToPayment'])->name('cart.proceedToPayment');
    Route::post('/cart/actualizarEnvioPaqueteria', [ShippingPaqueteriaController::class, 'actualizarEnvioPaqueteria'])->name('cart.actualizarEnvioPaqueteria');
    Route::post('/cart/addPaqueteriaMethod', [ShippingPaqueteriaController::class, 'addPaqueteriaMethod'])->name('cart.addPaqueteriaMethod');
    Route::post('/cart/actualizar-envio-por-cobrar', [ShippingCobrarController::class, 'actualizarEnvio'])->name('cart.actualizarEnvioPorCobrar');
    Route::post('/cart/update-method', [ShippingCobrarController::class, 'addShippingMethod'])->name('cart.updateMethod');
    Route::get('/checkout', [CartController::class, 'showCheckout'])->name('checkout');
    Route::post('/update-payment-method', [CartController::class, 'updatePaymentMethod'])->name('update.payment.method');
    Route::post('/cart/process-cod', [CartController::class, 'processCod'])->name('process.cod');
    Route::get('/regimenes/{tipo_persona}', [AccountController::class, 'getRegimenesPorTipoPersona']);
    Route::get('/usos-cfdi/{regimen_fiscal_id}', [AccountController::class, 'getUsosCfdiPorRegimen']);
});
Route::group(['middleware' => ['auth', 'web']], function () {
    Route::post('/paqueteexpress/cotizar', [PaqueteExpressController::class, 'showRequestCotizador'])->name('paqueteexpress.cotizar');
    Route::post('/paqueteexpress/solicitar', [PaqueteExpressController::class, 'sendRequestCotizadorPaqueteExpress'])->name('paqueteexpress.solicitar');
});

/***
 * Fiserv
 */

//Route::get('/payment/callback/success', [PaymentController::class, 'handleSuccess'])->name('payment.callback.success');
//Route::get('/payment/callback/fail', [PaymentController::class, 'handleFail'])->name('payment.callback.fail');
Route::match(['get', 'post'], '/payment/callback/success', [PaymentController::class, 'handleSuccess'])->name('payment.callback.success');
Route::match(['get', 'post'], '/payment/callback/fail', [PaymentController::class, 'handleFail'])->name('payment.callback.fail');
Route::view('/payment/success', 'success')->name('payment.success');
// Route::any('/payment/fail', 'fail')->name('payment.fail');
Route::post('/process-order', [PaymentController::class, 'processOrder'])->name('process.order');

/***
 * Rutas del Administrador
 */
Route::prefix('adminlanz')->middleware('adminsession')->group(function () {
    Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register'])->name('admin.register.post');
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        Route::post('/subir-imagen-principal', [ItemController::class, 'subirImagenPrincipal'])->name('subir.imagen.principal');
        Route::post('/subir-imagen-secundaria', [ItemController::class, 'subirImagenSecundaria'])->name('subir.imagen.secundaria');
        Route::post('/hacer-imagen-principal', [ItemController::class, 'hacerImagenPrincipal'])->name('hacer.imagen.principal');
        Route::get('/admin/config', [AdminController::class, 'showChangePasswordForm'])->name('admin.config');
        Route::post('/admin/config/cambiar-password', [AdminController::class, 'changePassword'])->name('admin.changePassword');
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('admin.profile');
        Route::middleware('admin.role:superusuario')->group(function () {
            Route::get('/manage-admins', [AdminAuthController::class, 'manageAdmins'])->name('admin.manage.admins');
            Route::post('/update-admin-role/{id}', [AdminAuthController::class, 'updateAdminRole'])->name('admin.update.admin.role');
            Route::get('/shipping-methods', [ShippingMethodController::class, 'showShippingMethods'])->name('admin.shipping_methods');
            Route::post('/shipping-methods/update', [ShippingMethodController::class, 'updateShippingMethod'])->name('admin.shipping_methods.update');
            Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
            Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.showusers');
            Route::post('/users/{id}/change-password', [AdminUserController::class, 'changePassword'])->name('admin.users.changePassword');
            Route::delete('/users/{id}/delete', [AdminUserController::class, 'deleteUser'])->name('admin.users.delete');
            Route::post('/users/{id}/delete-carts', [AdminUserController::class, 'deleteCarts'])->name('admin.users.deleteCarts');
            Route::post('/users/{id}/delete-shipments', [AdminUserController::class, 'deleteShipments'])->name('admin.users.deleteShipments');
            Route::get('/admin/cliente-distinguido', [ClienteDistinguidoController::class, 'show'])->name('cliente.distinguido.show');
            Route::get('ordenes', [AdminOrderController::class, 'index'])->name('admin.orders.index');
            Route::get('ordenes/{orderId}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
            Route::get('/admin/pedido/{orderId}/pdf', [AdminOrderController::class, 'downloadOrderPdf'])->name('admin.order.pdf');
            Route::get('/newsletter', [NewsletterController::class, 'show'])->name('newsletter.show');
            Route::post('/newsletter/toggle/{id}', [NewsletterController::class, 'toggleSubscription'])->name('newsletter.toggle');
            Route::delete('/newsletter/{id}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        });

        Route::middleware('admin.role:superusuario,editor')->group(function () {
            Route::get('/items', [ItemController::class, 'index'])->name('admin.items.index');
            Route::get('/items/{id}/edit', [ItemController::class, 'edit'])->name('admin.items.edit');
            Route::put('/items/{id}', [ItemController::class, 'update'])->name('admin.items.update');
            Route::get('/pages', [EditorController::class, 'listPages'])->name('admin.pages.list');
            Route::get('/pages/create', [EditorController::class, 'createPage'])->name('admin.pages.create');
            Route::post('/pages', [EditorController::class, 'storePage'])->name('admin.pages.store');
            Route::delete('/pages/{id}', [EditorController::class, 'deletePage'])->name('admin.pages.delete');
            Route::get('/editor/{id}', [EditorController::class, 'showEditor'])->name('admin.editor');
            Route::put('/editor/{id}', [EditorController::class, 'saveContent'])->name('admin.editor.save');
            Route::get('/footer-links', [App\Http\Controllers\Admin\FooterAdminController::class, 'index'])->name('footer_links.index');
            Route::get('/footer-links/create', [App\Http\Controllers\Admin\FooterAdminController::class, 'create'])->name('footer_links.create');
            Route::post('/footer-links', [App\Http\Controllers\Admin\FooterAdminController::class, 'store'])->name('footer_links.store');
            Route::get('/footer-links/{id}/edit', [App\Http\Controllers\Admin\FooterAdminController::class, 'edit'])->name('footer_links.edit');
            Route::put('/footer-links/{id}', [App\Http\Controllers\Admin\FooterAdminController::class, 'update'])->name('footer_links.update');
            Route::delete('/footer-links/{id}', [App\Http\Controllers\Admin\FooterAdminController::class, 'destroy'])->name('footer_links.destroy');
            Route::get('/carousel', [CarouselController::class, 'index'])->name('admin.carousel_settings');
            Route::post('/carousel', [CarouselController::class, 'updateCarouselImages'])->name('admin.update_carousel');
            Route::delete('/carousel/{id}', [CarouselController::class, 'deleteCarouselImage'])->name('admin.delete_carousel_image');
            Route::patch('/carousel/{id}/toggle', [CarouselController::class, 'toggleCarouselImage'])->name('admin.toggle_carousel_image');
            Route::patch('/carousel/{id}/update-link', [CarouselController::class, 'updateCarouselImageLink'])->name('admin.update_carousel_image_link');
            Route::post('/carousel/update-order', [CarouselController::class, 'updateCarouselOrder'])->name('admin.update_carousel_order');
            Route::get('/grid', [CarouselController::class, 'indexGrid'])->name('admin.grid_settings');
            Route::post('/grid', [CarouselController::class, 'updateGridImages'])->name('admin.update_grid');
            Route::patch('/grid/{id}', [CarouselController::class, 'toggleGridImage'])->name('admin.toggle_grid_image');
            Route::delete('/grid/{id}', [CarouselController::class, 'deleteGridImage'])->name('admin.delete_grid_image');
            Route::get('/banner', [BannerController::class, 'index'])->name('admin.banner_settings');
            Route::post('/banner/upload', [BannerController::class, 'uploadBanner'])->name('admin.upload_banner');
            Route::patch('/banner/{id}/toggle', [BannerController::class, 'toggleBanner'])->name('admin.toggle_banner');
            Route::delete('/banner/{id}', [BannerController::class, 'deleteBanner'])->name('admin.delete_banner');
            Route::get('/destacados', [AdminDestacadosController::class, 'showDestacadosForm'])->name('admin.destacados.form');
            Route::post('/destacados/guardar', [AdminDestacadosController::class, 'guardarDestacados'])->name('admin.destacados.guardar');
            Route::get('/modal-config', [ModalConfigController::class, 'showModalConfig'])->name('admin.modal_config');
            Route::post('/modal-config/save', [ModalConfigController::class, 'saveModalConfig'])->name('admin.modal_config.save');
            Route::match(['get', 'post'], '/config', [AdminConfigController::class, 'configurarMantenimiento'])->name('configadmin');
            Route::get('/payment-logs', [PaymentLogController::class, 'index'])->name('admin.payment_logs.index');
            Route::get('/payment-logs/{id}', [PaymentLogController::class, 'show'])->name('admin.payment_logs.show');
            Route::get('/items-data', [ProductImportController::class, 'showItemsData'])->name('admin.itemsData');
            Route::get('/fetch-items', [ProductImportController::class, 'fetchItemsManually'])->name('admin.fetchItems');
            Route::get('/fetch-guias', [GuiasController::class, 'guiasearch'])->name('admin.fetchGuias');
        });
    });
});

