@extends('layouts.adminlte')

@section('page-title', 'Reporte: Tamaño-Conteo')

@section('page-content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Tamaño-Conteo</li>
            </ol>
        </nav>
    </div>
</div>

<div class="callout callout-warning no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Catálogo de clasificaciones de tamaño con conteo por empaque para estimación de peso, planificación de carga y gestión de inventario.</p>
</div>

<div class="card card-warning card-outline no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Opciones</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-danger mr-2" onclick="exportToPDF()"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
                <button type="button" class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel mr-1"></i> Excel</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $total }}</h3>
                <p>Total Clasificaciones</p>
            </div>
            <div class="icon"><i class="fas fa-ruler"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $activos }}</h3>
                <p>Activos</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $total - $activos }}</h3>
                <p>Inactivos</p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $activos > 0 ? number_format(($activos / $total) * 100, 0) : 0 }}%</h3>
                <p>Tasa de Actividad</p>
            </div>
            <div class="icon"><i class="fas fa-percentage"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Conteo por Empaque</h3>
            </div>
            <div class="card-body">
                <canvas id="chartTamano" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table mr-2"></i>Resumen por Clasificación</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="text-center">Conteo</th>
                            <th class="text-center">Peso Unit.</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $t)
                        <tr>
                            <td><span class="badge badge-warning">{{ $t->nombre }}</span></td>
                            <td class="text-center"><strong>{{ $t->conteo_por_empaque }}</strong></td>
                            <td class="text-center">{{ number_format($t->peso_promedio_unidad, 3) }} kg</td>
                            <td class="text-center">
                                @if($t->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-center">{{ $total }} clasificaciones</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-weight mr-2"></i>Detalle Completo de Tamaño-Conteo</h3>
        <div class="card-tools">
            <span class="badge badge-warning">{{ $total }} registros</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover" id="tabla-tamano">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Clasificación</th>
                    <th>Producto</th>
                    <th class="text-center">Conteo por Empaque</th>
                    <th class="text-center">Peso Promedio Unidad (kg)</th>
                    <th class="text-center">Peso Total Empaque (kg)</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $t)
                @php 
                    $pesoTotal = $t->conteo_por_empaque * $t->peso_promedio_unidad;
                @endphp
                <tr>
                    <td><code>{{ $t->id }}</code></td>
                    <td><strong>{{ $t->nombre }}</strong></td>
                    <td>{{ $t->producto ?? '-' }}</td>
                    <td class="text-center"><span class="badge badge-info badge-pill">{{ $t->conteo_por_empaque }}</span></td>
                    <td class="text-center">{{ number_format($t->peso_promedio_unidad, 3) }}</td>
                    <td class="text-center"><span class="badge badge-primary">{{ number_format($pesoTotal, 2) }}</span></td>
                    <td class="text-center">
                        @if($t->activo)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-secondary">Inactivo</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay clasificaciones registradas</td></tr>
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
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    new Chart(document.getElementById('chartTamano').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($data->pluck('nombre')) !!},
            datasets: [{
                label: 'Conteo por Empaque',
                data: {!! json_encode($data->pluck('conteo_por_empaque')) !!},
                backgroundColor: '#ffc107'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(16);
        doc.text('Reporte: Tamaño-Conteo', 14, 20);
        doc.setFontSize(10);
        doc.text('Total: {{ $total }} clasificaciones | Activos: {{ $activos }}', 14, 28);
        
        doc.autoTable({
            startY: 36,
            head: [['Nombre', 'Producto', 'Conteo/Empaque', 'Peso Unid (kg)', 'Peso Total (kg)', 'Estado']],
            body: [
                @foreach($data as $t)
                ['{{ $t->nombre }}', '{{ $t->producto ?? "-" }}', {{ $t->conteo_por_empaque }}, {{ number_format($t->peso_promedio_unidad, 3) }}, {{ number_format($t->conteo_por_empaque * $t->peso_promedio_unidad, 2) }}, '{{ $t->activo ? "Activo" : "Inactivo" }}'],
                @endforeach
            ],
            theme: 'striped'
        });
        doc.save('tamano_conteo_{{ now()->format("Y-m-d") }}.pdf');
    }

    function exportToExcel() {
        const data = [
            ['Nombre', 'Producto', 'Conteo/Empaque', 'Peso Unid (kg)', 'Peso Total (kg)', 'Estado'],
            @foreach($data as $t)
            ['{{ $t->nombre }}', '{{ $t->producto ?? "-" }}', {{ $t->conteo_por_empaque }}, {{ number_format($t->peso_promedio_unidad, 3) }}, {{ number_format($t->conteo_por_empaque * $t->peso_promedio_unidad, 2) }}, '{{ $t->activo ? "Activo" : "Inactivo" }}'],
            @endforeach
        ];
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Tamano Conteo');
        XLSX.writeFile(wb, 'tamano_conteo_{{ now()->format("Y-m-d") }}.xlsx');
    }
</script>
@endpush

@push('css')
<style>@media print { .no-print { display: none !important; } .main-sidebar, .main-header, .main-footer { display: none !important; } .content-wrapper { margin-left: 0 !important; } }</style>
@endpush
