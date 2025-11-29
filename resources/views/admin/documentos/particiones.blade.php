@extends('layouts.adminlte')

@section('page-title', 'Particiones del Envío')

@section('page-content')
<div class="row">
    <div class="col-12">
        <a href="{{ route('admin.documentos.index') }}" class="mb-3 d-inline-block"><i class="fas fa-arrow-left mr-1"></i> Volver atrás</a>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Particiones</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID Partición</th>
                                <th>Estado</th>
                                <th>Transportista</th>
                                <th>Vehículo</th>
                                <th class="text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#9</td>
                                <td><span class="badge badge-success">Entregado</span></td>
                                <td>Mauri Martinez</td>
                                <td>STU0123</td>
                                <td class="text-right"><a href="{{ route('admin.documentos.ver', ['id' => 9]) }}">Ver documento</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


