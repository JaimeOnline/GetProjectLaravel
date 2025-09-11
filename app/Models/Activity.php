<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;
    // Campos que se pueden llenar de forma masiva
    protected $fillable = [
        'name',
        'description',
        'estatus_operacional',
        'status',
        'fecha_recepcion',
        'caso',
        'parent_id',
    ];
    // Campos que deben ser tratados como fechas
    protected $casts = [
        'fecha_recepcion' => 'datetime',
    ];
    /**
     * Usuarios asignados a la actividad (relación muchos a muchos)
     * DEPRECATED: Mantener para compatibilidad, usar analistas() en su lugar
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'activity_user');
    }

    /**
     * Analistas asignados a la actividad (relación muchos a muchos)
     */
    public function analistas()
    {
        return $this->belongsToMany(Analista::class, 'activity_analista');
    }
    /**
     * Requerimientos asociados a la actividad (relación uno a muchos)
     */
    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }
    /**
     * Subactividades (relación uno a muchos)
     */
    public function subactivities()
    {
        return $this->hasMany(Activity::class, 'parent_id')->with('subactivities');
    }
    /**
     * Actividad padre (si existe)
     */
    public function parent()
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }
    /**
     * Comentarios asociados a la actividad (relación uno a muchos)
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Correos asociados a la actividad (relación uno a muchos)
     */
    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Estados asignados a la actividad (relación muchos a muchos)
     */
    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'activity_statuses')
            ->withPivot('assigned_at')
            ->withTimestamps()
            ->orderBy('order');
    }

    /**
     * Obtener la etiqueta legible del estado (DEPRECATED - usar statuses())
     * Mantener para compatibilidad con código existente
     */
    public function getStatusLabelAttribute()
    {
        // Si tiene estados en la nueva relación, usar el primero
        if ($this->statuses && $this->statuses->count() > 0) {
            return $this->statuses->first()->label;
        }

        // Fallback al sistema anterior
        $labels = [
            'en_ejecucion' => 'En ejecución',
            'culminada' => 'Culminada',
            'en_espera_de_insumos' => 'En espera de insumos',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Obtener todos los badges HTML de los estados
     */
    public function getStatusBadgesAttribute()
    {
        return $this->statuses->map(function ($status) {
            return $status->badge_html;
        })->implode(' ');
    }

    /**
     * Verificar si la actividad tiene un estado específico
     */
    public function hasStatus($statusName)
    {
        return $this->statuses->contains('name', $statusName);
    }

    /**
     * Obtener el estado principal (el primero en orden)
     */
    public function getPrimaryStatusAttribute()
    {
        return $this->statuses->first();
    }
}
