@extends('layouts.cliente')

@section('page-title', 'Mis Envíos')

@push('css')
    <style>
        .filter-card {
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .filter-card.active .info-box {
            border: 2px solid #007bff;
            background-color: #f4f6f9;
        }

        .card[data-href] {
            cursor: pointer;
            transition: all 0.3s;
        }

        .card[data-href]:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .envio-route {
            border-left: 3px solid #dee2e6;
            padding-left: 1rem;
        }

        .text-truncate-2lines {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4em;
            min-height: 2.8em;
            max-height: 2.8em;
        }
    </style>
@endpush

@section('page-content')
    <!-- Widgets de Resumen y Filtrado -->
    <div class="row">
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
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-signature"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Asignados</span>
                    <span class="info-box-number" id="statAsignados">0</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box filter-card" data-filter="curso">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">En curso</span>
                    <span class="info-box-number" id="statCurso">0</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box filter-card" data-filter="parcial">
                <span class="info-box-icon bg-orange elevation-1"><i class="fas fa-shipping-fast text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Parc. Entregado</span>
                    <span class="info-box-number" id="statParcial">0</span>
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
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box filter-card" data-filter="rechazados">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Cancelados</span>
                    <span class="info-box-number" id="statRechazados">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal con Búsqueda -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de mis envíos</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 400px;">
                    <a href="{{ route('envios.create') }}" class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-plus"></i> Nuevo Envío
                    </a>
                    <input type="text" id="inputBuscarEnvio" class="form-control" placeholder="Buscar envíos...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body bg-light">
            <div class="row" id="envioGrid">
                <!-- Las cards se renderizarán aquí -->
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        if (!window.__envioIndexClienteInitialized) {
            window.__envioIndexClienteInitialized = true;

            // Auth
            const _rawToken = localStorage.getItem('authToken');
            const token = _rawToken ? _rawToken.replace(/^"+|"+$/g, '') : null;
            if (!token) { window.location.href = '/login'; }

            // Elementos del DOM
            const grid = document.getElementById('envioGrid');
            const searchInput = document.getElementById('inputBuscarEnvio');
            const statPendientes = document.getElementById('statPendientes');
            const statAsignados = document.getElementById('statAsignados');
            const statCurso = document.getElementById('statCurso');
            const statParcial = document.getElementById('statParcial');
            const statCompletados = document.getElementById('statCompletados');
            const statRechazados = document.getElementById('statRechazados');
            const filterCards = document.querySelectorAll('.filter-card');

            // Definición de Grupos de Estado
            const STATUS_GROUPS = {
                pendientes: (estado) => ['pendiente', 'sin estado', 'sin asignar'].includes(estado),
                asignados: (estado) => ['asignado'].includes(estado),
                curso: (estado) => ['en curso'].includes(estado),
                parcial: (estado) => ['parcialmente entregado'].includes(estado),
                completados: (estado) => ['entregado', 'finalizado'].includes(estado),
                rechazados: (estado) => ['cancelado', 'rechazado'].includes(estado)
            };

            const STATUS_META = {
                'pendiente': { label: 'PENDIENTE', badge: 'badge-warning', icon: 'fa-clock' },
                'sin estado': { label: 'PENDIENTE', badge: 'badge-warning', icon: 'fa-clock' },
                'sin asignar': { label: 'PENDIENTE', badge: 'badge-warning', icon: 'fa-clock' },
                'asignado': { label: 'ASIGNADO', badge: 'badge-info', icon: 'fa-file-signature' },
                'en curso': { label: 'EN CURSO', badge: 'badge-primary', icon: 'fa-truck' },
                'parcialmente entregado': { label: 'PARCIAL', badge: 'badge-warning', icon: 'fa-box-open' },
                'entregado': { label: 'COMPLETADO', badge: 'badge-success', icon: 'fa-check' },
                'finalizado': { label: 'COMPLETADO', badge: 'badge-success', icon: 'fa-check' },
                'cancelado': { label: 'CANCELADO', badge: 'badge-danger', icon: 'fa-times' },
                'rechazado': { label: 'RECHAZADO', badge: 'badge-danger', icon: 'fa-ban' },
            };

            let envios = [];
            let activeFilter = null;
            let searchTerm = '';

            // Inicializar Filtros
            filterCards.forEach(card => {
                card.addEventListener('click', () => {
                    const filter = card.getAttribute('data-filter');
                    if (filter === activeFilter) return;

                    activeFilter = filter;

                    filterCards.forEach(c => {
                        if (c.getAttribute('data-filter') === filter) {
                            c.classList.add('active');
                            c.classList.add('bg-light');
                        } else {
                            c.classList.remove('active');
                            c.classList.remove('bg-light');
                        }
                    });

                    renderGrid();
                });
            });

            searchInput.addEventListener('input', (event) => {
                searchTerm = event.target.value.trim().toLowerCase();
                renderGrid();
            });

            function normalizarEstado(est) {
                return (est || 'pendiente').toLowerCase().trim();
            }

            function coincideBusqueda(envio, term) {
                if (!term) return true;
                const idStr = String(envio.id);
                const origen = (envio.nombre_origen || '').toLowerCase();
                const destino = (envio.nombre_destino || '').toLowerCase();
                return idStr.includes(term) || origen.includes(term) || destino.includes(term);
            }

            function crearCard(envio) {
                const estado = normalizarEstado(envio.estado);
                const meta = STATUS_META[estado] || { label: (envio.estado || 'SIN ESTADO').toUpperCase(), badge: 'badge-secondary' };

                let badgeStyleClass = 'badge-secondary';
                if (estado.includes('pendiente')) badgeStyleClass = 'badge-warning'; // Yellow
                else if (estado.includes('asignado')) badgeStyleClass = 'badge-info'; // Blue
                else if (estado.includes('curso')) badgeStyleClass = 'badge-primary'; // Dark Blue
                else if (estado.includes('entregado') || estado.includes('finalizado')) badgeStyleClass = 'badge-success'; // Green
                else if (estado.includes('cancelado') || estado.includes('rechazado')) badgeStyleClass = 'badge-danger'; // Red
                else if (estado.includes('parcial')) badgeStyleClass = 'badge-warning'; // Orange-ish

                const metricas = envio.metricas || { particiones: 0, items: 0, peso: 0 };
                const fecha = envio.fecha_creacion ? new Date(envio.fecha_creacion).toLocaleDateString() : '—';

                return `
                        <div class="col-12 col-md-6 col-lg-4 d-flex align-items-stretch mb-4">
                            <div class="card w-100 shadow-sm border-0" data-href="{{ url('/envios') }}/${envio.id}" style="border-radius: 8px;">
                                <div class="card-body">
                                    <!-- Header: Badge & Status -->
                                    <div class="mb-2">
                                        <span class="badge ${badgeStyleClass} py-2 px-3" style="font-size: 0.85rem; border-radius: 4px;">
                                            #${envio.id} ${meta.label}
                                        </span>
                                    </div>

                                    <!-- Date -->
                                    <div class="text-muted small mb-4">
                                        <i class="far fa-calendar-alt mr-1"></i> ${fecha}
                                    </div>

                                    <!-- Route Timeline -->
                                    <div class="envio-route position-relative pl-3 mb-4" style="border-left: 3px solid #dee2e6;">
                                        <div class="mb-3">
                                            <small class="text-secondary font-weight-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">RECOGIDA</small>
                                            <div class="font-weight-bold text-dark text-truncate-2lines" style="line-height: 1.2;">
                                                ${envio.nombre_origen || 'No especificado'}
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-secondary font-weight-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">ENTREGA</small>
                                            <div class="font-weight-bold text-dark text-truncate-2lines" style="line-height: 1.2;">
                                                ${envio.nombre_destino || 'No especificado'}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Metrics Box -->
                                    <div class="row bg-light rounded py-3 mx-0 mb-4 justify-content-between text-center">
                                        <div class="col-4 border-right">
                                            <small class="d-block text-muted text-uppercase" style="font-size: 0.6rem;">Productos</small>
                                            <strong class="h6 mb-0 text-dark">${metricas.items}</strong>
                                        </div>
                                        <div class="col-4 border-right">
                                            <small class="d-block text-muted text-uppercase" style="font-size: 0.6rem;">Peso</small>
                                            <strong class="h6 mb-0 text-dark">${metricas.peso} kg</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="d-block text-muted text-uppercase" style="font-size: 0.6rem;">Particiones</small>
                                            <strong class="h6 mb-0 text-dark">${metricas.particiones}</strong>
                                        </div>
                                    </div>

                                    <!-- Footer Actions -->
                                    <div class="d-flex justify-content-between align-items-center">
                                         <div>
                                            <small class="text-muted font-weight-bold text-uppercase d-block" style="font-size: 0.7rem;">CLIENTE</small>
                                            <strong class="text-dark">Tú</strong>
                                         </div>
                                         <button class="btn btn-primary d-flex align-items-center px-4" style="border-radius: 50px;">
                                            <i class="fas fa-eye mr-2"></i> Ver
                                         </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
            }

            function renderSummary() {
                const counts = { pendientes: 0, asignados: 0, curso: 0, parcial: 0, completados: 0, rechazados: 0 };
                envios.forEach(envio => {
                    const estado = normalizarEstado(envio.estado);
                    if (STATUS_GROUPS.pendientes(estado)) counts.pendientes++;
                    if (STATUS_GROUPS.asignados(estado)) counts.asignados++;
                    if (STATUS_GROUPS.curso(estado)) counts.curso++;
                    if (STATUS_GROUPS.parcial(estado)) counts.parcial++;
                    if (STATUS_GROUPS.completados(estado)) counts.completados++;
                    if (STATUS_GROUPS.rechazados(estado)) counts.rechazados++;
                });

                statPendientes.textContent = counts.pendientes;
                statAsignados.textContent = counts.asignados;
                statCurso.textContent = counts.curso;
                statParcial.textContent = counts.parcial;
                statCompletados.textContent = counts.completados;
                statRechazados.textContent = counts.rechazados;

                if (!activeFilter) {
                    if (counts.curso > 0) activeFilter = 'curso';
                    else if (counts.pendientes > 0) activeFilter = 'pendientes';
                    else activeFilter = 'completados';

                    const widget = document.querySelector(`.filter-card[data-filter="${activeFilter}"]`);
                    if (widget) {
                        widget.classList.add('active');
                        widget.classList.add('bg-light');
                    }
                }
            }

            function getEmptyMessage(filter) {
                switch (filter) {
                    case 'pendientes': return 'No tienes envíos pendientes de aprobación.';
                    case 'asignados': return 'No tienes envíos asignados actualmente.';
                    case 'curso': return 'No tienes envíos en tránsito en este momento.';
                    case 'parcial': return 'No tienes envíos parcialmente entregados.';
                    case 'completados': return 'No tienes envíos completados en el historial.';
                    case 'rechazados': return 'No tienes envíos cancelados.';
                    default: return 'No hay envíos.';
                }
            }

            function renderGrid() {
                if (!envios.length) {
                    grid.innerHTML = '<div class="col-12 text-center text-muted py-5"><i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i><br>No tienes envíos registrados.</div>';
                    return;
                }

                const filtrados = envios
                    .filter(envio => !activeFilter || (STATUS_GROUPS[activeFilter] && STATUS_GROUPS[activeFilter](normalizarEstado(envio.estado))))
                    .filter(envio => coincideBusqueda(envio, searchTerm));

                if (!filtrados.length) {
                    const emptyMsg = getEmptyMessage(activeFilter);
                    grid.innerHTML = `<div class="col-12 text-center text-muted py-5"><i class="fas fa-search fa-3x mb-3 text-gray-300"></i><br>${emptyMsg}</div>`;
                    return;
                }

                grid.innerHTML = filtrados.map(e => crearCard(e)).join('');

                grid.querySelectorAll('.card[data-href]').forEach(card => {
                    card.addEventListener('click', (e) => {
                        if (!e.target.closest('button') && !e.target.closest('a')) {
                            const href = card.getAttribute('data-href');
                            if (href) window.location.href = href;
                        }
                    });
                });
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

            async function cargarEnvios() {
                try {
                    grid.innerHTML = '<div class="col-12 text-center text-muted py-5"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando envíos...</div>';

                    const res = await fetch(`${window.location.origin}/api/envios`, {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    if (!res.ok) {
                        if (res.status === 401) { localStorage.removeItem('authToken'); window.location.href = '/login'; return; }
                        throw new Error('No se pudieron cargar los envíos');
                    }

                    const base = await res.json();

                    envios = await enriquecerEnvios(Array.isArray(base) ? base : []);

                    renderSummary();
                    renderGrid();

                } catch (e) {
                    grid.innerHTML = `<div class="col-12 text-center text-danger py-5">${e.message}</div>`;
                }
            }
            cargarEnvios();

        } // Fin de window.__envioIndexClienteInitialized
    </script>
@endpush