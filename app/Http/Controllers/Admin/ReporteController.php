<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    // ============================================================================
    // REPORTE 1: ANÁLISIS DE ENVÍOS POR ESTADO
    // Propósito: Permite visualizar la distribución de envíos por estado para 
    // identificar cuellos de botella, envíos pendientes y eficiencia operativa.
    // ============================================================================
    public function enviosPorEstado(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        // Consulta compleja con subquery para obtener el último estado de cada envío
        $data = DB::table('envios')
            ->leftJoin('historialestados', function($join) {
                $join->on('envios.id', '=', 'historialestados.id_envio')
                     ->whereRaw('historialestados.id = (SELECT MAX(h2.id) FROM historialestados h2 WHERE h2.id_envio = envios.id)');
            })
            ->leftJoin('estados_envio', 'historialestados.id_estado_envio', '=', 'estados_envio.id')
            ->whereBetween('envios.fecha_creacion', [$fechaInicio, $fechaFin])
            ->select(
                DB::raw("COALESCE(estados_envio.nombre, 'Sin estado') as estado"),
                DB::raw('count(envios.id) as total'),
                DB::raw('count(CASE WHEN envios.cancelado = true THEN 1 END) as cancelados')
            )
            ->groupBy('estados_envio.nombre')
            ->orderByDesc('total')
            ->get();

        // Métricas adicionales para toma de decisiones
        $totalEnvios = $data->sum('total');
        $totalCancelados = $data->sum('cancelados');
        $tasaCancelacion = $totalEnvios > 0 ? round(($totalCancelados / $totalEnvios) * 100, 2) : 0;

        // Comparativa con período anterior
        $fechaInicioAnterior = now()->parse($fechaInicio)->subMonth()->format('Y-m-d');
        $fechaFinAnterior = now()->parse($fechaFin)->subMonth()->format('Y-m-d');
        
        $totalAnterior = DB::table('envios')
            ->whereBetween('fecha_creacion', [$fechaInicioAnterior, $fechaFinAnterior])
            ->count();

        $variacion = $totalAnterior > 0 ? round((($totalEnvios - $totalAnterior) / $totalAnterior) * 100, 1) : 0;

        return view('admin.reportes.envios_estado', compact(
            'data', 'fechaInicio', 'fechaFin', 
            'totalEnvios', 'totalCancelados', 'tasaCancelacion',
            'totalAnterior', 'variacion'
        ));
    }

    // ============================================================================
    // REPORTE 2: RENDIMIENTO DE TRANSPORTISTAS
    // Propósito: Ranking y métricas de desempeño de transportistas para evaluar
    // productividad, identificar empleados destacados y necesidades de capacitación.
    // ============================================================================
    public function enviosPorTransportista(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        // Consulta compleja con múltiples joins y agregaciones
        $data = DB::table('asignacionmultiple')
            ->join('transportistas', 'asignacionmultiple.id_transportista', '=', 'transportistas.id')
            ->join('usuarios', 'transportistas.id_usuario', '=', 'usuarios.id')
            ->join('persona', 'usuarios.id_persona', '=', 'persona.id')
            ->join('envios', 'asignacionmultiple.id_envio', '=', 'envios.id')
            ->join('estados_asignacion_multiple', 'asignacionmultiple.id_estado_asignacion', '=', 'estados_asignacion_multiple.id')
            ->whereBetween('envios.fecha_creacion', [$fechaInicio, $fechaFin])
            ->whereNotNull('asignacionmultiple.id_transportista')
            ->select(
                'transportistas.id as transportista_id',
                'persona.nombre',
                'persona.apellido',
                'persona.ci',
                DB::raw('count(asignacionmultiple.id) as total_asignaciones'),
                DB::raw('count(DISTINCT asignacionmultiple.id_envio) as envios_unicos'),
                DB::raw("count(CASE WHEN estados_asignacion_multiple.nombre = 'Finalizado' THEN 1 END) as completados"),
                DB::raw("count(CASE WHEN estados_asignacion_multiple.nombre = 'Cancelado' THEN 1 END) as cancelados")
            )
            ->groupBy('transportistas.id', 'persona.nombre', 'persona.apellido', 'persona.ci')
            ->orderByDesc('total_asignaciones')
            ->get();

        // Cálculo de eficiencia por transportista
        $data = $data->map(function($item) {
            $item->eficiencia = $item->total_asignaciones > 0 
                ? round(($item->completados / $item->total_asignaciones) * 100, 1) 
                : 0;
            return $item;
        });

        // KPIs globales
        $totalTransportistas = $data->count();
        $totalAsignaciones = $data->sum('total_asignaciones');
        $promedioAsignaciones = $totalTransportistas > 0 ? round($totalAsignaciones / $totalTransportistas, 1) : 0;
        $eficienciaGlobal = $totalAsignaciones > 0 
            ? round(($data->sum('completados') / $totalAsignaciones) * 100, 1) 
            : 0;

        return view('admin.reportes.envios_transportista', compact(
            'data', 'fechaInicio', 'fechaFin',
            'totalTransportistas', 'totalAsignaciones', 'promedioAsignaciones', 'eficienciaGlobal'
        ));
    }

    // ============================================================================
    // REPORTE 3: CATÁLOGO DE PRODUCTOS POR CATEGORÍA
    // Propósito: Inventario completo de productos para gestión de catálogo,
    // identificación de productos sin categoría y análisis de oferta.
    // ============================================================================
    public function productosMasEnviados(Request $request)
    {
        $categoriaFiltro = $request->input('categoria');

        $query = DB::table('catalogo_productos')
            ->leftJoin('catalogo_categorias', 'catalogo_productos.id_categoria', '=', 'catalogo_categorias.id')
            ->select(
                'catalogo_productos.id',
                'catalogo_productos.nombre as producto',
                'catalogo_productos.descripcion',
                'catalogo_productos.peso_promedio',
                'catalogo_categorias.id as categoria_id',
                'catalogo_categorias.nombre as categoria',
                'catalogo_categorias.descripcion as categoria_desc'
            );

        if ($categoriaFiltro) {
            $query->where('catalogo_categorias.id', $categoriaFiltro);
        }

        $data = $query->orderBy('catalogo_categorias.nombre')
                      ->orderBy('catalogo_productos.nombre')
                      ->get();

        $categorias = DB::table('catalogo_categorias')->orderBy('nombre')->get();
        $dataGrouped = $data->groupBy('categoria');

        // Métricas
        $totalProductos = $data->count();
        $totalCategorias = $categorias->count();
        $sinCategoria = $data->where('categoria', null)->count();

        return view('admin.reportes.productos_enviados', compact(
            'data', 'dataGrouped', 'categorias', 'categoriaFiltro',
            'totalProductos', 'totalCategorias', 'sinCategoria'
        ));
    }

    // ============================================================================
    // REPORTE 4: DISTRIBUCIÓN DE USUARIOS POR ROL
    // Propósito: Control de accesos y seguridad, distribución de roles para
    // gestión de permisos y planificación de recursos humanos.
    // ============================================================================
    public function usuariosPorRol(Request $request)
    {
        $rolFiltro = $request->input('rol');

        // Distribución por rol con métricas
        $data = DB::table('usuarios')
            ->join('roles_usuario', 'usuarios.id_rol', '=', 'roles_usuario.id')
            ->select(
                'roles_usuario.id as rol_id',
                'roles_usuario.nombre as rol',
                DB::raw('count(usuarios.id) as total'),
                DB::raw('count(CASE WHEN usuarios.fecha_registro > NOW() - INTERVAL \'30 days\' THEN 1 END) as nuevos_30d')
            )
            ->groupBy('roles_usuario.id', 'roles_usuario.nombre')
            ->orderByDesc('total')
            ->get();

        $total = $data->sum('total');

        // Detalle de usuarios con filtro
        $queryUsuarios = DB::table('usuarios')
            ->join('roles_usuario', 'usuarios.id_rol', '=', 'roles_usuario.id')
            ->leftJoin('persona', 'usuarios.id_persona', '=', 'persona.id')
            ->select(
                'usuarios.id',
                'usuarios.correo',
                'persona.nombre',
                'persona.apellido',
                'persona.ci',
                'roles_usuario.nombre as rol',
                'usuarios.fecha_registro'
            )
            ->orderBy('usuarios.fecha_registro', 'desc');

        if ($rolFiltro) {
            $queryUsuarios->where('roles_usuario.id', $rolFiltro);
        }

        $usuarios = $queryUsuarios->limit(100)->get();

        $roles = DB::table('roles_usuario')->orderBy('nombre')->get();

        return view('admin.reportes.usuarios_rol', compact(
            'data', 'total', 'usuarios', 'roles', 'rolFiltro'
        ));
    }

    // ============================================================================
    // REPORTE 5: FLOTA DE VEHÍCULOS POR TIPO Y ESTADO
    // Propósito: Gestión de flota vehicular, control de disponibilidad,
    // planificación de mantenimiento y capacidad logística.
    // ============================================================================
    public function vehiculosPorTipo(Request $request)
    {
        $estadoFiltro = $request->input('estado');

        // Distribución por tipo
        $dataTipo = DB::table('vehiculos')
            ->join('tipos_vehiculo', 'vehiculos.id_tipo_vehiculo', '=', 'tipos_vehiculo.id')
            ->select('tipos_vehiculo.nombre as tipo', DB::raw('count(vehiculos.id) as total'))
            ->groupBy('tipos_vehiculo.nombre')
            ->orderByDesc('total')
            ->get();

        // Distribución por estado
        $dataEstado = DB::table('vehiculos')
            ->join('estados_vehiculo', 'vehiculos.id_estado_vehiculo', '=', 'estados_vehiculo.id')
            ->select('estados_vehiculo.nombre as estado', DB::raw('count(vehiculos.id) as total'))
            ->groupBy('estados_vehiculo.nombre')
            ->orderByDesc('total')
            ->get();

        $total = $dataTipo->sum('total');

        // Detalle de vehículos
        $queryVehiculos = DB::table('vehiculos')
            ->join('tipos_vehiculo', 'vehiculos.id_tipo_vehiculo', '=', 'tipos_vehiculo.id')
            ->join('estados_vehiculo', 'vehiculos.id_estado_vehiculo', '=', 'estados_vehiculo.id')
            ->select(
                'vehiculos.id',
                'vehiculos.placa',
                'vehiculos.capacidad',
                'vehiculos.fecha_registro',
                'tipos_vehiculo.nombre as tipo',
                'estados_vehiculo.nombre as estado'
            )
            ->orderBy('tipos_vehiculo.nombre');

        if ($estadoFiltro) {
            $queryVehiculos->where('estados_vehiculo.id', $estadoFiltro);
        }

        $vehiculos = $queryVehiculos->get();
        $estados = DB::table('estados_vehiculo')->orderBy('nombre')->get();

        // Capacidad total
        $capacidadTotal = $vehiculos->sum('capacidad');
        $disponibles = $dataEstado->where('estado', 'Disponible')->first()->total ?? 0;

        return view('admin.reportes.vehiculos_tipo', compact(
            'dataTipo', 'dataEstado', 'total', 'vehiculos', 'estados', 'estadoFiltro',
            'capacidadTotal', 'disponibles'
        ));
    }

    // ============================================================================
    // REPORTE 6: TENDENCIA MENSUAL DE ENVÍOS (ANÁLISIS TEMPORAL)
    // Propósito: Análisis de estacionalidad, proyección de demanda,
    // identificación de picos operativos para planificación de recursos.
    // ============================================================================
    public function enviosPorMes(Request $request)
    {
        $year = $request->input('year', now()->year);

        // Envíos por mes con métricas detalladas
        $data = DB::table('envios')
            ->whereRaw('EXTRACT(YEAR FROM fecha_creacion) = ?', [$year])
            ->select(
                DB::raw('EXTRACT(MONTH FROM fecha_creacion)::integer as mes'),
                DB::raw('count(*) as total'),
                DB::raw('count(CASE WHEN cancelado = true THEN 1 END) as cancelados'),
                DB::raw('count(CASE WHEN cancelado = false OR cancelado IS NULL THEN 1 END) as efectivos')
            )
            ->groupBy(DB::raw('EXTRACT(MONTH FROM fecha_creacion)'))
            ->orderBy('mes')
            ->get();

        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        $totalAnual = $data->sum('total');
        $promedioMensual = $data->count() > 0 ? round($totalAnual / $data->count(), 0) : 0;
        $mesMayor = $data->sortByDesc('total')->first();
        $mesMenor = $data->sortBy('total')->first();

        // Comparativa con año anterior
        $yearAnterior = $year - 1;
        $totalAnterior = DB::table('envios')
            ->whereRaw('EXTRACT(YEAR FROM fecha_creacion) = ?', [$yearAnterior])
            ->count();
        
        $crecimiento = $totalAnterior > 0 ? round((($totalAnual - $totalAnterior) / $totalAnterior) * 100, 1) : 0;

        // Años disponibles para filtro
        $years = DB::table('envios')
            ->selectRaw('EXTRACT(YEAR FROM fecha_creacion)::integer as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('admin.reportes.envios_mes', compact(
            'data', 'year', 'meses', 'years',
            'totalAnual', 'promedioMensual', 'mesMayor', 'mesMenor',
            'totalAnterior', 'crecimiento', 'yearAnterior'
        ));
    }

    // ============================================================================
    // EXPORTACIÓN CSV (Alternativa sin librerías externas)
    // ============================================================================
    public function exportCSV(Request $request, $reporte)
    {
        $data = $this->getDataForExport($reporte, $request);
        
        $filename = "reporte_{$reporte}_" . now()->format('Y-m-d_His') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data, $reporte) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8
            
            // Headers según reporte
            switch($reporte) {
                case 'envios_estado':
                    fputcsv($file, ['Estado', 'Total', 'Cancelados']);
                    foreach($data as $row) {
                        fputcsv($file, [$row->estado, $row->total, $row->cancelados ?? 0]);
                    }
                    break;
                case 'transportistas':
                    fputcsv($file, ['Transportista', 'CI', 'Total Asignaciones', 'Completados', 'Eficiencia %']);
                    foreach($data as $row) {
                        fputcsv($file, [
                            $row->nombre . ' ' . $row->apellido,
                            $row->ci,
                            $row->total_asignaciones,
                            $row->completados,
                            $row->eficiencia
                        ]);
                    }
                    break;
                // Agregar más casos según necesidad
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function getDataForExport($reporte, $request)
    {
        switch($reporte) {
            case 'envios_estado':
                $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
                $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
                return DB::table('envios')
                    ->leftJoin('historialestados', function($join) {
                        $join->on('envios.id', '=', 'historialestados.id_envio')
                             ->whereRaw('historialestados.id = (SELECT MAX(h2.id) FROM historialestados h2 WHERE h2.id_envio = envios.id)');
                    })
                    ->leftJoin('estados_envio', 'historialestados.id_estado_envio', '=', 'estados_envio.id')
                    ->whereBetween('envios.fecha_creacion', [$fechaInicio, $fechaFin])
                    ->select(
                        DB::raw("COALESCE(estados_envio.nombre, 'Sin estado') as estado"),
                        DB::raw('count(envios.id) as total'),
                        DB::raw('count(CASE WHEN envios.cancelado = true THEN 1 END) as cancelados')
                    )
                    ->groupBy('estados_envio.nombre')
                    ->get();
            default:
                return collect([]);
        }
    }

    // ============================================================================
    // REPORTE 7: ENVÍO DETALLADO CON MAPA
    // Propósito: Visualización completa de un envío con ruta en mapa, información
    // del cliente, transportista, carga, y estado actual.
    // ============================================================================
    public function envioDetallado(Request $request)
    {
        $envioId = $request->input('envio_id');
        
        // Obtener lista de envíos para selector
        $envios = DB::table('envios')
            ->leftJoin('direccion', 'envios.id_direccion', '=', 'direccion.id')
            ->select('envios.id', 'direccion.nombreorigen', 'direccion.nombredestino', 'envios.fecha_creacion')
            ->orderByDesc('envios.fecha_creacion')
            ->limit(100)
            ->get();

        $envio = null;
        $asignaciones = collect([]);
        $historial = collect([]);

        if ($envioId) {
            // Datos del envío
            $envio = DB::table('envios')
                ->leftJoin('direccion', 'envios.id_direccion', '=', 'direccion.id')
                ->leftJoin('usuarios', 'envios.id_usuario', '=', 'usuarios.id')
                ->leftJoin('persona', 'usuarios.id_persona', '=', 'persona.id')
                ->where('envios.id', $envioId)
                ->select(
                    'envios.*',
                    'direccion.nombreorigen', 'direccion.origen_lat', 'direccion.origen_lng',
                    'direccion.nombredestino', 'direccion.destino_lat', 'direccion.destino_lng',
                    'direccion.rutageojson',
                    'persona.nombre as cliente_nombre', 'persona.apellido as cliente_apellido',
                    'persona.ci as cliente_ci'
                )
                ->first();

            // Asignaciones del envío
            if ($envio) {
                $asignaciones = DB::table('asignacionmultiple')
                    ->leftJoin('transportistas', 'asignacionmultiple.id_transportista', '=', 'transportistas.id')
                    ->leftJoin('usuarios', 'transportistas.id_usuario', '=', 'usuarios.id')
                    ->leftJoin('persona', 'usuarios.id_persona', '=', 'persona.id')
                    ->leftJoin('vehiculos', 'asignacionmultiple.id_vehiculo', '=', 'vehiculos.id')
                    ->leftJoin('estados_asignacion_multiple', 'asignacionmultiple.id_estado_asignacion', '=', 'estados_asignacion_multiple.id')
                    ->leftJoin('tipotransporte', 'asignacionmultiple.id_tipo_transporte', '=', 'tipotransporte.id')
                    ->where('asignacionmultiple.id_envio', $envioId)
                    ->select(
                        'asignacionmultiple.*',
                        'persona.nombre as transportista_nombre', 'persona.apellido as transportista_apellido',
                        'vehiculos.placa',
                        'estados_asignacion_multiple.nombre as estado',
                        'tipotransporte.nombre as tipo_transporte'
                    )
                    ->get();

                // Historial de estados
                $historial = DB::table('historialestados')
                    ->join('estados_envio', 'historialestados.id_estado_envio', '=', 'estados_envio.id')
                    ->where('historialestados.id_envio', $envioId)
                    ->select('estados_envio.nombre as estado', 'historialestados.fecha')
                    ->orderBy('historialestados.fecha')
                    ->get();
            }
        }

        return view('admin.reportes.envio_detallado', compact('envios', 'envio', 'envioId', 'asignaciones', 'historial'));
    }

    // ============================================================================
    // REPORTE 8: CATÁLOGO DE TIPOS DE EMPAQUE
    // Propósito: Gestión del inventario de empaques disponibles con dimensiones,
    // capacidad y especificaciones técnicas.
    // ============================================================================
    public function tiposEmpaque(Request $request)
    {
        $data = DB::table('catalogo_tipos_empaque')
            ->select(
                'id', 'nombre', 'descripcion',
                'largo', 'ancho', 'alto', 'tara',
                'capacidad', 'unidades_por_pallet'
            )
            ->orderBy('nombre')
            ->get();

        // Calcular volumen
        $data = $data->map(function($item) {
            $item->volumen = ($item->largo && $item->ancho && $item->alto) 
                ? round(($item->largo * $item->ancho * $item->alto) / 1000000, 4) . ' m³'
                : '-';
            return $item;
        });

        $total = $data->count();
        $conDimensiones = $data->filter(fn($d) => $d->largo && $d->ancho && $d->alto)->count();

        return view('admin.reportes.tipos_empaque', compact('data', 'total', 'conDimensiones'));
    }

    // ============================================================================
    // REPORTE 9: CATÁLOGO DE TAMAÑO-CONTEO
    // Propósito: Gestión de clasificaciones de tamaño con conteo por empaque
    // para estimación de peso y planificación de carga.
    // ============================================================================
    public function tamanoConteo(Request $request)
    {
        $data = DB::table('catalogo_tamano_conteo')
            ->leftJoin('catalogo_productos', 'catalogo_tamano_conteo.id_producto', '=', 'catalogo_productos.id')
            ->select(
                'catalogo_tamano_conteo.*',
                'catalogo_productos.nombre as producto'
            )
            ->orderBy('catalogo_tamano_conteo.nombre')
            ->get();

        $total = $data->count();
        $activos = $data->where('activo', true)->count();

        return view('admin.reportes.tamano_conteo', compact('data', 'total', 'activos'));
    }
}

