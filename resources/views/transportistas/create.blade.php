@extends('adminlte::page')

@section('title', 'Crear Transportista')

@section('content_header')
    <h1>Crear Transportista</h1>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('transportistas.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ci">CI <span class="text-danger">*</span></label>
                            <input type="text" name="ci" id="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci') }}" required>
                            @error('ci')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" required>
                            @error('telefono')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="licencia">Licencia <span class="text-danger">*</span></label>
                    <input type="text" name="licencia" id="licencia" class="form-control @error('licencia') is-invalid @enderror" value="{{ old('licencia') }}" required>
                    @error('licencia')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="id_estado_transportista">Estado <span class="text-danger">*</span></label>
                    <select name="id_estado_transportista" id="id_estado_transportista" class="form-control" required>
                        @foreach($estadosTransportista as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('transportistas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
