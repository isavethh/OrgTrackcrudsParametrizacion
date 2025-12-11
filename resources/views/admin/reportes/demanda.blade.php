@extends('adminlte::page')

@section('title', 'Reporte de Demanda')

@section('content_header')
    <h1>Demanda Geográfica</h1>
@stop

@section('content')
     <div class="row">
        <div class="col-12">
            <div class="alert alert-success">
                <h5><i class="icon fas fa-check"></i> En Construcción</h5>
                Este reporte mostrará el mapa de calor de destinos frecuentes.
            </div>
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
@stop
