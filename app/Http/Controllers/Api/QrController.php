<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrToken;
use App\Models\AsignacionMultiple;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class QrController extends Controller
{
    /**
     * Generar QR token para una asignación
     */
    public function generarQrToken(Request $request, int $id_asignacion): JsonResponse
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $userId = $usuario['id'];
            $rol = $usuario['rol'];

            // Verificar que la asignación existe
            $asignacion = AsignacionMultiple::with('envio:id,id_usuario')->find($id_asignacion);
            if (!$asignacion) {
                return response()->json(['error' => 'Asignación no encontrada'], 404);
            }

            // Solo transportistas pueden generar QR
            if ($rol !== 'transportista') {
                return response()->json(['error' => 'Solo los transportistas pueden generar QR tokens'], 403);
            }

            // Verificar si ya existe un QR token para esta asignación
            $qrExistente = QrToken::where('id_asignacion', $id_asignacion)->first();
            if ($qrExistente) {
                return response()->json([
                    'mensaje' => 'QR token ya existe para esta asignación',
                    'id_asignacion' => $id_asignacion,
                    'token' => $qrExistente->token,
                    'imagenQR' => $qrExistente->imagenqr,
                    'fecha_creacion' => $qrExistente->fecha_creacion,
                    'fecha_expiracion' => $qrExistente->fecha_expiracion,
                ]);
            }

            // Generar nuevo token
            $nuevoToken = Str::uuid();
            // URL específica para validar el QR con el token
            $tokenUrl = config('app.frontend_url', 'https://orgtrackprueba.netlify.app') . '/validar-qr/' . $nuevoToken;
            
            // Generar imagen QR real usando API
            $qrBase64 = $this->generarQRReal($tokenUrl);

            // Crear QR token
            $qrToken = QrToken::create([
                'id_asignacion' => $id_asignacion,
                'id_usuario_cliente' => $asignacion->envio->id_usuario,
                'token' => $nuevoToken,
                'imagenqr' => $qrBase64,
                'usado' => false,
                'fecha_creacion' => now(),
                'fecha_expiracion' => now()->addDay(),
            ]);

            return response()->json([
                'mensaje' => 'QR token generado correctamente',
                'id_asignacion' => $id_asignacion,
                'token' => $nuevoToken,
                'imagenQR' => $qrBase64,
                'fecha_creacion' => $qrToken->fecha_creacion,
                'fecha_expiracion' => $qrToken->fecha_expiracion,
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error al generar QR token: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al generar QR token'], 500);
        }
    }

    /**
     * Obtener QR token por asignación
     */
    public function obtenerQrToken(int $id_asignacion): JsonResponse
    {
        try {
            $qrToken = QrToken::where('id_asignacion', $id_asignacion)->first();

            if (!$qrToken) {
                return response()->json(['error' => 'QR token no encontrado'], 404);
            }

            return response()->json([
                'id_asignacion' => $qrToken->id_asignacion,
                'token' => $qrToken->token,
                'imagenQR' => $qrToken->imagenqr,
                'usado' => $qrToken->usado,
                'fecha_creacion' => $qrToken->fecha_creacion,
                'fecha_expiracion' => $qrToken->fecha_expiracion,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener QR token: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener QR token'], 500);
        }
    }

    /**
     * Obtener QR específico para transportista (con validaciones de acceso)
     */
    public function obtenerQR(Request $request, int $id_asignacion): JsonResponse
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $userId = $usuario['id'];
            $rol = $usuario['rol'];

            // Verificar que el usuario sea un transportista
            if ($rol !== 'transportista') {
                return response()->json(['error' => 'Solo los transportistas pueden ver los QR'], 403);
            }

            // Buscar el transportista relacionado con el usuario autenticado
            $transportista = \App\Models\Transportista::where('id_usuario', $userId)->first();
            if (!$transportista) {
                return response()->json(['error' => 'No se encontró al transportista'], 403);
            }

            // Verificar que el transportista sea el asignado a esta partición
            $asignacion = AsignacionMultiple::where('id', $id_asignacion)
                ->where('id_transportista', $transportista->id)
                ->first();

            if (!$asignacion) {
                return response()->json(['error' => 'No tienes acceso a esta asignación'], 403);
            }

            // Buscar el QR token
            $qrToken = QrToken::where('id_asignacion', $id_asignacion)->first();

            if (!$qrToken) {
                return response()->json(['error' => 'QR no encontrado para esta asignación'], 404);
            }

            // Construir URL completa para el QR
            $frontendUrl = config('app.frontend_url', 'https://orgtrackprueba.netlify.app');
            $qrUrl = $frontendUrl . '/validar-qr/' . $qrToken->token;

            return response()->json([
                'mensaje' => 'QR encontrado correctamente',
                'id_asignacion' => $qrToken->id_asignacion,
                'token' => $qrToken->token,
                'imagenQR' => $qrToken->imagenqr,
                'usado' => $qrToken->usado,
                'fecha_creacion' => $qrToken->fecha_creacion,
                'fecha_expiracion' => $qrToken->fecha_expiracion,
                'frontend_url' => $qrUrl,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener QR: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener QR'], 500);
        }
    }

    /**
     * Validar y usar QR token
     */
    public function validarQrToken(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);

            $qrToken = QrToken::where('token', $request->token)->first();

            if (!$qrToken) {
                return response()->json(['error' => 'Token QR no válido'], 404);
            }

            // Verificar si el token ha expirado
            if (now()->gt($qrToken->fecha_expiracion)) {
                return response()->json(['error' => 'Token QR expirado'], 400);
            }

            // Verificar si ya fue usado
            if ($qrToken->usado) {
                return response()->json(['error' => 'Token QR ya fue utilizado'], 400);
            }

            // Marcar como usado
            $qrToken->update(['usado' => true]);

            // Obtener información de la asignación
            $asignacion = AsignacionMultiple::with([
                'envio.usuario:id,nombre,apellido',
                'envio.direccion:id,nombreorigen,nombredestino',
                'vehiculo:id,placa,tipo',
                'transportista.usuario:id,nombre,apellido'
            ])->find($qrToken->id_asignacion);

            return response()->json([
                'mensaje' => 'Token QR válido',
                'valido' => true,
                'asignacion' => [
                    'id_asignacion' => $asignacion->id,
                    'estado' => $asignacion->estado,
                    'cliente' => [
                        'nombre' => $asignacion->envio->usuario->nombre,
                        'apellido' => $asignacion->envio->usuario->apellido,
                    ],
                    'origen' => $asignacion->envio->direccion?->nombreorigen,
                    'destino' => $asignacion->envio->direccion?->nombredestino,
                    'vehiculo' => [
                        'placa' => $asignacion->vehiculo?->placa,
                        'tipo' => $asignacion->vehiculo?->tipo,
                    ],
                    'transportista' => [
                        'nombre' => $asignacion->transportista?->usuario?->nombre,
                        'apellido' => $asignacion->transportista?->usuario?->apellido,
                    ],
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al validar QR token: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al validar QR token'], 500);
        }
    }

    /**
     * Obtener QR tokens por cliente
     */
    public function obtenerQrTokensCliente(Request $request): JsonResponse
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $userId = $usuario['id'];

            $qrTokens = QrToken::with([
                'asignacion.envio:id,estado',
                'asignacion.vehiculo:id,placa,tipo',
                'asignacion.transportista.usuario:id,nombre,apellido'
            ])
            ->where('id_usuario_cliente', $userId)
            ->orderBy('fecha_creacion', 'desc')
            ->get();

            $tokens = $qrTokens->map(function ($qrToken) {
                return [
                    'id_asignacion' => $qrToken->id_asignacion,
                    'token' => $qrToken->token,
                    'imagenQR' => $qrToken->imagenqr,
                    'usado' => $qrToken->usado,
                    'fecha_creacion' => $qrToken->fecha_creacion,
                    'fecha_expiracion' => $qrToken->fecha_expiracion,
                    'asignacion' => [
                        'estado' => $qrToken->asignacion?->estado,
                        'vehiculo' => [
                            'placa' => $qrToken->asignacion?->vehiculo?->placa,
                            'tipo' => $qrToken->asignacion?->vehiculo?->tipo,
                        ],
                        'transportista' => [
                            'nombre' => $qrToken->asignacion?->transportista?->usuario?->nombre,
                            'apellido' => $qrToken->asignacion?->transportista?->usuario?->apellido,
                        ],
                    ],
                ];
            });

            return response()->json($tokens);

        } catch (\Exception $e) {
            \Log::error('Error al obtener QR tokens del cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener QR tokens'], 500);
        }
    }

    /**
     * Eliminar QR token
     */
    public function eliminarQrToken(int $id_asignacion): JsonResponse
    {
        try {
            $qrToken = QrToken::where('id_asignacion', $id_asignacion)->first();

            if (!$qrToken) {
                return response()->json(['error' => 'QR token no encontrado'], 404);
            }

            $qrToken->delete();

            return response()->json([
                'mensaje' => 'QR token eliminado correctamente',
                'id_asignacion' => $id_asignacion
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar QR token: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al eliminar QR token'], 500);
        }
    }

    /**
     * Generar QR real usando API externa
     */
    private function generarQRReal(string $url)
    {
        try {
            // Usar API de QR Server para generar QR real
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($url);
            
            // Descargar la imagen
            $imageData = file_get_contents($qrUrl);
            
            if ($imageData === false) {
                throw new \Exception('No se pudo generar el QR');
            }
            
            // Convertir a base64 con el formato correcto
            $base64 = base64_encode($imageData);
            return 'data:image/png;base64,' . $base64;
            
        } catch (\Exception $e) {
            \Log::error('Error al generar QR: ' . $e->getMessage());
            
            // Fallback: QR placeholder simple
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        }
    }
}
