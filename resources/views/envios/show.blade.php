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
                                ($envio->estado == 'Pendiente' ? 'warning' : 'secondary')))
                            }} badge-lg">
                                {{ $envio->estado }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Administrador:</dt>
                        <dd class="col-sm-8">
                            @if($envio->admin && $envio->admin->usuario)
                                {{ $envio->admin->usuario->nombre }} {{ $envio->admin->usuario->apellido }}<br>
                                <small class="text-muted">{{ $envio->admin->usuario->correo }}</small>
                            @else
                                <span class="text-muted">No asignado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Tipo de Empaque:</dt>
                        <dd class="col-sm-8">
                            {{ $envio->tipoEmpaque->nombre ?? '-' }}
                        </dd>

                        <dt class="col-sm-4">Peso:</dt>
                        <dd class="col-sm-8">
                            @if($envio->peso)
                                {{ number_format($envio->peso, 2) }} {{ $envio->unidadMedida->nombre ?? '' }}
                            @else
                                -
                            @endif
                        </dd>

                        <dt class="col-sm-4">Unidad de Medida:</dt>
                        <dd class="col-sm-8">
                            {{ $envio->unidadMedida->nombre ?? '-' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-route"></i> Rutas del Envío</h3>
                </div>
                <div class="card-body">
                    @if($envio->direcciones && $envio->direcciones->count() > 0)
                        @foreach($envio->direcciones as $direccion)
                            <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <h6 class="text-primary">
                                    <i class="fas fa-map-marker-alt"></i> {{ $direccion->nombre_ruta }}
                                </h6>
                                @if($direccion->ruta_geojson)
                                    <small class="text-muted">
                                        <i class="fas fa-check-circle text-success"></i> Ruta calculada
                                    </small>
                                @else
                                    <small class="text-muted">
                                        <i class="fas fa-exclamation-circle text-warning"></i> Sin ruta
                                    </small>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle"></i> No hay rutas asignadas a este envío.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
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
