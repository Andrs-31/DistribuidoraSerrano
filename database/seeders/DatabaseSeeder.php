<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermisoSeeder::class,
            UserSeeder::class,
        ]);
    }
}
