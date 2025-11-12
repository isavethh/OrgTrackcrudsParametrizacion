@extends('adminlte::page')

@section('title', 'Administradores')

@section('content_header')
    <h1>Administradores</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admins.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Administrador
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered table-striped" id="adminsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Nivel de Acceso</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>{{ $admin->usuario->nombre }} {{ $admin->usuario->apellido }}</td>
                        <td>{{ $admin->usuario->correo }}</td>
                        <td>
                            <span class="badge badge-info">Nivel {{ $admin->nivel_acceso }}</span>
                        </td>
                        <td>{{ $admin->usuario->fecha_registro ? $admin->usuario->fecha_registro->format('d/m/Y') : '-' }}</td>
                        <td>
                            <a href="{{ route('admins.show', $admin->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admins.edit', $admin->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" style="display:inline;">
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

@section('js')
<script>
    $('#adminsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });
</script>
@stop
