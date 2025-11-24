    .scan-overlay.detected {
        border-color: #ffc107;
        box-shadow: 0 0 25px rgba(255,193,7,0.65) inset;
    }
@extends('adminlte::page')

@section('title', 'Leer Código QR')

@section('content_header')
    <h1><i class="fas fa-camera"></i> Escanear Código QR</h1>
@stop

@section('css')
<style>
    .scanner-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
        border-radius: 20px;
        color: white;
    }
    .resultado-scan {
        display: none;
        margin-top: 30px;
    }
    .qr-simulator {
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        color: #333;
        min-height: 400px;
    }
    .qr-animation {
        animation: scanPulse 2s infinite;
    }
    @keyframes scanPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
    }
    #reader {
        border: 3px solid #28a745;
        border-radius: 10px;
        overflow: hidden;
    }
    #reader video {
        width: 100% !important;
        max-width: 500px;
        border-radius: 8px;
    }
    #camera-preview {
        width: 100%;
        display: block;
        border-radius: 12px;
    }
    .camera-viewport {
        position: relative;
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        border: 3px solid #28a745;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        aspect-ratio: 4 / 3;
    }
    .scan-overlay {
        position: absolute;
        top: 10%;
        left: 10%;
        width: 80%;
        height: 80%;
        border: 3px solid rgba(255,255,255,0.35);
        border-radius: 20px;
        pointer-events: none;
        box-shadow: 0 0 25px rgba(0,0,0,0.35) inset;
    }
    .scan-overlay .laser {
        position: absolute;
        left: 5%;
        width: 90%;
        height: 4px;
        background: linear-gradient(90deg, transparent, #28a745, transparent);
        box-shadow: 0 0 12px rgba(40,167,69,0.8);
        animation: laserMove 2s linear infinite;
    }
    @keyframes laserMove {
        0% { top: 12%; }
        50% { top: 85%; }
        100% { top: 12%; }
    }
</style>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="scanner-container">
                <h3 class="text-center mb-4">
                    <i class="fas fa-qrcode fa-2x"></i><br>
                    Escanear o Buscar Código QR
                </h3>
                
                <!-- Tabs para elegir método -->
                <ul class="nav nav-pills nav-justified mb-4" id="scanTabs" role="tablist" style="background: white; border-radius: 10px; padding: 5px;">
                    <li class="nav-item">
                        <a class="nav-link active" id="manual-tab" data-toggle="pill" href="#manual" role="tab">
                            <i class="fas fa-keyboard"></i> Ingresar Código
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="camera-tab" data-toggle="pill" href="#camera" role="tab">
                            <i class="fas fa-camera"></i> Escanear con Cámara
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="scanTabsContent">
                    <!-- Tab Manual -->
                    <div class="tab-pane fade show active" id="manual" role="tabpanel">
                        <div class="qr-simulator">
                            <div id="scanner-status">
                                <i class="fas fa-keyboard fa-4x text-primary mb-3"></i>
                                <h4>Ingresa el código manualmente</h4>
                                <p class="text-muted">Copia y pega el código que aparece en tu envío</p>
                                
                                <div class="alert alert-info mx-auto" style="max-width: 500px;">
                                    <i class="fas fa-info-circle"></i> <strong>¿Dónde encontrar el código?</strong><br>
                                    <small>Ve a <strong>Códigos QR</strong>, selecciona tu cliente, genera el QR y copia el código del cuadro verde.</small>
                                </div>
                                
                                <div class="form-group mt-4">
                                    <label for="codigo-input" class="font-weight-bold">Código del Envío:</label>
                                    <input type="text" id="codigo-input" class="form-control form-control-lg text-center" 
                                           placeholder="Ej: ENV-ABC123XYZ" style="max-width: 450px; margin: 0 auto; font-size: 1.2rem; letter-spacing: 1px;">
                                    <small class="text-muted">Escribe o pega el código aquí</small>
                                </div>
                                
                                <button id="btn-buscar-manual" class="btn btn-primary btn-lg mt-3">
                                    <i class="fas fa-search"></i> Buscar Envío
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Cámara -->
                    <div class="tab-pane fade" id="camera" role="tabpanel">
                        <div class="qr-simulator">
                            <div id="camera-container">
                                <div id="camera-placeholder" class="mb-3">
                                    <i class="fas fa-camera fa-4x text-success mb-3"></i>
                                    <h4>Escanea el código QR</h4>
                                    <p class="text-muted">Apunta tu cámara al código QR y espera a que se detecte automáticamente</p>
                                </div>

                                <div class="camera-viewport" style="display:none;">
                                    <video id="camera-preview" autoplay playsinline muted></video>
                                    <div class="scan-overlay">
                                        <span class="laser"></span>
                                    </div>
                                </div>

                                <canvas id="camera-canvas" style="display:none;"></canvas>

                                <div id="camera-status" class="alert alert-secondary" style="max-width: 500px; margin: 15px auto;">
                                    <i class="fas fa-info-circle"></i> Cámara aún no iniciada
                                </div>
                                
                                <div id="camera-debug" class="alert alert-light text-left" style="display:none; max-width: 500px; margin: 0 auto; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;"></div>
                                
                                <div class="mt-4">
                                    <button id="btn-iniciar-camera" class="btn btn-success btn-lg">
                                        <i class="fas fa-play"></i> Iniciar Cámara
                                    </button>
                                    <button id="btn-detener-camera" class="btn btn-danger btn-lg" style="display:none;">
                                        <i class="fas fa-stop"></i> Detener Cámara
                                    </button>
                                </div>

                                <div class="alert alert-warning mt-3" style="max-width: 500px; margin: 0 auto;">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>Permisos de cámara:</strong> Tu navegador te pedirá permiso para acceder a la cámara.
                                </div>

                                <div id="camera-result-card" class="alert alert-success mt-3 text-center" style="display:none; max-width: 520px; margin: 0 auto;">
                                    <h5 class="font-weight-bold mb-1">Acceso exitoso ✅</h5>
                                    <p class="mb-2 text-muted" id="camera-result-code"></p>
                                    <div class="d-flex flex-column flex-md-row justify-content-center" style="gap:10px;">
                                        <a id="camera-btn-open" href="#" target="_blank" class="btn btn-success mb-2 mb-md-0" style="display:none;">
                                            <i class="fas fa-eye"></i> Abrir PDF
                                        </a>
                                        <a id="camera-btn-download" href="#" class="btn btn-outline-success" style="display:none;">
                                            <i class="fas fa-download"></i> Descargar PDF
                                        </a>
                                    </div>
                                    <small class="d-block mt-2">Si no se abre automático, presiona cualquiera de los botones.</small>
                                </div>
                            </div>
                            
                            <div id="scanning-animation" style="display:none;">
                                <div class="qr-animation">
                                    <i class="fas fa-qrcode fa-5x text-success mb-3"></i>
                                </div>
                                <h4 class="text-success">
                                    <i class="fas fa-spinner fa-spin"></i> Procesando código...
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="resultado-scan">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-check-circle"></i> ¡Código Escaneado!</h3>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-box-open fa-4x text-success mb-3"></i>
                        <h4>Código detectado correctamente</h4>
                        <p class="text-success font-weight-bold mb-1">Acceso exitoso ✅</p>
                        <p class="text-muted" id="codigo-escaneado"></p>
                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2 mt-3">
                            <a id="btn-ver-documento" href="#" target="_blank" class="btn btn-success btn-lg mb-2 mb-md-0" style="display:none;">
                                <i class="fas fa-eye"></i> Abrir PDF
                            </a>
                            <a id="btn-descargar-documento" href="#" class="btn btn-outline-success btn-lg" style="display:none;">
                                <i class="fas fa-download"></i> Descargar PDF
                            </a>
                        </div>
                        <small class="text-muted d-block mt-3">Si no se abre automáticamente, usa cualquiera de los botones.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('qr.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Volver a Códigos QR
            </a>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/jsqr.min.js') }}"></script>
<script>
let scanning = false;
let cameraStream = null;
let scanAnimationFrame = null;
let videoElement = null;
let canvasElement = null;
let canvasContext = null;

function cameraLog(message, type = 'info') {
    const box = document.getElementById('camera-debug');
    if (!box) {
        console.log(message);
        return;
    }

    const time = new Date().toLocaleTimeString();
    const color = type === 'error' ? '#c53030' : (type === 'success' ? '#2f855a' : '#2b6cb0');
    const line = `<div style="color:${color};">[${time}] ${message}</div>`;

    if (box.style.display === 'none') {
        box.style.display = 'block';
        box.innerHTML = '';
    }

    box.innerHTML += line;
    box.scrollTop = box.scrollHeight;
    console.log(message);
}

function updateCameraStatus(message, type = 'secondary') {
    const statusBox = document.getElementById('camera-status');
    if (!statusBox) return;
    const classes = {
        secondary: 'alert alert-secondary',
        info: 'alert alert-info',
        success: 'alert alert-success',
        warning: 'alert alert-warning',
        danger: 'alert alert-danger'
    };
    statusBox.className = classes[type] || classes.secondary;
    statusBox.innerHTML = message;
}

$(document).ready(function() {
    videoElement = document.getElementById('camera-preview');
    canvasElement = document.getElementById('camera-canvas');
    if (canvasElement) {
        canvasContext = canvasElement.getContext('2d');
    }

    cameraLog('✅ Página lista');
    cameraLog('✅ Botón iniciar encontrado: ' + $('#btn-iniciar-camera').length);
    cameraLog('✅ Botón detener encontrado: ' + $('#btn-detener-camera').length);
    cameraLog('✅ Video element encontrado: ' + (videoElement ? 1 : 0));
    cameraLog('✅ jsQR disponible: ' + (typeof window.jsQR));
    if (typeof window.jsQR !== 'function') {
        updateCameraStatus('<i class="fas fa-exclamation-triangle"></i> No se pudo cargar jsQR. Verifica la conexión o recarga la página.', 'danger');
    }
    
    // ===== BÚSQUEDA MANUAL =====
    $('#btn-buscar-manual').click(function() {
        const codigo = $('#codigo-input').val().trim();
        
        if (!codigo) {
            Swal.fire({
                icon: 'warning',
                title: 'Código requerido',
                text: 'Por favor ingresa un código QR',
            });
            return;
        }
        
        buscarPorCodigo(codigo, 'manual');
    });
    
    // Permitir Enter en el input
    $('#codigo-input').keypress(function(e) {
        if (e.which == 13) {
            $('#btn-buscar-manual').click();
        }
    });
    
    // ===== ESCANEO CON CÁMARA =====
    $('#btn-iniciar-camera').click(function(e) {
        e.preventDefault();
        console.log('🔵 Botón Iniciar Cámara clickeado');
        iniciarCamara();
    });
    
    $('#btn-detener-camera').click(function(e) {
        e.preventDefault();
        console.log('🔴 Botón Detener Cámara clickeado');
        detenerCamara();
    });
});

async function iniciarCamara() {
    cameraLog('🎥 Iniciando cámara...');
    updateCameraStatus('<i class="fas fa-spinner fa-spin"></i> Abriendo cámara, espera unos segundos...', 'info');
    
    if (scanning) {
        cameraLog('⚠️ Ya hay una cámara activa');
        return;
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        Swal.fire({
            icon: 'error',
            title: 'Navegador no compatible',
            text: 'Tu navegador no soporta acceso a la cámara'
        });
        return;
    }

    try {
        $('#camera-placeholder').hide();
        $('.camera-viewport').show();
        $('#camera-debug').show();

        cameraLog('🔑 Solicitando permisos con getUserMedia...');
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: 'environment' },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });

        videoElement.srcObject = cameraStream;
        await videoElement.play();

        scanning = true;
        $('#btn-iniciar-camera').hide();
        $('#btn-detener-camera').show();
        updateCameraStatus('<i class="fas fa-check text-success"></i> Cámara activa. Escanea tu código.', 'success');
        cameraLog('🚀 Cámara iniciada correctamente', 'success');

        iniciarEscaneoFrames();
    } catch (err) {
        console.error(err);
        cameraLog('❌ Error al acceder a la cámara: ' + err.message, 'error');
        updateCameraStatus('<i class="fas fa-times"></i> No se pudo acceder a la cámara: ' + err.message, 'danger');
        Swal.fire({
            icon: 'error',
            title: 'No se pudo encender la cámara',
            html: `<p>${err.message}</p>
                   <ul style="text-align:left;">
                       <li>Verifica los permisos en el candado de la barra del navegador.</li>
                       <li>Cierra otras apps que usen la cámara (Zoom, Meet, Teams...).</li>
                       <li>Recarga la página y vuelve a intentarlo.</li>
                   </ul>`
        });
        $('#camera-placeholder').show();
        $('.camera-viewport').hide();
    }
}

function iniciarEscaneoFrames() {
    if (!videoElement || !canvasElement || !canvasContext) {
        cameraLog('⚠️ No se encontró video/canvas para escanear', 'error');
        return;
    }

    const scanLoop = () => {
        if (!scanning) return;

        if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
            const videoWidth = videoElement.videoWidth || 640;
            const videoHeight = videoElement.videoHeight || 480;
            const scanSize = Math.floor(Math.min(videoWidth, videoHeight) * 0.8);
            const startX = Math.floor((videoWidth - scanSize) / 2);
            const startY = Math.floor((videoHeight - scanSize) / 2);

            if (canvasElement.width !== scanSize) {
                canvasElement.width = scanSize;
                canvasElement.height = scanSize;
                cameraLog(`🎯 Tamaño del recorte: ${scanSize}x${scanSize}`);
            }

            canvasContext.drawImage(
                videoElement,
                startX,
                startY,
                scanSize,
                scanSize,
                0,
                0,
                scanSize,
                scanSize
            );

            const imageData = canvasContext.getImageData(0, 0, scanSize, scanSize);
            const code = window.jsQR ? jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: 'attemptBoth'
            }) : null;

            if (code) {
                drawBoundingBox(code.location);
                cameraLog('✅ Código detectado: ' + code.data, 'success');
                scanning = false;
                detenerCamara();
                procesarQRCamera(code.data);
                return;
            }
        }

        scanAnimationFrame = requestAnimationFrame(scanLoop);
    };

    scanAnimationFrame = requestAnimationFrame(scanLoop);
}

function drawBoundingBox(location) {
    if (!canvasContext || !location) return;
    const overlay = document.querySelector('.scan-overlay');
    if (overlay) {
        overlay.classList.add('detected');
        setTimeout(() => overlay.classList.remove('detected'), 1200);
    }
}

function detenerCamara() {
    cameraLog('🛑 Deteniendo cámara...');
    updateCameraStatus('<i class="fas fa-stop"></i> Cámara detenida. Puedes iniciarla nuevamente.', 'secondary');

    scanning = false;

    if (scanAnimationFrame) {
        cancelAnimationFrame(scanAnimationFrame);
        scanAnimationFrame = null;
    }

    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }

    if (videoElement) {
        videoElement.pause();
        videoElement.srcObject = null;
    }

    $('#btn-iniciar-camera').show();
    $('#btn-detener-camera').hide();
    $('#camera-placeholder').show();
    $('.camera-viewport').hide();
    const overlay = document.querySelector('.scan-overlay');
    if (overlay) {
        overlay.classList.remove('detected');
    }
}

function procesarQRCamera(rawValue) {
    const valor = (rawValue || '').trim();
    cameraLog('📦 Dato recibido del QR: ' + valor);
    
    $('#camera-container').hide();
    $('#scanning-animation').fadeIn();
    $('#camera-debug').hide();
    
    const match = valor.match(/\/qr\/documento\/([^\/\?]+)/);
    const codigo = match && match[1] ? match[1].trim() : valor;
    
    setTimeout(() => {
        buscarPorCodigo(codigo, 'camera');
    }, 600);
}

// ===== FUNCIÓN COMÚN DE BÚSQUEDA =====
function buscarPorCodigo(codigo, origen = 'manual') {
    cameraLog('🔎 Buscando envío con código: ' + codigo);
    // Mostrar animación
    if ($('#manual').hasClass('active')) {
        $('#scanner-status').hide();
    }
    $('#scanning-animation').fadeIn();
    $('#btn-ver-documento, #btn-descargar-documento').hide().attr('href', '#');
    $('#camera-btn-open, #camera-btn-download').hide().attr('href', '#');
    $('#camera-result-card').hide();
    $('#camera-result-code').text('');
    
    // Buscar el código
    $.ajax({
        url: '{{ route("qr.buscar") }}',
        method: 'POST',
        data: {
            codigo: codigo,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            cameraLog('✅ Respuesta recibida: ' + JSON.stringify(response));
            $('#scanning-animation').hide();
            $('#camera-container').show();
            $('#scanner-status').show();
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡QR Encontrado!',
                text: 'Código detectado correctamente',
                timer: 1500,
                showConfirmButton: false
            });
            
            // Mostrar resultado
            $('#codigo-escaneado').html('<strong>Código:</strong> ' + codigo);
            if (response && response.url) {
                $('#camera-result-code').text('Código: ' + codigo);
                $('#camera-result-card').fadeIn();
                $('#camera-btn-open').attr('href', response.url).show();
                $('#camera-btn-download').attr('href', response.url + '?download=1').show();
                $('#btn-ver-documento').attr('href', response.url).show();
                $('#btn-descargar-documento').attr('href', response.url + '?download=1').show();

                if (origen === 'camera') {
                    setTimeout(() => {
                        cameraLog('➡️ Redirigiendo automáticamente al documento...', 'success');
                        try {
                            window.location.href = response.url;
                        } catch (err) {
                            cameraLog('⚠️ No se pudo redirigir automáticamente: ' + err.message, 'error');
                        }
                    }, 350);
                }
            }
            $('.resultado-scan').fadeIn();
            
            // Scroll suave al resultado
            $('html, body').animate({
                scrollTop: $('.resultado-scan').offset().top - 100
            }, 500);
        },
        error: function(xhr) {
            cameraLog('❌ Error en búsqueda: ' + xhr.status + ' ' + xhr.responseText, 'error');
            $('#scanning-animation').hide();
            $('#camera-container').show();
            $('#scanner-status').show();
            
            let errorMsg = 'Código QR no encontrado';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
            });
        }
    });
}

// Limpiar al salir o cambiar de tab
$(window).on('beforeunload', function() {
    if (scanning) {
        detenerCamara();
    }
});

// Detener cámara al cambiar de tab
$('#manual-tab').on('shown.bs.tab', function() {
    if (scanning) {
        detenerCamara();
    }
});
</script>
@stop
