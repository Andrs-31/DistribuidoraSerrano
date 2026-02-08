<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaPrecio extends Model
{
    protected $table = 'lista_precios';
    protected $primaryKey = 'id_lista';

    public $timestamps = false;

    protected $fillable = [
        'id_producto_fk',
        'id_grupo_fk',
        'unidad',
        'precio_base',
        'estado',
    ];

    // 🔗 Relación
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto_fk', 'id_producto');
    }

    public function grupo()
    {
        return $this->belongsTo(GrupoCliente::class, 'id_grupo_fk', 'id_grupo');
    }
}