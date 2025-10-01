<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    public function activities()
    {
        return $this->hasMany(\App\Models\Activity::class, 'proyecto_id');
    }
}
