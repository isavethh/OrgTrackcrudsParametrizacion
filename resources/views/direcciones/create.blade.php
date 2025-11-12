@extends('adminlte::page')

@section('title', 'Crear Dirección')

@section('content_header')
    <h1>Crear Nueva Dirección</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('direcciones.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="id_usuario">Usuario <span class="text-danger">*</span></label>
                    <select name="id_usuario" id="id_usuario" class="form-control @error('id_usuario') is-invalid @enderror" required>
                        <option value="">Seleccione un usuario</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ old('id_usuario') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->nombre }} {{ $usuario->apellido }} - {{ $usuario->correo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_usuario')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h5 class="text-primary"><i class="fas fa-map-marker-alt"></i> Origen</h5>
                        <hr>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombreorigen">Nombre del Origen <span class="text-danger">*</span></label>
                    <input type="text" name="nombreorigen" id="nombreorigen" class="form-control @error('nombreorigen') is-invalid @enderror" value="{{ old('nombreorigen') }}" required placeholder="Ej: Av. Principal #123, La Paz">
                    @error('nombreorigen')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="origen_lat">Latitud Origen</label>
                            <input type="number" step="any" name="origen_lat" id="origen_lat" class="form-control @error('origen_lat') is-invalid @enderror" value="{{ old('origen_lat') }}" placeholder="-16.5000">
                            @error('origen_lat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="origen_lng">Longitud Origen</label>
                            <input type="number" step="any" name="origen_lng" id="origen_lng" class="form-control @error('origen_lng') is-invalid @enderror" value="{{ old('origen_lng') }}" placeholder="-68.1500">
                            @error('origen_lng')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5 class="text-success"><i class="fas fa-flag-checkered"></i> Destino</h5>
                        <hr>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombredestino">Nombre del Destino <span class="text-danger">*</span></label>
                    <input type="text" name="nombredestino" id="nombredestino" class="form-control @error('nombredestino') is-invalid @enderror" value="{{ old('nombredestino') }}" required placeholder="Ej: Calle Comercio #456, El Alto">
                    @error('nombredestino')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="destino_lat">Latitud Destino</label>
                            <input type="number" step="any" name="destino_lat" id="destino_lat" class="form-control @error('destino_lat') is-invalid @enderror" value="{{ old('destino_lat') }}" placeholder="-16.5000">
                            @error('destino_lat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="destino_lng">Longitud Destino</label>
                            <input type="number" step="any" name="destino_lng" id="destino_lng" class="form-control @error('destino_lng') is-invalid @enderror" value="{{ old('destino_lng') }}" placeholder="-68.1500">
                            @error('destino_lng')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="rutageojson">Ruta GeoJSON (Opcional)</label>
                    <textarea name="rutageojson" id="rutageojson" class="form-control @error('rutageojson') is-invalid @enderror" rows="3" placeholder='{"type":"LineString","coordinates":[[-68.15,-16.5],[-68.14,-16.49]]}'></textarea>
                    @error('rutageojson')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Formato GeoJSON para la ruta</small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('direcciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
