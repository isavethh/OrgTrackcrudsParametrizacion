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
            background: white;
        }
        
        /* Modal responsive fixes */
        .modal-backdrop {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal {
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .modal-dialog {
            margin: 1rem;
            max-width: 90%;
        }
        
        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 500px;
                margin: 1.75rem auto;
            }
        }
        
        @media (min-width: 992px) {
            .modal-lg {
                max-width: 700px;
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
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
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            .modal-content {
                border-radius: 0.5rem;
            }
            .signature-canvas {
                width: 100% !important;
                height: auto !important;
            }
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
            ? new Date(asignacion.recogida_entrega.fecha_recogida).toLocaleDateString("es-ES", { day: '2-digit', month: '2-digit', year: 'numeric' })
            : new Date().toLocaleDateString("es-ES", { day: '2-digit', month: '2-digit', year: 'numeric' });

        const horaRecogida = asignacion.recogida_entrega?.hora_recogida 
            ? asignacion.recogida_entrega.hora_recogida.slice(0, 5) + ' a. m.'
            : 'Sin rellenar';
        const horaEntrega = asignacion.recogida_entrega?.hora_entrega 
            ? asignacion.recogida_entrega.hora_entrega.slice(0, 5) + ' a. m.'
            : 'Sin rellenar';

        const instruccionesRecogida = asignacion.recogida_entrega?.instrucciones_recogida || 'Sin rellenar';
        const instruccionesEntrega = asignacion.recogida_entrega?.instrucciones_entrega || 'Sin rellenar';

        contenedorParticion.innerHTML = `
            <div class="row justify-content-center py-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-3 p-md-4">
                            <!-- Encabezado -->
                            <div class="text-center mb-4">
                                <h1 class="mb-2" style="font-size: 2rem; font-weight: 700;">Ortrack</h1>
                                <p class="text-muted mb-0">"Documento de Envío"</p>
                            </div>

                            <!-- Tabla Única Consolidada -->
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <!-- Cliente y Fecha -->
                                    <tr>
                                        <td class="font-weight-bold bg-light" style="width: 50%;">Nombre de cliente</td>
                                        <td class="font-weight-bold bg-light" style="width: 50%;">Fecha</td>
                                    </tr>
                                    <tr>
                                        <td>${asignacion.cliente?.nombre || ''} ${asignacion.cliente?.apellido || ''}</td>
                                        <td>${fechaRecogida}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">Punto de recogida</td>
                                        <td class="font-weight-bold bg-light">Punto de Entrega</td>
                                    </tr>
                                    <tr>
                                        <td>${asignacion.origen}</td>
                                        <td>${asignacion.destino}</td>
                                    </tr>
                                    
                                    <!-- Detalles de Bloque de Envío -->
                                    <tr>
                                        <th colspan="2" class="text-center font-weight-bold bg-light">Detalles de Bloque de Envío</th>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="font-weight-bold">Día</td>
                                        <td class="font-weight-bold">Hora de Recogida / Hora de Entrega</td>
                                    </tr>
                                    <tr>
                                        <td>${fechaRecogida}</td>
                                        <td>${horaRecogida} / ${horaEntrega}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">Instrucciones en punto de recogida</td>
                                        <td class="font-weight-bold bg-light">Instrucciones en punto de entrega</td>
                                    </tr>
                                    <tr>
                                        <td>${instruccionesRecogida}</td>
                                        <td>${instruccionesEntrega}</td>
                                    </tr>
                                    
                                    <!-- Transportista -->
                                    <tr>
                                        <th colspan="2" class="text-center font-weight-bold bg-light">Transportista</th>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="font-weight-bold">Nombre y Apellido</td>
                                        <td class="font-weight-bold">Teléfono / CI</td>
                                    </tr>
                                    <tr>
                                        <td>${asignacion.transportista?.nombre || ''} ${asignacion.transportista?.apellido || ''}</td>
                                        <td>${asignacion.transportista?.telefono || 'N/A'} / ${asignacion.transportista?.ci || 'N/A'}</td>
                                    </tr>
                                    
                                    <!-- Vehículo -->
                                    <tr>
                                        <th colspan="2" class="text-center font-weight-bold bg-light">Vehículo</th>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="font-weight-bold">Tipo</td>
                                        <td class="font-weight-bold">Placa</td>
                                    </tr>
                                    <tr>
                                        <td>${asignacion.vehiculo?.tipo || 'N/A'}</td>
                                        <td>${asignacion.vehiculo?.placa || 'N/A'}</td>
                                    </tr>
                                    
                                    <!-- Transporte -->
                                    <tr>
                                        <th colspan="2" class="text-center font-weight-bold bg-light">Transporte</th>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="font-weight-bold">Tipo</td>
                                        <td class="font-weight-bold">Descripción</td>
                                    </tr>
                                    <tr>
                                        <td>${asignacion.tipo_transporte || 'N/A'}</td>
                                        <td>${asignacion.descripcion_transporte || 'Sin descripción'}</td>
                                    </tr>
                                    
                                    <!-- Detalles de Cargamento -->
                                    <tr>
                                        <th colspan="2" class="text-center font-weight-bold bg-light">Detalles de Cargamento</th>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Tabla de Cargas con scroll horizontal en móviles -->
                            <div class="table-responsive">
                                <table class="table table-bordered mb-4">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="min-width: 80px;">Tipo</th>
                                            <th style="min-width: 100px;">Variedad</th>
                                            <th style="min-width: 100px;">Empaquetado</th>
                                            <th style="min-width: 70px;">Cantidad</th>
                                            <th style="min-width: 80px;">Peso Kg</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${asignacion.cargas?.map(c => `
                                            <tr>
                                                <td>${c.catalogo?.tipo || ''}</td>
                                                <td>${c.catalogo?.variedad || ''}</td>
                                                <td>${c.catalogo?.empaque || ''}</td>
                                                <td>${c.cantidad}</td>
                                                <td>${c.peso} kg</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>

                            <!-- Botón Firmar -->
                            <div class="text-center">
                                <button id="btnMostrarFirma" class="btn btn-primary btn-lg">
                                    <i class="fas fa-signature mr-2"></i>
                                    Firmar Documento
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Firma -->
            <div class="modal fade" id="modalFirma" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-signature mr-2"></i>Firma del Cliente</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-2 p-md-3">
                            <p class="text-muted mb-3 text-center">Por favor firme en el recuadro</p>
                            <div class="canvas-container" style="width: 100%; overflow: hidden;">
                                <canvas id="canvas" class="signature-canvas" style="width: 100%; height: 200px; display: block; border: 2px solid #ddd; border-radius: 8px;"></canvas>
                            </div>
                        </div>
                        <div class="modal-footer flex-column flex-sm-row">
                            <button type="button" class="btn btn-secondary btn-block btn-sm-auto mb-2 mb-sm-0" id="limpiarBtn">
                                <i class="fas fa-eraser mr-2"></i>Limpiar
                            </button>
                            <button type="button" class="btn btn-success btn-block btn-sm-auto" id="guardarBtn">
                                <i class="fas fa-save mr-2"></i>Guardar Firma
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        setupFirmaEvents();
    }

    function setupFirmaEvents() {
        const btnMostrarFirma = document.getElementById("btnMostrarFirma");
        
        btnMostrarFirma.addEventListener("click", () => {
            $('#modalFirma').modal('show');
            
            setTimeout(() => {
                const canvas = document.getElementById("canvas");
                const container = canvas.parentElement;
                const ctx = canvas.getContext("2d");
                let dibujando = false;

                // Ajustar tamaño del canvas según el contenedor
                function ajustarCanvas() {
                    const containerWidth = container.offsetWidth;
                    canvas.width = containerWidth - 4; // -4 por el borde
                    canvas.height = 200;
                    
                    ctx.lineWidth = 3;
                    ctx.lineCap = "round";
                    ctx.lineJoin = "round";
                    ctx.strokeStyle = "#000000";
                }
                
                ajustarCanvas();
                window.addEventListener('resize', ajustarCanvas);

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

                // Limpiar event listeners al cerrar modal
                $('#modalFirma').on('hidden.bs.modal', function () {
                    window.removeEventListener('resize', ajustarCanvas);
                });

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
                            $('#modalFirma').modal('hide');
                            
                            // Mostrar mensaje de éxito con imagen de firma
                            document.querySelector('.card-body').insertAdjacentHTML('beforeend', `
                                <div class="alert alert-success alert-dismissible fade show mt-3">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <h5><i class="icon fas fa-check"></i> ¡Firma guardada correctamente!</h5>
                                    <p class="mb-3">El documento ha sido firmado exitosamente.</p>
                                    <div class="text-center p-3" style="border-top: 2px solid #000; max-width: 400px; margin: 0 auto;">
                                        <img src="${dataURL}" alt="Firma" style="max-width: 100%; height: auto;">
                                        <p class="mt-2 mb-0 font-weight-bold">Firma del Cliente</p>
                                    </div>
                                </div>
                            `);
                            
                            // Ocultar botón de firmar
                            btnMostrarFirma.style.display = 'none';
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
            }, 300);
        });
    }
});
</script>
</body>
</html>
