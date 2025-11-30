<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de Envío - OrgTrack</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .validation-card {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .code-input {
            font-size: 1.25rem;
            text-align: center;
            letter-spacing: 0.15em;
            font-weight: bold;
            text-transform: uppercase;
        }
        .signature-canvas {
            border: 2px dashed #ccc;
            border-radius: 8px;
            cursor: crosshair;
            touch-action: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .code-input {
                font-size: 1rem;
                letter-spacing: 0.1em;
            }
            .validation-card .card-body {
                padding: 2rem 1.5rem !important;
            }
            .card-title {
                font-size: 1.5rem !important;
            }
            .icon-circle {
                width: 60px !important;
                height: 60px !important;
            }
            .icon-circle i {
                font-size: 2rem !important;
            }
            .info-box-number, .small-box h4 {
                font-size: 1rem !important;
            }
            .table {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Sección de Ingreso de Código -->
        <div id="seccion-codigo" class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="card card-primary card-outline validation-card">
                    <div class="card-body text-center p-4 p-md-5">
                        <div class="mb-4">
                            <div class="icon-circle d-inline-flex align-items-center justify-content-center bg-primary rounded-circle" style="width: 80px; height: 80px;">
                                <i class="fas fa-lock text-white" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                        <div class="w-100">
                            <h2 class="mb-3" style="font-size: 1.8rem; font-weight: 700;">Validación de Envío</h2>
                            <p class="text-muted mb-4">Ingrese el código de acceso proporcionado para ver el documento</p>
                        </div>
                        
                        <div class="form-group">
                            <input type="text" id="inputCodigo" class="form-control form-control-lg code-input" placeholder="CÓDIGO" maxlength="10">
                        </div>
                        
                        <button id="btnValidarCodigo" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-check-circle mr-2"></i>
                            Validar Código
                        </button>
                        
                        <div id="mensajeError" class="alert alert-danger mt-3" style="display: none;">
                            <i class="icon fas fa-ban"></i>
                            <span id="textoError"></span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Conexión segura - OrgTrack
                    </small>
                </div>
            </div>
        </div>

        <!-- Contenedor del Documento -->
        <div id="contenedor-particion" style="display: none;">
            <!-- Content injected by JS -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <script>
document.addEventListener("DOMContentLoaded", async () => {
    const seccionCodigo = document.getElementById("seccion-codigo");
    const inputCodigo = document.getElementById("inputCodigo");
    const btnValidarCodigo = document.getElementById("btnValidarCodigo");
    const mensajeError = document.getElementById("mensajeError");
    const textoError = document.getElementById("textoError");
    const contenedorParticion = document.getElementById("contenedor-particion");
    
    let idAsignacionActual = null;

    async function validarCodigo() {
        const codigo = inputCodigo.value.trim().toUpperCase();
        if (!codigo) {
            mostrarError("Por favor ingrese un código.");
            return;
        }

        mostrarError("", false);
        btnValidarCodigo.disabled = true;
        btnValidarCodigo.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validando...';

        try {
            const response = await fetch('/api/qr/codigoacceso', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ codigo: codigo })
            });

            const data = await response.json();

            if (response.ok && data.valido) {
                idAsignacionActual = data.asignacion.id_asignacion;
                renderizarDocumento(data.asignacion);
                seccionCodigo.style.display = 'none';
                contenedorParticion.style.display = 'block';
            } else {
                mostrarError(data.error || "Código inválido. Intente nuevamente.");
            }
        } catch (error) {
            console.error("Error:", error);
            mostrarError("Error de conexión. Verifique su internet.");
        } finally {
            btnValidarCodigo.disabled = false;
            btnValidarCodigo.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Validar Código';
        }
    }

    function mostrarError(msg, show = true) {
        if(textoError) textoError.textContent = msg;
        mensajeError.style.display = show ? 'block' : 'none';
    }

    btnValidarCodigo.addEventListener("click", validarCodigo);
    inputCodigo.addEventListener("keypress", (e) => {
        if (e.key === "Enter") validarCodigo();
    });

    function getBadgeClass(status) {
        switch(status?.toLowerCase()) {
            case 'entregado': return 'badge-success';
            case 'en curso': return 'badge-primary';
            case 'pendiente': return 'badge-warning';
            case 'cancelado': return 'badge-danger';
            default: return 'badge-secondary';
        }
    }

    function renderizarDocumento(asignacion) {
        const fechaRecogida = asignacion.recogida_entrega?.fecha_recogida 
            ? new Date(asignacion.recogida_entrega.fecha_recogida).toLocaleDateString("es-ES")
            : new Date().toLocaleDateString("es-ES");

        const horaRecogida = asignacion.recogida_entrega?.hora_recogida?.slice(0, 5) || 'N/A';
        const horaEntrega = asignacion.recogida_entrega?.hora_entrega?.slice(0, 5) || 'N/A';

        contenedorParticion.innerHTML = `
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title mb-0" style="font-size: 1.5rem; font-weight: 700;">
                                        <i class="fas fa-file-invoice mr-2"></i>
                                        ORDEN DE ENVÍO
                                    </h3>
                                    <small class="text-muted">ID: #${asignacion.id_asignacion}</small>
                                </div>
                                <div class="text-right">
                                    <span class="badge ${getBadgeClass(asignacion.estado)} badge-lg" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                        ${asignacion.estado}
                                    </span>
                                    <div class="text-muted mt-1"><small>${fechaRecogida}</small></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Información de Personas -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-user"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Cliente</span>
                                            <span class="info-box-number">${asignacion.cliente?.nombre || ''} ${asignacion.cliente?.apellido || ''}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-success"><i class="fas fa-user-tie"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Transportista</span>
                                            <span class="info-box-number">${asignacion.transportista?.nombre || ''} ${asignacion.transportista?.apellido || ''}</span>
                                            <small>CI: ${asignacion.transportista?.ci || 'N/A'} | Tel: ${asignacion.transportista?.telefono || 'N/A'}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ruta -->
                            <div class="card card-outline card-info mb-4">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-route mr-2"></i>Detalles de Ruta</h3>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="time-label">
                                            <span class="bg-success">Recogida</span>
                                        </div>
                                        <div>
                                            <i class="fas fa-map-marker-alt bg-success"></i>
                                            <div class="timeline-item">
                                                <h3 class="timeline-header"><strong>${asignacion.origen}</strong></h3>
                                                <div class="timeline-body">
                                                    <p class="mb-1"><i class="far fa-clock mr-1"></i> ${horaRecogida}</p>
                                                    ${asignacion.recogida_entrega?.instrucciones_recogida ? `<div class="alert alert-info mb-0"><small><strong>Instrucciones:</strong> ${asignacion.recogida_entrega.instrucciones_recogida}</small></div>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="time-label">
                                            <span class="bg-danger">Entrega</span>
                                        </div>
                                        <div>
                                            <i class="fas fa-map-marker-alt bg-danger"></i>
                                            <div class="timeline-item">
                                                <h3 class="timeline-header"><strong>${asignacion.destino}</strong></h3>
                                                <div class="timeline-body">
                                                    <p class="mb-1"><i class="far fa-clock mr-1"></i> ${horaEntrega}</p>
                                                    ${asignacion.recogida_entrega?.instrucciones_entrega ? `<div class="alert alert-info mb-0"><small><strong>Instrucciones:</strong> ${asignacion.recogida_entrega.instrucciones_entrega}</small></div>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <i class="fas fa-clock bg-gray"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Vehículo y Transporte -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="small-box bg-light">
                                        <div class="inner">
                                            <h4>${asignacion.vehiculo?.tipo || 'N/A'}</h4>
                                            <p>Placa: <strong>${asignacion.vehiculo?.placa || 'N/A'}</strong></p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small-box bg-light">
                                        <div class="inner">
                                            <h4>${asignacion.tipo_transporte || 'N/A'}</h4>
                                            <p>Tipo de Transporte</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-dolly"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Carga -->
                            <div class="card card-outline card-warning mb-4">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Detalles de Carga</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Variedad</th>
                                                <th>Empaque</th>
                                                <th class="text-right">Cantidad</th>
                                                <th class="text-right">Peso (kg)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${asignacion.cargas?.map(c => `
                                                <tr>
                                                    <td><strong>${c.catalogo?.tipo || ''}</strong></td>
                                                    <td>${c.catalogo?.variedad || ''}</td>
                                                    <td><span class="badge badge-secondary">${c.catalogo?.empaque || ''}</span></td>
                                                    <td class="text-right">${c.cantidad}</td>
                                                    <td class="text-right"><strong>${c.peso}</strong></td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Botón Firmar -->
                            <div id="zona-firma-wrapper">
                                <button id="btnMostrarFirma" class="btn btn-success btn-lg btn-block">
                                    <i class="fas fa-pen-fancy mr-2"></i>
                                    Firmar de Conformidad
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        setupFirmaEvents();
    }

    function setupFirmaEvents() {
        const btnMostrarFirma = document.getElementById("btnMostrarFirma");
        const zonaFirmaWrapper = document.getElementById("zona-firma-wrapper");

        btnMostrarFirma.addEventListener("click", () => {
            zonaFirmaWrapper.innerHTML = `
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-signature mr-2"></i>Captura de Firma</h3>
                        <div class="card-tools">
                            <button type="button" id="btnCancelarFirma" class="btn btn-tool">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Por favor firme en el recuadro de abajo</p>
                        <canvas id="canvas" class="signature-canvas" width="700" height="200" style="width: 100%; background: white;"></canvas>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <button id="limpiarBtn" class="btn btn-default btn-block">
                                    <i class="fas fa-eraser mr-2"></i>Limpiar
                                </button>
                            </div>
                            <div class="col-6">
                                <button id="guardarBtn" class="btn btn-success btn-block">
                                    <i class="fas fa-save mr-2"></i>Guardar Firma
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const canvas = document.getElementById("canvas");
            const ctx = canvas.getContext("2d");
            let dibujando = false;

            const btnCancelarFirma = document.getElementById("btnCancelarFirma");
            btnCancelarFirma.addEventListener("click", () => {
                zonaFirmaWrapper.innerHTML = `
                    <button id="btnMostrarFirma" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-pen-fancy mr-2"></i>Firmar de Conformidad
                    </button>
                `;
                setupFirmaEvents();
            });

            ctx.lineWidth = 3;
            ctx.lineCap = "round";
            ctx.lineJoin = "round";
            ctx.strokeStyle = "#000000";

            function limpiarCanvas() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }

            function obtenerCoordenadas(event) {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;
                
                let x, y;
                if (event.touches && event.touches.length > 0) {
                    const touch = event.touches[0];
                    x = (touch.clientX - rect.left) * scaleX;
                    y = (touch.clientY - rect.top) * scaleY;
                } else {
                    x = (event.clientX - rect.left) * scaleX;
                    y = (event.clientY - rect.top) * scaleY;
                }
                return { x, y };
            }

            const startDraw = (e) => {
                dibujando = true;
                const { x, y } = obtenerCoordenadas(e);
                ctx.beginPath();
                ctx.moveTo(x, y);
                if(e.type === 'touchstart') e.preventDefault();
            };

            const moveDraw = (e) => {
                if (!dibujando) return;
                const { x, y } = obtenerCoordenadas(e);
                ctx.lineTo(x, y);
                ctx.stroke();
                if(e.type === 'touchmove') e.preventDefault();
            };

            const endDraw = () => { dibujando = false; };

            canvas.addEventListener("mousedown", startDraw);
            canvas.addEventListener("mousemove", moveDraw);
            canvas.addEventListener("mouseup", endDraw);
            canvas.addEventListener("mouseleave", endDraw);
            canvas.addEventListener("touchstart", startDraw, { passive: false });
            canvas.addEventListener("touchmove", moveDraw, { passive: false });
            canvas.addEventListener("touchend", endDraw);

            document.getElementById("limpiarBtn").addEventListener("click", limpiarCanvas);

            document.getElementById("guardarBtn").addEventListener("click", async () => {
                if (!idAsignacionActual) return;

                const btnGuardar = document.getElementById("guardarBtn");
                const originalText = btnGuardar.innerHTML;
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';

                const dataURL = canvas.toDataURL("image/png");

                try {
                    const response = await fetch(`/api/firmas/envio/${idAsignacionActual}`, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ imagenFirma: dataURL })
                    });

                    const resultado = await response.json();
                    if (response.ok) {
                        zonaFirmaWrapper.innerHTML = `
                            <div class="alert alert-success">
                                <h5><i class="icon fas fa-check"></i> ¡Firma guardada correctamente!</h5>
                                El documento ha sido firmado exitosamente.
                            </div>
                        `;
                    } else {
                        alert(`Error: ${resultado.error || "Inténtalo de nuevo."}`);
                        btnGuardar.disabled = false;
                        btnGuardar.innerHTML = originalText;
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("Error al conectar con el servidor.");
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = originalText;
                }
            });
        });
    }
});
</script>
</body>
</html>
