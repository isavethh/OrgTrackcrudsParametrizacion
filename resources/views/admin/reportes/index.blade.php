@extends('layouts.adminlte')

@section('page-title', 'Centro de Reportes Avanzados')

@section('page-content')
<style>
    .report-row .col-lg-4 { display: flex; margin-bottom: 1.5rem; }
    .report-row .card { width: 100%; display: flex; flex-direction: column; }
    .report-row .card-body { flex: 1; }
</style>

<div class="row">
    <div class="col-12">
        <div class="callout callout-info">
            <h5><i class="fas fa-info-circle"></i> Sistema de Reportes Avanzados</h5>
            <p class="mb-0">Seleccione un reporte para visualizar métricas, análisis y exportar en múltiples formatos (PDF, Excel).</p>
        </div>
    </div>
</div>

<div class="row report-row">
    <!-- REPORTE 1 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Envíos por Estado</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Visualizar la distribución de envíos según su estado actual para identificar cuellos de botella y medir eficiencia operativa.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Filtros por fecha</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Comparativa con período anterior</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Tasa de cancelación</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Gráfico circular interactivo</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.envios_estado') }}" class="btn btn-primary btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 2 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-truck mr-2"></i>Rendimiento Transportistas</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Evaluar productividad de transportistas, identificar empleados destacados y detectar necesidades de capacitación.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Ranking por asignaciones</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Índice de eficiencia individual</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Métricas de completados vs cancelados</li>
                    <li><i class="fas fa-check text-success mr-2"></i>KPIs globales del equipo</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.envios_transportista') }}" class="btn btn-success btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 3 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Catálogo de Productos</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Gestión de catálogo, inventario de productos por categoría y análisis de oferta disponible.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Filtro por categoría</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Agrupación jerárquica</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Detección de productos sin categoría</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Especificaciones técnicas</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.productos_enviados') }}" class="btn btn-info btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 4 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Usuarios por Rol</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Control de accesos y seguridad, distribución de roles y planificación de recursos humanos.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Distribución porcentual por rol</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Filtro por tipo de rol</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Usuarios nuevos últimos 30 días</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Detalle de cada usuario</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.usuarios_rol') }}" class="btn btn-warning btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 5 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-car mr-2"></i>Flota de Vehículos</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Gestión de flota vehicular, control de disponibilidad y planificación de capacidad logística.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Distribución por tipo de vehículo</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Estado actual de la flota</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Capacidad total disponible</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Detalle de cada vehículo</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.vehiculos_tipo') }}" class="btn btn-danger btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 6 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Tendencia Mensual</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Análisis de estacionalidad, proyección de demanda e identificación de picos operativos.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Filtro por año</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Comparativa interanual</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Mes con mayor/menor demanda</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Gráfico de tendencia</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.envios_mes') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 7 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marked-alt mr-2"></i>Envío Detallado con Mapa</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Visualización completa de un envío con mapa de ruta, información del cliente, transportista e historial.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Mapa interactivo (Leaflet)</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Origen, destino y ruta</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Historial de estados</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Asignaciones de transporte</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.envio_detallado') }}" class="btn btn-primary btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 8 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box mr-2"></i>Tipos de Empaque</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Gestión de empaques disponibles con dimensiones, capacidad, tara y especificaciones técnicas.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Dimensiones (LxAxH)</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Cálculo de volumen</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Capacidad y tara</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Unidades por pallet</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.tipos_empaque') }}" class="btn btn-info btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 9 -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-weight mr-2"></i>Tamaño-Conteo</h3>
            </div>
            <div class="card-body">
                <p><strong>Propósito:</strong> Clasificaciones de tamaño con conteo por empaque para estimación de peso y planificación de carga.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success mr-2"></i>Conteo por empaque</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Peso promedio unitario</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Cálculo peso total</li>
                    <li><i class="fas fa-check text-success mr-2"></i>Estado activo/inactivo</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reportes.tamano_conteo') }}" class="btn btn-warning btn-block">
                    <i class="fas fa-arrow-right mr-2"></i>Generar Reporte
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

