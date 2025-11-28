<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando datos iniciales del sistema...');

        // 1. Roles de usuario
        $this->seedRoles();

        // 2. Estados de vehículos
        $this->seedEstadosVehiculo();

        // 3. Estados de transportistas
        $this->seedEstadosTransportista();

        // 4. Estados de envíos
        $this->seedEstadosEnvio();

        // 5. Estados de asignación
        $this->seedEstadosAsignacion();

        // 6. Estados de QR Token
        $this->seedEstadosQrToken();

        $this->command->info('✓ Datos iniciales creados correctamente');
    }

    private function seedRoles()
    {
        $roles = [
            ['codigo' => 'admin', 'nombre' => 'Administrador', 'descripcion' => 'Usuario con acceso completo al sistema'],
            ['codigo' => 'cliente', 'nombre' => 'Cliente', 'descripcion' => 'Usuario que solicita envíos'],
            ['codigo' => 'transportista', 'nombre' => 'Transportista', 'descripcion' => 'Usuario que realiza transportes'],
        ];

        foreach ($roles as $rol) {
            DB::table('roles_usuario')->updateOrInsert(
                ['codigo' => $rol['codigo']],
                $rol
            );
        }

        $this->command->info('  ✓ Roles de usuario');
    }

    private function seedEstadosVehiculo()
    {
        $estados = [
            ['nombre' => 'Disponible'],
            ['nombre' => 'En uso'],
            ['nombre' => 'En mantenimiento'],
            ['nombre' => 'Fuera de servicio'],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_vehiculo')->updateOrInsert($estado, $estado);
        }

        $this->command->info('  ✓ Estados de vehículo');
    }

    private function seedEstadosTransportista()
    {
        $estados = [
            ['nombre' => 'Disponible'],
            ['nombre' => 'En viaje'],
            ['nombre' => 'Ocupado'],
            ['nombre' => 'Inactivo'],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_transportista')->updateOrInsert($estado, $estado);
        }

        $this->command->info('  ✓ Estados de transportista');
    }

    private function seedEstadosEnvio()
    {
        $estados = [
            ['nombre' => 'Pendiente'],
            ['nombre' => 'En preparación'],
            ['nombre' => 'En tránsito'],
            ['nombre' => 'Entregado'],
            ['nombre' => 'Cancelado'],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_envio')->updateOrInsert($estado, $estado);
        }

        $this->command->info('  ✓ Estados de envío');
    }

    private function seedEstadosAsignacion()
    {
        $estados = [
            ['nombre' => 'Pendiente'],
            ['nombre' => 'Asignada'],
            ['nombre' => 'En curso'],
            ['nombre' => 'Completada'],
            ['nombre' => 'Cancelada'],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_asignacion_multiple')->updateOrInsert($estado, $estado);
        }

        $this->command->info('  ✓ Estados de asignación');
    }

    private function seedEstadosQrToken()
    {
        $estados = [
            ['nombre' => 'Activo'],
            ['nombre' => 'Usado'],
            ['nombre' => 'Expirado'],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_qrtoken')->updateOrInsert($estado, $estado);
        }

        $this->command->info('  ✓ Estados de QR Token');
    }
}
