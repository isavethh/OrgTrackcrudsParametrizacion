@extends('adminlte::page')

@section('title', 'Editar Envío')

@section('content_header')
    <h1>Editar Envío #{{ $envio->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('envios.update', $envio) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="admin_id">Administrador <span class="text-danger">*</span></label>
                            <select name="admin_id" id="admin_id" class="form-control select2 @error('admin_id') is-invalid @enderror" required>
                                <option value="">Seleccione un administrador</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ old('admin_id', $envio->admin_id) == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->usuario->nombre }} {{ $admin->usuario->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            @error('admin_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado">Estado <span class="text-danger">*</span></label>
                            <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                                <option value="">Seleccione un estado</option>
                                <option value="Pendiente" {{ old('estado', $envio->estado) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="Asignado" {{ old('estado', $envio->estado) == 'Asignado' ? 'selected' : '' }}>Asignado</option>
                                <option value="En curso" {{ old('estado', $envio->estado) == 'En curso' ? 'selected' : '' }}>En curso</option>
                                <option value="Entregado" {{ old('estado', $envio->estado) == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                                <option value="Parcialmente entregado" {{ old('estado', $envio->estado) == 'Parcialmente entregado' ? 'selected' : '' }}>Parcialmente entregado</option>
                            </select>
                            @error('estado')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_empaque_id">Tipo de Empaque</label>
                            <select name="tipo_empaque_id" id="tipo_empaque_id" class="form-control select2 @error('tipo_empaque_id') is-invalid @enderror">
                                <option value="">Seleccione un tipo</option>
                                @foreach($tiposEmpaque as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_empaque_id', $envio->tipo_empaque_id) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_empaque_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unidad_medida_id">Unidad de Medida</label>
                            <select name="unidad_medida_id" id="unidad_medida_id" class="form-control @error('unidad_medida_id') is-invalid @enderror">
                                <option value="">Seleccione una unidad</option>
                                @foreach($unidadesMedida as $unidad)
                                    <option value="{{ $unidad->id }}" {{ old('unidad_medida_id', $envio->unidad_medida_id) == $unidad->id ? 'selected' : '' }}>
                                        {{ $unidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unidad_medida_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="peso">Peso</label>
                            <input type="number" step="0.01" name="peso" id="peso" class="form-control @error('peso') is-invalid @enderror" value="{{ old('peso', $envio->peso) }}">
                            @error('peso')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="volumen">Volumen (m³)</label>
                            <input type="number" step="0.01" name="volumen" id="volumen" class="form-control @error('volumen') is-invalid @enderror" value="{{ old('volumen', $envio->volumen) }}">
                            @error('volumen')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_envio">Fecha de Envío <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha_envio" id="fecha_envio" class="form-control @error('fecha_envio') is-invalid @enderror" 
                                value="{{ old('fecha_envio', $envio->fecha_envio ? $envio->fecha_envio->format('Y-m-d\TH:i') : '') }}" required>
                            @error('fecha_envio')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_entrega_estimada">Fecha de Entrega Estimada</label>
                            <input type="datetime-local" name="fecha_entrega_estimada" id="fecha_entrega_estimada" class="form-control @error('fecha_entrega_estimada') is-invalid @enderror" 
                                value="{{ old('fecha_entrega_estimada', $envio->fecha_entrega_estimada ? $envio->fecha_entrega_estimada->format('Y-m-d\TH:i') : '') }}">
                            @error('fecha_entrega_estimada')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Las direcciones/rutas se gestionan en el módulo de Direcciones.
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Envío
                    </button>
                    <a href="{{ route('envios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.5.4/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });
    </script>
@stop
