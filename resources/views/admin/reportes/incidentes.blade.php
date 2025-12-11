@extends('adminlte::page')

@section('title', 'Reporte de Calidad')

@section('content_header')
    <h1>Calidad y Servicio: Incidentes</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i> En Construcción</h5>
                Este reporte mostrará gráficas de incidentes por tipo y ubicación.
            </div>
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
@stop
