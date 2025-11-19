@extends('adminlte::page')

@section('title', 'Vehículos')

@section('content_header')
    <h1>Gestión de Vehículos</h1>
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
            <h3 class="card-title">Lista de Vehículos</h3>
            <div class="card-tools">
                <a href="{{ route('vehiculos.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Vehículo
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="vehiculos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Admin</th>
                        <th>Tipo Transporte</th>
                        <th>Tamaño</th>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehiculos as $vehiculo)
                    <tr>
                        <td>{{ $vehiculo->id }}</td>
                        <td>{{ $vehiculo->admin ? $vehiculo->admin->usuario->nombre : '-' }}</td>
                        <td>{{ $vehiculo->tipoTransporte->nombre }}</td>
                        <td>{{ $vehiculo->tamanoTransporte->nombre }}</td>
                        <td><strong>{{ $vehiculo->placa }}</strong></td>
                        <td>{{ $vehiculo->marca ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $vehiculo->estado == 'Disponible' ? 'success' : ($vehiculo->estado == 'En ruta' ? 'warning' : 'secondary') }}">
                                {{ $vehiculo->estado }}
                            </span>
                        </td>
                        <td>{{ $vehiculo->fecha_registro ? $vehiculo->fecha_registro->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vehiculos.destroy', $vehiculo) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este vehículo?')">
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
            $('#vehiculos-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop
