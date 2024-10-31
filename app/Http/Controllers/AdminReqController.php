<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\DB;

class ProductImportController extends Controller
{
    public function showItemsData()
    {
        // Cambia 'get' a 'paginate' para habilitar la paginación
        $logs = DB::table('api_import_logs')->orderBy('request_time', 'desc')->paginate(5); // Ajusta el número de registros por página
        return view('admin.items_data', compact('logs'));
    }
    
    

    public function fetchItemsManually(Request $request, ProductController $productController)
    {
        // Llamar al método fetchItems del ProductController
        return $productController->fetchItems($request);
    }
}
