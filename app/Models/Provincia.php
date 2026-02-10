<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincia';
    protected $primaryKey = 'codigo_provincia';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function distritos()
    {
        return $this->hasMany(Distrito::class, 'codigo_provincia', 'codigo_provincia');
    }
}
