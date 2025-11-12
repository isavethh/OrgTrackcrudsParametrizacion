@extends('adminlte::page')

@section('title', 'Editar Administrador')

@section('content_header')
    <h1>Editar Administrador</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admins.update', $admin->id) }}" method="POST">
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

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="correo">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $admin->usuario->correo) }}" required>
                            @error('correo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contrasena">Contraseña</label>
                            <input type="password" name="contrasena" id="contrasena" class="form-control @error('contrasena') is-invalid @enderror">
                            @error('contrasena')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nivel_acceso">Nivel de Acceso <span class="text-danger">*</span></label>
                    <select name="nivel_acceso" id="nivel_acceso" class="form-control @error('nivel_acceso') is-invalid @enderror" required>
                        <option value="">Seleccione un nivel</option>
                        <option value="1" {{ old('nivel_acceso', $admin->nivel_acceso) == '1' ? 'selected' : '' }}>Nivel 1 - Básico</option>
                        <option value="2" {{ old('nivel_acceso', $admin->nivel_acceso) == '2' ? 'selected' : '' }}>Nivel 2 - Intermedio</option>
                        <option value="3" {{ old('nivel_acceso', $admin->nivel_acceso) == '3' ? 'selected' : '' }}>Nivel 3 - Avanzado</option>
                        <option value="4" {{ old('nivel_acceso', $admin->nivel_acceso) == '4' ? 'selected' : '' }}>Nivel 4 - Supervisor</option>
                        <option value="5" {{ old('nivel_acceso', $admin->nivel_acceso) == '5' ? 'selected' : '' }}>Nivel 5 - Administrador</option>
                    </select>
                    @error('nivel_acceso')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('admins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

