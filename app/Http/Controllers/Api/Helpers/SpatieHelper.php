<?php

namespace App\Http\Controllers\Api\Helpers;

use App\Models\Usuario;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SpatieHelper
{
    /**
     * Obtiene el rol del usuario usando Spatie si está disponible,
     * o fallback al sistema tradicional
     */
    public static function obtenerRolUsuario(Usuario $usuario): ?string
    {
        try {
            // Intentar obtener el rol desde Spatie
            $roles = $usuario->getRoleNames();
            if ($roles->isNotEmpty()) {
                return $roles->first();
            }
        } catch (\Throwable $e) {
            // Si falla, usar el sistema tradicional
        }

        // Fallback al sistema tradicional
        return $usuario->rol?->codigo;
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * Compatible con ambos sistemas (Spatie y tradicional)
     */
    public static function tieneRol(Usuario $usuario, string $rol): bool
    {
        try {
            // Intentar verificar con Spatie
            if ($usuario->hasRole($rol)) {
                return true;
            }
        } catch (\Throwable $e) {
            // Si falla, usar el sistema tradicional
        }

        // Fallback al sistema tradicional
        return $usuario->rol?->codigo === $rol;
    }

    /**
     * Asigna un rol al usuario en Spatie y sincroniza con el sistema tradicional
     */
    public static function asignarRol(Usuario $usuario, string $rolCodigo): bool
    {
        try {
            // Asignar rol en Spatie
            if (!$usuario->hasRole($rolCodigo)) {
                $usuario->assignRole($rolCodigo);
            }

            // Sincronizar con el sistema tradicional
            $rol = \App\Models\RolesUsuario::where('codigo', $rolCodigo)->first();
            if ($rol && $usuario->id_rol !== $rol->id) {
                $usuario->update(['id_rol' => $rol->id]);
            }

            return true;
        } catch (\Throwable $e) {
            // Si falla Spatie, al menos actualizar el sistema tradicional
            $rol = \App\Models\RolesUsuario::where('codigo', $rolCodigo)->first();
            if ($rol) {
                $usuario->update(['id_rol' => $rol->id]);
                return true;
            }
            return false;
        }
    }

    /**
     * Asegura que un rol existe en Spatie basado en roles_usuario
     */
    public static function asegurarRolExiste(string $rolCodigo): ?Role
    {
        try {
            $rol = Role::firstOrCreate(
                ['name' => $rolCodigo, 'guard_name' => 'web'],
                ['name' => $rolCodigo, 'guard_name' => 'web']
            );
            return $rol;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     * Compatible con ambos sistemas (Spatie y tradicional por rol)
     */
    public static function tienePermiso(Usuario $usuario, string $permiso): bool
    {
        try {
            // Intentar verificar con Spatie
            if ($usuario->hasPermissionTo($permiso)) {
                return true;
            }
        } catch (\Throwable $e) {
            // Si falla, usar verificación por rol (fallback)
        }

        // Fallback: Verificar si el rol del usuario tiene el permiso
        // O simplemente verificar por rol si el permiso no existe
        $rol = $usuario->rol?->codigo;
        
        // Mapeo de permisos a roles (fallback si Spatie no tiene el permiso)
        $permisosPorRol = [
            'admin' => ['todos'], // Admin tiene todos los permisos
            'cliente' => ['crear envios', 'ver envios', 'crear firmas', 'ver firmas'],
            'transportista' => ['ver envios', 'asignar transportista', 'generar qr', 'validar qr', 'crear firmas', 'ver firmas'],
        ];

        // Si es admin, tiene todos los permisos
        if ($rol === 'admin') {
            return true;
        }

        // Verificar si el permiso está en la lista del rol
        $permisosDelRol = $permisosPorRol[$rol] ?? [];
        return in_array($permiso, $permisosDelRol);
    }

    /**
     * Obtiene todos los permisos del usuario (desde Spatie o fallback)
     */
    public static function obtenerPermisos(Usuario $usuario): array
    {
        try {
            // Intentar obtener desde Spatie
            $permisos = $usuario->getAllPermissions()->pluck('name')->toArray();
            if (!empty($permisos)) {
                return $permisos;
            }
        } catch (\Throwable $e) {
            // Si falla, usar fallback
        }

        // Fallback: Retornar permisos según el rol
        $rol = $usuario->rol?->codigo;
        $permisosPorRol = [
            'admin' => ['todos los permisos'],
            'cliente' => ['crear envios', 'ver envios', 'crear firmas', 'ver firmas'],
            'transportista' => ['ver envios', 'asignar transportista', 'generar qr', 'validar qr', 'crear firmas', 'ver firmas'],
        ];

        return $permisosPorRol[$rol] ?? [];
    }
}

