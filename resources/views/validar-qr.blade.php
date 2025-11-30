@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto w-full px-4 sm:px-0">
        
        <!-- Sección de Ingreso de Código -->
        <div id="seccion-codigo" class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-md mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-extrabold text-gray-900">Validación de Envío</h2>
                            <p class="text-gray-500 mt-2">Ingrese el código de seguridad para acceder al documento.</p>
                        </div>
                        <div class="relative">
                            <input autocomplete="off" id="inputCodigo" name="codigo" type="text" class="peer placeholder-transparent h-10 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-blue-600 text-center text-2xl tracking-widest" placeholder="Código" />
                            <label for="codigo" class="absolute left-0 -top-3.5 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-440 peer-placeholder-shown:top-2 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm">Código de Acceso</label>
                        </div>
                        <div class="pt-6 text-base leading-6 font-bold sm:text-lg sm:leading-7">
                            <button id="btnValidarCodigo" class="w-full bg-blue-600 text-white rounded-md px-4 py-2 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:-translate-y-1">
                                Validar Documento
                            </button>
                        </div>
                        <p id="mensajeError" class="text-red-500 text-sm mt-2 text-center hidden"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor del Documento (Oculto inicialmente) -->
        <div id="contenedor-particion" class="hidden relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-10 print:shadow-none print:p-0">
            <!-- Content injected by JS -->
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", async () => {
    const seccionCodigo = document.getElementById("seccion-codigo");
    const inputCodigo = document.getElementById("inputCodigo");
    const btnValidarCodigo = document.getElementById("btnValidarCodigo");
    const mensajeError = document.getElementById("mensajeError");
    const contenedorParticion = document.getElementById("contenedor-particion");
    
    // Variables globales para el estado
    let idAsignacionActual = null;
    const token = localStorage.getItem("token");

    // Función para validar código
    async function validarCodigo() {
        const codigo = inputCodigo.value.trim();
        if (!codigo) {
            mostrarError("Por favor ingrese un código.");
            return;
        }

        mostrarError("", false);
        btnValidarCodigo.disabled = true;
        btnValidarCodigo.textContent = "Validando...";

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
                // Éxito
                idAsignacionActual = data.asignacion.id_asignacion;
                renderizarDocumento(data.asignacion);
                seccionCodigo.classList.add("hidden");
                contenedorParticion.classList.remove("hidden");
            } else {
                mostrarError(data.error || "Código inválido. Intente nuevamente.");
            }
        } catch (error) {
            console.error("Error:", error);
            mostrarError("Error de conexión. Verifique su internet.");
        } finally {
            btnValidarCodigo.disabled = false;
            btnValidarCodigo.textContent = "Validar Documento";
        }
    }

    function mostrarError(msg, show = true) {
        mensajeError.textContent = msg;
        if (show) mensajeError.classList.remove("hidden");
        else mensajeError.classList.add("hidden");
    }

    // Event Listeners para el código
    btnValidarCodigo.addEventListener("click", validarCodigo);
    inputCodigo.addEventListener("keypress", (e) => {
        if (e.key === "Enter") validarCodigo();
    });

    function getStatusColor(status) {
        switch(status?.toLowerCase()) {
            case 'entregado': return 'bg-green-100 text-green-800';
            case 'en curso': return 'bg-blue-100 text-blue-800';
            case 'pendiente': return 'bg-yellow-100 text-yellow-800';
            case 'cancelado': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    // Función para renderizar el documento
    function renderizarDocumento(asignacion) {
        const fechaRecogida = asignacion.recogida_entrega?.fecha_recogida 
            ? new Date(asignacion.recogida_entrega.fecha_recogida).toLocaleDateString("es-ES", { day: "2-digit", month: "2-digit", year: "numeric" })
            : new Date().toLocaleDateString("es-ES");

        const horaRecogida = asignacion.recogida_entrega?.hora_recogida 
            ? (asignacion.recogida_entrega.hora_recogida.length > 5 ? asignacion.recogida_entrega.hora_recogida.slice(0, 5) : asignacion.recogida_entrega.hora_recogida)
            : 'Sin rellenar';
            
        const horaEntrega = asignacion.recogida_entrega?.hora_entrega
            ? (asignacion.recogida_entrega.hora_entrega.length > 5 ? asignacion.recogida_entrega.hora_entrega.slice(0, 5) : asignacion.recogida_entrega.hora_entrega)
            : 'Sin rellenar';

        contenedorParticion.innerHTML = `
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex justify-between items-start border-b pb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">ORDEN DE ENVÍO</h1>
                        <p class="text-sm text-gray-500">#${asignacion.id_asignacion}</p>
                    </div>
                    <div class="text-right">
                        <div class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${getStatusColor(asignacion.estado)}">
                            ${asignacion.estado}
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${fechaRecogida}</p>
                    </div>
                </div>

                <!-- Personas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cliente</h3>
                        <p class="font-medium text-gray-900">${asignacion.cliente?.nombre || ''} ${asignacion.cliente?.apellido || ''}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Transportista</h3>
                        <p class="font-medium text-gray-900">${asignacion.transportista?.nombre || ''} ${asignacion.transportista?.apellido || ''}</p>
                        <p class="text-sm text-gray-600">CI: ${asignacion.transportista?.ci || 'N/A'}</p>
                        <p class="text-sm text-gray-600">Tel: ${asignacion.transportista?.telefono || 'N/A'}</p>
                    </div>
                </div>

                <!-- Ruta -->
                <div class="border-t border-b border-gray-200 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Origen</h3>
                            <p class="text-gray-900">${asignacion.origen}</p>
                            <p class="text-sm text-gray-500 mt-1">Recogida: ${horaRecogida}</p>
                            ${asignacion.recogida_entrega?.instrucciones_recogida ? `<p class="text-xs text-gray-500 mt-1 italic">"${asignacion.recogida_entrega.instrucciones_recogida}"</p>` : ''}
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Destino</h3>
                            <p class="text-gray-900">${asignacion.destino}</p>
                            <p class="text-sm text-gray-500 mt-1">Entrega: ${horaEntrega}</p>
                            ${asignacion.recogida_entrega?.instrucciones_entrega ? `<p class="text-xs text-gray-500 mt-1 italic">"${asignacion.recogida_entrega.instrucciones_entrega}"</p>` : ''}
                        </div>
                    </div>
                </div>

                <!-- Vehiculo y Transporte -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Vehículo:</span>
                        <span class="font-medium ml-2">${asignacion.vehiculo?.tipo || 'N/A'} - ${asignacion.vehiculo?.placa || 'N/A'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Tipo Transporte:</span>
                        <span class="font-medium ml-2">${asignacion.tipo_transporte || 'N/A'}</span>
                    </div>
                </div>

                <!-- Carga -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Detalles de Carga</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalle</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cant.</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Peso</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${asignacion.cargas?.map(c => `
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${c.catalogo?.tipo || ''}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            ${c.catalogo?.variedad || ''} 
                                            <span class="text-xs text-gray-400">(${c.catalogo?.empaque || ''})</span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">${c.cantidad}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">${c.peso} kg</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botón Firmar -->
                <div id="zona-firma-wrapper" class="mt-8 pt-6 border-t border-gray-200">
                    <button id="btnMostrarFirma" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Firmar de Conformidad
                    </button>

                    <!-- Área de Firma (Inline, oculta inicialmente) -->
                    <div id="areaFirma" class="hidden mt-4 bg-gray-50 p-4 rounded-xl border-2 border-dashed border-gray-300">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-semibold text-gray-700">Su Firma:</h3>
                            <button id="btnCancelarFirma" class="text-sm text-red-500 hover:text-red-700">Cancelar</button>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <canvas id="canvas" class="w-full h-48 touch-none cursor-crosshair"></canvas>
                        </div>
                        <div class="flex gap-3 mt-4">
                            <button id="limpiarBtn" class="flex-1 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Limpiar</button>
                            <button id="guardarBtn" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-sm transition">Guardar Firma</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Configurar eventos de firma
        setupFirmaEvents();
    }

    function setupFirmaEvents() {
        const btnMostrarFirma = document.getElementById("btnMostrarFirma");
        const areaFirma = document.getElementById("areaFirma");
        const btnCancelarFirma = document.getElementById("btnCancelarFirma");
        const canvas = document.getElementById("canvas");
        const ctx = canvas.getContext("2d");
        let dibujando = false;

        // Mostrar área de firma
        btnMostrarFirma.addEventListener("click", () => {
            btnMostrarFirma.classList.add("hidden");
            areaFirma.classList.remove("hidden");
            ajustarTamañoCanvas();
        });

        // Cancelar firma
        btnCancelarFirma.addEventListener("click", () => {
            areaFirma.classList.add("hidden");
            btnMostrarFirma.classList.remove("hidden");
            limpiarCanvas();
        });

        function ajustarTamañoCanvas() {
            const rect = canvas.parentElement.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = 192; // h-48 = 12rem = 192px
            
            ctx.lineWidth = 3;
            ctx.lineCap = "round";
            ctx.lineJoin = "round";
            ctx.strokeStyle = "#000000";
            limpiarCanvas();
        }

        function limpiarCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function obtenerCoordenadas(event) {
            const rect = canvas.getBoundingClientRect();
            let x, y;

            if (event.touches && event.touches.length > 0) {
                const touch = event.touches[0];
                x = (touch.clientX - rect.left);
                y = (touch.clientY - rect.top);
            } else {
                x = (event.clientX - rect.left);
                y = (event.clientY - rect.top);
            }
            return { x, y };
        }

        // Eventos de dibujo
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

        // Botones de acción
        document.getElementById("limpiarBtn").addEventListener("click", limpiarCanvas);

        document.getElementById("guardarBtn").addEventListener("click", async () => {
            if (!idAsignacionActual) return;

            const btnGuardar = document.getElementById("guardarBtn");
            const originalText = btnGuardar.textContent;
            btnGuardar.disabled = true;
            btnGuardar.textContent = "Guardando...";

            const dataURL = canvas.toDataURL("image/png");

            try {
                const response = await fetch(`/api/firmas/envio/${idAsignacionActual}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ imagenFirma: dataURL })
                });

                const resultado = await response.json();
                if (response.ok) {
                    alert("✅ Firma guardada correctamente.");
                    areaFirma.innerHTML = `
                        <div class="text-center py-8 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-lg font-semibold">Documento firmado exitosamente</p>
                        </div>
                    `;
                } else {
                    alert(`Error al guardar firma: ${resultado.error || "Inténtalo de nuevo."}`);
                    btnGuardar.disabled = false;
                    btnGuardar.textContent = originalText;
                }
            } catch (error) {
                console.error("Error al guardar firma:", error);
                alert("Error al conectar con el servidor.");
                btnGuardar.disabled = false;
                btnGuardar.textContent = originalText;
            }
        });
    }
});
</script>
@endsection
