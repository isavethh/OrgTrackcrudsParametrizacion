<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cámara</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        #video {
            width: 100%;
            max-width: 640px;
            border: 3px solid #28a745;
            border-radius: 10px;
            display: none;
        }
        button {
            padding: 15px 30px;
            font-size: 18px;
            margin: 10px;
            cursor: pointer;
        }
        .success {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .danger {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
        }
        #status {
            margin: 20px;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .good {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <h1>🎥 Prueba de Cámara</h1>
    
    <div id="status" class="info">Haz clic en "Iniciar Cámara" para comenzar</div>
    
    <video id="video" autoplay playsinline></video>
    
    <div>
        <button id="startBtn" class="success">▶️ Iniciar Cámara</button>
        <button id="stopBtn" class="danger" style="display:none;">⏹️ Detener Cámara</button>
    </div>
    
    <div style="margin-top: 30px; text-align: left; background: #f5f5f5; padding: 20px; border-radius: 10px;">
        <h3>📋 Log de eventos:</h3>
        <div id="log" style="font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto;"></div>
    </div>

    <script>
        const video = document.getElementById('video');
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const status = document.getElementById('status');
        const log = document.getElementById('log');
        let stream = null;

        function addLog(message, type = 'info') {
            const time = new Date().toLocaleTimeString();
            const color = type === 'error' ? 'red' : type === 'success' ? 'green' : 'blue';
            log.innerHTML += `<div style="color: ${color};">[${time}] ${message}</div>`;
            log.scrollTop = log.scrollHeight;
            console.log(message);
        }

        startBtn.addEventListener('click', async () => {
            addLog('🔵 Botón "Iniciar Cámara" clickeado', 'info');
            
            // Verificar si navigator.mediaDevices está disponible
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                addLog('❌ ERROR: Tu navegador NO soporta getUserMedia', 'error');
                status.className = 'error';
                status.textContent = '❌ Tu navegador no soporta acceso a cámara';
                return;
            }
            
            addLog('✅ Navigator.mediaDevices está disponible', 'success');
            status.className = 'info';
            status.textContent = '⏳ Solicitando permisos de cámara...';
            
            try {
                addLog('📸 Llamando a getUserMedia()...', 'info');
                
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user', // Cambié a 'user' para cámara frontal (más común en laptops)
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                });
                
                addLog('✅ Stream obtenido exitosamente!', 'success');
                addLog(`📹 Tracks en el stream: ${stream.getTracks().length}`, 'info');
                
                stream.getTracks().forEach(track => {
                    addLog(`  - Track: ${track.kind}, estado: ${track.readyState}, enabled: ${track.enabled}`, 'info');
                });
                
                // Asignar stream al video
                video.srcObject = stream;
                addLog('✅ Stream asignado a video.srcObject', 'success');
                
                // Mostrar video
                video.style.display = 'block';
                addLog('✅ Video element display = block', 'success');
                
                // Esperar a que el video cargue metadata
                video.onloadedmetadata = () => {
                    addLog('✅ Video metadata cargada', 'success');
                    addLog(`📐 Dimensiones: ${video.videoWidth}x${video.videoHeight}`, 'info');
                    
                    video.play().then(() => {
                        addLog('✅ Video.play() exitoso - ¡CÁMARA FUNCIONANDO!', 'success');
                        status.className = 'good';
                        status.textContent = '✅ ¡CÁMARA FUNCIONANDO CORRECTAMENTE!';
                        startBtn.style.display = 'none';
                        stopBtn.style.display = 'inline-block';
                    }).catch(err => {
                        addLog(`❌ Error en video.play(): ${err.message}`, 'error');
                        status.className = 'error';
                        status.textContent = '❌ Error al reproducir video';
                    });
                };
                
            } catch (err) {
                addLog(`❌ ERROR en getUserMedia: ${err.name} - ${err.message}`, 'error');
                
                status.className = 'error';
                
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    status.textContent = '❌ Permisos denegados - Haz clic en el 🔒 candado y permite la cámara';
                    addLog('💡 SOLUCIÓN: Ve al candado 🔒 en la barra de direcciones → Permisos → Cámara → Permitir', 'info');
                } else if (err.name === 'NotFoundError') {
                    status.textContent = '❌ No se encontró cámara en el dispositivo';
                } else if (err.name === 'NotReadableError') {
                    status.textContent = '❌ Cámara en uso por otra aplicación';
                    addLog('💡 SOLUCIÓN: Cierra otras aplicaciones que usen la cámara (Zoom, Teams, etc)', 'info');
                } else {
                    status.textContent = `❌ Error: ${err.name}`;
                }
            }
        });

        stopBtn.addEventListener('click', () => {
            addLog('🔴 Deteniendo cámara...', 'info');
            
            if (stream) {
                stream.getTracks().forEach(track => {
                    track.stop();
                    addLog(`✅ Track ${track.kind} detenido`, 'success');
                });
                stream = null;
            }
            
            video.srcObject = null;
            video.style.display = 'none';
            startBtn.style.display = 'inline-block';
            stopBtn.style.display = 'none';
            
            status.className = 'info';
            status.textContent = 'Cámara detenida. Puedes iniciarla nuevamente.';
            addLog('✅ Cámara detenida correctamente', 'success');
        });

        // Log inicial
        addLog('🚀 Página cargada - Sistema listo', 'success');
        addLog(`🌐 Navegador: ${navigator.userAgent}`, 'info');
        addLog(`🔒 Protocolo: ${window.location.protocol}`, 'info');
        addLog(`📍 URL: ${window.location.href}`, 'info');
    </script>
</body>
</html>
