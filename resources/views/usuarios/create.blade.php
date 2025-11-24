@extends('adminlte::page')

@section('title', 'Crear Usuario')

@section('content_header')
    <h1>Crear Nuevo Usuario</h1>
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
            <form action="{{ route('usuarios.store') }}" method="POST" id="usuario-form">
                @csrf
                
                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuario <span class="text-danger">*</span></label>
                    <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                        <option value="">Seleccione un tipo</option>
                        @foreach($roles as $rol)
                            <option value="{{ strtolower($rol->codigo) }}">{{ $rol->nombre }}</option>
                        @endforeach
                        <option value="transportista">Transportista (Sin usuario)</option>
                    </select>
                </div>

                <!-- Campos para Admin y Cliente (con usuario) -->
                <div id="campos-usuario" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}">
                                @error('nombre')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido">Apellido <span class="text-danger">*</span></label>
                                <input type="text" name="apellido" id="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido') }}">
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
                                <input type="text" name="ci" id="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci') }}">
                                @error('ci')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono <span class="text-danger">*</span></label>
                                <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
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
                                <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}">
                                @error('correo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contrasena">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="contrasena" id="contrasena" class="form-control @error('contrasena') is-invalid @enderror">
                                @error('contrasena')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id_rol" id="id_rol">

                    <!-- Campo específico para Admin -->
                    <div id="campo-nivel-acceso" style="display: none;">
                        <div class="form-group">
                            <label for="nivel_acceso">Nivel de Acceso <span class="text-danger">*</span></label>
                            <select name="nivel_acceso" id="nivel_acceso" class="form-control">
                                <option value="1">1 - Básico</option>
                                <option value="2">2 - Medio</option>
                                <option value="3">3 - Avanzado</option>
                                <option value="4">4 - Supervisor</option>
                                <option value="5">5 - Super Admin</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Campos para Transportista (sin usuario) -->
                <div id="campos-transportista" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ci_transportista">CI <span class="text-danger">*</span></label>
                                <input type="text" name="ci" id="ci_transportista" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci') }}">
                                @error('ci')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono_transportista">Teléfono <span class="text-danger">*</span></label>
                                <input type="text" name="telefono" id="telefono_transportista" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
                                @error('telefono')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_estado_transportista">Estado <span class="text-danger">*</span></label>
                        <select name="id_estado_transportista" id="id_estado_transportista" class="form-control">
                            @foreach($estadosTransportista as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('admins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    const roles = @json($roles);
    
    $('#tipo_usuario').on('change', function() {
        const tipo = $(this).val();
        
        // Ocultar todos los campos
        $('#campos-usuario').hide();
        $('#campos-transportista').hide();
        $('#campo-nivel-acceso').hide();
        
        if (tipo === 'transportista') {
            // Mostrar campos de transportista (sin usuario)
            $('#campos-transportista').show();
        } else if (tipo) {
            // Mostrar campos de usuario (admin o cliente)
            $('#campos-usuario').show();
            
            // Buscar el rol correspondiente
            const rol = roles.find(r => r.codigo.toLowerCase() === tipo);
            if (rol) {
                $('#id_rol').val(rol.id);
                
                // Si es admin, mostrar campo de nivel de acceso
                if (rol.codigo === 'ADMIN') {
                    $('#campo-nivel-acceso').show();
                    $('#nivel_acceso').prop('required', true);
                } else {
                    $('#nivel_acceso').prop('required', false);
                }
            }
        }
    });
});
</script>
@stop
