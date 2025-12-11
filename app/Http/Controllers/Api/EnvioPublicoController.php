<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Envio;
use App\Models\Direccion;
use App\Models\AsignacionMultiple;
use App\Models\RecogidaEntrega;
use App\Models\Carga;
use App\Models\AsignacionCarga;
use App\Models\CatalogoCategoria;
use App\Models\CatalogoProducto;
use App\Models\CatalogoTipoEmpaque;
use App\Models\EspecificacionTamanoConteo;
use App\Models\EspecificacionMedidasPeso;
use App\Models\EspecificacionFormaPedido;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class EnvioPublicoController extends Controller
{
    /**
     * Crear dirección de productor (sin autenticación)
     */
    public function crearDireccionProductor(Request $request)
    {
        $request->validate([
            'nombreorigen' => 'required|string|max:255',
            'nombredestino' => 'required|string|max:255',
            'origen_lat' => 'required|numeric',
            'origen_lng' => 'required|numeric',
            'destino_lat' => 'required|numeric',
            'destino_lng' => 'required|numeric',
            'rutageojson' => 'nullable|string',
        ]);

        try {
            $direccion = Direccion::create([
                'nombreorigen' => $request->nombreorigen,
                'nombredestino' => $request->nombredestino,
                'origen_lat' => $request->origen_lat,
                'origen_lng' => $request->origen_lng,
                'destino_lat' => $request->destino_lat,
                'destino_lng' => $request->destino_lng,
                'rutageojson' => $request->rutageojson,
                'id_usuario' => null, // Dirección pública sin usuario asociado
            ]);

            return response()->json([
                'mensaje' => 'Dirección creada exitosamente',
                'id_direccion' => $direccion->id
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear la dirección',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear envío de productor (sin autenticación)
     * Para productores externos que crean envíos
     */
    public function crearEnvioProductor(Request $request)
    {
        try {
            $request->validate([
                'nombre_remitente' => 'required|string|max:100',
                'telefono_remitente' => 'required|string|max:20',
                'email_remitente' => 'nullable|email|max:100',
                'id_direccion' => 'required|integer|exists:direccion,id',
                'particiones' => 'required|array|min:1',
                'particiones.*.id_tipo_transporte' => 'required|integer|exists:tipotransporte,id',
                'particiones.*.cargas' => 'required|array|min:1',
                'particiones.*.cargas.*.id_categoria' => 'nullable|integer|exists:catalogo_categorias,id',
                'particiones.*.cargas.*.id_producto' => 'nullable|integer|exists:catalogo_productos,id',
                'particiones.*.cargas.*.id_tipo_empaque' => 'nullable|integer|exists:catalogo_tipos_empaque,id',
                'particiones.*.cargas.*.tipo' => 'nullable|string|max:50',
                'particiones.*.cargas.*.variedad' => 'nullable|string|max:50',
                'particiones.*.cargas.*.cantidad' => 'required|numeric|min:0',
                'particiones.*.cargas.*.peso' => 'required|numeric|min:0',
                'particiones.*.cargas.*.empaquetado' => 'nullable|string|max:50',
                // Nuevas validaciones para especificaciones
                'particiones.*.cargas.*.conteo_por_empaque' => 'nullable|integer|min:1',
                'particiones.*.cargas.*.peso_promedio_unidad' => 'nullable|numeric|min:0',
                'particiones.*.cargas.*.capacidad_por_empaque' => 'nullable|integer|min:1',
                'particiones.*.cargas.*.largo_cm' => 'nullable|numeric|min:0',
                'particiones.*.cargas.*.ancho_cm' => 'nullable|numeric|min:0',
                'particiones.*.cargas.*.alto_cm' => 'nullable|numeric|min:0',
                'particiones.*.cargas.*.peso_neto_kg' => 'nullable|numeric|min:0',
                'particiones.*.cargas.*.tara_kg' => 'nullable|numeric|min:0',
                'particiones.*.cargas.*.peso_bruto_kg' => 'nullable|numeric|min:0',
                'particiones.*.cargas.*.forma_pedido' => 'nullable|string|in:empaques,cajas,bolsas,pallets',
                'particiones.*.cargas.*.cantidad_pedido' => 'nullable|integer|min:1',
                'particiones.*.cargas.*.empaques_calculados' => 'nullable|integer|min:0',
                'particiones.*.cargas.*.unidades_por_pallet' => 'nullable|integer|min:1',
                'particiones.*.cargas.*.numero_pallets' => 'nullable|integer|min:0',
                'particiones.*.recogidaEntrega.fecha_recogida' => 'required|date',
                'particiones.*.recogidaEntrega.hora_recogida' => 'required|string',
                'particiones.*.recogidaEntrega.hora_entrega' => 'required|string',
            ]);

            $idDireccion = $request->input('id_direccion');
            $particiones = $request->input('particiones');
            $nombreRemitente = $request->input('nombre_remitente');
            $telefonoRemitente = $request->input('telefono_remitente');
            $emailRemitente = $request->input('email_remitente');

            // Validar que la dirección exista
            $direccion = Direccion::find($idDireccion);
            if (!$direccion) {
                return response()->json(['error' => 'La dirección no existe'], Response::HTTP_BAD_REQUEST);
            }

            return DB::transaction(function () use ($idDireccion, $particiones, $nombreRemitente, $telefonoRemitente, $emailRemitente) {
                // Crear envío de productor sin id_usuario
                $envio = Envio::create([
                    'id_usuario' => null,
                    'id_direccion' => $idDireccion,
                    'nombre_remitente' => $nombreRemitente,
                    'telefono_remitente' => $telefonoRemitente,
                    'email_remitente' => $emailRemitente,
                    'es_publico' => true,
                ]);

                // Crear estado inicial en historial
                EstadoHelper::actualizarEstadoEnvio($envio->id, 'Pendiente');

                foreach ($particiones as $particion) {
                    $cargas = $particion['cargas'];
                    $recogidaEntrega = $particion['recogidaEntrega'];
                    $idTipoTransporte = $particion['id_tipo_transporte'];

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
                        $idCategoria = $carga['id_categoria'] ?? null;
                        $idProducto = $carga['id_producto'] ?? null;
                        $idTipoEmpaque = $carga['id_tipo_empaque'] ?? null;

                        // Si NO vienen IDs, intentar crearlos con firstOrCreate (Legacy support)
                        if (!$idCategoria && !empty($carga['tipo'])) {
                            $cat = CatalogoCategoria::firstOrCreate(
                                ['nombre' => $carga['tipo']],
                                ['descripcion' => 'Generado automáticamente por envío público']
                            );
                            $idCategoria = $cat->id;
                        }

                        if (!$idProducto && !empty($carga['variedad'])) {
                            $prod = CatalogoProducto::firstOrCreate(
                                [
                                    'nombre' => $carga['variedad'],
                                    'id_categoria' => $idCategoria
                                ],
                                ['descripcion' => 'Generado automáticamente por envío público']
                            );
                            $idProducto = $prod->id;
                        }

                        if (!$idTipoEmpaque && !empty($carga['empaquetado'])) {
                            $emp = CatalogoTipoEmpaque::firstOrCreate(
                                ['nombre' => $carga['empaquetado']],
                                [
                                    'descripcion' => 'Generado automáticamente por envío público',
                                    'largo' => 0,
                                    'ancho' => 0,
                                    'alto' => 0,
                                    'tara' => 0,
                                    'capacidad' => 0
                                ]
                            );
                            $idTipoEmpaque = $emp->id;
                        }

                        $c = Carga::create([
                            // 'id_catalogo_carga' => null, // Ya no se usa
                            'id_categoria' => $idCategoria,
                            'id_producto' => $idProducto,
                            'id_tipo_empaque' => $idTipoEmpaque,
                            'cantidad' => $carga['cantidad'],
                            'peso' => $carga['peso'],
                        ]);

                        AsignacionCarga::create([
                            'id_asignacion' => $asignacion->id,
                            'id_carga' => $c->id,
                        ]);

                        // Crear especificaciones en tablas separadas si existen datos
                        if (isset($carga['conteo_por_empaque']) || isset($carga['peso_promedio_unidad']) || isset($carga['capacidad_por_empaque'])) {
                            EspecificacionTamanoConteo::create([
                                'id_carga' => $c->id,
                                'conteo_por_empaque' => $carga['conteo_por_empaque'] ?? null,
                                'peso_promedio_unidad' => $carga['peso_promedio_unidad'] ?? null,
                                'capacidad_por_empaque' => $carga['capacidad_por_empaque'] ?? null,
                            ]);
                        }

                        if (
                            isset($carga['largo_cm']) || isset($carga['ancho_cm']) || isset($carga['alto_cm']) ||
                            isset($carga['peso_neto_kg']) || isset($carga['tara_kg']) || isset($carga['peso_bruto_kg'])
                        ) {
                            EspecificacionMedidasPeso::create([
                                'id_carga' => $c->id,
                                'largo_cm' => $carga['largo_cm'] ?? null,
                                'ancho_cm' => $carga['ancho_cm'] ?? null,
                                'alto_cm' => $carga['alto_cm'] ?? null,
                                'peso_neto_kg' => $carga['peso_neto_kg'] ?? null,
                                'tara_kg' => $carga['tara_kg'] ?? null,
                                'peso_bruto_kg' => $carga['peso_bruto_kg'] ?? null,
                            ]);
                        }

                        if (
                            isset($carga['forma_pedido']) || isset($carga['cantidad_pedido']) || isset($carga['empaques_calculados']) ||
                            isset($carga['unidades_por_pallet']) || isset($carga['numero_pallets'])
                        ) {
                            EspecificacionFormaPedido::create([
                                'id_carga' => $c->id,
                                'forma_pedido' => $carga['forma_pedido'] ?? null,
                                'cantidad_pedido' => $carga['cantidad_pedido'] ?? null,
                                'empaques_calculados' => $carga['empaques_calculados'] ?? null,
                                'unidades_por_pallet' => $carga['unidades_por_pallet'] ?? null,
                                'numero_pallets' => $carga['numero_pallets'] ?? null,
                            ]);
                        }
                    }
                }

                return response()->json([
                    'mensaje' => 'Envío de productor creado exitosamente',
                    'id_envio' => $envio->id,
                ], Response::HTTP_CREATED);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación',
                'detalles' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            \Log::error('Error creando envío público: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Error al crear el envío',
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => basename($e->getFile())
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Crear envío desde solicitud de materiales
     * Convierte estructura de material_request a estructura de envío OrgTrack
     */
    public function crearEnvioDesdeMateriaPrima(Request $request)
    {
        try {
            $request->validate([
                // Datos de la solicitud de materiales
                'order_id' => 'nullable|integer',
                'request_number' => 'nullable|string|max:50',
                'required_date' => 'nullable|date',
                'priority' => 'nullable|integer|min:1|max:10',
                'observations' => 'nullable|string',
                'materials' => 'required|array|min:1',
                'materials.*.material_id' => 'nullable|integer',
                'materials.*.material_name' => 'required|string|max:100',
                'materials.*.requested_quantity' => 'required|numeric|min:0',
                'materials.*.unit' => 'required|string|max:20',

                // Datos del productor (remitente)
                'nombre_remitente' => 'required|string|max:100',
                'telefono_remitente' => 'required|string|max:20',
                'email_remitente' => 'nullable|email|max:100',

                // Direcciones
                'id_direccion_origen' => 'required|integer|exists:direccion,id',
                'id_direccion_destino' => 'required|integer|exists:direccion,id',

                // Datos de transporte
                'id_tipo_transporte' => 'required|integer|exists:tipotransporte,id',
                'fecha_recogida' => 'required|date',
                'hora_recogida' => 'required|string',
                'hora_entrega' => 'required|string',
                'instrucciones_recogida' => 'nullable|string',
                'instrucciones_entrega' => 'nullable|string',
            ]);

            $materials = $request->input('materials');

            // Generar descripción de carga desde los materiales
            $descripcionCarga = collect($materials)->map(function ($material) {
                return "{$material['requested_quantity']} {$material['unit']} {$material['material_name']}";
            })->join(', ');

            return DB::transaction(function () use ($request, $descripcionCarga) {

                // Crear envío de productor
                $envio = Envio::create([
                    'id_usuario' => null,
                    'id_direccion' => $request->id_direccion_origen, // Usamos origen como dirección principal
                    'nombre_remitente' => $request->nombre_remitente,
                    'telefono_remitente' => $request->telefono_remitente,
                    'email_remitente' => $request->email_remitente,
                    'es_publico' => true,
                    'numero_solicitud' => $request->request_number,
                    'fecha_requerida' => $request->required_date,
                    'prioridad' => $request->priority ?? 1,
                    'observaciones_solicitud' => $request->observations,
                ]);

                // Crear estado inicial en historial
                EstadoHelper::actualizarEstadoEnvio($envio->id, 'Pendiente');

                // Crear recogida y entrega
                $recogidaEntrega = RecogidaEntrega::create([
                    'fecha_recogida' => $request->fecha_recogida,
                    'hora_recogida' => $request->hora_recogida,
                    'hora_entrega' => $request->hora_entrega,
                    'instrucciones_recogida' => $request->instrucciones_recogida,
                    'instrucciones_entrega' => $request->instrucciones_entrega,
                ]);

                // Obtener estado "Pendiente"
                $idEstadoPendiente = EstadoHelper::obtenerEstadoAsignacionPorNombre('Pendiente');

                // Generar código de acceso único
                $codigoAcceso = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

                // Crear asignación única (todos los materiales en una partición)
                $asignacion = AsignacionMultiple::create([
                    'id_envio' => $envio->id,
                    'id_tipo_transporte' => $request->id_tipo_transporte,
                    'id_estado_asignacion' => $idEstadoPendiente,
                    'id_recogida_entrega' => $recogidaEntrega->id,
                    'codigo_acceso' => $codigoAcceso,
                ]);

                // Crear cargas desde los materiales
                foreach ($request->materials as $material) {
                    // Buscar o crear catálogo de carga
                    // Buscar o crear Categoría (tipo)
                    $categoria = CatalogoCategoria::firstOrCreate(
                        ['nombre' => $material['material_name']], // Por ahora usaremos nombre material como categoría si es genérico, o ajustamos lógica
                        ['descripcion' => 'Materia Prima']
                    );

                    // Nota: Para materia prima "pura", quizás queremos un producto específico o una categoría "Materia Prima".
                    // Ajustando: Categoría="Materia Prima", Producto=Name
                    $catMateriaPrima = CatalogoCategoria::firstOrCreate(
                        ['nombre' => 'Materia Prima'],
                        ['descripcion' => 'Categoría general para insumos']
                    );

                    $prodMateria = CatalogoProducto::firstOrCreate(
                        [
                            'nombre' => $material['material_name'],
                            'id_categoria' => $catMateriaPrima->id
                        ],
                        ['descripcion' => "Material ID: {$material['material_id']}"]
                    );

                    $tipoEmpaque = CatalogoTipoEmpaque::firstOrCreate(
                        ['nombre' => $material['unit']],
                        ['largo' => 0, 'ancho' => 0, 'alto' => 0, 'tara' => 0, 'capacidad' => 0]
                    );

                    // Crear carga
                    $carga = Carga::create([
                        // 'id_catalogo_carga' => $catalogo->id, // Legacy
                        'id_categoria' => $catMateriaPrima->id,
                        'id_producto' => $prodMateria->id,
                        'id_tipo_empaque' => $tipoEmpaque->id,
                        'cantidad' => 1,
                        'peso' => $material['requested_quantity'],
                    ]);

                    // Vincular carga con asignación
                    AsignacionCarga::create([
                        'id_asignacion' => $asignacion->id,
                        'id_carga' => $carga->id,
                    ]);
                }

                return response()->json([
                    'mensaje' => 'Envío creado exitosamente desde solicitud de materiales',
                    'id_envio' => $envio->id,
                    'numero_solicitud' => $envio->numero_solicitud,
                    'descripcion_carga' => $descripcionCarga,
                    'codigo_acceso' => $codigoAcceso,
                ], Response::HTTP_CREATED);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación',
                'detalles' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            \Log::error('Error creando envío desde materiales: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Error al crear el envío',
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => basename($e->getFile())
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Listar TODOS los envíos públicos con sus estados (sin autenticación)
     * Similar a la vista de admin pero solo muestra envíos públicos
     */
    public function listarTodosEnviosPublicos()
    {
        try {
            $envios = Envio::with(['direccion'])
                ->where('es_publico', true)
                ->orderBy('fecha_creacion', 'desc')
                ->get()
                ->map(function ($envio) {
                    // Obtener último estado del historial
                    $ultimoEstado = \DB::table('historialestados')
                        ->join('estados_envio', 'historialestados.id_estado_envio', '=', 'estados_envio.id')
                        ->where('historialestados.id_envio', $envio->id)
                        ->orderBy('historialestados.fecha', 'desc')
                        ->first();

                    return [
                        'id' => $envio->id,
                        'nombre_remitente' => $envio->nombre_remitente,
                        'telefono_remitente' => $envio->telefono_remitente,
                        'email_remitente' => $envio->email_remitente,
                        'estado' => $ultimoEstado ? $ultimoEstado->nombre : 'Desconocido',
                        'fecha_creacion' => $envio->fecha_creacion ? $envio->fecha_creacion->format('Y-m-d H:i:s') : null,
                        'fecha_inicio' => $envio->fecha_inicio ? $envio->fecha_inicio->format('Y-m-d H:i:s') : null,
                        'fecha_entrega' => $envio->fecha_entrega ? $envio->fecha_entrega->format('Y-m-d H:i:s') : null,
                        'direccion_origen' => $envio->direccion ? $envio->direccion->nombreorigen : null,
                        'direccion_destino' => $envio->direccion ? $envio->direccion->nombredestino : null,
                        'numero_solicitud' => $envio->numero_solicitud,
                        'fecha_requerida' => $envio->fecha_requerida,
                        'prioridad' => $envio->prioridad,
                        'observaciones_solicitud' => $envio->observaciones_solicitud,
                        'cancelado' => $envio->cancelado,
                    ];
                });

            return response()->json($envios);

        } catch (\Exception $e) {
            \Log::error('Error listando envíos públicos: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener los envíos',
                'mensaje' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener envío público completo con particiones (seguimiento)
     * Similar al endpoint de admin pero sin autenticación y solo para envíos públicos
     */
    public function obtenerEnvioPublicoPorId($id)
    {
        try {
            $envio = Envio::with([
                'asignaciones.transportista.usuario.persona:id,nombre,apellido,ci,telefono',
                'asignaciones.transportista:id,id_usuario',
                'asignaciones.vehiculo.tipoVehiculo:id,nombre',
                'asignaciones.vehiculo:id,placa,capacidad',
                'asignaciones.estadoAsignacion:id,nombre',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas.categoria:id,nombre',
                'asignaciones.cargas.producto:id,nombre',
                'asignaciones.cargas.tipoEmpaque:id,nombre',
                'direccion:id,nombreorigen,nombredestino,origen_lng,origen_lat,destino_lng,destino_lat,rutageojson'
            ])
                ->where('es_publico', true)
                ->find($id);

            if (!$envio) {
                return response()->json(['error' => 'Envío no encontrado'], 404);
            }

            // Obtener último estado del historial
            $ultimoEstado = \DB::table('historialestados')
                ->join('estados_envio', 'historialestados.id_estado_envio', '=', 'estados_envio.id')
                ->where('historialestados.id_envio', $envio->id)
                ->orderBy('historialestados.fecha', 'desc')
                ->first();

            // Datos básicos del envío
            $response = [
                'id' => $envio->id,
                'nombre_remitente' => $envio->nombre_remitente,
                'telefono_remitente' => $envio->telefono_remitente,
                'email_remitente' => $envio->email_remitente,
                'estado' => $ultimoEstado ? $ultimoEstado->nombre : 'Desconocido',
                'fecha_creacion' => $envio->fecha_creacion ? $envio->fecha_creacion->format('Y-m-d H:i:s') : null,
                'fecha_inicio' => $envio->fecha_inicio ? $envio->fecha_inicio->format('Y-m-d H:i:s') : null,
                'fecha_entrega' => $envio->fecha_entrega ? $envio->fecha_entrega->format('Y-m-d H:i:s') : null,
                'numero_solicitud' => $envio->numero_solicitud,
                'fecha_requerida' => $envio->fecha_requerida,
                'prioridad' => $envio->prioridad,
                'observaciones_solicitud' => $envio->observaciones_solicitud,
                'cancelado' => $envio->cancelado,

                // Coordenadas
                'coordenadas_origen' => [
                    'lng' => $envio->direccion?->origen_lng,
                    'lat' => $envio->direccion?->origen_lat,
                ],
                'coordenadas_destino' => [
                    'lng' => $envio->direccion?->destino_lng,
                    'lat' => $envio->direccion?->destino_lat,
                ],
                'nombre_origen' => $envio->direccion?->nombreorigen,
                'nombre_destino' => $envio->direccion?->nombredestino,
                'rutaGeoJSON' => $envio->direccion?->rutageojson,
            ];

            // Transformar asignaciones a particiones
            $response['particiones'] = $envio->asignaciones->map(function ($asignacion) {
                $cargasTransformadas = $asignacion->cargas->map(function ($carga) {
                    return [
                        'id' => $carga->id,
                        'tipo' => $carga->categoria?->nombre ?? '—',
                        'variedad' => $carga->producto?->nombre ?? '—',
                        'empaquetado' => $carga->tipoEmpaque?->nombre ?? '—',
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
                    'transportista' => $asignacion->transportista ? [
                        'nombre' => $asignacion->transportista->usuario?->persona?->nombre,
                        'apellido' => $asignacion->transportista->usuario?->persona?->apellido,
                        'telefono' => $asignacion->transportista->usuario?->persona?->telefono,
                        'ci' => $asignacion->transportista->usuario?->persona?->ci,
                    ] : null,
                    'vehiculo' => $asignacion->vehiculo ? [
                        'placa' => $asignacion->vehiculo->placa,
                        'tipo' => $asignacion->vehiculo->tipoVehiculo?->nombre,
                    ] : null,
                    'tipoTransporte' => [
                        'nombre' => $asignacion->tipoTransporte?->nombre,
                        'descripcion' => $asignacion->tipoTransporte?->descripcion,
                    ],
                    'recogidaEntrega' => $asignacion->recogidaEntrega ? [
                        'fecha_recogida' => $asignacion->recogidaEntrega->fecha_recogida,
                        'hora_recogida' => $asignacion->recogidaEntrega->hora_recogida,
                        'hora_entrega' => $asignacion->recogidaEntrega->hora_entrega,
                        'instrucciones_recogida' => $asignacion->recogidaEntrega->instrucciones_recogida,
                        'instrucciones_entrega' => $asignacion->recogidaEntrega->instrucciones_entrega,
                    ] : null,
                    'cargas' => $cargasTransformadas,
                ];
            });

            // Calcular estado resumen
            $total = $response['particiones']->count();
            $activos = $response['particiones']->filter(function ($p) {
                return $p['estado'] === 'En curso';
            })->count();
            $response['estado_resumen'] = "En curso ({$activos} de {$total} camiones activos)";

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error al obtener envío público por ID: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener el envío',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar todos los envíos de productores ENTREGADOS (sin autenticación)
     * Muestra solo envíos creados por productores externos que ya fueron entregados
     */
    public function listarEnviosProductores()
    {
        try {
            // Obtener IDs de estados "Entregado" y "Parcialmente entregado"
            $estadosValidos = \DB::table('estados_envio')
                ->whereIn('nombre', ['Entregado', 'Parcialmente entregado'])
                ->pluck('id')
                ->toArray();

            if (empty($estadosValidos)) {
                return response()->json([]);
            }

            // Filtrar directamente en la query solo envíos públicos con último estado = Entregado o Parcialmente entregado
            $enviosEntregados = Envio::select('envios.*')
                ->join('historialestados', function ($join) {
                    $join->on('envios.id', '=', 'historialestados.id_envio')
                        ->whereRaw('historialestados.fecha = (
                             SELECT MAX(fecha) 
                             FROM historialestados he2 
                             WHERE he2.id_envio = envios.id
                         )');
                })
                ->where('envios.es_publico', true)
                ->whereIn('historialestados.id_estado_envio', $estadosValidos)
                ->with([
                    'direccion:id,nombreorigen,nombredestino',
                    'asignaciones' => function ($query) {
                        $query->orderBy('fecha_fin', 'desc')->limit(1);
                    },
                    'historialEstados' => function ($query) {
                        $query->orderBy('fecha', 'desc')->limit(1)->with('estadoEnvio:id,nombre');
                    }
                ])
                ->orderBy('envios.fecha_creacion', 'desc')
                ->get();

            // Transformar respuesta
            $resultado = $enviosEntregados->map(function ($envio) {
                // Usar fecha_fin de la última asignación si fecha_entrega del envío está vacía
                $fechaEntrega = $envio->fecha_entrega;
                if (!$fechaEntrega && $envio->asignaciones->isNotEmpty()) {
                    $fechaEntrega = $envio->asignaciones->first()->fecha_fin;
                }

                // Obtener el estado actual del envío desde el historial
                $estadoActual = $envio->historialEstados->first()?->estadoEnvio?->nombre ?? 'Entregado';

                return [
                    'id' => $envio->id,
                    'nombre_remitente' => $envio->nombre_remitente,
                    'telefono_remitente' => $envio->telefono_remitente,
                    'email_remitente' => $envio->email_remitente,
                    'estado' => $estadoActual,
                    'fecha_creacion' => $envio->fecha_creacion,
                    'fecha_entrega' => $fechaEntrega,
                    'nombre_origen' => $envio->direccion?->nombreorigen ?? "—",
                    'nombre_destino' => $envio->direccion?->nombredestino ?? "—",
                ];
            });

            return response()->json($resultado);

        } catch (\Exception $e) {
            \Log::error('Error en listarEnviosProductores: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'Error al obtener envíos de productores',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener documento PDF de envío de productor (sin autenticación)
     * Duplica la funcionalidad de generarDocumentoEnvio pero SIN necesitar usuario
     */
    public function obtenerDocumentoProductor(Request $request, $id_envio)
    {
        try {
            // Cargar envío con todas las relaciones necesarias
            $envio = Envio::with([
                'asignaciones.transportista.usuario.persona:id,nombre,apellido,ci,telefono',
                'asignaciones.vehiculo:id,placa,id_tipo_vehiculo',
                'asignaciones.vehiculo.tipoVehiculo:id,nombre',
                'asignaciones.estadoAsignacion:id,nombre',
                'asignaciones.tipoTransporte:id,nombre,descripcion',
                'asignaciones.recogidaEntrega',
                'asignaciones.recogidaEntrega',
                'asignaciones.cargas.categoria:id,nombre',
                'asignaciones.cargas.producto:id,nombre',
                'asignaciones.cargas.tipoEmpaque:id,nombre',
                'asignaciones.cargas.catalogoCarga:id,tipo,variedad,empaque', // Legacy split
                'asignaciones.checklistCondicion.detalles.condicion:id,titulo',
                'asignaciones.checklistIncidente.detalles.tipoIncidente:id,titulo',
                'asignaciones.firmaEnvio',
                'asignaciones.firmaTransportista',
                'direccion:id,nombreorigen,nombredestino',
                'historialEstados.estadoEnvio:id,nombre'
            ])
                ->where('id', $id_envio)
                ->where('es_publico', true)
                ->first();

            if (!$envio) {
                return response()->json([
                    'error' => 'Envío no encontrado o no es de productor'
                ], Response::HTTP_NOT_FOUND);
            }

            // Validar si el envío está ENTREGADO o PARCIALMENTE ENTREGADO
            $estadoEnvio = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
            if (!in_array($estadoEnvio, ['Entregado', 'Parcialmente entregado'])) {
                return response()->json([
                    'error' => 'El documento solo está disponible para envíos entregados o parcialmente entregados.'
                ], 400);
            }

            // Transformar asignaciones a particiones (MISMO formato que documentos de clientes)
            $particiones = $envio->asignaciones->map(function ($asignacion) {
                $cargasTransformadas = $asignacion->cargas->map(function ($carga) {
                    return [
                        'id' => $carga->id,
                        'tipo' => $carga->categoria?->nombre ?? $carga->catalogoCarga?->tipo,
                        'variedad' => $carga->producto?->nombre ?? $carga->catalogoCarga?->variedad,
                        'empaquetado' => $carga->tipoEmpaque?->nombre ?? $carga->catalogoCarga?->empaque,
                        'cantidad' => $carga->cantidad,
                        'peso' => $carga->peso,
                    ];
                });

                // Checklists de condiciones
                $checklistCondiciones = [];
                if ($asignacion->checklistCondicion && $asignacion->checklistCondicion->detalles) {
                    $checklistCondiciones = $asignacion->checklistCondicion->detalles->map(function ($det) {
                        return [
                            'id' => $det->id,
                            'condicion' => [
                                'id' => $det->condicion?->id,
                                'titulo' => $det->condicion?->titulo,
                            ],
                            'cumple' => $det->cumple,
                            'observacion' => $det->observacion,
                        ];
                    })->toArray();
                }

                // Checklists de incidentes
                $checklistIncidentes = [];
                if ($asignacion->checklistIncidente && $asignacion->checklistIncidente->detalles) {
                    $checklistIncidentes = $asignacion->checklistIncidente->detalles->map(function ($det) {
                        return [
                            'id' => $det->id,
                            'tipo_incidente' => [
                                'id' => $det->tipoIncidente?->id,
                                'titulo' => $det->tipoIncidente?->titulo,
                            ],
                            'ocurrio' => $det->ocurrio,
                            'descripcion' => $det->descripcion,
                        ];
                    })->toArray();
                }

                return [
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
                    'checklistCondiciones' => $checklistCondiciones,
                    'observaciones_condiciones' => $asignacion->checklistCondicion?->observaciones,
                    'checklistIncidentes' => $checklistIncidentes,
                    'observaciones_incidentes' => $asignacion->checklistIncidente?->observaciones,
                    'firmaTransportista' => $asignacion->firmaTransportista?->imagenfirma,
                    'firma' => $asignacion->firmaEnvio?->imagenfirma,
                ];
            });

            // Retornar el mismo formato JSON que el endpoint de clientes
            return response()->json([
                'id_envio' => $envio->id,
                'nombre_cliente' => $envio->nombre_remitente ?? 'Productor',
                'estado' => $estadoEnvio,
                'fecha_creacion' => $envio->fecha_creacion,
                'fecha_inicio' => $envio->fecha_inicio,
                'fecha_entrega' => $envio->fecha_entrega,
                'nombre_origen' => $envio->direccion?->nombreorigen ?? '—',
                'nombre_destino' => $envio->direccion?->nombredestino ?? '—',
                'particiones' => $particiones,
            ]);

        } catch (\Exception $e) {
            \Log::error("Error generando documento productor ID {$id_envio}: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'Error al generar documento',
                'mensaje' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
