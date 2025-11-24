@extends('adminlte::page')

@section('title', 'Envíos')

@section('content_header')
    <h1>Gestión de Envíos</h1>
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
            <h3 class="card-title">Lista de Envíos</h3>
            <div class="card-tools">
                <a href="{{ route('envios.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Envío
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="envios-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Entrega</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($envios as $envio)
                    <tr>
                        <td><strong>#{{ $envio->id }}</strong></td>
                        <td>
                            @if($envio->usuario && $envio->usuario->persona)
                                {{ $envio->usuario->persona->nombre }} {{ $envio->usuario->persona->apellido }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($envio->direccion)
                                <i class="fas fa-map-marker-alt text-success"></i> {{ $envio->direccion->nombreorigen }}
                                <br>
                                <i class="fas fa-flag-checkered text-info"></i> {{ $envio->direccion->nombredestino }}
                            @else
                                <span class="text-danger">Sin dirección asignada</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $estadoActual = $envio->historialEstados->first();
                            @endphp
                            @if($estadoActual && $estadoActual->estadoEnvio)
                                <span class="badge badge-{{
                                    $estadoActual->estadoEnvio->nombre == 'Entregado' ? 'success' :
                                    ($estadoActual->estadoEnvio->nombre == 'En curso' || $estadoActual->estadoEnvio->nombre == 'En tránsito' ? 'primary' :
                                    ($estadoActual->estadoEnvio->nombre == 'Pendiente' ? 'warning' : 'secondary'))
                                }}">
                                    {{ $estadoActual->estadoEnvio->nombre }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Sin estado</span>
                            @endif
                        </td>
                        <td>{{ $envio->fecha_creacion ? \Carbon\Carbon::parse($envio->fecha_creacion)->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $envio->fecha_inicio ? \Carbon\Carbon::parse($envio->fecha_inicio)->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $envio->fecha_entrega ? \Carbon\Carbon::parse($envio->fecha_entrega)->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('envios.edit', $envio) }}" class="btn btn-primary btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('envios.destroy', $envio) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este envío?')" title="Eliminar">
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
            $('#envios-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                order: [[0, 'desc']]
            });
        });
    </script>
@stop
