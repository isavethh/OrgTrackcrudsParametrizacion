@extends('adminlte::page')

@section('title', 'Direcciones')

@section('content_header')
    <h1>Gestión de Direcciones</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Direcciones</h3>
            <div class="card-tools">
                <a href="{{ route('direcciones.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nueva Dirección
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="direcciones-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Coordenadas Origen</th>
                        <th>Coordenadas Destino</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($direcciones as $direccion)
                    <tr>
                        <td>{{ $direccion->id }}</td>
                        <td>
                            <i class="fas fa-map-marker-alt text-success"></i> 
                            <strong>{{ $direccion->nombreorigen }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-flag-checkered text-info"></i> 
                            <strong>{{ $direccion->nombredestino }}</strong>
                        </td>
                        <td>
                            <small>
                                Lat: {{ number_format($direccion->origen_lat, 6) }}<br>
                                Lng: {{ number_format($direccion->origen_lng, 6) }}
                            </small>
                        </td>
                        <td>
                            <small>
                                Lat: {{ number_format($direccion->destino_lat, 6) }}<br>
                                Lng: {{ number_format($direccion->destino_lng, 6) }}
                            </small>
                        </td>
                        <td>
                            <a href="{{ route('direcciones.edit', $direccion) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('direcciones.destroy', $direccion) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta dirección?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#direcciones-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop
