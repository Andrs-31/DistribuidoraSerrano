<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Http;


class User extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'user',
        'email',
        'password',
        'estado',
        'permiso_fk'
    ];

    protected $hidden = [
        'password'
    ];

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'permiso_fk', 'id_permiso');
    }
}
