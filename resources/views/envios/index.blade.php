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
                        <th>Admin</th>
                        <th>Estado</th>
                        <th>Tipo Empaque</th>
                        <th>Peso</th>
                        <th>Volumen</th>
                        <th>Rutas</th>
                        <th>Fecha Envío</th>
                        <th>Entrega Estimada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($envios as $envio)
                    <tr>
                        <td><strong>#{{ $envio->id }}</strong></td>
                        <td>{{ $envio->admin ? $envio->admin->usuario->nombre : '-' }}</td>
                        <td>
                            <span class="badge badge-{{
                                $envio->estado == 'Entregado' ? 'success' :
                                ($envio->estado == 'En curso' ? 'primary' :
                                ($envio->estado == 'Asignado' ? 'info' :
                                ($envio->estado == 'Parcialmente entregado' ? 'warning' : 'secondary')))
                            }}">
                                {{ $envio->estado }}
                            </span>
                        </td>
                        <td>{{ $envio->tipoEmpaque->nombre ?? '-' }}</td>
                        <td>
                            @if($envio->peso)
                                {{ number_format($envio->peso, 2) }} {{ $envio->unidadMedida->nombre ?? '' }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $envio->volumen ? number_format($envio->volumen, 2) . ' m³' : '-' }}</td>
                        <td>
                            <span class="badge badge-secondary">{{ $envio->direcciones->count() }} rutas</span>
                        </td>
                        <td>{{ $envio->fecha_envio ? $envio->fecha_envio->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $envio->fecha_entrega_estimada ? $envio->fecha_entrega_estimada->format('d/m/Y H:i') : '-' }}</td>
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
