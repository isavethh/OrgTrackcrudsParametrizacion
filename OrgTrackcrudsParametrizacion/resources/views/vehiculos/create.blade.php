@extends('adminlte::page')

@section('title', 'Crear Vehículo')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-truck text-success"></i> Crear Nuevo Vehículo</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehiculos.index') }}">Vehículos</a></li>
                <li class="breadcrumb-item active">Crear</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-success">
            <h3 class="card-title"><i class="fas fa-plus-circle"></i> Formulario de Registro</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('vehiculos.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="placa"><i class="fas fa-id-card text-muted"></i> Placa <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                <input type="text" name="placa" id="placa" class="form-control @error('placa') is-invalid @enderror" value="{{ old('placa') }}" required placeholder="Ej: ABC-1234">
                            </div>
                            @error('placa')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="capacidad"><i class="fas fa-weight-hanging text-muted"></i> Capacidad (kg) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-balance-scale"></i></span>
                                </div>
                                <input type="number" step="0.01" name="capacidad" id="capacidad" class="form-control @error('capacidad') is-invalid @enderror" value="{{ old('capacidad') }}" required placeholder="Ej: 1000.50">
                            </div>
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
                                    <option value="{{ $tipo->id }}" {{ old('id_tipo_vehiculo') == $tipo->id ? 'selected' : '' }}>
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
                                    <option value="{{ $estado->id }}" {{ old('id_estado_vehiculo') == $estado->id ? 'selected' : '' }}>
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
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
<style>
    .card {
        border-radius: 10px;
        border: none;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .form-group label {
        font-weight: 600;
        color: #495057;
    }
    .input-group-text {
        background-color: #e9ecef;
    }
    .btn {
        padding: 8px 20px;
        font-weight: 500;
    }
</style>
@stop
