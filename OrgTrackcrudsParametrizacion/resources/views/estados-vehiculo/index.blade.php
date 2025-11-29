@extends('adminlte::page')

@section('title', 'Estados de Vehículo')

@section('content_header')
    <h1>Estados de Vehículo</h1>
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
            <h3 class="card-title">Lista de Estados de Vehículo</h3>
            <div class="card-tools">
                <a href="{{ route('estados-vehiculo.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Estado
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estadosVehiculo as $estado)
                    <tr>
                        <td>{{ $estado->id }}</td>
                        <td>{{ $estado->nombre }}</td>
                        <td>
                            <a href="{{ route('estados-vehiculo.edit', $estado->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('estados-vehiculo.destroy', $estado->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro?')">
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
