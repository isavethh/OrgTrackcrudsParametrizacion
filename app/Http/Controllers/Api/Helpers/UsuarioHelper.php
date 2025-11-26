<?php

namespace App\Http\Controllers\Api\Helpers;

use App\Models\Usuario;
use App\Models\RolesUsuario;

class UsuarioHelper
{
    /**
     * Obtener datos completos del usuario desde el request
     */
    public static function obtenerUsuarioCompleto(array $usuarioRequest): ?array
    {
        $usuario = Usuario::with(['rol', 'persona'])->find($usuarioRequest['id']);
        
        if (!$usuario) {
            return null;
        }

        return [
            'id' => $usuario->id,
            'correo' => $usuario->correo,
            'rol' => $usuario->rol?->codigo ?? $usuarioRequest['rol'] ?? 'cliente',
            'rol_nombre' => $usuario->rol?->nombre,
            'nombre' => $usuario->persona?->nombre,
            'apellido' => $usuario->persona?->apellido,
            'ci' => $usuario->persona?->ci,
            'telefono' => $usuario->persona?->telefono,
        ];
    }

    /**
     * Obtener rol del usuario (usando codigo del rol)
     */
    public static function obtenerRolUsuario(int $idUsuario): ?string
    {
        $usuario = Usuario::with('rol')->find($idUsuario);
        return $usuario?->rol?->codigo;
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public static function tieneRol(array $usuarioRequest, string $rol): bool
    {
        $rolUsuario = self::obtenerRolUsuario($usuarioRequest['id']);
        return $rolUsuario === $rol;
    }

    /**
     * Obtener o crear rol por código
     */
    public static function obtenerRolPorCodigo(string $codigo): int
    {
        $rol = RolesUsuario::firstOrCreate(
            ['codigo' => $codigo],
            ['nombre' => ucfirst($codigo), 'descripcion' => null]
        );
        return $rol->id;
    }
}

