@extends('adminlte::page')

@section('title', 'Crear Vehículo')

@section('content_header')
    <h1>Crear Nuevo Vehículo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('vehiculos.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="transportista_id">Transportista <span class="text-danger">*</span></label>
                            <select name="transportista_id" id="transportista_id" class="form-control @error('transportista_id') is-invalid @enderror" required>
                                <option value="">Seleccione un transportista</option>
                                @foreach($transportistas as $transportista)
                                    <option value="{{ $transportista->id }}" {{ old('transportista_id') == $transportista->id ? 'selected' : '' }}>
                                        {{ $transportista->usuario->nombre }} {{ $transportista->usuario->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            @error('transportista_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_transporte_id">Tipo de Transporte <span class="text-danger">*</span></label>
                            <select name="tipo_transporte_id" id="tipo_transporte_id" class="form-control @error('tipo_transporte_id') is-invalid @enderror" required>
                                <option value="">Seleccione un tipo</option>
                                @foreach($tiposTransporte as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_transporte_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_transporte_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tamano_transporte_id">Tamaño de Transporte <span class="text-danger">*</span></label>
                            <select name="tamano_transporte_id" id="tamano_transporte_id" class="form-control @error('tamano_transporte_id') is-invalid @enderror" required>
                                <option value="">Seleccione un tamaño</option>
                                @foreach($tamanosTransporte as $tamano)
                                    <option value="{{ $tamano->id }}" {{ old('tamano_transporte_id') == $tamano->id ? 'selected' : '' }}>
                                        {{ $tamano->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tamano_transporte_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="placa">Placa <span class="text-danger">*</span></label>
                            <input type="text" name="placa" id="placa" class="form-control @error('placa') is-invalid @enderror" value="{{ old('placa') }}" required>
                            @error('placa')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marca">Marca</label>
                            <input type="text" name="marca" id="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca') }}">
                            @error('marca')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="modelo">Modelo</label>
                            <input type="text" name="modelo" id="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo') }}">
                            @error('modelo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado">Estado <span class="text-danger">*</span></label>
                            <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                                <option value="">Seleccione un estado</option>
                                <option value="Disponible" {{ old('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="En ruta" {{ old('estado') == 'En ruta' ? 'selected' : '' }}>En ruta</option>
                                <option value="No Disponible" {{ old('estado') == 'No Disponible' ? 'selected' : '' }}>No Disponible</option>
                                <option value="Mantenimiento" {{ old('estado') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                            @error('estado')
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
