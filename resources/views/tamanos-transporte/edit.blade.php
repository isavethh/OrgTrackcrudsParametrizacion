@extends('adminlte::page')

@section('title', 'Editar Tamaño de Transporte')

@section('content_header')
    <h1>Editar Tamaño de Transporte</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('tamanos-transporte.update', $tamanosTransporte->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $tamanosTransporte->nombre) }}" required>
                    @error('nombre')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('tamanos-transporte.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

