@extends('layouts.adminlte')

@section('page-title', 'Reporte: Flota de Vehículos')

@section('page-content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Flota de Vehículos</li>
            </ol>
        </nav>
    </div>
</div>

<div class="callout callout-danger no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Gestión de flota vehicular: control de disponibilidad, distribución por tipo, capacidad total y planificación de recursos logísticos.</p>
</div>

<div class="card card-danger card-outline no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">-- Todos los estados --</option>
                        @foreach($estados as $e)
                            <option value="{{ $e->id }}" {{ $estadoFiltro == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-danger mr-2"><i class="fas fa-search mr-1"></i> Filtrar</button>
                    <button type="button" class="btn btn-danger mr-2" onclick="exportToPDF()"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel mr-1"></i> Excel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $total }}</h3>
                <p>Total Vehículos</p>
            </div>
            <div class="icon"><i class="fas fa-car"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $disponibles }}</h3>
                <p>Disponibles</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($capacidadTotal, 0) }}</h3>
                <p>Capacidad Total (kg)</p>
            </div>
            <div class="icon"><i class="fas fa-weight"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $dataTipo->count() }}</h3>
                <p>Tipos de Vehículo</p>
            </div>
            <div class="icon"><i class="fas fa-truck"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Distribución por Tipo</h3>
            </div>
            <div class="card-body">
                <canvas id="chartTipo" style="height: 200px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Distribución por Estado</h3>
            </div>
            <div class="card-body">
                <canvas id="chartEstado" style="height: 200px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Detalle de Vehículos</h3>
        <div class="card-tools"><span class="badge badge-danger">{{ $vehiculos->count() }} vehículos</span></div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover" id="tabla-vehiculos">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Placa</th>
                    <th>Tipo</th>
                    <th class="text-center">Capacidad</th>
                    <th class="text-center">Estado</th>
                    <th>Registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehiculos as $v)
                <tr>
                    <td><code>{{ $v->id }}</code></td>
                    <td><strong>{{ $v->placa }}</strong></td>
                    <td><span class="badge badge-info">{{ $v->tipo }}</span></td>
                    <td class="text-center">{{ number_format($v->capacidad, 0) }} kg</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $v->estado == 'Disponible' ? 'success' : ($v->estado == 'En uso' ? 'warning' : 'secondary') }}">
                            {{ $v->estado }}
                        </span>
                    </td>
                    <td><small>{{ $v->fecha_registro ? \Carbon\Carbon::parse($v->fecha_registro)->format('d/m/Y') : '-' }}</small></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row no-print">
    <div class="col-12">
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    new Chart(document.getElementById('chartTipo').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($dataTipo->pluck('tipo')) !!},
            datasets: [{ label: 'Cantidad', data: {!! json_encode($dataTipo->pluck('total')) !!}, backgroundColor: '#dc3545' }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    new Chart(document.getElementById('chartEstado').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($dataEstado->pluck('estado')) !!},
            datasets: [{ data: {!! json_encode($dataEstado->pluck('total')) !!}, backgroundColor: ['#28a745', '#ffc107', '#6c757d', '#dc3545'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(16);
        doc.text('Reporte: Flota de Vehículos', 14, 20);
        doc.setFontSize(10);
        doc.text('Total: {{ $total }} vehículos | Capacidad: {{ number_format($capacidadTotal, 0) }} kg', 14, 28);
        
        doc.autoTable({
            startY: 36,
            head: [['Placa', 'Tipo', 'Capacidad', 'Estado']],
            body: [
                @foreach($vehiculos as $v)
                ['{{ $v->placa }}', '{{ $v->tipo }}', '{{ number_format($v->capacidad, 0) }} kg', '{{ $v->estado }}'],
                @endforeach
            ],
            theme: 'striped'
        });
        doc.save('reporte_vehiculos_{{ now()->format("Y-m-d") }}.pdf');
    }

    function exportToExcel() {
        const data = [
            ['Placa', 'Tipo', 'Capacidad (kg)', 'Estado'],
            @foreach($vehiculos as $v)
            ['{{ $v->placa }}', '{{ $v->tipo }}', {{ $v->capacidad }}, '{{ $v->estado }}'],
            @endforeach
        ];
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Vehiculos');
        XLSX.writeFile(wb, 'reporte_vehiculos_{{ now()->format("Y-m-d") }}.xlsx');
    }
</script>
@endpush

@push('css')
<style>@media print { .no-print { display: none !important; } .main-sidebar, .main-header, .main-footer { display: none !important; } .content-wrapper { margin-left: 0 !important; } }</style>
@endpush
