@extends('adminlte::page')

@section('title', 'Transportistas')

@section('content_header')
    <h1>Gestión de Transportistas</h1>
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
            <h3 class="card-title">Lista de Transportistas</h3>
            <div class="card-tools">
                <a href="{{ route('transportistas.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Transportista
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="transportistas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>CI</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transportistas as $transportista)
                    <tr>
                        <td>{{ $transportista->id }}</td>
                        <td>
                            @if($transportista->usuario)
                                {{ $transportista->usuario->nombre }} {{ $transportista->usuario->apellido }}
                            @else
                                <span class="text-muted">Sin usuario</span>
                            @endif
                        </td>
                        <td><strong>{{ $transportista->ci }}</strong></td>
                        <td>{{ $transportista->telefono ?? '-' }}</td>
                        <td>
                            @if($transportista->estado)
                                <span class="badge badge-{{ $transportista->estado->nombre == 'Disponible' ? 'success' : ($transportista->estado->nombre == 'En ruta' ? 'warning' : 'secondary') }}">
                                    {{ $transportista->estado->nombre }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Sin estado</span>
                            @endif
                        </td>
                        <td>{{ $transportista->usuario && $transportista->usuario->fecha_registro ? $transportista->usuario->fecha_registro->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('transportistas.edit', $transportista) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('transportistas.destroy', $transportista) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este transportista?')">
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
            $('#transportistas-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop
