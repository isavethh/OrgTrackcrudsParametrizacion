@extends('adminlte::page')

@section('title', 'Editar Administrador')

@section('content_header')
    <h1>Editar Administrador</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admins.update', $admin) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $admin->usuario->nombre) }}" required>
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" id="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido', $admin->usuario->apellido) }}" required>
                            @error('apellido')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="correo">Correo Electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $admin->usuario->correo) }}" required>
                    @error('correo')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="contrasena">Nueva Contraseña</label>
                    <input type="password" name="contrasena" id="contrasena" class="form-control @error('contrasena') is-invalid @enderror">
                    @error('contrasena')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual. Mínimo 6 caracteres.</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Fecha de registro:</strong> {{ $admin->usuario->fecha_registro->format('d/m/Y H:i') }}
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Administrador
                    </button>
                    <a href="{{ route('admins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
