<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoCliente extends Model
{
    protected $table = 'grupo_clientes';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;
    protected $guarded = [];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_grupo_fk', 'id_grupo');
    }

    public function listasPrecio()
    {
        return $this->hasMany(ListaPrecio::class, 'id_grupo_fk', 'id_grupo');
    }
}
