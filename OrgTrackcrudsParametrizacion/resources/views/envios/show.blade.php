@extends('adminlte::page')

@section('title', 'Ver Envío')

@section('content_header')
    <h1>Detalle del Envío #{{ $envio->id }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-shipping-fast"></i> Información del Envío</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID del Envío:</dt>
                        <dd class="col-sm-8"><strong>#{{ $envio->id }}</strong></dd>

                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{
                                $envio->estado == 'Entregado' ? 'success' :
                                ($envio->estado == 'En curso' ? 'primary' :
                                ($envio->estado == 'Asignado' ? 'info' :
                                ($envio->estado == 'Parcialmente entregado' ? 'warning' : 'secondary')))
                            }} badge-lg">
                                {{ $envio->estado }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Cliente:</dt>
                        <dd class="col-sm-8">
                            {{ $envio->usuario->nombre }} {{ $envio->usuario->apellido }}<br>
                            <small class="text-muted">{{ $envio->usuario->correo }}</small>
                        </dd>

                        <dt class="col-sm-4">Fecha de Creación:</dt>
                        <dd class="col-sm-8">{{ $envio->fecha_creacion->format('d/m/Y H:i:s') }}</dd>

                        <dt class="col-sm-4">Fecha de Inicio:</dt>
                        <dd class="col-sm-8">{{ $envio->fecha_inicio ? $envio->fecha_inicio->format('d/m/Y H:i:s') : '-' }}</dd>

                        <dt class="col-sm-4">Fecha de Entrega:</dt>
                        <dd class="col-sm-8">{{ $envio->fecha_entrega ? $envio->fecha_entrega->format('d/m/Y H:i:s') : '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-route"></i> Ruta del Envío</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary"><i class="fas fa-map-marker-alt"></i> Origen:</h6>
                        <p class="mb-1"><strong>{{ $envio->direccion->nombreorigen }}</strong></p>
                        @if($envio->direccion->origen_lat && $envio->direccion->origen_lng)
                            <small class="text-muted">
                                Lat: {{ number_format($envio->direccion->origen_lat, 6) }}<br>
                                Lng: {{ number_format($envio->direccion->origen_lng, 6) }}
                            </small>
                        @endif
                    </div>

                    <div class="text-center my-2">
                        <i class="fas fa-arrow-down fa-2x text-muted"></i>
                    </div>

                    <div>
                        <h6 class="text-success"><i class="fas fa-flag-checkered"></i> Destino:</h6>
                        <p class="mb-1"><strong>{{ $envio->direccion->nombredestino }}</strong></p>
                        @if($envio->direccion->destino_lat && $envio->direccion->destino_lng)
                            <small class="text-muted">
                                Lat: {{ number_format($envio->direccion->destino_lat, 6) }}<br>
                                Lng: {{ number_format($envio->direccion->destino_lng, 6) }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('envios.edit', $envio) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Envío
            </a>
            <a href="{{ route('envios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la Lista
            </a>
        </div>
    </div>
@stop
