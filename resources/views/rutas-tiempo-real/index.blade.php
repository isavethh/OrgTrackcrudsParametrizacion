@extends('adminlte::page')

@section('title', 'Rutas en Tiempo Real')

@section('content_header')
    <h1><i class="fas fa-route"></i> Rutas en Tiempo Real</h1>
@stop

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .route-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .route-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .badge-pendiente {
            background-color: #6c757d;
        }
        .badge-en_ruta {
            background-color: #17a2b8;
            animation: pulse 2s infinite;
        }
        .badge-completada {
            background-color: #28a745;
        }
        @keyframes pulse {
            0%, 100% { 
                opacity: 1; 
                transform: scale(1);
            }
            50% { 
                opacity: 0.7; 
                transform: scale(1.1);
            }
        }
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }
        .tracking-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .distance-badge {
            font-size: 1.1rem;
            padding: 8px 15px;
        }
        .custom-div-icon div {
            animation: vehiclePulse 2s ease-in-out infinite;
        }
        @keyframes vehiclePulse {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            }
            50% { 
                transform: scale(1.15);
                box-shadow: 0 4px 16px rgba(40,167,69,0.6);
            }
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Map Container -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Mapa de Seguimiento</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="tracking-info" class="tracking-info d-none">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-shipping-fast"></i> Envío:</strong> <span id="current-envio-id"></span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-clock"></i> Estado:</strong> <span id="tracking-status" class="badge"></span>
                        </div>
                    </div>
                </div>
                <div id="map"></div>
            </div>
        </div>

        <!-- Route List -->
        <div class="row">
            @forelse($envios as $envio)
                <div class="col-md-6 col-lg-4">
                    <div class="card route-card" data-envio-id="{{ $envio->id }}">
                        <div class="card-header bg-gradient-{{ $envio->direccion ? ($envio->estado_tracking == 'completada' ? 'success' : ($envio->estado_tracking == 'en_ruta' ? 'info' : 'secondary')) : 'warning' }}">
                            <h3 class="card-title">
                                <i class="fas fa-box"></i> Envío #{{ $envio->id }}
                            </h3>
                            <div class="card-tools">
                                @if($envio->direccion)
                                    <span class="badge badge-{{ $envio->estado_tracking == 'completada' ? 'success' : ($envio->estado_tracking == 'en_ruta' ? 'info' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $envio->estado_tracking)) }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        Sin Dirección
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong><i class="fas fa-user"></i> Cliente:</strong> {{ $envio->usuario->persona->nombre ?? 'N/A' }} {{ $envio->usuario->persona->apellido ?? '' }}</p>
                            
                            @if($envio->direccion)
                                <p><strong><i class="fas fa-map-pin"></i> Origen:</strong> {{ $envio->direccion->nombreorigen }}</p>
                                <p><strong><i class="fas fa-flag-checkered"></i> Destino:</strong> {{ $envio->direccion->nombredestino }}</p>
                                
                                @if($envio->estado_tracking == 'en_ruta')
                                    <div class="alert alert-info mb-2">
                                        <i class="fas fa-info-circle"></i> En tránsito desde {{ $envio->fecha_inicio_tracking->format('H:i') }}
                                    </div>
                                @elseif($envio->estado_tracking == 'completada')
                                    <div class="alert alert-success mb-2">
                                        <i class="fas fa-check-circle"></i> Completada el {{ $envio->fecha_fin_tracking->format('d/m/Y H:i') }}
                                    </div>
                                @endif

                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-primary btn-view-route" data-envio-id="{{ $envio->id }}">
                                        <i class="fas fa-eye"></i> Ver Ruta
                                    </button>
                                    @if($envio->estado_tracking == 'pendiente')
                                        <button class="btn btn-success btn-start-tracking" data-envio-id="{{ $envio->id }}">
                                            <i class="fas fa-play"></i> Empezar
                                        </button>
                                    @elseif($envio->estado_tracking == 'en_ruta')
                                        <button class="btn btn-warning btn-complete-tracking" data-envio-id="{{ $envio->id }}">
                                            <i class="fas fa-stop"></i> Completar
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning mb-2">
                                    <i class="fas fa-exclamation-triangle"></i> Este envío no tiene una dirección asignada.
                                </div>
                                <p class="text-muted"><small>Asigna una dirección desde el módulo de Envíos para poder realizar el seguimiento en tiempo real.</small></p>
                                <a href="{{ route('envios.edit', $envio->id) }}" class="btn btn-warning btn-block">
                                    <i class="fas fa-edit"></i> Asignar Dirección
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay envíos registrados. <a href="{{ route('envios.create') }}">Crear primer envío</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Completion Modal -->
    <div class="modal fade" id="completionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Ruta Terminada</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success" style="animation: pulse 1s ease-in-out;"></i>
                    </div>
                    <h2 class="text-success mb-3"><strong>¡ENVÍO ENTREGADO!</strong></h2>
                    <h4 class="mb-3">El paquete llegó exitosamente a su destino</h4>
                    <div class="alert alert-success">
                        <i class="fas fa-clock"></i> Entrega completada el día de hoy
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-lg" data-dismiss="modal">
                        <i class="fas fa-thumbs-up"></i> Excelente
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let currentMarker;
        let routeLine;
        let trackingInterval;
        let currentEnvioId = null;
        let simulationStep = 0;
        let routeCoordinates = [];

        // Initialize map
        document.addEventListener('DOMContentLoaded', function() {
            map = L.map('map').setView([-17.7833, -63.1821], 13); // Santa Cruz, Bolivia

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        });

        // View route on map
        document.querySelectorAll('.btn-view-route').forEach(button => {
            button.addEventListener('click', function() {
                const envioId = this.dataset.envioId;
                viewRoute(envioId);
            });
        });

        // Start tracking
        document.querySelectorAll('.btn-start-tracking').forEach(button => {
            button.addEventListener('click', function() {
                const envioId = this.dataset.envioId;
                startTracking(envioId);
            });
        });

        // Complete tracking
        document.querySelectorAll('.btn-complete-tracking').forEach(button => {
            button.addEventListener('click', function() {
                const envioId = this.dataset.envioId;
                completeTracking(envioId);
            });
        });

        function viewRoute(envioId) {
            fetch(`/rutas-tiempo-real/${envioId}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const direccion = data.envio.direccion;
                        
                        // Clear existing layers
                        if (routeLine) map.removeLayer(routeLine);
                        if (currentMarker) map.removeLayer(currentMarker);

                        // Draw route line
                        const origen = [direccion.origen_lat, direccion.origen_lng];
                        const destino = [direccion.destino_lat, direccion.destino_lng];
                        
                        routeLine = L.polyline([origen, destino], {
                            color: '#007bff',
                            weight: 5,
                            opacity: 0.7
                        }).addTo(map);

                        // Add markers
                        L.marker(origen).addTo(map)
                            .bindPopup(`<b>Origen:</b> ${direccion.nombreorigen}`);
                        
                        L.marker(destino).addTo(map)
                            .bindPopup(`<b>Destino:</b> ${direccion.nombredestino}`);

                        // If tracking, show current position
                        if (data.envio.ubicacion_actual_lat && data.envio.ubicacion_actual_lng) {
                            const currentPos = [data.envio.ubicacion_actual_lat, data.envio.ubicacion_actual_lng];
                            currentMarker = L.marker(currentPos, {
                                icon: L.divIcon({
                                    className: 'custom-div-icon',
                                    html: "<div style='background-color:#28a745;width:20px;height:20px;border-radius:50%;border:3px solid white;'></div>",
                                    iconSize: [20, 20],
                                    iconAnchor: [10, 10]
                                })
                            }).addTo(map).bindPopup('<b>Ubicación Actual</b>');
                        }

                        map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });
                    }
                });
        }

        async function startTracking(envioId) {
            try {
                const response = await fetch(`/rutas-tiempo-real/${envioId}/start`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    currentEnvioId = envioId;
                    simulationStep = 0;
                    
                    // Show tracking info
                    document.getElementById('tracking-info').classList.remove('d-none');
                    document.getElementById('current-envio-id').textContent = `#${envioId}`;
                    updateTrackingStatus('en_ruta');

                    const direccion = data.envio.direccion;
                    const origen = [direccion.origen_lng, direccion.origen_lat]; // OpenRouteService usa [lng, lat]
                    const destino = [direccion.destino_lng, direccion.destino_lat];

                    // Obtener ruta real de OpenRouteService
                    console.log('Solicitando ruta de OpenRouteService...');
                    const routeData = await fetchRealRoute(origen, destino);
                    
                    if (routeData && routeData.length > 0) {
                        routeCoordinates = routeData; // Ya viene en formato [lat, lng]
                        console.log(`Ruta obtenida con ${routeCoordinates.length} puntos`);
                    } else {
                        // Fallback a interpolación lineal si falla la API
                        console.warn('Usando interpolación lineal como fallback');
                        routeCoordinates = generateIntermediatePoints(
                            [direccion.origen_lat, direccion.origen_lng],
                            [direccion.destino_lat, direccion.destino_lng],
                            100
                        );
                    }

                    // Dibujar ruta real en el mapa
                    if (routeLine) map.removeLayer(routeLine);
                    routeLine = L.polyline(routeCoordinates, {
                        color: '#007bff',
                        weight: 5,
                        opacity: 0.7
                    }).addTo(map);

                    // Agregar marcadores
                    L.marker([direccion.origen_lat, direccion.origen_lng]).addTo(map)
                        .bindPopup(`<b>Origen:</b> ${direccion.nombreorigen}`);
                    
                    L.marker([direccion.destino_lat, direccion.destino_lng]).addTo(map)
                        .bindPopup(`<b>Destino:</b> ${direccion.nombredestino}`);

                    // Crear marcador móvil
                    currentMarker = L.marker(routeCoordinates[0], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div style='background-color:#28a745;width:24px;height:24px;border-radius:50%;border:4px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);'></div>",
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        })
                    }).addTo(map).bindPopup('<b>🚚 En Tránsito</b>');

                    map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });
                    
                    // Iniciar simulación RÁPIDA (cada 300ms)
                    trackingInterval = setInterval(() => simulateMovement(envioId), 300);
                }
            } catch (error) {
                console.error('Error iniciando tracking:', error);
                alert('Error al iniciar el seguimiento');
            }
        }

        async function fetchRealRoute(start, end) {
            try {
                // Usar OpenRouteService API (gratis, no requiere API key para uso básico)
                const url = `https://router.project-osrm.org/route/v1/driving/${start[0]},${start[1]};${end[0]},${end[1]}?overview=full&geometries=geojson`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                    const coordinates = data.routes[0].geometry.coordinates;
                    // Convertir de [lng, lat] a [lat, lng] para Leaflet
                    return coordinates.map(coord => [coord[1], coord[0]]);
                }
                
                return null;
            } catch (error) {
                console.error('Error obteniendo ruta:', error);
                return null;
            }
        }

        function simulateMovement(envioId) {
            if (simulationStep >= routeCoordinates.length - 1) {
                completeTracking(envioId);
                return;
            }

            // Avanzar varios pasos a la vez para movimiento más rápido
            const stepsPerUpdate = Math.ceil(routeCoordinates.length / 150); // Divide la ruta en ~150 actualizaciones
            simulationStep += stepsPerUpdate;
            
            if (simulationStep >= routeCoordinates.length) {
                simulationStep = routeCoordinates.length - 1;
            }

            const currentPos = routeCoordinates[simulationStep];
            
            fetch(`/rutas-tiempo-real/${envioId}/update-location`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    lat: currentPos[0],
                    lng: currentPos[1]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Animar movimiento del marcador suavemente
                    if (currentMarker) {
                        currentMarker.setLatLng(currentPos);
                        // Centrar mapa en el marcador actual
                        map.panTo(currentPos, {
                            animate: true,
                            duration: 0.3
                        });
                    }

                    // Auto-complete si llegó al destino
                    if (simulationStep >= routeCoordinates.length - 1 || data.envio.estado_tracking === 'completada') {
                        clearInterval(trackingInterval);
                        completeTracking(envioId);
                    }
                }
            }).catch(error => {
                console.error('Error en simulación:', error);
            });
        }

        function completeTracking(envioId) {
            clearInterval(trackingInterval);
            
            // Actualizar UI inmediatamente sin esperar respuesta del servidor
            updateTrackingStatus('completada');
            
            // Cambiar icono del marcador
            if (currentMarker) {
                currentMarker.setIcon(L.divIcon({
                    className: 'custom-div-icon',
                    html: "<div style='background-color:#28a745;width:30px;height:30px;border-radius:50%;border:4px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;'>✓</div>",
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                }));
                currentMarker.bindPopup('<b style="color:#28a745;">✓ ENVÍO ENTREGADO</b>').openPopup();
            }
            
            showCompletionModal();
            
            // Guardar en segundo plano sin esperar
            fetch(`/rutas-tiempo-real/${envioId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                // Recargar después de guardar
                setTimeout(() => location.reload(), 3000);
            }).catch(err => {
                console.error('Error al guardar:', err);
                setTimeout(() => location.reload(), 3000);
            });
        }

        function updateTrackingStatus(status) {
            const badge = document.getElementById('tracking-status');
            badge.className = 'badge badge-' + (status === 'completada' ? 'success' : 'info');
            badge.textContent = status === 'en_ruta' ? 'En Ruta' : 'Completada';
        }

        function showCompletionModal() {
            $('#completionModal').modal('show');
        }

        function generateIntermediatePoints(start, end, steps) {
            const points = [];
            for (let i = 0; i <= steps; i++) {
                const lat = start[0] + (end[0] - start[0]) * (i / steps);
                const lng = start[1] + (end[1] - start[1]) * (i / steps);
                points.push([lat, lng]);
            }
            return points;
        }
    </script>
@stop
