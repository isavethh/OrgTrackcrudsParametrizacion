@extends('layouts.adminlte')

@section('page-title', 'Reporte: Usuarios por Rol')

@section('page-content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Usuarios por Rol</li>
            </ol>
        </nav>
    </div>
</div>

<div class="callout callout-warning no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Control de accesos y seguridad del sistema. Permite visualizar la distribución de usuarios por rol para gestión de permisos y planificación de recursos.</p>
</div>

<div class="card card-warning card-outline no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Rol</label>
                    <select name="rol" class="form-control">
                        <option value="">-- Todos los roles --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ $rolFiltro == $r->id ? 'selected' : '' }}>{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-warning mr-2"><i class="fas fa-search mr-1"></i> Filtrar</button>
                    <button type="button" class="btn btn-danger mr-2" onclick="exportToPDF()"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel mr-1"></i> Excel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Distribución por Rol</h3>
            </div>
            <div class="card-body">
                <canvas id="chartRoles" style="height: 250px;"></canvas>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table mr-2"></i>Resumen por Rol</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rol</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Nuevos (30d)</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td><span class="badge badge-warning">{{ $row->rol }}</span></td>
                            <td class="text-center"><strong>{{ $row->total }}</strong></td>
                            <td class="text-center"><span class="badge badge-success">+{{ $row->nuevos_30d }}</span></td>
                            <td class="text-center">{{ $total > 0 ? number_format(($row->total / $total) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-center">{{ $total }}</th>
                            <th class="text-center">{{ $data->sum('nuevos_30d') }}</th>
                            <th class="text-center">100%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Detalle de Usuarios</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ $usuarios->count() }} mostrados</span>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 500px;">
                <table class="table table-sm table-hover" id="tabla-usuarios">
                    <thead class="thead-light">
                        <tr>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $u)
                        <tr>
                            <td>{{ $u->nombre ?? '' }} {{ $u->apellido ?? '' }}</td>
                            <td><small>{{ $u->correo }}</small></td>
                            <td><span class="badge badge-secondary">{{ $u->rol }}</span></td>
                            <td><small>{{ $u->fecha_registro ? \Carbon\Carbon::parse($u->fecha_registro)->format('d/m/Y') : '-' }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
    new Chart(document.getElementById('chartRoles').getContext('2d'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($data->pluck('rol')) !!},
            datasets: [{
                data: {!! json_encode($data->pluck('total')) !!},
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545', '#6c757d']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(16);
        doc.text('Reporte: Usuarios por Rol', 14, 20);
        doc.setFontSize(10);
        doc.text('Generado: {{ now()->format("d/m/Y H:i") }} | Total: {{ $total }} usuarios', 14, 28);
        
        doc.autoTable({
            startY: 36,
            head: [['Rol', 'Total', 'Nuevos 30d', '%']],
            body: [
                @foreach($data as $row)
                ['{{ $row->rol }}', {{ $row->total }}, {{ $row->nuevos_30d }}, '{{ $total > 0 ? number_format(($row->total / $total) * 100, 1) : 0 }}%'],
                @endforeach
            ],
            theme: 'striped'
        });
        doc.save('reporte_usuarios_rol_{{ now()->format("Y-m-d") }}.pdf');
    }

    function exportToExcel() {
        const data = [
            ['Rol', 'Total', 'Nuevos 30d', 'Porcentaje'],
            @foreach($data as $row)
            ['{{ $row->rol }}', {{ $row->total }}, {{ $row->nuevos_30d }}, '{{ $total > 0 ? number_format(($row->total / $total) * 100, 1) : 0 }}%'],
            @endforeach
        ];
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Usuarios por Rol');
        XLSX.writeFile(wb, 'reporte_usuarios_rol_{{ now()->format("Y-m-d") }}.xlsx');
    }
</script>
@endpush

@push('css')
<style>@media print { .no-print { display: none !important; } .main-sidebar, .main-header, .main-footer { display: none !important; } .content-wrapper { margin-left: 0 !important; } }</style>
@endpush
