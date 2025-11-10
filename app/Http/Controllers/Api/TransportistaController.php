<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transportista;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TransportistaController extends Controller
{
    /**
     * Obtener todos los transportistas (incluye nombre y apellido desde la tabla Usuarios)
     */
    public function obtenerTodos(): JsonResponse
    {
        try {
            $transportistas = Transportista::with('usuario:id,nombre,apellido')
                ->select('id', 'id_usuario', 'ci', 'telefono', 'estado', 'fecha_registro')
                ->get();

            // Transformar la respuesta para incluir nombre y apellido directamente
            $transportistas = $transportistas->map(function ($transportista) {
                return [
                    'id' => $transportista->id,
                    'id_usuario' => $transportista->id_usuario,
                    'ci' => $transportista->ci,
                    'telefono' => $transportista->telefono,
                    'estado' => $transportista->estado,
                    'fecha_registro' => $transportista->fecha_registro,
                    'nombre' => $transportista->usuario?->nombre,
                    'apellido' => $transportista->usuario?->apellido,
                ];
            });

            return response()->json($transportistas);
        } catch (\Exception $e) {
            \Log::error('Error al obtener transportistas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener transportistas'], 500);
        }
    }

    /**
     * Obtener transportista por ID
     */
    public function obtenerPorId(int $id): JsonResponse
    {
        try {
            $transportista = Transportista::with('usuario:id,nombre,apellido,correo,rol')
                ->find($id);

            if (!$transportista) {
                return response()->json(['error' => 'Transportista no encontrado'], 404);
            }

            // Transformar la respuesta para incluir datos del usuario
            $response = [
                'id' => $transportista->id,
                'id_usuario' => $transportista->id_usuario,
                'ci' => $transportista->ci,
                'telefono' => $transportista->telefono,
                'estado' => $transportista->estado,
                'fecha_registro' => $transportista->fecha_registro,
                'usuario' => $transportista->usuario,
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error al obtener transportista: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener transportista'], 500);
        }
    }

    /**
     * Crear transportista
     */
    public function crear(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id_usuario' => 'required|integer|exists:usuarios,id',
                'ci' => 'required|string|max:20|unique:transportistas,ci',
                'telefono' => 'required|string|max:20',
            ]);

            // Verificar que el usuario no tenga ya un transportista asociado
            $usuario = Usuario::find($request->id_usuario);
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }
            if (Transportista::where('id_usuario', $usuario->id)->exists()) {
                return response()->json(['error' => 'El usuario ya es transportista'], 409);
            }

            $transportista = Transportista::create([
                'id_usuario' => $usuario->id,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
                'estado' => 'Disponible',
            ]);

            // Actualizar rol del usuario (si no lo era ya)
            if ($usuario->rol !== 'transportista') {
                $usuario->rol = 'transportista';
                $usuario->save();
            }

            return response()->json([
                'mensaje' => 'Transportista creado correctamente',
                'transportista' => $transportista
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al crear transportista: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear transportista'], 500);
        }
    }

    /**
     * Editar transportista
     */
    public function editar(Request $request, int $id): JsonResponse
    {
        try {
            // Validar los datos de entrada
            $transportista = Transportista::find($id);

            if (!$transportista) {
                return response()->json(['error' => 'Transportista no encontrado'], 404);
            }

            if ($transportista->estado === 'En ruta') {
                return response()->json(['error' => 'No se puede editar un transportista que está en ruta'], 400);
            }

            $request->validate([
                'ci' => 'required|string|max:20|unique:transportistas,ci,' . $id,
                'telefono' => 'required|string|max:20',
            ]);

            $transportista->update([
                'ci' => $request->ci,
                'telefono' => $request->telefono,
            ]);

            return response()->json(['mensaje' => 'Transportista actualizado correctamente']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al editar transportista: ' . $e->getMessage());
            return response()->json(['error' => 'Error al editar transportista'], 500);
        }
    }

    /**
     * Eliminar transportista
     */
    public function eliminar(int $id): JsonResponse
    {
        try {
            $transportista = Transportista::find($id);

            if (!$transportista) {
                return response()->json(['error' => 'Transportista no encontrado'], 404);
            }

            if ($transportista->estado === 'En ruta') {
                return response()->json(['error' => 'No se puede eliminar un transportista que está en ruta'], 400);
            }

            $id_usuario = $transportista->id_usuario;

            // Usar transacción para asegurar consistencia
            DB::transaction(function () use ($transportista, $id_usuario) {
                // Eliminar transportista
                $transportista->delete();
                
                // Revertir rol del usuario a cliente
                Usuario::where('id', $id_usuario)->update(['rol' => 'cliente']);
            });

            return response()->json(['mensaje' => 'Transportista y usuario eliminados correctamente']);
        } catch (\Exception $e) {
            \Log::error('Error al eliminar transportista: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar transportista'], 500);
        }
    }

    /**
     * Crear Transportista COMPLETO
     */
    public function crearTransportistaCompleto(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ci' => 'required|string|max:20|unique:transportistas,ci',
                'telefono' => 'required|string|max:20',
                'id_usuario' => 'nullable|integer|exists:usuarios,id',
                'usuario.nombre' => 'required_without:id_usuario|string|max:100',
                'usuario.apellido' => 'required_without:id_usuario|string|max:100',
                'usuario.correo' => 'required_without:id_usuario|email|max:100|unique:usuarios,correo',
                'usuario.contrasena' => 'required_without:id_usuario|string|min:6',
            ]);

            $result = DB::transaction(function () use ($request) {
                $usuario = null;

                if ($request->filled('id_usuario')) {
                    $usuario = Usuario::lockForUpdate()->find($request->id_usuario);
                    if (!$usuario) {
                        throw ValidationException::withMessages(['id_usuario' => 'Usuario no encontrado']);
                    }
                    if (Transportista::where('id_usuario', $usuario->id)->exists()) {
                        throw ValidationException::withMessages(['id_usuario' => 'El usuario ya es transportista']);
                    }
                } else {
                    $usuarioData = $request->input('usuario');
                    $usuario = Usuario::create([
                        'nombre' => $usuarioData['nombre'],
                        'apellido' => $usuarioData['apellido'],
                        'correo' => $usuarioData['correo'],
                        'contrasena' => Hash::make($usuarioData['contrasena']),
                        'rol' => 'transportista',
                    ]);
                }

                if ($usuario->rol !== 'transportista') {
                    $usuario->rol = 'transportista';
                    $usuario->save();
                }

                return Transportista::create([
                    'id_usuario' => $usuario->id,
                    'ci' => $request->ci,
                    'telefono' => $request->telefono,
                    'estado' => 'Disponible',
                ]);
            });

            return response()->json([
                'mensaje' => 'Transportista creado y rol actualizado correctamente',
                'transportista' => $result
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al crear transportista completo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear transportista completo'], 500);
        }
    }

    /**
     * Obtener transportistas por estado
     */
    public function obtenerPorEstado(string $estado): JsonResponse
    {
        try {
            // Validar que el estado sea válido
            if (!in_array($estado, ['Inactivo', 'No Disponible', 'En ruta', 'Disponible'])) {
                return response()->json(['error' => 'Estado no válido'], 400);
            }

            $transportistas = Transportista::with('usuario:id,nombre,apellido')
                ->porEstado($estado)
                ->select('id', 'id_usuario', 'ci', 'telefono', 'estado', 'fecha_registro')
                ->get();

            // Transformar la respuesta para incluir nombre y apellido directamente
            $transportistas = $transportistas->map(function ($transportista) {
                return [
                    'id' => $transportista->id,
                    'id_usuario' => $transportista->id_usuario,
                    'ci' => $transportista->ci,
                    'telefono' => $transportista->telefono,
                    'estado' => $transportista->estado,
                    'fecha_registro' => $transportista->fecha_registro,
                    'nombre' => $transportista->usuario?->nombre,
                    'apellido' => $transportista->usuario?->apellido,
                ];
            });

            return response()->json($transportistas);
        } catch (\Exception $e) {
            \Log::error('Error al obtener transportistas por estado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener transportistas por estado'], 500);
        }
    }

    /**
     * Obtener transportistas disponibles
     */
    public function obtenerDisponibles(): JsonResponse
    {
        try {
            $transportistas = Transportista::with('usuario:id,nombre,apellido')
                ->disponibles()
                ->select('id', 'id_usuario', 'ci', 'telefono', 'estado', 'fecha_registro')
                ->get();

            // Transformar la respuesta para incluir nombre y apellido directamente
            $transportistas = $transportistas->map(function ($transportista) {
                return [
                    'id' => $transportista->id,
                    'id_usuario' => $transportista->id_usuario,
                    'ci' => $transportista->ci,
                    'telefono' => $transportista->telefono,
                    'estado' => $transportista->estado,
                    'fecha_registro' => $transportista->fecha_registro,
                    'nombre' => $transportista->usuario?->nombre,
                    'apellido' => $transportista->usuario?->apellido,
                ];
            });

            return response()->json($transportistas);
        } catch (\Exception $e) {
            \Log::error('Error al obtener transportistas disponibles: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener transportistas disponibles'], 500);
        }
    }
}
