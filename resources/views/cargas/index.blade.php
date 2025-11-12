@extends('adminlte::page')

@section('title', 'Cargas')

@section('content_header')
    <h1>Gestión de Cargas</h1>
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
            <h3 class="card-title">Lista de Cargas</h3>
            <div class="card-tools">
                <a href="{{ route('cargas.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nueva Carga
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="cargas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Variedad</th>
                        <th>Cantidad</th>
                        <th>Empaquetado</th>
                        <th>Peso (kg)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cargas as $carga)
                    <tr>
                        <td>{{ $carga->id }}</td>
                        <td>{{ $carga->tipo }}</td>
                        <td>{{ $carga->variedad }}</td>
                        <td>{{ $carga->cantidad }}</td>
                        <td>{{ $carga->empaquetado }}</td>
                        <td>{{ number_format($carga->peso, 2) }}</td>
                        <td>
                            <a href="{{ route('cargas.edit', $carga) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('cargas.destroy', $carga) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta carga?')">
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
            $('#cargas-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop
