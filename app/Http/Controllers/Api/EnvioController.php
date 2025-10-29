<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AsignacionCarga;
use App\Models\AsignacionMultiple;
use App\Models\Carga;
use App\Models\Envio;
use App\Models\RecogidaEntrega;
use App\Models\Tipotransporte;
use App\Models\Direccion;
use App\Models\Transportista;
use App\Models\Vehiculo;
use App\Models\ChecklistCondicionTransporte;
use App\Models\ChecklistIncidenteTransporte;
use App\Models\FirmaEnvio;
use App\Models\FirmaTransportista;
use App\Models\QrToken;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class EnvioController extends Controller
{
    public function crearEnvioCompleto(Request $request)
    {
        $idDireccion = $request->input('id_direccion');
        $particiones = $request->input('particiones');
        $usuario = $request->attributes->get('usuario');
        $idUsuario = $usuario ? $usuario['id'] : null;

        if (!$idUsuario) {
            return response()->json(['error' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        if (!$idDireccion || !is_array($particiones) || count($particiones) === 0) {
            return response()->json(['error' => 'Faltan datos para crear el envío (dirección o particiones)'], Response::HTTP_BAD_REQUEST);
        }

        // Validar que la dirección exista y sea del usuario
        $direccion = \App\Models\Direccion::where('id', $idDireccion)
            ->where('id_usuario', $idUsuario)
            ->first();
        if (!$direccion) {
            return response()->json(['error' => 'La dirección no existe o no pertenece al usuario'], Response::HTTP_BAD_REQUEST);
        }

        return DB::transaction(function () use ($idUsuario, $idDireccion, $particiones) {
            $envio = Envio::create([
                'id_usuario' => $idUsuario,
                'estado' => 'Pendiente',
                'id_direccion' => $idDireccion,
            ]);

            foreach ($particiones as $particion) {
                $cargas = $particion['cargas'] ?? null;
                $recogidaEntrega = $particion['recogidaEntrega'] ?? null;
                $idTipoTransporte = $particion['id_tipo_transporte'] ?? null;

                if (!$cargas || !is_array($cargas) || count($cargas) === 0 || !$recogidaEntrega || !$idTipoTransporte) {
                    return response()->json(['error' => 'Cada partición debe incluir cargas, recogidaEntrega y tipo de transporte'], Response::HTTP_BAD_REQUEST);
                }

                // validar existencia de tipo transporte
                if (!Tipotransporte::where('id', $idTipoTransporte)->exists()) {
                    return response()->json(['error' => 'El tipo de transporte no existe: '.$idTipoTransporte], Response::HTTP_BAD_REQUEST);
                }

                $r = RecogidaEntrega::create([
                    'fecha_recogida' => $recogidaEntrega['fecha_recogida'],
                    'hora_recogida' => $recogidaEntrega['hora_recogida'],
                    'hora_entrega' => $recogidaEntrega['hora_entrega'],
                    'instrucciones_recogida' => $recogidaEntrega['instrucciones_recogida'] ?? null,
                    'instrucciones_entrega' => $recogidaEntrega['instrucciones_entrega'] ?? null,
                ]);

                $asignacion = AsignacionMultiple::create([
                    'id_envio' => $envio->id,
                    'id_tipo_transporte' => $idTipoTransporte,
                    'estado' => 'Pendiente',
                    'id_recogida_entrega' => $r->id,
                ]);

                foreach ($cargas as $carga) {
                    $c = Carga::create([
                        'tipo' => $carga['tipo'],
                        'variedad' => $carga['variedad'],
                        'cantidad' => $carga['cantidad'],
                        'empaquetado' => $carga['empaquetado'],
                        'peso' => $carga['peso'],
                    ]);
                    AsignacionCarga::create([
                        'id_asignacion' => $asignacion->id,
                        'id_carga' => $c->id,
                    ]);
                }
            }

            return response()->json([
                'mensaje' => 'Envío creado exitosamente para el cliente',
                'id_envio' => $envio->id,
            ], Response::HTTP_CREATED);
        });
    }

    /**
     * Crear envío completo con particiones (ADMIN)
     * Adaptado de Express/SQL Server + MongoDB a Laravel/PostgreSQL
     */
    public function crearEnvioCompletoAdmin(Request $request)
    {
        try {
            // Validar datos de entrada
            $request->validate([
                'id_usuario_cliente' => 'required|integer|exists:usuarios,id',
                'ubicacion' => 'required|array',
                'ubicacion.nombreorigen' => 'required|string|max:200',
                'ubicacion.origen_lng' => 'required|numeric',
                'ubicacion.origen_lat' => 'required|numeric',
                'ubicacion.nombredestino' => 'required|string|max:200',
                'ubicacion.destino_lng' => 'required|numeric',
                'ubicacion.destino_lat' => 'required|numeric',
                'ubicacion.rutageojson' => 'nullable|string',
                'particiones' => 'required|array|min:1',
                'particiones.*.cargas' => 'required|array|min:1',
                'particiones.*.cargas.*.tipo' => 'required|string|max:50',
                'particiones.*.cargas.*.variedad' => 'required|string|max:50',
                'particiones.*.cargas.*.cantidad' => 'required|integer|min:1',
                'particiones.*.cargas.*.empaquetado' => 'required|string|max:50',
                'particiones.*.cargas.*.peso' => 'required|numeric|min:0',
                'particiones.*.recogidaEntrega' => 'required|array',
                'particiones.*.recogidaEntrega.fecha_recogida' => 'required|date',
                'particiones.*.recogidaEntrega.hora_recogida' => 'required|date_format:H:i:s',
                'particiones.*.recogidaEntrega.hora_entrega' => 'required|date_format:H:i:s',
                'particiones.*.recogidaEntrega.instrucciones_recogida' => 'nullable|string|max:255',
                'particiones.*.recogidaEntrega.instrucciones_entrega' => 'nullable|string|max:255',
                'particiones.*.id_tipo_transporte' => 'required|integer|exists:tipotransporte,id',
                'particiones.*.id_transportista' => 'required|integer|exists:transportistas,id',
                'particiones.*.id_vehiculo' => 'required|integer|exists:vehiculos,id',
            ]);

            $id_usuario_cliente = $request->id_usuario_cliente;
            $ubicacion = $request->ubicacion;
            $particiones = $request->particiones;

            return DB::transaction(function () use ($id_usuario_cliente, $ubicacion, $particiones) {
                // 1. Guardar ubicación en PostgreSQL (en lugar de MongoDB)
                $nuevaUbicacion = Direccion::create([
                    'id_usuario' => $id_usuario_cliente,
                    'nombreorigen' => $ubicacion['nombreorigen'],
                    'origen_lng' => $ubicacion['origen_lng'],
                    'origen_lat' => $ubicacion['origen_lat'],
                    'nombredestino' => $ubicacion['nombredestino'],
                    'destino_lng' => $ubicacion['destino_lng'],
                    'destino_lat' => $ubicacion['destino_lat'],
                    'rutageojson' => $ubicacion['rutageojson'] ?? null,
                ]);

                // 2. Insertar envío principal
                $envio = Envio::create([
                    'id_usuario' => $id_usuario_cliente,
                    'estado' => 'Asignado',
                    'id_direccion' => $nuevaUbicacion->id,
                ]);

                // 3. Procesar cada partición
                foreach ($particiones as $bloque) {
                    $cargas = $bloque['cargas'];
                    $recogidaEntrega = $bloque['recogidaEntrega'];
                    $id_tipo_transporte = $bloque['id_tipo_transporte'];
                    $id_transportista = $bloque['id_transportista'];
                    $id_vehiculo = $bloque['id_vehiculo'];

                    // 4. Insertar RecogidaEntrega
                    $recogida = RecogidaEntrega::create([
                        'fecha_recogida' => $recogidaEntrega['fecha_recogida'],
                        'hora_recogida' => $recogidaEntrega['hora_recogida'],
                        'hora_entrega' => $recogidaEntrega['hora_entrega'],
                        'instrucciones_recogida' => $recogidaEntrega['instrucciones_recogida'] ?? null,
                        'instrucciones_entrega' => $recogidaEntrega['instrucciones_entrega'] ?? null,
                    ]);

                    // 5. Verificar disponibilidad de transportista y vehículo
                    $transportista = Transportista::find($id_transportista);
                    $vehiculo = Vehiculo::find($id_vehiculo);

                    if (!$transportista || $transportista->estado !== 'Disponible') {
                        throw new \Exception("Transportista {$id_transportista} no disponible");
                    }

                    if (!$vehiculo || $vehiculo->estado !== 'Disponible') {
                        throw new \Exception("Vehículo {$id_vehiculo} no disponible");
                    }

                    // 6. Insertar Asignación
                    $asignacion = AsignacionMultiple::create([
                        'id_envio' => $envio->id,
                        'id_transportista' => $id_transportista,
                        'id_vehiculo' => $id_vehiculo,
                        'estado' => 'Pendiente',
                        'id_tipo_transporte' => $id_tipo_transporte,
                        'id_recogida_entrega' => $recogida->id,
                    ]);

                    // 7. Marcar transportista y vehículo como no disponibles
                    $transportista->update(['estado' => 'No Disponible']);
                    $vehiculo->update(['estado' => 'No Disponible']);

                    // 8. Insertar cargas y relacionarlas con la asignación
                    foreach ($cargas as $carga) {
                        $nuevaCarga = Carga::create([
                            'tipo' => $carga['tipo'],
                            'variedad' => $carga['variedad'],
                            'cantidad' => $carga['cantidad'],
                            'empaquetado' => $carga['empaquetado'],
                            'peso' => $carga['peso'],
                        ]);

                        // Relacionar carga con asignación
                        AsignacionCarga::create([
                            'id_asignacion' => $asignacion->id,
                            'id_carga' => $nuevaCarga->id,
                        ]);
                    }
                }

                return response()->json([
                    'mensaje' => 'Envío creado con múltiples particiones, cargas y asignaciones',
                    'id_envio' => $envio->id,
                    'id_direccion' => $nuevaUbicacion->id,
                ], Response::HTTP_CREATED);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'detalles' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            \Log::error('Error al crear envío particionado (admin): ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno al crear envío (admin)',
                'mensaje' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener todos los envíos
     */
    public function obtenerTodos(Request $request)
    {
        try {
            \Log::info('Iniciando obtenerTodos');
            
            $usuario = $request->attributes->get('usuario');
            \Log::info('Usuario obtenido: ' . json_encode($usuario));
            
            if (!$usuario) {
                \Log::error('Usuario no encontrado en request');
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            // Consulta básica sin relaciones complejas
            $query = Envio::with([
                'usuario:id,nombre,apellido,rol',
                'direccion:id,nombreorigen,nombredestino'
            ]);

            // Si no es admin, solo mostrar sus envíos
            if ($usuario['rol'] !== 'admin') {
                $query->where('id_usuario', $usuario['id']);
                \Log::info('Filtrando por usuario: ' . $usuario['id']);
            }

            $envios = $query->get();
            \Log::info('Envíos encontrados: ' . $envios->count());

            // Si no hay envíos, devolver array vacío
            if ($envios->isEmpty()) {
                return response()->json([]);
            }

            // Transformar la respuesta de forma más simple
            $envios = $envios->map(function ($envio) {
                return [
                    'id' => $envio->id,
                    'id_usuario' => $envio->id_usuario,
                    'estado' => $envio->estado,
                    'fecha_creacion' => $envio->fecha_creacion,
                    'fecha_inicio' => $envio->fecha_inicio,
                    'fecha_entrega' => $envio->fecha_entrega,
                    'id_direccion' => $envio->id_direccion,
                    'usuario' => [
                        'id' => $envio->usuario?->id,
                        'nombre' => $envio->usuario?->nombre,
                        'apellido' => $envio->usuario?->apellido,
                        'rol' => $envio->usuario?->rol,
                    ],
                    'nombre_origen' => $envio->direccion?->nombreorigen ?? "—",
                    'nombre_destino' => $envio->direccion?->nombredestino ?? "—",
                    'particiones' => [] // Por ahora vacío, se puede expandir después
                ];
            });

            \Log::info('Respuesta preparada exitosamente');
            return response()->json($envios);

        } catch (\Exception $e) {
            \Log::error('Error al obtener envíos: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error al obtener envíos', 'detalle' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener envío por ID
     */
    public function obtenerPorId(Request $request, int $id)
    {
        try {
            $usuario = $request->attributes->get('usuario');

            $envio = Envio::with([
                'usuario:id,nombre,apellido',
                'asignaciones.transportista.usuario:id,nombre,apellido',
                'asignaciones.vehiculo:id,placa,tipo',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas',
                'direccion:id,nombreorigen,nombredestino,origen_lng,origen_lat,destino_lng,destino_lat,rutageojson'
            ])->find($id);

            if (!$envio) {
                return response()->json(['error' => 'Envío no encontrado'], 404);
            }

            // Validar permisos
            if ($usuario['rol'] !== 'admin' && $envio->id_usuario !== $usuario['id']) {
                return response()->json(['error' => 'No tienes permiso para ver este envío'], 403);
            }

            // Agregar datos de ubicación
            $envio->coordenadas_origen = [
                'lng' => $envio->direccion?->origen_lng,
                'lat' => $envio->direccion?->origen_lat,
            ];
            $envio->coordenadas_destino = [
                'lng' => $envio->direccion?->destino_lng,
                'lat' => $envio->direccion?->destino_lat,
            ];
            $envio->nombre_origen = $envio->direccion?->nombreorigen;
            $envio->nombre_destino = $envio->direccion?->nombredestino;
            $envio->rutaGeoJSON = $envio->direccion?->rutageojson;

            // Transformar asignaciones a particiones
            $envio->particiones = $envio->asignaciones->map(function ($asignacion) {
                return [
                    'id_asignacion' => $asignacion->id,
                    'estado' => $asignacion->estado,
                    'fecha_asignacion' => $asignacion->fecha_asignacion,
                    'fecha_inicio' => $asignacion->fecha_inicio,
                    'fecha_fin' => $asignacion->fecha_fin,
                    'transportista' => [
                        'nombre' => $asignacion->transportista?->usuario?->nombre,
                        'apellido' => $asignacion->transportista?->usuario?->apellido,
                        'telefono' => $asignacion->transportista?->telefono,
                        'ci' => $asignacion->transportista?->ci,
                    ],
                    'vehiculo' => [
                        'placa' => $asignacion->vehiculo?->placa,
                        'tipo' => $asignacion->vehiculo?->tipo,
                    ],
                    'tipoTransporte' => [
                        'nombre' => $asignacion->tipoTransporte?->nombre,
                        'descripcion' => $asignacion->tipoTransporte?->descripcion,
                    ],
                    'recogidaEntrega' => [
                        'fecha_recogida' => $asignacion->recogidaEntrega?->fecha_recogida,
                        'hora_recogida' => $asignacion->recogidaEntrega?->hora_recogida,
                        'hora_entrega' => $asignacion->recogidaEntrega?->hora_entrega,
                        'instrucciones_recogida' => $asignacion->recogidaEntrega?->instrucciones_recogida,
                        'instrucciones_entrega' => $asignacion->recogidaEntrega?->instrucciones_entrega,
                    ],
                    'cargas' => $asignacion->cargas,
                ];
            });

            // Calcular estado resumen
            $total = $envio->particiones->count();
            $activos = $envio->particiones->where('estado', 'En curso')->count();
            $envio->estado_resumen = "En curso ({$activos} de {$total} camiones activos)";

            return response()->json($envio);

        } catch (\Exception $e) {
            \Log::error('Error al obtener envío por ID: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener el envío'], 500);
        }
    }

    /**
     * Asignar transportista y vehículo a una partición existente
     */
    public function asignarTransportistaYVehiculoAParticion(Request $request, int $id_asignacion)
    {
        try {
            $request->validate([
                'id_transportista' => 'required|integer|exists:transportistas,id',
                'id_vehiculo' => 'required|integer|exists:vehiculos,id',
            ]);

            return DB::transaction(function () use ($id_asignacion, $request) {
                // Verificar disponibilidad
                $transportista = Transportista::find($request->id_transportista);
                $vehiculo = Vehiculo::find($request->id_vehiculo);

                if (!$transportista || $transportista->estado !== 'Disponible') {
                    return response()->json(['error' => 'Transportista no disponible'], 400);
                }

                if (!$vehiculo || $vehiculo->estado !== 'Disponible') {
                    return response()->json(['error' => 'Vehículo no disponible'], 400);
                }

                // Verificar existencia de la partición
                $asignacion = AsignacionMultiple::find($id_asignacion);
                if (!$asignacion) {
                    return response()->json(['error' => 'Partición (Asignación) no encontrada'], 404);
                }

                // Validar que la partición no esté ya completada o en curso
                if (in_array($asignacion->estado, ['Completado', 'En curso', 'Finalizado', 'Entregado'])) {
                    return response()->json([
                        'error' => 'No se puede asignar a una partición que ya está ' . strtolower($asignacion->estado),
                        'estado_actual' => $asignacion->estado
                    ], 400);
                }

                // Validar que la partición no tenga ya transportista y vehículo asignados
                if ($asignacion->id_transportista && $asignacion->id_vehiculo) {
                    return response()->json([
                        'error' => 'Esta partición ya tiene transportista y vehículo asignados',
                        'transportista_actual' => $asignacion->id_transportista,
                        'vehiculo_actual' => $asignacion->id_vehiculo
                    ], 400);
                }

                // Verificar que el envío no esté completado
                $envio = Envio::find($asignacion->id_envio);
                if (!$envio) {
                    return response()->json(['error' => 'Envío no encontrado'], 404);
                }

                if (in_array($envio->estado, ['Completado', 'Finalizado'])) {
                    return response()->json([
                        'error' => 'No se puede asignar a un envío que ya está ' . strtolower($envio->estado),
                        'estado_envio' => $envio->estado
                    ], 400);
                }

                // Actualizar la partición
                $asignacion->update([
                    'id_transportista' => $request->id_transportista,
                    'id_vehiculo' => $request->id_vehiculo,
                    'estado' => 'Pendiente',
                ]);

                // Marcar como no disponibles
                $transportista->update(['estado' => 'No Disponible']);
                $vehiculo->update(['estado' => 'No Disponible']);

                // Actualizar estado global del envío
                $this->actualizarEstadoGlobalEnvioInterno($asignacion->id_envio);

                return response()->json(['mensaje' => 'Transportista y vehículo asignados correctamente a la partición']);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al asignar a partición: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al asignar a partición'], 500);
        }
    }

    /**
     * Obtener mis envíos (para cliente o admin)
     */
    public function obtenerMisEnvios(Request $request)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $userId = $usuario['id'];

            $envios = Envio::with([
                'usuario:id,nombre,apellido,rol',
                'asignaciones.transportista.usuario:id,nombre,apellido',
                'asignaciones.vehiculo:id,placa,tipo',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas',
                'direccion:id,nombreorigen,nombredestino'
            ])->where('id_usuario', $userId)->get();

            // Transformar la respuesta
            $envios = $envios->map(function ($envio) {
                $envio->nombre_origen = $envio->direccion?->nombreorigen ?? "—";
                $envio->nombre_destino = $envio->direccion?->nombredestino ?? "—";
                
                $envio->particiones = $envio->asignaciones->map(function ($asignacion) {
                    return [
                        'id_asignacion' => $asignacion->id,
                        'estado' => $asignacion->estado,
                        'fecha_asignacion' => $asignacion->fecha_asignacion,
                        'fecha_inicio' => $asignacion->fecha_inicio,
                        'fecha_fin' => $asignacion->fecha_fin,
                        'transportista' => [
                            'nombre' => $asignacion->transportista?->usuario?->nombre,
                            'apellido' => $asignacion->transportista?->usuario?->apellido,
                            'ci' => $asignacion->transportista?->ci,
                            'telefono' => $asignacion->transportista?->telefono,
                        ],
                        'vehiculo' => [
                            'placa' => $asignacion->vehiculo?->placa,
                            'tipo' => $asignacion->vehiculo?->tipo,
                        ],
                        'recogidaEntrega' => [
                            'fecha_recogida' => $asignacion->recogidaEntrega?->fecha_recogida,
                            'hora_recogida' => $asignacion->recogidaEntrega?->hora_recogida,
                            'hora_entrega' => $asignacion->recogidaEntrega?->hora_entrega,
                            'instrucciones_recogida' => $asignacion->recogidaEntrega?->instrucciones_recogida,
                            'instrucciones_entrega' => $asignacion->recogidaEntrega?->instrucciones_entrega,
                        ],
                        'tipoTransporte' => [
                            'nombre' => $asignacion->tipoTransporte?->nombre,
                            'descripcion' => $asignacion->tipoTransporte?->descripcion,
                        ],
                        'cargas' => $asignacion->cargas,
                    ];
                });

                return $envio;
            });

            return response()->json($envios);

        } catch (\Exception $e) {
            \Log::error('Error al obtener mis envíos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener tus envíos'], 500);
        }
    }

    /**
     * Actualizar estado global del envío (endpoint público)
     */
    public function actualizarEstadoGlobalEnvio(Request $request, int $id_envio)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            
            // Solo admin puede actualizar estado global manualmente
            if ($usuario['rol'] !== 'admin') {
                return response()->json(['error' => 'Solo los administradores pueden actualizar el estado global'], 403);
            }

            // Verificar que el envío existe
            $envio = Envio::find($id_envio);
            if (!$envio) {
                return response()->json(['error' => 'Envío no encontrado'], 404);
            }

            // Actualizar estado global
            $this->actualizarEstadoGlobalEnvioInterno($id_envio);

            return response()->json(['mensaje' => 'Estado global del envío actualizado correctamente']);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar estado global: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al actualizar estado global'], 500);
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

    /**
     * Generar QR placeholder (método legacy)
     */
    private function generarQRPlaceholder(string $url)
    {
        return $this->generarQRReal($url);
    }

    /**
     * Actualizar estado global del envío (método interno)
     */
    private function actualizarEstadoGlobalEnvioInterno(int $id_envio)
    {
        $asignaciones = AsignacionMultiple::where('id_envio', $id_envio)->get();
        $estados = $asignaciones->pluck('estado')->toArray();

        $nuevoEstado = 'Asignado';

        if (empty($estados)) {
            $nuevoEstado = 'Pendiente';
        } elseif (count(array_filter($estados, fn($e) => $e === 'Entregado')) === count($estados)) {
            $nuevoEstado = 'Entregado';
        } elseif (count(array_filter($estados, fn($e) => $e === 'Pendiente')) === count($estados)) {
            $nuevoEstado = 'Asignado';
        } elseif (in_array('Entregado', $estados) && count(array_filter($estados, fn($e) => $e !== 'Entregado')) > 0) {
            $nuevoEstado = 'Parcialmente entregado';
        } elseif (in_array('En curso', $estados)) {
            $nuevoEstado = 'En curso';
        }

        Envio::where('id', $id_envio)->update(['estado' => $nuevoEstado]);
    }

    /**
     * Iniciar viaje (con generación de QR)
     */
    public function iniciarViaje(Request $request, int $id_asignacion)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $userId = $usuario['id'];
            $rol = $usuario['rol'];

            if ($rol !== 'transportista') {
                return response()->json(['error' => 'Solo los transportistas pueden iniciar el viaje'], 403);
            }

            return DB::transaction(function () use ($id_asignacion, $userId) {
                // Obtener ID del transportista autenticado
                $transportista = Transportista::where('id_usuario', $userId)->first();
                if (!$transportista) {
                    return response()->json(['error' => 'No se encontró al transportista'], 403);
                }

                // Verificar asignación válida
                $asignacion = AsignacionMultiple::with('envio:id,id_usuario')
                    ->where('id', $id_asignacion)
                    ->where('id_transportista', $transportista->id)
                    ->where('estado', 'Pendiente')
                    ->first();

                if (!$asignacion) {
                    return response()->json(['error' => 'No tienes acceso o la asignación no está disponible para iniciar'], 403);
                }

                // Verificar checklist por asignación
                $checklist = ChecklistCondicionTransporte::where('id_asignacion', $id_asignacion)->first();
                if (!$checklist) {
                    return response()->json(['error' => 'Debes completar el checklist antes de iniciar el viaje'], 400);
                }

                // Actualizar asignación
                $asignacion->update([
                    'estado' => 'En curso',
                    'fecha_inicio' => now(),
                ]);

                // Actualizar estado de recursos
                $transportista->update(['estado' => 'En ruta']);
                $asignacion->vehiculo->update(['estado' => 'En ruta']);

                // Actualizar estado global del envío
                $this->actualizarEstadoGlobalEnvioInterno($asignacion->id_envio);

                // Generar QR automáticamente (si no existe)
                $qrToken = QrToken::where('id_asignacion', $id_asignacion)->first();

                if (!$qrToken) {
                    $nuevoToken = \Str::uuid();
                    // URL específica para validar el QR con el token
                    $tokenUrl = config('app.frontend_url', 'https://orgtrackprueba.netlify.app') . '/validar-qr/' . $nuevoToken;
                    
                    // Generar imagen QR real usando API
                    $qrBase64 = $this->generarQRReal($tokenUrl);

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
                        'mensaje' => 'Viaje iniciado correctamente para esta asignación',
                        'id_asignacion' => $id_asignacion,
                        'token' => $nuevoToken,
                        'imagenQR' => $qrBase64,
                        'fecha_creacion' => $qrToken->fecha_creacion,
                    ]);
                } else {
                    return response()->json([
                        'mensaje' => 'Viaje iniciado correctamente para esta asignación (QR ya existía)',
                        'id_asignacion' => $id_asignacion,
                        'token' => $qrToken->token,
                        'imagenQR' => $qrToken->imagenqr,
                        'fecha_creacion' => $qrToken->fecha_creacion,
                    ]);
                }
            });

        } catch (\Exception $e) {
            \Log::error('Error al iniciar viaje: ' . $e->getMessage());
            return response()->json(['error' => 'Error al iniciar el viaje'], 500);
        }
    }

    /**
     * Obtener envíos asignados al transportista autenticado
     */
    public function obtenerEnviosAsignadosTransportista(Request $request)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $id_usuario = $usuario['id'];

            // Obtener ID del transportista autenticado
            $transportista = Transportista::where('id_usuario', $id_usuario)->first();
            if (!$transportista) {
                return response()->json(['error' => 'No eres un transportista válido'], 404);
            }

            // Obtener asignaciones de este transportista
            $asignaciones = AsignacionMultiple::with([
                'envio.usuario:id,nombre,apellido',
                'envio.direccion:id,nombreorigen,nombredestino,origen_lng,origen_lat,destino_lng,destino_lat,rutageojson',
                'vehiculo:id,placa,tipo',
                'tipoTransporte:id,nombre,descripcion',
                'recogidaEntrega',
                'cargas'
            ])->where('id_transportista', $transportista->id)->get();

            // Transformar la respuesta
            $enviosCompletos = $asignaciones->map(function ($asignacion) {
                $envio = $asignacion->envio;
                
                return [
                    'id_asignacion' => $asignacion->id,
                    'estado' => $asignacion->estado,
                    'fecha_inicio' => $asignacion->fecha_inicio,
                    'fecha_fin' => $asignacion->fecha_fin,
                    'fecha_asignacion' => $asignacion->fecha_asignacion,
                    'id_envio' => $asignacion->id_envio,
                    'id_vehiculo' => $asignacion->id_vehiculo,
                    'id_recogida_entrega' => $asignacion->id_recogida_entrega,
                    'id_tipo_transporte' => $asignacion->id_tipo_transporte,
                    'estado_envio' => $envio->estado,
                    'fecha_creacion' => $envio->fecha_creacion,
                    'id_usuario' => $envio->id_usuario,
                    'id_ubicacion_mongo' => $envio->id_direccion, // Adaptado para PostgreSQL
                    'placa' => $asignacion->vehiculo?->placa,
                    'tipo_vehiculo' => $asignacion->vehiculo?->tipo,
                    'tipo_transporte' => $asignacion->tipoTransporte?->nombre,
                    'descripcion_transporte' => $asignacion->tipoTransporte?->descripcion,
                    'nombre_cliente' => $envio->usuario?->nombre,
                    'apellido_cliente' => $envio->usuario?->apellido,
                    'nombre_origen' => $envio->direccion?->nombreorigen,
                    'nombre_destino' => $envio->direccion?->nombredestino,
                    'coordenadas_origen' => [
                        'lng' => $envio->direccion?->origen_lng,
                        'lat' => $envio->direccion?->origen_lat,
                    ],
                    'coordenadas_destino' => [
                        'lng' => $envio->direccion?->destino_lng,
                        'lat' => $envio->direccion?->destino_lat,
                    ],
                    'rutaGeoJSON' => $envio->direccion?->rutageojson,
                    'cargas' => $asignacion->cargas,
                    'recogidaEntrega' => $asignacion->recogidaEntrega,
                ];
            });

            return response()->json($enviosCompletos);

        } catch (\Exception $e) {
            \Log::error('Error al obtener envíos del transportista: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener los envíos'], 500);
        }
    }

    /**
     * Finalizar envío (transportista)
     */
    public function finalizarEnvio(Request $request, int $id_asignacion)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $id_usuario = $usuario['id'];

            return DB::transaction(function () use ($id_asignacion, $id_usuario) {
                // Obtener ID del transportista autenticado
                $transportista = Transportista::where('id_usuario', $id_usuario)->first();
                if (!$transportista) {
                    return response()->json(['error' => 'No tienes permisos para esta acción'], 403);
                }

                // Obtener asignación
                $asignacion = AsignacionMultiple::find($id_asignacion);
                if (!$asignacion) {
                    return response()->json(['error' => 'Asignación no encontrada'], 404);
                }

                // Validar que le pertenece al transportista y esté en curso
                if ($asignacion->id_transportista !== $transportista->id) {
                    return response()->json(['error' => 'No tienes permiso para finalizar esta asignación'], 403);
                }

                if ($asignacion->estado !== 'En curso') {
                    return response()->json(['error' => 'Esta asignación no está en curso'], 400);
                }

                // Validar que exista checklist de incidentes
                $checklist = ChecklistIncidenteTransporte::where('id_asignacion', $id_asignacion)->first();
                if (!$checklist) {
                    return response()->json(['error' => 'Debes completar el checklist de incidentes antes de finalizar el viaje.'], 400);
                }

                // Validar que exista firma
                $firma = FirmaEnvio::where('id_asignacion', $id_asignacion)->first();
                if (!$firma) {
                    return response()->json(['error' => 'Debes capturar la firma del cliente antes de finalizar el viaje.'], 400);
                }

                // Validar que exista firma del transportista
                $firmaTransportista = FirmaTransportista::where('id_asignacion', $id_asignacion)->first();
                if (!$firmaTransportista) {
                    return response()->json(['error' => 'Debes capturar tu firma como transportista antes de finalizar el viaje.'], 400);
                }

                // Guardar referencias antes de limpiar
                $vehiculo = $asignacion->vehiculo;

                // Actualizar asignación como finalizada
                $asignacion->update([
                    'estado' => 'Entregado',
                    'fecha_fin' => now(),
                    'id_transportista' => null,  // Limpiar asignación
                    'id_vehiculo' => null,        // Limpiar asignación
                ]);

                // Liberar transportista y vehículo
                $transportista->update(['estado' => 'Disponible']);
                if ($vehiculo) {
                    $vehiculo->update(['estado' => 'Disponible']);
                }

                // Actualizar estado global del envío
                $this->actualizarEstadoGlobalEnvioInterno($asignacion->id_envio);

                return response()->json(['mensaje' => 'Asignación finalizada correctamente']);
            });

        } catch (\Exception $e) {
            \Log::error('Error al finalizar asignación: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al finalizar asignación'], 500);
        }
    }

    /**
     * Registrar checklist de condiciones antes de iniciar viaje
     */
    public function registrarChecklistCondiciones(Request $request, int $id_asignacion)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $id_usuario = $usuario['id'];

            $request->validate([
                'temperatura_controlada' => 'required|boolean',
                'embalaje_adecuado' => 'required|boolean',
                'carga_segura' => 'required|boolean',
                'vehiculo_limpio' => 'required|boolean',
                'documentos_presentes' => 'required|boolean',
                'ruta_conocida' => 'required|boolean',
                'combustible_completo' => 'required|boolean',
                'gps_operativo' => 'required|boolean',
                'comunicacion_funcional' => 'required|boolean',
                'estado_general_aceptable' => 'required|boolean',
                'observaciones' => 'nullable|string|max:255',
            ]);

            return DB::transaction(function () use ($id_asignacion, $id_usuario, $request) {
                // Verificar si el transportista autenticado corresponde a la asignación
                $asignacion = AsignacionMultiple::with('transportista:id,id_usuario')
                    ->find($id_asignacion);

                if (!$asignacion) {
                    return response()->json(['error' => 'Asignación no encontrada'], 404);
                }

                if ($asignacion->transportista->id_usuario !== $id_usuario) {
                    return response()->json(['error' => 'No tienes permiso para esta asignación'], 403);
                }

                if ($asignacion->estado !== 'Pendiente') {
                    return response()->json(['error' => 'El checklist solo se puede registrar si la asignación está pendiente'], 400);
                }

                // Verificar si ya existe un checklist
                $yaExiste = ChecklistCondicionTransporte::where('id_asignacion', $id_asignacion)->first();
                if ($yaExiste) {
                    return response()->json(['error' => 'Este checklist ya fue registrado'], 400);
                }

                // Insertar checklist
                ChecklistCondicionTransporte::create([
                    'id_asignacion' => $id_asignacion,
                    'temperatura_controlada' => $request->temperatura_controlada,
                    'embalaje_adecuado' => $request->embalaje_adecuado,
                    'carga_segura' => $request->carga_segura,
                    'vehiculo_limpio' => $request->vehiculo_limpio,
                    'documentos_presentes' => $request->documentos_presentes,
                    'ruta_conocida' => $request->ruta_conocida,
                    'combustible_completo' => $request->combustible_completo,
                    'gps_operativo' => $request->gps_operativo,
                    'comunicacion_funcional' => $request->comunicacion_funcional,
                    'estado_general_aceptable' => $request->estado_general_aceptable,
                    'observaciones' => $request->observaciones,
                    'fecha' => now(),
                ]);

                return response()->json(['mensaje' => 'Checklist de condiciones registrado correctamente'], 201);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al registrar checklist de condiciones: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al registrar checklist'], 500);
        }
    }

    /**
     * Registrar checklist de incidentes luego de iniciar el viaje
     */
    public function registrarChecklistIncidentes(Request $request, int $id_asignacion)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $id_usuario = $usuario['id'];

            $request->validate([
                'retraso' => 'required|boolean',
                'problema_mecanico' => 'required|boolean',
                'accidente' => 'required|boolean',
                'perdida_carga' => 'required|boolean',
                'condiciones_climaticas_adversas' => 'required|boolean',
                'ruta_alternativa_usada' => 'required|boolean',
                'contacto_cliente_dificultoso' => 'required|boolean',
                'parada_imprevista' => 'required|boolean',
                'problemas_documentacion' => 'required|boolean',
                'otros_incidentes' => 'required|boolean',
                'descripcion_incidente' => 'nullable|string|max:255',
            ]);

            return DB::transaction(function () use ($id_asignacion, $id_usuario, $request) {
                // Validar que la asignación exista y pertenezca al transportista autenticado
                $asignacion = AsignacionMultiple::with('transportista:id,id_usuario')
                    ->find($id_asignacion);

                if (!$asignacion) {
                    return response()->json(['error' => 'Asignación no encontrada'], 404);
                }

                if ($asignacion->transportista->id_usuario !== $id_usuario) {
                    return response()->json(['error' => 'No tienes permiso para esta asignación'], 403);
                }

                // Permitir registrar checklist cuando la asignación esté EN CURSO
                if ($asignacion->estado !== 'En curso') {
                    return response()->json(['error' => 'Solo puedes registrar el checklist si el viaje está en curso'], 400);
                }

                // Validar si ya existe un checklist de incidentes para esta asignación
                $yaExiste = ChecklistIncidenteTransporte::where('id_asignacion', $id_asignacion)->first();
                if ($yaExiste) {
                    return response()->json(['error' => 'El checklist ya fue registrado'], 400);
                }

                // Insertar el nuevo checklist de incidentes
                ChecklistIncidenteTransporte::create([
                    'id_asignacion' => $id_asignacion,
                    'retraso' => $request->retraso,
                    'problema_mecanico' => $request->problema_mecanico,
                    'accidente' => $request->accidente,
                    'perdida_carga' => $request->perdida_carga,
                    'condiciones_climaticas_adversas' => $request->condiciones_climaticas_adversas,
                    'ruta_alternativa_usada' => $request->ruta_alternativa_usada,
                    'contacto_cliente_dificultoso' => $request->contacto_cliente_dificultoso,
                    'parada_imprevista' => $request->parada_imprevista,
                    'problemas_documentacion' => $request->problemas_documentacion,
                    'otros_incidentes' => $request->otros_incidentes,
                    'descripcion_incidente' => $request->descripcion_incidente,
                    'fecha' => now(),
                ]);

                return response()->json(['mensaje' => 'Checklist de incidentes registrado correctamente'], 201);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al guardar checklist de incidentes: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al registrar el checklist'], 500);
        }
    }

    /**
     * Asignar transportista y vehículo (método original)
     */
    public function asignarTransportistaYVehiculo(Request $request, int $id_envio)
    {
        try {
            $request->validate([
                'id_transportista' => 'required|integer|exists:transportistas,id',
                'id_vehiculo' => 'required|integer|exists:vehiculos,id',
                'carga' => 'required|array',
                'carga.tipo' => 'required|string|max:50',
                'carga.variedad' => 'required|string|max:50',
                'carga.cantidad' => 'required|integer|min:1',
                'carga.empaquetado' => 'required|string|max:50',
                'carga.peso' => 'required|numeric|min:0',
                'recogidaEntrega' => 'required|array',
                'recogidaEntrega.fecha_recogida' => 'required|date',
                'recogidaEntrega.hora_recogida' => 'required|date_format:H:i:s',
                'recogidaEntrega.hora_entrega' => 'required|date_format:H:i:s',
                'recogidaEntrega.instrucciones_recogida' => 'nullable|string|max:255',
                'recogidaEntrega.instrucciones_entrega' => 'nullable|string|max:255',
                'id_tipo_transporte' => 'required|integer|exists:tipotransporte,id',
            ]);

            return DB::transaction(function () use ($id_envio, $request) {
                // Verificar disponibilidad
                $transportista = Transportista::find($request->id_transportista);
                $vehiculo = Vehiculo::find($request->id_vehiculo);

                if (!$transportista || $transportista->estado !== 'Disponible') {
                    return response()->json(['error' => 'Transportista no disponible'], 400);
                }

                if (!$vehiculo || $vehiculo->estado !== 'Disponible') {
                    return response()->json(['error' => 'Vehículo no disponible'], 400);
                }

                // Verificar existencia del envío
                $envio = Envio::find($id_envio);
                if (!$envio) {
                    return response()->json(['error' => 'Envío no encontrado'], 404);
                }

                // Validar que el envío no esté completado
                if (in_array($envio->estado, ['Completado', 'Finalizado', 'Entregado'])) {
                    return response()->json([
                        'error' => 'No se puede asignar a un envío que ya está ' . strtolower($envio->estado),
                        'estado_envio' => $envio->estado
                    ], 400);
                }

                // Insertar carga
                $carga = Carga::create([
                    'tipo' => $request->carga['tipo'],
                    'variedad' => $request->carga['variedad'],
                    'cantidad' => $request->carga['cantidad'],
                    'empaquetado' => $request->carga['empaquetado'],
                    'peso' => $request->carga['peso'],
                ]);

                // Insertar RecogidaEntrega
                $recogida = RecogidaEntrega::create([
                    'fecha_recogida' => $request->recogidaEntrega['fecha_recogida'],
                    'hora_recogida' => $request->recogidaEntrega['hora_recogida'],
                    'hora_entrega' => $request->recogidaEntrega['hora_entrega'],
                    'instrucciones_recogida' => $request->recogidaEntrega['instrucciones_recogida'] ?? null,
                    'instrucciones_entrega' => $request->recogidaEntrega['instrucciones_entrega'] ?? null,
                ]);

                // Insertar asignación múltiple
                $asignacion = AsignacionMultiple::create([
                    'id_envio' => $id_envio,
                    'id_transportista' => $request->id_transportista,
                    'id_vehiculo' => $request->id_vehiculo,
                    'estado' => 'Pendiente',
                    'id_tipo_transporte' => $request->id_tipo_transporte,
                    'id_recogida_entrega' => $recogida->id,
                ]);

                // Relacionar carga con asignación
                AsignacionCarga::create([
                    'id_asignacion' => $asignacion->id,
                    'id_carga' => $carga->id,
                ]);

                // Actualizar estados
                $transportista->update(['estado' => 'No Disponible']);
                $vehiculo->update(['estado' => 'No Disponible']);

                return response()->json(['mensaje' => 'Asignación registrada correctamente con carga y detalles completos']);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al asignar: ' . $e->getMessage());
            return response()->json(['error' => 'Error al asignar transporte'], 500);
        }
    }

    /**
     * Generar documento de envío completo
     */
    public function generarDocumentoEnvio(Request $request, int $id_envio)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $rol = $usuario['rol'];
            $id_usuario = $usuario['id'];

            $envio = Envio::with([
                'usuario:id,nombre,apellido',
                'asignaciones.transportista.usuario:id,nombre,apellido',
                'asignaciones.vehiculo:id,placa,tipo',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas',
                'asignaciones.checklistCondiciones',
                'asignaciones.checklistIncidentes',
                'asignaciones.firmaEnvio',
                'asignaciones.firmaTransportista',
                'direccion:id,nombreorigen,nombredestino'
            ])->find($id_envio);

            if (!$envio) {
                return response()->json(['error' => 'Envío no encontrado'], 404);
            }

            // Validar si el envío está completamente ENTREGADO
            if ($envio->estado !== 'Entregado') {
                return response()->json(['error' => 'El documento solo se puede generar cuando el envío esté completamente entregado.'], 400);
            }

            // Validar si el cliente tiene permiso (si no es admin)
            if ($rol !== 'admin' && $envio->id_usuario !== $id_usuario) {
                return response()->json(['error' => 'No tienes acceso a este envío'], 403);
            }

            // Transformar asignaciones a particiones
            $particiones = $envio->asignaciones->map(function ($asignacion) use ($rol) {
                $particion = [
                    'id_asignacion' => $asignacion->id,
                    'estado' => $asignacion->estado,
                    'fecha_asignacion' => $asignacion->fecha_asignacion,
                    'fecha_inicio' => $asignacion->fecha_inicio,
                    'fecha_fin' => $asignacion->fecha_fin,
                    'transportista' => [
                        'nombre' => $asignacion->transportista?->usuario?->nombre,
                        'apellido' => $asignacion->transportista?->usuario?->apellido,
                        'telefono' => $asignacion->transportista?->telefono,
                        'ci' => $asignacion->transportista?->ci,
                    ],
                    'vehiculo' => [
                        'placa' => $asignacion->vehiculo?->placa,
                        'tipo' => $asignacion->vehiculo?->tipo,
                    ],
                    'tipo_transporte' => [
                        'nombre' => $asignacion->tipoTransporte?->nombre,
                        'descripcion' => $asignacion->tipoTransporte?->descripcion,
                    ],
                    'recogidaEntrega' => [
                        'fecha_recogida' => $asignacion->recogidaEntrega?->fecha_recogida,
                        'hora_recogida' => $asignacion->recogidaEntrega?->hora_recogida,
                        'hora_entrega' => $asignacion->recogidaEntrega?->hora_entrega,
                        'instrucciones_recogida' => $asignacion->recogidaEntrega?->instrucciones_recogida,
                        'instrucciones_entrega' => $asignacion->recogidaEntrega?->instrucciones_entrega,
                    ],
                    'cargas' => $asignacion->cargas,
                    'firmaTransportista' => $asignacion->firmaTransportista?->imagenfirma,
                    'firma' => $asignacion->firmaEnvio?->imagenfirma,
                ];

                // Incluir checklists solo si es admin
                if ($rol === 'admin') {
                    $particion['checklistCondiciones'] = $asignacion->checklistCondiciones;
                    $particion['checklistIncidentes'] = $asignacion->checklistIncidentes;
                }

                return $particion;
            });

            return response()->json([
                'id_envio' => $envio->id,
                'nombre_cliente' => $envio->usuario->nombre . ' ' . $envio->usuario->apellido,
                'estado' => $envio->estado,
                'fecha_creacion' => $envio->fecha_creacion,
                'fecha_inicio' => $envio->fecha_inicio,
                'fecha_entrega' => $envio->fecha_entrega,
                'nombre_origen' => $envio->direccion?->nombreorigen ?? '—',
                'nombre_destino' => $envio->direccion?->nombredestino ?? '—',
                'particiones' => $particiones,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al generar documento: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al generar documento'], 500);
        }
    }

    /**
     * Generar documento de partición específica
     */
    public function generarDocumentoParticion(Request $request, int $id_asignacion)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $rol = $usuario['rol'];
            $id_usuario = $usuario['id'];

            $asignacion = AsignacionMultiple::with([
                'envio.usuario:id,nombre,apellido',
                'envio.direccion:id,nombreorigen,nombredestino',
                'vehiculo:id,placa,tipo',
                'transportista.usuario:id,nombre,apellido',
                'tipoTransporte:id,nombre,descripcion',
                'recogidaEntrega',
                'cargas',
                'checklistCondiciones',
                'checklistIncidentes',
                'firmaEnvio',
                'firmaTransportista'
            ])->find($id_asignacion);

            if (!$asignacion) {
                return response()->json(['error' => 'Asignación no encontrada'], 404);
            }

            // Validar permisos
            if ($rol !== 'admin' && $asignacion->envio->id_usuario !== $id_usuario) {
                return response()->json(['error' => 'No tienes acceso a esta asignación'], 403);
            }

            $particion = [
                'id_asignacion' => $asignacion->id,
                'estado' => $asignacion->estado,
                'fecha_asignacion' => $asignacion->fecha_asignacion,
                'fecha_inicio' => $asignacion->fecha_inicio,
                'fecha_fin' => $asignacion->fecha_fin,
                'transportista' => [
                    'nombre' => $asignacion->transportista?->usuario?->nombre,
                    'apellido' => $asignacion->transportista?->usuario?->apellido,
                    'telefono' => $asignacion->transportista?->telefono,
                    'ci' => $asignacion->transportista?->ci,
                ],
                'vehiculo' => [
                    'placa' => $asignacion->vehiculo?->placa,
                    'tipo' => $asignacion->vehiculo?->tipo,
                ],
                'tipo_transporte' => [
                    'nombre' => $asignacion->tipoTransporte?->nombre,
                    'descripcion' => $asignacion->tipoTransporte?->descripcion,
                ],
                'recogidaEntrega' => [
                    'fecha_recogida' => $asignacion->recogidaEntrega?->fecha_recogida,
                    'hora_recogida' => $asignacion->recogidaEntrega?->hora_recogida,
                    'hora_entrega' => $asignacion->recogidaEntrega?->hora_entrega,
                    'instrucciones_recogida' => $asignacion->recogidaEntrega?->instrucciones_recogida,
                    'instrucciones_entrega' => $asignacion->recogidaEntrega?->instrucciones_entrega,
                ],
                'cargas' => $asignacion->cargas,
                'firma' => $asignacion->firmaEnvio?->imagenfirma,
                'firma_transportista' => $asignacion->firmaTransportista?->imagenfirma,
            ];

            // Incluir checklists solo si es admin
            if ($rol === 'admin') {
                $particion['checklistCondiciones'] = $asignacion->checklistCondiciones;
                $particion['checklistIncidentes'] = $asignacion->checklistIncidentes;
            }

            return response()->json([
                'id_envio' => $asignacion->id_envio,
                'nombre_cliente' => $asignacion->envio->usuario->nombre . ' ' . $asignacion->envio->usuario->apellido,
                'estado_envio' => $asignacion->envio->estado,
                'fecha_creacion' => $asignacion->envio->fecha_creacion,
                'fecha_inicio' => $asignacion->envio->fecha_inicio,
                'fecha_entrega' => $asignacion->envio->fecha_entrega,
                'nombre_origen' => $asignacion->envio->direccion?->nombreorigen ?? '—',
                'nombre_destino' => $asignacion->envio->direccion?->nombredestino ?? '—',
                'particion' => $particion,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al generar documento de partición: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al generar documento'], 500);
        }
    }

    /**
     * Obtener solo particiones en curso del cliente
     */
    public function obtenerParticionesEnCursoCliente(Request $request)
    {
        try {
            $usuario = $request->attributes->get('usuario');
            $userId = $usuario['id'];
            $rol = $usuario['rol'];

            // Solo clientes pueden acceder a este endpoint
            if ($rol !== 'cliente') {
                return response()->json(['error' => 'Solo los clientes pueden ver sus particiones en curso'], 403);
            }

            $particiones = AsignacionMultiple::with([
                'envio.direccion:id,nombreorigen,nombredestino',
                'vehiculo:id,placa,tipo',
                'tipoTransporte:id,nombre,descripcion',
                'recogidaEntrega',
                'cargas'
            ])
            ->whereHas('envio', function ($query) use ($userId) {
                $query->where('id_usuario', $userId);
            })
            ->where('estado', 'En curso')
            ->get();

            $particiones = $particiones->map(function ($particion) {
                return [
                    'id_asignacion' => $particion->id,
                    'estado' => $particion->estado,
                    'fecha_asignacion' => $particion->fecha_asignacion,
                    'fecha_inicio' => $particion->fecha_inicio,
                    'nombre_origen' => $particion->envio->direccion?->nombreorigen ?? "—",
                    'nombre_destino' => $particion->envio->direccion?->nombredestino ?? "—",
                    'vehiculo' => [
                        'placa' => $particion->vehiculo?->placa,
                        'tipo' => $particion->vehiculo?->tipo,
                    ],
                    'tipoTransporte' => [
                        'nombre' => $particion->tipoTransporte?->nombre,
                        'descripcion' => $particion->tipoTransporte?->descripcion,
                    ],
                    'recogidaEntrega' => [
                        'fecha_recogida' => $particion->recogidaEntrega?->fecha_recogida,
                        'hora_recogida' => $particion->recogidaEntrega?->hora_recogida,
                        'hora_entrega' => $particion->recogidaEntrega?->hora_entrega,
                        'instrucciones_recogida' => $particion->recogidaEntrega?->instrucciones_recogida,
                        'instrucciones_entrega' => $particion->recogidaEntrega?->instrucciones_entrega,
                    ],
                    'cargas' => $particion->cargas,
                ];
            });

            return response()->json($particiones);

        } catch (\Exception $e) {
            \Log::error('Error al obtener particiones en curso: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener particiones en curso'], 500);
        }
    }
}


