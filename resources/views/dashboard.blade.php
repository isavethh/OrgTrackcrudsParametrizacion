@extends('adminlte::page')

@section('title', 'Dashboard - OrgTrack')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total_admins'] }}</h3>
                    <p>Administradores</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <a href="{{ route('admins.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_clientes'] }}</h3>
                    <p>Clientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user"></i>
                </div>
                <a href="{{ route('clientes.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_transportistas'] }}</h3>
                    <p>Transportistas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <a href="{{ route('transportistas.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_vehiculos'] }}</h3>
                    <p>Vehículos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
                <a href="{{ route('vehiculos.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $stats['total_envios'] }}</h3>
                    <p>Total Envíos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <a href="{{ route('envios.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $stats['total_direcciones'] }}</h3>
                    <p>Direcciones/Rutas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <a href="{{ route('direcciones.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-indigo">
                <div class="inner">
                    <h3>{{ $stats['envios_pendientes'] }}</h3>
                    <p>Envíos Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('envios.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estado de Vehículos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Disponibles</span>
                                    <span class="info-box-number">{{ $stats['vehiculos_disponibles'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-route"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">En Ruta</span>
                                    <span class="info-box-number">{{ $stats['vehiculos_en_ruta'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-wrench"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Mantenimiento</span>
                                    <span class="info-box-number">{{ $stats['vehiculos_mantenimiento'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estado de Transportistas</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Disponibles</span>
                                    <span class="info-box-number">{{ $stats['transportistas_disponibles'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-route"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">En Ruta</span>
                                    <span class="info-box-number">{{ $stats['transportistas_en_ruta'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
