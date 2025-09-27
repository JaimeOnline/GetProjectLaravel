<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    protected $table = 'tipos_productos';

    protected $fillable = [
        'nombre',
    ];
}
