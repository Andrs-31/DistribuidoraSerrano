<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table = 'distrito';
    protected $primaryKey = 'codigo_distrito';
    public $incrementing = false;
    public $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}
