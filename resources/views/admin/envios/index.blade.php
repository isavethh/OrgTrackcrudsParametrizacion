@extends('layouts.adminlte')

@section('page-title', 'Gestión de Envíos')

@push('css')
<style>
.filter-card { cursor: pointer; }
.card[data-href] { cursor: pointer; transition: all 0.3s; }
.envio-route { border-left: 3px solid #dee2e6; padding-left: 1rem; }
</style>
@endpush

@section('page-content')
<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box filter-card" data-filter="todos">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-clipboard-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Todos</span>
                <span class="info-box-number" id="statTodos">0</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box filter-card" data-filter="pendientes">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pendientes</span>
                <span class="info-box-number" id="statPendientes">0</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box filter-card" data-filter="asignados">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-file-signature"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Asignados</span>
                <span class="info-box-number" id="statAsignados">0</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box filter-card" data-filter="curso">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-truck"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">En curso</span>
                <span class="info-box-number" id="statCurso">0</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box filter-card" data-filter="completados">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Completados</span>
                <span class="info-box-number" id="statCompletados">0</span>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de envíos de clientes</h3>
        <div class="card-tools">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="inputBuscarEnvio" class="form-control" placeholder="Buscar envíos...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="row p-3" id="envioGrid"></div>
    </div>
</div>
@endsection

@push('js')
<script>
if (!window.__envioIndexAdminInitialized) {
    window.__envioIndexAdminInitialized = true;
    
    (function(){
        const rawToken = localStorage.getItem('authToken');
        const token = rawToken ? rawToken.replace(/^"+|"+$/g, '') : null;
        if (!token) {
            window.location.replace('/login');
            return;
        }

        const grid = document.getElementById('envioGrid');
        const searchInput = document.getElementById('inputBuscarEnvio');
        const statTodos = document.getElementById('statTodos');
        const statPendientes = document.getElementById('statPendientes');
        const statAsignados = document.getElementById('statAsignados');
        const statCurso = document.getElementById('statCurso');
        const statCompletados = document.getElementById('statCompletados');
        const filterCards = document.querySelectorAll('.filter-card');
        const pillCounters = document.querySelectorAll('[data-count-pill]');

        const STATUS_GROUPS = {
            pendientes: (estado) => ['pendiente', 'sin estado', 'sin asignar'].includes(estado),
            asignados: (estado) => ['asignado'].includes(estado),
            curso: (estado) => ['en curso'].includes(estado),
            completados: (estado) => ['entregado', 'finalizado'].includes(estado),
            todos: () => true
        };

        const STATUS_META = {
            'pendiente': { label: 'Pendiente', badge: 'badge-pendiente' },
            'sin estado': { label: 'Pendiente', badge: 'badge-pendiente' },
            'sin asignar': { label: 'Pendiente', badge: 'badge-pendiente' },
            'asignado': { label: 'Asignado', badge: 'badge-asignado' },
            'en curso': { label: 'En curso', badge: 'badge-curso' },
            'entregado': { label: 'Completado', badge: 'badge-completado' },
            'finalizado': { label: 'Completado', badge: 'badge-completado' },
        };

        let envios = [];
        let activeFilter = 'todos';
        let searchTerm = '';

        filterCards.forEach(card => {
            card.addEventListener('click', () => {
                const filter = card.getAttribute('data-filter');
                if (!filter || filter === activeFilter) return;
                activeFilter = filter;
                filterCards.forEach(c => c.classList.toggle('active', c.getAttribute('data-filter') === filter));
                renderGrid();
            });
        });

        searchInput.addEventListener('input', (event) => {
            searchTerm = event.target.value.trim().toLowerCase();
            renderGrid();
        });

        async function fetchEnvios() {
            grid.innerHTML = '<div class="col-12 text-center text-muted py-5"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando envíos...</div>';
            try {
                const res = await fetch(`${window.location.origin}/api/envios`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (res.status === 401) {
                    localStorage.removeItem('authToken');
                    localStorage.removeItem('usuario');
                    window.location.replace('/login');
                    return;
                }
                if (!res.ok) throw new Error('No se pudieron cargar los envíos');

                const base = await res.json();
                envios = await enriquecerEnvios(Array.isArray(base) ? base : []);
                renderSummary();
                renderGrid();
            } catch (error) {
                console.error(error);
                grid.innerHTML = `<div class="col-12 text-center text-danger py-5">${error.message}</div>`;
            }
        }

        async function enriquecerEnvios(lista) {
            return Promise.all(lista.map(async (envio) => {
                const enriched = { ...envio, metricas: { particiones: 0, items: 0, peso: 0 } };
                try {
                    const detailRes = await fetch(`${window.location.origin}/api/envios/${envio.id}`, {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    if (detailRes.ok) {
                        const detalle = await detailRes.json();
                        const particiones = Array.isArray(detalle.particiones) ? detalle.particiones : [];
                        const items = particiones.reduce((acc, part) => {
                            return acc + (Array.isArray(part.cargas) ? part.cargas.reduce((sum, carga) => sum + (Number(carga.cantidad) || 0), 0) : 0);
                        }, 0);
                        const peso = particiones.reduce((acc, part) => {
                            return acc + (Array.isArray(part.cargas) ? part.cargas.reduce((sum, carga) => sum + (Number(carga.peso) || 0), 0) : 0);
                        }, 0);
                        enriched.metricas = {
                            particiones: particiones.length,
                            items,
                            peso: Number(peso.toFixed(1))
                        };
                    }
                } catch (error) {
                    console.warn('No se pudo enriquecer el envío', envio.id, error);
                }
                return enriched;
            }));
        }

        function renderSummary() {
            const resumen = calcularResumen(envios);
            statTodos.textContent = resumen.todos;
            statPendientes.textContent = resumen.pendientes;
            statAsignados.textContent = resumen.asignados;
            statCurso.textContent = resumen.curso;
            statCompletados.textContent = resumen.completados;

            pillCounters.forEach(pill => {
                const key = pill.getAttribute('data-count-pill');
                pill.textContent = resumen[key] ?? resumen.total;
            });
        }

        function calcularResumen(data) {
            const counts = { pendientes: 0, asignados: 0, curso: 0, completados: 0, todos: data.length, total: data.length };
            data.forEach(envio => {
                const estado = normalizarEstado(envio.estado);
                if (STATUS_GROUPS.pendientes(estado)) counts.pendientes += 1;
                if (STATUS_GROUPS.asignados(estado)) counts.asignados += 1;
                if (STATUS_GROUPS.curso(estado)) counts.curso += 1;
                if (STATUS_GROUPS.completados(estado)) counts.completados += 1;
            });
            return counts;
        }

        function renderGrid() {
            if (!envios.length) {
                grid.innerHTML = '<div class="col-12 text-center text-muted py-5">No hay envíos registrados.</div>';
                return;
            }
            const filtrados = envios
                .filter(envio => STATUS_GROUPS[activeFilter]?.(normalizarEstado(envio.estado)) ?? true)
                .filter(envio => coincideBusqueda(envio, searchTerm));

            if (!filtrados.length) {
                grid.innerHTML = '<div class="col-12 text-center text-muted py-5">No hay envíos que coincidan con el filtro.</div>';
                return;
            }

            grid.innerHTML = filtrados.map(envio => crearCard(envio)).join('');
            grid.querySelectorAll('.card[data-href]').forEach(card => {
                card.addEventListener('click', (e) => {
                    if (!e.target.closest('button')) {
                        const href = card.getAttribute('data-href');
                        if (href) window.location.href = href;
                    }
                });
            });
        }

        function crearCard(envio) {
            const estado = normalizarEstado(envio.estado);
            const meta = STATUS_META[estado] || { label: envio.estado || 'Sin estado', badge: 'badge-light' };
            const badgeClass = meta.badge.replace('badge-pendiente', 'badge-warning')
                                        .replace('badge-asignado', 'badge-info')
                                        .replace('badge-curso', 'badge-primary')
                                        .replace('badge-completado', 'badge-success');
            const metricas = envio.metricas || { particiones: 0, items: 0, peso: 0 };
            const cliente = `${envio.usuario?.nombre || ''} ${envio.usuario?.apellido || ''}`.trim() || 'Cliente sin nombre';
            const fecha = formatearFecha(envio.fecha_creacion);

            return `
                <div class="col-xl-4 col-lg-6 mb-3">
                    <div class="card card-outline card-primary" data-href="{{ url('/admin/envios') }}/${envio.id}">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <strong>#${envio.id}</strong>
                                <span class="badge ${badgeClass} float-right">${meta.label}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3"><i class="far fa-calendar mr-1"></i>${fecha}</p>
                            
                            <div class="mb-3 pb-3 envio-route">
                                <div class="mb-2">
                                    <small class="text-muted text-uppercase">Recogida</small>
                                    <div class="font-weight-bold">${envio.nombre_origen || 'Sin origen'}</div>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase">Entrega</small>
                                    <div class="font-weight-bold">${envio.nombre_destino || 'Sin destino'}</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-4 text-center">
                                    <div class="bg-light p-2 rounded">
                                        <small class="text-muted d-block">Productos</small>
                                        <strong>${metricas.items || 0}</strong>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="bg-light p-2 rounded">
                                        <small class="text-muted d-block">Peso</small>
                                        <strong>${metricas.peso || 0} kg</strong>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="bg-light p-2 rounded">
                                        <small class="text-muted d-block">Particiones</small>
                                        <strong>${metricas.particiones || 0}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted text-uppercase">Cliente</small>
                                    <div class="font-weight-bold">${cliente}</div>
                                </div>
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye mr-1"></i>Ver
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
        }

        function normalizarEstado(estado) {
            return (estado || '').toString().trim().toLowerCase() || 'sin estado';
        }

        function formatearFecha(value) {
            if (!value) return 'Fecha no registrada';
            try {
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) return value;
                return date.toLocaleDateString('es-BO', { weekday: 'short', day: 'numeric', month: 'short' });
            } catch {
                return value;
            }
        }

        function coincideBusqueda(envio, termino) {
            if (!termino) return true;
            const texto = [
                `#${envio.id}`,
                envio.nombre_origen || '',
                envio.nombre_destino || '',
                envio.usuario?.nombre || '',
                envio.usuario?.apellido || ''
            ].join(' ').toLowerCase();
            return texto.includes(termino);
        }

        fetchEnvios();
    })();
    
} // Fin de window.__envioIndexAdminInitialized
</script>
@endpush