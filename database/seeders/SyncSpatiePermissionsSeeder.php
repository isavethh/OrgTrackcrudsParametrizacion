<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SyncSpatiePermissionsSeeder extends Seeder
{
    /**
     * Crea permisos y los asigna a roles según las acciones del sistema
     * 
     * Ejecutar con: php artisan db:seed --class=SyncSpatiePermissionsSeeder
     */
    public function run(): void
    {
        $this->command->info('Creando permisos y asignándolos a roles...');

        // 1. Crear todos los permisos del sistema
        $permisos = $this->crearPermisos();
        
        // 2. Asignar permisos a roles
        $this->asignarPermisosARoles($permisos);

        $this->command->info('✓ Permisos creados y asignados correctamente');
    }

    private function crearPermisos(): array
    {
        $permisos = [];

        // Permisos de Envíos
        $permisos['envios'] = [
            'crear envios',
            'editar envios',
            'ver envios',
            'ver todos envios',
            'eliminar envios',
            'cancelar envios',
            'asignar transportista',
            'generar documento envio',
        ];

        // Permisos de Usuarios
        $permisos['usuarios'] = [
            'crear usuarios',
            'editar usuarios',
            'ver usuarios',
            'ver todos usuarios',
            'eliminar usuarios',
            'cambiar rol usuarios',
        ];

        // Permisos de Vehículos
        $permisos['vehiculos'] = [
            'crear vehiculos',
            'editar vehiculos',
            'ver vehiculos',
            'eliminar vehiculos',
        ];

        // Permisos de Transportistas
        $permisos['transportistas'] = [
            'crear transportistas',
            'editar transportistas',
            'ver transportistas',
            'eliminar transportistas',
        ];

        // Permisos de QR
        $permisos['qr'] = [
            'generar qr',
            'validar qr',
        ];

        // Permisos de Firmas
        $permisos['firmas'] = [
            'crear firmas',
            'ver firmas',
        ];

        // Permisos de Catálogos (solo admin)
        $permisos['catalogos'] = [
            'gestionar condiciones transporte',
            'gestionar tipos incidente',
            'gestionar tipos transporte',
            'gestionar tipos vehiculo',
            'gestionar catalogo carga',
            'gestionar unidades medida',
        ];

        // Crear todos los permisos
        $permisosCreados = [];
        foreach ($permisos as $categoria => $lista) {
            foreach ($lista as $permiso) {
                $permisoCreado = Permission::firstOrCreate(
                    ['name' => $permiso, 'guard_name' => 'web'],
                    ['name' => $permiso, 'guard_name' => 'web']
                );
                $permisosCreados[$permiso] = $permisoCreado;
                $this->command->line("  ✓ Permiso creado: {$permiso}");
            }
        }

        return $permisosCreados;
    }

    private function asignarPermisosARoles(array $permisos): void
    {
        // Obtener roles
        $rolAdmin = Role::findByName('admin', 'web');
        $rolCliente = Role::findByName('cliente', 'web');
        $rolTransportista = Role::findByName('transportista', 'web');

        // ADMIN: Tiene todos los permisos
        $permisosAdmin = array_keys($permisos);
        $rolAdmin->syncPermissions($permisosAdmin);
        $this->command->info("  ✓ Permisos asignados a ADMIN: " . count($permisosAdmin));

        // CLIENTE: Solo puede crear/ver sus propios envíos
        $permisosCliente = [
            'crear envios',
            'ver envios',
            'crear firmas',
            'ver firmas',
        ];
        $rolCliente->syncPermissions($permisosCliente);
        $this->command->info("  ✓ Permisos asignados a CLIENTE: " . count($permisosCliente));

        // TRANSPORTISTA: Puede gestionar asignaciones y QR
        $permisosTransportista = [
            'ver envios',
            'asignar transportista',
            'generar qr',
            'validar qr',
            'crear firmas',
            'ver firmas',
        ];
        $rolTransportista->syncPermissions($permisosTransportista);
        $this->command->info("  ✓ Permisos asignados a TRANSPORTISTA: " . count($permisosTransportista));
    }
}



