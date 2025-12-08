<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Http\Controllers\Api\Helpers\SpatieHelper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesWebController extends Controller
{
    /**
     * Mostrar gestión de roles (solo admin)
     * Usa Spatie para verificar y gestionar roles
     */
    public function index()
    {
        try {
            $usuarioId = session('usuario_id');
            $usuario = Usuario::find($usuarioId);

            if (!$usuario) {
                return redirect()->route('login')->with('error', 'Debes iniciar sesión');
            }

            // Verificar rol usando Spatie (con fallback)
            if (!SpatieHelper::tieneRol($usuario, 'admin')) {
                abort(403, 'Solo administradores pueden acceder a esta sección');
            }

            // Obtener todos los roles usando Spatie (si está disponible)
            $roles = [];
            try {
                $roles = Role::all();
            } catch (\Throwable $e) {
                // Si Spatie no está activo, obtener de la tabla tradicional
                $roles = \App\Models\RolesUsuario::all();
            }

            // Obtener todos los usuarios con sus roles
            $usuarios = Usuario::with(['rol', 'persona'])->get();
            
            // Para cada usuario, verificar si tiene rol en Spatie
            foreach ($usuarios as $usuario) {
                $usuario->rol_spatie = SpatieHelper::obtenerRolUsuario($usuario);
            }

            return view('admin.roles.index', [
                'roles' => $roles,
                'usuarios' => $usuarios,
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error al cargar roles: ' . $e->getMessage());
        }
    }

    /**
     * Asignar rol a usuario usando Spatie
     */
    public function asignarRol(Request $request, int $id)
    {
        try {
            $usuarioAdminId = session('usuario_id');
            $usuarioAdmin = Usuario::find($usuarioAdminId);

            if (!SpatieHelper::tieneRol($usuarioAdmin, 'admin')) {
                return back()->with('error', 'No tienes permisos para esta acción');
            }

            $request->validate([
                'rol' => 'required|string|in:admin,cliente,transportista',
            ]);

            $usuario = Usuario::find($id);
            if (!$usuario) {
                return back()->with('error', 'Usuario no encontrado');
            }

            // Asignar rol usando Spatie (sincroniza ambos sistemas)
            SpatieHelper::asignarRol($usuario, $request->rol);

            return back()->with('success', 'Rol asignado correctamente');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al asignar rol: ' . $e->getMessage());
        }
    }

    /**
     * Verificar roles de un usuario (demo de Spatie)
     */
    public function verificarRoles(int $id)
    {
        try {
            $usuarioAdminId = session('usuario_id');
            $usuarioAdmin = Usuario::find($usuarioAdminId);

            if (!SpatieHelper::tieneRol($usuarioAdmin, 'admin')) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $usuario = Usuario::find($id);
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Obtener rol usando Spatie (con fallback)
            $rolSpatie = SpatieHelper::obtenerRolUsuario($usuario);
            $rolTradicional = $usuario->rol?->codigo;

            // Verificar roles específicos
            $tieneAdmin = SpatieHelper::tieneRol($usuario, 'admin');
            $tieneCliente = SpatieHelper::tieneRol($usuario, 'cliente');
            $tieneTransportista = SpatieHelper::tieneRol($usuario, 'transportista');

            return response()->json([
                'usuario_id' => $usuario->id,
                'rol_spatie' => $rolSpatie,
                'rol_tradicional' => $rolTradicional,
                'verificaciones' => [
                    'es_admin' => $tieneAdmin,
                    'es_cliente' => $tieneCliente,
                    'es_transportista' => $tieneTransportista,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

