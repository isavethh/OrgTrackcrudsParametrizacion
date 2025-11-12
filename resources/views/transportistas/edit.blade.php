@extends('adminlte::page')

@section('title', 'Editar Transportista')

@section('content_header')
    <h1>Editar Transportista</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('transportistas.update', $transportista) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="id_usuario">Usuario (Opcional)</label>
                    <select name="id_usuario" id="id_usuario" class="form-control @error('id_usuario') is-invalid @enderror">
                        <option value="">Seleccione un usuario</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ old('id_usuario', $transportista->id_usuario) == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->nombre }} {{ $usuario->apellido }} ({{ $usuario->correo }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_usuario')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ci">Cédula de Identidad <span class="text-danger">*</span></label>
                            <input type="text" name="ci" id="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $transportista->ci) }}" required>
                            @error('ci')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $transportista->telefono) }}">
                            @error('telefono')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="estado">Estado <span class="text-danger">*</span></label>
                    <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                        <option value="">Seleccione un estado</option>
                        @foreach(\App\Models\Transportista::ESTADOS as $estado)
                            <option value="{{ $estado }}" {{ old('estado', $transportista->estado) == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                        @endforeach
                    </select>
                    @error('estado')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('transportistas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
