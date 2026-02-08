<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        User::insertOrIgnore([
            [
                'nombre' => 'Administrador',
                'user' => 'admin',
                'email' => 'admin@demo.com',
                'password' => Hash::make('123456'),
                'permiso_fk' => 1
            ],
            [
                'nombre' => 'Gestor Productos',
                'user' => 'productos',
                'email' => 'productos@demo.com',
                'password' => Hash::make('123456'),
                'permiso_fk' => 3
            ],
            [
                'nombre' => 'Gestor Clientes',
                'user' => 'clientes',
                'email' => 'clientes@demo.com',
                'password' => Hash::make('123456'),
                'permiso_fk' => 4
            ],
            [
                'nombre' => 'Gestión Usuarios',
                'user' => 'usuarios',
                'email' => 'usuarios@demo.com',
                'password' => Hash::make('123456'),
                'permiso_fk' => 2
            ],
            [
                'nombre' => 'Operador Factura',
                'user' => 'factura',
                'email' => 'factura@demo.com',
                'password' => Hash::make('123456'),
                'permiso_fk' => 5
            ],
            [
                'nombre' => 'Analista Reportes',
                'user' => 'reportes',
                'email' => 'reportes@demo.com',
                'password' => Hash::make('123456'),
                'permiso_fk' => 6
            ],
        ]);
    }

}
