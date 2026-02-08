<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    protected $table = 'detalle_factura';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;
    protected $guarded = [];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'num_factura_fk', 'num_factura');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto_fk', 'id_producto');
    }
}
