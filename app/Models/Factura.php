<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';
    protected $primaryKey = 'num_factura';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'ruc_cliente_fk', 'ruc');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario_fk', 'id_user');
    }

    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class, 'num_factura_fk', 'id_factura');
    }
}
