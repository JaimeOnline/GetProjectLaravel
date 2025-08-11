<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
       'email_verified_at' => 'datetime',
       'password' => 'hashed',
   ];
   
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_user'); // Asegúrate de que el nombre de la tabla pivote sea correcto
    }
}