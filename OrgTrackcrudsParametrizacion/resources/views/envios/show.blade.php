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

                        <dt class="col-sm-4">Fecha Entrega Estimada:</dt>
                        <dd class="col-sm-8">
                            @if($envio->fecha_entrega_aproximada)
                                {{ \Carbon\Carbon::parse($envio->fecha_entrega_aproximada)->format('d/m/Y') }}
                                @if($envio->hora_entrega_aproximada)
                                    <small class="text-muted">{{ substr($envio->hora_entrega_aproximada, 0, 5) }}</small>
                                @endif
                            @else
                                -
                            @endif
                        </dd>

                        <dt class="col-sm-4">Fecha de Inicio:</dt>
                        <dd class="col-sm-8">{{ $envio->fecha_inicio ? $envio->fecha_inicio->format('d/m/Y H:i:s') : '-' }}</dd>

                        <dt class="col-sm-4">Fecha de Entrega Real:</dt>
                        <dd class="col-sm-8">{{ $envio->fecha_entrega ? $envio->fecha_entrega->format('d/m/Y H:i:s') : '-' }}</dd>

                        <dt class="col-sm-4">Estado de Aprobación:</dt>
                        <dd class="col-sm-8">
                            @php
                                $estadoAprobacion = $envio->estado_aprobacion ?? 'pendiente';
                            @endphp
                            <span class="badge badge-{{
                                $estadoAprobacion == 'aprobado' ? 'success' :
                                ($estadoAprobacion == 'rechazado' ? 'danger' : 'warning')
                            }} badge-lg">
                                @if($estadoAprobacion == 'pendiente')
                                    <i class="fas fa-clock"></i> Pendiente Aprobación
                                @elseif($estadoAprobacion == 'aprobado')
                                    <i class="fas fa-check-circle"></i> Aprobado
                                @else
                                    <i class="fas fa-times-circle"></i> Rechazado
                                @endif
                            </span>
                        </dd>

                        @if($estadoAprobacion == 'rechazado' && $envio->motivo_rechazo)
                            <dt class="col-sm-4">Motivo de Rechazo:</dt>
                            <dd class="col-sm-8">
                                <div class="alert alert-danger mb-0">
                                    {{ $envio->motivo_rechazo }}
                                </div>
                            </dd>
                        @endif

                        @if($envio->tipoVehiculo)
                            <dt class="col-sm-4">Tipo de Vehículo:</dt>
                            <dd class="col-sm-8">
                                <i class="fas fa-truck"></i> {{ $envio->tipoVehiculo->nombre }}
                            </dd>
                        @endif

                        @if($envio->transportistaAsignado)
                            <dt class="col-sm-4">Transportista Asignado:</dt>
                            <dd class="col-sm-8">
                                <i class="fas fa-user"></i> {{ $envio->transportistaAsignado->nombre }} {{ $envio->transportistaAsignado->apellido }}
                            </dd>
                        @endif
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

    <!-- Sección de Productos/Insumos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title"><i class="fas fa-boxes"></i> Productos/Insumos del Envío</h3>
                </div>
                <div class="card-body">
                    @if($envio->productos && $envio->productos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="4%">#</th>
                                        <th width="12%">Categoría</th>
                                        <th width="20%">Producto/Insumo</th>
                                        <th width="8%">Cantidad</th>
                                        <th width="10%">Unidad</th>
                                        <th width="10%">Empaque</th>
                                        <th width="10%">Peso Unit. (kg)</th>
                                        <th width="10%">Peso Total (kg)</th>
                                        <th width="8%">Costo Unit.</th>
                                        <th width="8%">Costo Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($envio->productos as $index => $producto)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $producto->categoria == 'Insumos' ? 'warning' :
                                                    ($producto->categoria == 'Verduras' ? 'success' :
                                                    ($producto->categoria == 'Frutas' ? 'danger' : 'secondary'))
                                                }}">
                                                    {{ $producto->categoria }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $producto->producto }}</strong>
                                            </td>
                                            <td class="text-center">{{ $producto->cantidad }}</td>
                                            <td class="text-center">
                                                @if($producto->unidadMedida)
                                                    <span class="badge badge-light">{{ $producto->unidadMedida->nombre }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($producto->tipoEmpaque)
                                                    <span class="badge badge-light">{{ $producto->tipoEmpaque->nombre }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($producto->peso_por_unidad, 2) }}</td>
                                            <td class="text-right">
                                                <strong>{{ number_format($producto->peso_total, 2) }}</strong>
                                            </td>
                                            <td class="text-right">Bs. {{ number_format($producto->costo_unitario, 2) }}</td>
                                            <td class="text-right">
                                                <strong>Bs. {{ number_format($producto->costo_total, 2) }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="7" class="text-right">TOTALES:</th>
                                        <th class="text-right">{{ number_format($envio->peso_total_envio, 2) }} kg</th>
                                        <th></th>
                                        <th class="text-right">Bs. {{ number_format($envio->costo_total_envio, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Este envío no tiene productos o insumos registrados.
                        </div>
                    @endif
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
