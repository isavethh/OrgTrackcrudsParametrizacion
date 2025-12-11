@extends('layouts.adminlte')

@section('page-title', 'Reporte: Catálogo de Productos')

@section('page-content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                <li class="breadcrumb-item active">Catálogo de Productos</li>
            </ol>
        </nav>
    </div>
</div>

<div class="callout callout-info no-print">
    <h5><i class="fas fa-info-circle"></i> Propósito del Reporte</h5>
    <p class="mb-0">Inventario completo de productos organizados por categoría para gestión del catálogo, identificación de productos sin clasificar y análisis de la oferta disponible.</p>
</div>

<div class="card card-info card-outline no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="categoria" class="form-control">
                        <option value="">-- Todas las categorías --</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ $categoriaFiltro == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-info mr-2"><i class="fas fa-search mr-1"></i> Filtrar</button>
                    <button type="button" class="btn btn-danger mr-2" onclick="exportToPDF()"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel mr-1"></i> Excel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalProductos }}</h3>
                <p>Total Productos</p>
            </div>
            <div class="icon"><i class="fas fa-boxes"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $totalCategorias }}</h3>
                <p>Categorías</p>
            </div>
            <div class="icon"><i class="fas fa-folder"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-{{ $sinCategoria > 0 ? 'warning' : 'success' }}">
            <div class="inner">
                <h3>{{ $sinCategoria }}</h3>
                <p>Sin Categoría</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

@foreach($dataGrouped as $categoria => $productos)
<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-folder-open mr-2"></i>{{ $categoria ?? 'Sin Categoría' }}
            <span class="badge badge-info ml-2">{{ $productos->count() }} productos</span>
        </h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Descripción</th>
                    <th class="text-center">Peso Promedio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $p)
                <tr>
                    <td><code>{{ $p->id }}</code></td>
                    <td><strong>{{ $p->producto }}</strong></td>
                    <td>{{ Str::limit($p->descripcion, 60) ?? '-' }}</td>
                    <td class="text-center">{{ $p->peso_promedio ? $p->peso_promedio . ' kg' : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

@if($dataGrouped->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No hay productos registrados</h4>
    </div>
</div>
@endif

<div class="row no-print">
    <div class="col-12">
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
    </div>
</div>

<div class="d-none d-print-block">
    <h2>Catálogo de Productos</h2>
    <p>Generado: {{ now()->format('d/m/Y H:i') }} | Total: {{ $totalProductos }} productos en {{ $totalCategorias }} categorías</p>
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
        doc.text('Catálogo de Productos', 14, 20);
        doc.setFontSize(10);
        doc.text('Generado: {{ now()->format("d/m/Y H:i") }} | Total: {{ $totalProductos }} productos', 14, 28);
        
        doc.autoTable({
            startY: 36,
            head: [['Categoría', 'Producto', 'Descripción', 'Peso']],
            body: [
                @foreach($data as $p)
                ['{{ $p->categoria ?? "Sin Categoría" }}', '{{ $p->producto }}', '{{ Str::limit($p->descripcion, 40) ?? "-" }}', '{{ $p->peso_promedio ? $p->peso_promedio . " kg" : "-" }}'],
                @endforeach
            ],
            theme: 'striped'
        });
        doc.save('catalogo_productos_{{ now()->format("Y-m-d") }}.pdf');
    }

    function exportToExcel() {
        const data = [
            ['Categoría', 'Producto', 'Descripción', 'Peso Promedio (kg)'],
            @foreach($data as $p)
            ['{{ $p->categoria ?? "Sin Categoría" }}', '{{ $p->producto }}', '{{ $p->descripcion ?? "-" }}', {{ $p->peso_promedio ?? 0 }}],
            @endforeach
        ];
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Productos');
        XLSX.writeFile(wb, 'catalogo_productos_{{ now()->format("Y-m-d") }}.xlsx');
    }
</script>
@endpush

@push('css')
<style>@media print { .no-print { display: none !important; } .main-sidebar, .main-header, .main-footer { display: none !important; } .content-wrapper { margin-left: 0 !important; } }</style>
@endpush
