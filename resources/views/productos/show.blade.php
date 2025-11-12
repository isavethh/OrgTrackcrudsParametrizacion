@extends('adminlte::page')

@section('title', 'Ver Producto')

@section('content_header')
    <h1>Detalle del Producto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $producto->nombre }}</h3>
            <div class="card-tools">
                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">ID</th>
                            <td>{{ $producto->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre</th>
                            <td>{{ $producto->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Categoría</th>
                            <td>
                                <span class="badge badge-{{ $producto->categoria == 'Frutas' ? 'success' : 'warning' }}">
                                    {{ $producto->categoria }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Peso por Unidad</th>
                            <td>{{ number_format($producto->peso_por_unidad, 3) }} kg</td>
                        </tr>
                        <tr>
                            <th>Descripción</th>
                            <td>{{ $producto->descripcion ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Creación</th>
                            <td>{{ $producto->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización</th>
                            <td>{{ $producto->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
