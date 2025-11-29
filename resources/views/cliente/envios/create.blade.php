@extends('layouts.cliente')

@section('page-title', 'Crear Nuevo Envío')

@section('page-content')
<div class="row">
    <div class="col-12">
        <!-- Paso / Progreso -->
        <div class="card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <div class="mb-1"><span class="badge badge-primary">1</span></div>
                        <div>Origen y Destino</div>
                        <small>Paso 1 de 3</small>
                    </div>
                    <div class="col">
                        <div class="mb-1"><span class="badge badge-secondary" id="step2-badge">2</span></div>
                        <div>Datos del envío</div>
                        <small>Paso 2 de 3</small>
                    </div>
                    <div class="col">
                        <div class="mb-1"><span class="badge badge-secondary" id="step3-badge">3</span></div>
                        <div>Confirmación</div>
                        <small>Paso 3 de 3</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- PASO 1: Mapa -->
        <!-- Rutas Guardadas -->
        <div class="card" id="rutasGuardadas">
            <div class="card-header"><h3 class="card-title">Rutas Guardadas</h3></div>
            <div class="card-body">
                <p class="mb-2">Selecciona una ruta guardada o marca nuevos puntos en el mapa</p>
                <div class="form-row">
                    <div class="form-group col-md-9">
                        <select id="selRutaGuardada" class="form-control">
                            <option value="">Seleccionar ruta guardada</option>
                            <option value="ruta1" data-id-direccion="1">Ruta 1: Ferbo → Estación Argentina</option>
                            <option value="ruta2" data-id-direccion="2">Ruta 2: 4to anillo Norte → Parque Urbano</option>
                        </select>
                        <input type="hidden" id="idDireccionSeleccionada" value="">
                    </div>
                    <div class="form-group col-md-3 text-right">
                        <button type="button" id="btnGuardarDireccion" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-save mr-1"></i> Guardar dirección del mapa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PASO 1: Mapa -->
        <div class="card" id="step1">
            <div class="card-header"><h3 class="card-title">Ubicación en el Mapa</h3></div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                    <input type="text" class="form-control" placeholder="Haz clic en el mapa para marcar el origen" readonly id="hintInput">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btnReset">Reiniciar</button>
                    </div>
                </div>

                <div id="mapNuevoEnvio" style="height: 420px;" class="rounded border"></div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <span class="text-success"><i class="fas fa-circle"></i></span> <strong>Origen Actual</strong>
                        <input type="text" class="form-control mt-2" id="origenNombre" placeholder="Se mostrará al seleccionar dirección" readonly>
                    </div>
                    <div class="col-md-6">
                        <span class="text-danger"><i class="fas fa-circle"></i></span> <strong>Destino Actual</strong>
                        <input type="text" class="form-control mt-2" id="destinoNombre" placeholder="Se mostrará al seleccionar dirección" readonly>
                    </div>
                </div>
            </div>
            <div class="card-footer d-none"></div>
        </div>

        <!-- PASO 2: Datos del envío -->
        <div class="card d-none" id="step2">
            <div class="card-header d-flex justify-content-between align-items-center"><h3 class="card-title mb-0">Partición 1</h3><button type="button" class="btn btn-tool text-danger d-none btn-eliminar-particion" title="Eliminar partición"><i class="fas fa-times"></i></button></div>
            <div class="card-body particion-template">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Fecha de envío</label>
                        <input type="date" class="form-control js-fecha" value="">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Hora de recogida</label>
                        <input type="time" class="form-control js-hora-recogida" value="">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Hora de entrega estimada</label>
                        <input type="time" class="form-control js-hora-entrega" value="">
                    </div>
                </div>

                <h5 class="mt-3">Productos a transportar</h5>
                <div class="productosContainer">
                <div class="producto-item border rounded p-3 mb-3">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Categoría</label>
                        <select class="form-control js-tipo"><option value="">Selecciona</option><option value="Verduras">Verduras</option><option value="Frutas">Frutas</option></select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Producto</label>
                        <select class="form-control js-variedad"><option value="">Selecciona</option><option value="Zanahorias">Zanahorias</option><option value="Tomates">Tomates</option><option value="Manzanas">Manzanas</option></select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Cantidad</label>
                        <input type="number" class="form-control js-cantidad" value="">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Peso volumétrico</label>
                        <input type="number" class="form-control js-peso" value="">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Tipo de empaque</label>
                        <select class="form-control js-empaquetado"><option value="">Selecciona</option><option value="Bolsa plástica">Bolsa plástica</option><option value="Cajas">Cajas</option><option value="Cajón">Cajón</option></select>
                    </div>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-producto"><i class="fas fa-times mr-1"></i> Eliminar</button>
                </div>
                </div>
                </div>
                <div class="mb-2 only-step2 d-none">
                    <button class="btn btn-outline-primary btn-agregar-producto"><i class="fas fa-plus mr-1"></i> Agregar producto</button>
                </div>
                </div>

                <!-- Lista de productos agregados -->
                <div id="productosAgregados" class="mt-2"><div class="alert alert-light border">Sin productos agregados</div></div>

                <h5 class="mt-3">Tipo de transporte requerido</h5>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Tipo de transporte</label>
                        <select class="form-control js-id-tipo-transporte">
                            <option value="">Selecciona...</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Contenedor para particiones extra dentro del paso 2 -->
            <div id="particionesContainer"></div>

            <div class="card-footer d-none"></div>
        </div>

        <!-- Contenedor donde se agregarán particiones clonadas -->
        <template id="tplParticion">
            <div class="card particion-item">
                <div class="card-header d-flex justify-content-between align-items-center"><h3 class="card-title mb-0">Partición</h3><button type="button" class="btn btn-tool text-danger btn-eliminar-particion" title="Eliminar partición"><i class="fas fa-times"></i></button></div>
                <div class="card-body"></div>
                <div class="card-footer text-right"></div>
            </div>
        </template>

        <button class="btn btn-outline-secondary btn-block d-none only-step2" id="btnAgregarParticion">
            <i class="fas fa-layer-group mr-1"></i> Agregar otra partición
        </button>

        <!-- PASO 3: Confirmación -->
        <div class="card d-none mx-auto text-center" id="step3" style="max-width: 800px; font-size: 1.1rem;">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 1.6rem;">Confirmar Solicitud de Envío</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="callout callout-success">
                            <h5 style="font-size: 1.2rem;">Origen</h5>
                            <p id="origenResumen">—</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger">
                            <h5 style="font-size: 1.2rem;">Destino</h5>
                            <p id="destinoResumen">—</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col"><strong>Particiones</strong><div id="resumenParticiones">0</div></div>
                            <div class="col"><strong>Peso Total</strong><div id="resumenPeso">0.0 kg</div></div>
                            <div class="col"><strong>Productos</strong><div id="resumenProductos">0</div></div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body" id="resumenParticionesDetalle">
                        <div class="text-muted">Aún no has agregado particiones.</div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-none"></div>
        </div>
    </div>

    <!-- Barra global de navegación del wizard (sobre el copyright) -->
    <div class="mt-3 d-flex justify-content-between align-items-center" id="wizardActions">
        <button class="btn btn-secondary d-none" id="btnAnterior"><i class="fas fa-arrow-left mr-1"></i> Anterior</button>
        <div class="ml-auto">
            <button class="btn btn-success d-none" id="btnEnviar"><i class="fas fa-paper-plane mr-1"></i> Enviar Solicitud</button>
            <button class="btn btn-primary" id="btnSiguiente">Continuar <i class="fas fa-arrow-right ml-1"></i></button>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
// Prevenir ejecución múltiple
if (!window.__envioCreateInitialized) {
    window.__envioCreateInitialized = true;
    
(function() {
    'use strict';
    
    // --- Auth ---
    // Normalizar token por si está guardado como string con comillas
    const _rawToken = localStorage.getItem('authToken');
    const token = _rawToken ? _rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) {
        window.location.href = '/login';
        return;
    }
    // OpenRouteService
    const ORS_API_KEY = '5b3ce3597851110001cf6248dbff311ed4d34185911c2eb9e6c50080';

    // --- Wizard simple ---
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const step2Badge = document.getElementById('step2-badge');
    const step3Badge = document.getElementById('step3-badge');
    const rutasCard = document.getElementById('rutasGuardadas');
    const btnAgregarParticion = document.getElementById('btnAgregarParticion');
    const cardParticion2 = document.getElementById('particion2');

    // Navegación usando barra global
    let currentStep = 1;
    const btnAnterior = document.getElementById('btnAnterior');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const btnEnviar = document.getElementById('btnEnviar');
    function renderStep(){
        if (currentStep === 1){
            step1.classList.remove('d-none');
            step2.classList.add('d-none');
            step3.classList.add('d-none');
            rutasCard.classList.remove('d-none');
            document.querySelectorAll('.only-step2').forEach(el=> el.classList.add('d-none'));
            step2Badge.classList.replace('badge-primary','badge-secondary');
            step3Badge.classList.replace('badge-primary','badge-secondary');
            btnAnterior.classList.add('d-none');
            btnSiguiente.classList.remove('d-none');
            btnEnviar.classList.add('d-none');
        } else if (currentStep === 2){
            step1.classList.add('d-none');
            step2.classList.remove('d-none');
            step3.classList.add('d-none');
            rutasCard.classList.add('d-none');
            document.querySelectorAll('.only-step2').forEach(el=> el.classList.remove('d-none'));
            step2Badge.classList.replace('badge-secondary','badge-primary');
            step3Badge.classList.replace('badge-primary','badge-secondary');
            btnAnterior.classList.remove('d-none');
            btnSiguiente.classList.remove('d-none');
            btnEnviar.classList.add('d-none');
            // Asegurar selects de tipo transporte poblados al entrar al paso 2
            cargarTiposTransporte();
        } else {
            step1.classList.add('d-none');
            step2.classList.add('d-none');
            step3.classList.remove('d-none');
            rutasCard.classList.add('d-none');
            document.querySelectorAll('.only-step2').forEach(el=> el.classList.add('d-none'));
            step3Badge.classList.replace('badge-secondary','badge-primary');
            document.getElementById('origenResumen').innerText = origenNombre.value || '—';
            document.getElementById('destinoResumen').innerText = destinoNombre.value || '—';
            actualizarResumen();
            btnAnterior.classList.remove('d-none');
            btnSiguiente.classList.add('d-none');
            btnEnviar.classList.remove('d-none');
        }
    }
    // Listeners globales
    btnSiguiente.addEventListener('click', ()=>{
        if (currentStep === 1) currentStep = 2; else if (currentStep === 2) currentStep = 3;
        renderStep();
    });
    btnAnterior.addEventListener('click', ()=>{
        if (currentStep === 2) currentStep = 1; else if (currentStep === 3) currentStep = 2;
        renderStep();
    });
    renderStep();
    actualizarResumen();

    // --- GESTOR DE MAPA Y RUTAS (REFACTORIZADO) ---
    const MapManager = {
        map: null,
        apiKey: '5b3ce3597851110001cf6248dbff311ed4d34185911c2eb9e6c50080',
        markers: { origin: null, destination: null },
        routeLayer: null,
        lastGeoJSON: null,

        init: function() {
            // Limpiar contenedor si existe instancia previa
            const container = document.getElementById('mapNuevoEnvio');
            if (container && container._leaflet_id) container._leaflet_id = null;

            this.map = L.map('mapNuevoEnvio').setView([-17.7833, -63.1833], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(this.map);

            this.map.on('click', (e) => this.handleMapClick(e));
            
            // Botones
            document.getElementById('btnReset').onclick = () => this.reset();
            document.getElementById('selRutaGuardada').onchange = (e) => this.loadRouteFromId(e.target.value);
            document.getElementById('btnGuardarDireccion').onclick = () => this.saveRoute();
            
            this.loadSavedRoutesList();
        },

        handleMapClick: async function(e) {
            if (!this.markers.origin) {
                this.setMarker('origin', e.latlng);
                this.updateUI('Selecciona el destino en el mapa');
            } else if (!this.markers.destination) {
                this.setMarker('destination', e.latlng);
                await this.calculateRoute();
            }
        },

        setMarker: function(type, latlng) {
            const color = type === 'origin' ? 'text-success' : 'text-danger';
            const icon = L.divIcon({
                className: color,
                html: '<i class="fas fa-map-marker-alt fa-2x"></i>',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            });

            if (this.markers[type]) this.map.removeLayer(this.markers[type]);
            
            this.markers[type] = L.marker(latlng, {icon: icon}).addTo(this.map);
            
            // Actualizar inputs visuales
            const inputId = type === 'origin' ? 'origenNombre' : 'destinoNombre';
            document.getElementById(inputId).value = `${latlng.lat.toFixed(5)}, ${latlng.lng.toFixed(5)}`;
        },

        reset: function() {
            if (this.markers.origin) this.map.removeLayer(this.markers.origin);
            if (this.markers.destination) this.map.removeLayer(this.markers.destination);
            if (this.routeLayer) this.map.removeLayer(this.routeLayer);
            
            this.markers = { origin: null, destination: null };
            this.routeLayer = null;
            this.lastGeoJSON = null;
            
            document.getElementById('origenNombre').value = '';
            document.getElementById('destinoNombre').value = '';
            document.getElementById('idDireccionSeleccionada').value = '';
            document.getElementById('selRutaGuardada').value = '';
            this.updateUI('Haz clic en el mapa para marcar el origen');
        },

        calculateRoute: async function() {
            if (!this.markers.origin || !this.markers.destination) return;

            this.updateUI('Calculando ruta...');
            const start = this.markers.origin.getLatLng();
            const end = this.markers.destination.getLatLng();

            try {
                const response = await fetch('https://api.openrouteservice.org/v2/directions/driving-car/geojson', {
                    method: 'POST',
                    headers: {
                        'Authorization': this.apiKey,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        coordinates: [[start.lng, start.lat], [end.lng, end.lat]]
                    })
                });

                if (!response.ok) throw new Error('Error en servicio de rutas');
                
                const geoJSON = await response.json();
                this.drawRoute(geoJSON);
                this.updateUI('Ruta calculada correctamente');

            } catch (e) {
                console.error(e);
                this.drawFallbackRoute(start, end);
                this.updateUI('Ruta calculada (modo simple)');
            }
        },

        drawRoute: function(geoJSON) {
            if (this.routeLayer) this.map.removeLayer(this.routeLayer);
            
            // Validar que el GeoJSON tenga coordenadas válidas
            if (!geoJSON || !geoJSON.features || !geoJSON.features[0].geometry || !geoJSON.features[0].geometry.coordinates) {
                console.warn('GeoJSON inválido, usando fallback');
                if (this.markers.origin && this.markers.destination) {
                    this.drawFallbackRoute(this.markers.origin.getLatLng(), this.markers.destination.getLatLng());
                }
                return;
            }

            try {
                this.routeLayer = L.geoJSON(geoJSON, {
                    style: { color: '#3b82f6', weight: 5, opacity: 0.8 }
                }).addTo(this.map);
                
                this.map.fitBounds(this.routeLayer.getBounds(), { padding: [50, 50] });
                this.lastGeoJSON = JSON.stringify(geoJSON);
            } catch (e) {
                console.error('Error dibujando GeoJSON:', e);
                if (this.markers.origin && this.markers.destination) {
                    this.drawFallbackRoute(this.markers.origin.getLatLng(), this.markers.destination.getLatLng());
                }
            }
        },

        drawFallbackRoute: function(start, end) {
            if (this.routeLayer) this.map.removeLayer(this.routeLayer);
            
            // Asegurar números
            const sLat = parseFloat(start.lat), sLng = parseFloat(start.lng);
            const eLat = parseFloat(end.lat), eLng = parseFloat(end.lng);

            if (isNaN(sLat) || isNaN(sLng) || isNaN(eLat) || isNaN(eLng)) {
                console.error('Coordenadas inválidas para fallback:', start, end);
                return;
            }

            const line = [[sLat, sLng], [eLat, eLng]];
            
            try {
                this.routeLayer = L.polyline(line, {
                    color: '#ff6b6b', weight: 4, dashArray: '5, 10'
                }).addTo(this.map);
                
                this.map.fitBounds(this.routeLayer.getBounds(), { padding: [50, 50] });
                
                // Crear un GeoJSON simple para el fallback
                const simpleGeoJSON = {
                    type: "FeatureCollection",
                    features: [{
                        type: "Feature",
                        geometry: {
                            type: "LineString",
                            coordinates: [[sLng, sLat], [eLng, eLat]]
                        }
                    }]
                };
                this.lastGeoJSON = JSON.stringify(simpleGeoJSON);
            } catch (e) {
                console.error('Error dibujando fallback:', e);
            }
        },

        loadSavedRoutesList: async function() {
            const select = document.getElementById('selRutaGuardada');
            select.innerHTML = '<option value="">Cargando...</option>';
            
            try {
                const res = await fetch('/api/ubicaciones', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (!res.ok) throw new Error('Error cargando rutas');
                
                const rutas = await res.json();
                select.innerHTML = '<option value="">Seleccionar ruta guardada</option>';
                
                rutas.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r.id;
                    opt.textContent = `${r.nombreorigen || 'Origen'} → ${r.nombredestino || 'Destino'}`;
                    select.appendChild(opt);
                });
            } catch (e) {
                select.innerHTML = '<option value="">Error al cargar rutas</option>';
            }
        },

        loadRouteFromId: async function(id) {
            if (!id) { this.reset(); return; }
            
            try {
                const res = await fetch(`/api/ubicaciones/${id}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (!res.ok) throw new Error('Error obteniendo detalle de ruta');
                
                const data = await res.json();
                this.reset(); // Limpiar mapa antes de cargar
                
                // Setear ID
                document.getElementById('idDireccionSeleccionada').value = data.id;
                document.getElementById('origenNombre').value = data.nombreorigen || '';
                document.getElementById('destinoNombre').value = data.nombredestino || '';

                // Marcadores
                if (data.origen_lat && data.origen_lng) {
                    this.setMarker('origin', { lat: parseFloat(data.origen_lat), lng: parseFloat(data.origen_lng) });
                }
                if (data.destino_lat && data.destino_lng) {
                    this.setMarker('destination', { lat: parseFloat(data.destino_lat), lng: parseFloat(data.destino_lng) });
                }

                // Ruta
                if (data.rutageojson) {
                    try {
                        const geoJSON = JSON.parse(data.rutageojson);
                        this.drawRoute(geoJSON);
                    } catch (e) {
                        // Si falla el GeoJSON, intentar recalcular o fallback si hay puntos
                        if (this.markers.origin && this.markers.destination) {
                            this.drawFallbackRoute(this.markers.origin.getLatLng(), this.markers.destination.getLatLng());
                        }
                    }
                }
                this.updateUI('Ruta cargada');
            } catch (e) {
                console.error(e);
                alert('No se pudo cargar la ruta seleccionada');
            }
        },

        saveRoute: async function() {
            if (!this.markers.origin || !this.markers.destination) {
                alert('Debes tener un origen y destino marcados');
                return;
            }
            
            const nombreOrigen = document.getElementById('origenNombre').value;
            const nombreDestino = document.getElementById('destinoNombre').value;
            const o = this.markers.origin.getLatLng();
            const d = this.markers.destination.getLatLng();

            const payload = {
                nombreOrigen: nombreOrigen,
                origen_lat: o.lat,
                origen_lng: o.lng,
                nombreDestino: nombreDestino,
                destino_lat: d.lat,
                destino_lng: d.lng,
                rutaGeoJSON: this.lastGeoJSON
            };

            try {
                const res = await fetch('/api/ubicaciones', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                if (!res.ok) throw new Error('Error guardando ruta');
                
                const nueva = await res.json();
                await this.loadSavedRoutesList();
                document.getElementById('selRutaGuardada').value = nueva.id;
                document.getElementById('idDireccionSeleccionada').value = nueva.id;
                alert('Ruta guardada correctamente');
            } catch (e) {
                alert('Error al guardar la ruta');
            }
        },

        updateUI: function(msg) {
            document.getElementById('hintInput').value = msg;
        }
    };

    // Iniciar mapa
    MapManager.init();

    // Exponer MapManager globalmente si es necesario para otras funciones (como enviarEnvioCompleto)
    window.MapManager = MapManager;

    // --- Productos y particiones ---
    // Delegación: agregar producto dentro de la partición correspondiente
    document.addEventListener('click', (e)=>{
        // Agregar producto
        const addBtn = e.target.closest('.btn-agregar-producto');
        if (addBtn){
            const body = addBtn.closest('.particion-template');
            const productosContainer = body.querySelector('.productosContainer');
            const first = productosContainer.querySelector('.producto-item');
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input').forEach(i=> i.value = '');
            clone.querySelectorAll('select').forEach(s=> s.value = '');
            productosContainer.appendChild(clone);
            actualizarResumen();
        }
        // Eliminar producto
        if (e.target.closest('.btn-eliminar-producto')){
            const cont = e.target.closest('.productosContainer');
            const items = cont.querySelectorAll('.producto-item');
            if (items.length > 1) e.target.closest('.producto-item').remove();
            actualizarResumen();
        }
        // Eliminar partición
        const delPartBtn = e.target.closest('.btn-eliminar-particion');
        if (delPartBtn){
            const card = delPartBtn.closest('.particion-item');
            if (card) card.remove();
            actualizarResumen();
        }
    });

    // Agregar partición: clona el cuerpo de la partición template (igual a la 1)
    btnAgregarParticion.addEventListener('click', ()=>{
        const tpl = document.getElementById('tplParticion').content.cloneNode(true);
        // clonar el contenido de la partición base
        const baseBody = document.querySelector('#step2 .particion-template');
        const newBody = baseBody.cloneNode(true);
        // limpiar datos
        newBody.querySelectorAll('input').forEach(i=> i.value = '');
        newBody.querySelectorAll('select').forEach(s=> s.value = '');
        // asegurar sección de tipo de transporte en la partición clonada
        if (!newBody.querySelector('.js-id-tipo-transporte')){
            const titulo = document.createElement('h5');
            titulo.className = 'mt-3';
            titulo.textContent = 'Tipo de transporte requerido';
            const wrapper = document.createElement('div');
            wrapper.className = 'form-row';
            const col = document.createElement('div');
            col.className = 'form-group col-md-6';
            col.innerHTML = `
                <label>Tipo de transporte</label>
                <select class="form-control js-id-tipo-transporte">
                    <option value="">Selecciona...</option>
                </select>`;
            wrapper.appendChild(col);
            newBody.appendChild(titulo);
            newBody.appendChild(wrapper);
        }
        tpl.querySelector('.card-body').replaceWith(newBody);
        document.getElementById('particionesContainer').appendChild(tpl);
        // Poblar tipos para los selects recién creados
        cargarTiposTransporte();
        // Renumerar encabezados
        document.querySelectorAll('#particionesContainer .particion-item').forEach((el, idx)=>{
            const h = el.querySelector('.card-title');
            if (h) h.textContent = `Partición ${idx+2}`;
        });
        // mostrar botón eliminar en particiones clonadas (no en la primera)
        const lastCard = document.getElementById('particionesContainer').lastElementChild;
        if (lastCard){
            const hdrBtn = lastCard.querySelector('.btn-eliminar-particion');
            if (hdrBtn) hdrBtn.classList.remove('d-none');
        }
        actualizarResumen();
    });

    // Actualizar resumen ante cambios en campos de particiones
    document.addEventListener('input', (e)=>{
        if (e.target.closest('.particion-template')){
            actualizarResumen();
        }
    });
    document.addEventListener('change', (e)=>{
        if (e.target.closest('.particion-template')){
            actualizarResumen();
        }
    });

    // --- Envío al endpoint /api/envios/completo (cliente) ---
    function buildParticionFromCard(cardEl){
        const fecha = cardEl.querySelector('.js-fecha')?.value || '';
        const horaRecogida = cardEl.querySelector('.js-hora-recogida')?.value || '';
        const horaEntrega = cardEl.querySelector('.js-hora-entrega')?.value || '';
        // Buscar el select de tipo de transporte con tolerancia
        let idTipoSelect = cardEl.querySelector('select.js-id-tipo-transporte');
        if (!idTipoSelect) {
            // fallback: primer select con opciones cargadas por Tipotransporte
            const candidates = cardEl.querySelectorAll('select');
            idTipoSelect = Array.from(candidates).find(s => Array.from(s.options).some(o => /^\d+$/.test(o.value)));
        }
        if (!idTipoSelect) {
            // último fallback: tomar el primer select global con la clase
            idTipoSelect = document.querySelector('select.js-id-tipo-transporte');
        }
        const cargas = [];
        cardEl.querySelectorAll('.productosContainer .producto-item').forEach(item=>{
            const tipo = item.querySelector('.js-tipo')?.value || '';
            const variedad = item.querySelector('.js-variedad')?.value || '';
            const cantidad = parseInt(item.querySelector('.js-cantidad')?.value || '0', 10);
            const empaquetado = item.querySelector('.js-empaquetado')?.value || '';
            const peso = parseFloat(item.querySelector('.js-peso')?.value || '0');
            if (tipo && variedad && cantidad > 0 && empaquetado){
                cargas.push({ tipo, variedad, cantidad, empaquetado, peso: isNaN(peso)?0:peso });
            }
        });
        let id_tipo_transporte = null;
        let tipo_transporte_nombre = '';
        if (idTipoSelect) {
            const raw = (idTipoSelect.value || '').trim();
            const parsed = Number(raw);
            if (!Number.isNaN(parsed) && parsed > 0) {
                id_tipo_transporte = parsed;
                tipo_transporte_nombre = idTipoSelect.options[idTipoSelect.selectedIndex]?.textContent?.trim() || '';
            }
        }
        // Normalizar horas por si el navegador entrega formato 12h con sufijos
        const toHHMMSS = (str) => {
            if (!str) return '';
            let s = String(str).trim().toLowerCase();
            // "11:11" -> "11:11:00"
            if (/^\d{1,2}:\d{2}$/.test(s)) return s + ':00';
            // "11:11 am" / "11:11 a. m." -> convertir
            const am = s.includes('am') || s.includes('a. m.');
            const pm = s.includes('pm') || s.includes('p. m.');
            s = s.replace(/[^\d:]/g,'');
            const [hh, mm] = s.split(':').map(n=>parseInt(n||'0',10));
            let H = hh;
            if (pm && hh < 12) H = hh + 12;
            if (am && hh === 12) H = 0;
            const HH = String(H).padStart(2,'0');
            const MM = String(isNaN(mm)?0:mm).padStart(2,'0');
            return `${HH}:${MM}:00`;
        };
        const recogidaEntrega = {
            fecha_recogida: fecha,
            hora_recogida: toHHMMSS(horaRecogida),
            hora_entrega: toHHMMSS(horaEntrega),
            instrucciones_recogida: null,
            instrucciones_entrega: null
        };
        return { cargas, recogidaEntrega, id_tipo_transporte, tipo_transporte_nombre };
    }

    function obtenerParticiones(validar = false){
        const items = [];
        const base = document.querySelector('#step2 .particion-template');
        if (base) {
            items.push({ card: base, data: buildParticionFromCard(base) });
        }
        document.querySelectorAll('#particionesContainer .particion-item .particion-template, #particionesContainer .particion-item .card-body.particion-template').forEach(el=>{
            items.push({ card: el, data: buildParticionFromCard(el) });
        });

        if (validar){
            for (let i = 0; i < items.length; i++){
                const { card, data: p } = items[i];
                if (!p || !Array.isArray(p.cargas) || p.cargas.length === 0){
                    return { error: `La partición ${i+1} no tiene productos válidos.` };
                }
                if (!p.recogidaEntrega || !p.recogidaEntrega.fecha_recogida || !p.recogidaEntrega.hora_recogida || !p.recogidaEntrega.hora_entrega){
                    return { error: `La partición ${i+1} requiere fecha y horas de recogida y entrega.` };
                }
                const idVal = p.id_tipo_transporte;
                const isValidId = (typeof idVal === 'number' && idVal > 0) || (/^\d+$/.test(String(idVal)) && Number(idVal) > 0);
                if (!isValidId){
                    const sel = card ? card.querySelector('.js-id-tipo-transporte') : null;
                    if (sel) sel.classList.add('is-invalid');
                    return { error: `Selecciona el tipo de transporte en la partición ${i+1}.` };
                }
                p.id_tipo_transporte = Number(idVal);
            }
        }

        return { particiones: items.map(it => it.data) };
    }

    function actualizarResumen(particionesOverride = null){
        const data = particionesOverride ?? obtenerParticiones(false).particiones ?? [];
        const totalParticiones = data.length;
        let totalProductos = 0;
        let totalPeso = 0;
        data.forEach(p=>{
            (p.cargas || []).forEach(c=>{
                totalProductos += 1;
                totalPeso += Number(c.peso) || 0;
            });
        });
        const elPart = document.getElementById('resumenParticiones');
        const elProd = document.getElementById('resumenProductos');
        const elPeso = document.getElementById('resumenPeso');
        const elDetalle = document.getElementById('resumenParticionesDetalle');
        if (elPart) elPart.innerText = String(totalParticiones);
        if (elProd) elProd.innerText = String(totalProductos);
        if (elPeso) elPeso.innerText = `${totalPeso.toFixed(1)} kg`;
        if (elDetalle){
            if (data.length === 0){
                elDetalle.innerHTML = '<div class="text-muted">Aún no has agregado particiones.</div>';
            } else {
                elDetalle.innerHTML = data.map((p, idx) => {
                    const productos = (p.cargas || []).map(c => `
                        <li>${c.tipo || '—'} - ${c.variedad || '—'} · ${Number(c.cantidad || 0)} uds · ${Number(c.peso || 0).toFixed(1)} kg · ${c.empaquetado || '—'}</li>
                    `).join('') || '<li class="text-muted">Sin productos</li>';
                    return `
                        <div class="mb-3">
                            <strong>Partición ${idx+1}</strong>
                            <ul class="mb-2">${productos}</ul>
                            <div class="small text-muted">
                                Recogida: ${p.recogidaEntrega?.fecha_recogida || '—'} ${p.recogidaEntrega?.hora_recogida || ''}
                                · Entrega: ${p.recogidaEntrega?.hora_entrega || ''}
                                · Tipo transporte: ${p.tipo_transporte_nombre || p.id_tipo_transporte || '—'}
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }
    }

    // Cargar tipos de transporte y poblar selects en todas las particiones
    async function cargarTiposTransporte(){
        if (!token) { window.location.replace('/login'); return; }
        try{
            const res = await fetch(`${window.location.origin}/api/tipotransporte`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (res.status === 401) {
                localStorage.removeItem('authToken');
                localStorage.removeItem('usuario');
                window.location.replace('/login');
                return;
            }
            if (!res.ok) {
                console.warn('No se pudieron cargar los tipos de transporte');
                return;
            }
            const tipos = await res.json();
            const rellenar = (select) => {
                if (!tipos.length) {
                    select.innerHTML = '<option value=\"\">No hay tipos disponibles</option>';
                    return;
                }
                select.innerHTML = '<option value=\"\">Selecciona...</option>';
                tipos.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = String(t.id);
                    opt.textContent = t.nombre;
                    select.appendChild(opt);
                });
                // Si no hay valor seleccionado, escoger el primero válido
                if (!select.value) {
                    const firstValid = Array.from(select.options).find(o => o.value && /^\d+$/.test(o.value));
                    if (firstValid) select.value = firstValid.value;
                }
            };
            document.querySelectorAll('.js-id-tipo-transporte').forEach(rellenar);
        } catch(e){ console.warn(e); }
    }
    cargarTiposTransporte();

    async function enviarEnvioCompleto(){
        let id_direccion = document.getElementById('idDireccionSeleccionada').value;
        
        // Si no hay id_direccion pero el usuario marcó en el mapa, crear la dirección al vuelo
        if (!id_direccion && MapManager.markers.origin && MapManager.markers.destination){
            try{
                const o = MapManager.markers.origin.getLatLng();
                const d = MapManager.markers.destination.getLatLng();
                const nombreOrigen = document.getElementById('origenNombre').value;
                const nombreDestino = document.getElementById('destinoNombre').value;

                const rutaGeoJSON = MapManager.lastGeoJSON || JSON.stringify({
                    type:'FeatureCollection',
                    features:[{type:'Feature',geometry:{type:'LineString',coordinates:[[o.lng,o.lat],[d.lng,d.lat]]},properties:{}}]
                });
                
                const resDir = await fetch(`${window.location.origin}/api/ubicaciones`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nombreOrigen: nombreOrigen || null,
                        origen_lng: o.lng,
                        origen_lat: o.lat,
                        nombreDestino: nombreDestino || null,
                        destino_lng: d.lng,
                        destino_lat: d.lat,
                        rutaGeoJSON
                    })
                });
                
                if (!resDir.ok){
                    const e = await resDir.json().catch(()=>({}));
                    throw new Error(e.error || 'No se pudo crear la dirección desde el mapa');
                }
                
                const creada = await resDir.json();
                id_direccion = String(creada.id);
                
                // reflejar en UI
                document.getElementById('idDireccionSeleccionada').value = id_direccion;
                await MapManager.loadSavedRoutesList();
                document.getElementById('selRutaGuardada').value = id_direccion;
            } catch(err){
                alert(err.message);
                return;
            }
        }
        if (!id_direccion){
            alert('Selecciona o marca en el mapa una ruta antes de continuar.');
            return;
        }
        if (!token){
            alert('Sesión expirada. Vuelve a iniciar sesión.');
            window.location.href = '/login';
            return;
        }
        const { particiones, error } = obtenerParticiones(true);
        if (error){
            alert(error);
            return;
        }
        // Debug particiones
        try { console.debug('DEBUG particiones antes de enviar:', JSON.stringify(particiones)); } catch{}
        actualizarResumen(particiones);
        const payload = {
            id_direccion: parseInt(id_direccion,10),
            particiones: particiones.map(p => ({
                cargas: p.cargas,
                recogidaEntrega: p.recogidaEntrega,
                id_tipo_transporte: p.id_tipo_transporte
            }))
        };
        const btn = document.getElementById('btnEnviar');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Enviando...';
        try{
            // Debug mínimo en consola para verificar selects detectados
            try {
                const selects = document.querySelectorAll('.js-id-tipo-transporte');
                console.debug('DEBUG tipos de transporte:', {
                    cantidadSelects: selects.length,
                    valores: Array.from(selects).map(s => s.value)
                });
            } catch {}
            // Redundar el token también en query para sortear proxies/redirecciones que limpian headers
            const url = `${window.location.origin}/api/envios/completo?token=${encodeURIComponent(token)}`;
            const res = await fetch(url, {
                method:'POST',
                headers: {
                    'Content-Type':'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Auth-Token': token
                },
                body: JSON.stringify(payload)
            });
            if (!res.ok){
                const err = await res.json().catch(()=>({}));
                throw new Error(err.error || err.message || 'Error al crear el envío');
            }
            // éxito
            window.location.href = "{{ route('envios.index') }}";
        } catch (e){
            alert(e.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Enviar Solicitud';
        }
    }

    document.getElementById('btnEnviar').addEventListener('click', (e)=>{
        e.preventDefault();
        enviarEnvioCompleto();
    });
})();

} // Fin de window.__envioCreateInitialized
</script>
@endpush