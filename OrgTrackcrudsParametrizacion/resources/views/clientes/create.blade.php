@extends('adminlte::page')

@section('title', 'Crear Cliente')

@section('content_header')
    <h1>Crear Cliente</h1>
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
            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" id="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido') }}" required>
                            @error('apellido')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

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

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="correo">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}" required>
                            @error('correo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contrasena">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="contrasena" id="contrasena" class="form-control @error('contrasena') is-invalid @enderror" required>
                            @error('contrasena')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
