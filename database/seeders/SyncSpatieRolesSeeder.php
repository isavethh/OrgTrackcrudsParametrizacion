<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Usuario;

class SyncSpatieRolesSeeder extends Seeder
{
    /**
     * Sincroniza los roles existentes de roles_usuario a Spatie Permission
     * y asigna los roles a los usuarios existentes.
     * 
     * Ejecutar con: php artisan db:seed --class=SyncSpatieRolesSeeder
     */
    public function run(): void
    {
        $this->command->info('Sincronizando roles con Spatie Permission...');

        // Obtener todos los roles de la tabla roles_usuario
        $rolesExistentes = DB::table('roles_usuario')->get();

        if ($rolesExistentes->isEmpty()) {
            $this->command->warn('No se encontraron roles en la tabla roles_usuario.');
            return;
        }

        // Crear roles en Spatie basados en los roles existentes
        foreach ($rolesExistentes as $rolExistente) {
            // Crear o actualizar el rol en Spatie usando el código como nombre
            $role = Role::firstOrCreate(
                ['name' => $rolExistente->codigo, 'guard_name' => 'web'],
                ['name' => $rolExistente->codigo, 'guard_name' => 'web']
            );

            $this->command->info("  ✓ Rol sincronizado: {$rolExistente->codigo}");

            // Asignar el rol a todos los usuarios que tengan este rol en la tabla usuarios
            $usuariosConRol = Usuario::where('id_rol', $rolExistente->id)->get();

            foreach ($usuariosConRol as $usuario) {
                // Verificar si el usuario ya tiene el rol en Spatie
                try {
                    if (!$usuario->hasRole($rolExistente->codigo)) {
                        $usuario->assignRole($rolExistente->codigo);
                        $this->command->line("    → Rol asignado a usuario ID: {$usuario->id}");
                    }
                } catch (\Throwable $e) {
                    $this->command->warn("    ⚠ Error al asignar rol a usuario ID {$usuario->id}: " . $e->getMessage());
                }
            }
        }

        $this->command->info('✓ Sincronización de roles completada');
    }
}

