@extends('layouts.adminlte')

@section('page-title', 'Reporte: Rendimiento de Transportistas')

@section('page-content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Rendimiento Transportistas</li>
            </ol>
        </nav>
    </div>
</div>

<div class="callout callout-success no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Evaluar la productividad de cada transportista para identificar empleados destacados, detectar necesidades de capacitación y optimizar la asignación de recursos.</p>
</div>

<div class="card card-success card-outline no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-success mr-2"><i class="fas fa-search mr-1"></i> Filtrar</button>
                    <button type="button" class="btn btn-danger mr-2" onclick="exportToPDF()"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel mr-1"></i> Excel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- KPIs -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalTransportistas }}</h3>
                <p>Transportistas Activos</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ number_format($totalAsignaciones) }}</h3>
                <p>Total Asignaciones</p>
            </div>
            <div class="icon"><i class="fas fa-tasks"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $promedioAsignaciones }}</h3>
                <p>Promedio por Transportista</p>
            </div>
            <div class="icon"><i class="fas fa-calculator"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-{{ $eficienciaGlobal >= 80 ? 'success' : ($eficienciaGlobal >= 50 ? 'warning' : 'danger') }}">
            <div class="inner">
                <h3>{{ $eficienciaGlobal }}%</h3>
                <p>Eficiencia Global</p>
            </div>
            <div class="icon"><i class="fas fa-percentage"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-trophy mr-2"></i>Ranking de Transportistas</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover" id="tabla-transportistas">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Transportista</th>
                    <th class="text-center">CI</th>
                    <th class="text-center">Asignaciones</th>
                    <th class="text-center">Completados</th>
                    <th class="text-center">Cancelados</th>
                    <th class="text-center">Eficiencia</th>
                    <th style="width: 150px;">Rendimiento</th>
                </tr>
            </thead>
            <tbody>
                @php $max = $data->max('total_asignaciones') ?: 1; @endphp
                @forelse($data as $i => $row)
                <tr>
                    <td>
                        @if($i == 0)
                            <span class="badge badge-warning"><i class="fas fa-trophy"></i> 1°</span>
                        @elseif($i == 1)
                            <span class="badge badge-secondary">2°</span>
                        @elseif($i == 2)
                            <span class="badge badge-dark">3°</span>
                        @else
                            <span class="text-muted">{{ $i + 1 }}°</span>
                        @endif
                    </td>
                    <td><strong>{{ $row->nombre }} {{ $row->apellido }}</strong></td>
                    <td class="text-center"><code>{{ $row->ci ?? 'N/A' }}</code></td>
                    <td class="text-center"><span class="badge badge-primary">{{ $row->total_asignaciones }}</span></td>
                    <td class="text-center"><span class="badge badge-success">{{ $row->completados }}</span></td>
                    <td class="text-center"><span class="badge badge-danger">{{ $row->cancelados }}</span></td>
                    <td class="text-center">
                        <span class="badge badge-{{ $row->eficiencia >= 80 ? 'success' : ($row->eficiencia >= 50 ? 'warning' : 'danger') }}">
                            {{ $row->eficiencia }}%
                        </span>
                    </td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: {{ ($row->total_asignaciones / $max) * 100 }}%">
                                {{ number_format(($row->total_asignaciones / $max) * 100, 0) }}%
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4">No hay transportistas con asignaciones en este período</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row no-print">
    <div class="col-12">
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
    </div>
</div>

<div class="d-none d-print-block">
    <h2>Reporte: Rendimiento de Transportistas</h2>
    <p>Período: {{ $fechaInicio }} al {{ $fechaFin }} | Generado: {{ now()->format('d/m/Y H:i') }}</p>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(16);
        doc.text('Reporte: Rendimiento de Transportistas', 14, 20);
        doc.setFontSize(10);
        doc.text('Período: {{ $fechaInicio }} al {{ $fechaFin }} | Generado: {{ now()->format("d/m/Y H:i") }}', 14, 28);
        doc.text('Transportistas: {{ $totalTransportistas }} | Eficiencia Global: {{ $eficienciaGlobal }}%', 14, 34);
        
        doc.autoTable({
            startY: 42,
            head: [['#', 'Transportista', 'CI', 'Asignaciones', 'Completados', 'Cancelados', 'Eficiencia']],
            body: [
                @foreach($data as $i => $row)
                [{{ $i + 1 }}, '{{ $row->nombre }} {{ $row->apellido }}', '{{ $row->ci ?? "N/A" }}', {{ $row->total_asignaciones }}, {{ $row->completados }}, {{ $row->cancelados }}, '{{ $row->eficiencia }}%'],
                @endforeach
            ],
            theme: 'striped'
        });
        doc.save('reporte_transportistas_{{ now()->format("Y-m-d") }}.pdf');
    }

    function exportToExcel() {
        const data = [
            ['#', 'Transportista', 'CI', 'Asignaciones', 'Completados', 'Cancelados', 'Eficiencia'],
            @foreach($data as $i => $row)
            [{{ $i + 1 }}, '{{ $row->nombre }} {{ $row->apellido }}', '{{ $row->ci ?? "N/A" }}', {{ $row->total_asignaciones }}, {{ $row->completados }}, {{ $row->cancelados }}, '{{ $row->eficiencia }}%'],
            @endforeach
        ];
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Transportistas');
        XLSX.writeFile(wb, 'reporte_transportistas_{{ now()->format("Y-m-d") }}.xlsx');
    }
</script>
@endpush

@push('css')
<style>@media print { .no-print { display: none !important; } .main-sidebar, .main-header, .main-footer { display: none !important; } .content-wrapper { margin-left: 0 !important; } }</style>
@endpush
