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
use App\Models\ChecklistCondicion;
use App\Models\ChecklistCondicionDetalle;
use App\Models\ChecklistIncidente;
use App\Models\ChecklistIncidenteDetalle;
use App\Models\IncidentesTransporte;
use App\Models\CatalogoCarga;
use App\Models\EstadosAsignacionMultiple;
use App\Models\EstadosEnvio;
use App\Models\EstadosVehiculo;
use App\Models\EstadosTransportista;
use App\Models\EstadosQrToken;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
use App\Http\Controllers\Api\Helpers\UsuarioHelper;
use App\Models\FirmaEnvio;
use App\Models\FirmaTransportista;
use App\Models\QrToken;
use App\Models\Usuario;
use Illuminate\Support\Str;
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

        // Validar que la dirección exista
        $direccion = Direccion::find($idDireccion);
        if (!$direccion) {
            return response()->json(['error' => 'La dirección no existe'], Response::HTTP_BAD_REQUEST);
        }

        return DB::transaction(function () use ($idUsuario, $idDireccion, $particiones) {
            $envio = Envio::create([
                'id_usuario' => $idUsuario,
                'id_direccion' => $idDireccion,
            ]);

            // Crear estado inicial en historial
            EstadoHelper::actualizarEstadoEnvio($envio->id, 'Pendiente');

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

                // Obtener o crear estado "Pendiente"
                $idEstadoPendiente = EstadoHelper::obtenerEstadoAsignacionPorNombre('Pendiente');

                // Generar código de acceso único de 6 caracteres
                $codigoAcceso = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

                $asignacion = AsignacionMultiple::create([
                    'id_envio' => $envio->id,
                    'id_tipo_transporte' => $idTipoTransporte,
                    'id_estado_asignacion' => $idEstadoPendiente,
                    'id_recogida_entrega' => $r->id,
                    'codigo_acceso' => $codigoAcceso,
                ]);

                foreach ($cargas as $carga) {
                    // Buscar o crear catalogo de carga
                    $catalogo = CatalogoCarga::firstOrCreate(
                        [
                            'tipo' => $carga['tipo'],
                            'variedad' => $carga['variedad'],
                            'empaque' => $carga['empaquetado'],
                        ],
                        ['descripcion' => null]
                    );

                    $c = Carga::create([
                        'id_catalogo_carga' => $catalogo->id,
                        'cantidad' => $carga['cantidad'],
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

            // Validar duplicados en recursos (Transportistas y Vehículos)
            $transportistasIds = array_column($particiones, 'id_transportista');
            $vehiculosIds = array_column($particiones, 'id_vehiculo');

            if (count($transportistasIds) !== count(array_unique($transportistasIds))) {
                return response()->json(['error' => 'No se puede asignar el mismo transportista a múltiples particiones en el mismo envío.'], 400);
            }
            if (count($vehiculosIds) !== count(array_unique($vehiculosIds))) {
                return response()->json(['error' => 'No se puede asignar el mismo vehículo a múltiples particiones en el mismo envío.'], 400);
            }

            return DB::transaction(function () use ($id_usuario_cliente, $ubicacion, $particiones) {
                // 1. Guardar ubicación en PostgreSQL (en lugar de MongoDB)
                $nuevaUbicacion = Direccion::create([
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
                    'id_direccion' => $nuevaUbicacion->id,
                ]);

                // Crear estado inicial en historial
                EstadoHelper::actualizarEstadoEnvio($envio->id, 'Asignado');

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
                    $transportista = Transportista::with('estadoTransportista')->find($id_transportista);
                    $vehiculo = Vehiculo::with('estadoVehiculo')->find($id_vehiculo);

                    if (!$transportista || $transportista->estadoTransportista?->nombre !== 'Disponible') {
                        throw new \Exception("Transportista {$id_transportista} no disponible");
                    }

                    if (!$vehiculo || $vehiculo->estadoVehiculo?->nombre !== 'Disponible') {
                        throw new \Exception("Vehículo {$id_vehiculo} no disponible");
                    }

                    // 6. Insertar Asignación
                    $idEstadoPendiente = EstadoHelper::obtenerEstadoAsignacionPorNombre('Pendiente');
                    
                    // Generar código de acceso único de 6 caracteres
                    $codigoAcceso = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
                    
                    $asignacion = AsignacionMultiple::create([
                        'id_envio' => $envio->id,
                        'id_transportista' => $id_transportista,
                        'id_vehiculo' => $id_vehiculo,
                        'id_estado_asignacion' => $idEstadoPendiente,
                        'id_tipo_transporte' => $id_tipo_transporte,
                        'id_recogida_entrega' => $recogida->id,
                        'codigo_acceso' => $codigoAcceso,
                    ]);

                    // 7. Marcar transportista y vehículo como no disponibles
                    $idEstadoNoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('No Disponible');
                    $idEstadoVehiculoNoDisponible = EstadoHelper::obtenerEstadoVehiculoPorNombre('No Disponible');
                    $transportista->update(['id_estado_transportista' => $idEstadoNoDisponible]);
                    $vehiculo->update(['id_estado_vehiculo' => $idEstadoVehiculoNoDisponible]);

                    // 8. Insertar cargas y relacionarlas con la asignación
                    foreach ($cargas as $carga) {
                        // Buscar o crear catalogo de carga
                        $catalogo = CatalogoCarga::firstOrCreate(
                            [
                                'tipo' => $carga['tipo'],
                                'variedad' => $carga['variedad'],
                                'empaque' => $carga['empaquetado'],
                            ],
                            ['descripcion' => null]
                        );

                        $nuevaCarga = Carga::create([
                            'id_catalogo_carga' => $catalogo->id,
                            'cantidad' => $carga['cantidad'],
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
                'usuario.persona:id,nombre,apellido',
                'usuario.rol:id,codigo,nombre',
                'direccion:id,nombreorigen,nombredestino',
                'historialEstados.estadoEnvio:id,nombre'
            ]);

            // Si no es admin, solo mostrar sus envíos
            if (!UsuarioHelper::tieneRol($usuario, 'admin')) {
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
                $estadoActual = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
                return [
                    'id' => $envio->id,
                    'id_usuario' => $envio->id_usuario,
                    'estado' => $estadoActual ?? 'Pendiente',
                    'fecha_creacion' => $envio->fecha_creacion,
                    'fecha_inicio' => $envio->fecha_inicio,
                    'fecha_entrega' => $envio->fecha_entrega,
                    'id_direccion' => $envio->id_direccion,
                    'usuario' => [
                        'id' => $envio->usuario?->id,
                        'nombre' => $envio->usuario?->persona?->nombre,
                        'apellido' => $envio->usuario?->persona?->apellido,
                        'rol' => $envio->usuario?->rol?->codigo,
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
                'usuario.persona:id,nombre,apellido',
                'usuario.rol:id,codigo,nombre',
                'asignaciones.transportista.estadoTransportista:id,nombre',
                'asignaciones.transportista.usuario.persona:id,nombre,apellido,ci,telefono',
                'asignaciones.transportista:id,id_usuario',
                'asignaciones.vehiculo.tipoVehiculo:id,nombre',
                'asignaciones.vehiculo.estadoVehiculo:id,nombre',
                'asignaciones.vehiculo:id,placa,capacidad',
                'asignaciones.estadoAsignacion:id,nombre',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas.catalogoCarga:id,tipo,variedad,empaque',
                'direccion:id,nombreorigen,nombredestino,origen_lng,origen_lat,destino_lng,destino_lat,rutageojson'
            ])->find($id);

            if (!$envio) {
                return response()->json(['error' => 'Envío no encontrado'], 404);
            }

            // Validar permisos
            if (!UsuarioHelper::tieneRol($usuario, 'admin') && $envio->id_usuario !== $usuario['id']) {
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
                $cargasTransformadas = $asignacion->cargas->map(function ($carga) {
                    return [
                        'id' => $carga->id,
                        'tipo' => $carga->catalogoCarga?->tipo,
                        'variedad' => $carga->catalogoCarga?->variedad,
                        'empaquetado' => $carga->catalogoCarga?->empaque,
                        'cantidad' => $carga->cantidad,
                        'peso' => $carga->peso,
                    ];
                });

                return [
                    'id_asignacion' => $asignacion->id,
                    'codigo_acceso' => $asignacion->codigo_acceso,
                    'id_transportista' => $asignacion->id_transportista,
                    'id_vehiculo' => $asignacion->id_vehiculo,
                    'estado' => $asignacion->estadoAsignacion?->nombre ?? 'Pendiente',
                    'fecha_asignacion' => $asignacion->fecha_asignacion,
                    'fecha_inicio' => $asignacion->fecha_inicio,
                    'fecha_fin' => $asignacion->fecha_fin,
                    'transportista' => [
                        'nombre' => $asignacion->transportista?->usuario?->persona?->nombre,
                        'apellido' => $asignacion->transportista?->usuario?->persona?->apellido,
                        'telefono' => $asignacion->transportista?->usuario?->persona?->telefono,
                        'ci' => $asignacion->transportista?->usuario?->persona?->ci,
                    ],
                    'vehiculo' => [
                        'placa' => $asignacion->vehiculo?->placa,
                        'tipo' => $asignacion->vehiculo?->tipoVehiculo?->nombre,
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
                    'cargas' => $cargasTransformadas,
                ];
            });

            // Calcular estado resumen
            $total = $envio->particiones->count();
            $activos = $envio->particiones->filter(function ($p) {
                return $p['estado'] === 'En curso';
            })->count();
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
                $transportista = Transportista::with('estadoTransportista')->find($request->id_transportista);
                $vehiculo = Vehiculo::with('estadoVehiculo')->find($request->id_vehiculo);

                if (!$transportista || $transportista->estadoTransportista?->nombre !== 'Disponible') {
                    return response()->json(['error' => 'Transportista no disponible'], 400);
                }

                if (!$vehiculo || $vehiculo->estadoVehiculo?->nombre !== 'Disponible') {
                    return response()->json(['error' => 'Vehículo no disponible'], 400);
                }

                // Verificar existencia de la partición
                $asignacion = AsignacionMultiple::find($id_asignacion);
                if (!$asignacion) {
                    return response()->json(['error' => 'Partición (Asignación) no encontrada'], 404);
                }

                // Validar que la partición no esté ya completada o en curso
                $estadoActual = $asignacion->estadoAsignacion?->nombre ?? 'Pendiente';
                if (in_array($estadoActual, ['Completado', 'En curso', 'Finalizado', 'Entregado'])) {
                    return response()->json([
                        'error' => 'No se puede asignar a una partición que ya está ' . strtolower($estadoActual),
                        'estado_actual' => $estadoActual
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

                $estadoEnvio = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
                if (in_array($estadoEnvio, ['Completado', 'Finalizado', 'Entregado'])) {
                    return response()->json([
                        'error' => 'No se puede asignar a un envío que ya está ' . strtolower($estadoEnvio),
                        'estado_envio' => $estadoEnvio
                    ], 400);
                }

                // Actualizar la partición
                $idEstadoPendiente = EstadoHelper::obtenerEstadoAsignacionPorNombre('Pendiente');
                $asignacion->update([
                    'id_transportista' => $request->id_transportista,
                    'id_vehiculo' => $request->id_vehiculo,
                    'id_estado_asignacion' => $idEstadoPendiente,
                ]);

                // Marcar como no disponibles
                $idEstadoNoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('No Disponible');
                $idEstadoVehiculoNoDisponible = EstadoHelper::obtenerEstadoVehiculoPorNombre('No Disponible');
                $transportista->update(['id_estado_transportista' => $idEstadoNoDisponible]);
                $vehiculo->update(['id_estado_vehiculo' => $idEstadoVehiculoNoDisponible]);

                // Actualizar estado global del envío para reflejar nueva asignación
                EstadoHelper::actualizarEstadoGlobalEnvio($asignacion->id_envio);

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
            \Log::info('=== Iniciando obtenerMisEnvios ===');
            
            $usuario = $request->attributes->get('usuario');
            \Log::info('Usuario desde request: ', ['usuario' => $usuario]);
            
            if (!$usuario || !isset($usuario['id'])) {
                \Log::error('Usuario no autenticado o sin ID');
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            
            $userId = $usuario['id'];
            \Log::info('User ID: ' . $userId);

            $envios = Envio::with([
                'usuario.persona:id,nombre,apellido',
                'usuario.rol:id,codigo,nombre',
                'asignaciones.transportista.usuario.persona:id,nombre,apellido,ci,telefono',
                'asignaciones.vehiculo.tipoVehiculo:id,nombre',
                'asignaciones.vehiculo:id,placa',
                'asignaciones.estadoAsignacion:id,nombre',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas.catalogoCarga:id,tipo,variedad,empaque',
                'direccion:id,nombreorigen,nombredestino'
            ])->where('id_usuario', $userId)->get();

            \Log::info('Total de envíos encontrados: ' . $envios->count());
            
            if ($envios->isEmpty()) {
                \Log::info('No hay envíos para este usuario');
                return response()->json([]);
            }

            // Transformar la respuesta
            $envios = $envios->map(function ($envio) {
                \Log::info('Procesando envío ID: ' . $envio->id);
                $estadoActual = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
                \Log::info('Estado actual: ' . $estadoActual);
                
                $envio->estado = $estadoActual ?? 'Pendiente';
                $envio->nombre_origen = $envio->direccion?->nombreorigen ?? "—";
                $envio->nombre_destino = $envio->direccion?->nombredestino ?? "—";
                
                \Log::info('Total de asignaciones/particiones: ' . $envio->asignaciones->count());
                
                $envio->particiones = $envio->asignaciones->map(function ($asignacion) {
                    $cargasTransformadas = $asignacion->cargas->map(function ($carga) {
                        return [
                            'id' => $carga->id,
                            'tipo' => $carga->catalogoCarga?->tipo,
                            'variedad' => $carga->catalogoCarga?->variedad,
                            'empaquetado' => $carga->catalogoCarga?->empaque,
                            'cantidad' => $carga->cantidad,
                            'peso' => $carga->peso,
                        ];
                    });

                    return [
                        'id_asignacion' => $asignacion->id,
                        'codigo_acceso' => $asignacion->codigo_acceso,
                        'estado' => $asignacion->estadoAsignacion?->nombre ?? 'Pendiente',
                        'fecha_asignacion' => $asignacion->fecha_asignacion,
                        'fecha_inicio' => $asignacion->fecha_inicio,
                        'fecha_fin' => $asignacion->fecha_fin,
                        'transportista' => [
                            'nombre' => $asignacion->transportista?->usuario?->persona?->nombre,
                            'apellido' => $asignacion->transportista?->usuario?->persona?->apellido,
                            'ci' => $asignacion->transportista?->usuario?->persona?->ci,
                            'telefono' => $asignacion->transportista?->usuario?->persona?->telefono,
                        ],
                        'vehiculo' => [
                            'placa' => $asignacion->vehiculo?->placa,
                            'tipo' => $asignacion->vehiculo?->tipoVehiculo?->nombre,
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
                        'cargas' => $cargasTransformadas,
                    ];
                });

                return $envio;
            });

            \Log::info('Respuesta preparada exitosamente');
            return response()->json($envios);

        } catch (\Exception $e) {
            \Log::error('Error al obtener mis envíos: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error al obtener tus envíos', 'detalle' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener envíos de un usuario específico (para admin)
     */
    public function obtenerEnviosPorUsuario(Request $request, int $id_usuario)
    {
        try {
            $usuarioAuth = $request->attributes->get('usuario');
            
            // Verificar que sea admin
            if (!UsuarioHelper::tieneRol($usuarioAuth, 'admin')) {
                return response()->json(['error' => 'No tienes permiso para ver envíos de otros usuarios'], 403);
            }

            $envios = Envio::with([
                'usuario.persona:id,nombre,apellido',
                'usuario.rol:id,codigo,nombre',
                'asignaciones.transportista.usuario.persona:id,nombre,apellido,ci,telefono',
                'asignaciones.vehiculo.tipoVehiculo:id,nombre',
                'asignaciones.vehiculo:id,placa',
                'asignaciones.estadoAsignacion:id,nombre',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas.catalogoCarga:id,tipo,variedad,empaque',
                'direccion:id,nombreorigen,nombredestino'
            ])->where('id_usuario', $id_usuario)->get();

            // Transformar la respuesta
            $envios = $envios->map(function ($envio) {
                $estadoActual = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
                
                $envio->estado_nombre = $estadoActual ?? 'Pendiente';
                $envio->nombre_origen = $envio->direccion?->nombreorigen ?? "—";
                $envio->nombre_destino = $envio->direccion?->nombredestino ?? "—";
                
                $envio->particiones = $envio->asignaciones->map(function ($asignacion) {
                    $cargasTransformadas = $asignacion->cargas->map(function ($carga) {
                        return [
                            'id' => $carga->id,
                            'tipo' => $carga->catalogoCarga?->tipo,
                            'variedad' => $carga->catalogoCarga?->variedad,
                            'empaquetado' => $carga->catalogoCarga?->empaque,
                            'cantidad' => $carga->cantidad,
                            'peso' => $carga->peso,
                        ];
                    });

                    return [
                        'id_asignacion' => $asignacion->id,
                        'codigo_acceso' => $asignacion->codigo_acceso,
                        'estado' => $asignacion->estadoAsignacion?->nombre ?? 'Pendiente',
                        'fecha_asignacion' => $asignacion->fecha_asignacion,
                        'fecha_inicio' => $asignacion->fecha_inicio,
                        'fecha_fin' => $asignacion->fecha_fin,
                        'transportista' => [
                            'nombre' => $asignacion->transportista?->usuario?->persona?->nombre,
                            'apellido' => $asignacion->transportista?->usuario?->persona?->apellido,
                            'ci' => $asignacion->transportista?->usuario?->persona?->ci,
                            'telefono' => $asignacion->transportista?->usuario?->persona?->telefono,
                        ],
                        'vehiculo' => [
                            'placa' => $asignacion->vehiculo?->placa,
                            'tipo' => $asignacion->vehiculo?->tipoVehiculo?->nombre,
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
                        'cargas' => $cargasTransformadas,
                    ];
                });

                return $envio;
            });

            return response()->json($envios);

        } catch (\Exception $e) {
            \Log::error('Error al obtener envíos por usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los envíos del usuario'], 500);
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
            if (!UsuarioHelper::tieneRol($usuario, 'admin')) {
                return response()->json(['error' => 'Solo los administradores pueden actualizar el estado global'], 403);
            }

            // Verificar que el envío existe
            $envio = Envio::find($id_envio);
            if (!$envio) {
                return response()->json(['error' => 'Envío no encontrado'], 404);
            }

            // Actualizar estado global
            EstadoHelper::actualizarEstadoGlobalEnvio($id_envio);

            return response()->json(['mensaje' => 'Estado global del envío actualizado correctamente']);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar estado global: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al actualizar estado global'], 500);
        }
    }

    /**
     * Generar QR real usando API externa
     */
    private function generarQRReal(string $url): string
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
                $transportista = Transportista::where('id_usuario', $userId)->first();
                if (!$transportista) {
                    return response()->json(['error' => 'No se encontró al transportista'], 403);
                }

                // Verificar asignación válida
                $idEstadoPendiente = EstadoHelper::obtenerEstadoAsignacionPorNombre('Pendiente');
                $asignacion = AsignacionMultiple::with(['envio:id,id_usuario', 'estadoAsignacion'])
                    ->where('id', $id_asignacion)
                    ->where('id_transportista', $transportista->id)
                    ->where('id_estado_asignacion', $idEstadoPendiente)
                    ->first();

                if (!$asignacion) {
                    return response()->json(['error' => 'No tienes acceso o la asignación no está disponible para iniciar'], 403);
                }

                // Verificar checklist por asignación
                $checklist = ChecklistCondicion::where('id_asignacion', $id_asignacion)->first();
                if (!$checklist) {
                    return response()->json(['error' => 'Debes completar el checklist antes de iniciar el viaje'], 400);
                }

                // Actualizar asignación
                $idEstadoEnCurso = EstadoHelper::obtenerEstadoAsignacionPorNombre('En curso');
                $asignacion->update([
                    'id_estado_asignacion' => $idEstadoEnCurso,
                    'fecha_inicio' => now(),
                ]);

                // Actualizar estado de recursos
                $idEstadoEnRuta = EstadoHelper::obtenerEstadoTransportistaPorNombre('En ruta');
                $idEstadoVehiculoEnRuta = EstadoHelper::obtenerEstadoVehiculoPorNombre('En ruta');
                $transportista->update(['id_estado_transportista' => $idEstadoEnRuta]);
                $asignacion->vehiculo->update(['id_estado_vehiculo' => $idEstadoVehiculoEnRuta]);

                // Actualizar estado global del envío
                EstadoHelper::actualizarEstadoGlobalEnvio($asignacion->id_envio);

                // Generar QR automáticamente (si no existe)
                $qrToken = QrToken::where('id_asignacion', $id_asignacion)->first();

                if (!$qrToken) {
                    $nuevoToken = \Str::uuid();
                    // Construir frontend base desde la configuración y asegurar sin slash final
                    $frontend = rtrim(config('app.frontend_url', config('app.url', 'http://localhost')), '/');
                    // URL específica para validar el QR con el token
                    $tokenUrl = $frontend . '/validar-qr/' . $nuevoToken;

                    // Generar imagen QR real usando API
                    $qrBase64 = $this->generarQRReal($tokenUrl);

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
                        'mensaje' => 'Viaje iniciado correctamente para esta asignación',
                        'id_asignacion' => $id_asignacion,
                        'token' => $nuevoToken,
                        'imagenQR' => $qrBase64,
                        'fecha_creacion' => $qrToken->fecha_creacion,
                        'codigo_acceso' => $asignacion->codigo_acceso ?? null,
                    ]);
                } else {
                    return response()->json([
                        'mensaje' => 'Viaje iniciado correctamente para esta asignación (QR ya existía)',
                        'id_asignacion' => $id_asignacion,
                        'token' => $qrToken->token,
                        'imagenQR' => $qrToken->imagenqr,
                        'fecha_creacion' => $qrToken->fecha_creacion,
                        'codigo_acceso' => $asignacion->codigo_acceso ?? null,
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

            // Obtener transportista vinculado al usuario autenticado
            $transportista = Transportista::where('id_usuario', $id_usuario)->first();
            if (!$transportista) {
                return response()->json(['error' => 'No eres un transportista válido'], 404);
            }

            // Obtener asignaciones de este transportista
            $asignaciones = AsignacionMultiple::with([
                'envio.usuario.persona:id,nombre,apellido',
                'envio.usuario.rol:id,codigo,nombre',
                'envio.direccion:id,nombreorigen,nombredestino,origen_lng,origen_lat,destino_lng,destino_lat,rutageojson',
                'envio.historialEstados.estadoEnvio:id,nombre',
                'vehiculo.tipoVehiculo:id,nombre',
                'vehiculo:id,placa',
                'estadoAsignacion:id,nombre',
                'tipoTransporte:id,nombre,descripcion',
                'recogidaEntrega',
                'cargas.catalogoCarga:id,tipo,variedad,empaque'
            ])->where('id_transportista', $transportista->id)->get();

            // Transformar la respuesta
            $enviosCompletos = $asignaciones->map(function ($asignacion) {
                $envio = $asignacion->envio;
                
                $estadoEnvio = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
                $cargasTransformadas = $asignacion->cargas->map(function ($carga) {
                    return [
                        'id' => $carga->id,
                        'tipo' => $carga->catalogoCarga?->tipo,
                        'variedad' => $carga->catalogoCarga?->variedad,
                        'empaquetado' => $carga->catalogoCarga?->empaque,
                        'cantidad' => $carga->cantidad,
                        'peso' => $carga->peso,
                    ];
                });

                return [
                    'id_asignacion' => $asignacion->id,
                    'codigo_acceso' => $asignacion->codigo_acceso,
                    'estado' => $asignacion->estadoAsignacion?->nombre ?? 'Pendiente',
                    'fecha_inicio' => $asignacion->fecha_inicio,
                    'fecha_fin' => $asignacion->fecha_fin,
                    'fecha_asignacion' => $asignacion->fecha_asignacion,
                    'id_envio' => $asignacion->id_envio,
                    'id_vehiculo' => $asignacion->id_vehiculo,
                    'id_recogida_entrega' => $asignacion->id_recogida_entrega,
                    'id_tipo_transporte' => $asignacion->id_tipo_transporte,
                    'estado_envio' => $estadoEnvio ?? 'Pendiente',
                    'fecha_creacion' => $envio->fecha_creacion,
                    'id_usuario' => $envio->id_usuario,
                    'id_ubicacion_mongo' => $envio->id_direccion, // Adaptado para PostgreSQL
                    'placa' => $asignacion->vehiculo?->placa,
                    'tipo_vehiculo' => $asignacion->vehiculo?->tipoVehiculo?->nombre,
                    'tipo_transporte' => $asignacion->tipoTransporte?->nombre,
                    'descripcion_transporte' => $asignacion->tipoTransporte?->descripcion,
                    'nombre_cliente' => $envio->usuario?->persona?->nombre,
                    'apellido_cliente' => $envio->usuario?->persona?->apellido,
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
                    'cargas' => $cargasTransformadas,
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
                $transportista = Transportista::where('id_usuario', $id_usuario)->first();
                if (!$transportista) {
                    return response()->json(['error' => 'No tienes permisos para esta acción'], 403);
                }

                // Obtener asignación
                $asignacion = AsignacionMultiple::with('estadoAsignacion')->find($id_asignacion);
                if (!$asignacion) {
                    return response()->json(['error' => 'Asignación no encontrada'], 404);
                }

                // Validar que le pertenece al transportista y esté en curso
                if ($asignacion->id_transportista !== $transportista->id) {
                    return response()->json(['error' => 'No tienes permiso para finalizar esta asignación'], 403);
                }

                $estadoActual = $asignacion->estadoAsignacion?->nombre ?? 'Pendiente';
                if ($estadoActual !== 'En curso') {
                    return response()->json(['error' => 'Esta asignación no está en curso'], 400);
                }

                // Validar que exista checklist de incidentes
                $checklist = ChecklistIncidente::where('id_asignacion', $id_asignacion)->first();
                $oldChecklist = IncidentesTransporte::where('id_asignacion', $id_asignacion)->first();

                if (!$checklist && !$oldChecklist) {
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
                $idEstadoEntregado = EstadoHelper::obtenerEstadoAsignacionPorNombre('Entregado');
                $asignacion->update([
                    'id_estado_asignacion' => $idEstadoEntregado,
                    'fecha_fin' => now(),
                ]);

                // Liberar transportista y vehículo
                $idEstadoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('Disponible');
                $idEstadoVehiculoDisponible = EstadoHelper::obtenerEstadoVehiculoPorNombre('Disponible');
                $transportista->update(['id_estado_transportista' => $idEstadoDisponible]);
                if ($vehiculo) {
                    $vehiculo->update(['id_estado_vehiculo' => $idEstadoVehiculoDisponible]);
                }

                // Actualizar estado global del envío
                EstadoHelper::actualizarEstadoGlobalEnvio($asignacion->id_envio);

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
                'condiciones' => 'required|array|min:1',
                'condiciones.*.id_condicion' => 'required|integer|exists:condiciones_transporte,id',
                'condiciones.*.valor' => 'required|boolean',
                'condiciones.*.comentario' => 'nullable|string|max:255',
                'observaciones' => 'nullable|string|max:255',
            ]);

            return DB::transaction(function () use ($id_asignacion, $id_usuario, $request) {
                $transportista = Transportista::where('id_usuario', $id_usuario)->first();
                if (!$transportista) {
                    return response()->json(['error' => 'No tienes permiso para esta asignación'], 403);
                }

                $asignacion = AsignacionMultiple::with(['transportista', 'estadoAsignacion'])
                    ->find($id_asignacion);

                if (!$asignacion) {
                    return response()->json(['error' => 'Asignación no encontrada'], 404);
                }

                // Validar que el transportista corresponda (ajustar según tu lógica)
                if ($asignacion->id_transportista !== $transportista->id) {
                    return response()->json(['error' => 'No tienes permiso para esta asignación'], 403);
                }

                $estadoActual = $asignacion->estadoAsignacion?->nombre ?? 'Pendiente';
                if ($estadoActual !== 'Pendiente') {
                    return response()->json(['error' => 'El checklist solo se puede registrar si la asignación está pendiente'], 400);
                }

                // Verificar si ya existe un checklist
                $yaExiste = ChecklistCondicion::where('id_asignacion', $id_asignacion)->first();
                if ($yaExiste) {
                    return response()->json(['error' => 'Este checklist ya fue registrado'], 400);
                }

                // Crear checklist principal
                $checklist = ChecklistCondicion::create([
                    'id_asignacion' => $id_asignacion,
                    'observaciones' => $request->observaciones,
                    'fecha' => now(),
                ]);

                // Crear detalles del checklist
                foreach ($request->condiciones as $condicion) {
                    ChecklistCondicionDetalle::create([
                        'id_checklist' => $checklist->id,
                        'id_condicion' => $condicion['id_condicion'],
                        'valor' => $condicion['valor'],
                        'comentario' => $condicion['comentario'] ?? null,
                    ]);
                }

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
                'incidentes' => 'required|array',
                'incidentes.*.id_tipo_incidente' => 'required|integer|exists:tipos_incidente_transporte,id',
                'incidentes.*.descripcion_incidente' => 'nullable|string|max:255',
            ]);

            return DB::transaction(function () use ($id_asignacion, $id_usuario, $request) {
                $transportista = Transportista::where('id_usuario', $id_usuario)->first();
                if (!$transportista) {
                    return response()->json(['error' => 'No tienes permiso para esta asignación'], 403);
                }

                $asignacion = AsignacionMultiple::with(['transportista', 'estadoAsignacion'])
                    ->find($id_asignacion);

                if (!$asignacion) {
                    return response()->json(['error' => 'Asignación no encontrada'], 404);
                }

                // Validar que el transportista corresponda (ajustar según tu lógica)
                if ($asignacion->id_transportista !== $transportista->id) {
                    return response()->json(['error' => 'No tienes permiso para esta asignación'], 403);
                }

                // Permitir registrar checklist cuando la asignación esté EN CURSO
                $estadoActual = $asignacion->estadoAsignacion?->nombre ?? 'Pendiente';
                if ($estadoActual !== 'En curso') {
                    return response()->json(['error' => 'Solo puedes registrar el checklist si el viaje está en curso'], 400);
                }

                // Verificar si ya existe un checklist
                $yaExiste = ChecklistIncidente::where('id_asignacion', $id_asignacion)->first();
                if ($yaExiste) {
                    return response()->json(['error' => 'Este checklist ya fue registrado'], 400);
                }

                // Crear checklist principal
                $checklist = ChecklistIncidente::create([
                    'id_asignacion' => $id_asignacion,
                    'fecha' => now(),
                ]);

                // Insertar incidentes (puede haber múltiples)
                foreach ($request->incidentes as $incidente) {
                    ChecklistIncidenteDetalle::create([
                        'id_checklist' => $checklist->id,
                        'id_tipo_incidente' => $incidente['id_tipo_incidente'],
                        'ocurrio' => true,
                        'descripcion' => $incidente['descripcion_incidente'] ?? null,
                    ]);
                }

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
                $transportista = Transportista::with('estadoTransportista')->find($request->id_transportista);
                $vehiculo = Vehiculo::with('estadoVehiculo')->find($request->id_vehiculo);

                if (!$transportista || $transportista->estadoTransportista?->nombre !== 'Disponible') {
                    return response()->json(['error' => 'Transportista no disponible'], 400);
                }

                if (!$vehiculo || $vehiculo->estadoVehiculo?->nombre !== 'Disponible') {
                    return response()->json(['error' => 'Vehículo no disponible'], 400);
                }

                // Verificar existencia del envío
                $envio = Envio::find($id_envio);
                if (!$envio) {
                    return response()->json(['error' => 'Envío no encontrado'], 404);
                }

                // Validar que el envío no esté completado
                $estadoEnvio = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
                if (in_array($estadoEnvio, ['Completado', 'Finalizado', 'Entregado'])) {
                    return response()->json([
                        'error' => 'No se puede asignar a un envío que ya está ' . strtolower($estadoEnvio),
                        'estado_envio' => $estadoEnvio
                    ], 400);
                }

                // Buscar o crear catalogo de carga
                $catalogo = CatalogoCarga::firstOrCreate(
                    [
                        'tipo' => $request->carga['tipo'],
                        'variedad' => $request->carga['variedad'],
                        'empaque' => $request->carga['empaquetado'],
                    ],
                    ['descripcion' => null]
                );

                // Insertar carga
                $carga = Carga::create([
                    'id_catalogo_carga' => $catalogo->id,
                    'cantidad' => $request->carga['cantidad'],
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
                $idEstadoPendiente = EstadoHelper::obtenerEstadoAsignacionPorNombre('Pendiente');
                $asignacion = AsignacionMultiple::create([
                    'id_envio' => $id_envio,
                    'id_transportista' => $request->id_transportista,
                    'id_vehiculo' => $request->id_vehiculo,
                    'id_estado_asignacion' => $idEstadoPendiente,
                    'id_tipo_transporte' => $request->id_tipo_transporte,
                    'id_recogida_entrega' => $recogida->id,
                ]);

                // Relacionar carga con asignación
                AsignacionCarga::create([
                    'id_asignacion' => $asignacion->id,
                    'id_carga' => $carga->id,
                ]);

                // Actualizar estados
                $idEstadoNoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('No Disponible');
                $idEstadoVehiculoNoDisponible = EstadoHelper::obtenerEstadoVehiculoPorNombre('No Disponible');
                $transportista->update(['id_estado_transportista' => $idEstadoNoDisponible]);
                $vehiculo->update(['id_estado_vehiculo' => $idEstadoVehiculoNoDisponible]);

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
                'usuario.persona:id,nombre,apellido',
                'usuario.rol:id,codigo,nombre',
                'asignaciones.transportista.usuario.persona:id,nombre,apellido,ci,telefono',
                'asignaciones.vehiculo.tipoVehiculo:id,nombre',
                'asignaciones.vehiculo:id,placa',
                'asignaciones.estadoAsignacion:id,nombre',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas.catalogoCarga:id,tipo,variedad,empaque',
                'asignaciones.checklistCondicion.detalles.condicion:id,titulo',
                'asignaciones.incidentes.tipoIncidente:id,titulo',
                'asignaciones.firmaEnvio',
                'asignaciones.firmaTransportista',
                'direccion:id,nombreorigen,nombredestino',
                'historialEstados.estadoEnvio:id,nombre'
            ])->find($id_envio);

            if (!$envio) {
                return response()->json(['error' => 'Envío no encontrado'], 404);
            }

            // Validar si el envío está completamente ENTREGADO
            $estadoEnvio = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
            if ($estadoEnvio !== 'Entregado') {
                return response()->json(['error' => 'El documento solo se puede generar cuando el envío esté completamente entregado.'], 400);
            }

            // Validar si el cliente tiene permiso (si no es admin)
            if (!UsuarioHelper::tieneRol($usuario, 'admin') && $envio->id_usuario !== $id_usuario) {
                return response()->json(['error' => 'No tienes acceso a este envío'], 403);
            }

            // Transformar asignaciones a particiones
            $particiones = $envio->asignaciones->map(function ($asignacion) use ($rol, $usuario) {
                $cargasTransformadas = $asignacion->cargas->map(function ($carga) {
                    return [
                        'id' => $carga->id,
                        'tipo' => $carga->catalogoCarga?->tipo,
                        'variedad' => $carga->catalogoCarga?->variedad,
                        'empaquetado' => $carga->catalogoCarga?->empaque,
                        'cantidad' => $carga->cantidad,
                        'peso' => $carga->peso,
                    ];
                });

                $particion = [
                    'id_asignacion' => $asignacion->id,
                    'estado' => $asignacion->estadoAsignacion?->nombre ?? 'Pendiente',
                    'fecha_asignacion' => $asignacion->fecha_asignacion,
                    'fecha_inicio' => $asignacion->fecha_inicio,
                    'fecha_fin' => $asignacion->fecha_fin,
                    'transportista' => [
                        'nombre' => $asignacion->transportista?->usuario?->persona?->nombre,
                        'apellido' => $asignacion->transportista?->usuario?->persona?->apellido,
                        'telefono' => $asignacion->transportista?->usuario?->persona?->telefono,
                        'ci' => $asignacion->transportista?->usuario?->persona?->ci,
                    ],
                    'vehiculo' => [
                        'placa' => $asignacion->vehiculo?->placa,
                        'tipo' => $asignacion->vehiculo?->tipoVehiculo?->nombre,
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
                    'cargas' => $cargasTransformadas,
                    'firmaTransportista' => $asignacion->firmaTransportista?->imagenfirma,
                    'firma' => $asignacion->firmaEnvio?->imagenfirma,
                ];

                // Incluir checklists solo si es admin
                if (UsuarioHelper::tieneRol($usuario, 'admin')) {
                    $particion['checklistCondiciones'] = $asignacion->checklistCondicion?->detalles ?? [];
                    $particion['checklistIncidentes'] = $asignacion->incidentes ?? [];
                }

                return $particion;
            });

            return response()->json([
                'id_envio' => $envio->id,
                'nombre_cliente' => ($envio->usuario?->persona?->nombre ?? '') . ' ' . ($envio->usuario?->persona?->apellido ?? ''),
                'estado' => $estadoEnvio ?? 'Pendiente',
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
                'envio.usuario.persona:id,nombre,apellido',
                'envio.usuario.rol:id,codigo,nombre',
                'envio.direccion:id,nombreorigen,nombredestino',
                'envio.historialEstados.estadoEnvio:id,nombre',
                'vehiculo.tipoVehiculo:id,nombre',
                'vehiculo:id,placa',
                'estadoAsignacion:id,nombre',
                'transportista.usuario.persona:id,nombre,apellido,ci,telefono',
                'tipoTransporte:id,nombre,descripcion',
                'recogidaEntrega',
                'cargas.catalogoCarga:id,tipo,variedad,empaque',
                'checklistCondicion.detalles.condicion:id,titulo',
                'incidentes.tipoIncidente:id,titulo',
                'firmaEnvio',
                'firmaTransportista'
            ])->find($id_asignacion);

            if (!$asignacion) {
                return response()->json(['error' => 'Asignación no encontrada'], 404);
            }

            // Validar permisos
            if (!UsuarioHelper::tieneRol($usuario, 'admin') && $asignacion->envio->id_usuario !== $id_usuario) {
                return response()->json(['error' => 'No tienes acceso a esta asignación'], 403);
            }

            $cargasTransformadas = $asignacion->cargas->map(function ($carga) {
                return [
                    'id' => $carga->id,
                    'tipo' => $carga->catalogoCarga?->tipo,
                    'variedad' => $carga->catalogoCarga?->variedad,
                    'empaquetado' => $carga->catalogoCarga?->empaque,
                    'cantidad' => $carga->cantidad,
                    'peso' => $carga->peso,
                ];
            });

            $estadoEnvio = EstadoHelper::obtenerEstadoActualEnvio($asignacion->envio->id);

            $particion = [
                'id_asignacion' => $asignacion->id,
                'estado' => $asignacion->estadoAsignacion?->nombre ?? 'Pendiente',
                'fecha_asignacion' => $asignacion->fecha_asignacion,
                'fecha_inicio' => $asignacion->fecha_inicio,
                'fecha_fin' => $asignacion->fecha_fin,
                'transportista' => [
                    'nombre' => $asignacion->transportista?->usuario?->persona?->nombre,
                    'apellido' => $asignacion->transportista?->usuario?->persona?->apellido,
                    'telefono' => $asignacion->transportista?->usuario?->persona?->telefono,
                    'ci' => $asignacion->transportista?->usuario?->persona?->ci,
                ],
                'vehiculo' => [
                    'placa' => $asignacion->vehiculo?->placa,
                    'tipo' => $asignacion->vehiculo?->tipoVehiculo?->nombre,
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
                'cargas' => $cargasTransformadas,
                'firma' => $asignacion->firmaEnvio?->imagenfirma,
                'firma_transportista' => $asignacion->firmaTransportista?->imagenfirma,
            ];

            // Incluir checklists solo si es admin
            if (UsuarioHelper::tieneRol($usuario, 'admin')) {
                $particion['checklistCondiciones'] = $asignacion->checklistCondicion?->detalles ?? [];
                $particion['checklistIncidentes'] = $asignacion->incidentes ?? [];
            }

            return response()->json([
                'id_envio' => $asignacion->id_envio,
                'nombre_cliente' => ($asignacion->envio->usuario?->persona?->nombre ?? '') . ' ' . ($asignacion->envio->usuario?->persona?->apellido ?? ''),
                'estado_envio' => $estadoEnvio ?? 'Pendiente',
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
            if (!UsuarioHelper::tieneRol($usuario, 'cliente')) {
                return response()->json(['error' => 'Solo los clientes pueden ver sus particiones en curso'], 403);
            }

            $idEstadoEnCurso = EstadoHelper::obtenerEstadoAsignacionPorNombre('En curso');
            $particiones = AsignacionMultiple::with([
                'envio.direccion:id,nombreorigen,nombredestino',
                'vehiculo.tipoVehiculo:id,nombre',
                'vehiculo:id,placa',
                'estadoAsignacion:id,nombre',
                'tipoTransporte:id,nombre,descripcion',
                'recogidaEntrega',
                'cargas.catalogoCarga:id,tipo,variedad,empaque'
            ])
            ->whereHas('envio', function ($query) use ($userId) {
                $query->where('id_usuario', $userId);
            })
            ->where('id_estado_asignacion', $idEstadoEnCurso)
            ->get();

            $particiones = $particiones->map(function ($particion) {
                $cargasTransformadas = $particion->cargas->map(function ($carga) {
                    return [
                        'id' => $carga->id,
                        'tipo' => $carga->catalogoCarga?->tipo,
                        'variedad' => $carga->catalogoCarga?->variedad,
                        'empaquetado' => $carga->catalogoCarga?->empaque,
                        'cantidad' => $carga->cantidad,
                        'peso' => $carga->peso,
                    ];
                });

                return [
                    'id_asignacion' => $particion->id,
                    'codigo_acceso' => $particion->codigo_acceso,
                    'estado' => $particion->estadoAsignacion?->nombre ?? 'Pendiente',
                    'fecha_asignacion' => $particion->fecha_asignacion,
                    'fecha_inicio' => $particion->fecha_inicio,
                    'nombre_origen' => $particion->envio->direccion?->nombreorigen ?? "—",
                    'nombre_destino' => $particion->envio->direccion?->nombredestino ?? "—",
                    'vehiculo' => [
                        'placa' => $particion->vehiculo?->placa,
                        'tipo' => $particion->vehiculo?->tipoVehiculo?->nombre,
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
                    'cargas' => $cargasTransformadas,
                ];
            });

            return response()->json($particiones);

        } catch (\Exception $e) {
            \Log::error('Error al obtener particiones en curso: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener particiones en curso'], 500);
        }
    }
}


