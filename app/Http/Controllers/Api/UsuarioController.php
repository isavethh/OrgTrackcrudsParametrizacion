<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\RolesUsuario;
use App\Models\Transportista;
use App\Models\Admin;
use App\Models\Cliente;
use App\Http\Controllers\Api\Helpers\UsuarioHelper;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos(): JsonResponse
    {
        try {
            $usuarios = Usuario::with(['persona', 'rol'])->get();
            $usuarios = $usuarios->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'correo' => $usuario->correo,
                    'fecha_registro' => $usuario->fecha_registro,
                    'nombre' => $usuario->persona?->nombre,
                    'apellido' => $usuario->persona?->apellido,
                    'ci' => $usuario->persona?->ci,
                    'telefono' => $usuario->persona?->telefono,
                    'rol' => $usuario->rol?->codigo,
                    'rol_nombre' => $usuario->rol?->nombre,
                ];
            });
            return response()->json($usuarios);
        } catch (\Exception $e) {
            \Log::error('Error al obtener usuarios: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener usuarios'], 500);
        }
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId(int $id): JsonResponse
    {
        try {
            $usuario = Usuario::with(['persona', 'rol'])->find($id);

            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            return response()->json([
                'id' => $usuario->id,
                'correo' => $usuario->correo,
                'fecha_registro' => $usuario->fecha_registro,
                'nombre' => $usuario->persona?->nombre,
                'apellido' => $usuario->persona?->apellido,
                'ci' => $usuario->persona?->ci,
                'telefono' => $usuario->persona?->telefono,
                'rol' => $usuario->rol?->codigo,
                'rol_nombre' => $usuario->rol?->nombre,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al obtener usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener usuario'], 500);
        }
    }

    /**
     * Editar usuario (nombre, apellido, correo, rol)
     */
    public function editar(Request $request, int $id): JsonResponse
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'correo' => 'required|email|max:100|unique:usuarios,correo,' . $id,
                'ci' => 'nullable|string|max:20',
                'telefono' => 'nullable|string|max:20',
                'rol' => 'required|string|in:transportista,cliente,admin'
            ]);

            return DB::transaction(function () use ($id, $request) {
                $usuario = Usuario::with('persona')->find($id);

                if (!$usuario) {
                    return response()->json(['error' => 'Usuario no encontrado'], 404);
                }

                // Actualizar persona
                if ($usuario->persona) {
                    $usuario->persona->update([
                        'nombre' => $request->nombre,
                        'apellido' => $request->apellido,
                        'ci' => $request->ci ?? $usuario->persona->ci,
                        'telefono' => $request->telefono ?? $usuario->persona->telefono,
                    ]);
                }

                // Actualizar usuario
                $idRol = UsuarioHelper::obtenerRolPorCodigo($request->rol);
                $usuario->update([
                    'correo' => $request->correo,
                    'id_rol' => $idRol,
                ]);

                return response()->json(['mensaje' => 'Usuario actualizado correctamente']);
            });
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al editar usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al editar usuario'], 500);
        }
    }

    /**
     * Eliminar usuario
     */
    public function eliminar(int $id): JsonResponse
    {
        try {
            $usuario = Usuario::find($id);

            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $usuario->delete();

            return response()->json(['mensaje' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            \Log::error('Error al eliminar usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar usuario'], 500);
        }
    }

    /**
     * Obtener todos los clientes
     */
    public function obtenerClientes(): JsonResponse
    {
        try {
            $idRolCliente = UsuarioHelper::obtenerRolPorCodigo('cliente');
            $clientes = Usuario::with(['persona', 'rol'])
                ->where('id_rol', $idRolCliente)
                ->get();

            $clientes = $clientes->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'nombre' => $usuario->persona?->nombre,
                    'apellido' => $usuario->persona?->apellido,
                    'correo' => $usuario->correo,
                    'telefono' => $usuario->persona?->telefono,
                    'ci' => $usuario->persona?->ci,
                ];
            });

            return response()->json($clientes);
        } catch (\Exception $e) {
            \Log::error('Error al obtener clientes: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener clientes'], 500);
        }
    }

    /**
     * Crear nuevo usuario
     */
    public function crear(Request $request): JsonResponse
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'correo' => 'required|email|max:100|unique:usuarios,correo',
                'contrasena' => 'required|string|min:6',
                'ci' => 'required|string|max:20|unique:persona,ci',
                'telefono' => 'nullable|string|max:20',
                'rol' => 'required|string|in:transportista,cliente,admin'
            ]);

            return DB::transaction(function () use ($request) {
                // Crear persona primero
                $persona = Persona::create([
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'ci' => $request->ci,
                    'telefono' => $request->telefono ?? null,
                ]);

                // Obtener rol
                $idRol = UsuarioHelper::obtenerRolPorCodigo($request->rol);

                // Crear usuario
                $usuario = Usuario::create([
                    'correo' => $request->correo,
                    'contrasena' => Hash::make($request->contrasena),
                    'id_rol' => $idRol,
                    'id_persona' => $persona->id,
                ]);

                // Si es transportista, crear registro en transportistas
                if ($request->rol === 'transportista') {
                    $idEstadoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('Disponible');
                    Transportista::create([
                        'id_usuario' => $usuario->id,
                        'id_estado_transportista' => $idEstadoDisponible,
                    ]);
                }
                
                // Si es cliente, crear registro en cliente
                if ($request->rol === 'cliente') {
                    Cliente::create([
                        'id_usuario' => $usuario->id,
                    ]);
                }
                
                // Si es admin, crear registro en admin
                if ($request->rol === 'admin') {
                    Admin::create([
                        'id_usuario' => $usuario->id,
                        'nivel_acceso' => 1, // Nivel de acceso por defecto
                    ]);
                }

                return response()->json([
                    'mensaje' => 'Usuario creado correctamente',
                    'usuario' => [
                        'id' => $usuario->id,
                        'correo' => $usuario->correo,
                        'nombre' => $persona->nombre,
                        'apellido' => $persona->apellido,
                        'rol' => $request->rol,
                    ]
                ], 201);
            });
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear usuario'], 500);
        }
    }

    /**
     * Obtener usuarios por rol
     */
    public function obtenerPorRol(string $rol): JsonResponse
    {
        try {
            // Validar que el rol sea válido
            if (!in_array($rol, ['transportista', 'cliente', 'admin'])) {
                return response()->json(['error' => 'Rol no válido'], 400);
            }

            $idRol = UsuarioHelper::obtenerRolPorCodigo($rol);
            $usuarios = Usuario::with(['persona', 'rol'])
                ->where('id_rol', $idRol)
                ->get();

            $usuarios = $usuarios->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'nombre' => $usuario->persona?->nombre,
                    'apellido' => $usuario->persona?->apellido,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol?->codigo,
                    'fecha_registro' => $usuario->fecha_registro,
                ];
            });

            return response()->json($usuarios);
        } catch (\Exception $e) {
            \Log::error('Error al obtener usuarios por rol: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener usuarios por rol'], 500);
        }
    }

    /**
     * Cambiar rol de usuario
     */
    public function cambiarRol(Request $request, int $id)
    {
        try {
            $request->validate([
                'rol' => 'required|string|in:cliente,transportista,admin',
            ]);

            return DB::transaction(function () use ($id, $request) {
                $usuario = Usuario::with(['persona', 'rol'])->find($id);
                if (!$usuario) {
                    return response()->json(['error' => 'Usuario no encontrado'], 404);
                }

                $rolAnterior = $usuario->rol?->codigo;
                $idRolNuevo = UsuarioHelper::obtenerRolPorCodigo($request->rol);
                $usuario->update(['id_rol' => $idRolNuevo]);

                // Si cambia a transportista, crear registro en transportistas si no existe
                if ($request->rol === 'transportista' && $rolAnterior !== 'transportista') {
                    $transportista = Transportista::where('id_usuario', $usuario->id)->first();
                    if (!$transportista) {
                        $idEstadoDisponible = \App\Http\Controllers\Api\Helpers\EstadoHelper::obtenerEstadoTransportistaPorNombre('Disponible');
                        Transportista::create([
                            'id_usuario' => $usuario->id,
                            'id_estado_transportista' => $idEstadoDisponible,
                        ]);
                    }
                }
                
                // Si cambia de transportista a otro rol, eliminar registro de transportista
                if ($rolAnterior === 'transportista' && $request->rol !== 'transportista') {
                    $transportista = Transportista::where('id_usuario', $usuario->id)->first();
                    if ($transportista) {
                        $transportista->delete();
                    }
                }
                
                // Si cambia a admin, crear registro en admin si no existe
                if ($request->rol === 'admin' && $rolAnterior !== 'admin') {
                    $admin = Admin::where('id_usuario', $usuario->id)->first();
                    if (!$admin) {
                        Admin::create([
                            'id_usuario' => $usuario->id,
                            'nivel_acceso' => 1, // Nivel de acceso por defecto
                        ]);
                    }
                }
                
                // Si cambia de admin a otro rol, eliminar registro de admin
                if ($rolAnterior === 'admin' && $request->rol !== 'admin') {
                    $admin = Admin::where('id_usuario', $usuario->id)->first();
                    if ($admin) {
                        $admin->delete();
                    }
                }

                return response()->json([
                    'mensaje' => 'Rol actualizado correctamente',
                    'usuario' => [
                        'id' => $usuario->id,
                        'nombre' => $usuario->persona?->nombre,
                        'apellido' => $usuario->persona?->apellido,
                        'correo' => $usuario->correo,
                        'rol' => $request->rol,
                        'rol_anterior' => $rolAnterior
                    ]
                ]);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al cambiar rol: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cambiar rol'], 500);
        }
    }
}
