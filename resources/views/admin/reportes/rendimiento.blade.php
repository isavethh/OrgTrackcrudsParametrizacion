@extends('adminlte::page')

@section('title', 'Reporte de Rendimiento')

@section('content_header')
    <h1>Rendimiento de Transporte</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.rendimiento') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">Filtrar</button>
                        <button type="submit" name="export" value="pdf" class="btn btn-danger mr-2"><i class="fas fa-file-pdf"></i> PDF</button>
                        <button type="submit" name="export" value="excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Resultados</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Transportista</th>
                        <th>Envíos Entregados</th>
                        <th>Tiempo Promedio (Días)</th>
                        <th>Eficiencia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td>{{ $row->nombre }} {{ $row->apellido }}</td>
                            <td>{{ $row->entregas_totales }}</td>
                            <td>{{ number_format($row->promedio_dias, 1) }}</td>
                            <td>
                                @php
                                    $eff = $row->promedio_dias < 2 ? 100 : ($row->promedio_dias < 4 ? 80 : 50);
                                    $color = $eff > 90 ? 'success' : ($eff > 60 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ $eff }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay datos para este rango.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
