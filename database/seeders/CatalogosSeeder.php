<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * php artisan db:seed --class=CatalogosSeeder
     */
    public function run(): void
    {
        $this->command->info('Iniciando seeder de catálogos...');

        // 1. Categorías (SOLO FRUTAS Y VERDURAS)
        $categorias = [
            ['nombre' => 'Frutas', 'descripcion' => 'Frutas frescas orgánicas'],
            ['nombre' => 'Verduras', 'descripcion' => 'Verduras frescas orgánicas'],
            // Granos eliminados
        ];

        foreach ($categorias as $cat) {
            if (!DB::table('catalogo_categorias')->where('nombre', $cat['nombre'])->exists()) {
                DB::table('catalogo_categorias')->insert($cat);
            }
        }
        $this->command->info('  ✓ Categorías (2)');

        // 2. Productos con peso promedio (SOLO EJEMPLOS)
        // Nota: El listado completo de 40 productos está en CatalogosEspecificacionesSeeder.
        // Aquí dejamos solo los básicos para asegurar compatibilidad si este seeder corre antes.
        $productos = [
            // Frutas Base
            ['nombre' => 'Manzanas', 'descripcion' => 'Manzanas orgánicas', 'peso_promedio' => 0.180, 'id_categoria' => 1],
            ['nombre' => 'Naranjas', 'descripcion' => 'Naranjas orgánicas', 'peso_promedio' => 0.200, 'id_categoria' => 1],
            ['nombre' => 'Plátanos', 'descripcion' => 'Plátanos orgánicos', 'peso_promedio' => 0.150, 'id_categoria' => 1],

            // Verduras Base
            ['nombre' => 'Lechugas', 'descripcion' => 'Lechugas hidropónicas', 'peso_promedio' => 0.300, 'id_categoria' => 2],
            ['nombre' => 'Tomates', 'descripcion' => 'Tomates orgánicos', 'peso_promedio' => 0.120, 'id_categoria' => 2],
            ['nombre' => 'Zanahorias', 'descripcion' => 'Zanahorias orgánicas', 'peso_promedio' => 0.100, 'id_categoria' => 2],
        ];

        foreach ($productos as $prod) {
            // Verificar por nombre e id_categoria para evitar duplicados
            if (!DB::table('catalogo_productos')->where('nombre', $prod['nombre'])->where('id_categoria', $prod['id_categoria'])->exists()) {
                DB::table('catalogo_productos')->insert($prod);
            }
        }
        $this->command->info('  ✓ Productos base (6)');

        // 3. Tipos de Empaque con medidas, tara y capacidad
        $tiposEmpaque = [
            [
                'nombre' => 'Caja plástica',
                'descripcion' => 'Caja plástica ventilada estándar para frutas/verduras',
                'largo' => 40.0,
                'ancho' => 30.0,
                'alto' => 25.0,
                'tara' => 1.2,
                'capacidad' => 100, // 100 unidades (manzanas/naranjas)
                'unidades_por_pallet' => 48
            ],
            [
                'nombre' => 'Bolsa plástica',
                'descripcion' => 'Bolsa plástica grado alimenticio sellada',
                'largo' => 60.0,
                'ancho' => 40.0,
                'alto' => 15.0,
                'tara' => 0.1,
                'capacidad' => 50, // 50 unidades (mitad de la caja)
                'unidades_por_pallet' => 80
            ],
            [
                'nombre' => 'Pallet (48 cajas)',
                'descripcion' => 'Pallet europeo 120×80 cm con 48 cajas apiladas',
                'largo' => 120.0,
                'ancho' => 80.0,
                'alto' => 170.0,
                'tara' => 82.6, // 48 cajas × 1.2 kg + 25 kg pallet
                'capacidad' => 4800, // 48 cajas × 100 unidades
                'unidades_por_pallet' => 1
            ],
        ];

        foreach ($tiposEmpaque as $tipo) {
            if (!DB::table('catalogo_tipos_empaque')->where('nombre', $tipo['nombre'])->exists()) {
                DB::table('catalogo_tipos_empaque')->insert($tipo);
            }
        }
        $this->command->info('  ✓ Tipos de Empaque (3)');

        $this->command->info('');
        $this->command->info('✅ Catálogos base creados!');
    }
}
