<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transportista;
use App\Models\Usuario;
use App\Models\Persona;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
use App\Http\Controllers\Api\Helpers\UsuarioHelper;
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
            $transportistas = Transportista::with(['estadoTransportista', 'usuario.persona'])
                ->get();

            $transportistas = $transportistas->map(function ($transportista) {
                return [
                    'id' => $transportista->id,
                    'id_usuario' => $transportista->id_usuario,
                    'ci' => $transportista->usuario?->persona?->ci,
                    'telefono' => $transportista->usuario?->persona?->telefono,
                    'estado' => $transportista->estadoTransportista?->nombre,
                    'fecha_registro' => $transportista->fecha_registro,
                    'nombre' => $transportista->usuario?->persona?->nombre,
                    'apellido' => $transportista->usuario?->persona?->apellido,
                    'correo' => $transportista->usuario?->correo,
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
            $transportista = Transportista::with(['estadoTransportista', 'usuario.persona', 'usuario.rol'])
                ->find($id);

            if (!$transportista) {
                return response()->json(['error' => 'Transportista no encontrado'], 404);
            }

            // Transformar la respuesta para incluir datos del usuario
            $response = [
                'id' => $transportista->id,
                'id_usuario' => $transportista->id_usuario,
                'ci' => $transportista->usuario?->persona?->ci,
                'telefono' => $transportista->usuario?->persona?->telefono,
                'estado' => $transportista->estadoTransportista?->nombre,
                'fecha_registro' => $transportista->fecha_registro,
                'usuario' => $transportista->usuario ? [
                    'id' => $transportista->usuario->id,
                    'correo' => $transportista->usuario->correo,
                    'nombre' => $transportista->usuario->persona?->nombre,
                    'apellido' => $transportista->usuario->persona?->apellido,
                    'rol' => $transportista->usuario->rol?->codigo,
                ] : null,
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
                'id_usuario' => 'required|integer|exists:usuarios,id|unique:transportistas,id_usuario',
            ]);

            // Verificar que no exista ya un transportista con ese usuario
            if (Transportista::where('id_usuario', $request->id_usuario)->exists()) {
                return response()->json(['error' => 'Ya existe un transportista para ese usuario'], 409);
            }

            $usuario = Usuario::with('persona')->find($request->id_usuario);
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Actualizar rol del usuario a transportista
            $idRolTransportista = UsuarioHelper::obtenerRolPorCodigo('transportista');
            $usuario->update(['id_rol' => $idRolTransportista]);

            $idEstadoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('Disponible');
            $transportista = Transportista::create([
                'id_usuario' => $usuario->id,
                'id_estado_transportista' => $idEstadoDisponible,
            ]);

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
     * Nota: Los datos de CI y teléfono se editan desde UsuarioController
     * Este método solo permite cambiar el estado del transportista si es necesario
     */
    public function editar(Request $request, int $id): JsonResponse
    {
        try {
            $transportista = Transportista::with('estadoTransportista')->find($id);

            if (!$transportista) {
                return response()->json(['error' => 'Transportista no encontrado'], 404);
            }

            if ($transportista->estadoTransportista?->nombre === 'En ruta') {
                return response()->json(['error' => 'No se puede editar un transportista que está en ruta'], 400);
            }

            // Por ahora, este método no requiere campos ya que ci y telefono están en persona
            // Si necesitas editar el estado, puedes agregarlo aquí
            // $request->validate(['id_estado_transportista' => 'sometimes|integer|exists:estados_transportista,id']);
            
            // Si se necesita actualizar el estado en el futuro:
            // if ($request->has('id_estado_transportista')) {
            //     $transportista->update(['id_estado_transportista' => $request->id_estado_transportista]);
            // }

            return response()->json([
                'mensaje' => 'Transportista actualizado correctamente',
                'nota' => 'Los datos de CI y teléfono se editan desde el endpoint de usuarios'
            ]);
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
            $transportista = Transportista::with('estadoTransportista')->find($id);

            if (!$transportista) {
                return response()->json(['error' => 'Transportista no encontrado'], 404);
            }

            if ($transportista->estadoTransportista?->nombre === 'En ruta') {
                return response()->json(['error' => 'No se puede eliminar un transportista que está en ruta'], 400);
            }

            // Usar transacción para asegurar consistencia
            DB::transaction(function () use ($transportista) {
                // Revertir rol del usuario a cliente si existe
                if ($transportista->id_usuario) {
                    $usuario = Usuario::find($transportista->id_usuario);
                    if ($usuario) {
                        $idRolCliente = UsuarioHelper::obtenerRolPorCodigo('cliente');
                        $usuario->update(['id_rol' => $idRolCliente]);
                    }
                }
                
                // Eliminar transportista
                $transportista->delete();
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
                'usuario.nombre' => 'required|string|max:100',
                'usuario.apellido' => 'required|string|max:100',
                'usuario.ci' => 'required|string|max:20|unique:persona,ci',
                'usuario.correo' => 'required|email|max:100|unique:usuarios,correo',
                'usuario.contrasena' => 'required|string|min:6',
                'usuario.telefono' => 'nullable|string|max:20',
            ]);

            $result = DB::transaction(function () use ($request) {
                $usuarioData = $request->input('usuario');
                
                // Crear persona
                $persona = Persona::create([
                    'nombre' => $usuarioData['nombre'],
                    'apellido' => $usuarioData['apellido'],
                    'ci' => $usuarioData['ci'],
                    'telefono' => $usuarioData['telefono'] ?? null,
                ]);

                // Crear usuario
                $idRolTransportista = UsuarioHelper::obtenerRolPorCodigo('transportista');
                $usuario = Usuario::create([
                    'correo' => $usuarioData['correo'],
                    'contrasena' => Hash::make($usuarioData['contrasena']),
                    'id_rol' => $idRolTransportista,
                    'id_persona' => $persona->id,
                ]);

                // Crear transportista
                $idEstadoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('Disponible');
                return Transportista::create([
                    'id_usuario' => $usuario->id,
                    'id_estado_transportista' => $idEstadoDisponible,
                ]);
            });

            return response()->json([
                'mensaje' => 'Transportista creado correctamente',
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

            $transportistas = Transportista::with(['estadoTransportista', 'usuario.persona'])
                ->porEstado($estado)
                ->get();

            // Transformar la respuesta para incluir nombre y apellido directamente
            $transportistas = $transportistas->map(function ($transportista) {
                return [
                    'id' => $transportista->id,
                    'id_usuario' => $transportista->id_usuario,
                    'ci' => $transportista->usuario?->persona?->ci,
                    'telefono' => $transportista->usuario?->persona?->telefono,
                    'estado' => $transportista->estadoTransportista?->nombre,
                    'fecha_registro' => $transportista->fecha_registro,
                    'nombre' => $transportista->usuario?->persona?->nombre,
                    'apellido' => $transportista->usuario?->persona?->apellido,
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
            $transportistas = Transportista::with(['estadoTransportista', 'usuario.persona'])
                ->disponibles()
                ->get();

            // Transformar la respuesta para incluir nombre y apellido directamente
            $transportistas = $transportistas->map(function ($transportista) {
                return [
                    'id' => $transportista->id,
                    'id_usuario' => $transportista->id_usuario,
                    'ci' => $transportista->usuario?->persona?->ci,
                    'telefono' => $transportista->usuario?->persona?->telefono,
                    'estado' => $transportista->estadoTransportista?->nombre,
                    'fecha_registro' => $transportista->fecha_registro,
                    'nombre' => $transportista->usuario?->persona?->nombre,
                    'apellido' => $transportista->usuario?->persona?->apellido,
                ];
            });

            return response()->json($transportistas);
        } catch (\Exception $e) {
            \Log::error('Error al obtener transportistas disponibles: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener transportistas disponibles'], 500);
        }
    }
}
