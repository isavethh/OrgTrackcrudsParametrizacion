@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
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
            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $usuario->persona->nombre) }}" required>
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" id="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido', $usuario->persona->apellido) }}" required>
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
                            <input type="text" name="ci" id="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $usuario->persona->ci) }}" required>
                            @error('ci')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $usuario->persona->telefono) }}" required>
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
                            <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $usuario->correo) }}" required>
                            @error('correo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contrasena">Contraseña <small>(dejar en blanco para no cambiar)</small></label>
                            <input type="password" name="contrasena" id="contrasena" class="form-control @error('contrasena') is-invalid @enderror">
                            @error('contrasena')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_rol">Rol <span class="text-danger">*</span></label>
                    <select name="id_rol" id="id_rol" class="form-control @error('id_rol') is-invalid @enderror" required>
                        <option value="">Seleccione un rol</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" {{ old('id_rol', $usuario->id_rol) == $rol->id ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_rol')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                @if($usuario->admin)
                    <div class="form-group">
                        <label for="nivel_acceso">Nivel de Acceso</label>
                        <select name="nivel_acceso" id="nivel_acceso" class="form-control">
                            <option value="1" {{ old('nivel_acceso', $usuario->admin->nivel_acceso) == 1 ? 'selected' : '' }}>1 - Básico</option>
                            <option value="2" {{ old('nivel_acceso', $usuario->admin->nivel_acceso) == 2 ? 'selected' : '' }}>2 - Medio</option>
                            <option value="3" {{ old('nivel_acceso', $usuario->admin->nivel_acceso) == 3 ? 'selected' : '' }}>3 - Avanzado</option>
                            <option value="4" {{ old('nivel_acceso', $usuario->admin->nivel_acceso) == 4 ? 'selected' : '' }}>4 - Supervisor</option>
                            <option value="5" {{ old('nivel_acceso', $usuario->admin->nivel_acceso) == 5 ? 'selected' : '' }}>5 - Super Admin</option>
                        </select>
                    </div>
                @endif

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
