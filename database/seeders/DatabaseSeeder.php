<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Datos iniciales del sistema (roles, estados, etc.)
        $this->call([
            InitialDataSeeder::class,
            AdminSeeder::class,
            DatosSeeder::class,
        ]);
    }
}
