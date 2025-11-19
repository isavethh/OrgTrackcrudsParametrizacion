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
                                    <option value="{{ $unidad->id }}" data-unidad="{{ $unidad->nombre }}" {{ old('unidad_medida_id', $envio->unidad_medida_id) == $unidad->id ? 'selected' : '' }}>
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="direccion_id">Dirección/Ruta</label>
                            <select name="direccion_id" id="direccion_id" class="form-control select2 @error('direccion_id') is-invalid @enderror">
                                <option value="">Seleccione una dirección</option>
                                @foreach($direcciones as $direccion)
                                    <option value="{{ $direccion->id }}" 
                                        {{ old('direccion_id', $envio->direcciones->first()?->id) == $direccion->id ? 'selected' : '' }}>
                                        {{ $direccion->nombre_ruta }}
                                        @if($direccion->descripcion)
                                            - {{ Str::limit($direccion->descripcion, 50) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('direccion_id')
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
                            <label for="peso_por_unidad" id="label_peso_unidad">Peso por Unidad (kg)</label>
                            <input type="number" step="0.01" name="peso_por_unidad" id="peso_por_unidad" class="form-control @error('peso_por_unidad') is-invalid @enderror" value="{{ old('peso_por_unidad', $envio->peso_por_unidad) }}">
                            @error('peso_por_unidad')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cantidad_productos">Cantidad de Productos</label>
                            <input type="number" name="cantidad_productos" id="cantidad_productos" class="form-control @error('cantidad_productos') is-invalid @enderror" value="{{ old('cantidad_productos', $envio->cantidad_productos) }}" min="1">
                            @error('cantidad_productos')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="peso" id="label_peso_total">Peso Total (kg) <small class="text-muted">(Calculado automáticamente)</small></label>
                            <input type="number" step="0.01" name="peso" id="peso" class="form-control @error('peso') is-invalid @enderror" value="{{ old('peso', $envio->peso) }}" readonly style="background-color: #e9ecef;">
                            @error('peso')
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

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>Importante:</strong> Puedes cambiar la dirección/ruta de este envío seleccionando una diferente del listado.
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

            // Función para actualizar las etiquetas según la unidad de medida
            function actualizarEtiquetas() {
                var unidadSeleccionada = $('#unidad_medida_id option:selected').data('unidad') || 'kg';
                $('#label_peso_unidad').html('Peso por Unidad (' + unidadSeleccionada + ')');
                $('#label_peso_total').html('Peso Total (' + unidadSeleccionada + ') <small class="text-muted">(Calculado automáticamente)</small>');
            }

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

            // Actualizar etiquetas cuando cambie la unidad de medida
            $('#unidad_medida_id').on('change', function() {
                actualizarEtiquetas();
            });

            // Ejecutar cálculo cuando cambien los campos
            $('#peso_por_unidad, #cantidad_productos').on('input change', function() {
                calcularPesoTotal();
            });

            // Ejecutar al cargar la página
            actualizarEtiquetas();
            calcularPesoTotal();
        });
    </script>
@stop
