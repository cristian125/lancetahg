<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;






class ItemController extends Controller
{
    public function index(Request $request)
    {
        // Captura del término de búsqueda
        $search = $request->input('search');
    
        // Consulta SQL para obtener los items únicos con cantidad disponible
        $items = DB::table('itemsdb')
            ->leftJoin('inventario', 'itemsdb.no_s', '=', 'inventario.no_s') // Unión con inventario
            ->leftJoin('producto_atributo', 'itemsdb.id', '=', 'producto_atributo.producto_id')
            ->leftJoin('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
            ->leftJoin('grupos', 'atributos.grupo_id', '=', 'grupos.id')
            ->select(
                'itemsdb.id',
                'itemsdb.no_s',
                'itemsdb.nombre',
                'itemsdb.precio_unitario',
                DB::raw('GROUP_CONCAT(DISTINCT grupos.id) as grupos_ids'), // Combinar grupos
                'inventario.cantidad_disponible',  // Existencia actual
                'itemsdb.activo'
            )
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('itemsdb.nombre', 'like', '%' . $search . '%')
                        ->orWhere('itemsdb.no_s', 'like', '%' . $search . '%')
                        ->orWhere('grupos.id', 'like', '%' . $search . '%');
                });
            })
            ->groupBy(
                'itemsdb.id',
                'itemsdb.no_s',
                'itemsdb.nombre',
                'itemsdb.precio_unitario',
                'inventario.cantidad_disponible',
                'itemsdb.activo'
            )
            ->paginate(15);
    
        // Devolver la vista con los datos
        return view('admin.items_list', compact('items', 'search'));
    }
    

    
    public function edit($id)
    {
        // Consulta SQL para obtener el item por ID
        $item = DB::table('itemsdb')->where('id', $id)->first();

        // Verificamos si existe el item
        if (!$item) {
            return redirect()->route('admin.items.index')->withErrors('Item no encontrado.');
        }
        // Obtener todos los grupos existentes
        $grupos = DB::table('grupos')->select('id', 'descripcion')->get();

        // Obtener el grupo actual del producto (suponiendo que un producto solo tiene atributos de un grupo)
        $grupoActual = DB::table('producto_atributo')
            ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
            ->join('grupos', 'atributos.grupo_id', '=', 'grupos.id')
            ->where('producto_atributo.producto_id', $item->id)
            ->select('grupos.id', 'grupos.descripcion')
            ->first();

        // Obtener todos los atributos agrupados por grupo
        $atributos = DB::table('atributos')
            ->join('grupos', 'atributos.grupo_id', '=', 'grupos.id')
            ->select('atributos.*', 'grupos.descripcion as grupo_descripcion')
            ->get()
            ->groupBy('grupo_descripcion');
                // Obtener los productos agrupados por grupo, excluyendo el producto actual
    $productosPorGrupo = DB::table('itemsdb')
    ->join('producto_atributo', 'itemsdb.id', '=', 'producto_atributo.producto_id')
    ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
    ->join('grupos', 'atributos.grupo_id', '=', 'grupos.id')
    ->where('itemsdb.id', '!=', $id) // Excluir el producto actual
    ->select(
        'grupos.descripcion as grupo_descripcion',
        'grupos.id as grupo_id',
        'itemsdb.id as item_id',
        'itemsdb.no_s',
        'itemsdb.nombre',
        'itemsdb.precio_unitario',
        'itemsdb.activo'
    )
    ->distinct()
    ->get()
    ->groupBy('grupo_id');

    // Obtener los IDs de los atributos asignados al producto
    $atributosProducto = DB::table('producto_atributo')
        ->where('producto_id', $id)
        ->pluck('atributo_id')
        ->toArray();
        // Obtener los proveedores
        $proveedores = DB::table('proveedores')
            ->select('no_', 'nombre')
            ->orderBy('nombre')
            ->get();

        // Obtener la división seleccionada del item
        $divisionSeleccionada = DB::table('divisiones')
            ->where('codigo_division', $item->cod_division)
            ->first();

        // Obtener la categoría seleccionada del item
        $categoriaSeleccionada = DB::table('categorias_divisiones')
            ->where('cod_categoria_producto', $item->cod_categoria_producto)
            ->first();

        // Obtener el grupo minorista seleccionado del item
        $grupoMinoristaSeleccionado = DB::table('grupos_minorista')
            ->where('codigo_de_producto_minorista', $item->codigo_de_producto_minorista)
            ->first();

        // Obtener todas las divisiones
        $divisiones = DB::table('divisiones')->select('codigo_division', 'descripcion')->get();

        // Obtener las categorías de la división seleccionada
        $categorias = DB::table('categorias_divisiones')
            ->where('cod_division', $item->cod_division)
            ->select('cod_categoria_producto', 'descripcion')
            ->get();

        // Obtener los grupos minoristas de la categoría seleccionada
        $gruposMinoristas = DB::table('grupos_minorista')
            ->where('cod_categoria_producto', $item->cod_categoria_producto)
            ->select('codigo_de_producto_minorista', 'numeros_serie')
            ->get();

        // Obtener las unidades de medida desde la tabla 'unidades_m'
        $unidadesMedida = DB::table('unidades_m')
            ->select('codigo', 'descripcion')
            ->get();

        // Formatear 'no_s' con ceros a la izquierda (asumiendo longitud de 6 dígitos)
        $no_s_padded = str_pad($item->no_s, 6, '0', STR_PAD_LEFT);

        // Gestionar las imágenes del producto desde el sistema de archivos
        $disk = 'public';
        $imageFolder = 'itemsview/' . $no_s_padded;

        $images = [];

        // Verificar si la carpeta existe en el disco 'public'
        if (Storage::disk($disk)->exists($imageFolder)) {
            // Obtener todos los archivos en la carpeta del producto
            $files = Storage::disk($disk)->files($imageFolder);

            foreach ($files as $file) {
                $images[] = asset('storage/' . $file); // Obtener la URL de la imagen
            }
        }

        // Designar la primera imagen como principal, si no existe una designada explícitamente
        $mainImage = $images[0] ?? asset('storage/itemsview/default.jpg'); // Si no hay imágenes, mostrar una por defecto

        // Todas las imágenes se consideran secundarias, excepto la principal
        $secondaryImages = array_slice($images, 1); // Todas menos la primera son secundarias

        // Obtener otros productos en el mismo grupo
        $otrosProductosMismoGrupo = collect(); // Inicializar una colección vacía

        if ($grupoActual) {
            $otrosProductosMismoGrupo = DB::table('itemsdb')
                ->join('producto_atributo', 'itemsdb.id', '=', 'producto_atributo.producto_id')
                ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
                ->join('grupos', 'atributos.grupo_id', '=', 'grupos.id')
                ->where('grupos.id', $grupoActual->id)
                ->where('itemsdb.id', '!=', $id) // Excluir el producto actual
                ->select(
                    'itemsdb.id',
                    'itemsdb.no_s',
                    'itemsdb.nombre',
                    'itemsdb.precio_unitario',
                    'grupos.descripcion as grupo_descripcion',
                    'itemsdb.activo'
                )
                ->distinct()
                ->get();
        }
            // Obtener los productos que tienen los atributos seleccionados
    $productos = collect();
    if (!empty($atributosProducto)) {
        $productos = DB::table('itemsdb')
            ->join('producto_atributo', 'itemsdb.id', '=', 'producto_atributo.producto_id')
            ->whereIn('producto_atributo.atributo_id', $atributosProducto)
            ->where('itemsdb.id', '!=', $id) // Excluir el producto actual
            ->select('itemsdb.*')
            ->distinct()
            ->get();
    }
        // Consulta para obtener el item y su cantidad disponible
        $item = DB::table('itemsdb')
        ->leftJoin('inventario', 'itemsdb.no_s', '=', 'inventario.no_s')
        ->select(
            'itemsdb.*',
            'inventario.cantidad_disponible'
        )
        ->where('itemsdb.id', $id)
        ->first();
        // Enviar los datos a la vista, incluyendo todas las imágenes
        return view('admin.edit_items', compact(
            'item',
            'divisiones',
            'categorias',
            'gruposMinoristas',
            'proveedores',
            'unidadesMedida',
            'mainImage', // Imagen principal
            'secondaryImages', // Todas las imágenes, excepto la primera
            'divisionSeleccionada',
            'categoriaSeleccionada',
            'grupoMinoristaSeleccionado',
            'grupos',
            'grupoActual',
            'atributos',
            'atributosProducto',
            'otrosProductosMismoGrupo', // Añadir esta línea
            'productosPorGrupo',
            'productos'
        ));
    }

    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'no_s' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'precio_unitario' => 'required|numeric',
            'activo' => 'required|boolean',
            'no_proveedor' => 'required|string|max:255',
            'unidad_medida_venta' => 'required|string|max:255',
            'cod_division' => 'required|string|max:255',
            'cod_categoria_producto' => 'required|string|max:255',
            'codigo_de_producto_minorista' => 'required|string|max:255',
            'costo_unitario' => 'required|numeric',
            'precio_unitario_IVAinc' => 'required|numeric',
            'descuento' => 'nullable|numeric',
            'precio_con_descuento' => 'nullable|numeric',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'secondary_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'grupo_id' => 'nullable|integer|exists:grupos,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Preparar los datos para actualizar en la base de datos
        $data = [
            'no_s' => $request->input('no_s'),

            'no_proveedor' => $request->input('no_proveedor'),
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'nombre_bc' => $request->input('nombre_bc'),
            'costo_unitario' => $request->input('costo_unitario'),
            'precio_unitario' => $request->input('precio_unitario'),
            'cod_division' => $request->input('cod_division'),
            'cod_categoria_producto' => $request->input('cod_categoria_producto'),
            'codigo_de_producto_minorista' => $request->input('codigo_de_producto_minorista'),
            'unidad_medida_venta' => $request->input('unidad_medida_venta'),
            'precio_unitario_IVAinc' => $request->input('precio_unitario_IVAinc'),
            'descuento' => $request->input('descuento'),
            'precio_con_descuento' => $request->input('precio_con_descuento'),
            'descripcion_alias' => $request->input('descripcion_alias'),
            'activo' => $request->input('activo'),
            'modificada_por' => 'admin',
            'fecha_modificacion' => now(),
            'allow_local_shipping' => $request->has('allow_local_shipping') ? 1 : 0,
            'allow_paqueteria_shipping' => $request->has('allow_paqueteria_shipping') ? 1 : 0,
            'allow_store_pickup' => $request->has('allow_store_pickup') ? 1 : 0,
            'allow_cobrar_shipping' => $request->has('allow_cobrar_shipping') ? 1 : 0, // Añadir aquí
        ];

        // Actualizar el item en la base de datos
        DB::table('itemsdb')->where('id', $id)->update($data);

        // Lógica para manejar las imágenes
        $no_s = $request->input('no_s');
        $no_s_padded = str_pad($no_s, 6, '0', STR_PAD_LEFT);
        $carpetaProducto = "itemsview/{$no_s_padded}"; // Carpeta donde se guardan las imágenes
        $disk = 'public'; // Disk de Laravel para almacenamiento público

        // Crear la carpeta si no existe
        if (!Storage::disk($disk)->exists($carpetaProducto)) {
            Storage::disk($disk)->makeDirectory($carpetaProducto);
        }

        // Manejar imagen principal si existe una nueva
        if ($request->hasFile('main_image')) {
            // Eliminar la imagen principal anterior si existe
            $oldMainImage = "{$carpetaProducto}/{$no_s_padded}.*";
            $files = Storage::disk($disk)->files($carpetaProducto);
            foreach ($files as $file) {
                if (preg_match("/{$no_s_padded}\./", basename($file))) {
                    Storage::disk($disk)->delete($file);
                }
            }

            // Guardar la nueva imagen principal
            $mainImage = $request->file('main_image');
            $mainImageExtension = $mainImage->getClientOriginalExtension();
            $mainImageName = "{$no_s_padded}.{$mainImageExtension}"; // Asignar el número de serie formateado como nombre
            $mainImage->storeAs($carpetaProducto, $mainImageName, $disk); // Guardar en 'storage/app/public/itemsview/{no_s_padded}/'
        }

        // Manejar imágenes secundarias
        if ($request->hasFile('secondary_images')) {
            foreach ($request->file('secondary_images') as $secondaryImage) {
                $secondaryImageName = uniqid() . '.' . $secondaryImage->getClientOriginalExtension();
                $secondaryImage->storeAs($carpetaProducto, $secondaryImageName, $disk); // Guardar en la carpeta correspondiente
            }
        }

        // Si el item se desactiva (activo = 0), eliminarlo de los carritos y envíos
        if ($request->input('activo') == 0) {
            DB::table('cart_items')->where('no_s', $no_s)->delete();
            DB::table('shippment_items')->where('no_s', $no_s)->delete();
        }

        // Si la petición es AJAX, devolver una respuesta JSON
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        // Obtener el ID del grupo seleccionado
        $grupoId = $request->input('grupo_id');

        // Eliminar las relaciones actuales de atributos del producto
        DB::table('producto_atributo')->where('producto_id', $id)->delete();

        // Si se seleccionó un grupo, asignar los atributos del grupo al producto
        if ($grupoId) {
            // Obtener los atributos del grupo
            $atributos = DB::table('atributos')->where('grupo_id', $grupoId)->pluck('id');

            // Crear las relaciones en producto_atributo
            foreach ($atributos as $atributoId) {
                DB::table('producto_atributo')->insert([
                    'producto_id' => $id,
                    'atributo_id' => $atributoId,
                ]);
            }
        }
        $atributosSeleccionados = $request->input('atributos', []);
        DB::table('producto_atributo')->where('producto_id', $id)->delete();
        foreach ($atributosSeleccionados as $atributoId) {
            DB::table('producto_atributo')->insert([
                'producto_id' => $id,
                'atributo_id' => $atributoId,
            ]);
        }

        // Redirigir de vuelta con un mensaje de éxito
        return redirect()->route('admin.items.index')->with('success', 'Item actualizado exitosamente');
    }

    public function getDivisiones()
    {
        $divisiones = DB::table('divisiones')->select('codigo_division', 'descripcion')->get();
        return response()->json($divisiones);
    }

    public function getCategorias($divisionId)
    {
        $categorias = DB::table('categorias_divisiones')
            ->where('cod_division', $divisionId)
            ->select('cod_categoria_producto', 'descripcion')
            ->get();
        return response()->json($categorias);
    }

    public function getGruposMinoristas($categoriaId)
    {
        $grupos_minoristas = DB::table('grupos_minorista')
            ->where('cod_categoria_producto', $categoriaId)
            ->select('codigo_de_producto_minorista', 'numeros_serie')
            ->get();
        return response()->json($grupos_minoristas);
    }

    public function eliminarImagen(Request $request)
    {
        // Validar que la imagen ha sido enviada
        $request->validate([
            'image' => 'required|string',
        ]);

        // Recibir la ruta completa de la imagen desde el request
        $imageUrl = $request->input('image');

        // Parsear la URL de la imagen para obtener el path relativo en el almacenamiento

        $imagePath = str_replace(url('storage') . '/', '', $imageUrl);

        // Verificar si la imagen existe en el almacenamiento (dentro de 'storage/app/public')
        if (Storage::disk('public')->exists($imagePath)) {
            // Eliminar la imagen del almacenamiento
            Storage::disk('public')->delete($imagePath);
            return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente.']);
        }

        // Enviar respuesta si la imagen no existe
        return response()->json(['error' => 'La imagen no se encuentra o ya ha sido eliminada.'], 404);
    }

    public function subirImagenPrincipal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'main_image' => 'required|image|mimes:jpeg,jpg,png|max:2048', // Permitir imágenes JPEG, JPG y PNG
            'item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $item = DB::table('itemsdb')->where('id', $request->input('item_id'))->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item no encontrado'], 404);
        }

        $no_s = $item->no_s;
        $no_s_padded = str_pad($no_s, 6, '0', STR_PAD_LEFT);
        $carpetaProducto = "itemsview/{$no_s_padded}";
        $disk = 'public';

        // Crear la carpeta si no existe
        if (!Storage::disk($disk)->exists($carpetaProducto)) {
            Storage::disk($disk)->makeDirectory($carpetaProducto);
        }

        // Mover la imagen principal existente a una imagen secundaria (considerar tanto .jpg como .png)
        $files = Storage::disk($disk)->files($carpetaProducto);
        $secondaryImageCounter = 1;

        // Renombrar las imágenes principales que tengan guiones o sufijos (.jpg o .png)
        foreach ($files as $file) {
            $fileBasename = basename($file);
            // Si es la imagen principal (sin guiones), ya sea JPG o PNG, renombrarla como secundaria
            if (preg_match("/^{$no_s_padded}\.(jpg|png)$/i", $fileBasename)) {
                // Encontrar el próximo número disponible para la imagen secundaria
                while (Storage::disk($disk)->exists("{$carpetaProducto}/{$no_s_padded}_{$secondaryImageCounter}." . pathinfo($file, PATHINFO_EXTENSION))) {
                    $secondaryImageCounter++;
                }

                $newSecondaryImageName = "{$no_s_padded}_{$secondaryImageCounter}." . pathinfo($file, PATHINFO_EXTENSION);
                Storage::disk($disk)->move($file, "{$carpetaProducto}/{$newSecondaryImageName}");
                $secondaryImageCounter++;
            }
        }

        // Guardar la nueva imagen principal (siempre guardarla como .jpg o mantener la extensión original)
        $mainImage = $request->file('main_image');
        $mainImageExtension = strtolower($mainImage->getClientOriginalExtension()); // Obtener la extensión del archivo subido
        $mainImageName = "{$no_s_padded}.{$mainImageExtension}"; // Guardar la imagen con la extensión original

        // Guardar la imagen principal
        $mainImage->storeAs($carpetaProducto, $mainImageName, $disk);

        $imageUrl = asset("storage/{$carpetaProducto}/{$mainImageName}");

        return response()->json(['success' => true, 'image_url' => $imageUrl]);
    }

    public function subirImagenSecundaria(Request $request)
    {
        // Validación de las imágenes subidas
        $validator = Validator::make($request->all(), [
            'secondary_images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Obtener el item de la base de datos
        $item = DB::table('itemsdb')->where('id', $request->input('item_id'))->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item no encontrado'], 404);
        }

        $no_s = $item->no_s;
        $no_s_padded = str_pad($no_s, 6, '0', STR_PAD_LEFT);
        $carpetaProducto = "itemsview/{$no_s_padded}";
        $disk = 'public';

        // Crear la carpeta si no existe
        if (!Storage::disk($disk)->exists($carpetaProducto)) {
            Storage::disk($disk)->makeDirectory($carpetaProducto);
        }

        // Subir las nuevas imágenes secundarias
        foreach ($request->file('secondary_images') as $secondaryImage) {
            $extension = strtolower($secondaryImage->getClientOriginalExtension());
            $secondaryImageName = uniqid() . '.' . $extension;
            $secondaryImage->storeAs($carpetaProducto, $secondaryImageName, $disk);
        }

        // Verificar si existe una imagen principal
        $files = Storage::disk($disk)->files($carpetaProducto);

        $mainImageExists = false;
        $imageCount = count($files);

        foreach ($files as $file) {
            $fileBasename = basename($file);
            // Si existe un archivo con el nombre principal (no_s_padded.jpg, .jpeg, .png)
            if (preg_match("/^{$no_s_padded}\.(jpg|jpeg|png)$/i", $fileBasename)) {
                $mainImageExists = true;
                break;
            }
        }

        // Si no existe una imagen principal y solo hay una imagen en la carpeta
        if (!$mainImageExists && $imageCount == 1) {
            $onlyImagePath = $files[0];
            $extension = strtolower(pathinfo($onlyImagePath, PATHINFO_EXTENSION));
            $mainImageName = "{$no_s_padded}.{$extension}";
            $newMainImagePath = "{$carpetaProducto}/{$mainImageName}";

            // Renombrar la única imagen como imagen principal
            Storage::disk($disk)->move($onlyImagePath, $newMainImagePath);
        }

        // Obtener todas las imágenes actuales en la carpeta
        $allImageUrls = [];
        $allFiles = Storage::disk($disk)->files($carpetaProducto);

        $mainImageUrl = null;

        foreach ($allFiles as $file) {
            $basename = basename($file);
            if (preg_match("/^{$no_s_padded}\.(jpg|jpeg|png)$/i", $basename)) {
                // Es la imagen principal
                $mainImageUrl = asset("storage/{$file}");
            } else {
                $allImageUrls[] = asset("storage/{$file}");
            }
        }

        // Devolver todas las imágenes, incluyendo la principal
        return response()->json([
            'success' => true,
            'image_urls' => $allImageUrls,
            'main_image_url' => $mainImageUrl,
        ]);
    }

    public function hacerImagenPrincipal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $item = DB::table('itemsdb')->where('id', $request->input('item_id'))->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item no encontrado'], 404);
        }

        $imageUrl = $request->input('image');
        $disk = 'public';

        // Convertir la URL de la imagen a la ruta relativa en el disco
        $relativeImagePath = str_replace(asset('storage') . '/', '', $imageUrl);

        if (!Storage::disk($disk)->exists($relativeImagePath)) {
            return response()->json(['success' => false, 'message' => 'Imagen no encontrada'], 404);
        }

        // Verificar que la imagen sea de tipo JPG o JPEG
        $extension = strtolower(pathinfo($relativeImagePath, PATHINFO_EXTENSION));
        if ($extension != 'jpg' && $extension != 'jpeg') {
            return response()->json(['success' => false, 'message' => 'La imagen principal debe ser de tipo JPG o JPEG'], 422);
        }

        $no_s = $item->no_s;
        $no_s_padded = str_pad($no_s, 6, '0', STR_PAD_LEFT);
        $carpetaProducto = "itemsview/{$no_s_padded}";

        // Renombrar cualquier imagen que actualmente sea principal (cualquier archivo con el nombre no_s_padded)
        $files = Storage::disk($disk)->files($carpetaProducto);
        $secondaryImageCounter = 1;

        foreach ($files as $file) {
            $fileBasename = basename($file);
            // Verificar si alguna imagen es principal (con el nombre no_s_padded y cualquier extensión)
            if (preg_match("/^{$no_s_padded}\.(jpg|jpeg|png)$/i", $fileBasename)) {
                // Renombrarla a secundaria (asignar el próximo número disponible)
                while (Storage::disk($disk)->exists("{$carpetaProducto}/{$no_s_padded}_{$secondaryImageCounter}.jpg")) {
                    $secondaryImageCounter++;
                }
                $newSecondaryImageName = "{$no_s_padded}_{$secondaryImageCounter}." . pathinfo($file, PATHINFO_EXTENSION);
                Storage::disk($disk)->move($file, "{$carpetaProducto}/{$newSecondaryImageName}");
            }
        }

        // Mover la imagen seleccionada como la nueva imagen principal
        $mainImagePath = "{$carpetaProducto}/{$no_s_padded}.jpg";
        Storage::disk($disk)->move($relativeImagePath, $mainImagePath);

        $imageUrl = asset("storage/{$mainImagePath}");

        return response()->json(['success' => true, 'image_url' => $imageUrl]);
    }
    public function createGrupo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion_grupo' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Crear el nuevo grupo
        $grupoId = DB::table('grupos')->insertGetId([
            'descripcion' => $request->input('descripcion_grupo'),
        ]);

        return response()->json(['success' => true, 'grupo_id' => $grupoId]);
    }

    public function obtenerProductosPorAtributos(Request $request)
    {
        $atributosSeleccionados = $request->input('atributos', []);
        $productoIdActual = $request->input('producto_id_actual');
    
        // Si no hay atributos seleccionados, devolver una colección vacía
        if (empty($atributosSeleccionados)) {
            $productos = collect(); // Colección vacía
        } else {
            // Obtener los grupos de los atributos seleccionados
            $gruposIds = DB::table('atributos')
                ->whereIn('id', $atributosSeleccionados)
                ->pluck('grupo_id')
                ->unique();
    
            // Obtener todos los productos que tengan atributos en esos grupos
            $productos = DB::table('itemsdb')
                ->join('producto_atributo', 'itemsdb.id', '=', 'producto_atributo.producto_id')
                ->join('atributos', 'producto_atributo.atributo_id', '=', 'atributos.id')
                ->whereIn('atributos.grupo_id', $gruposIds)
                ->where('itemsdb.id', '!=', $productoIdActual)
                ->select('itemsdb.*')
                ->distinct()
                ->get();
        }
    
        // Generar el HTML de la tabla de productos
        $html = view('admin.partials.productos_lista', compact('productos'))->render();
    
        return response()->json(['html' => $html]);
    }
    
    
    


}
