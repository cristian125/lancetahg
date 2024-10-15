<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    protected $table = 'users_data'; // Nombre de la tabla
    protected $fillable = [
        'user_id',
        'apellido_paterno',
        'apellido_materno',
        'telefono',
        'tratamiento',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
