<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\DB;

class ProductImportController extends Controller
{
    public function showItemsData()
    {

        $logs = DB::table('api_import_logs')->orderBy('request_time', 'desc')->paginate(5); 
        return view('admin.items_data', compact('logs'));
    }



    public function fetchItemsManually(Request $request,ProductController $productController)
    {
        return $productController->fetchItems($request);
    }
}
