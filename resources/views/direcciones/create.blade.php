@extends('adminlte::page')

@section('title', 'Crear Dirección')

@section('content_header')
    <h1>Crear Nueva Dirección/Ruta</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('direcciones.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="nombre_ruta">Nombre de Ruta <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_ruta" id="nombre_ruta" class="form-control @error('nombre_ruta') is-invalid @enderror" value="{{ old('nombre_ruta') }}" required placeholder="Ej: Ruta La Paz - El Alto">
                    @error('nombre_ruta')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3" placeholder="Detalles adicionales de la ruta">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Mapa Interactivo -->
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Mapa de Ruta</h3>
                    </div>
                    <div class="card-body">
                        <div id="map" style="height: 500px; width: 100%;"></div>
                        <small class="form-text text-muted">Haga clic en el mapa para establecer los puntos de recogida y entrega. También puede hacer clic en "Ruta" para trazar el camino.</small>
                    </div>
                </div>

                <!-- Punto de Recogida -->
                <div class="card mt-3">
                    <div class="card-header bg-success">
                        <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Punto de Recogida</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre_punto_recogida">Nombre del Punto de Recogida</label>
                            <input type="text" name="nombre_punto_recogida" id="nombre_punto_recogida" class="form-control @error('nombre_punto_recogida') is-invalid @enderror" value="{{ old('nombre_punto_recogida') }}" placeholder="Ej: Av. Principal #123, La Paz">
                            @error('nombre_punto_recogida')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="punto_recogida_lat">Latitud</label>
                                    <input type="number" step="0.000001" name="punto_recogida_lat" id="punto_recogida_lat" class="form-control @error('punto_recogida_lat') is-invalid @enderror" value="{{ old('punto_recogida_lat') }}" placeholder="-16.5000" readonly>
                                    @error('punto_recogida_lat')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="punto_recogida_lng">Longitud</label>
                                    <input type="number" step="0.000001" name="punto_recogida_lng" id="punto_recogida_lng" class="form-control @error('punto_recogida_lng') is-invalid @enderror" value="{{ old('punto_recogida_lng') }}" placeholder="-68.1500" readonly>
                                    @error('punto_recogida_lng')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Punto de Entrega -->
                <div class="card mt-3">
                    <div class="card-header bg-info">
                        <h3 class="card-title"><i class="fas fa-flag-checkered"></i> Punto de Entrega</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre_punto_entrega">Nombre del Punto de Entrega</label>
                            <input type="text" name="nombre_punto_entrega" id="nombre_punto_entrega" class="form-control @error('nombre_punto_entrega') is-invalid @enderror" value="{{ old('nombre_punto_entrega') }}" placeholder="Ej: Calle Comercio #456, El Alto">
                            @error('nombre_punto_entrega')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="punto_entrega_lat">Latitud</label>
                                    <input type="number" step="0.000001" name="punto_entrega_lat" id="punto_entrega_lat" class="form-control @error('punto_entrega_lat') is-invalid @enderror" value="{{ old('punto_entrega_lat') }}" placeholder="-16.5000" readonly>
                                    @error('punto_entrega_lat')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="punto_entrega_lng">Longitud</label>
                                    <input type="number" step="0.000001" name="punto_entrega_lng" id="punto_entrega_lng" class="form-control @error('punto_entrega_lng') is-invalid @enderror" value="{{ old('punto_entrega_lng') }}" placeholder="-68.1500" readonly>
                                    @error('punto_entrega_lng')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Dirección
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
        .btn.active {
            box-shadow: 0 0 10px rgba(0,123,255,0.5);
            border: 2px solid #007bff !important;
        }
    </style>
@stop

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(document).ready(function() {

            // Inicializar mapa centrado en Bolivia (La Paz)
            var map = L.map('map').setView([-16.5000, -68.1500], 13);

            // Capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var pickupMarker = null;
            var deliveryMarker = null;
            var routeLine = null;
            var clickMode = 'pickup'; // 'pickup' o 'delivery'

            // Iconos personalizados
            var pickupIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var deliveryIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Controles de modo
            var controlDiv = L.control({position: 'topright'});
            controlDiv.onAdd = function(map) {
                var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                div.innerHTML = `
                    <div style="background: white; padding: 10px; border-radius: 5px;">
                        <div style="margin-bottom: 5px;">
                            <button id="pickupBtn" class="btn btn-sm btn-success active" style="width: 100%;">
                                <i class="fas fa-map-marker-alt"></i> Punto Recogida
                            </button>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <button id="deliveryBtn" class="btn btn-sm btn-info" style="width: 100%;">
                                <i class="fas fa-flag-checkered"></i> Punto Entrega
                            </button>
                        </div>
                        <div>
                            <button id="routeBtn" class="btn btn-sm btn-primary" style="width: 100%;">
                                <i class="fas fa-route"></i> Trazar Ruta
                            </button>
                        </div>
                    </div>
                `;
                
                // Prevenir que los clicks en el control se propaguen al mapa
                L.DomEvent.disableClickPropagation(div);
                
                return div;
            };
            controlDiv.addTo(map);

            // Botones de control
            setTimeout(function() {
                document.getElementById('pickupBtn').onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    clickMode = 'pickup';
                    this.classList.add('active');
                    document.getElementById('deliveryBtn').classList.remove('active');
                    console.log('Modo: Punto de Recogida activado');
                };

                document.getElementById('deliveryBtn').onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    clickMode = 'delivery';
                    this.classList.add('active');
                    document.getElementById('pickupBtn').classList.remove('active');
                    console.log('Modo: Punto de Entrega activado');
                };

                document.getElementById('routeBtn').onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (pickupMarker && deliveryMarker) {
                        drawRoute();
                    } else {
                        alert('Debe establecer ambos puntos primero');
                    }
                };
            }, 100);

            // Variables para nombres de lugares
            var pickupPlaceName = '';
            var deliveryPlaceName = '';

            // Función de geocodificación inversa
            function reverseGeocode(lat, lng, callback) {
                var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        var placeName = '';
                        if (data.address) {
                            // Construir nombre del lugar con más detalles
                            var parts = [];
                            
                            // Intentar agregar calle y número
                            if (data.address.road) {
                                var street = data.address.road;
                                if (data.address.house_number) {
                                    street += ' #' + data.address.house_number;
                                }
                                parts.push(street);
                            } else if (data.address.amenity) {
                                parts.push(data.address.amenity);
                            } else if (data.address.neighbourhood) {
                                parts.push(data.address.neighbourhood);
                            }
                            
                            // Agregar barrio o zona
                            if (data.address.suburb && !parts.includes(data.address.suburb)) {
                                parts.push(data.address.suburb);
                            } else if (data.address.neighbourhood && !parts.includes(data.address.neighbourhood)) {
                                parts.push(data.address.neighbourhood);
                            } else if (data.address.quarter && !parts.includes(data.address.quarter)) {
                                parts.push(data.address.quarter);
                            }
                            
                            // Agregar ciudad
                            if (data.address.city) {
                                parts.push(data.address.city);
                            } else if (data.address.town) {
                                parts.push(data.address.town);
                            } else if (data.address.village) {
                                parts.push(data.address.village);
                            } else if (data.address.municipality) {
                                parts.push(data.address.municipality);
                            }
                            
                            placeName = parts.length > 0 ? parts.join(', ') : data.display_name;
                        } else {
                            placeName = data.display_name || 'Ubicación desconocida';
                        }
                        callback(placeName);
                    })
                    .catch(error => {
                        console.error('Error en geocodificación:', error);
                        callback('Ubicación desconocida');
                    });
            }

            // Función para actualizar nombre de ruta
            function updateRouteName() {
                if (pickupPlaceName && deliveryPlaceName) {
                    document.getElementById('nombre_ruta').value = pickupPlaceName + ' - ' + deliveryPlaceName;
                }
            }

            // Click en el mapa
            map.on('click', function(e) {
                if (clickMode === 'pickup') {
                    if (pickupMarker) {
                        map.removeLayer(pickupMarker);
                    }
                    pickupMarker = L.marker([e.latlng.lat, e.latlng.lng], {icon: pickupIcon})
                        .addTo(map)
                        .bindPopup('Obteniendo ubicación...')
                        .openPopup();
                    
                    document.getElementById('punto_recogida_lat').value = e.latlng.lat.toFixed(6);
                    document.getElementById('punto_recogida_lng').value = e.latlng.lng.toFixed(6);
                    
                    // Obtener nombre del lugar
                    reverseGeocode(e.latlng.lat, e.latlng.lng, function(placeName) {
                        pickupPlaceName = placeName;
                        document.getElementById('nombre_punto_recogida').value = placeName;
                        pickupMarker.bindPopup('<b>Punto de Recogida</b><br>' + placeName).openPopup();
                        updateRouteName();
                    });
                    
                    // NO cambiar el modo automáticamente
                } else if (clickMode === 'delivery') {
                    if (deliveryMarker) {
                        map.removeLayer(deliveryMarker);
                    }
                    deliveryMarker = L.marker([e.latlng.lat, e.latlng.lng], {icon: deliveryIcon})
                        .addTo(map)
                        .bindPopup('Obteniendo ubicación...')
                        .openPopup();
                    
                    document.getElementById('punto_entrega_lat').value = e.latlng.lat.toFixed(6);
                    document.getElementById('punto_entrega_lng').value = e.latlng.lng.toFixed(6);
                    
                    // Obtener nombre del lugar
                    reverseGeocode(e.latlng.lat, e.latlng.lng, function(placeName) {
                        deliveryPlaceName = placeName;
                        document.getElementById('nombre_punto_entrega').value = placeName;
                        deliveryMarker.bindPopup('<b>Punto de Entrega</b><br>' + placeName).openPopup();
                        updateRouteName();
                    });
                    
                    // NO cambiar el modo automáticamente
                }
            });

            // Función para dibujar ruta
            function drawRoute() {
                if (routeLine) {
                    map.removeLayer(routeLine);
                }

                var pickupLatLng = pickupMarker.getLatLng();
                var deliveryLatLng = deliveryMarker.getLatLng();

                routeLine = L.polyline([pickupLatLng, deliveryLatLng], {
                    color: 'blue',
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '10, 5'
                }).addTo(map);

                map.fitBounds(routeLine.getBounds(), {padding: [50, 50]});
            }
        });
    </script>
@stop


