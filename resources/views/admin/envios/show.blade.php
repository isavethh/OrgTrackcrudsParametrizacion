@extends('layouts.admin')

@section('title', 'Seguimiento del envío - OrgTrack')
@section('page-title', 'Seguimiento del envío')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.envios.index') }}">Envíos</a></li>
    <li class="breadcrumb-item active">Detalle</li>
@endsection

@section('content')
<style>
    .assignment-section {
        background: #fdfdfd;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 1.25rem;
    }
    .selection-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .selection-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.85rem 1rem;
        background: #fff;
        width: 100%;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all .2s ease;
        cursor: pointer;
    }
    .selection-card:hover {
        border-color: #c3d4ff;
        box-shadow: 0 8px 20px rgba(96, 165, 250, 0.25);
    }
    .selection-card.active {
        border-color: #28a745;
        background: #f5fff8;
        box-shadow: 0 12px 24px rgba(40, 167, 69, 0.22);
    }
    .selection-card.disabled {
        opacity: .6;
        cursor: not-allowed;
        box-shadow: none;
    }
    .selection-avatar {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 0.75rem;
    }
    .selection-title {
        font-weight: 600;
        margin-bottom: 0.15rem;
    }
    .selection-subtitle {
        font-size: 0.85rem;
        color: #6b7280;
    }
    .selection-status {
        font-size: 0.78rem;
        font-weight: 600;
    }
    .assignment-empty {
        border: 1px dashed #d1d5db;
        background: #f9fafb;
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
        color: #6b7280;
        font-weight: 500;
    }
    .assignment-confirm-btn {
        border-radius: 999px;
        font-weight: 600;
        padding: 0.9rem;
    }
    .assignment-alert {
        border-radius: 14px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    @media (max-width: 991px) {
        .assignment-section {
            padding: 1rem;
        }
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="detalleEnvio"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const _rawToken = localStorage.getItem('authToken');
    const token = _rawToken ? _rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) { window.location.href = '/login'; }

    const envioId = {{ (int)($id ?? 0) }};
    const cont = document.getElementById('detalleEnvio');
    const state = {
        envio: null,
        transportistas: [],
        vehiculos: [],
        selecciones: {}
    };
    const loaderHtml = '<div class="text-center text-muted py-4">Cargando particiones...</div>';

    function badgeFor(estado){
        const map = { 'En curso':'badge-info', 'Pendiente':'badge-warning', 'Asignado':'badge-primary', 'Entregado':'badge-success', 'Finalizado':'badge-secondary' };
        const cls = map[estado] || 'badge-light';
        return `<span class="badge ${cls} ml-1">${estado}</span>`;
    }

    function initials(nombre = '', apellido = '') {
        const parts = [nombre, apellido].filter(Boolean);
        if (!parts.length) return '—';
        return parts.map(p => p.trim()[0] || '').join('').substring(0, 2).toUpperCase() || '—';
    }

    function formatCapacidad(valor) {
        if (valor === null || valor === undefined) return '—';
        const num = Number(valor);
        if (Number.isNaN(num)) { return valor; }
        return `${num.toLocaleString('es-BO')} kg`;
    }

    function vehiculosCompatiblesPara(particion) {
        const nombre = (particion.tipoTransporte?.nombre || '').toLowerCase();
        if (!nombre) { return state.vehiculos; }
        const compatibles = state.vehiculos.filter(v => (v.tipo_transporte?.nombre || '').toLowerCase() === nombre);
        return compatibles.length ? compatibles : state.vehiculos;
    }

    function buildTransportistasList(asignacionId) {
        if (!state.transportistas.length) {
            return '<div class="assignment-empty">No hay transportistas disponibles</div>';
        }
        return state.transportistas.map(t => {
            const nombreCompleto = [t.nombre, t.apellido].filter(Boolean).join(' ') || 'Sin nombre';
            return `
                <button type="button" class="selection-card" data-role="transportista" data-id="${t.id}" data-asignacion="${asignacionId}">
                    <div class="d-flex align-items-center">
                        <div class="selection-avatar">${initials(t.nombre, t.apellido)}</div>
                        <div class="text-left">
                            <div class="selection-title">${nombreCompleto}</div>
                            <div class="selection-subtitle">Tel: ${t.telefono || '—'}</div>
                        </div>
                    </div>
                    <div class="selection-status text-success">Disponible</div>
                </button>
            `;
        }).join('');
    }

    function buildVehiculosList(asignacionId, vehiculos) {
        if (!vehiculos.length) {
            return '<div class="assignment-empty">No hay vehículos compatibles disponibles</div>';
        }
        return vehiculos.map(v => `
            <button type="button" class="selection-card" data-role="vehiculo" data-id="${v.id}" data-asignacion="${asignacionId}">
                <div class="text-left">
                    <div class="selection-title">${v.tipo || 'Vehículo'} - ${v.placa || 'Sin placa'}</div>
                    <div class="selection-subtitle">Cap: ${formatCapacidad(v.capacidad)} · ${v.tipo_transporte?.nombre || 'Sin tipo'}</div>
                </div>
                <div class="selection-status text-success">Disponible</div>
            </button>
        `).join('');
    }

    function showFeedback(message, type = 'success') {
        const holder = document.getElementById('asignacionFeedback');
        if (!holder) return;
        holder.innerHTML = `<div class="alert alert-${type} mb-0">${message}</div>`;
        setTimeout(() => {
            if (holder.innerHTML.includes(message)) {
                holder.innerHTML = '';
            }
        }, 4000);
    }

    function attachSelectionHandlers(){
        cont.querySelectorAll('.selection-card').forEach(card => {
            card.addEventListener('click', () => {
                if (card.classList.contains('disabled')) return;
                const rol = card.dataset.role;
                const asignacionId = Number(card.dataset.asignacion);
                const entityId = Number(card.dataset.id);
                seleccionarElemento(rol, asignacionId, entityId);
            });
        });

        cont.querySelectorAll('.assignment-confirm-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const asignacionId = Number(btn.dataset.asignacion);
                confirmarAsignacion(asignacionId);
            });
        });
    }

    function seleccionarElemento(rol, asignacionId, entityId) {
        if (!['transportista', 'vehiculo'].includes(rol)) return;
        if (!state.selecciones[asignacionId]) {
            state.selecciones[asignacionId] = { transportistaId: null, vehiculoId: null };
        }
        if (rol === 'transportista') {
            state.selecciones[asignacionId].transportistaId = entityId;
        } else {
            state.selecciones[asignacionId].vehiculoId = entityId;
        }

        cont.querySelectorAll(`.selection-card[data-role="${rol}"][data-asignacion="${asignacionId}"]`).forEach(el => {
            el.classList.toggle('active', Number(el.dataset.id) === entityId);
        });
        actualizarEstadoBoton(asignacionId);
    }

    function puedeConfirmar(asignacionId) {
        const particion = (state.envio?.particiones || []).find(p => p.id_asignacion === asignacionId);
        if (!particion || (particion.id_transportista && particion.id_vehiculo)) {
            return false;
        }
        const seleccion = state.selecciones[asignacionId] || {};
        return Boolean(seleccion.transportistaId && seleccion.vehiculoId);
    }

    function actualizarEstadoBoton(asignacionId) {
        const btn = cont.querySelector(`.assignment-confirm-btn[data-asignacion="${asignacionId}"]`);
        if (!btn) return;
        btn.disabled = !puedeConfirmar(asignacionId);
    }

    function renderEnvio(envio){
        state.envio = envio;
        const particiones = Array.isArray(envio.particiones) ? envio.particiones : [];
        if (particiones.length === 0){
            cont.innerHTML = '<div class="text-muted">Este envío no tiene particiones.</div>';
            return;
        }
        const wrapper = document.createElement('div');
        particiones.forEach((p, idx) => {
            const card = document.createElement('div');
            card.className = 'card mb-4';
            const body = document.createElement('div');
            body.className = 'card-body';

            const row = document.createElement('div');
            row.className = 'row';

            const colLeft = document.createElement('div');
            colLeft.className = 'col-lg-6';
            colLeft.innerHTML = `
                <h5 class="mb-3 d-flex align-items-center justify-content-between">Partición ${idx+1} ${badgeFor(p.estado)}</h5>
                <h6>Transportista</h6>
                <p class="mb-1">Nombre: ${(p.transportista?.nombre || '—')} ${(p.transportista?.apellido || '')}</p>
                <p class="mb-1">Teléfono: ${p.transportista?.telefono || '—'}</p>
                <p class="mb-3">CI: ${p.transportista?.ci || '—'}</p>

                <h6>Vehículo</h6>
                <p class="mb-1">Placa: ${p.vehiculo?.placa || '—'}</p>
                <p class="mb-3">Tipo: ${p.vehiculo?.tipo || '—'}</p>

                <h6>Transporte</h6>
                <p class="mb-3">Tipo de transporte: ${p.tipoTransporte?.nombre || '—'}<br>Descripción: ${p.tipoTransporte?.descripcion || '—'}</p>

                <div class="timeline-item mb-3">
                    <span class="text-success">●</span>
                    <strong class="ml-1">Recogida:</strong> ${p.recogidaEntrega?.fecha_recogida || '—'} – ${p.recogidaEntrega?.hora_recogida || '—'}
                    <div class="mt-2 p-2 bg-light rounded">
                        <strong>Origen:</strong> ${envio.nombre_origen || '—'}<br>
                        ${Array.isArray(p.cargas) && p.cargas.length ? p.cargas.map(c => `
                            <div>• ${c.tipo} - ${c.variedad} (${Number(c.cantidad || 0)} uds, ${Number(c.peso || 0).toFixed(1)} kg, ${c.empaquetado || '—'})</div>
                        `).join('') : 'Sin productos'}<br>
                        Sin instrucciones
                    </div>
                </div>

                <div class="timeline-item">
                    <span class="text-muted">●</span>
                    <strong class="ml-1">Entrega:</strong> ${p.recogidaEntrega?.fecha_recogida || '—'} – ${p.recogidaEntrega?.hora_entrega || '—'}
                    <div class="mt-2 p-2 bg-light rounded">
                        <strong>Destino:</strong> ${envio.nombre_destino || '—'}<br>
                        ${Array.isArray(p.cargas) && p.cargas.length ? p.cargas.map(c => `
                            <div>• ${c.tipo} - ${c.variedad} (${Number(c.cantidad || 0)} uds, ${Number(c.peso || 0).toFixed(1)} kg, ${c.empaquetado || '—'})</div>
                        `).join('') : 'Sin productos'}<br>
                        Sin instrucciones
                    </div>
                </div>
            `;

            const colRight = document.createElement('div');
            colRight.className = 'col-lg-6';
            const mapId = `map-${idx}`;
            colRight.innerHTML = `<div id="${mapId}" style="height: 420px;" class="rounded border"></div>`;

            row.appendChild(colLeft);
            row.appendChild(colRight);
            body.appendChild(row);

            const alreadyAssigned = Boolean(p.id_transportista && p.id_vehiculo);
            const estadoNoEditable = ['En curso', 'Finalizado', 'Entregado', 'Completado'].includes((p.estado || '').trim());
            const assignmentSection = document.createElement('div');
            assignmentSection.className = 'mt-4';

            if (alreadyAssigned || estadoNoEditable) {
                assignmentSection.innerHTML = `
                    <div class="assignment-alert">
                        <span class="badge badge-${alreadyAssigned ? 'success' : 'secondary'} mb-0">
                            ${alreadyAssigned ? 'Asignado' : 'No disponible'}
                        </span>
                        <div>
                            <strong>${alreadyAssigned ? 'Recursos confirmados.' : 'No se puede asignar.'}</strong>
                            <div class="text-muted small">
                                ${alreadyAssigned
                                    ? 'Esta partición ya tiene transportista y vehículo.'
                                    : `Estado actual: ${p.estado || 'Desconocido'}. Las asignaciones solo se permiten cuando está pendiente.`}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                const vehiculosCompatibles = vehiculosCompatiblesPara(p);
                assignmentSection.innerHTML = `
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="assignment-section">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-0">Seleccionar transportista</h6>
                                        <small class="text-muted">Elige uno disponible para la ruta</small>
                                    </div>
                                    <span class="badge badge-light text-secondary">${state.transportistas.length}</span>
                                </div>
                                <div class="selection-list" data-role="transportista" data-asignacion="${p.id_asignacion}">
                                    ${buildTransportistasList(p.id_asignacion)}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="assignment-section">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-0">Seleccionar vehículo</h6>
                                        <small class="text-muted">Debe cumplir el tipo requerido</small>
                                    </div>
                                    <span class="badge badge-light text-secondary">${vehiculosCompatibles.length}</span>
                                </div>
                                <div class="selection-list" data-role="vehiculo" data-asignacion="${p.id_asignacion}">
                                    ${buildVehiculosList(p.id_asignacion, vehiculosCompatibles)}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-success btn-lg assignment-confirm-btn w-100 mt-2" data-asignacion="${p.id_asignacion}" disabled>Confirmar asignación</button>
                        </div>
                    </div>
                `;
            }

            body.appendChild(assignmentSection);
            card.appendChild(body);
            wrapper.appendChild(card);

            setTimeout(() => {
                const map = L.map(mapId).setView([envio.coordenadas_origen?.lat || -17.7833, envio.coordenadas_origen?.lng || -63.1833], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
                const o = [envio.coordenadas_origen?.lat, envio.coordenadas_origen?.lng];
                const d = [envio.coordenadas_destino?.lat, envio.coordenadas_destino?.lng];
                if (o[0] && o[1]) L.marker(o).addTo(map).bindPopup('Origen');
                if (d[0] && d[1]) L.marker(d).addTo(map).bindPopup('Destino');
                try {
                    if (envio.rutaGeoJSON){
                        const gj = JSON.parse(envio.rutaGeoJSON);
                        const layer = L.geoJSON(gj, { style: { color: '#007bff', weight: 4 } }).addTo(map);
                        map.fitBounds(layer.getBounds(), { padding: [20,20] });
                    } else if (o[0] && d[0]){
                        const line = L.polyline([o, d], { color:'#007bff', weight:4 }).addTo(map);
                        map.fitBounds(line.getBounds(), { padding: [20,20] });
                    }
                } catch {}
            }, 0);
        });
        cont.innerHTML = '';
        const feedback = document.createElement('div');
        feedback.id = 'asignacionFeedback';
        feedback.className = 'mb-3';
        cont.appendChild(feedback);
        cont.appendChild(wrapper);
        attachSelectionHandlers();
    }

    async function obtenerEnvio(){
        const res = await fetch(`${window.location.origin}/api/envios/${envioId}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok){
            if (res.status === 401){ localStorage.removeItem('authToken'); window.location.href = '/login'; }
            throw new Error('No se pudo cargar el envío');
        }
        return res.json();
    }

    async function obtenerCatalogos(){
        const headers = { 'Authorization': `Bearer ${token}` };
        const [transportistasRes, vehiculosRes] = await Promise.all([
            fetch(`${window.location.origin}/api/transportistas/disponibles`, { headers }),
            fetch(`${window.location.origin}/api/vehiculos`, { headers })
        ]);

        if (transportistasRes.status === 401 || vehiculosRes.status === 401) {
            localStorage.removeItem('authToken'); window.location.href = '/login';
            return { transportistas: [], vehiculos: [] };
        }

        if (!transportistasRes.ok) {
            throw new Error('No se pudo cargar la lista de transportistas');
        }
        if (!vehiculosRes.ok) {
            throw new Error('No se pudo cargar la lista de vehículos');
        }

        const transportistas = await transportistasRes.json();
        const vehiculosRaw = await vehiculosRes.json();

        const vehiculos = Array.isArray(vehiculosRaw)
            ? vehiculosRaw.filter(v => (v.estado || '').toLowerCase() === 'disponible')
            : [];

        return {
            transportistas: Array.isArray(transportistas) ? transportistas : [],
            vehiculos
        };
    }

    async function recargarTodo(showLoader = false){
        if (showLoader) {
            cont.innerHTML = loaderHtml;
        }
        try {
            const [envio, catalogos] = await Promise.all([obtenerEnvio(), obtenerCatalogos()]);
            state.transportistas = catalogos.transportistas;
            state.vehiculos = catalogos.vehiculos;
            state.selecciones = {};
            renderEnvio(envio);
        } catch(e){
            cont.innerHTML = `<div class="text-danger">${e.message}</div>`;
        }
    }

    async function confirmarAsignacion(asignacionId){
        if (!puedeConfirmar(asignacionId)) return;
        const seleccion = state.selecciones[asignacionId];
        const btn = document.querySelector(`.assignment-confirm-btn[data-asignacion="${asignacionId}"]`);
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>Asignando...';
        }
        try{
            const res = await fetch(`${window.location.origin}/api/envios/asignacion/${asignacionId}/asignar`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_transportista: seleccion.transportistaId,
                    id_vehiculo: seleccion.vehiculoId
                })
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.error || 'No se pudo asignar la partición');
            }
            await recargarTodo();
            showFeedback('Asignación confirmada correctamente', 'success');
        } catch (error){
            showFeedback(error.message || 'Ocurrió un error al asignar', 'danger');
            if (btn && document.body.contains(btn)) {
                btn.disabled = false;
                btn.innerHTML = originalHtml || 'Confirmar asignación';
            }
        }
    }

    recargarTodo(true);
</script>
@endsection


