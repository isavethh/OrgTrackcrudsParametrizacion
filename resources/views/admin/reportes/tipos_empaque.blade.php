@extends('layouts.adminlte')

@section('page-title', 'Reporte: Tipos de Empaque')

@section('page-content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Tipos de Empaque</li>
            </ol>
        </nav>
    </div>
</div>

<div class="callout callout-info no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Gestión del inventario de empaques disponibles con dimensiones, capacidad, tara y especificaciones técnicas para planificación de carga y logística.</p>
</div>

<div class="card card-info card-outline no-print">
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
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $total }}</h3>
                <p>Total Tipos de Empaque</p>
            </div>
            <div class="icon"><i class="fas fa-box"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $conDimensiones }}</h3>
                <p>Con Dimensiones Completas</p>
            </div>
            <div class="icon"><i class="fas fa-ruler-combined"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $total - $conDimensiones }}</h3>
                <p>Sin Dimensiones</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $conDimensiones > 0 ? number_format(($conDimensiones / $total) * 100, 0) : 0 }}%</h3>
                <p>Completitud</p>
            </div>
            <div class="icon"><i class="fas fa-percentage"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Distribución por Volumen</h3>
            </div>
            <div class="card-body">
                <canvas id="chartEmpaque" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table mr-2"></i>Resumen de Empaques</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="text-center">Dimensiones</th>
                            <th class="text-center">Capacidad</th>
                            <th class="text-center">Volumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $emp)
                        <tr>
                            <td><span class="badge badge-info">{{ $emp->nombre }}</span></td>
                            <td class="text-center"><small>{{ $emp->largo ?? '-' }}x{{ $emp->ancho ?? '-' }}x{{ $emp->alto ?? '-' }}</small></td>
                            <td class="text-center"><strong>{{ $emp->capacidad ?? '-' }}</strong></td>
                            <td class="text-center">{{ $emp->volumen }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-center">{{ $total }} tipos</th>
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
        <h3 class="card-title"><i class="fas fa-box-open mr-2"></i>Detalle Completo de Empaques</h3>
        <div class="card-tools">
            <span class="badge badge-info">{{ $total }} registros</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover" id="tabla-empaques">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">L (cm)</th>
                    <th class="text-center">A (cm)</th>
                    <th class="text-center">H (cm)</th>
                    <th class="text-center">Volumen</th>
                    <th class="text-center">Tara (kg)</th>
                    <th class="text-center">Capacidad</th>
                    <th class="text-center">Unid/Pallet</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $emp)
                <tr>
                    <td><code>{{ $emp->id }}</code></td>
                    <td><strong>{{ $emp->nombre }}</strong></td>
                    <td><small>{{ Str::limit($emp->descripcion, 30) ?? '-' }}</small></td>
                    <td class="text-center">{{ $emp->largo ?? '-' }}</td>
                    <td class="text-center">{{ $emp->ancho ?? '-' }}</td>
                    <td class="text-center">{{ $emp->alto ?? '-' }}</td>
                    <td class="text-center"><span class="badge badge-info">{{ $emp->volumen }}</span></td>
                    <td class="text-center">{{ $emp->tara ?? '-' }}</td>
                    <td class="text-center"><span class="badge badge-primary">{{ $emp->capacidad ?? '-' }}</span></td>
                    <td class="text-center"><span class="badge badge-secondary">{{ $emp->unidades_por_pallet ?? '-' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No hay tipos de empaque registrados</td></tr>
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
    new Chart(document.getElementById('chartEmpaque').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($data->pluck('nombre')) !!},
            datasets: [{
                label: 'Capacidad',
                data: {!! json_encode($data->pluck('capacidad')) !!},
                backgroundColor: '#17a2b8'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');
        doc.setFontSize(16);
        doc.text('Reporte: Tipos de Empaque', 14, 20);
        doc.setFontSize(10);
        doc.text('Total: {{ $total }} tipos | Generado: {{ now()->format("d/m/Y H:i") }}', 14, 28);
        
        doc.autoTable({
            startY: 36,
            head: [['Nombre', 'Largo', 'Ancho', 'Alto', 'Volumen', 'Tara', 'Capacidad', 'Unid/Pallet']],
            body: [
                @foreach($data as $emp)
                ['{{ $emp->nombre }}', '{{ $emp->largo ?? "-" }}', '{{ $emp->ancho ?? "-" }}', '{{ $emp->alto ?? "-" }}', '{{ $emp->volumen }}', '{{ $emp->tara ?? "-" }}', '{{ $emp->capacidad ?? "-" }}', '{{ $emp->unidades_por_pallet ?? "-" }}'],
                @endforeach
            ],
            theme: 'striped'
        });
        doc.save('tipos_empaque_{{ now()->format("Y-m-d") }}.pdf');
    }

    function exportToExcel() {
        const data = [
            ['Nombre', 'Descripción', 'Largo (cm)', 'Ancho (cm)', 'Alto (cm)', 'Tara (kg)', 'Capacidad', 'Unid/Pallet'],
            @foreach($data as $emp)
            ['{{ $emp->nombre }}', '{{ $emp->descripcion ?? "-" }}', {{ $emp->largo ?? 0 }}, {{ $emp->ancho ?? 0 }}, {{ $emp->alto ?? 0 }}, {{ $emp->tara ?? 0 }}, {{ $emp->capacidad ?? 0 }}, {{ $emp->unidades_por_pallet ?? 0 }}],
            @endforeach
        ];
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Tipos Empaque');
        XLSX.writeFile(wb, 'tipos_empaque_{{ now()->format("Y-m-d") }}.xlsx');
    }
</script>
@endpush

@push('css')
<style>@media print { .no-print { display: none !important; } .main-sidebar, .main-header, .main-footer { display: none !important; } .content-wrapper { margin-left: 0 !important; } }</style>
@endpush
