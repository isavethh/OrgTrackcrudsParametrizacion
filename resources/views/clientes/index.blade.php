@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Clientes</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="clientesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Dirección Entrega</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td>{{ $cliente->usuario->nombre }} {{ $cliente->usuario->apellido }}</td>
                        <td>{{ $cliente->usuario->correo }}</td>
                        <td>{{ $cliente->telefono ?? '-' }}</td>
                        <td>{{ $cliente->direccion_entrega ?? '-' }}</td>
                        <td>{{ $cliente->usuario->fecha_registro ? $cliente->usuario->fecha_registro->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
<script>
    $('#clientesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });
</script>
@stop
