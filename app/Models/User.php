<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified',
    ];

    // Si quieres añadir otros atributos relacionados con envíos, puedes agregarlos aquí:
    // protected $fillable = ['name', 'email', 'password', 'direccion', 'codigo_postal', 'numero_telefono'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
