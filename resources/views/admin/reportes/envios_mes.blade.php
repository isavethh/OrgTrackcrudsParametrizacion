@extends('layouts.adminlte')

@section('page-title', 'Reporte: Tendencia Mensual de Envíos')

@section('page-content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Tendencia Mensual</li>
            </ol>
        </nav>
    </div>
</div>

<div class="callout callout-secondary no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Análisis de estacionalidad y proyección de demanda. Identifica picos operativos para planificación de recursos y detección de tendencias.</p>
</div>

<div class="card card-secondary card-outline no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Año</label>
                    <select name="year" class="form-control">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-9 d-flex align-items-end">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-secondary mr-2"><i class="fas fa-search mr-1"></i> Filtrar</button>
                    <button type="button" class="btn btn-danger mr-2" onclick="exportToPDF()"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel mr-1"></i> Excel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ number_format($totalAnual) }}</h3>
                <p>Total Envíos {{ $year }}</p>
            </div>
            <div class="icon"><i class="fas fa-shipping-fast"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-{{ $crecimiento >= 0 ? 'success' : 'danger' }}">
            <div class="inner">
                <h3>{{ $crecimiento >= 0 ? '+' : '' }}{{ $crecimiento }}%</h3>
                <p>vs. {{ $yearAnterior }} ({{ $totalAnterior }})</p>
            </div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($promedioMensual) }}</h3>
                <p>Promedio Mensual</p>
            </div>
            <div class="icon"><i class="fas fa-calculator"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $mesMayor ? $meses[$mesMayor->mes - 1] : '-' }}</h3>
                <p>Mes con Mayor Demanda</p>
            </div>
            <div class="icon"><i class="fas fa-trophy"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-area mr-2"></i>Evolución Mensual - {{ $year }}</h3>
    </div>
    <div class="card-body">
        <canvas id="chartMeses" style="height: 300px;"></canvas>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table mr-2"></i>Detalle por Mes</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped" id="tabla-meses">
                    <thead>
                        <tr>
                            <th>Mes</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Cancelados</th>
                            <th class="text-center">Efectivos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $dataMap = $data->keyBy('mes'); @endphp
                        @for($m = 1; $m <= 12; $m++)
                        @php $mesData = $dataMap->get($m); @endphp
                        <tr class="{{ $mesData && $mesData->total > 0 ? '' : 'text-muted' }}">
                            <td>{{ $meses[$m - 1] }}</td>
                            <td class="text-center"><strong>{{ $mesData->total ?? 0 }}</strong></td>
                            <td class="text-center"><span class="badge badge-danger">{{ $mesData->cancelados ?? 0 }}</span></td>
                            <td class="text-center"><span class="badge badge-success">{{ $mesData->efectivos ?? 0 }}</span></td>
                        </tr>
                        @endfor
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-center">{{ $totalAnual }}</th>
                            <th class="text-center">{{ $data->sum('cancelados') }}</th>
                            <th class="text-center">{{ $data->sum('efectivos') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lightbulb mr-2"></i>Análisis</h3>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-arrow-up text-success mr-2"></i>Mes con Mayor Demanda</span>
                        <span class="badge badge-success badge-pill">{{ $mesMayor ? $meses[$mesMayor->mes - 1] . ' (' . $mesMayor->total . ')' : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-arrow-down text-danger mr-2"></i>Mes con Menor Demanda</span>
                        <span class="badge badge-danger badge-pill">{{ $mesMenor ? $meses[$mesMenor->mes - 1] . ' (' . $mesMenor->total . ')' : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-chart-line text-info mr-2"></i>Crecimiento vs Año Anterior</span>
                        <span class="badge badge-{{ $crecimiento >= 0 ? 'success' : 'danger' }} badge-pill">{{ $crecimiento >= 0 ? '+' : '' }}{{ $crecimiento }}%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-percentage text-warning mr-2"></i>Tasa de Cancelación Anual</span>
                        <span class="badge badge-warning badge-pill">{{ $totalAnual > 0 ? number_format(($data->sum('cancelados') / $totalAnual) * 100, 1) : 0 }}%</span>
                    </li>
                </ul>
            </div>
        </div>
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
    const meses = {!! json_encode($meses) !!};
    const dataRaw = {!! json_encode($data->pluck('total', 'mes')->toArray()) !!};
    const dataCancelados = {!! json_encode($data->pluck('cancelados', 'mes')->toArray()) !!};
    const dataValues = meses.map((_, i) => dataRaw[i + 1] || 0);
    const canceladosValues = meses.map((_, i) => dataCancelados[i + 1] || 0);
    
    new Chart(document.getElementById('chartMeses').getContext('2d'), {
        type: 'line',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Total Envíos',
                    data: dataValues,
                    borderColor: '#6c757d',
                    backgroundColor: 'rgba(108, 117, 125, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Cancelados',
                    data: canceladosValues,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(16);
        doc.text('Reporte: Tendencia Mensual de Envíos - {{ $year }}', 14, 20);
        doc.setFontSize(10);
        doc.text('Total: {{ $totalAnual }} envíos | Crecimiento: {{ $crecimiento }}%', 14, 28);
        
        doc.autoTable({
            startY: 36,
            head: [['Mes', 'Total', 'Cancelados', 'Efectivos']],
            body: [
                @php $dataMap = $data->keyBy('mes'); @endphp
                @for($m = 1; $m <= 12; $m++)
                @php $mesData = $dataMap->get($m); @endphp
                ['{{ $meses[$m - 1] }}', {{ $mesData->total ?? 0 }}, {{ $mesData->cancelados ?? 0 }}, {{ $mesData->efectivos ?? 0 }}],
                @endfor
            ],
            foot: [['TOTAL', {{ $totalAnual }}, {{ $data->sum('cancelados') }}, {{ $data->sum('efectivos') }}]],
            theme: 'striped'
        });
        doc.save('reporte_envios_mensual_{{ $year }}.pdf');
    }

    function exportToExcel() {
        const data = [
            ['Mes', 'Total', 'Cancelados', 'Efectivos'],
            @php $dataMap = $data->keyBy('mes'); @endphp
            @for($m = 1; $m <= 12; $m++)
            @php $mesData = $dataMap->get($m); @endphp
            ['{{ $meses[$m - 1] }}', {{ $mesData->total ?? 0 }}, {{ $mesData->cancelados ?? 0 }}, {{ $mesData->efectivos ?? 0 }}],
            @endfor
            ['TOTAL', {{ $totalAnual }}, {{ $data->sum('cancelados') }}, {{ $data->sum('efectivos') }}]
        ];
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Envios Mensual');
        XLSX.writeFile(wb, 'reporte_envios_mensual_{{ $year }}.xlsx');
    }
</script>
@endpush

@push('css')
<style>@media print { .no-print { display: none !important; } .main-sidebar, .main-header, .main-footer { display: none !important; } .content-wrapper { margin-left: 0 !important; } }</style>
@endpush
