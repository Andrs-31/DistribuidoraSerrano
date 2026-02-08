<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Permiso;

class PermisoSeeder extends Seeder
{
    public function run()
    {
        Permiso::insertOrIgnore([
            [
                'id_permiso' => 1,
                'nombre' => 'admin',
                'descripcion' => 'Administrador del sistema. Acceso total a todos los módulos.'
            ],
            [
                'id_permiso' => 2,
                'nombre' => 'gestion_usuarios',
                'descripcion' => 'Gestión de usuarios del sistema.'
            ],
            [
                'id_permiso' => 3,
                'nombre' => 'gestion_productos',
                'descripcion' => 'Gestión de productos y administración de precios.'
            ],
            [
                'id_permiso' => 4,
                'nombre' => 'gestion_clientes',
                'descripcion' => 'Registro, edición y desactivación de clientes.'
            ],
            [
                'id_permiso' => 5,
                'nombre' => 'facturacion',
                'descripcion' => 'Creación, edición, impresión y consulta de facturas.'
            ],
            [
                'id_permiso' => 6,
                'nombre' => 'reportes',
                'descripcion' => 'Acceso a reportes y consultas avanzadas.'
            ],
        ]);
    }
}
