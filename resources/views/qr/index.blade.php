@extends('adminlte::page')

@section('title', 'Códigos QR de Envíos')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-qrcode text-primary"></i> Gestión de Códigos QR</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Códigos QR</li>
            </ol>
        </div>
    </div>
@stop

@section('css')
<style>
    .qr-card {
        transition: all 0.3s;
        border-left: 4px solid #007bff;
    }
    .qr-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .qr-image {
        max-width: 200px;
        border: 3px solid #f8f9fa;
        border-radius: 10px;
        padding: 10px;
        background: white;
    }
    .envio-badge {
        font-size: 1.1rem;
        padding: 10px 15px;
    }
    .cliente-selector {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 40px;
    }
</style>
@stop

@section('content')
<div class="container-fluid">
    <!-- Selector de Cliente -->
    <div class="cliente-selector">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3><i class="fas fa-user-circle"></i> Seleccionar Cliente</h3>
                <p class="mb-0">Elige un cliente para ver sus envíos y generar códigos QR</p>
            </div>
            <div class="col-md-4">
                <select id="cliente-select" class="form-control form-control-lg">
                    <option value="">-- Selecciona un cliente --</option>
                    @foreach($clientes as $cliente)
                        @if($cliente->persona)
                            <option value="{{ $cliente->id }}">
                                {{ $cliente->persona->nombre }} {{ $cliente->persona->apellido }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Botón Leer QR -->
    <div class="text-right mb-3">
        <a href="{{ route('qr.leer') }}" class="btn btn-success btn-lg">
            <i class="fas fa-camera"></i> Leer Código QR
        </a>
    </div>

    <!-- Loading -->
    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
        <p class="mt-3">Cargando envíos...</p>
    </div>

    <!-- Envíos Container -->
    <div id="envios-container" class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Selecciona un cliente para ver sus envíos
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#cliente-select').change(function() {
        const clienteId = $(this).val();
        
        if (!clienteId) {
            $('#envios-container').html(`
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Selecciona un cliente para ver sus envíos
                    </div>
                </div>
            `);
            return;
        }

        // Mostrar loading
        $('.loading-spinner').show();
        $('#envios-container').empty();

        // Cargar envíos del cliente
        $.ajax({
            url: '{{ route("qr.envios-cliente") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_cliente: clienteId
            },
            success: function(response) {
                $('.loading-spinner').hide();
                
                if (response.success && response.envios.length > 0) {
                    let html = '';
                    
                    response.envios.forEach(envio => {
                        const estadoClase = envio.estado_tracking === 'completada' ? 'success' : 
                                          envio.estado_tracking === 'en_ruta' ? 'primary' : 'warning';
                        const estadoTexto = envio.estado_tracking === 'completada' ? 'Entregado' :
                                          envio.estado_tracking === 'en_ruta' ? 'En Ruta' : 'Pendiente';
                        
                        const pesoTotal = parseFloat(envio.peso_total_envio || 0).toFixed(2);
                        const costoTotal = parseFloat(envio.costo_total_envio || 0).toFixed(2);
                        
                        html += `
                            <div class="col-md-6 col-lg-4">
                                <div class="card qr-card" id="card-envio-${envio.id}">
                                    <div class="card-header bg-${estadoClase}">
                                        <h3 class="card-title">
                                            <i class="fas fa-box"></i> Envío #${envio.id}
                                        </h3>
                                        <div class="card-tools">
                                            <span class="badge badge-light">${estadoTexto}</span>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        ${envio.codigo_qr ? `
                                            <img src="/qr/generar/${envio.id}?t=${Date.now()}" class="qr-image mb-3" alt="QR Code" loading="lazy">
                                            <div class="alert alert-success" style="font-size: 1.1rem;">
                                                <i class="fas fa-key"></i> <strong>Código para escanear:</strong><br>
                                                <span style="font-size: 1.3rem; font-weight: bold; letter-spacing: 1px;">${envio.codigo_qr}</span>
                                            </div>
                                            <p class="text-muted"><small>Usa este código en "Leer Código QR"</small></p>
                                        ` : `
                                            <div class="mb-3">
                                                <i class="fas fa-qrcode fa-5x text-muted"></i>
                                                <p class="text-muted mt-2">QR no generado</p>
                                            </div>
                                        `}
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon"><i class="fas fa-weight-hanging"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Peso</span>
                                                        <span class="info-box-number">${pesoTotal} kg</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Costo</span>
                                                        <span class="info-box-number">Bs. ${costoTotal}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        ${envio.direccion ? `
                                            <p class="text-left mb-1"><small><i class="fas fa-map-pin text-success"></i> <strong>Origen:</strong> ${envio.direccion.nombreorigen}</small></p>
                                            <p class="text-left mb-3"><small><i class="fas fa-flag-checkered text-danger"></i> <strong>Destino:</strong> ${envio.direccion.nombredestino}</small></p>
                                        ` : ''}

                                        <div class="btn-group btn-block" role="group">
                                            ${!envio.codigo_qr ? `
                                                <button class="btn btn-primary btn-generar-qr" data-id="${envio.id}">
                                                    <i class="fas fa-qrcode"></i> Generar QR
                                                </button>
                                            ` : `
                                                <a href="/qr/documento/${envio.codigo_qr}" target="_blank" class="btn btn-success">
                                                    <i class="fas fa-file-pdf"></i> Ver Documento
                                                </a>
                                                <a href="/qr/generar/${envio.id}" download="QR-Envio-${envio.id}.svg" class="btn btn-info">
                                                    <i class="fas fa-download"></i> Descargar QR
                                                </a>
                                            `}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    $('#envios-container').html(html);
                } else {
                    $('#envios-container').html(`
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Este cliente no tiene envíos registrados
                            </div>
                        </div>
                    `);
                }
            },
            error: function() {
                $('.loading-spinner').hide();
                $('#envios-container').html(`
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> Error al cargar los envíos
                        </div>
                    </div>
                `);
            }
        });
    });

    // Generar QR
    $(document).on('click', '.btn-generar-qr', function() {
        const btn = $(this);
        const envioId = btn.data('id');
        const card = $(`#card-envio-${envioId}`);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');
        
        // Hacer petición para generar el código QR
        $.ajax({
            url: `/qr/generar-codigo/${envioId}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            timeout: 5000, // 5 segundos timeout
            success: function(response) {
                if (response.success) {
                    // Actualizar solo esta tarjeta sin recargar todo
                    const cardBody = card.find('.card-body');
                    const pesoTotal = cardBody.find('.info-box-number').first().text();
                    const costoTotal = cardBody.find('.info-box-number').last().text();
                    const direccionHTML = cardBody.find('p.text-left').parent().html() || '';
                    
                    cardBody.html(`
                        <img src="/qr/generar/${envioId}?t=${Date.now()}" class="qr-image mb-3" alt="QR Code" loading="lazy">
                        <div class="alert alert-success" style="font-size: 1.1rem;">
                            <i class="fas fa-key"></i> <strong>Código para escanear:</strong><br>
                            <span style="font-size: 1.3rem; font-weight: bold; letter-spacing: 1px;">${response.codigo}</span>
                        </div>
                        <p class="text-muted"><small>Usa este código en "Leer Código QR"</small></p>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon"><i class="fas fa-weight-hanging"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Peso</span>
                                        <span class="info-box-number">${pesoTotal}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Costo</span>
                                        <span class="info-box-number">${costoTotal}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${direccionHTML}

                        <div class="btn-group btn-block" role="group">
                            <a href="/qr/documento/${response.codigo}" target="_blank" class="btn btn-success">
                                <i class="fas fa-file-pdf"></i> Ver Documento
                            </a>
                            <a href="/qr/generar/${envioId}" download="QR-Envio-${envioId}.svg" class="btn btn-info">
                                <i class="fas fa-download"></i> Descargar QR
                            </a>
                        </div>
                    `);
                    
                    // Mostrar toast de éxito
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: '¡Éxito!',
                        subtitle: `Envío #${envioId}`,
                        body: `Código QR generado: <strong>${response.codigo}</strong>`,
                        autohide: true,
                        delay: 4000,
                        icon: 'fas fa-check-circle'
                    });
                }
            },
            error: function(xhr, status) {
                let errorMsg = 'No se pudo generar el código QR';
                if (status === 'timeout') {
                    errorMsg = 'El servidor tardó demasiado en responder';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg,
                    timer: 3000
                });
                btn.prop('disabled', false).html('<i class="fas fa-qrcode"></i> Generar QR');
            }
        });
    });
});
</script>
@stop
