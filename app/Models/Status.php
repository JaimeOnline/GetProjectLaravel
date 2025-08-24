<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'color',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Actividades que tienen este estado
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_statuses')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }

    /**
     * Scope para obtener solo estados activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar por el campo order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Obtener el badge HTML para mostrar el estado
     */
    public function getBadgeHtmlAttribute()
    {
        return sprintf(
            '<span class="badge" style="background-color: %s; color: %s;"><i class="%s"></i> %s</span>',
            $this->color,
            $this->getContrastColor(),
            $this->icon ?? 'fas fa-circle',
            $this->label
        );
    }

    /**
     * Calcular color de contraste para el texto del badge
     */
    public function getContrastColor()
    {
        $hex = str_replace('#', '', $this->color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        return $brightness > 155 ? '#000000' : '#ffffff';
    }
}
