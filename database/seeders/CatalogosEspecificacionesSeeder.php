<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatalogoCategoria;
use App\Models\CatalogoProducto;
use App\Models\CatalogoTipoEmpaque;
use App\Models\CatalogoTamanoConteo;

class CatalogosEspecificacionesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Categorías (SOLO FRUTAS Y VERDURAS)
        $frutas = CatalogoCategoria::firstOrCreate(['nombre' => 'Frutas']);
        $verduras = CatalogoCategoria::firstOrCreate(['nombre' => 'Verduras']);

        // -----------------------------------------------------
        // 2. Definir Productos (20 Frutas, 20 Verduras)
        // -----------------------------------------------------

        // Lista de 20 Frutas
        $listaFrutas = [
            'Manzanas',
            'Naranjas',
            'Plátanos',
            'Uvas',
            'Fresas',
            'Piñas',
            'Mangos',
            'Aguacates',
            'Melones',
            'Limones',
            'Sandías',
            'Papayas',
            'Peras',
            'Duraznos',
            'Kiwis',
            'Cerezas',
            'Mandarinas',
            'Pomelos',
            'Ciruelas',
            'Higos'
        ];
        foreach ($listaFrutas as $nombre) {
            CatalogoProducto::firstOrCreate(['id_categoria' => $frutas->id, 'nombre' => $nombre]);
        }

        // Lista de 20 Verduras
        $listaVerduras = [
            'Tomates',
            'Lechugas',
            'Zanahorias',
            'Cebollas',
            'Papas',
            'Pimientos',
            'Pepinos',
            'Brócoli',
            'Coliflor',
            'Espárragos',
            'Espinacas',
            'Acelgas',
            'Calabacines',
            'Berenjenas',
            'Apio',
            'Rábanos',
            'Betarragas',
            'Repollo',
            'Ajos',
            'Ejotes'
        ];
        foreach ($listaVerduras as $nombre) {
            CatalogoProducto::firstOrCreate(['id_categoria' => $verduras->id, 'nombre' => $nombre]);
        }


        // -----------------------------------------------------
        // 3. Crear Tipos de Empaque (Estándar)
        // -----------------------------------------------------
        $tiposEmpaque = [
            ['nombre' => 'Caja plástica', 'largo' => 60, 'ancho' => 40, 'alto' => 30, 'tara' => 2.5, 'capacidad' => 50, 'unidades_por_pallet' => 40],
            ['nombre' => 'Caja de cartón', 'largo' => 50, 'ancho' => 30, 'alto' => 30, 'tara' => 0.5, 'capacidad' => 40, 'unidades_por_pallet' => 48],
            ['nombre' => 'Bolsa', 'largo' => 30, 'ancho' => 20, 'alto' => 10, 'tara' => 0.05, 'capacidad' => 10, 'unidades_por_pallet' => 200],
            ['nombre' => 'Saco', 'largo' => 80, 'ancho' => 50, 'alto' => 20, 'tara' => 0.2, 'capacidad' => 100, 'unidades_por_pallet' => 50],
            ['nombre' => 'Pallet', 'largo' => 120, 'ancho' => 100, 'alto' => 15, 'tara' => 25, 'capacidad' => 1000, 'unidades_por_pallet' => 1],
            ['nombre' => 'Contenedor', 'largo' => 1200, 'ancho' => 235, 'alto' => 239, 'tara' => 3700, 'capacidad' => 28000, 'unidades_por_pallet' => 20],
        ];
        foreach ($tiposEmpaque as $datos) {
            CatalogoTipoEmpaque::updateOrCreate(['nombre' => $datos['nombre']], $datos);
        }


        // -----------------------------------------------------
        // 4. Generar Especificaciones (Tamaño/Conteo) 
        // -----------------------------------------------------

        // === FRUTAS (20) ===

        $this->crearSpecs('Manzanas', [
            ['conteo' => 18, 'peso' => 0.280, 'nombre' => '18 un - Calibre muy grande'],
            ['conteo' => 36, 'peso' => 0.220, 'nombre' => '36 un - Calibre mediano-grande'],
            ['conteo' => 100, 'peso' => 0.160, 'nombre' => '100 un - Calibre pequeño'],
        ]);

        $this->crearSpecs('Naranjas', [
            ['conteo' => 48, 'peso' => 0.350, 'nombre' => '48 un - Calibre Super Extra'],
            ['conteo' => 72, 'peso' => 0.230, 'nombre' => '72 un - Calibre Grande'],
            ['conteo' => 113, 'peso' => 0.150, 'nombre' => '113 un - Calibre Pequeño'],
        ]);

        $this->crearSpecs('Plátanos', [
            ['conteo' => 100, 'peso' => 0.180, 'nombre' => '100 dedos - Calibre Grande'],
            ['conteo' => 150, 'peso' => 0.120, 'nombre' => '150 dedos - Calibre Pequeño'],
        ]);

        $this->crearSpecs('Uvas', [
            ['conteo' => 10, 'peso' => 0.800, 'nombre' => 'Racimo Grande (XL)'],
            ['conteo' => 15, 'peso' => 0.500, 'nombre' => 'Racimo Mediano (L)'],
            ['conteo' => 20, 'peso' => 0.350, 'nombre' => 'Racimo Pequeño (M)'],
        ]);

        $this->crearSpecs('Fresas', [
            ['conteo' => 8, 'peso' => 0.500, 'nombre' => '8 Clamshells'],
            ['conteo' => 12, 'peso' => 0.340, 'nombre' => '12 Clamshells'],
        ]);

        $this->crearSpecs('Piñas', [
            ['conteo' => 5, 'peso' => 2.2, 'nombre' => '5 un - Calibre Jumbo'],
            ['conteo' => 7, 'peso' => 1.6, 'nombre' => '7 un - Calibre Mediano'],
            ['conteo' => 9, 'peso' => 1.2, 'nombre' => '9 un - Calibre Pequeño'],
        ]);

        $this->crearSpecs('Mangos', [
            ['conteo' => 6, 'peso' => 0.650, 'nombre' => '6 un - Calibre Jumbo'],
            ['conteo' => 9, 'peso' => 0.450, 'nombre' => '9 un - Calibre Grande'],
            ['conteo' => 12, 'peso' => 0.350, 'nombre' => '12 un - Calibre Mediano'],
        ]);

        $this->crearSpecs('Aguacates', [
            ['conteo' => 32, 'peso' => 0.350, 'nombre' => '32 un - Calibre Extra'],
            ['conteo' => 48, 'peso' => 0.230, 'nombre' => '48 un - Calibre Mediano'],
            ['conteo' => 60, 'peso' => 0.180, 'nombre' => '60 un - Calibre Comercial'],
        ]);

        $this->crearSpecs('Melones', [
            ['conteo' => 4, 'peso' => 3.5, 'nombre' => '4 un - Jumbo'],
            ['conteo' => 6, 'peso' => 2.2, 'nombre' => '6 un - Standard'],
            ['conteo' => 8, 'peso' => 1.5, 'nombre' => '8 un - Small'],
        ]);

        $this->crearSpecs('Limones', [
            ['conteo' => 115, 'peso' => 0.160, 'nombre' => '115 un - Calibre Grande'],
            ['conteo' => 165, 'peso' => 0.110, 'nombre' => '165 un - Calibre Mediano'],
            ['conteo' => 200, 'peso' => 0.090, 'nombre' => '200 un - Calibre Pequeño'],
        ]);

        $this->crearSpecs('Sandías', [
            ['conteo' => 3, 'peso' => 5.0, 'nombre' => '3 un - Jumbo'],
            ['conteo' => 4, 'peso' => 4.0, 'nombre' => '4 un - Grande'],
            ['conteo' => 5, 'peso' => 3.0, 'nombre' => '5 un - Mediana'],
        ]);

        $this->crearSpecs('Papayas', [
            ['conteo' => 8, 'peso' => 1.5, 'nombre' => '8 un - Calibre A'],
            ['conteo' => 10, 'peso' => 1.2, 'nombre' => '10 un - Calibre B'],
        ]);

        $this->crearSpecs('Peras', [
            ['conteo' => 40, 'peso' => 0.250, 'nombre' => '40 un - Calibre Grande'],
            ['conteo' => 50, 'peso' => 0.200, 'nombre' => '50 un - Calibre Mediano'],
        ]);

        $this->crearSpecs('Duraznos', [
            ['conteo' => 30, 'peso' => 0.180, 'nombre' => '30 un - Calibre Grande'],
            ['conteo' => 40, 'peso' => 0.140, 'nombre' => '40 un - Calibre Mediano'],
        ]);

        $this->crearSpecs('Kiwis', [
            ['conteo' => 36, 'peso' => 0.110, 'nombre' => '36 un - Calibre 36'],
            ['conteo' => 42, 'peso' => 0.095, 'nombre' => '42 un - Calibre 42'],
        ]);

        $this->crearSpecs('Cerezas', [
            ['conteo' => 10, 'peso' => 0.500, 'nombre' => '10 Pouches 500g'], // Venta por bolsa
            ['conteo' => 1, 'peso' => 5.000, 'nombre' => 'Caja Bulk 5kg'],
        ]);

        $this->crearSpecs('Mandarinas', [
            ['conteo' => 60, 'peso' => 0.150, 'nombre' => '60 un - Calibre 1'],
            ['conteo' => 75, 'peso' => 0.120, 'nombre' => '75 un - Calibre 2'],
        ]);

        $this->crearSpecs('Pomelos', [
            ['conteo' => 32, 'peso' => 0.450, 'nombre' => '32 un - Calibre 32'],
            ['conteo' => 40, 'peso' => 0.380, 'nombre' => '40 un - Calibre 40'],
        ]);

        $this->crearSpecs('Ciruelas', [
            ['conteo' => 50, 'peso' => 0.100, 'nombre' => '50 un - Calibre Grande'],
            ['conteo' => 70, 'peso' => 0.070, 'nombre' => '70 un - Calibre Mediano'],
        ]);

        $this->crearSpecs('Higos', [
            ['conteo' => 20, 'peso' => 0.050, 'nombre' => '20 un - Bandeja'],
        ]);


        // === VERDURAS (20) ===

        $this->crearSpecs('Tomates', [
            ['conteo' => 40, 'peso' => 0.280, 'nombre' => 'XL (5x6)'],
            ['conteo' => 50, 'peso' => 0.220, 'nombre' => 'L (6x6)'],
        ]);

        $this->crearSpecs('Lechugas', [
            ['conteo' => 24, 'peso' => 0.800, 'nombre' => '24 cabezas - Estándar'],
            ['conteo' => 30, 'peso' => 0.600, 'nombre' => '30 cabezas - Pequeña'],
        ]);

        $this->crearSpecs('Zanahorias', [
            ['conteo' => 1, 'peso' => 22.68, 'nombre' => 'Saco Jumbo 50lb'],
            ['conteo' => 48, 'peso' => 0.454, 'nombre' => '48 Bolsas 1lb'],
        ]);

        $this->crearSpecs('Cebollas', [
            ['conteo' => 1, 'peso' => 22.68, 'nombre' => 'Saco 50lb'],
            ['conteo' => 25, 'peso' => 0.90, 'nombre' => '25 Mallas 2lb'],
        ]);

        $this->crearSpecs('Papas', [
            ['conteo' => 1, 'peso' => 45.0, 'nombre' => 'Saco 45kg - Papas Grandes'],
            ['conteo' => 1, 'peso' => 25.0, 'nombre' => 'Saco 25kg - Papas Medianas'],
        ]);

        $this->crearSpecs('Pimientos', [
            ['conteo' => 40, 'peso' => 0.250, 'nombre' => 'XL - Caja 10kg'],
            ['conteo' => 60, 'peso' => 0.160, 'nombre' => 'L - Caja 10kg'],
        ]);

        $this->crearSpecs('Pepinos', [
            ['conteo' => 24, 'peso' => 0.400, 'nombre' => 'Select - Caja'],
        ]);

        $this->crearSpecs('Brócoli', [
            ['conteo' => 14, 'peso' => 0.500, 'nombre' => '14 Coronas - Caja'],
        ]);

        $this->crearSpecs('Coliflor', [
            ['conteo' => 12, 'peso' => 1.0, 'nombre' => '12 Cabezas - Mediana'],
        ]);

        $this->crearSpecs('Espárragos', [
            ['conteo' => 11, 'peso' => 0.450, 'nombre' => '11 Atados - 1lb Standard'],
        ]);

        $this->crearSpecs('Espinacas', [
            ['conteo' => 12, 'peso' => 0.300, 'nombre' => '12 Manojos - Grande'],
        ]);

        $this->crearSpecs('Acelgas', [
            ['conteo' => 12, 'peso' => 0.400, 'nombre' => '12 Manojos - Grande'],
        ]);

        $this->crearSpecs('Calabacines', [
            ['conteo' => 30, 'peso' => 0.300, 'nombre' => '30 un - Caja Mediana'],
        ]);

        $this->crearSpecs('Berenjenas', [
            ['conteo' => 18, 'peso' => 0.450, 'nombre' => '18 un - Caja'],
            ['conteo' => 24, 'peso' => 0.350, 'nombre' => '24 un - Caja'],
        ]);

        $this->crearSpecs('Apio', [
            ['conteo' => 24, 'peso' => 0.600, 'nombre' => '24 un - Caja Standard'],
        ]);

        $this->crearSpecs('Rábanos', [
            ['conteo' => 30, 'peso' => 0.200, 'nombre' => '30 Manojos'],
        ]);

        $this->crearSpecs('Betarragas', [
            ['conteo' => 24, 'peso' => 0.300, 'nombre' => '24 Manojos'],
        ]);

        $this->crearSpecs('Repollo', [
            ['conteo' => 12, 'peso' => 1.5, 'nombre' => '12 Cabezas - Saco'],
        ]);

        $this->crearSpecs('Ajos', [
            ['conteo' => 1, 'peso' => 10.0, 'nombre' => 'Caja Bulk 10kg'],
        ]);

        $this->crearSpecs('Ejotes', [
            ['conteo' => 1, 'peso' => 12.0, 'nombre' => 'Caja Bulk 12kg'],
        ]);

    }

    /**
     * Helper para crear especificaciones masivamente.
     */
    private function crearSpecs($nombreProducto, $listaSpecs)
    {
        // Buscar producto por nombre (o LIKE)
        $producto = CatalogoProducto::where('nombre', 'like', $nombreProducto . '%')->first();

        if ($producto) {
            foreach ($listaSpecs as $spec) {
                CatalogoTamanoConteo::firstOrCreate(
                    [
                        'id_producto' => $producto->id,
                        'conteo_por_empaque' => $spec['conteo'],
                        'peso_promedio_unidad' => $spec['peso']
                    ],
                    [
                        'nombre' => $spec['nombre'],
                        'activo' => true
                    ]
                );
            }
        }
    }
}