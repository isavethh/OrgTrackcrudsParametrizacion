@extends('layouts.adminlte')

@section('page-title', 'Dashboard Admin')

@section('page-content')

    <style>
        .metrics-row {
            display: flex;
            gap: 2rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }

        .metric-card {
            flex: 1 1 220px;
            min-width: 220px;
            max-width: 260px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 1.2rem 1rem 1rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .metric-card .metric-value {
            font-size: 2.2rem;
            font-weight: bold;
            color: #fff;
            margin-bottom: 0.2rem;
            z-index: 2;
        }

        .metric-card .metric-label {
            font-size: 1.1rem;
            color: #fff;
            margin-bottom: 1.2rem;
            z-index: 2;
        }

        .metric-card .metric-link {
            background: rgba(0, 0, 0, 0.08);
            color: #fff;
            border-radius: 0 0 16px 16px;
            padding: 0.5rem 1rem;
            width: 100%;
            text-align: right;
            font-weight: 500;
            text-decoration: none;
            position: absolute;
            left: 0;
            bottom: 0;
            z-index: 2;
        }

        .metric-card.bg-info {
            background: #17a2b8;
        }

        .metric-card.bg-success {
            background: #28a745;
        }

        .metric-card.bg-warning {
            background: #ffc107;
            color: #222;
        }

        .metric-card.bg-danger {
            background: #dc3545;
        }

        .metric-card.bg-warning .metric-value,
        .metric-card.bg-warning .metric-label {
            color: #222;
        }

        @media (max-width: 900px) {
            .metrics-row {
                flex-direction: column;
                gap: 1rem;
            }
        }

        .dashboard-graphs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 1rem;
        }

        .dashboard-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            transition: box-shadow 0.2s;
            min-height: 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dashboard-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .dashboard-title {
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1.2rem;
            color: #222;
        }

        .dashboard-canvas {
            width: 100% !important;
            max-width: 350px;
            height: 260px !important;
        }

        @media (max-width: 900px) {
            .dashboard-graphs {
                grid-template-columns: 1fr;
            }
        }
    </style>


    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="total-envios">-</h3>
                    <p>Envíos Totales</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="{{ route('admin.envios.index') }}" class="small-box-footer">Más información <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="total-usuarios">-</h3>
                    <p>Usuarios Registrados</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ route('admin.usuarios.index') }}" class="small-box-footer">Más información <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="total-transportistas">-</h3>
                    <p>Transportistas</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="{{ route('admin.transportistas.index') }}" class="small-box-footer">Más información <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="total-vehiculos">-</h3>
                    <p>Vehículos</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="{{ route('admin.vehiculos.index') }}" class="small-box-footer">Más información <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card" style="min-height: 400px;">
                <div class="card-header"><b>Tendencia de envíos por mes</b></div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="enviosPorMesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card" style="min-height: 400px;">
                <div class="card-header"><b>Transportistas - Disponibilidad</b></div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="estadosEnviosChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card" style="min-height: 400px;">
                <div class="card-header"><b>Porcentaje de estados de los envíos</b></div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="estadosPieChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card" style="min-height: 400px;">
                <div class="card-header"><b>Comparativa de productos enviados por categoría</b></div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="productosPorCategoriaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- CONFIGURACIÓN ROW -->
    <h5 class="mb-2 mt-4">Resumen de Configuración</h5>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-map-marker-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Direcciones</span>
                    <span class="info-box-number" id="total-direcciones">-</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-truck-pickup"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tipos Vehículo</span>
                    <span class="info-box-number" id="total-tipos-vehiculo">-</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Catálogo Carga</span>
                    <span class="info-box-number" id="total-catalogo">-</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tipos Incidente</span>
                    <span class="info-box-number" id="total-incidentes">-</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Recent Envios -->
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">Envíos Recientes</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Destinatario</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="recent-envios">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('admin.envios.index') }}" class="uppercase">Ver todos los envíos</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-6">
            <!-- Quick Actions -->
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">Acciones Rápidas</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('admin.envios.create') }}" class="btn btn-primary btn-block mb-3">
                                <i class="fas fa-plus"></i> Nuevo Envío
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-success btn-block mb-3">
                                <i class="fas fa-users"></i> Gestionar Usuarios
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('admin.transportistas.index') }}" class="btn btn-info btn-block mb-3">
                                <i class="fas fa-user-friends"></i> Transportistas
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.vehiculos.index') }}" class="btn btn-warning btn-block mb-3">
                                <i class="fas fa-truck"></i> Vehículos
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@push('js')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.enviosPorMesChart = window.enviosPorMesChart || null;
        window.estadosEnviosChart = window.estadosEnviosChart || null;
        window.estadosPieChart = window.estadosPieChart || null;
        window.productosPorCategoriaChart = window.productosPorCategoriaChart || null;

        (function () {
            const dashboardToken = localStorage.getItem('authToken');
            if (!dashboardToken) {
                window.location.href = '/login';
                return;
            }

            async function cargarEstadisticas() {
                try {
                    // ========== KPIs (usando APIs originales) ==========
                    // Envíos
                    const resEnvios = await fetch(`/api/envios`, {
                        headers: { 'Authorization': `Bearer ${dashboardToken}` }
                    });
                    let envios = [];
                    if (resEnvios.ok) {
                        envios = await resEnvios.json();
                        document.getElementById('total-envios').textContent = Array.isArray(envios) ? envios.length : 0;

                        const recentEnvios = Array.isArray(envios) ? envios.slice(0, 5) : [];
                        const tbody = document.getElementById('recent-envios');
                        if (recentEnvios.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay envíos</td></tr>';
                        } else {
                            tbody.innerHTML = recentEnvios.map(e => `
                                            <tr>
                                                <td>#${e.id}</td>
                                                <td>${e.nombre_destino || '—'}</td>
                                                <td><span class="badge badge-${e.estado === 'Entregado' ? 'success' : e.estado === 'En curso' ? 'info' : 'warning'}">${e.estado || '—'}</span></td>
                                                <td>${e.fecha_creacion || '—'}</td>
                                            </tr>
                                        `).join('');
                        }
                    }

                    // Usuarios
                    fetch(`/api/usuarios`, { headers: { 'Authorization': `Bearer ${dashboardToken}` } })
                        .then(r => r.ok ? r.json() : [])
                        .then(d => document.getElementById('total-usuarios').textContent = Array.isArray(d) ? d.length : 0);

                    // Transportistas
                    fetch(`/api/transportistas`, { headers: { 'Authorization': `Bearer ${dashboardToken}` } })
                        .then(r => r.ok ? r.json() : [])
                        .then(d => document.getElementById('total-transportistas').textContent = Array.isArray(d) ? d.length : 0);

                    // Vehículos
                    fetch(`/api/vehiculos`, { headers: { 'Authorization': `Bearer ${dashboardToken}` } })
                        .then(r => r.ok ? r.json() : [])
                        .then(d => document.getElementById('total-vehiculos').textContent = Array.isArray(d) ? d.length : 0);

                    // Direcciones
                    fetch(`/api/ubicaciones`, { headers: { 'Authorization': `Bearer ${dashboardToken}` } })
                        .then(r => r.ok ? r.json() : [])
                        .then(d => document.getElementById('total-direcciones').textContent = Array.isArray(d) ? d.length : (d.data ? d.data.length : 0));

                    // Tipos Vehículo
                    fetch(`/api/tipos-vehiculo`, { headers: { 'Authorization': `Bearer ${dashboardToken}` } })
                        .then(r => r.ok ? r.json() : [])
                        .then(d => document.getElementById('total-tipos-vehiculo').textContent = Array.isArray(d) ? d.length : 0);

                    // Catálogo Categorías
                    fetch(`/api/catalogo-categorias`)
                        .then(r => r.ok ? r.json() : [])
                        .then(d => document.getElementById('total-catalogo').textContent = Array.isArray(d) ? d.length : 0);

                    // Incidentes
                    fetch(`/api/tipos-incidente-transporte`, { headers: { 'Authorization': `Bearer ${dashboardToken}` } })
                        .then(r => r.ok ? r.json() : [])
                        .then(d => document.getElementById('total-incidentes').textContent = Array.isArray(d) ? d.length : 0);

                    // ========== GRÁFICAS (usando nuevo endpoint) ==========
                    const resCharts = await fetch('/admin/api/dashboard/stats');
                    const chartData = resCharts.ok ? await resCharts.json() : {};

                    // GRÁFICA 1: Tendencia de envíos por mes
                    if (window.enviosPorMesChart && typeof window.enviosPorMesChart.destroy === 'function') window.enviosPorMesChart.destroy();
                    const enviosMes = chartData.envios_por_mes || [];
                    if (enviosMes.length) {
                        window.enviosPorMesChart = new Chart(document.getElementById('enviosPorMesChart'), {
                            type: 'line',
                            data: {
                                labels: enviosMes.map(m => m.mes_corto),
                                datasets: [{
                                    label: 'Envíos',
                                    data: enviosMes.map(m => m.total),
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    fill: true,
                                    tension: 0.3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: `Variación: ${chartData.variacion_mensual > 0 ? '+' : ''}${chartData.variacion_mensual || 0}% vs mes anterior` }
                                }
                            }
                        });
                    }

                    // GRÁFICA 2: Transportistas Disponibilidad
                    if (window.estadosEnviosChart && typeof window.estadosEnviosChart.destroy === 'function') window.estadosEnviosChart.destroy();
                    const transpDisp = chartData.transportistas_disponibilidad || [];
                    if (transpDisp.length) {
                        window.estadosEnviosChart = new Chart(document.getElementById('estadosEnviosChart'), {
                            type: 'doughnut',
                            data: {
                                labels: transpDisp.map(t => t.estado),
                                datasets: [{
                                    data: transpDisp.map(t => t.total),
                                    backgroundColor: transpDisp.map(t => t.color)
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false }
                        });
                    }

                    // GRÁFICA 3: Estados de envíos
                    if (window.estadosPieChart && typeof window.estadosPieChart.destroy === 'function') window.estadosPieChart.destroy();
                    const estadosEnvio = chartData.envios_por_estado || [];
                    if (estadosEnvio.length) {
                        window.estadosPieChart = new Chart(document.getElementById('estadosPieChart'), {
                            type: 'pie',
                            data: {
                                labels: estadosEnvio.map(e => `${e.estado} (${e.porcentaje}%)`),
                                datasets: [{
                                    data: estadosEnvio.map(e => e.total),
                                    backgroundColor: estadosEnvio.map(e => e.color)
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { title: { display: true, text: `Tasa de entrega: ${chartData.tasa_entrega || 0}%` } }
                            }
                        });
                    }

                    // GRÁFICA 4: Productos por categoría
                    if (window.productosPorCategoriaChart && typeof window.productosPorCategoriaChart.destroy === 'function') window.productosPorCategoriaChart.destroy();
                    const productosCat = chartData.productos_por_categoria || [];
                    if (productosCat.length) {
                        window.productosPorCategoriaChart = new Chart(document.getElementById('productosPorCategoriaChart'), {
                            type: 'bar',
                            data: {
                                labels: productosCat.map(p => p.categoria),
                                datasets: [{
                                    label: 'Cargas',
                                    data: productosCat.map(p => p.total),
                                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#7BC225']
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y' }
                        });
                    }

                } catch (error) {
                    console.error('Error cargando estadísticas:', error);
                }
            }

            cargarEstadisticas();
        })();
    </script>
@endpush