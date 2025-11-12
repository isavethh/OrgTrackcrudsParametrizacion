@extends('adminlte::page')

@section('title', 'Nueva Unidad de Medida')

@section('content_header')
    <h1>Nueva Unidad de Medida</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('unidades-medida.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                    @error('nombre')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Ejemplo: kg, litros, toneladas, unidades, m³</small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('unidades-medida.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
