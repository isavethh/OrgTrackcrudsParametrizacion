@extends('layouts.cliente')

@section('page-title', 'Crear Nuevo Envío')

@section('page-content')
<div class="row">
    <div class="col-12">
        <!-- Wizard Progress -->
        <div class="card mb-3">
            <div class="card-body p-3">
                <div class="row text-center">
                    <div class="col step-indicator active" id="ind-step1">
                        <div class="mb-1"><span class="badge badge-primary badge-pill" style="font-size: 1.2em;">1</span></div>
                        <div class="font-weight-bold">Ubicación</div>
                        <small class="text-muted">Origen y Destino</small>
                    </div>
                    <div class="col step-indicator" id="ind-step2">
                        <div class="mb-1"><span class="badge badge-secondary badge-pill" style="font-size: 1.2em;">2</span></div>
                        <div class="font-weight-bold">Detalles</div>
                        <small class="text-muted">Cargas y Transporte</small>
                    </div>
                    <div class="col step-indicator" id="ind-step3">
                        <div class="mb-1"><span class="badge badge-secondary badge-pill" style="font-size: 1.2em;">3</span></div>
                        <div class="font-weight-bold">Confirmación</div>
                        <small class="text-muted">Resumen y Envío</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 1: UBICACIÓN -->
        <div id="step1" class="wizard-step">
            <div class="row">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title"><i class="fas fa-bookmark mr-2"></i>Mis Direcciones</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Seleccionar ruta guardada:</label>
                                <select id="selRutaGuardada" class="form-control select2">
                                    <option value="">-- Nueva Ruta --</option>
                                </select>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="text-success"><i class="fas fa-map-marker-alt mr-1"></i> Origen</label>
                                <input type="text" class="form-control bg-light" id="txtOrigen" readonly placeholder="Selecciona en el mapa">
                            </div>
                            <div class="form-group">
                                <label class="text-danger"><i class="fas fa-map-marker-alt mr-1"></i> Destino</label>
                                <input type="text" class="form-control bg-light" id="txtDestino" readonly placeholder="Selecciona en el mapa">
                            </div>
                            <input type="hidden" id="idDireccionSeleccionada">
                            
                            <div class="alert alert-info mt-3 text-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                Si seleccionas una ruta guardada, el mapa se centrará automáticamente. Si marcas puntos nuevos, se creará una nueva dirección.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Mapa Interactivo</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetMap">
                                    <i class="fas fa-eraser mr-1"></i> Limpiar Mapa
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="mapNuevoEnvio" style="height: 500px; width: 100%; position: relative; z-index: 1;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: DETALLES (PARTICIONES) -->
        <div id="step2" class="wizard-step d-none">
            <div id="particionesContainer">
                <!-- Las particiones se generarán aquí dinámicamente -->
            </div>
            
            <div class="text-center mt-4 mb-5">
                <button type="button" class="btn btn-outline-primary btn-lg dashed-border" id="btnAgregarParticion">
                    <i class="fas fa-plus-circle mr-2"></i> Agregar otro camión / partición
                </button>
            </div>
        </div>

        <!-- STEP 3: CONFIRMACIÓN -->
        <div id="step3" class="wizard-step d-none">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Resumen de la Solicitud</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success"><i class="fas fa-map-marker-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Origen</span>
                                    <span class="info-box-number" id="resumenOrigen">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-danger"><i class="fas fa-map-marker-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Destino</span>
                                    <span class="info-box-number" id="resumenDestino">--</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="text-secondary border-bottom pb-2 mb-3">Detalle de Envíos (Particiones)</h5>
                    <div id="resumenParticiones" class="accordion">
                        <!-- Resumen dinámico -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="card mt-3">
            <div class="card-body d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="btnPrev" disabled>
                    <i class="fas fa-arrow-left mr-2"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" id="btnNext">
                    Siguiente <i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="button" class="btn btn-success d-none" id="btnFinish">
                    <i class="fas fa-check mr-2"></i> Confirmar y Crear Envío
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATES -->

<!-- Template: Partición -->
<template id="tplParticion">
    <div class="card card-outline card-primary mb-4 particion-item" data-index="{index}">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-truck mr-2"></i> Envío / Camión #<span class="particion-num">1</span></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool btn-collapse" data-toggle="collapse" data-target="#collapsePart{index}">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool text-danger btn-remove-particion" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body collapse show" id="collapsePart{index}">
            <!-- Tipo Transporte -->
            <div class="form-group">
                <label>Tipo de Transporte Requerido <span class="text-danger">*</span></label>
                <select class="form-control js-tipo-transporte" required>
                    <option value="">Seleccione...</option>
                    <!-- Se llena via JS -->
                </select>
            </div>

            <!-- Recogida y Entrega -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Fecha Recogida <span class="text-danger">*</span></label>
                        <input type="date" class="form-control js-fecha-recogida" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Hora Recogida <span class="text-danger">*</span></label>
                        <input type="time" class="form-control js-hora-recogida" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Hora Entrega Estimada <span class="text-danger">*</span></label>
                        <input type="time" class="form-control js-hora-entrega" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Instrucciones de Recogida</label>
                        <textarea class="form-control js-instr-recogida" rows="2" placeholder="Ej: Puerta trasera, preguntar por..."></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Instrucciones de Entrega</label>
                        <textarea class="form-control js-instr-entrega" rows="2" placeholder="Ej: Dejar en recepción..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Cargas -->
            <h5 class="mt-4 border-bottom pb-2">Cargas / Productos</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Variedad</th>
                            <th>Empaque</th>
                            <th width="100">Cant.</th>
                            <th width="100">Peso (kg)</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody class="cargas-container">
                        <!-- Cargas items here -->
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success btn-add-carga">
                <i class="fas fa-plus mr-1"></i> Agregar Producto
            </button>
        </div>
    </div>
</template>

<!-- Template: Carga Row -->
<template id="tplCarga">
    <tr class="carga-item">
        <td>
            <select class="form-control form-control-sm js-carga-tipo" required>
                <option value="">Seleccione...</option>
                <option value="Frutas">Frutas</option>
                <option value="Verduras">Verduras</option>
                <option value="Hortalizas">Hortalizas</option>
                <option value="Granos">Granos</option>
                <option value="Otros">Otros</option>
            </select>
        </td>
        <td><input type="text" class="form-control form-control-sm js-carga-variedad" placeholder="Ej: Manzanas" required></td>
        <td>
            <select class="form-control form-control-sm js-carga-empaque" required>
                <option value="">Seleccione...</option>
                <option value="Cajas de madera">Cajas de madera</option>
                <option value="Cajas de cartón">Cajas de cartón</option>
                <option value="Sacos">Sacos</option>
                <option value="Bolsas">Bolsas</option>
                <option value="Granel">Granel</option>
                <option value="Pallets">Pallets</option>
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm js-carga-cantidad" min="1" required></td>
        <td><input type="number" class="form-control form-control-sm js-carga-peso" min="0.1" step="0.1" required></td>
        <td class="text-center">
            <button type="button" class="btn btn-xs btn-danger btn-remove-carga"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* INLINE LEAFLET CSS FALLBACK */
    .leaflet-pane, .leaflet-tile, .leaflet-marker-icon, .leaflet-marker-shadow, .leaflet-tile-container, .leaflet-pane > svg, .leaflet-pane > canvas, .leaflet-zoom-box, .leaflet-image-layer, .leaflet-layer { position: absolute; left: 0; top: 0; }
    .leaflet-container { overflow: hidden; }
    .leaflet-tile, .leaflet-marker-icon, .leaflet-marker-shadow { -webkit-user-select: none; -moz-user-select: none; user-select: none; -webkit-user-drag: none; }
    .leaflet-tile::selection { background: transparent; }
    .leaflet-safari .leaflet-tile { image-rendering: -webkit-optimize-contrast; }
    .leaflet-safari .leaflet-tile-container { width: 1600px; height: 1600px; -webkit-transform-origin: 0 0; }
    .leaflet-marker-icon, .leaflet-marker-shadow { display: block; }
    .leaflet-container .leaflet-overlay-pane svg { max-width: none !important; max-height: none !important; }
    .leaflet-container .leaflet-marker-pane img, .leaflet-container .leaflet-shadow-pane img, .leaflet-container .leaflet-tile-pane img, .leaflet-container img.leaflet-image-layer, .leaflet-container .leaflet-tile { max-width: none !important; max-height: none !important; width: auto; padding: 0; }
    .leaflet-container img.leaflet-tile { mix-blend-mode: plus-lighter; }
    .leaflet-container.leaflet-touch-zoom { -ms-touch-action: pan-x pan-y; touch-action: pan-x pan-y; }
    .leaflet-container.leaflet-touch-drag { -ms-touch-action: pinch-zoom; touch-action: none; touch-action: pinch-zoom; }
    .leaflet-container.leaflet-touch-drag.leaflet-touch-zoom { -ms-touch-action: none; touch-action: none; }
    .leaflet-container { -webkit-tap-highlight-color: transparent; }
    .leaflet-container a { -webkit-tap-highlight-color: rgba(51, 181, 229, 0.4); }
    .leaflet-tile { filter: inherit; visibility: hidden; }
    .leaflet-tile-loaded { visibility: inherit; }
    .leaflet-zoom-box { width: 0; height: 0; -moz-box-sizing: border-box; box-sizing: border-box; z-index: 800; }
    .leaflet-overlay-pane svg { -moz-user-select: none; }
    .leaflet-pane { z-index: 400; }
    .leaflet-tile-pane { z-index: 200; }
    .leaflet-overlay-pane { z-index: 400; }
    .leaflet-shadow-pane { z-index: 500; }
    .leaflet-marker-pane { z-index: 600; }
    .leaflet-tooltip-pane { z-index: 650; }
    .leaflet-popup-pane { z-index: 700; }
    .leaflet-map-pane canvas { z-index: 100; }
    .leaflet-map-pane svg { z-index: 200; }
    .leaflet-vml-shape { width: 1px; height: 1px; }
    .lvml { behavior: url(#default#VML); display: inline-block; position: absolute; }
    .leaflet-control { position: relative; z-index: 800; pointer-events: visiblePainted; pointer-events: auto; }
    .leaflet-top, .leaflet-bottom { position: absolute; z-index: 1000; pointer-events: none; }
    .leaflet-top { top: 0; }
    .leaflet-right { right: 0; }
    .leaflet-bottom { bottom: 0; }
    .leaflet-left { left: 0; }
    .leaflet-control { float: left; clear: both; }
    .leaflet-right .leaflet-control { float: right; }
    .leaflet-top .leaflet-control { margin-top: 10px; }
    .leaflet-bottom .leaflet-control { margin-bottom: 10px; }
    .leaflet-left .leaflet-control { margin-left: 10px; }
    .leaflet-right .leaflet-control { margin-right: 10px; }
    .leaflet-fade-anim .leaflet-popup { opacity: 0; -webkit-transition: opacity 0.2s linear; -moz-transition: opacity 0.2s linear; transition: opacity 0.2s linear; }
    .leaflet-fade-anim .leaflet-map-pane .leaflet-popup { opacity: 1; }
    .leaflet-zoom-animated { -webkit-transform-origin: 0 0; -ms-transform-origin: 0 0; transform-origin: 0 0; }
    svg.leaflet-zoom-animated { will-change: transform; }
    .leaflet-zoom-anim .leaflet-zoom-animated { -webkit-transition: -webkit-transform 0.25s cubic-bezier(0,0,0.25,1); -moz-transition: -moz-transform 0.25s cubic-bezier(0,0,0.25,1); transition: transform 0.25s cubic-bezier(0,0,0.25,1); }
    .leaflet-zoom-anim .leaflet-tile, .leaflet-pan-anim .leaflet-tile { -webkit-transition: none; -moz-transition: none; transition: none; }
    .leaflet-zoom-anim .leaflet-zoom-hide { visibility: hidden; }
    .leaflet-interactive { cursor: pointer; }
    .leaflet-grab { cursor: -webkit-grab; cursor: -moz-grab; cursor: grab; }
    .leaflet-crosshair, .leaflet-crosshair .leaflet-interactive { cursor: crosshair; }
    .leaflet-popup-pane, .leaflet-control { cursor: auto; }
    .leaflet-dragging .leaflet-grab, .leaflet-dragging .leaflet-grab .leaflet-interactive, .leaflet-dragging .leaflet-marker-draggable { cursor: move; cursor: -webkit-grabbing; cursor: -moz-grabbing; cursor: grabbing; }
    .leaflet-marker-icon, .leaflet-marker-shadow, .leaflet-image-layer, .leaflet-pane > svg path, .leaflet-tile-container { pointer-events: none; }
    .leaflet-marker-icon.leaflet-interactive, .leaflet-image-layer.leaflet-interactive, .leaflet-pane > svg path.leaflet-interactive, svg.leaflet-image-layer.leaflet-interactive path { pointer-events: visiblePainted; pointer-events: auto; }
    .leaflet-container { background: #ddd; outline-offset: 1px; }
    .leaflet-container a { color: #0078A8; }
    .leaflet-zoom-box { border: 2px dotted #38f; background: rgba(255,255,255,0.5); }
    .leaflet-container { font-family: "Helvetica Neue", Arial, Helvetica, sans-serif; font-size: 12px; font-size: 0.75rem; line-height: 1.5; }
    
    .dashed-border {
        border-style: dashed !important;
        border-width: 2px !important;
    }
    .step-indicator {
        opacity: 0.5;
        transition: all 0.3s;
    }
    .step-indicator.active {
        opacity: 1;
        transform: scale(1.05);
    }
    /* Cursores para el mapa */
    .leaflet-container { cursor: grab; }
    .leaflet-container:active { cursor: grabbing; }
</style>
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Evitar doble ejecución del script (soluciona duplicidad de particiones)
    if (window.createEnvioScriptLoaded) return;
    window.createEnvioScriptLoaded = true;

    // --- CONFIGURACIÓN ---
    const token = localStorage.getItem('authToken')?.replace(/^"+|"+$/g, '');
    if (!token) { window.location.href = '/login'; return; }

    // --- ESTADO GLOBAL ---
    const state = {
        currentStep: 1,
        tiposTransporte: [],
        map: null,
        markers: { origin: null, destination: null },
        routeLayer: null,
        lastGeoJSON: null
    };

    // --- ELEMENTOS DOM ---
    const steps = {
        1: document.getElementById('step1'),
        2: document.getElementById('step2'),
        3: document.getElementById('step3')
    };
    const indicators = {
        1: document.getElementById('ind-step1'),
        2: document.getElementById('ind-step2'),
        3: document.getElementById('ind-step3')
    };
    const btns = {
        prev: document.getElementById('btnPrev'),
        next: document.getElementById('btnNext'),
        finish: document.getElementById('btnFinish')
    };

    // --- VARIABLES GLOBALES PARA PARTICIONES ---
    const container = document.getElementById('particionesContainer');
    const tplParticion = document.getElementById('tplParticion');
    const tplCarga = document.getElementById('tplCarga');
    let partitionCounter = 0;

    // --- INICIALIZACIÓN ---
    initMap();
    loadSavedRoutes();
    loadTiposTransporte();
    addPartition(); // Agregar primera partición por defecto

    // --- NAVEGACIÓN WIZARD ---
    btns.next.addEventListener('click', () => {
        if (validateStep(state.currentStep)) {
            goToStep(state.currentStep + 1);
        }
    });

    btns.prev.addEventListener('click', () => {
        goToStep(state.currentStep - 1);
    });

    btns.finish.addEventListener('click', submitForm);

    function goToStep(step) {
        // Ocultar todos
        Object.values(steps).forEach(el => el.classList.add('d-none'));
        Object.values(indicators).forEach(el => {
            el.classList.remove('active');
            el.querySelector('.badge').classList.replace('badge-primary', 'badge-secondary');
        });

        // Mostrar actual
        steps[step].classList.remove('d-none');
        indicators[step].classList.add('active');
        indicators[step].querySelector('.badge').classList.replace('badge-secondary', 'badge-primary');

        state.currentStep = step;

        // Actualizar botones
        btns.prev.disabled = step === 1;
        if (step === 3) {
            btns.next.classList.add('d-none');
            btns.finish.classList.remove('d-none');
            renderSummary();
        } else {
            btns.next.classList.remove('d-none');
            btns.finish.classList.add('d-none');
        }

        // Fix mapa al volver al paso 1
        if (step === 1 && state.map) {
            setTimeout(() => state.map.invalidateSize(), 200);
        }
    }

    function validateStep(step) {
        if (step === 1) {
            const idDir = document.getElementById('idDireccionSeleccionada').value;
            const hasMarkers = state.markers.origin && state.markers.destination;
            
            if (!idDir && !hasMarkers) {
                alert('Por favor selecciona una ruta guardada o marca origen y destino en el mapa.');
                return false;
            }
            return true;
        }
        if (step === 2) {
            // Validar que haya al menos una partición
            const parts = document.querySelectorAll('.particion-item');
            if (parts.length === 0) {
                alert('Debes agregar al menos un envío/camión.');
                return false;
            }
            
            let isValid = true;
            // Validar campos requeridos HTML5
            const inputs = steps[2].querySelectorAll('input[required], select[required]');
            inputs.forEach(input => {
                if (!input.value) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                alert('Por favor completa todos los campos obligatorios en los detalles del envío.');
                return false;
            }
            
            // Validar que cada partición tenga cargas
            let emptyLoads = false;
            parts.forEach((part, idx) => {
                if (part.querySelectorAll('.carga-item').length === 0) {
                    emptyLoads = true;
                }
            });
            
            if (emptyLoads) {
                alert('Cada envío debe tener al menos un producto/carga.');
                return false;
            }

            return true;
        }
        return true;
    }

    // --- MAPA (LEAFLET) ---
    function initMap() {
        if (state.map) return; // Evitar reinicialización

        const mapContainer = document.getElementById('mapNuevoEnvio');
        if (!mapContainer) return;
        
        // Limpieza defensiva por si acaso Leaflet dejó basura
        if (mapContainer._leaflet_id) mapContainer._leaflet_id = null;

        state.map = L.map('mapNuevoEnvio', { 
            preferCanvas: false,
            dragging: true,
            tap: false // Fix para dispositivos híbridos/Windows
        }).setView([-17.7833, -63.1833], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(state.map);

        state.map.on('click', handleMapClick);
        document.getElementById('btnResetMap').addEventListener('click', resetMap);
        
        // Force resize
        setTimeout(() => state.map.invalidateSize(), 500);
    }

    function handleMapClick(e) {
        // Si ya hay una dirección guardada seleccionada, limpiar selección
        if (document.getElementById('selRutaGuardada').value) {
            document.getElementById('selRutaGuardada').value = "";
            document.getElementById('idDireccionSeleccionada').value = "";
        }

        if (!state.markers.origin) {
            setMarker('origin', e.latlng);
        } else if (!state.markers.destination) {
            setMarker('destination', e.latlng);
            drawRoute(state.markers.origin.getLatLng(), state.markers.destination.getLatLng());
        }
    }

    function setMarker(type, latlng) {
        const color = type === 'origin' ? 'text-success' : 'text-danger';
        const icon = L.divIcon({
            className: color,
            html: '<i class="fas fa-map-marker-alt fa-2x"></i>',
            iconSize: [25, 41],
            iconAnchor: [12, 41]
        });

        if (state.markers[type]) state.map.removeLayer(state.markers[type]);
        state.markers[type] = L.marker(latlng, {icon: icon}).addTo(state.map);

        const inputId = type === 'origin' ? 'txtOrigen' : 'txtDestino';
        document.getElementById(inputId).value = `${latlng.lat.toFixed(5)}, ${latlng.lng.toFixed(5)}`;
    }

    async function drawRoute(start, end) {
        if (state.routeLayer) state.map.removeLayer(state.routeLayer);
        
        const apiKey = '5b3ce3597851110001cf6248dbff311ed4d34185911c2eb9e6c50080';
        const url = `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}&start=${start.lng},${start.lat}&end=${end.lng},${end.lat}`;

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error en servicio de rutas');
            
            const data = await response.json();

            if (data.features && data.features.length > 0) {
                const geoJSON = data.features[0];
                state.routeLayer = L.geoJSON(geoJSON, {
                    style: { color: '#3b82f6', weight: 5, opacity: 0.8 }
                }).addTo(state.map);
                
                state.map.fitBounds(state.routeLayer.getBounds(), { padding: [50, 50] });
                
                // Guardar GeoJSON completo (FeatureCollection para compatibilidad)
                state.lastGeoJSON = JSON.stringify({
                    type: "FeatureCollection",
                    features: [geoJSON]
                });
            } else {
                throw new Error('No se encontró ruta');
            }
        } catch (e) {
            console.warn('Falló OpenRouteService, usando línea recta:', e);
            // Fallback simple line
            const line = [[start.lat, start.lng], [end.lat, end.lng]];
            state.routeLayer = L.polyline(line, {color: 'red', weight: 4, dashArray: '10, 10'}).addTo(state.map);
            state.map.fitBounds(state.routeLayer.getBounds(), {padding: [50, 50]});
            
            // GeoJSON simple de respaldo
            state.lastGeoJSON = JSON.stringify({
                type: "FeatureCollection",
                features: [{
                    type: "Feature",
                    geometry: {
                        type: "LineString",
                        coordinates: [[start.lng, start.lat], [end.lng, end.lat]]
                    }
                }]
            });
        }
    }

    function resetMap() {
        if (state.markers.origin) state.map.removeLayer(state.markers.origin);
        if (state.markers.destination) state.map.removeLayer(state.markers.destination);
        if (state.routeLayer) state.map.removeLayer(state.routeLayer);
        state.markers = { origin: null, destination: null };
        state.routeLayer = null;
        state.lastGeoJSON = null;
        
        document.getElementById('txtOrigen').value = "";
        document.getElementById('txtDestino').value = "";
        document.getElementById('idDireccionSeleccionada').value = "";
        document.getElementById('selRutaGuardada').value = "";
    }

    // --- API DATA ---
    async function loadSavedRoutes() {
        try {
            const res = await fetch('/api/ubicaciones', { headers: { 'Authorization': `Bearer ${token}` } });
            const rutas = await res.json();
            const select = document.getElementById('selRutaGuardada');
            
            rutas.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = `${r.nombreorigen || 'Origen'} → ${r.nombredestino || 'Destino'}`;
                opt.dataset.json = JSON.stringify(r);
                select.appendChild(opt);
            });

            select.addEventListener('change', (e) => {
                const opt = e.target.selectedOptions[0];
                if (!opt.value) { resetMap(); return; }
                
                const data = JSON.parse(opt.dataset.json);
                document.getElementById('idDireccionSeleccionada').value = data.id;
                document.getElementById('txtOrigen').value = data.nombreorigen;
                document.getElementById('txtDestino').value = data.nombredestino;

                // Update map
                if (state.markers.origin) state.map.removeLayer(state.markers.origin);
                if (state.markers.destination) state.map.removeLayer(state.markers.destination);
                if (state.routeLayer) state.map.removeLayer(state.routeLayer);

                if (data.origen_lat && data.origen_lng) {
                    setMarker('origin', {lat: parseFloat(data.origen_lat), lng: parseFloat(data.origen_lng)});
                }
                if (data.destino_lat && data.destino_lng) {
                    setMarker('destination', {lat: parseFloat(data.destino_lat), lng: parseFloat(data.destino_lng)});
                }
                
                if (state.markers.origin && state.markers.destination) {
                    drawRoute(state.markers.origin.getLatLng(), state.markers.destination.getLatLng());
                }
            });
        } catch (e) { console.error(e); }
    }

    async function loadTiposTransporte() {
        try {
            const res = await fetch('/api/tipotransporte', { headers: { 'Authorization': `Bearer ${token}` } });
            state.tiposTransporte = await res.json();
            // Actualizar selects existentes
            document.querySelectorAll('.js-tipo-transporte').forEach(fillTransportSelect);
        } catch (e) { console.error(e); }
    }

    function fillTransportSelect(select) {
        if (select.options.length > 1) return; // Ya llenado
        state.tiposTransporte.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.nombre;
            select.appendChild(opt);
        });
    }

    // --- GESTIÓN DE PARTICIONES Y CARGAS ---
    // (Variables ya declaradas arriba)

    document.getElementById('btnAgregarParticion').addEventListener('click', addPartition);

    function addPartition() {
        partitionCounter++;
        const clone = tplParticion.content.cloneNode(true);
        const card = clone.querySelector('.particion-item');
        
        // IDs únicos para collapse
        card.dataset.index = partitionCounter;
        card.querySelector('.particion-num').textContent = partitionCounter;
        const collapseId = `collapsePart${partitionCounter}`;
        card.querySelector('.btn-collapse').dataset.target = `#${collapseId}`;
        card.querySelector('.card-body').id = collapseId;

        // Eventos
        card.querySelector('.btn-remove-particion').addEventListener('click', () => {
            if (document.querySelectorAll('.particion-item').length > 1) {
                card.remove();
                renumberPartitions();
            } else {
                alert('Debe haber al menos una partición.');
            }
        });

        card.querySelector('.btn-add-carga').addEventListener('click', () => addCarga(card.querySelector('.cargas-container')));

        // Llenar select transporte
        fillTransportSelect(card.querySelector('.js-tipo-transporte'));

        // Agregar carga inicial
        addCarga(card.querySelector('.cargas-container'));

        container.appendChild(card);
    }

    function addCarga(tbody) {
        const clone = tplCarga.content.cloneNode(true);
        clone.querySelector('.btn-remove-carga').addEventListener('click', (e) => {
            if (tbody.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
            }
        });
        tbody.appendChild(clone);
    }

    function renumberPartitions() {
        document.querySelectorAll('.particion-item').forEach((el, idx) => {
            el.querySelector('.particion-num').textContent = idx + 1;
        });
        partitionCounter = document.querySelectorAll('.particion-item').length;
    }

    // --- RESUMEN Y ENVÍO ---
    function getFormData() {
        const particiones = [];
        document.querySelectorAll('.particion-item').forEach(p => {
            const cargas = [];
            p.querySelectorAll('.carga-item').forEach(c => {
                cargas.push({
                    tipo: c.querySelector('.js-carga-tipo').value,
                    variedad: c.querySelector('.js-carga-variedad').value,
                    empaquetado: c.querySelector('.js-carga-empaque').value,
                    cantidad: parseInt(c.querySelector('.js-carga-cantidad').value),
                    peso: parseFloat(c.querySelector('.js-carga-peso').value)
                });
            });

            particiones.push({
                id_tipo_transporte: parseInt(p.querySelector('.js-tipo-transporte').value),
                recogidaEntrega: {
                    fecha_recogida: p.querySelector('.js-fecha-recogida').value,
                    hora_recogida: p.querySelector('.js-hora-recogida').value + ':00',
                    hora_entrega: p.querySelector('.js-hora-entrega').value + ':00',
                    instrucciones_recogida: p.querySelector('.js-instr-recogida').value,
                    instrucciones_entrega: p.querySelector('.js-instr-entrega').value
                },
                cargas: cargas
            });
        });

        return {
            id_direccion: document.getElementById('idDireccionSeleccionada').value || null,
            particiones: particiones,
            // Datos extra para crear dirección al vuelo si no existe ID
            temp_direccion: {
                nombreOrigen: document.getElementById('txtOrigen').value,
                nombreDestino: document.getElementById('txtDestino').value,
                origen_lat: state.markers.origin?.getLatLng().lat,
                origen_lng: state.markers.origin?.getLatLng().lng,
                destino_lat: state.markers.destination?.getLatLng().lat,
                destino_lng: state.markers.destination?.getLatLng().lng,
                rutaGeoJSON: state.lastGeoJSON
            }
        };
    }

    function renderSummary() {
        const data = getFormData();
        document.getElementById('resumenOrigen').textContent = data.temp_direccion.nombreOrigen || 'Coordenadas marcadas';
        document.getElementById('resumenDestino').textContent = data.temp_direccion.nombreDestino || 'Coordenadas marcadas';

        const container = document.getElementById('resumenParticiones');
        container.innerHTML = '';

        data.particiones.forEach((p, idx) => {
            const cargasHtml = p.cargas.map(c => `<li>${c.cantidad}x ${c.variedad} (${c.tipo}) - ${c.peso}kg</li>`).join('');
            
            const html = `
                <div class="card mb-2">
                    <div class="card-header bg-light p-2" id="heading${idx}">
                        <h5 class="mb-0">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold" type="button" data-toggle="collapse" data-target="#collapseRes${idx}">
                                Camión #${idx + 1} - ${p.recogidaEntrega.fecha_recogida}
                            </button>
                        </h5>
                    </div>
                    <div id="collapseRes${idx}" class="collapse show" data-parent="#resumenParticiones">
                        <div class="card-body p-3">
                            <p class="mb-1"><strong>Horario:</strong> ${p.recogidaEntrega.hora_recogida} - ${p.recogidaEntrega.hora_entrega}</p>
                            <p class="mb-2"><strong>Instrucciones:</strong> ${p.recogidaEntrega.instrucciones_recogida || 'Ninguna'}</p>
                            <strong>Cargas:</strong>
                            <ul class="pl-3 mb-0">${cargasHtml}</ul>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    async function submitForm() {
        const data = getFormData();
        const btn = document.getElementById('btnFinish');
        
        try {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            // 1. Si no hay ID de dirección, crearla primero
            if (!data.id_direccion) {
                const resDir = await fetch('/api/ubicaciones', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data.temp_direccion)
                });
                
                if (!resDir.ok) throw new Error('Error al guardar la ubicación');
                const nuevaDir = await resDir.json();
                data.id_direccion = nuevaDir.id;
            }

            // 2. Enviar Envío Completo
            const payload = {
                id_direccion: data.id_direccion,
                particiones: data.particiones
            };

            const res = await fetch('/api/envios/completo', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.error || 'Error al crear el envío');
            }

            alert('¡Envío creado exitosamente!');
            window.location.href = '/envios'; // Redirigir al index

        } catch (e) {
            alert(e.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i> Confirmar y Crear Envío';
        }
    }
});
</script>
@endpush
