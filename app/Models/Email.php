<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'type',
        'subject',
        'sender_recipient',
        'content',
        'attachments',
        'email_type', 
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    /**
     * Relación con la actividad
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Scope para correos enviados
     */
    public function scopeSent($query)
    {
        return $query->where('type', 'sent');
    }

    /**
     * Scope para correos recibidos
     */
    public function scopeReceived($query)
    {
        return $query->where('type', 'received');
    }

    /**
     * Obtener la etiqueta legible del tipo
     */
    public function getTypeLabelAttribute()
    {
        return $this->type === 'sent' ? 'Enviado' : 'Recibido';
    }
}
