<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analista extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * Relación muchos a muchos con Activity
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_analista');
    }
}
