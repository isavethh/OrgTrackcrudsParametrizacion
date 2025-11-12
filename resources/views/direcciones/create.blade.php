@extends('adminlte::page')

@section('title', 'Crear Dirección/Ruta')

@section('content_header')
    <h1>Crear Nueva Dirección/Ruta</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('direcciones.store') }}" method="POST" id="direccionForm">
                @csrf
                
                <div class="form-group">
                    <label for="envio_id">Envío <span class="text-danger">*</span></label>
                    <select name="envio_id" id="envio_id" class="form-control @error('envio_id') is-invalid @enderror" required>
                        <option value="">Seleccione un envío</option>
                        @foreach($envios as $envio)
                            <option value="{{ $envio->id }}" {{ old('envio_id') == $envio->id ? 'selected' : '' }}>
                                Envío #{{ $envio->id }} - {{ $envio->estado }}
                            </option>
                        @endforeach
                    </select>
                    @error('envio_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nombre_ruta">Nombre de la Ruta <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_ruta" id="nombre_ruta" class="form-control @error('nombre_ruta') is-invalid @enderror" 
                        value="{{ old('nombre_ruta') }}" required placeholder="Ej: Ruta La Paz - El Alto">
                    @error('nombre_ruta')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">El nombre se generará automáticamente basado en el origen y destino seleccionados</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Instrucciones:</strong> Haz clic en el mapa para marcar el origen (marcador azul) y luego el destino (marcador rojo). La ruta se calculará automáticamente.
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-map"></i> Mapa Interactivo</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 500px; width: 100%;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Origen</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="origen_nombre">Nombre del Origen</label>
                                    <input type="text" id="origen_nombre" class="form-control" readonly>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Latitud:</small>
                                        <input type="text" id="origen_lat" class="form-control form-control-sm" readonly>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Longitud:</small>
                                        <input type="text" id="origen_lng" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-flag-checkered"></i> Destino</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="destino_nombre">Nombre del Destino</label>
                                    <input type="text" id="destino_nombre" class="form-control" readonly>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Latitud:</small>
                                        <input type="text" id="destino_lat" class="form-control form-control-sm" readonly>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Longitud:</small>
                                        <input type="text" id="destino_lng" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campo oculto para enviar la ruta GeoJSON al servidor -->
                <input type="hidden" name="ruta_geojson" id="ruta_geojson" value="{{ old('ruta_geojson') }}">

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Ruta
                    </button>
                    <a href="{{ route('direcciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { z-index: 1; }
        .leaflet-container { height: 100%; width: 100%; }
    </style>
@stop

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const API_KEY = '5b3ce3597851110001cf6248dbff311ed4d34185911c2eb9e6c50080';
        let map, origenMarker, destinoMarker, routeLayer;
        let origenCoords = null, destinoCoords = null;
        let modo = 'origen'; // 'origen' o 'destino'

        // Inicializar mapa centrado en La Paz, Bolivia
        map = L.map('map').setView([-16.5000, -68.1500], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Función para geocodificación inversa usando OpenRouteService
        async function reverseGeocode(lat, lng) {
            try {
                const response = await fetch(
                    `https://api.openrouteservice.org/geocoding/reverse?api_key=${API_KEY}&point.lon=${lng}&point.lat=${lat}&size=1`
                );
                const data = await response.json();
                if (data.features && data.features.length > 0) {
                    return data.features[0].properties.label || 'Ubicación sin nombre';
                }
                return 'Ubicación sin nombre';
            } catch (error) {
                console.error('Error en geocodificación inversa:', error);
                return 'Ubicación sin nombre';
            }
        }

        // Función para obtener ruta usando OpenRouteService
        async function getRoute(start, end) {
            try {
                const response = await fetch(
                    `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${API_KEY}`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            coordinates: [[start.lng, start.lat], [end.lng, end.lat]],
                            format: 'geojson'
                        })
                    }
                );
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error de API:', response.status, errorText);
                    throw new Error(`Error ${response.status}: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Respuesta de API:', data);
                
                let geojson;
                
                if (data.type === 'FeatureCollection' && data.features && data.features.length > 0) {
                    // Si la respuesta ya es GeoJSON
                    geojson = data.features[0].geometry;
                } else if (data.routes && data.routes.length > 0) {
                    // Formato antiguo de la API
                    const route = data.routes[0];
                    const geometry = route.geometry;
                    
                    let coordinates;
                    if (typeof geometry === 'string') {
                        // Geometría codificada - crear ruta simple
                        coordinates = [[start.lng, start.lat], [end.lng, end.lat]];
                    } else if (geometry.coordinates) {
                        coordinates = geometry.coordinates;
                    } else {
                        throw new Error('Formato de geometría no reconocido');
                    }
                    
                    // Convertir a GeoJSON
                    geojson = {
                        type: 'LineString',
                        coordinates: coordinates.map(coord => Array.isArray(coord) && coord.length >= 2 ? [coord[0], coord[1]] : [coord.lng || coord[0], coord.lat || coord[1]])
                    };
                } else {
                    throw new Error('No se encontró ruta en la respuesta: ' + JSON.stringify(data));
                }
                
                // Guardar en campo oculto
                document.getElementById('ruta_geojson').value = JSON.stringify(geojson);
                console.log('Ruta guardada:', geojson);
                
                // Dibujar ruta en el mapa
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                }
                
                routeLayer = L.geoJSON(geojson, {
                    style: {
                        color: '#3388ff',
                        weight: 5,
                        opacity: 0.7
                    }
                }).addTo(map);
                
                // Ajustar vista para mostrar toda la ruta
                const bounds = L.latLngBounds([start, end]);
                map.fitBounds(bounds, { padding: [50, 50] });
                
                return geojson;
            } catch (error) {
                console.error('Error al obtener ruta:', error);
                
                // Crear una ruta simple como fallback (línea recta)
                const geojson = {
                    type: 'LineString',
                    coordinates: [[start.lng, start.lat], [end.lng, end.lat]]
                };
                
                document.getElementById('ruta_geojson').value = JSON.stringify(geojson);
                console.log('Ruta fallback guardada:', geojson);
                
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                }
                
                routeLayer = L.geoJSON(geojson, {
                    style: {
                        color: '#ff8800',
                        weight: 3,
                        opacity: 0.5,
                        dashArray: '10, 5'
                    }
                }).addTo(map);
                
                alert('No se pudo calcular la ruta optimizada. Se ha creado una ruta directa. Puedes guardar de todas formas.');
                return geojson;
            }
        }

        // Manejar clics en el mapa
        map.on('click', async function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            if (modo === 'origen') {
                // Marcar origen
                if (origenMarker) {
                    map.removeLayer(origenMarker);
                }
                
                origenCoords = { lat, lng };
                origenMarker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup('Origen').openPopup();
                
                document.getElementById('origen_lat').value = lat.toFixed(8);
                document.getElementById('origen_lng').value = lng.toFixed(8);
                
                // Obtener nombre del lugar
                const nombre = await reverseGeocode(lat, lng);
                document.getElementById('origen_nombre').value = nombre;
                
                modo = 'destino';
                alert('Origen marcado. Ahora haz clic para marcar el destino.');
                
            } else if (modo === 'destino') {
                // Marcar destino
                if (destinoMarker) {
                    map.removeLayer(destinoMarker);
                }
                
                destinoCoords = { lat, lng };
                destinoMarker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup('Destino').openPopup();
                
                document.getElementById('destino_lat').value = lat.toFixed(8);
                document.getElementById('destino_lng').value = lng.toFixed(8);
                
                // Obtener nombre del lugar
                const nombre = await reverseGeocode(lat, lng);
                document.getElementById('destino_nombre').value = nombre;
                
                // Calcular y dibujar ruta
                if (origenCoords && destinoCoords) {
                    await getRoute(origenCoords, destinoCoords);
                    
                    // Generar nombre de ruta automáticamente
                    const origenNombre = document.getElementById('origen_nombre').value;
                    const destinoNombre = document.getElementById('destino_nombre').value;
                    if (origenNombre && destinoNombre) {
                        document.getElementById('nombre_ruta').value = `${origenNombre} → ${destinoNombre}`;
                    }
                }
                
                modo = 'origen'; // Permitir cambiar origen y destino
            }
        });

        // Validar formulario antes de enviar
        document.getElementById('direccionForm').addEventListener('submit', function(e) {
            const rutaGeojson = document.getElementById('ruta_geojson').value;
            const nombreRuta = document.getElementById('nombre_ruta').value;
            
            if (!rutaGeojson || rutaGeojson.trim() === '') {
                e.preventDefault();
                alert('Por favor, marca el origen y destino en el mapa antes de guardar.');
                return false;
            }
            
            if (!nombreRuta || nombreRuta.trim() === '') {
                e.preventDefault();
                alert('Por favor, ingresa un nombre para la ruta.');
                return false;
            }
            
            // Validar que ruta_geojson sea JSON válido
            try {
                JSON.parse(rutaGeojson);
            } catch (error) {
                e.preventDefault();
                alert('Error en el formato de la ruta. Por favor, marca nuevamente el origen y destino.');
                return false;
            }
        });

        // Botón para reiniciar
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('direccionForm');
            const resetBtn = document.createElement('button');
            resetBtn.type = 'button';
            resetBtn.className = 'btn btn-warning ml-2';
            resetBtn.innerHTML = '<i class="fas fa-redo"></i> Reiniciar Mapa';
            resetBtn.onclick = function() {
                if (origenMarker) map.removeLayer(origenMarker);
                if (destinoMarker) map.removeLayer(destinoMarker);
                if (routeLayer) map.removeLayer(routeLayer);
                origenMarker = null;
                destinoMarker = null;
                routeLayer = null;
                origenCoords = null;
                destinoCoords = null;
                modo = 'origen';
                document.getElementById('origen_lat').value = '';
                document.getElementById('origen_lng').value = '';
                document.getElementById('destino_lat').value = '';
                document.getElementById('destino_lng').value = '';
                document.getElementById('origen_nombre').value = '';
                document.getElementById('destino_nombre').value = '';
                document.getElementById('ruta_geojson').value = '';
                document.getElementById('nombre_ruta').value = '';
            };
            form.querySelector('.form-group.mt-3').appendChild(resetBtn);
        });
    </script>
@stop
