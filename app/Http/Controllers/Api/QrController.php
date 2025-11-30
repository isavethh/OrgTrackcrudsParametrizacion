<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrToken;
use App\Models\AsignacionMultiple;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
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
            // Construir frontend base desde la configuración y asegurar sin slash final
            $frontend = rtrim(config('app.frontend_url', config('app.url', 'http://localhost')), '/');
            // URL específica para validar el QR con el token
            $tokenUrl = $frontend . '/validar-qr/' . $nuevoToken;

            // Generar imagen QR real usando API
            $qrBase64 = $this->generarQRReal($tokenUrl);

            // Crear QR token
            $idEstadoQrActivo = EstadoHelper::obtenerEstadoQrTokenPorNombre('Activo');
            $qrToken = QrToken::create([
                'id_asignacion' => $id_asignacion,
                'id_estado_qrtoken' => $idEstadoQrActivo,
                'token' => $nuevoToken,
                'imagenqr' => $qrBase64,
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
                'estado' => $qrToken->estadoQrToken?->nombre,
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

            // Construir URL completa para el QR usando la configuración FRONTEND_URL
            $frontend = rtrim(config('app.frontend_url', config('app.url', 'http://localhost')), '/');
            $qrUrl = $frontend . '/validar-qr/' . $qrToken->token;

            return response()->json([
                'mensaje' => 'QR encontrado correctamente',
                'id_asignacion' => $qrToken->id_asignacion,
                'token' => $qrToken->token,
                'imagenQR' => $qrToken->imagenqr,
                'estado' => $qrToken->estadoQrToken?->nombre,
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
                'codigo' => 'required|string'
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
            if ($qrToken->estadoQrToken?->nombre === 'Usado') {
                return response()->json(['error' => 'Token QR ya fue utilizado'], 400);
            }

            // Obtener información de la asignación
            $asignacion = AsignacionMultiple::with([
                'envio.usuario.persona:id,nombre,apellido',
                'envio.direccion:id,nombreorigen,nombredestino',
                'vehiculo.tipoVehiculo:id,nombre',
                'vehiculo:id,placa',
                'estadoAsignacion:id,nombre',
                'transportista.usuario:id,id_persona',
                'transportista.usuario.persona:id,ci,telefono'
            ])->find($qrToken->id_asignacion);

            if (!$asignacion) {
                return response()->json(['error' => 'Asignación vinculada al QR no encontrada'], 404);
            }

            // Validar que el código proporcionado coincide con el de la asignación
            if (empty($asignacion->codigo_acceso) || $asignacion->codigo_acceso !== $request->codigo) {
                return response()->json(['error' => 'Código de acceso inválido'], 403);
            }

            // Marcar como usado (opcional, se mantiene comportamiento previo)
            $idEstadoUsado = EstadoHelper::obtenerEstadoQrTokenPorNombre('Usado');
            $qrToken->update(['id_estado_qrtoken' => $idEstadoUsado]);

            return response()->json([
                'mensaje' => 'Token QR válido',
                'valido' => true,
                'asignacion' => [
                    'id_asignacion' => $asignacion->id,
                    'estado' => $asignacion->estadoAsignacion?->nombre ?? 'Pendiente',
                    'cliente' => [
                        'nombre' => $asignacion->envio->usuario?->persona?->nombre,
                        'apellido' => $asignacion->envio->usuario?->persona?->apellido,
                    ],
                    'origen' => $asignacion->envio->direccion?->nombreorigen,
                    'destino' => $asignacion->envio->direccion?->nombredestino,
                    'vehiculo' => [
                        'placa' => $asignacion->vehiculo?->placa,
                        'tipo' => $asignacion->vehiculo?->tipoVehiculo?->nombre,
                    ],
                    'transportista' => [
                        'ci' => $asignacion->transportista?->usuario?->persona?->ci,
                        'telefono' => $asignacion->transportista?->usuario?->persona?->telefono,
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
     * Validar token + codigo de acceso (público) sin modificar estado del QR
     */
    public function validarCodigoAcceso(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'codigo' => 'required|string'
            ]);

            // Buscar la asignación por código de acceso (único)
            $asignacion = AsignacionMultiple::with([
                'envio.usuario.persona:id,nombre,apellido',
                'envio.direccion:id,nombreorigen,nombredestino',
                'vehiculo.tipoVehiculo:id,nombre',
                'vehiculo:id,placa',
                'estadoAsignacion:id,nombre',
                'transportista.usuario:id,id_persona',
                'transportista.usuario.persona:id,ci,telefono,nombre,apellido',
                'tipoTransporte:id,nombre',
                'cargas:id,id_catalogo_carga,cantidad,peso,id_unidad_medida',
                'cargas.catalogoCarga:id,tipo,variedad,empaque,descripcion',
                'cargas.unidadMedida:id,nombre',
                'recogidaEntrega:id,fecha_recogida,hora_recogida,hora_entrega,instrucciones_recogida,instrucciones_entrega'
            ])->where('codigo_acceso', $request->codigo)->first();

            if (!$asignacion) {
                return response()->json(['error' => 'Código de acceso no válido o no encontrado'], 404);
            }

            // Retornar información de la asignación sin modificar estado ni marcar nada
            return response()->json([
                'mensaje' => 'Código válido',
                'valido' => true,
                'asignacion' => [
                    'id_asignacion' => $asignacion->id,
                    'estado' => $asignacion->estadoAsignacion?->nombre ?? 'Pendiente',
                    'cliente' => [
                        'nombre' => $asignacion->envio->usuario?->persona?->nombre,
                        'apellido' => $asignacion->envio->usuario?->persona?->apellido,
                    ],
                    'origen' => $asignacion->envio->direccion?->nombreorigen,
                    'destino' => $asignacion->envio->direccion?->nombredestino,
                    'vehiculo' => [
                        'placa' => $asignacion->vehiculo?->placa,
                        'tipo' => $asignacion->vehiculo?->tipoVehiculo?->nombre,
                    ],
                    'tipo_transporte' => $asignacion->tipoTransporte?->nombre,
                    'transportista' => [
                        'ci' => $asignacion->transportista?->usuario?->persona?->ci,
                        'telefono' => $asignacion->transportista?->usuario?->persona?->telefono,
                        'nombre' => $asignacion->transportista?->usuario?->persona?->nombre,
                        'apellido' => $asignacion->transportista?->usuario?->persona?->apellido,
                    ],
                    'recogida_entrega' => [
                        'fecha_recogida' => $asignacion->recogidaEntrega?->fecha_recogida,
                        'hora_recogida' => $asignacion->recogidaEntrega?->hora_recogida,
                        'hora_entrega' => $asignacion->recogidaEntrega?->hora_entrega,
                        'instrucciones_recogida' => $asignacion->recogidaEntrega?->instrucciones_recogida,
                        'instrucciones_entrega' => $asignacion->recogidaEntrega?->instrucciones_entrega,
                    ],
                    'cargas' => $asignacion->cargas->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'cantidad' => $c->cantidad,
                            'peso' => $c->peso,
                            'unidad' => $c->unidadMedida?->nombre,
                            'catalogo' => [
                                'tipo' => $c->catalogoCarga?->tipo,
                                'variedad' => $c->catalogoCarga?->variedad,
                                'empaque' => $c->catalogoCarga?->empaque,
                                'descripcion' => $c->catalogoCarga?->descripcion,
                            ],
                        ];
                    })->all(),
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al validar codigo de acceso: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Error interno al validar codigo'], 500);
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

            // Obtener envíos del usuario y luego los QR tokens de esas asignaciones
            $envios = \App\Models\Envio::where('id_usuario', $userId)->pluck('id');
            $asignaciones = AsignacionMultiple::whereIn('id_envio', $envios)->pluck('id');
            
            $qrTokens = QrToken::with([
                'asignacion.envio.historialEstados.estadoEnvio:id,nombre',
                'asignacion.vehiculo.tipoVehiculo:id,nombre',
                'asignacion.vehiculo:id,placa',
                'asignacion.estadoAsignacion:id,nombre',
                'asignacion.transportista.usuario.persona:id,ci,telefono',
                'estadoQrToken:id,nombre'
            ])
            ->whereIn('id_asignacion', $asignaciones)
            ->orderBy('fecha_creacion', 'desc')
            ->get();

            $tokens = $qrTokens->map(function ($qrToken) {
                $estadoEnvio = \App\Http\Controllers\Api\Helpers\EstadoHelper::obtenerEstadoActualEnvio($qrToken->asignacion?->envio?->id ?? 0);
                return [
                    'id_asignacion' => $qrToken->id_asignacion,
                    'token' => $qrToken->token,
                    'imagenQR' => $qrToken->imagenqr,
                    'estado' => $qrToken->estadoQrToken?->nombre,
                    'fecha_creacion' => $qrToken->fecha_creacion,
                    'fecha_expiracion' => $qrToken->fecha_expiracion,
                    'asignacion' => [
                        'estado' => $qrToken->asignacion?->estadoAsignacion?->nombre ?? 'Pendiente',
                        'estado_envio' => $estadoEnvio ?? 'Pendiente',
                        'vehiculo' => [
                            'placa' => $qrToken->asignacion?->vehiculo?->placa,
                            'tipo' => $qrToken->asignacion?->vehiculo?->tipoVehiculo?->nombre,
                        ],
                        'transportista' => [
                            'ci' => $qrToken->asignacion?->transportista?->usuario?->persona?->ci,
                            'telefono' => $qrToken->asignacion?->transportista?->usuario?->persona?->telefono,
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
