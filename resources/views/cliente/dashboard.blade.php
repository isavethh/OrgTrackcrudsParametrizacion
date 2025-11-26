@extends('cliente.layouts.app')

@section('title', 'Dashboard - OrgTrack')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>150</h3>
                <p>Envíos Totales</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="{{ route('envios.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>53<sup style="font-size: 20px">%</sup></h3>
                <p>Envíos Entregados</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('envios.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>44</h3>
                <p>Direcciones Guardadas</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="{{ route('direcciones.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>65</h3>
                <p>Documentos</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{ route('documentos.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-6">
        <!-- Recent Envios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Envíos Recientes</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Destinatario</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#001</td>
                                <td>Juan Pérez</td>
                                <td><span class="badge badge-success">Entregado</span></td>
                                <td>2024-01-15</td>
                            </tr>
                            <tr>
                                <td>#002</td>
                                <td>María García</td>
                                <td><span class="badge badge-warning">En Tránsito</span></td>
                                <td>2024-01-14</td>
                            </tr>
                            <tr>
                                <td>#003</td>
                                <td>Carlos López</td>
                                <td><span class="badge badge-info">Procesando</span></td>
                                <td>2024-01-13</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="{{ route('envios.index') }}" class="uppercase">Ver todos los envíos</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
    <div class="col-md-6">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Acciones Rápidas</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('envios.create') }}" class="btn btn-primary btn-block mb-3">
                            <i class="fas fa-plus"></i> Nuevo Envío
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('direcciones.create') }}" class="btn btn-success btn-block mb-3">
                            <i class="fas fa-map-marker-alt"></i> Nueva Dirección
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('documentos.index') }}" class="btn btn-info btn-block mb-3">
                            <i class="fas fa-file-pdf"></i> Ver Documentos
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('envios.index') }}" class="btn btn-warning btn-block mb-3">
                            <i class="fas fa-search"></i> Buscar Envío
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@endsection
