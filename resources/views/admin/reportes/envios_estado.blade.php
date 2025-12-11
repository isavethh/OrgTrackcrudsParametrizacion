@extends('layouts.adminlte')

@section('page-title', 'Reporte: Análisis de Envíos por Estado')

@section('page-content')
<!-- Breadcrumb -->
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Envíos por Estado</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Info Box -->
<div class="callout callout-info no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Este reporte permite visualizar la distribución de envíos por estado para identificar cuellos de botella, medir la eficiencia operativa y detectar envíos pendientes que requieren atención.</p>
</div>

<!-- Filtros -->
<div class="card card-primary card-outline no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros de Búsqueda</h3>
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
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search mr-1"></i> Aplicar Filtros
                    </button>
                    <button type="button" class="btn btn-danger mr-2" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- KPIs -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalEnvios) }}</h3>
                <p>Total Envíos en Período</p>
            </div>
            <div class="icon"><i class="fas fa-shipping-fast"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-{{ $variacion >= 0 ? 'success' : 'danger' }}">
            <div class="inner">
                <h3>{{ $variacion >= 0 ? '+' : '' }}{{ $variacion }}%</h3>
                <p>vs. Período Anterior ({{ $totalAnterior }})</p>
            </div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $data->count() }}</h3>
                <p>Estados Diferentes</p>
            </div>
            <div class="icon"><i class="fas fa-tags"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-{{ $tasaCancelacion > 10 ? 'danger' : 'success' }}">
            <div class="inner">
                <h3>{{ $tasaCancelacion }}%</h3>
                <p>Tasa de Cancelación</p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>
</div>

<!-- Contenido Principal -->
<div class="row" id="report-content">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Distribución Gráfica</h3>
            </div>
            <div class="card-body">
                <canvas id="chartEstados" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table mr-2"></i>Detalle por Estado</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-hover" id="tabla-estados">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Cancelados</th>
                            <th class="text-center">Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                        @php $percent = $totalEnvios > 0 ? ($row->total / $totalEnvios) * 100 : 0; @endphp
                        <tr>
                            <td>
                                <span class="badge badge-{{ $row->estado == 'Entregado' ? 'success' : ($row->estado == 'Cancelado' ? 'danger' : 'primary') }} badge-pill">
                                    {{ $row->estado }}
                                </span>
                            </td>
                            <td class="text-center"><strong>{{ number_format($row->total) }}</strong></td>
                            <td class="text-center">{{ $row->cancelados ?? 0 }}</td>
                            <td class="text-center">
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                                </div>
                                <small>{{ number_format($percent, 1) }}%</small>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">No hay datos</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-center">{{ number_format($totalEnvios) }}</th>
                            <th class="text-center">{{ $totalCancelados }}</th>
                            <th class="text-center">100%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Botón Volver -->
<div class="row no-print">
    <div class="col-12">
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-default">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Centro de Reportes
        </a>
    </div>
</div>

<!-- Sección para impresión -->
<div class="d-none d-print-block">
    <h2>Reporte: Análisis de Envíos por Estado</h2>
    <p><strong>Período:</strong> {{ $fechaInicio }} al {{ $fechaFin }}</p>
    <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    <p><strong>Total Envíos:</strong> {{ $totalEnvios }} | <strong>Tasa Cancelación:</strong> {{ $tasaCancelacion }}%</p>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    // Gráfico
    const ctx = document.getElementById('chartEstados').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($data->pluck('estado')) !!},
            datasets: [{
                data: {!! json_encode($data->pluck('total')) !!},
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Exportar PDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.setFontSize(18);
        doc.text('Reporte: Envíos por Estado', 14, 22);
        doc.setFontSize(11);
        doc.text('Período: {{ $fechaInicio }} al {{ $fechaFin }}', 14, 30);
        doc.text('Generado: {{ now()->format("d/m/Y H:i") }}', 14, 36);
        doc.text('Total Envíos: {{ $totalEnvios }} | Tasa Cancelación: {{ $tasaCancelacion }}%', 14, 42);
        
        doc.autoTable({
            startY: 50,
            head: [['Estado', 'Cantidad', 'Cancelados', 'Porcentaje']],
            body: [
                @foreach($data as $row)
                ['{{ $row->estado }}', '{{ $row->total }}', '{{ $row->cancelados ?? 0 }}', '{{ $totalEnvios > 0 ? number_format(($row->total / $totalEnvios) * 100, 1) : 0 }}%'],
                @endforeach
            ],
            foot: [['TOTAL', '{{ $totalEnvios }}', '{{ $totalCancelados }}', '100%']],
            theme: 'striped'
        });
        
        doc.save('reporte_envios_estado_{{ now()->format("Y-m-d") }}.pdf');
    }

    // Exportar Excel
    function exportToExcel() {
        const data = [
            ['Estado', 'Cantidad', 'Cancelados', 'Porcentaje'],
            @foreach($data as $row)
            ['{{ $row->estado }}', {{ $row->total }}, {{ $row->cancelados ?? 0 }}, '{{ $totalEnvios > 0 ? number_format(($row->total / $totalEnvios) * 100, 1) : 0 }}%'],
            @endforeach
            ['TOTAL', {{ $totalEnvios }}, {{ $totalCancelados }}, '100%']
        ];
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Envios por Estado');
        XLSX.writeFile(wb, 'reporte_envios_estado_{{ now()->format("Y-m-d") }}.xlsx');
    }
</script>
@endpush

@push('css')
<style>
    @media print {
        .no-print { display: none !important; }
        .main-sidebar, .main-header, .main-footer { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
    }
</style>
@endpush
