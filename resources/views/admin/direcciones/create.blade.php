@extends('layouts.admin')

@section('title', isset($editId) ? 'Editar Dirección - OrgTrack' : 'Nueva Dirección - OrgTrack')
@section('page-title', isset($editId) ? 'Editar Dirección' : 'Nueva Dirección')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.direcciones.index') }}">Direcciones Guardadas</a></li>
    <li class="breadcrumb-item active">{{ isset($editId) ? 'Editar Dirección' : 'Nueva Dirección' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ isset($editId) ? 'Modificar Origen y Destino en el Mapa' : 'Seleccionar Origen y Destino en el Mapa' }}</h3>
            </div>
            <div class="card-body">
                <div id="error-message" class="alert alert-danger d-none"></div>
                <div id="success-message" class="alert alert-success d-none"></div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-location-arrow"></i></span></div>
                    <input type="text" id="hintDir" class="form-control" value="Selecciona el punto de origen en el mapa" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" id="resetDir" type="button">Reiniciar</button>
                    </div>
                </div>

                <div id="mapDirecciones" style="height: 500px;" class="rounded border"></div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Nombre del lugar de origen: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dirOrigen" placeholder="Ej. Finca Orgánica La Esperanza" required>
                        <small class="text-muted">Coordenadas: <span id="coordsOrigen">-</span></small>
                    </div>
                    <div class="col-md-6">
                        <label>Nombre del lugar de destino: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dirDestino" placeholder="Ej. Planta Central de Procesamiento" required>
                        <small class="text-muted">Coordenadas: <span id="coordsDestino">-</span></small>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button class="btn btn-primary" id="saveBtn" disabled>
                    <i class="fas fa-save mr-1"></i> {{ isset($editId) ? 'Actualizar Dirección' : 'Guardar Dirección' }}
                </button>
                <a href="{{ route('admin.direcciones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('authToken');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const ORS_API_KEY = '5b3ce3597851110001cf6248dbff311ed4d34185911c2eb9e6c50080';
    const editId = @json($editId ?? null);
    const isEditMode = editId !== null;
    
    const mapD = L.map('mapDirecciones').setView([-17.7833, -63.1833], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
        maxZoom: 19, 
        attribution: '&copy; OpenStreetMap contributors' 
    }).addTo(mapD);

    let mO = null, mD = null, routeLayer = null;
    let origenLat = null, origenLng = null, destinoLat = null, destinoLng = null;
    const dirOrigen = document.getElementById('dirOrigen');
    const dirDestino = document.getElementById('dirDestino');
    const hintDir = document.getElementById('hintDir');
    const coordsOrigen = document.getElementById('coordsOrigen');
    const coordsDestino = document.getElementById('coordsDestino');
    const saveBtn = document.getElementById('saveBtn');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');

    function reset(){
        if (mO) mapD.removeLayer(mO);
        if (mD) mapD.removeLayer(mD);
        if (routeLayer) mapD.removeLayer(routeLayer);
        mO = mD = routeLayer = null;
        origenLat = origenLng = destinoLat = destinoLng = null;
        dirOrigen.value = '';
        dirDestino.value = '';
        coordsOrigen.textContent = '-';
        coordsDestino.textContent = '-';
        hintDir.value = isEditMode ? 'Mueve los marcadores para cambiar la ruta' : 'Selecciona el punto de origen en el mapa';
        saveBtn.disabled = true;
        errorMessage.classList.add('d-none');
        successMessage.classList.add('d-none');
    }
    document.getElementById('resetDir').onclick = reset;

    // Cargar datos de edición si está en modo edición
    async function loadDireccionForEdit() {
        if (!isEditMode) {
            reset();
            return;
        }

        try {
            hintDir.value = 'Cargando dirección...';
            const response = await fetch(`${window.location.origin}/api/ubicaciones/${editId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    localStorage.removeItem('authToken');
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Error al cargar la dirección');
            }

            const direccion = await response.json();
            
            if (direccion.origen_lat && direccion.origen_lng && direccion.destino_lat && direccion.destino_lng) {
                origenLat = parseFloat(direccion.origen_lat);
                origenLng = parseFloat(direccion.origen_lng);
                destinoLat = parseFloat(direccion.destino_lat);
                destinoLng = parseFloat(direccion.destino_lng);
                
                dirOrigen.value = direccion.nombreorigen || '';
                dirDestino.value = direccion.nombredestino || '';
                coordsOrigen.textContent = `${origenLat.toFixed(6)}, ${origenLng.toFixed(6)}`;
                coordsDestino.textContent = `${destinoLat.toFixed(6)}, ${destinoLng.toFixed(6)}`;
                
                // Crear marcadores
                mO = L.marker([origenLat, origenLng], {
                    icon: L.divIcon({
                        className: 'custom-marker',
                        html: '<i class="fas fa-map-marker-alt fa-2x" style="color: #28a745;"></i>',
                        iconSize: [30, 30],
                        iconAnchor: [15, 30]
                    }),
                    draggable: true
                }).addTo(mapD).bindPopup('Origen (arrastra para mover)').openPopup();
                
                mD = L.marker([destinoLat, destinoLng], {
                    icon: L.divIcon({
                        className: 'custom-marker',
                        html: '<i class="fas fa-map-marker-alt fa-2x" style="color: #dc3545;"></i>',
                        iconSize: [30, 30],
                        iconAnchor: [15, 30]
                    }),
                    draggable: true
                }).addTo(mapD).bindPopup('Destino (arrastra para mover)').openPopup();
                
                // Event listeners para mover marcadores
                mO.on('dragend', async function(e) {
                    const pos = e.target.getLatLng();
                    origenLat = pos.lat;
                    origenLng = pos.lng;
                    coordsOrigen.textContent = `${origenLat.toFixed(6)}, ${origenLng.toFixed(6)}`;
                    hintDir.value = 'Recalculando ruta...';
                    await trazarRuta(origenLat, origenLng, destinoLat, destinoLng);
                });
                
                mD.on('dragend', async function(e) {
                    const pos = e.target.getLatLng();
                    destinoLat = pos.lat;
                    destinoLng = pos.lng;
                    coordsDestino.textContent = `${destinoLat.toFixed(6)}, ${destinoLng.toFixed(6)}`;
                    hintDir.value = 'Recalculando ruta...';
                    await trazarRuta(origenLat, origenLng, destinoLat, destinoLng);
                });
                
                // Ajustar vista del mapa para mostrar ambos marcadores
                const bounds = L.latLngBounds([
                    [origenLat, origenLng],
                    [destinoLat, destinoLng]
                ]);
                mapD.fitBounds(bounds, { padding: [50, 50] });
                
                // Trazar ruta inicial
                await trazarRuta(origenLat, origenLng, destinoLat, destinoLng);
                hintDir.value = 'Dirección cargada. Puedes mover los marcadores o cambiar los nombres.';
                saveBtn.disabled = false;
            } else {
                throw new Error('La dirección no tiene coordenadas válidas');
            }
        } catch (error) {
            showError('Error al cargar la dirección: ' + error.message);
            setTimeout(() => {
                window.location.href = '{{ route("admin.direcciones.index") }}';
            }, 2000);
        }
    }

    if (isEditMode) {
        loadDireccionForEdit();
    } else {
        reset();
    }

    // Trazar ruta usando OpenRouteService
    async function trazarRuta(origenLat, origenLng, destinoLat, destinoLng) {
        if (routeLayer) mapD.removeLayer(routeLayer);

        try {
            hintDir.value = 'Calculando ruta real...';
            
            const response = await fetch(`https://api.openrouteservice.org/v2/directions/driving-car/geojson`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': ORS_API_KEY,
                    'Accept': 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8'
                },
                body: JSON.stringify({
                    coordinates: [[origenLng, origenLat], [destinoLng, destinoLat]],
                    format: 'geojson'
                })
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error de OpenRouteService:', response.status, errorText);
                throw new Error(`Error al calcular la ruta: ${response.status}`);
            }

            const data = await response.json();
            console.log('Respuesta OpenRouteService:', data);
            
            // OpenRouteService devuelve GeoJSON directamente
            let coordinates = null;
            
            if (data.type === 'FeatureCollection' && data.features && data.features.length > 0) {
                // Formato FeatureCollection
                const feature = data.features[0];
                if (feature.geometry && feature.geometry.coordinates) {
                    coordinates = feature.geometry.coordinates;
                }
            } else if (data.type === 'Feature' && data.geometry && data.geometry.coordinates) {
                // Formato Feature directo
                coordinates = data.geometry.coordinates;
            } else if (data.routes && data.routes.length > 0 && data.routes[0].geometry) {
                // Formato JSON con routes
                coordinates = data.routes[0].geometry.coordinates;
            }
            
            if (coordinates && coordinates.length > 0) {
                // Las coordenadas vienen como [lng, lat], las convertimos a [lat, lng] para Leaflet
                const leafletCoords = coordinates.map(coord => [coord[1], coord[0]]);
                
                routeLayer = L.polyline(leafletCoords, {
                    color: '#3b82f6',
                    weight: 5,
                    opacity: 0.8
                }).addTo(mapD);
                
                // Ajustar el mapa para mostrar toda la ruta
                const bounds = L.latLngBounds(leafletCoords);
                mapD.fitBounds(bounds, { padding: [50, 50] });
                
                hintDir.value = 'Ruta trazada correctamente. Completa los nombres y guarda.';
                saveBtn.disabled = false;
            } else {
                console.error('No se encontraron coordenadas en la respuesta:', data);
                throw new Error('Formato de respuesta inválido');
            }
        } catch (error) {
            console.error('Error al trazar ruta:', error);
            alert('Error al calcular la ruta real: ' + error.message + '. Se mostrará una línea recta.');
            // Si falla la API, trazar línea recta como fallback
            routeLayer = L.polyline([[origenLat, origenLng], [destinoLat, destinoLng]], {
                color: '#ff6b6b',
                weight: 4,
                opacity: 0.5,
                dashArray: '5, 5'
            }).addTo(mapD);
            mapD.fitBounds([[origenLat, origenLng], [destinoLat, destinoLng]], { padding: [50, 50] });
            hintDir.value = 'Ruta trazada (línea recta - fallback). Completa los nombres y guarda.';
            saveBtn.disabled = false;
        }
    }

    mapD.on('click', async (e) => {
        // Solo permitir clicks en el mapa si NO está en modo edición o si aún no hay marcadores
        if (isEditMode && mO && mD) {
            return; // En modo edición, usar solo arrastrar marcadores
        }
        
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (!mO) {
            origenLat = lat;
            origenLng = lng;
            mO = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<i class="fas fa-map-marker-alt fa-2x" style="color: #28a745;"></i>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 30]
                }),
                draggable: true
            }).addTo(mapD).bindPopup('Origen (arrastra para mover)').openPopup();
            
            mO.on('dragend', async function(e) {
                const pos = e.target.getLatLng();
                origenLat = pos.lat;
                origenLng = pos.lng;
                coordsOrigen.textContent = `${origenLat.toFixed(6)}, ${origenLng.toFixed(6)}`;
                if (mD) {
                    hintDir.value = 'Recalculando ruta...';
                    await trazarRuta(origenLat, origenLng, destinoLat, destinoLng);
                }
            });
            
            coordsOrigen.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            hintDir.value = 'Ahora selecciona el destino en el mapa';
        } else if (!mD) {
            destinoLat = lat;
            destinoLng = lng;
            mD = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<i class="fas fa-map-marker-alt fa-2x" style="color: #dc3545;"></i>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 30]
                }),
                draggable: true
            }).addTo(mapD).bindPopup('Destino (arrastra para mover)').openPopup();
            
            mD.on('dragend', async function(e) {
                const pos = e.target.getLatLng();
                destinoLat = pos.lat;
                destinoLng = pos.lng;
                coordsDestino.textContent = `${destinoLat.toFixed(6)}, ${destinoLng.toFixed(6)}`;
                hintDir.value = 'Recalculando ruta...';
                await trazarRuta(origenLat, origenLng, destinoLat, destinoLng);
            });
            
            coordsDestino.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            hintDir.value = 'Calculando ruta...';
            
            await trazarRuta(origenLat, origenLng, destinoLat, destinoLng);
        }
    });

    // Guardar dirección
    saveBtn.addEventListener('click', async () => {
        if (!origenLat || !origenLng || !destinoLat || !destinoLng) {
            showError('Por favor selecciona origen y destino en el mapa');
            return;
        }

        const nombreOrigen = dirOrigen.value.trim();
        const nombreDestino = dirDestino.value.trim();

        if (!nombreOrigen || !nombreDestino) {
            showError('Por favor completa los nombres de origen y destino');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + (isEditMode ? 'Actualizando...' : 'Guardando...');

        try {
            // Obtener GeoJSON de la ruta si existe
            let rutaGeoJSON = null;
            if (routeLayer) {
                const latlngs = routeLayer.getLatLngs();
                const coordinates = latlngs.map(ll => [ll.lng, ll.lat]);
                rutaGeoJSON = JSON.stringify({
                    type: 'LineString',
                    coordinates: coordinates
                });
            }

            const url = isEditMode 
                ? `${window.location.origin}/api/ubicaciones/${editId}`
                : `${window.location.origin}/api/ubicaciones`;
            const method = isEditMode ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nombreOrigen: nombreOrigen,
                    origen_lng: origenLng,
                    origen_lat: origenLat,
                    nombreDestino: nombreDestino,
                    destino_lng: destinoLng,
                    destino_lat: destinoLat,
                    rutaGeoJSON: rutaGeoJSON
                })
            });

            if (!response.ok) {
                if (response.status === 401) {
                    localStorage.removeItem('authToken');
                    window.location.href = '/login';
                    return;
                }
                const data = await response.json().catch(() => ({}));
                throw new Error(data.error || `Error al ${isEditMode ? 'actualizar' : 'guardar'} la dirección`);
            }

            showSuccess(`Dirección ${isEditMode ? 'actualizada' : 'guardada'} correctamente. Redirigiendo...`);
            setTimeout(() => {
                window.location.href = '{{ route("admin.direcciones.index") }}';
            }, 1500);
        } catch (error) {
            showError(`Error al ${isEditMode ? 'actualizar' : 'guardar'} la dirección: ` + error.message);
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> ' + (isEditMode ? 'Actualizar Dirección' : 'Guardar Dirección');
        }
    });

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('d-none');
        successMessage.classList.add('d-none');
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.classList.remove('d-none');
        errorMessage.classList.add('d-none');
    }
});
</script>
<style>
.custom-marker {
    background: transparent;
    border: none;
}
</style>
@endsection
