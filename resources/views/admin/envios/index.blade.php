@extends('layouts.admin')

@section('title', 'Gestión de Envíos - OrgTrack Admin')
@section('page-title', 'Gestión de Envíos')


@section('content')
<style>
    .envios-admin-page {
        background-color: #f5f7fb;
        border-radius: 1rem;
        padding: 1.5rem;
    }
    .status-card {
        border-radius: .75rem;
        padding: 1rem;
        background: #fff;
        display: flex;
        align-items: center;
        min-height: 120px;
        box-shadow: 0 5px 15px rgba(15, 23, 42, 0.05);
        border: 1px solid transparent;
    }
    .status-card .status-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-right: .9rem;
    }
    .status-card h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.75rem;
    }
    .status-card small {
        color: #94a3b8;
    }
    .status-todos .status-icon { background: #eef2ff; color: #3730a3; }
    .status-pendientes .status-icon { background: #fff7e6; color: #d97706; }
    .status-asignados .status-icon { background: #fce7f3; color: #be185d; }
    .status-curso .status-icon { background: #e0f2fe; color: #0369a1; }
    .status-completados .status-icon { background: #dcfce7; color: #15803d; }

    .envio-card {
        border-radius: .85rem;
        border: 1px solid #e2e8f0;
        padding: 1.25rem;
        background: #fff;
        transition: box-shadow .2s, transform .2s;
        cursor: pointer;
        height: 100%;
    }
    .envio-card:hover {
        box-shadow: 0 15px 25px rgba(15, 23, 42, 0.12);
        transform: translateY(-4px);
    }
    .envio-badge {
        border-radius: 9999px;
        padding: .35rem .9rem;
        font-size: .8rem;
        font-weight: 600;
    }
    .badge-pendiente { background: #fef3c7; color: #c2410c; }
    .badge-asignado { background: #fce7f3; color: #be185d; }
    .badge-curso { background: #dbeafe; color: #1d4ed8; }
    .badge-completado { background: #dcfce7; color: #15803d; }
    .envio-route {
        border-left: 3px solid #e2e8f0;
        padding-left: 1rem;
        margin: 1rem 0;
    }
    .envio-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .envio-meta div {
        background: #f8fafc;
        border-radius: .75rem;
        padding: .65rem .9rem;
        flex: 1;
        min-width: 120px;
    }
    .envio-meta span {
        display: block;
        font-size: .85rem;
        color: #94a3b8;
    }
    .envio-meta strong {
        font-size: 1.05rem;
        color: #0f172a;
    }
    .filter-card {
        cursor: pointer;
        transition: all .2s;
    }
    .filter-card.active {
        border-color: #2563eb;
        box-shadow: 0 12px 28px rgba(37,99,235,.25);
    }
    @media (max-width: 767px) {
        .status-card { flex-direction: column; text-align: center; }
        .status-card .status-icon { margin-bottom: .5rem; }
        .envio-meta { flex-direction: column; }
    }
</style>
<div class="envios-admin-page">
    <div class="row mb-3">
        <div class="col-sm-6 col-lg-4 col-xl-2 mb-3">
            <div class="status-card status-todos filter-card active" data-filter="todos">
                <div class="status-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <p class="text-uppercase text-muted mb-1">Todos</p>
                    <h3 id="statTodos" data-count-pill="todos">0</h3>
                    <small>Todos los envíos</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl-2 mb-3">
            <div class="status-card status-pendientes filter-card" data-filter="pendientes">
                <div class="status-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <p class="text-uppercase text-muted mb-1">Pendientes</p>
                    <h3 id="statPendientes" data-count-pill="pendientes">0</h3>
                    <small>Esperando asignación</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl-2 mb-3">
            <div class="status-card status-asignados filter-card" data-filter="asignados">
                <div class="status-icon"><i class="fas fa-file-signature"></i></div>
                <div>
                    <p class="text-uppercase text-muted mb-1">Asignados</p>
                    <h3 id="statAsignados" data-count-pill="asignados">0</h3>
                    <small>Listos para recoger</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl-2 mb-3">
            <div class="status-card status-curso filter-card" data-filter="curso">
                <div class="status-icon"><i class="fas fa-truck"></i></div>
                <div>
                    <p class="text-uppercase text-muted mb-1">En curso</p>
                    <h3 id="statCurso" data-count-pill="curso">0</h3>
                    <small>En tránsito</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl-2 mb-3">
            <div class="status-card status-completados filter-card" data-filter="completados">
                <div class="status-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <p class="text-uppercase text-muted mb-1">Completados</p>
                    <h3 id="statCompletados" data-count-pill="completados">0</h3>
                    <small>Entregados con éxito</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center mb-3">
                <h5 class="mb-3 mb-md-0 font-weight-bold text-secondary">Listado de envíos de clientes</h5>
                <div class="input-group ml-md-auto w-100" style="max-width: 320px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="inputBuscarEnvio" class="form-control border-left-0" placeholder="Buscar envíos, clientes, origen o destino">
                </div>
            </div>
            <div class="row" id="envioGrid"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
            grid.querySelectorAll('.envio-card').forEach(card => {
                card.addEventListener('click', () => {
                    const href = card.getAttribute('data-href');
                    if (href) window.location.href = href;
                });
            });
        }

        function crearCard(envio) {
            const estado = normalizarEstado(envio.estado);
            const meta = STATUS_META[estado] || { label: envio.estado || 'Sin estado', badge: 'badge-light' };
            const metricas = envio.metricas || { particiones: 0, items: 0, peso: 0 };
            const cliente = `${envio.usuario?.nombre || ''} ${envio.usuario?.apellido || ''}`.trim() || 'Cliente sin nombre';
            const fecha = formatearFecha(envio.fecha_creacion);

            return `
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="envio-card" data-href="{{ url('/admin/envios') }}/${envio.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="h5 mb-0">#${envio.id}</div>
                            <span class="envio-badge ${meta.badge}">${meta.label}</span>
                        </div>
                        <p class="text-muted mb-2">${fecha}</p>
        <div class="envio-route">
                            <div class="mb-2">
                                <small class="text-uppercase text-muted">Recogida</small>
                <div class="font-weight-bold">${envio.nombre_origen || 'Sin origen'}</div>
                            </div>
                            <div>
                                <small class="text-uppercase text-muted">Entrega</small>
                <div class="font-weight-bold">${envio.nombre_destino || 'Sin destino'}</div>
                            </div>
                        </div>
                        <div class="envio-meta mb-3">
                            <div>
                                <span>Productos</span>
                                <strong>${metricas.items || 0}</strong>
                            </div>
                            <div>
                                <span>Peso total</span>
                                <strong>${metricas.peso || 0} kg</strong>
                            </div>
                            <div>
                                <span>Particiones</span>
                                <strong>${metricas.particiones || 0}</strong>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase text-muted">Cliente</small>
                <div class="font-weight-bold">${cliente}</div>
                            </div>
                            <button class="btn btn-outline-primary btn-sm">Ver detalle</button>
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
</script>
@endsection

