<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProdMin extends Model
{
    protected $table = 'list_prod_min'; // Nombre de la tabla en la base de datos
    
    protected $fillable = [
        'no_s', 'no_proveedor', 'descripcion', 'costo_unitario', 'precio_unitario', 
        'cod_division', 'cod_categoria_producto', 'codigo_de_producto_minorista', 
        'unidad_medida_venta', 'precio_unitario_IVAinc', 'descripcion_alias', 
        'creada_por', 'fecha_creacion', 'modificada_por', 'fecha_modificacion'
    ];

    public $timestamps = false; // Si estás manejando manualmente las fechas
}
