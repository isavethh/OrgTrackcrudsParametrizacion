@extends('adminlte::page')

@section('title', 'Editar Envío')

@section('content_header')
    <h1>Editar Envío #{{ $envio->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('envios.update', $envio) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_usuario">Usuario/Cliente <span class="text-danger">*</span></label>
                            <select name="id_usuario" id="id_usuario" class="form-control @error('id_usuario') is-invalid @enderror" required>
                                <option value="">Seleccione un usuario</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('id_usuario', $envio->id_usuario) == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->nombre }} {{ $usuario->apellido }} ({{ $usuario->correo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_usuario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_direccion">Dirección/Ruta <span class="text-danger">*</span></label>
                            <select name="id_direccion" id="id_direccion" class="form-control @error('id_direccion') is-invalid @enderror" required>
                                <option value="">Seleccione una dirección</option>
                                @foreach($direcciones as $direccion)
                                    <option value="{{ $direccion->id }}" {{ old('id_direccion', $envio->id_direccion) == $direccion->id ? 'selected' : '' }}>
                                        📍 {{ $direccion->nombreorigen }} → 🏁 {{ $direccion->nombredestino }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_direccion')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Selecciona la ruta que seguirá este envío. 
                                <a href="{{ route('direcciones.create') }}" target="_blank">¿Crear nueva ruta?</a>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio</label>
                            <input type="datetime-local" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                value="{{ old('fecha_inicio', $envio->fecha_inicio ? $envio->fecha_inicio->format('Y-m-d\TH:i') : '') }}">
                            @error('fecha_inicio')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_entrega">Fecha de Entrega Estimada</label>
                            <input type="datetime-local" name="fecha_entrega" id="fecha_entrega" class="form-control @error('fecha_entrega') is-invalid @enderror" 
                                value="{{ old('fecha_entrega', $envio->fecha_entrega ? $envio->fecha_entrega->format('Y-m-d\TH:i') : '') }}">
                            @error('fecha_entrega')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-calendar"></i> <small><strong>Fecha creación:</strong> {{ $envio->fecha_creacion->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>Importante:</strong> Puedes cambiar la dirección/ruta de este envío seleccionando una diferente del listado.
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Envío
                    </button>
                    <a href="{{ route('envios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
