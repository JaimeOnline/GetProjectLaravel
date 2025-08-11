<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Requirement extends Model
{
    protected $fillable = ['activity_id', 'description'];
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}