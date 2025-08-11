<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'status',
    ];
    /**
     * Usuarios asignados a la actividad (relación muchos a muchos)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'activity_user');
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }
}
