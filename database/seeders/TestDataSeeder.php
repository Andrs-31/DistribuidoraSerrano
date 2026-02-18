<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Seed permisos y usuarios para tests.
     */
    public function run(): void
    {
        // Crear permisos si no existen
        $permisos = [
            ['id_permiso' => 1, 'nombre' => 'admin', 'descripcion' => 'Administrador del sistema'],
            ['id_permiso' => 2, 'nombre' => 'gestion_usuarios', 'descripcion' => 'Gestión de usuarios'],
            ['id_permiso' => 3, 'nombre' => 'gestion_productos', 'descripcion' => 'Gestión de productos'],
            ['id_permiso' => 4, 'nombre' => 'gestion_clientes', 'descripcion' => 'Gestión de clientes'],
            ['id_permiso' => 5, 'nombre' => 'facturacion', 'descripcion' => 'Facturación'],
            ['id_permiso' => 6, 'nombre' => 'reportes', 'descripcion' => 'Reportes'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(
                ['id_permiso' => $permiso['id_permiso']],
                ['nombre' => $permiso['nombre'], 'descripcion' => $permiso['descripcion']]
            );
        }

        // Crear usuarios de prueba si no existen
        $usuarios = [
            [
                'nombre' => 'Administrador',
                'user' => 'admin',
                'email' => 'admin@demo.com',
                'password' => Hash::make('123456'),
                'estado' => 1,
                'permiso_fk' => 1,
            ],
            [
                'nombre' => 'Operador Factura',
                'user' => 'factura',
                'email' => 'factura@demo.com',
                'password' => Hash::make('123456'),
                'estado' => 1,
                'permiso_fk' => 5,
            ],
            [
                'nombre' => 'Gestor Clientes',
                'user' => 'clientes',
                'email' => 'clientes@demo.com',
                'password' => Hash::make('123456'),
                'estado' => 1,
                'permiso_fk' => 4,
            ],
            [
                'nombre' => 'Gestor Productos',
                'user' => 'productos',
                'email' => 'productos@demo.com',
                'password' => Hash::make('123456'),
                'estado' => 1,
                'permiso_fk' => 3,
            ],
        ];

        foreach ($usuarios as $usuario) {
            // Usar insert para tener control del id
            User::firstOrCreate(
                ['email' => $usuario['email']],
                $usuario
            );
        }
    }
}
