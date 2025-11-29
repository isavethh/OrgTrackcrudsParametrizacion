@extends('layouts.adminlte')

@section('page-title', 'Crear Nuevo Envío (Admin)')

@section('page-content')
<div class="row">
    <div class="col-12">
        <!-- Wizard Progress -->
        <div class="card mb-3">
            <div class="card-body p-3">
                <div class="row text-center">
                    <div class="col step-indicator active" id="ind-step1">
                        <div class="mb-1"><span class="badge badge-primary badge-pill" style="font-size: 1.2em;">1</span></div>
                        <div class="font-weight-bold">Cliente</div>
                    </div>
                    <div class="col step-indicator" id="ind-step2">
                        <div class="mb-1"><span class="badge badge-secondary badge-pill" style="font-size: 1.2em;">2</span></div>
                        <div class="font-weight-bold">Ubicación</div>
                    </div>
                    <div class="col step-indicator" id="ind-step3">
                        <div class="mb-1"><span class="badge badge-secondary badge-pill" style="font-size: 1.2em;">3</span></div>
                        <div class="font-weight-bold">Detalles</div>
                    </div>
                    <div class="col step-indicator" id="ind-step4">
                        <div class="mb-1"><span class="badge badge-secondary badge-pill" style="font-size: 1.2em;">4</span></div>
                        <div class="font-weight-bold">Asignación</div>
                    </div>
                    <div class="col step-indicator" id="ind-step5">
                        <div class="mb-1"><span class="badge badge-secondary badge-pill" style="font-size: 1.2em;">5</span></div>
                        <div class="font-weight-bold">Confirmación</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 1: SELECCIÓN DE CLIENTE -->
        <div id="step1" class="wizard-step">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Seleccionar Cliente</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Cliente Solicitante <span class="text-danger">*</span></label>
                        <select id="selCliente" class="form-control select2" style="width: 100%;">
                            <option value="">Buscar cliente...</option>
                        </select>
                        <small class="form-text text-muted">Seleccione el usuario cliente para quien se creará el envío.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: UBICACIÓN -->
        <div id="step2" class="wizard-step d-none">
            <div class="row">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Dirección</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Seleccionar ruta guardada (del cliente):</label>
                                <select id="selRutaGuardada" class="form-control select2" style="width: 100%;">
                                    <option value="">-- Nueva Ruta --</option>
                                </select>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="text-success"><i class="fas fa-map-marker-alt mr-1"></i> Origen</label>
                                <input type="text" class="form-control mb-2" id="txtNombreOrigen" readonly placeholder="Dirección de origen...">
                                <input type="text" class="form-control bg-light form-control-sm" id="txtOrigen" readonly placeholder="Coordenadas">
                            </div>
                            <div class="form-group">
                                <label class="text-danger"><i class="fas fa-map-marker-alt mr-1"></i> Destino</label>
                                <input type="text" class="form-control mb-2" id="txtNombreDestino" readonly placeholder="Dirección de destino...">
                                <input type="text" class="form-control bg-light form-control-sm" id="txtDestino" readonly placeholder="Coordenadas">
                            </div>
                            <input type="hidden" id="idDireccionSeleccionada">
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
                            <div id="mapNuevoEnvio" style="height: 500px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: DETALLES (PARTICIONES) -->
        <div id="step3" class="wizard-step d-none">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> Defina las cargas y el tipo de transporte requerido. En el siguiente paso asignará los vehículos específicos.
            </div>
            <div id="particionesContainer">
                <!-- Las particiones se generarán aquí dinámicamente -->
            </div>
            
            <div class="text-center mt-4 mb-5">
                <button type="button" class="btn btn-outline-primary btn-lg dashed-border" id="btnAgregarParticion">
                    <i class="fas fa-plus-circle mr-2"></i> Agregar otro camión / partición
                </button>
            </div>
        </div>

        <!-- STEP 4: ASIGNACIÓN DE RECURSOS -->
        <div id="step4" class="wizard-step d-none">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i> Asigne un Transportista y un Vehículo para cada partición definida.
            </div>
            <div id="asignacionesContainer">
                <!-- Se genera dinámicamente basado en Step 3 -->
            </div>
        </div>

        <!-- STEP 5: CONFIRMACIÓN -->
        <div id="step5" class="wizard-step d-none">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Resumen Final</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-primary"><i class="fas fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Cliente</span>
                                    <span class="info-box-number" id="resumenCliente">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success"><i class="fas fa-map-marker-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Origen</span>
                                    <span class="info-box-number" id="resumenOrigen">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-danger"><i class="fas fa-map-marker-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Destino</span>
                                    <span class="info-box-number" id="resumenDestino">--</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="text-secondary border-bottom pb-2 mb-3">Detalle de Asignaciones</h5>
                    <div id="resumenFinal" class="accordion">
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

<!-- Template: Partición (Step 3) -->
<template id="tplParticion">
    <div class="card card-outline card-primary mb-4 particion-item" data-index="{index}">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-box mr-2"></i> Definición de Carga #<span class="particion-num">1</span></h3>
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
                        <textarea class="form-control js-instr-recogida" rows="2"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Instrucciones de Entrega</label>
                        <textarea class="form-control js-instr-entrega" rows="2"></textarea>
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
        <td><input type="text" class="form-control form-control-sm js-carga-variedad" required></td>
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

<!-- Template: Asignación (Step 4) -->
<template id="tplAsignacion">
    <div class="card card-outline card-warning mb-3 asignacion-item" data-index="{index}">
        <div class="card-header">
            <h3 class="card-title">Asignación para Partición #<span class="asig-num">1</span></h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Transportista <span class="text-danger">*</span></label>
                        <input type="hidden" class="js-transportista-id" required>
                        <div class="resource-list js-list-transportista p-2">
                            <!-- Lista generada por JS -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Vehículo <span class="text-danger">*</span></label>
                        <input type="hidden" class="js-vehiculo-id" required>
                        <div class="resource-list js-list-vehiculo p-2">
                            <!-- Lista generada por JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-muted small mt-2">
                <strong>Requerimiento:</strong> <span class="js-req-tipo"></span> | 
                <strong>Carga:</strong> <span class="js-req-carga"></span>
            </div>
        </div>
    </div>
</template>

@stop

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    .step-indicator { opacity: 0.5; transition: all 0.3s; }
    .step-indicator.active { opacity: 1; transform: scale(1.05); }
    .dashed-border { border-style: dashed !important; border-width: 2px !important; }
    /* Leaflet fixes */
    .leaflet-container { z-index: 1; }
    /* Select2 fixes */
    .select2-container .select2-selection--single { height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
    
    /* Resource Selection Lists */
    .resource-list {
        max-height: 350px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
    }
    .resource-card {
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }
    .resource-card:last-child { border-bottom: none; }
    .resource-card:hover { background-color: #f8f9fa; }
    .resource-card.selected {
        background-color: #e8f4ff;
        border-left: 4px solid #007bff;
    }
    .resource-card.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #fdfdfd;
    }
</style>
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- ESTADO GLOBAL ---
    const state = {
        currentStep: 1,
        map: null,
        markers: { origin: null, destination: null },
        routeLayer: null,
        lastGeoJSON: null,
        tiposTransporte: [],
        transportistas: [],
        vehiculos: [],
        particionesData: [] // Datos guardados del paso 3
    };

    // --- ELEMENTOS ---
    const steps = {
        1: document.getElementById('step1'),
        2: document.getElementById('step2'),
        3: document.getElementById('step3'),
        4: document.getElementById('step4'),
        5: document.getElementById('step5')
    };
    const indicators = {
        1: document.getElementById('ind-step1'),
        2: document.getElementById('ind-step2'),
        3: document.getElementById('ind-step3'),
        4: document.getElementById('ind-step4'),
        5: document.getElementById('ind-step5')
    };
    const btns = {
        prev: document.getElementById('btnPrev'),
        next: document.getElementById('btnNext'),
        finish: document.getElementById('btnFinish')
    };

    // --- LOGICA PASO 3: PARTICIONES ---
    let partitionCounter = 0;
    const tplParticion = document.getElementById('tplParticion');
    const tplCarga = document.getElementById('tplCarga');

    // --- INICIALIZACIÓN ---
    initSelect2();
    loadClients();
    loadTiposTransporte();
    loadResources(); // Cargar transportistas y vehículos
    
    // Only add if empty
    if (document.getElementById('particionesContainer').children.length === 0) {
        addPartition(); 
    }

    // --- NAVEGACIÓN ---
    btns.next.addEventListener('click', () => {
        if (validateStep(state.currentStep)) {
            if (state.currentStep === 3) prepareStep4();
            if (state.currentStep === 4) prepareStep5();
            goToStep(state.currentStep + 1);
        }
    });

    btns.prev.addEventListener('click', () => goToStep(state.currentStep - 1));
    btns.finish.addEventListener('click', submitForm);

    function goToStep(step) {
        Object.values(steps).forEach(el => el.classList.add('d-none'));
        Object.values(indicators).forEach(el => {
            el.classList.remove('active');
            el.querySelector('.badge').classList.replace('badge-primary', 'badge-secondary');
        });

        steps[step].classList.remove('d-none');
        indicators[step].classList.add('active');
        indicators[step].querySelector('.badge').classList.replace('badge-secondary', 'badge-primary');

        state.currentStep = step;

        btns.prev.disabled = step === 1;
        if (step === 5) {
            btns.next.classList.add('d-none');
            btns.finish.classList.remove('d-none');
        } else {
            btns.next.classList.remove('d-none');
            btns.finish.classList.add('d-none');
        }

        if (step === 2) {
            setTimeout(() => {
                if (!state.map) initMap();
                else state.map.invalidateSize();
            }, 200);
        }
    }

    function validateStep(step) {
        if (step === 1) {
            if (!$('#selCliente').val()) {
                alert('Debe seleccionar un cliente.');
                return false;
            }
            return true;
        }
        if (step === 2) {
            const idDir = document.getElementById('idDireccionSeleccionada').value;
            const hasMarkers = state.markers.origin && state.markers.destination;
            if (!idDir && !hasMarkers) {
                alert('Seleccione una ruta o marque origen y destino.');
                return false;
            }
            return true;
        }
        if (step === 3) {
            // Validar particiones (similar a cliente)
            const parts = document.querySelectorAll('.particion-item');
            if (parts.length === 0) return false;
            
            let isValid = true;
            steps[3].querySelectorAll('input[required], select[required]').forEach(input => {
                if (!input.value) { input.classList.add('is-invalid'); isValid = false; }
                else input.classList.remove('is-invalid');
            });
            if (!isValid) alert('Complete todos los campos requeridos.');
            return isValid;
        }
        if (step === 4) {
            let isValid = true;
            const selectedTransportistas = new Set();
            const selectedVehiculos = new Set();
            
            steps[4].querySelectorAll('input[type="hidden"][required]').forEach(input => {
                if (!input.value) { 
                    input.nextElementSibling.style.border = "1px solid red";
                    isValid = false; 
                } else {
                    input.nextElementSibling.style.border = "1px solid #ced4da";
                    
                    // Check for duplicates
                    if (input.classList.contains('js-transportista-id')) {
                        if (selectedTransportistas.has(input.value)) {
                            alert('No puede asignar el mismo transportista a múltiples particiones.');
                            isValid = false;
                        }
                        selectedTransportistas.add(input.value);
                    }
                    if (input.classList.contains('js-vehiculo-id')) {
                        if (selectedVehiculos.has(input.value)) {
                            alert('No puede asignar el mismo vehículo a múltiples particiones.');
                            isValid = false;
                        }
                        selectedVehiculos.add(input.value);
                    }
                }
            });
            if (!isValid && selectedTransportistas.size === 0 && selectedVehiculos.size === 0) alert('Debe asignar transportista y vehículo a todas las particiones.');
            return isValid;
        }
        return true;
    }

    // --- LOGICA PASO 1: CLIENTES ---
    async function loadClients() {
        try {
            const res = await fetch('/api/usuarios/clientes', {
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem('authToken') }
            });
            const clientes = await res.json();
            const select = $('#selCliente');
            clientes.forEach(c => {
                // La API devuelve directamente nombre, apellido, correo en el objeto raíz
                const nombre = c.nombre || 'Sin Nombre';
                const apellido = c.apellido || '';
                const email = c.correo || 'Sin Email';
                select.append(new Option(`${nombre} ${apellido} (${email})`, c.id));
            });
        } catch (e) { console.error(e); }
    }

    // --- LOGICA PASO 2: MAPA ---
    function initMap() {
        // Check if map is already initialized on the container
        var container = L.DomUtil.get('mapNuevoEnvio');
        if(container != null){
            if(container._leaflet_id != null){
                container._leaflet_id = null;
            }
        }

        // Ensure dragging is enabled and z-index is correct
        state.map = L.map('mapNuevoEnvio', {
            dragging: true,
            tap: false // Sometimes helps with mobile/touch issues
        }).setView([-17.7833, -63.1833], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(state.map);
        
        state.map.on('click', handleMapClick);
        
        // Fix for map interaction issues
        setTimeout(() => {
            state.map.invalidateSize();
        }, 500);

        document.getElementById('btnResetMap').addEventListener('click', resetMap);
    }

    function handleMapClick(e) {
        if ($('#selRutaGuardada').val()) {
            $('#selRutaGuardada').val('').trigger('change');
            document.getElementById('idDireccionSeleccionada').value = "";
        }
        if (!state.markers.origin) setMarker('origin', e.latlng);
        else if (!state.markers.destination) {
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
        
        // Update coordinates
        document.getElementById(type === 'origin' ? 'txtOrigen' : 'txtDestino').value = `${latlng.lat.toFixed(5)}, ${latlng.lng.toFixed(5)}`;
        
        // Fetch address name
        fetchAddressName(latlng.lat, latlng.lng, type === 'origin' ? 'txtNombreOrigen' : 'txtNombreDestino');
    }

    async function fetchAddressName(lat, lng, elementId) {
        const el = document.getElementById(elementId);
        el.value = "Buscando dirección...";
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await res.json();
            el.value = data.display_name || "Dirección desconocida";
        } catch (e) {
            el.value = "No se pudo obtener la dirección";
            console.error(e);
        }
    }

    async function drawRoute(start, end) {
        if (state.routeLayer) state.map.removeLayer(state.routeLayer);
        const apiKey = '5b3ce3597851110001cf6248dbff311ed4d34185911c2eb9e6c50080';
        try {
            const res = await fetch(`https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}&start=${start.lng},${start.lat}&end=${end.lng},${end.lat}`);
            const data = await res.json();
            if (data.features && data.features.length > 0) {
                state.routeLayer = L.geoJSON(data.features[0]).addTo(state.map);
                state.map.fitBounds(state.routeLayer.getBounds(), { padding: [50, 50] });
                state.lastGeoJSON = JSON.stringify({ type: "FeatureCollection", features: [data.features[0]] });
            }
        } catch (e) {
            // Fallback line
            state.routeLayer = L.polyline([[start.lat, start.lng], [end.lat, end.lng]], {color: 'red', dashArray: '10, 10'}).addTo(state.map);
            state.map.fitBounds(state.routeLayer.getBounds());
        }
    }

    function resetMap() {
        if (state.markers.origin) state.map.removeLayer(state.markers.origin);
        if (state.markers.destination) state.map.removeLayer(state.markers.destination);
        if (state.routeLayer) state.map.removeLayer(state.routeLayer);
        state.markers = { origin: null, destination: null };
        document.getElementById('txtOrigen').value = "";
        document.getElementById('txtDestino').value = "";
        document.getElementById('txtNombreOrigen').value = "";
        document.getElementById('txtNombreDestino').value = "";
    }

    // --- LOGICA PASO 3: PARTICIONES ---
    // let partitionCounter = 0; // MOVIDO ARRIBA
    // const tplParticion = document.getElementById('tplParticion'); // MOVIDO ARRIBA
    // const tplCarga = document.getElementById('tplCarga'); // MOVIDO ARRIBA

    // Prevent multiple bindings
    const btnAddPart = document.getElementById('btnAgregarParticion');
    const newBtnAddPart = btnAddPart.cloneNode(true);
    btnAddPart.parentNode.replaceChild(newBtnAddPart, btnAddPart);
    newBtnAddPart.addEventListener('click', addPartition);

    function addPartition() {
        partitionCounter++;
        const clone = tplParticion.content.cloneNode(true);
        const card = clone.querySelector('.particion-item');
        card.dataset.index = partitionCounter;
        card.querySelector('.particion-num').textContent = partitionCounter;
        const collapseId = `collapsePart${partitionCounter}`;
        card.querySelector('.btn-collapse').dataset.target = `#${collapseId}`;
        card.querySelector('.card-body').id = collapseId;

        card.querySelector('.btn-remove-particion').addEventListener('click', () => {
            if (document.querySelectorAll('.particion-item').length > 1) card.remove();
        });
        card.querySelector('.btn-add-carga').addEventListener('click', () => addCarga(card.querySelector('.cargas-container')));
        
        const select = card.querySelector('.js-tipo-transporte');
        state.tiposTransporte.forEach(t => select.appendChild(new Option(t.nombre, t.id)));
        
        addCarga(card.querySelector('.cargas-container'));
        document.getElementById('particionesContainer').appendChild(card);
    }

    function addCarga(tbody) {
        const clone = tplCarga.content.cloneNode(true);
        clone.querySelector('.btn-remove-carga').addEventListener('click', (e) => {
            if (tbody.querySelectorAll('tr').length > 1) e.target.closest('tr').remove();
        });
        tbody.appendChild(clone);
    }

    async function loadTiposTransporte() {
        const res = await fetch('/api/tipotransporte', { headers: { 'Authorization': 'Bearer ' + localStorage.getItem('authToken') } });
        state.tiposTransporte = await res.json();
        // Actualizar selects existentes
        document.querySelectorAll('.js-tipo-transporte').forEach(sel => {
            if (sel.options.length === 1) state.tiposTransporte.forEach(t => sel.appendChild(new Option(t.nombre, t.id)));
        });
    }

    // --- LOGICA PASO 4: ASIGNACIÓN ---
    async function loadResources() {
        const headers = { 'Authorization': 'Bearer ' + localStorage.getItem('authToken') };
        try {
            const [resT, resV] = await Promise.all([
                fetch('/api/transportistas', { headers }), // Traer TODOS, no solo disponibles
                fetch('/api/vehiculos', { headers })
            ]);
            state.transportistas = await resT.json();
            state.vehiculos = await resV.json();
        } catch (e) { console.error('Error cargando recursos:', e); }
    }

    function prepareStep4() {
        // Recopilar datos del paso 3
        state.particionesData = [];
        document.querySelectorAll('.particion-item').forEach((p, idx) => {
            const cargas = [];
            p.querySelectorAll('.carga-item').forEach(c => {
                cargas.push({
                    tipo: c.querySelector('.js-carga-tipo').value,
                    variedad: c.querySelector('.js-carga-variedad').value,
                    empaquetado: c.querySelector('.js-carga-empaque').value,
                    cantidad: c.querySelector('.js-carga-cantidad').value,
                    peso: c.querySelector('.js-carga-peso').value
                });
            });
            
            state.particionesData.push({
                index: idx + 1,
                id_tipo_transporte: p.querySelector('.js-tipo-transporte').value,
                tipo_transporte_txt: p.querySelector('.js-tipo-transporte').selectedOptions[0].text,
                recogidaEntrega: {
                    fecha_recogida: p.querySelector('.js-fecha-recogida').value,
                    hora_recogida: p.querySelector('.js-hora-recogida').value,
                    hora_entrega: p.querySelector('.js-hora-entrega').value,
                    instrucciones_recogida: p.querySelector('.js-instr-recogida').value,
                    instrucciones_entrega: p.querySelector('.js-instr-entrega').value
                },
                cargas: cargas
            });
        });

        // Generar tarjetas de asignación
        const container = document.getElementById('asignacionesContainer');
        container.innerHTML = '';
        const tpl = document.getElementById('tplAsignacion');

        state.particionesData.forEach(part => {
            const clone = tpl.content.cloneNode(true);
            const card = clone.querySelector('.asignacion-item');
            card.dataset.index = part.index;
            card.querySelector('.asig-num').textContent = part.index;
            
            // Mostrar resumen
            card.querySelector('.js-req-tipo').textContent = `Tipo: ${part.tipo_transporte_txt}`;
            card.querySelector('.js-req-carga').textContent = `${part.cargas.length} productos (Total: ${part.cargas.reduce((a,b)=>a+parseFloat(b.peso),0)} kg)`;

            // Contenedores de listas
            const listT = card.querySelector('.js-list-transportista');
            const listV = card.querySelector('.js-list-vehiculo');
            const inputT = card.querySelector('.js-transportista-id');
            const inputV = card.querySelector('.js-vehiculo-id');

            // Llenar Transportistas
            state.transportistas.forEach(t => {
                const nombre = t.nombre ? `${t.nombre} ${t.apellido}` : `ID: ${t.id}`;
                const estado = t.estado || 'Desconocido';
                const disponible = estado === 'Disponible';
                const badgeClass = disponible ? 'badge-success' : 'badge-danger';
                const ci = t.ci || 'N/A';
                const telefono = t.telefono || 'N/A';

                const item = document.createElement('div');
                item.className = `resource-card p-2 mb-1 rounded ${!disponible ? 'disabled' : ''}`;
                item.dataset.name = nombre;
                item.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="fas fa-user-tie fa-2x text-secondary"></i></div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">${nombre}</div>
                            <div class="small text-muted">CI: ${ci} | Tel: ${telefono}</div>
                        </div>
                        <div class="ml-2">
                            <span class="badge ${badgeClass}">${estado}</span>
                        </div>
                    </div>
                `;

                if (disponible) {
                    item.addEventListener('click', () => {
                        // Deseleccionar otros
                        listT.querySelectorAll('.resource-card').forEach(el => el.classList.remove('selected'));
                        // Seleccionar este
                        item.classList.add('selected');
                        inputT.value = t.id;
                        // Quitar error visual si existe
                        inputT.nextElementSibling.style.border = "1px solid #ced4da";
                    });
                }
                listT.appendChild(item);
            });

            // Llenar Vehículos
            state.vehiculos.forEach(v => {
                const placa = v.placa || 'Sin Placa';
                const tipo = v.tipo || 'N/A';
                const tipoTransporte = v.tipo_transporte?.nombre || 'N/A';
                const capacidad = v.capacidad || 0;
                const estado = v.estado || 'Desconocido';
                const disponible = estado === 'Disponible';
                const badgeClass = disponible ? 'badge-success' : 'badge-danger';

                const item = document.createElement('div');
                item.className = `resource-card p-2 mb-1 rounded ${!disponible ? 'disabled' : ''}`;
                item.dataset.name = `Vehículo [${placa}]`;
                item.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="fas fa-truck fa-2x text-secondary"></i></div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">Vehículo <span class="text-primary">[${placa}]</span></div>
                            <div class="small text-muted">Tipo: ${tipo} | Transp: ${tipoTransporte}</div>
                            <div class="small text-muted">Cap: ${capacidad} kg</div>
                        </div>
                        <div class="ml-2">
                            <span class="badge ${badgeClass}">${estado}</span>
                        </div>
                    </div>
                `;

                if (disponible) {
                    item.addEventListener('click', () => {
                        // Deseleccionar otros
                        listV.querySelectorAll('.resource-card').forEach(el => el.classList.remove('selected'));
                        // Seleccionar este
                        item.classList.add('selected');
                        inputV.value = v.id;
                        // Quitar error visual si existe
                        inputV.nextElementSibling.style.border = "1px solid #ced4da";
                    });
                }
                listV.appendChild(item);
            });

            container.appendChild(card);
        });
    }

    // --- LOGICA PASO 5: CONFIRMACIÓN ---
    function prepareStep5() {
        // Actualizar resumen
        $('#resumenCliente').text($('#selCliente option:selected').text());
        $('#resumenOrigen').text($('#txtNombreOrigen').val() || $('#txtOrigen').val());
        $('#resumenDestino').text($('#txtNombreDestino').val() || $('#txtDestino').val());

        const container = document.getElementById('resumenFinal');
        container.innerHTML = '';

        // Combinar datos de particiones con asignaciones
        const asignacionesDivs = document.querySelectorAll('.asignacion-item');
        
        state.particionesData.forEach((part, idx) => {
            const asigDiv = asignacionesDivs[idx];
            
            const tCard = asigDiv.querySelector('.js-list-transportista .resource-card.selected');
            const vCard = asigDiv.querySelector('.js-list-vehiculo .resource-card.selected');
            
            const transpTxt = tCard ? tCard.dataset.name : '<span class="text-danger">No seleccionado</span>';
            const vehicTxt = vCard ? vCard.dataset.name : '<span class="text-danger">No seleccionado</span>';

            // Construir lista de cargas
            let cargasHtml = '<ul class="mb-0 pl-3">';
            part.cargas.forEach(c => {
                cargasHtml += `<li>${c.cantidad}x ${c.tipo} (${c.variedad}) - ${c.empaquetado} - ${c.peso}kg</li>`;
            });
            cargasHtml += '</ul>';

            const html = `
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-primary text-white p-2">
                        <strong><i class="fas fa-box-open mr-2"></i>Partición #${part.index}</strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <h6 class="text-primary font-weight-bold">Detalles del Envío</h6>
                                <p class="mb-1"><strong>Tipo Transporte:</strong> ${part.tipo_transporte_txt}</p>
                                <p class="mb-1"><strong>Recogida:</strong> ${part.recogidaEntrega.fecha_recogida} a las ${part.recogidaEntrega.hora_recogida}</p>
                                <p class="mb-1"><strong>Entrega Estimada:</strong> ${part.recogidaEntrega.hora_entrega}</p>
                                <div class="mt-2">
                                    <strong>Cargas:</strong>
                                    ${cargasHtml}
                                </div>
                                ${(part.recogidaEntrega.instrucciones_recogida || part.recogidaEntrega.instrucciones_entrega) ? 
                                    `<div class="mt-2 small text-muted">
                                        <em>Instr: ${part.recogidaEntrega.instrucciones_recogida || ''} / ${part.recogidaEntrega.instrucciones_entrega || ''}</em>
                                    </div>` : ''}
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success font-weight-bold">Recursos Asignados</h6>
                                <div class="mb-3">
                                    <label class="small text-muted mb-0">Transportista</label>
                                    <div class="font-weight-bold">${transpTxt}</div>
                                </div>
                                <div>
                                    <label class="small text-muted mb-0">Vehículo</label>
                                    <div class="font-weight-bold">${vehicTxt}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    // --- ENVÍO FINAL ---
    async function submitForm() {
        const btn = document.getElementById('btnFinish');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

        try {
            // Construir payload
            const asignacionesDivs = document.querySelectorAll('.asignacion-item');
            const particionesFinales = state.particionesData.map((part, idx) => {
                const asigDiv = asignacionesDivs[idx];
                return {
                    cargas: part.cargas,
                    recogidaEntrega: {
                        ...part.recogidaEntrega,
                        hora_recogida: part.recogidaEntrega.hora_recogida + ':00',
                        hora_entrega: part.recogidaEntrega.hora_entrega + ':00'
                    },
                    id_tipo_transporte: part.id_tipo_transporte,
                    id_transportista: $(asigDiv).find('.js-transportista-id').val(),
                    id_vehiculo: $(asigDiv).find('.js-vehiculo-id').val()
                };
            });

            const payload = {
                id_usuario_cliente: $('#selCliente').val(),
                ubicacion: {
                    nombreorigen: $('#txtNombreOrigen').val() || 'Origen Seleccionado',
                    nombredestino: $('#txtNombreDestino').val() || 'Destino Seleccionado',
                    origen_lng: state.markers.origin.getLatLng().lng,
                    origen_lat: state.markers.origin.getLatLng().lat,
                    destino_lng: state.markers.destination.getLatLng().lng,
                    destino_lat: state.markers.destination.getLatLng().lat,
                    rutageojson: state.lastGeoJSON
                },
                particiones: particionesFinales
            };

            const res = await fetch('/api/envios/completo-admin', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('authToken'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.mensaje || data.error || 'Error desconocido en el servidor');
            }

            alert('Envío creado exitosamente');
            window.location.href = '/admin/envios';

        } catch (e) {
            console.error(e);
            alert('Error al crear el envío: ' + e.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i> Confirmar y Crear Envío';
        }
    }

    function initSelect2() {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    }
});
</script>
@endpush
