<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        // Roles de Usuario
        DB::table('roles_usuario')->insert([
            ['codigo' => 'ADMIN', 'nombre' => 'Administrador', 'descripcion' => 'Usuario administrador del sistema'],
            ['codigo' => 'CLIENT', 'nombre' => 'Cliente', 'descripcion' => 'Usuario cliente'],
            ['codigo' => 'TRANS', 'nombre' => 'Transportista', 'descripcion' => 'Usuario transportista'],
        ]);

        // Tipos de Vehículo
        DB::table('tipos_vehiculo')->insert([
            ['nombre' => 'Camión', 'descripcion' => 'Vehículo de carga pesada'],
            ['nombre' => 'Camioneta', 'descripcion' => 'Vehículo de carga ligera'],
            ['nombre' => 'Furgoneta', 'descripcion' => 'Vehículo de carga mediana'],
            ['nombre' => 'Motocicleta', 'descripcion' => 'Vehículo de carga pequeña'],
        ]);

        // Estados de Vehículo
        DB::table('estados_vehiculo')->insert([
            ['nombre' => 'Disponible'],
            ['nombre' => 'En ruta'],
            ['nombre' => 'Mantenimiento'],
            ['nombre' => 'No Disponible'],
        ]);

        // Estados de Transportista
        DB::table('estados_transportista')->insert([
            ['nombre' => 'Disponible'],
            ['nombre' => 'En ruta'],
            ['nombre' => 'No Disponible'],
            ['nombre' => 'Inactivo'],
        ]);

        // Estados de Envío
        DB::table('estados_envio')->insert([
            ['nombre' => 'Pendiente'],
            ['nombre' => 'En curso'],
            ['nombre' => 'En tránsito'],
            ['nombre' => 'Entregado'],
            ['nombre' => 'Cancelado'],
        ]);

        // Estados de Asignación Múltiple
        DB::table('estados_asignacion_multiple')->insert([
            ['nombre' => 'Pendiente'],
            ['nombre' => 'En curso'],
            ['nombre' => 'Completada'],
            ['nombre' => 'Cancelada'],
        ]);

        // Tipo de Transporte
        DB::table('tipotransporte')->insert([
            ['nombre' => 'Terrestre', 'descripcion' => 'Transporte por carretera'],
            ['nombre' => 'Aéreo', 'descripcion' => 'Transporte por avión'],
            ['nombre' => 'Marítimo', 'descripcion' => 'Transporte por barco'],
        ]);

        // Catálogo de Carga
        DB::table('catalogo_carga')->insert([
            ['tipo' => 'Frutas', 'variedad' => 'Manzanas', 'empaque' => 'Cajas', 'descripcion' => 'Frutas frescas - Manzanas en cajas'],
            ['tipo' => 'Frutas', 'variedad' => 'Naranjas', 'empaque' => 'Sacos', 'descripcion' => 'Frutas frescas - Naranjas en sacos'],
            ['tipo' => 'Verduras', 'variedad' => 'Lechugas', 'empaque' => 'Cajas', 'descripcion' => 'Verduras frescas - Lechugas en cajas'],
            ['tipo' => 'Verduras', 'variedad' => 'Tomates', 'empaque' => 'Cajas', 'descripcion' => 'Verduras frescas - Tomates en cajas'],
            ['tipo' => 'Cereales', 'variedad' => 'Arroz', 'empaque' => 'Sacos', 'descripcion' => 'Cereales - Arroz en sacos'],
            ['tipo' => 'Cereales', 'variedad' => 'Maíz', 'empaque' => 'Sacos', 'descripcion' => 'Cereales - Maíz en sacos'],
        ]);

        // Condiciones de Transporte
        DB::table('condiciones_transporte')->insert([
            ['codigo' => 'COND001', 'titulo' => 'Vehículo limpio', 'descripcion' => 'El vehículo debe estar limpio y sin residuos'],
            ['codigo' => 'COND002', 'titulo' => 'Temperatura adecuada', 'descripcion' => 'La temperatura debe estar dentro del rango permitido'],
            ['codigo' => 'COND003', 'titulo' => 'Embalaje correcto', 'descripcion' => 'La carga debe tener el embalaje adecuado'],
            ['codigo' => 'COND004', 'titulo' => 'Documentación completa', 'descripcion' => 'Todos los documentos deben estar presentes'],
        ]);

        // Tipos de Incidente de Transporte
        DB::table('tipos_incidente_transporte')->insert([
            ['codigo' => 'INC001', 'titulo' => 'Retraso', 'descripcion' => 'Retraso en la entrega'],
            ['codigo' => 'INC002', 'titulo' => 'Daño en carga', 'descripcion' => 'Daño en la carga durante el transporte'],
            ['codigo' => 'INC003', 'titulo' => 'Accidente', 'descripcion' => 'Accidente de tránsito'],
            ['codigo' => 'INC004', 'titulo' => 'Falla mecánica', 'descripcion' => 'Falla mecánica del vehículo'],
            ['codigo' => 'INC005', 'titulo' => 'Carga incompleta', 'descripcion' => 'Falta parte de la carga'],
        ]);

        // Estados de QR Token
        DB::table('estados_qrtoken')->insert([
            ['nombre' => 'Activo'],
            ['nombre' => 'Usado'],
            ['nombre' => 'Expirado'],
            ['nombre' => 'Cancelado'],
        ]);
    }
}
