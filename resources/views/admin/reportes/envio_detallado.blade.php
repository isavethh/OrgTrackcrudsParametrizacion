@extends('layouts.adminlte')

@section('page-title', 'Reporte: Envío Detallado con Mapa')

@section('page-content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Envío Detallado</li>
            </ol>
        </nav>
    </div>
</div>

<div class="callout callout-primary no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Visualización completa de un envío con mapa de ruta (origen a destino), información del cliente, transportista asignado, historial de estados y documentación.</p>
</div>

<div class="card card-primary card-outline no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-search mr-2"></i>Seleccionar Envío</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Envío</label>
                    <select name="envio_id" class="form-control select2" style="width: 100%;">
                        <option value="">-- Seleccione un envío --</option>
                        @foreach($envios as $e)
                            <option value="{{ $e->id }}" {{ $envioId == $e->id ? 'selected' : '' }}>
                                #{{ $e->id }} - {{ $e->nombreorigen ?? 'Origen' }} → {{ $e->nombredestino ?? 'Destino' }}
                                ({{ \Carbon\Carbon::parse($e->fecha_creacion)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search mr-1"></i> Ver Detalle</button>
                    @if($envio)
                    <button type="button" class="btn btn-danger mr-2" onclick="window.print()"><i class="fas fa-file-pdf mr-1"></i> Imprimir</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

@if($envio)
<!-- MAPA -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title"><i class="fas fa-map-marked-alt mr-2"></i>Mapa de Ruta</h3>
    </div>
    <div class="card-body p-0">
        <div id="mapa" style="height: 400px; width: 100%;"></div>
    </div>
</div>

<div class="row">
    <!-- Información del Envío -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shipping-fast mr-2"></i>Información del Envío #{{ $envio->id }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th style="width: 40%">Fecha Creación</th>
                        <td>{{ \Carbon\Carbon::parse($envio->fecha_creacion)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Origen</th>
                        <td><i class="fas fa-map-marker-alt text-success mr-1"></i>{{ $envio->nombreorigen ?? 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <th>Destino</th>
                        <td><i class="fas fa-map-marker-alt text-danger mr-1"></i>{{ $envio->nombredestino ?? 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <th>Cliente</th>
                        <td>{{ $envio->cliente_nombre ?? '' }} {{ $envio->cliente_apellido ?? '' }} <code>{{ $envio->cliente_ci ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <th>Estado Cancelado</th>
                        <td>
                            @if($envio->cancelado)
                                <span class="badge badge-danger">Sí</span>
                            @else
                                <span class="badge badge-success">No</span>
                            @endif
                        </td>
                    </tr>
                    @if($envio->observaciones_solicitud)
                    <tr>
                        <th>Observaciones</th>
                        <td>{{ $envio->observaciones_solicitud }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Historial de Estados -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-2"></i>Historial de Estados</h3>
            </div>
            <div class="card-body p-0">
                <div class="timeline timeline-inverse p-3">
                    @forelse($historial as $h)
                    <div class="time-label">
                        <span class="bg-primary">{{ \Carbon\Carbon::parse($h->fecha)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <i class="fas fa-check bg-success"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">{{ $h->estado }}</h3>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">Sin historial de estados</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Asignaciones -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-truck mr-2"></i>Asignaciones de Transporte</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Transportista</th>
                    <th>Vehículo</th>
                    <th>Tipo Transporte</th>
                    <th>Estado</th>
                    <th>Fecha Asignación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asignaciones as $a)
                <tr>
                    <td><code>{{ $a->id }}</code></td>
                    <td>{{ $a->transportista_nombre ?? '' }} {{ $a->transportista_apellido ?? '' }}</td>
                    <td><span class="badge badge-dark">{{ $a->placa ?? 'N/A' }}</span></td>
                    <td>{{ $a->tipo_transporte ?? '-' }}</td>
                    <td><span class="badge badge-info">{{ $a->estado ?? '-' }}</span></td>
                    <td>{{ $a->fecha_asignacion ? \Carbon\Carbon::parse($a->fecha_asignacion)->format('d/m/Y H:i') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Sin asignaciones</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Seleccione un envío para ver el detalle</h4>
    </div>
</div>
@endif

<div class="row no-print">
    <div class="col-12">
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    @media print { 
        .no-print { display: none !important; } 
        .main-sidebar, .main-header, .main-footer { display: none !important; } 
        .content-wrapper { margin-left: 0 !important; }
        #mapa { height: 300px !important; }
    }
</style>
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($envio && $envio->origen_lat && $envio->destino_lat)
<script>
    const map = L.map('mapa').setView([{{ ($envio->origen_lat + $envio->destino_lat) / 2 }}, {{ ($envio->origen_lng + $envio->destino_lng) / 2 }}], 8);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Marcador origen
    L.marker([{{ $envio->origen_lat }}, {{ $envio->origen_lng }}], {
        icon: L.divIcon({className: 'bg-success rounded-circle', iconSize: [20, 20], html: '<i class="fas fa-map-marker-alt text-white"></i>'})
    }).addTo(map).bindPopup('<strong>Origen:</strong> {{ $envio->nombreorigen ?? "Punto de origen" }}');

    // Marcador destino
    L.marker([{{ $envio->destino_lat }}, {{ $envio->destino_lng }}], {
        icon: L.divIcon({className: 'bg-danger rounded-circle', iconSize: [20, 20], html: '<i class="fas fa-flag-checkered text-white"></i>'})
    }).addTo(map).bindPopup('<strong>Destino:</strong> {{ $envio->nombredestino ?? "Punto de destino" }}');

    // Línea de ruta
    L.polyline([
        [{{ $envio->origen_lat }}, {{ $envio->origen_lng }}],
        [{{ $envio->destino_lat }}, {{ $envio->destino_lng }}]
    ], {color: 'blue', weight: 3, dashArray: '5,10'}).addTo(map);

    // Ajustar bounds
    map.fitBounds([
        [{{ $envio->origen_lat }}, {{ $envio->origen_lng }}],
        [{{ $envio->destino_lat }}, {{ $envio->destino_lng }}]
    ], {padding: [50, 50]});
</script>
@elseif($envio)
<script>
    document.getElementById('mapa').innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-map fa-4x mb-3"></i><p>Coordenadas no disponibles para este envío</p></div>';
</script>
@endif
@endpush
