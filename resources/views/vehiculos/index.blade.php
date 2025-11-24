@extends('adminlte::page')

@section('title', 'Vehículos')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-truck text-success"></i> Gestión de Vehículos</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Vehículos</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-gradient-success">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Vehículos</h3>
            <div class="card-tools">
                <a href="{{ route('vehiculos.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Vehículo
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="vehiculos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Capacidad (kg)</th>
                        <th>Tipo de Vehículo</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehiculos as $vehiculo)
                    <tr>
                        <td>{{ $vehiculo->id }}</td>
                        <td><strong>{{ $vehiculo->placa }}</strong></td>
                        <td>{{ number_format($vehiculo->capacidad, 2) }}</td>
                        <td>{{ $vehiculo->tipoVehiculo->nombre }}</td>
                        <td>
                            <span class="badge badge-{{ $vehiculo->estadoVehiculo->nombre == 'Disponible' ? 'success' : ($vehiculo->estadoVehiculo->nombre == 'En ruta' ? 'warning' : 'secondary') }}">
                                {{ $vehiculo->estadoVehiculo->nombre }}
                            </span>
                        </td>
                        <td>{{ $vehiculo->fecha_registro ? \Carbon\Carbon::parse($vehiculo->fecha_registro)->format('d/m/Y H:i') : '-' }}</td>
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
    <style>
        .card {
            border-radius: 10px;
            border: none;
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
        .table thead th {
            background: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .badge {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .btn-sm {
            padding: 4px 10px;
        }
    </style>
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
