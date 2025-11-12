@extends('adminlte::page')

@section('title', 'Detalles del Cliente')

@section('content_header')
    <h1>Detalles del Cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Información del Cliente</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID:</th>
                            <td>{{ $cliente->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $cliente->usuario->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Apellido:</th>
                            <td>{{ $cliente->usuario->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Correo Electrónico:</th>
                            <td>{{ $cliente->usuario->correo }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $cliente->telefono ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dirección de Entrega:</th>
                            <td>{{ $cliente->direccion_entrega ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $cliente->usuario->fecha_registro ? $cliente->usuario->fecha_registro->format('d/m/Y H:i:s') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
@stop

