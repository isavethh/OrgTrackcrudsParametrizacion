@extends('cliente.layouts.app')

@section('title', 'Seguimiento del envío - OrgTrack')
@section('page-title', 'Seguimiento del envío')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('envios.index') }}">Envíos</a></li>
    <li class="breadcrumb-item active">Detalle</li>
@endsection

@section('content')
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
    // Auth
    const _rawToken = localStorage.getItem('authToken');
    const token = _rawToken ? _rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) { window.location.href = '/login'; }

    const envioId = {{ (int)($id ?? 0) }};
    const cont = document.getElementById('detalleEnvio');

    function badgeFor(estado){
        const map = { 'En curso':'badge-info', 'Pendiente':'badge-warning', 'Asignado':'badge-primary', 'Entregado':'badge-success', 'Finalizado':'badge-secondary' };
        const cls = map[estado] || 'badge-light';
        return `<span class="badge ${cls} ml-1">${estado}</span>`;
    }

    function renderEnvio(envio){
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
                <h5 class="mb-3">Partición ${idx+1} - Estado: ${badgeFor(p.estado)}</h5>
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
            card.appendChild(body);
            wrapper.appendChild(card);

            // Render mapa
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
        cont.appendChild(wrapper);
    }

    async function cargarEnvio(){
        try{
            const res = await fetch(`${window.location.origin}/api/envios/${envioId}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (!res.ok){
                if (res.status === 401){ localStorage.removeItem('authToken'); window.location.href = '/login'; return; }
                throw new Error('No se pudo cargar el envío');
            }
            const envio = await res.json();
            renderEnvio(envio);
        } catch(e){
            cont.innerHTML = `<div class="text-danger">${e.message}</div>`;
        }
    }
    cargarEnvio();
</script>
@endsection


