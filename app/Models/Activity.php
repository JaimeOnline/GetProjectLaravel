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
        return $this->hasMany(Activity::class, 'parent_id');
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
     * Obtener la etiqueta legible del estado
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'en_ejecucion' => 'En ejecución',
            'culminada' => 'Culminada',
            'en_espera_de_insumos' => 'En espera de insumos',
        ];
        return $labels[$this->status] ?? $this->status; // Devuelve el estado legible o el original si no se encuentra
    }
}