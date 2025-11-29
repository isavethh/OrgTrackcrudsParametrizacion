@extends('adminlte::page')

@section('title', 'Editar Vehículo')

@section('content_header')
    <h1>Editar Vehículo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="placa">Placa <span class="text-danger">*</span></label>
                            <input type="text" name="placa" id="placa" class="form-control @error('placa') is-invalid @enderror" value="{{ old('placa', $vehiculo->placa) }}" required placeholder="Ej: ABC-1234">
                            @error('placa')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="capacidad">Capacidad (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="capacidad" id="capacidad" class="form-control @error('capacidad') is-invalid @enderror" value="{{ old('capacidad', $vehiculo->capacidad) }}" required placeholder="Ej: 1000.50">
                            @error('capacidad')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_tipo_vehiculo">Tipo de Vehículo <span class="text-danger">*</span></label>
                            <select name="id_tipo_vehiculo" id="id_tipo_vehiculo" class="form-control @error('id_tipo_vehiculo') is-invalid @enderror" required>
                                <option value="">Seleccione un tipo</option>
                                @foreach($tiposVehiculo as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('id_tipo_vehiculo', $vehiculo->id_tipo_vehiculo) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_tipo_vehiculo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_estado_vehiculo">Estado del Vehículo <span class="text-danger">*</span></label>
                            <select name="id_estado_vehiculo" id="id_estado_vehiculo" class="form-control @error('id_estado_vehiculo') is-invalid @enderror" required>
                                <option value="">Seleccione un estado</option>
                                @foreach($estadosVehiculo as $estado)
                                    <option value="{{ $estado->id }}" {{ old('id_estado_vehiculo', $vehiculo->id_estado_vehiculo) == $estado->id ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_estado_vehiculo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>



                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
