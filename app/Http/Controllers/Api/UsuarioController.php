<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos(): JsonResponse
    {
        try {
            $usuarios = Usuario::all();
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
            $usuario = Usuario::find($id);

            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            return response()->json($usuario);
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
                'rol' => 'required|string|in:transportista,cliente,admin'
            ]);

            $usuario = Usuario::find($id);

            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $usuario->update([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'rol' => $request->rol
            ]);

            return response()->json(['mensaje' => 'Usuario actualizado correctamente']);
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
            $clientes = Usuario::select('id', 'nombre', 'apellido', 'correo')
                ->where('rol', 'cliente')
                ->get();

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
                'rol' => 'required|string|in:transportista,cliente,admin'
            ]);

            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'contrasena' => Hash::make($request->contrasena),
                'rol' => $request->rol
            ]);

            return response()->json([
                'mensaje' => 'Usuario creado correctamente',
                'usuario' => $usuario
            ], 201);
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

            $usuarios = Usuario::select('id', 'nombre', 'apellido', 'correo', 'rol', 'fecha_registro')
                ->where('rol', $rol)
                ->get();

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

            $usuario = Usuario::find($id);
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $rolAnterior = $usuario->rol;
            $usuario->update(['rol' => $request->rol]);

            // Si cambia a transportista, crear registro en transportistas si no existe
            if ($request->rol === 'transportista' && $rolAnterior !== 'transportista') {
                $transportista = Transportista::where('id_usuario', $id)->first();
                if (!$transportista) {
                    Transportista::create([
                        'id_usuario' => $id,
                        'ci' => '00000000', // CI temporal
                        'telefono' => '000-0000', // Teléfono temporal
                        'estado' => 'Disponible',
                    ]);
                }
            }

            // Si cambia de transportista a otro rol, actualizar estado
            if ($rolAnterior === 'transportista' && $request->rol !== 'transportista') {
                $transportista = Transportista::where('id_usuario', $id)->first();
                if ($transportista) {
                    $transportista->update(['estado' => 'No Disponible']);
                }
            }

            return response()->json([
                'mensaje' => 'Rol actualizado correctamente',
                'usuario' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol,
                    'rol_anterior' => $rolAnterior
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al cambiar rol: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cambiar rol'], 500);
        }
    }
}
