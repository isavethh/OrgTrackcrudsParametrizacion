@extends('adminlte::page')

@section('title', 'Detalles del Administrador')

@section('content_header')
    <h1>Detalles del Administrador</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Información del Administrador</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID:</th>
                            <td>{{ $admin->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $admin->usuario->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Apellido:</th>
                            <td>{{ $admin->usuario->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Correo Electrónico:</th>
                            <td>{{ $admin->usuario->correo }}</td>
                        </tr>
                        <tr>
                            <th>Nivel de Acceso:</th>
                            <td>
                                <span class="badge badge-info">Nivel {{ $admin->nivel_acceso }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $admin->usuario->fecha_registro ? $admin->usuario->fecha_registro->format('d/m/Y H:i:s') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('admins.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('admins.edit', $admin->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
@stop

