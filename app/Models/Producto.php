<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function getRouteKeyName()
    {
        return 'id_producto';
    }

    public function precios()
    {
        return $this->hasMany(ListaPrecio::class, 'id_producto_fk', 'id_producto');
    }

    public function precioBase()
    {
        return $this->hasOne(ListaPrecio::class, 'id_producto_fk', 'id_producto')
            ->where('id_grupo_fk', 1)
            ->where('estado', 1);
    }

    // ANDFRESSS MORANNN
    public function listasPrecio()
    {
        return $this->hasMany(ListaPrecio::class, 'id_producto_fk', 'id_producto');
    }
}
