<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Obtener estadísticas para las gráficas del dashboard
     */
    public function getStats()
    {
        try {
            return response()->json([
                'envios_por_mes' => $this->getEnviosPorMes(),
                'transportistas_disponibilidad' => $this->getTransportistasDisponibilidad(),
                'envios_por_estado' => $this->getEnviosPorEstado(),
                'productos_por_categoria' => $this->getProductosPorCategoria(),
                'variacion_mensual' => $this->getVariacionMensual(),
                'tasa_entrega' => $this->getTasaEntrega(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en DashboardController: ' . $e->getMessage());
            return response()->json([
                'envios_por_mes' => [],
                'transportistas_disponibilidad' => [],
                'envios_por_estado' => [],
                'productos_por_categoria' => [],
                'variacion_mensual' => 0,
                'tasa_entrega' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getEnviosPorMes()
    {
        try {
            $meses = [];
            for ($i = 5; $i >= 0; $i--) {
                $fecha = Carbon::now()->subMonths($i);
                $inicioMes = $fecha->copy()->startOfMonth()->format('Y-m-d');
                $finMes = $fecha->copy()->endOfMonth()->format('Y-m-d');

                $count = DB::table('envios')
                    ->whereDate('fecha_creacion', '>=', $inicioMes)
                    ->whereDate('fecha_creacion', '<=', $finMes)
                    ->count();

                $meses[] = [
                    'mes' => $fecha->format('M Y'),
                    'mes_corto' => $fecha->format('m/Y'),
                    'total' => $count,
                ];
            }
            return $meses;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getTransportistasDisponibilidad()
    {
        try {
            // Simplificado: contar todos los transportistas y los que tienen asignaciones sin fecha_fin
            $total = DB::table('transportistas')->count();
            
            $ocupados = DB::table('asignacionmultiple')
                ->whereNotNull('id_transportista')
                ->whereNull('fecha_fin')
                ->distinct()
                ->count('id_transportista');

            $disponibles = max(0, $total - $ocupados);

            return [
                ['estado' => 'Disponibles', 'total' => $disponibles, 'color' => '#28a745'],
                ['estado' => 'En Viaje', 'total' => $ocupados, 'color' => '#ffc107'],
            ];
        } catch (\Exception $e) {
            return [
                ['estado' => 'Disponibles', 'total' => 0, 'color' => '#28a745'],
                ['estado' => 'En Viaje', 'total' => 0, 'color' => '#ffc107'],
            ];
        }
    }

    private function getEnviosPorEstado()
    {
        try {
            $resultado = [];
            $estados = DB::table('estados_envio')->get();
            $total = 0;

            foreach ($estados as $estado) {
                // Conteo más simple: verificar el último estado de cada envío
                $count = DB::table('historialestados as h1')
                    ->where('h1.id_estado_envio', $estado->id)
                    ->whereRaw('h1.id = (SELECT MAX(h2.id) FROM historialestados h2 WHERE h2.id_envio = h1.id_envio)')
                    ->count();
                
                if ($count > 0) {
                    $resultado[] = [
                        'estado' => $estado->nombre,
                        'total' => $count,
                        'color' => $this->getColorEstado($estado->nombre),
                    ];
                    $total += $count;
                }
            }

            foreach ($resultado as &$item) {
                $item['porcentaje'] = $total > 0 ? round(($item['total'] / $total) * 100, 1) : 0;
            }

            return $resultado;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getProductosPorCategoria()
    {
        try {
            return DB::table('carga')
                ->leftJoin('catalogo_categorias', 'carga.id_categoria', '=', 'catalogo_categorias.id')
                ->selectRaw('COALESCE(catalogo_categorias.nombre, \'Sin categoría\') as categoria, COUNT(*) as total')
                ->groupBy('catalogo_categorias.id', 'catalogo_categorias.nombre')
                ->orderByDesc('total')
                ->limit(8)
                ->get()
                ->map(fn($i) => ['categoria' => $i->categoria, 'total' => $i->total])
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getVariacionMensual()
    {
        try {
            $mesActual = Carbon::now()->startOfMonth()->format('Y-m-d');
            $mesAnteriorInicio = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
            $mesAnteriorFin = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');

            $actual = DB::table('envios')->whereDate('fecha_creacion', '>=', $mesActual)->count();
            $anterior = DB::table('envios')
                ->whereDate('fecha_creacion', '>=', $mesAnteriorInicio)
                ->whereDate('fecha_creacion', '<=', $mesAnteriorFin)
                ->count();

            return $anterior > 0 ? round((($actual - $anterior) / $anterior) * 100, 1) : ($actual > 0 ? 100 : 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTasaEntrega()
    {
        try {
            $total = DB::table('envios')->count();
            if ($total == 0) return 0;

            $estado = DB::table('estados_envio')->where('nombre', 'Entregado')->first();
            if (!$estado) return 0;

            $entregados = DB::table('historialestados as h1')
                ->where('h1.id_estado_envio', $estado->id)
                ->whereRaw('h1.id = (SELECT MAX(h2.id) FROM historialestados h2 WHERE h2.id_envio = h1.id_envio)')
                ->count();

            return round(($entregados / $total) * 100, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getColorEstado($estado)
    {
        return [
            'Pendiente' => '#ffc107',
            'En curso' => '#17a2b8',
            'Entregado' => '#28a745',
            'Parcialmente entregado' => '#20c997',
            'Cancelado' => '#dc3545',
        ][$estado] ?? '#6c757d';
    }
}
