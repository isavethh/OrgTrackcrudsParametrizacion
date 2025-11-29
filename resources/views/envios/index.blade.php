@extends('adminlte::page')

@section('title', 'Envíos')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-shipping-fast text-purple"></i> Gestión de Envíos</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Envíos</li>
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

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-gradient-purple">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Envíos</h3>
            <div class="card-tools">
                <a href="{{ route('envios.create') }}" class="btn btn-light btn-sm">
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
                        <th>Entrega Estimada</th>
                        <th>Peso Total</th>
                        <th>Costo Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($envios as $envio)
                        <tr>
                            <td><strong>#{{ $envio->id }}</strong></td>
                            <td>
                                @if($envio->usuario)
                                    {{ $envio->usuario->nombre }} {{ $envio->usuario->apellido }}
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
                            <td>{{ $envio->fecha_creacion ? $envio->fecha_creacion->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if($envio->fecha_entrega_aproximada)
                                    {{ \Carbon\Carbon::parse($envio->fecha_entrega_aproximada)->format('d/m/Y') }}
                                    @if($envio->hora_entrega_aproximada)
                                        <br><small class="text-muted">{{ substr($envio->hora_entrega_aproximada, 0, 5) }}</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ number_format($envio->peso_total_envio, 2) }} kg</span></td>
                            <td><span class="badge badge-success">Bs. {{ number_format($envio->costo_total_envio, 2) }}</span></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('envios.show', $envio) }}" class="btn btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('envios.edit', $envio) }}" class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('envios.destroy', $envio) }}" method="POST" style="display:inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar este envío?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay envíos registrados</p>
                                <a href="{{ route('envios.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear primer envío
                                </a>
                            </td>
                        </tr>
                    @endforelse
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
        .bg-gradient-purple {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
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
