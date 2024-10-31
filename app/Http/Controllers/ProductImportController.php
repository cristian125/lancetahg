<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\DB;

class ProductImportController extends Controller
{
    public function showItemsData(Request $request)
    {
        $logs = DB::table('api_import_logs')->orderBy('request_time', 'desc')->paginate(10); 


        if ($request->ajax()) {
            $tableHtml = view('admin.partials.logs_table', compact('logs'))->render();
            $paginationHtml = $logs->links('pagination::bootstrap-4')->render();

            return response()->json(['tableHtml' => $tableHtml, 'paginationHtml' => $paginationHtml]);
        }

        return view('admin.items_data', compact('logs'));
    }



    public function fetchItemsManually(Request $request, ProductController $productController)
    {
        // Añadir api_key a la request manualmente
        $request->merge(['api_key' => env('EXTERNAL_API_KEY')]);

        return $productController->fetchItems($request);
    }

}
