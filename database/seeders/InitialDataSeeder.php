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

        // 7. Unidades de medida
        $this->seedUnidadesMedida();

        // 8. Motivos de cancelación
        $this->seedMotivosCancelacion();

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
            ['nombre' => 'No Disponible'],
            ['nombre' => 'En uso'],
            ['nombre' => 'En ruta'],
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
            ['nombre' => 'No Disponible'],
            ['nombre' => 'En viaje'],
            ['nombre' => 'En ruta'],
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
            ['nombre' => 'Asignado'],
            ['nombre' => 'En curso'],
            ['nombre' => 'Entregado'],
            ['nombre' => 'Parcialmente entregado'],
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
            ['nombre' => 'Entregado'],
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

    private function seedUnidadesMedida()
    {
        $unidades = [
            ['codigo' => 'KG', 'nombre' => 'Kilogramo', 'tipo' => 'peso', 'descripcion' => 'Unidad de peso'],
            ['codigo' => 'TON', 'nombre' => 'Tonelada', 'tipo' => 'peso', 'descripcion' => 'Unidad de peso equivalente a 1000 kg'],
            ['codigo' => 'GR', 'nombre' => 'Gramo', 'tipo' => 'peso', 'descripcion' => 'Unidad de peso'],
            ['codigo' => 'UND', 'nombre' => 'Unidad', 'tipo' => 'cantidad', 'descripcion' => 'Unidad individual'],
            ['codigo' => 'CAJA', 'nombre' => 'Caja', 'tipo' => 'cantidad', 'descripcion' => 'Caja de empaque'],
            ['codigo' => 'SACO', 'nombre' => 'Saco', 'tipo' => 'cantidad', 'descripcion' => 'Saco de empaque'],
            ['codigo' => 'LT', 'nombre' => 'Litro', 'tipo' => 'volumen', 'descripcion' => 'Unidad de volumen'],
            ['codigo' => 'ML', 'nombre' => 'Mililitro', 'tipo' => 'volumen', 'descripcion' => 'Unidad de volumen'],
        ];

        foreach ($unidades as $unidad) {
            DB::table('unidades_medida')->updateOrInsert(
                ['codigo' => $unidad['codigo']],
                $unidad
            );
        }

        $this->command->info('  ✓ Unidades de medida');
    }

    private function seedMotivosCancelacion()
    {
        $motivos = [
            ['codigo' => 'PROD_DAÑADO', 'titulo' => 'Producto dañado', 'descripcion' => 'El producto se encuentra en mal estado', 'activo' => true],
            ['codigo' => 'CLIENTE_RECHAZA', 'titulo' => 'Cliente rechaza el envío', 'descripcion' => 'El cliente no acepta el pedido', 'activo' => true],
            ['codigo' => 'SIN_TRANSPORTE', 'titulo' => 'Sin transporte disponible', 'descripcion' => 'No hay vehículos disponibles', 'activo' => true],
            ['codigo' => 'CLIMA_ADVERSO', 'titulo' => 'Condiciones climáticas adversas', 'descripcion' => 'Mal tiempo impide el transporte', 'activo' => true],
            ['codigo' => 'ERROR_DIRECCION', 'titulo' => 'Error en dirección', 'descripcion' => 'La dirección es incorrecta o inaccesible', 'activo' => true],
            ['codigo' => 'FALTA_PAGO', 'titulo' => 'Falta de pago', 'descripcion' => 'No se ha completado el pago', 'activo' => true],
            ['codigo' => 'OTRO', 'titulo' => 'Otro motivo', 'descripcion' => 'Motivo no especificado', 'activo' => true],
        ];

        foreach ($motivos as $motivo) {
            DB::table('motivos_cancelacion')->updateOrInsert(
                ['codigo' => $motivo['codigo']],
                $motivo
            );
        }

        $this->command->info('  ✓ Motivos de cancelación');
    }
}
