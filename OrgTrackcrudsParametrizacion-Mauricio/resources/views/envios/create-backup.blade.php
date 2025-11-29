@extends('adminlte::page')

@section('title', 'Crear Envío')

@section('content_header')
    <h1>Crear Nuevo Envío</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('envios.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_usuario">Usuario/Cliente <span class="text-danger">*</span></label>
                            <select name="id_usuario" id="id_usuario" class="form-control @error('id_usuario') is-invalid @enderror" required>
                                <option value="">Seleccione un usuario</option>
                                @foreach($usuarios as $usuario)
                                    @if($usuario->persona)
                                        <option value="{{ $usuario->id }}" {{ old('id_usuario') == $usuario->id ? 'selected' : '' }}>
                                            {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }} ({{ $usuario->correo }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('id_usuario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_direccion">Dirección/Ruta <span class="text-danger">*</span></label>
                            <select name="id_direccion" id="id_direccion" class="form-control @error('id_direccion') is-invalid @enderror" required>
                                <option value="">Seleccione una dirección</option>
                                @foreach($direcciones as $direccion)
                                    <option value="{{ $direccion->id }}" {{ old('id_direccion') == $direccion->id ? 'selected' : '' }}>
                                        📍 {{ $direccion->nombreorigen }} → 🏁 {{ $direccion->nombredestino }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_direccion')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Selecciona la ruta que seguirá este envío. 
                                <a href="{{ route('direcciones.create') }}" target="_blank">¿Crear nueva ruta?</a>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio</label>
                            <input type="datetime-local" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}">
                            @error('fecha_inicio')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_entrega">Fecha de Entrega Estimada</label>
                            <input type="datetime-local" name="fecha_entrega" id="fecha_entrega" class="form-control @error('fecha_entrega') is-invalid @enderror" value="{{ old('fecha_entrega') }}">
                            @error('fecha_entrega')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-clock"></i> <small>Las fechas son opcionales. El sistema registrará automáticamente la fecha de creación.</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>Importante:</strong> Este envío aparecerá en "Rutas en Tiempo Real" una vez creado, donde podrás iniciar el seguimiento.
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Crear Envío
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

            // Calcular peso total automáticamente
            function calcularPesoTotal() {
                var pesoPorUnidad = parseFloat($('#peso_por_unidad').val()) || 0;
                var cantidadProductos = parseInt($('#cantidad_productos').val()) || 0;
                var pesoTotal = pesoPorUnidad * cantidadProductos;
                
                if (pesoTotal > 0) {
                    $('#peso').val(pesoTotal.toFixed(2));
                } else {
                    $('#peso').val('');
                }
            }

            // Ejecutar cálculo cuando cambien los campos
            $('#peso_por_unidad, #cantidad_productos').on('input change', function() {
                calcularPesoTotal();
            });

            // Calcular al cargar si hay valores antiguos
            calcularPesoTotal();
        });
    </script>
@stop
