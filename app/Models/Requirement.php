<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requirement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'activity_id', 
        'description', 
        'status', 
        'fecha_recepcion', 
        'notas'
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Obtener la etiqueta legible del estado
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pendiente' => 'Pendiente',
            'recibido' => 'Recibido',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Scope para requerimientos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('status', 'pendiente');
    }

    /**
     * Scope para requerimientos recibidos
     */
    public function scopeRecibidos($query)
    {
        return $query->where('status', 'recibido');
    }
}