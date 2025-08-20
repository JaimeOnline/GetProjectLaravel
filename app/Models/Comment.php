<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    // Campos que se pueden llenar de forma masiva
    protected $fillable = [
        'activity_id',
        'comment',
    ];

    /**
     * Relación con la actividad
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
