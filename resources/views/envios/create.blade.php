@extends('adminlte::page')

@section('title', 'Crear Envío')

@section('content_header')
    <h1>Crear Nuevo Envío</h1>
@stop

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Errores de validación:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('envios.store') }}" method="POST" id="form-envio">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Información del Envío</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="id_usuario">Usuario/Cliente <span class="text-danger">*</span></label>
                            <select name="id_usuario" id="id_usuario" class="form-control @error('id_usuario') is-invalid @enderror" required>
                                <option value="">Seleccione un usuario</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('id_usuario') == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->nombre }} {{ $usuario->apellido }} ({{ $usuario->correo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_usuario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

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
                                <i class="fas fa-info-circle"></i> <a href="{{ route('direcciones.create') }}" target="_blank">¿Crear nueva ruta?</a>
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_entrega_aproximada">Fecha Entrega Estimada</label>
                                    <input type="date" name="fecha_entrega_aproximada" id="fecha_entrega_aproximada" class="form-control @error('fecha_entrega_aproximada') is-invalid @enderror" value="{{ old('fecha_entrega_aproximada') }}">
                                    @error('fecha_entrega_aproximada')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hora_entrega_aproximada">Hora Entrega Estimada</label>
                                    <input type="time" name="hora_entrega_aproximada" id="hora_entrega_aproximada" class="form-control @error('hora_entrega_aproximada') is-invalid @enderror" value="{{ old('hora_entrega_aproximada') }}">
                                    @error('hora_entrega_aproximada')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Totales del Envío</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-weight"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Peso Total</span>
                                        <span class="info-box-number" id="display-peso-total">0.00 kg</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Costo Total</span>
                                        <span class="info-box-number" id="display-costo-total">Bs. 0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-box"></i> Productos del Envío</h3>
            </div>
            <div class="card-body">
                <div id="productos-container">
                    <!-- Productos dinámicos aquí -->
                </div>
                
                <button type="button" class="btn btn-success btn-block" id="btn-agregar-producto">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Crear Envío
                </button>
                <a href="{{ route('envios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
    let productoIndex = 0;

    // Productos hardcodeados
    const productosPorCategoria = {
        'Verduras': ['Tomate', 'Lechuga', 'Zanahoria'],
        'Frutas': ['Manzana', 'Naranja', 'Plátano']
    };

    // Plantilla de producto
    function crearProductoHTML(index) {
        return `
            <div class="card producto-item mb-3" data-index="${index}">
                <div class="card-header bg-light">
                    <h5 class="card-title">Producto #${index + 1}</h5>
                    <button type="button" class="btn btn-danger btn-sm float-right btn-eliminar-producto" data-index="${index}">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Categoría <span class="text-danger">*</span></label>
                                <select name="productos[${index}][categoria]" class="form-control categoria-select" data-index="${index}" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Verduras">Verduras</option>
                                    <option value="Frutas">Frutas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Producto <span class="text-danger">*</span></label>
                                <select name="productos[${index}][producto]" class="form-control producto-select" data-index="${index}" required disabled>
                                    <option value="">Primero elija categoría</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo Empaque <span class="text-danger">*</span></label>
                                <select name="productos[${index}][id_tipo_empaque]" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($tiposEmpaque as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                <small><a href="{{ route('tipos-empaque.create') }}" target="_blank">+ Crear nuevo</a></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Unidad Medida <span class="text-danger">*</span></label>
                                <select name="productos[${index}][id_unidad_medida]" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($unidadesMedida as $unidad)
                                        <option value="{{ $unidad->id }}">{{ $unidad->nombre }} ({{ $unidad->abreviatura }})</option>
                                    @endforeach
                                </select>
                                <small><a href="{{ route('unidades-medida.create') }}" target="_blank">+ Crear nueva</a></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cantidad <span class="text-danger">*</span></label>
                                <input type="number" name="productos[${index}][cantidad]" class="form-control calc-field" data-index="${index}" min="1" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Peso por Unidad <span class="text-danger">*</span></label>
                                <input type="number" name="productos[${index}][peso_por_unidad]" class="form-control calc-field" data-index="${index}" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Costo Unitario (Bs.) <span class="text-danger">*</span></label>
                                <input type="number" name="productos[${index}][costo_unitario]" class="form-control calc-field" data-index="${index}" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-info mt-4 p-2">
                                <small><strong>Peso total:</strong> <span class="peso-producto" data-index="${index}">0.00</span> kg</small><br>
                                <small><strong>Costo total:</strong> Bs. <span class="costo-producto" data-index="${index}">0.00</span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Agregar producto
    $('#btn-agregar-producto').click(function() {
        const html = crearProductoHTML(productoIndex);
        $('#productos-container').append(html);
        productoIndex++;
    });

    // Eliminar producto
    $(document).on('click', '.btn-eliminar-producto', function() {
        const index = $(this).data('index');
        $(`.producto-item[data-index="${index}"]`).remove();
        calcularTotalesGlobales();
    });

    // Cambio de categoría
    $(document).on('change', '.categoria-select', function() {
        const index = $(this).data('index');
        const categoria = $(this).val();
        const productoSelect = $(`.producto-select[data-index="${index}"]`);
        
        productoSelect.empty();
        
        if (categoria && productosPorCategoria[categoria]) {
            productoSelect.prop('disabled', false);
            productoSelect.append('<option value="">Seleccione producto...</option>');
            productosPorCategoria[categoria].forEach(prod => {
                productoSelect.append(`<option value="${prod}">${prod}</option>`);
            });
        } else {
            productoSelect.prop('disabled', true);
            productoSelect.append('<option value="">Primero elija categoría</option>');
        }
    });

    // Calcular totales por producto
    $(document).on('input', '.calc-field', function() {
        const index = $(this).data('index');
        const card = $(`.producto-item[data-index="${index}"]`);
        
        const cantidad = parseFloat(card.find(`input[name="productos[${index}][cantidad]"]`).val()) || 0;
        const pesoPorUnidad = parseFloat(card.find(`input[name="productos[${index}][peso_por_unidad]"]`).val()) || 0;
        const costoUnitario = parseFloat(card.find(`input[name="productos[${index}][costo_unitario]"]`).val()) || 0;
        
        const pesoTotal = cantidad * pesoPorUnidad;
        const costoTotal = cantidad * costoUnitario;
        
        card.find(`.peso-producto[data-index="${index}"]`).text(pesoTotal.toFixed(2));
        card.find(`.costo-producto[data-index="${index}"]`).text(costoTotal.toFixed(2));
        
        calcularTotalesGlobales();
    });

    // Calcular totales globales
    function calcularTotalesGlobales() {
        let pesoTotal = 0;
        let costoTotal = 0;
        
        $('.peso-producto').each(function() {
            pesoTotal += parseFloat($(this).text()) || 0;
        });
        
        $('.costo-producto').each(function() {
            costoTotal += parseFloat($(this).text()) || 0;
        });
        
        $('#display-peso-total').text(pesoTotal.toFixed(2) + ' kg');
        $('#display-costo-total').text('Bs. ' + costoTotal.toFixed(2));
    }

    // Validación antes de enviar
    $('#form-envio').submit(function(e) {
        if ($('.producto-item').length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto al envío');
            return false;
        }
    });

    // Agregar primer producto automáticamente
    $(document).ready(function() {
        $('#btn-agregar-producto').click();
    });
</script>
@stop
