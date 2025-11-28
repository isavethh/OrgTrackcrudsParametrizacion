<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     *  php artisan db:seed --class=AdminSeeder
     * 
     */
    public function run(): void
    {
        // Obtener el ID del rol 'admin'
        $rolAdmin = DB::table('roles_usuario')
            ->where('codigo', 'admin')
            ->first();

        if (!$rolAdmin) {
            $this->command->error('El rol "admin" no existe. Ejecuta primero los seeders de roles.');
            return;
        }

        $admins = [
            [
                'nombre' => 'Mauricio',
                'apellido' => 'Martinez',
                'ci' => '1727362',
                'telefono' => '7558698',
                'correo' => 'maurimar@gmail.com',
                'contrasena' => 'mauricio123',
                'nivel_acceso' => 3
            ],
            [
                'nombre' => 'admin',
                'apellido' => 'Administrador',
                'ci' => '1726263',
                'telefono' => '762458699',
                'correo' => 'admin@gmail.com',
                'contrasena' => 'admin123',
                'nivel_acceso' => 3
            ],
            [
                'nombre' => 'admin2',
                'apellido' => 'Administrador2',
                'ci' => '17272411',
                'telefono' => '75585322',
                'correo' => 'admin2@gmail.com',
                'contrasena' => 'admin2004',
                'nivel_acceso' => 3
            ],
        ];

        foreach ($admins as $adminData) {
            // Verificar si el correo ya existe
            $existeCorreo = DB::table('usuarios')
                ->where('correo', $adminData['correo'])
                ->exists();

            if ($existeCorreo) {
                $this->command->warn("El usuario {$adminData['correo']} ya existe. Omitiendo...");
                continue;
            }

            // Verificar si el CI ya existe
            $existeCI = DB::table('persona')
                ->where('ci', $adminData['ci'])
                ->exists();

            if ($existeCI) {
                $this->command->warn("El CI {$adminData['ci']} ya existe. Omitiendo...");
                continue;
            }

            DB::transaction(function () use ($adminData, $rolAdmin) {
                // 1. Crear la persona
                $idPersona = DB::table('persona')->insertGetId([
                    'nombre' => $adminData['nombre'],
                    'apellido' => $adminData['apellido'],
                    'ci' => $adminData['ci'],
                    'telefono' => $adminData['telefono'],
                ]);

                // 2. Crear el usuario
                $idUsuario = DB::table('usuarios')->insertGetId([
                    'correo' => $adminData['correo'],
                    'contrasena' => Hash::make($adminData['contrasena']),
                    'id_rol' => $rolAdmin->id,
                    'fecha_registro' => now(),
                    'id_persona' => $idPersona,
                ]);

                // 3. Crear el registro en la tabla admin
                DB::table('admin')->insert([
                    'id_usuario' => $idUsuario,
                    'nivel_acceso' => $adminData['nivel_acceso'],
                ]);

                $this->command->info("✓ Admin creado: {$adminData['correo']} (Contraseña: {$adminData['contrasena']})");
            });
        }

        $this->command->info('');
        $this->command->info('════════════════════════════════════════');
        $this->command->info('  CUENTAS DE ADMINISTRADOR CREADAS  ');
        $this->command->info('════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('maurimar@gmail.com');
        $this->command->info('Contraseña: mauricio123');
        $this->command->info('Nivel: 3 - Mauricio Martinez');
        $this->command->info('');
        $this->command->info('admin@gmail.com');
        $this->command->info('Contraseña: admin123');
        $this->command->info('Nivel: 3 - admin Administrador');
        $this->command->info('');
        $this->command->info('admin2@gmail.com');
        $this->command->info('Contraseña: admin2004');
        $this->command->info('Nivel: 3 - admin2 Administrador2');
        $this->command->info('');
        $this->command->info('════════════════════════════════════════');
    }
}
