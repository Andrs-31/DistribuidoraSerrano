<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'ruc';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'ruc_cliente_fk', 'ruc');
    }

    public function grupoCliente()
    {
        return $this->belongsTo(GrupoCliente::class, 'id_grupo_fk', 'id_grupo');
    }

    // Extraer provincia de la dirección
    public function getProvinciaFromDireccion()
    {
        $partes = explode(', ', $this->direccion);
        return $partes[0] ?? null;
    }

    // Extraer distrito de la dirección
    public function getDistritoFromDireccion()
    {
        $partes = explode(', ', $this->direccion);
        return $partes[1] ?? null;
    }

    // Extraer dirección sin provincia y distrito
    public function getDireccionSimple()
    {
        $partes = explode(', ', $this->direccion);
        return $partes[2] ?? null;
    }
}
