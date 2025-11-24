<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEmpaqueSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Caja de Cartón', 'descripcion' => 'Caja de cartón corrugado estándar'],
            ['nombre' => 'Bolsa Plástica', 'descripcion' => 'Bolsa plástica resistente'],
            ['nombre' => 'Canasta', 'descripcion' => 'Canasta de mimbre o plástico'],
            ['nombre' => 'Cajón de Madera', 'descripcion' => 'Cajón de madera para productos pesados'],
            ['nombre' => 'Malla', 'descripcion' => 'Malla o red para productos a granel'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipo_empaque')->insert([
                'nombre' => $tipo['nombre'],
                'descripcion' => $tipo['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
