@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-user-shield text-primary"></i> Gestión de Usuarios</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Usuarios</li>
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
        <div class="card-header bg-gradient-primary">
            <h3 class="card-title"><i class="fas fa-users"></i> Usuarios del Sistema</h3>
            <div class="card-tools">
                <a href="{{ route('usuarios.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="usuarios-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>CI</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                        <td>{{ $usuario->ci }}</td>
                        <td>{{ $usuario->correo }}</td>
                        <td>{{ $usuario->telefono }}</td>
                        <td>
                            <span class="badge badge-{{ $usuario->rol->codigo === 'ADMIN' ? 'danger' : ($usuario->rol->codigo === 'CLIENT' ? 'info' : 'secondary') }}">
                                {{ $usuario->rol->nombre }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este usuario?')">
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

    <div class="card mt-3 shadow-sm">
        <div class="card-header bg-gradient-warning">
            <h3 class="card-title"><i class="fas fa-user-tie"></i> Transportistas (Sin Usuario)</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="transportistas-table">
                <thead>
                    <tr>
                        <th>ID</th>
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
                        <td>{{ $transportista->ci }}</td>
                        <td>{{ $transportista->telefono }}</td>
                        <td>
                            <span class="badge badge-{{ $transportista->estadoTransportista->nombre === 'Activo' ? 'success' : 'secondary' }}">
                                {{ $transportista->estadoTransportista->nombre }}
                            </span>
                        </td>
                        <td>{{ $transportista->fecha_registro }}</td>
                        <td>
                            <a href="{{ route('usuarios.edit-transportista', $transportista->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('usuarios.destroy-transportista', $transportista->id) }}" method="POST" style="display: inline-block;">
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
            $('#usuarios-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop
