@extends('adminlte::page')

@section('title', 'Crear Carga')

@section('content_header')
    <h1>Crear Nueva Carga</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('cargas.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo">Tipo <span class="text-danger">*</span></label>
                            <input type="text" name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" value="{{ old('tipo') }}" required>
                            @error('tipo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="variedad">Variedad <span class="text-danger">*</span></label>
                            <input type="text" name="variedad" id="variedad" class="form-control @error('variedad') is-invalid @enderror" value="{{ old('variedad') }}" required>
                            @error('variedad')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad') }}" required>
                            @error('cantidad')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="empaquetado">Empaquetado <span class="text-danger">*</span></label>
                            <input type="text" name="empaquetado" id="empaquetado" class="form-control @error('empaquetado') is-invalid @enderror" value="{{ old('empaquetado') }}" required>
                            @error('empaquetado')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="peso">Peso (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="peso" id="peso" class="form-control @error('peso') is-invalid @enderror" value="{{ old('peso') }}" required>
                            @error('peso')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('cargas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
