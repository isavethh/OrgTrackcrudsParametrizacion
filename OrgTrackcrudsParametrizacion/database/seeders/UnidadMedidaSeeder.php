<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['nombre' => 'Kilogramo', 'abreviatura' => 'kg', 'descripcion' => 'Unidad de masa'],
            ['nombre' => 'Gramo', 'abreviatura' => 'g', 'descripcion' => 'Unidad de masa menor'],
            ['nombre' => 'Litro', 'abreviatura' => 'L', 'descripcion' => 'Unidad de volumen'],
            ['nombre' => 'Unidad', 'abreviatura' => 'u', 'descripcion' => 'Pieza individual'],
            ['nombre' => 'Docena', 'abreviatura' => 'doc', 'descripcion' => '12 unidades'],
            ['nombre' => 'Libra', 'abreviatura' => 'lb', 'descripcion' => 'Aproximadamente 0.453 kg'],
        ];

        foreach ($unidades as $unidad) {
            DB::table('unidad_medida')->insert([
                'nombre' => $unidad['nombre'],
                'abreviatura' => $unidad['abreviatura'],
                'descripcion' => $unidad['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
