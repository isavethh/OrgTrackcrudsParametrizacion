@extends('layouts.adminlte')

@section('page-title', 'Dashboard Admin')

@section('page-content')
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="total-envios">-</h3>
                <p>Envíos Totales</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="{{ route('admin.envios.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3 id="total-usuarios">-</h3>
                <p>Usuarios Registrados</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="{{ route('admin.usuarios.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3 id="total-transportistas">-</h3>
                <p>Transportistas</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('admin.transportistas.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3 id="total-vehiculos">-</h3>
                <p>Vehículos</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{ route('admin.vehiculos.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

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
        <div class="card">
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
        <div class="card">
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
<script>
    const token = localStorage.getItem('authToken');
    if (!token) {
        window.location.href = '/login';
    }

    // Cargar estadísticas
    async function cargarEstadisticas() {
        try {
            // Cargar envíos
            const resEnvios = await fetch(`${window.location.origin}/api/envios`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (resEnvios.ok) {
                const envios = await resEnvios.json();
                document.getElementById('total-envios').textContent = Array.isArray(envios) ? envios.length : 0;
                
                // Mostrar envíos recientes
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

            // Cargar usuarios
            const resUsuarios = await fetch(`${window.location.origin}/api/usuarios`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (resUsuarios.ok) {
                const usuarios = await resUsuarios.json();
                document.getElementById('total-usuarios').textContent = Array.isArray(usuarios) ? usuarios.length : 0;
            }

            // Cargar transportistas
            const resTransportistas = await fetch(`${window.location.origin}/api/transportistas`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (resTransportistas.ok) {
                const transportistas = await resTransportistas.json();
                document.getElementById('total-transportistas').textContent = Array.isArray(transportistas) ? transportistas.length : 0;
            }

            // Cargar vehículos
            const resVehiculos = await fetch(`${window.location.origin}/api/vehiculos`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (resVehiculos.ok) {
                const vehiculos = await resVehiculos.json();
                document.getElementById('total-vehiculos').textContent = Array.isArray(vehiculos) ? vehiculos.length : 0;
            }

            // --- NUEVOS CONTADORES ---
            
            // Direcciones
            fetch(`${window.location.origin}/api/ubicaciones`, { headers: { 'Authorization': `Bearer ${token}` } })
                .then(r => r.json())
                .then(d => document.getElementById('total-direcciones').textContent = Array.isArray(d) ? d.length : (d.data ? d.data.length : 0))
                .catch(e => console.error(e));

            // Tipos Vehículo
            fetch(`${window.location.origin}/api/tipos-vehiculo`, { headers: { 'Authorization': `Bearer ${token}` } })
                .then(r => r.json())
                .then(d => document.getElementById('total-tipos-vehiculo').textContent = Array.isArray(d) ? d.length : (d.data ? d.data.length : 0))
                .catch(e => console.error(e));

            // Catálogo Carga
            fetch(`${window.location.origin}/api/catalogo-carga`, { headers: { 'Authorization': `Bearer ${token}` } })
                .then(r => r.json())
                .then(d => document.getElementById('total-catalogo').textContent = Array.isArray(d) ? d.length : (d.data ? d.data.length : 0))
                .catch(e => console.error(e));

            // Incidentes
            fetch(`${window.location.origin}/api/tipos-incidente-transporte`, { headers: { 'Authorization': `Bearer ${token}` } })
                .then(r => r.json())
                .then(d => document.getElementById('total-incidentes').textContent = Array.isArray(d) ? d.length : (d.data ? d.data.length : 0))
                .catch(e => console.error(e));

        } catch (error) {
            console.error('Error cargando estadísticas:', error);
        }
    }

    cargarEstadisticas();
</script>
@endpush