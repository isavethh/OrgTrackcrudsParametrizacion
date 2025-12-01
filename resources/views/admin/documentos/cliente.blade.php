@extends('layouts.adminlte')

@section('page-title', 'Documentos de Envíos')

@section('page-content')
<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
    .document-card { background: white; padding: 40px; max-width: 900px; margin: 0 auto; }
    .document-header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; }
    .document-header h2 { font-size: 32px; margin-bottom: 5px; color: #333; }
    .document-header p { font-size: 14px; color: #666; font-style: italic; }
    .document-table { width: 100%; border-collapse: collapse; margin: 0; font-size: 13px; }
    .document-table th, .document-table td { border: 1px solid #000; padding: 10px; }
    .document-table th { background-color: #f8f9fa; font-weight: bold; text-align: left; }
    .document-table td { text-align: left; }
    .table-section { margin: 0; }
    .section-title { font-weight: bold; font-size: 14px; margin: 0; text-align: center; background-color: #f8f9fa; padding: 8px; border: 1px solid #000; border-top: 0; }
    .checklist-table { width: 100%; border-collapse: collapse; margin: 0; font-size: 12px; }
    .checklist-table th, .checklist-table td { border: 1px solid #000; padding: 8px; }
    .checklist-table th { background-color: #f8f9fa; text-align: center; font-weight: bold; }
    .signature-container { display: flex; justify-content: space-around; margin-top: 40px; margin-bottom: 20px; }
    .signature-item { text-align: center; flex: 1; }
    .signature-item img { max-width: 180px; max-height: 80px; border: none; }
    .signature-line { border-top: 2px solid #000; margin: 10px 30px 5px 30px; padding-top: 5px; font-weight: bold; font-size: 13px; }
    .envio-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); cursor: pointer; }
</style>

<div class="row">
    <div class="col-12">
        <!-- Información del cliente -->
        <div class="card card-primary card-outline mb-3 no-print">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" id="clienteNombre">
                            <i class="fas fa-user mr-2"></i>Cargando información del cliente...
                        </h5>
                        <p class="text-muted mb-0 small" id="clienteCorreo"></p>
                    </div>
                    <a href="{{ route('admin.documentos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Volver a Lista de Clientes
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabs de navegación -->
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-historial" data-toggle="pill" href="#historial" role="tab">
                            <i class="fas fa-clipboard-list"></i> Historial de Envíos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" id="tab-particiones" data-toggle="pill" href="#particiones" role="tab">
                            <i class="fas fa-th-list"></i> Particiones del Envío
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" id="tab-documento" data-toggle="pill" href="#documento" role="tab">
                            <i class="fas fa-file-pdf"></i> Documento Completo
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-content">
                    <!-- TAB 1: Historial de Envíos -->
                    <div class="tab-pane fade show active" id="historial" role="tabpanel">
                        <div class="mb-3">
                            <div class="input-group" style="max-width: 350px;">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por ID de envío...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>

                        <div id="loadingSpinner" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando envíos entregados...</p>
                        </div>
                        
                        <div id="enviosContainer" style="display: none;"></div>
                        
                        <div id="noEnvios" style="display: none;" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Este cliente no tiene envíos entregados disponibles.</p>
                        </div>
                    </div>

                    <!-- TAB 2: Particiones -->
                    <div class="tab-pane fade" id="particiones" role="tabpanel">
                        <div class="mb-3">
                            <button class="btn btn-secondary" onclick="volverAHistorial()">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                        </div>
                        <h4 class="mb-3">Lista de Particiones</h4>
                        <div id="particionesContainer"></div>
                    </div>

                    <!-- TAB 3: Documento Completo -->
                    <div class="tab-pane fade" id="documento" role="tabpanel">
                        <div class="mb-3 no-print">
                            <button class="btn btn-secondary mr-2" onclick="volverAHistorial()">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                        <div id="printableArea">
                            <div id="documentoCompletoContainer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
const idCliente = {{ $id_cliente }};
const token = localStorage.getItem('authToken')?.replace(/^"+|"+$/g, '') || null;
if (!token) { window.location.href = '/login'; }

const headers = { 
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
};

let enviosData = [];
let envioSeleccionado = null;
let particionSeleccionada = null;

// Elementos del DOM
const searchInput = document.getElementById('searchInput');
const loadingSpinner = document.getElementById('loadingSpinner');
const enviosContainer = document.getElementById('enviosContainer');
const noEnvios = document.getElementById('noEnvios');
const particionesContainer = document.getElementById('particionesContainer');
const documentoCompletoContainer = document.getElementById('documentoCompletoContainer');

const tabHistorial = document.getElementById('tab-historial');
const tabParticiones = document.getElementById('tab-particiones');
const tabDocumento = document.getElementById('tab-documento');

// Cargar información del cliente
async function cargarInfoCliente() {
    try {
        const res = await fetch(`${window.location.origin}/api/usuarios/${idCliente}`, { headers });
        if (!res.ok) throw new Error('No se pudo cargar la información del cliente');
        
        const cliente = await res.json();
        document.getElementById('clienteNombre').innerHTML = `
            <i class="fas fa-user mr-2"></i>${cliente.nombre || ''} ${cliente.apellido || ''}
        `;
        document.getElementById('clienteCorreo').textContent = cliente.correo || '';
    } catch (error) {
        console.error('Error al cargar cliente:', error);
    }
}

// Cargar envíos del cliente
async function cargarEnvios() {
    try {
        const res = await fetch(`${window.location.origin}/api/envios/usuario/${idCliente}`, { headers });
        
        if (res.status === 401) {
            localStorage.removeItem('authToken');
            window.location.href = '/login';
            return;
        }

        if (!res.ok) throw new Error('No se pudieron cargar los envíos');

        const data = await res.json();
        enviosData = data.filter(e => e.estado_nombre === 'Entregado');

        renderEnvios(enviosData);
    } catch (error) {
        console.error('Error:', error);
        loadingSpinner.style.display = 'none';
        noEnvios.style.display = 'block';
    }
}

function renderEnvios(envios) {
    loadingSpinner.style.display = 'none';
    
    if (!envios || envios.length === 0) {
        noEnvios.style.display = 'block';
        enviosContainer.style.display = 'none';
        return;
    }

    enviosContainer.innerHTML = envios.map(envio => `
        <div class="card mb-3 envio-card" onclick="seleccionarEnvio(${envio.id})">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                        <div class="d-flex align-items-start">
                            <div class="mr-3">
                                <span class="badge badge-lg badge-success">ID: ${envio.id}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-2">${envio.nombre_origen || 'Origen'} → ${envio.nombre_destino || 'Destino'}</h5>
                                <p class="text-muted mb-1 small"><i class="fas fa-map-marker-alt mr-1"></i><strong>Origen:</strong> ${envio.nombre_origen || '—'}</p>
                                <p class="text-muted mb-1 small"><i class="fas fa-map-marker-alt mr-1"></i><strong>Destino:</strong> ${envio.nombre_destino || '—'}</p>
                                <div class="mt-2">
                                    <span class="badge badge-success">${envio.estado_nombre || 'Entregado'}</span>
                                    <span class="badge badge-info ml-1">${envio.particiones?.length || 0} partición(es)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-right">
                        <small class="text-muted d-block mb-1">Fecha de registro</small>
                        <strong>${envio.fecha_creacion ? new Date(envio.fecha_creacion).toLocaleDateString('es-BO') : 'Sin fecha'}</strong>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary btn-block" onclick="event.stopPropagation(); seleccionarEnvio(${envio.id})">
                                <i class="fas fa-file-alt mr-1"></i>Ver Documentos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    noEnvios.style.display = 'none';
    enviosContainer.style.display = 'block';
}

function seleccionarEnvio(idEnvio) {
    envioSeleccionado = enviosData.find(e => e.id === idEnvio);
    if (!envioSeleccionado) return;

    renderParticiones();
    
    // Activar tab de particiones
    tabParticiones.classList.remove('disabled');
    $(tabParticiones).tab('show');
}

function renderParticiones() {
    if (!envioSeleccionado || !envioSeleccionado.particiones) return;

    particionesContainer.innerHTML = envioSeleccionado.particiones.map((p, idx) => `
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">Partición ${idx + 1} - Asignación #${p.id_asignacion}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Estado:</strong> <span class="badge badge-${p.estado === 'Completada' ? 'success' : 'warning'}">${p.estado || '—'}</span></p>
                        <p><strong>Tipo de transporte:</strong> ${p.tipoTransporte?.nombre || '—'}</p>
                        <p><strong>Transportista:</strong> ${p.transportista?.nombre || '—'} ${p.transportista?.apellido || ''}</p>
                        <p><strong>Vehículo:</strong> ${p.vehiculo?.placa || '—'} (${p.vehiculo?.tipo || '—'})</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha recogida:</strong> ${p.recogidaEntrega?.fecha_recogida || '—'}</p>
                        <p><strong>Hora recogida:</strong> ${p.recogidaEntrega?.hora_recogida || '—'}</p>
                        <p><strong>Hora entrega:</strong> ${p.recogidaEntrega?.hora_entrega || '—'}</p>
                        <p><strong>Cargas:</strong> ${p.cargas?.length || 0} item(s)</p>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" onclick="verDocumentoCompleto(${p.id_asignacion})">
                        <i class="fas fa-file-pdf mr-1"></i>Ver Documento de Envío
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

async function verDocumentoCompleto(idAsignacion) {
    try {
        const res = await fetch(`${window.location.origin}/api/envios/documentos/asignacion/${idAsignacion}`, { headers });
        
        if (!res.ok) throw new Error('No se pudo cargar el documento');

        const data = await res.json();
        particionSeleccionada = data;
        
        renderDocumentoCompleto(data);
        
        // Activar tab de documento
        tabDocumento.classList.remove('disabled');
        $(tabDocumento).tab('show');
    } catch (error) {
        console.error('Error:', error);
        alert('No se pudo cargar el documento: ' + error.message);
    }
}

function renderDocumentoCompleto(data) {
    const particion = data.particion;
    console.log('Datos del documento:', data);
    console.log('Checklist Condiciones:', particion?.checklistCondiciones);
    console.log('Checklist Incidentes:', particion?.checklistIncidentes);
    
    const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-BO') : '—';
    const formatTime = (t) => t || '—';

    const firmaCliente = particion?.firma || '';
    const firmaTransportista = particion?.firma_transportista || '';

    const mostrarFirmaCliente = firmaCliente.trim().length > 100;
    const mostrarFirmaTransportista = firmaTransportista.trim().length > 100;

    documentoCompletoContainer.innerHTML = `
        <div class="document-card">
            <div class="document-header">
                <h2>DOCUMENTO DE TRANSPORTE DE CARGA</h2>
                <p>Registro de envío y condiciones de transporte</p>
            </div>

            <table class="document-table">
                <tr>
                    <th colspan="2" style="text-align: center; background-color: #f8f9fa;">Información del Envío</th>
                </tr>
                <tr>
                    <th style="width: 50%; text-align: center;">ID de Envío</th>
                    <th style="width: 50%; text-align: center;">ID de Asignación</th>
                </tr>
                <tr>
                    <td style="text-align: center;">${data.id_envio || '—'}</td>
                    <td style="text-align: center;">${particion.id_asignacion || '—'}</td>
                </tr>
                <tr>
                    <th style="text-align: center;">Nombre del Cliente</th>
                    <th style="text-align: center;">Estado del Envío</th>
                </tr>
                <tr>
                    <td style="text-align: center;">${data.nombre_cliente || '—'}</td>
                    <td style="text-align: center;">${data.estado_envio || '—'}</td>
                </tr>
                <tr>
                    <th style="width: 50%; text-align: center;">Punto de recogida</th>
                    <th style="width: 50%; text-align: center;">Punto de Entrega</th>
                </tr>
                <tr>
                    <td style="text-align: center;">${data.nombre_origen || '—'}</td>
                    <td style="text-align: center;">${data.nombre_destino || '—'}</td>
                </tr>
            </table>

            <table class="document-table">
                <tr>
                    <th colspan="3" style="text-align: center; background-color: #f8f9fa;">Detalles de Bloque de Envío</th>
                </tr>
                <tr>
                    <th style="width: 33.33%; text-align: center;">Día</th>
                    <th style="width: 33.33%; text-align: center;">Hora de Recogida</th>
                    <th style="width: 33.33%; text-align: center;">Hora de Entrega</th>
                </tr>
                <tr>
                    <td style="width: 33.33%; text-align: center;">${formatDate(particion.recogidaEntrega?.fecha_recogida)}</td>
                    <td style="width: 33.33%; text-align: center;">${formatTime(particion.recogidaEntrega?.hora_recogida)}</td>
                    <td style="width: 33.33%; text-align: center;">${formatTime(particion.recogidaEntrega?.hora_entrega)}</td>
                </tr>
            </table>

            <table class="document-table">
                <tr>
                    <th style="width: 50%; text-align: center;">Instrucciones en punto de recogida</th>
                    <th style="width: 50%; text-align: center;">Instrucciones en punto de entrega</th>
                </tr>
                <tr>
                    <td style="width: 50%; text-align: center;">${particion.recogidaEntrega?.instrucciones_recogida || 'Sin instrucciones'}</td>
                    <td style="width: 50%; text-align: center;">${particion.recogidaEntrega?.instrucciones_entrega || 'Sin instrucciones'}</td>
                </tr>
            </table>

            <table class="document-table">
                <tr>
                    <th colspan="3" style="text-align: center; background-color: #f8f9fa;">Transportista</th>
                </tr>
                <tr>
                    <th style="width: 40%; text-align: center;">Nombre y Apellido</th>
                    <th style="width: 30%; text-align: center;">Teléfono</th>
                    <th style="width: 30%; text-align: center;">CI</th>
                </tr>
                <tr>
                    <td style="text-align: center;">${particion.transportista?.nombre || '—'} ${particion.transportista?.apellido || ''}</td>
                    <td style="text-align: center;">${particion.transportista?.telefono || '—'}</td>
                    <td style="text-align: center;">${particion.transportista?.ci || '—'}</td>
                </tr>
            </table>

            <table class="document-table">
                <tr>
                    <th colspan="2" style="text-align: center; background-color: #f8f9fa;">Vehículo</th>
                </tr>
                <tr>
                    <th style="width: 50%; text-align: center;">Tipo</th>
                    <th style="width: 50%; text-align: center;">Placa</th>
                </tr>
                <tr>
                    <td style="text-align: center;">${particion.vehiculo?.tipo || '—'}</td>
                    <td style="text-align: center;">${particion.vehiculo?.placa || '—'}</td>
                </tr>
            </table>

            <table class="document-table">
                <tr>
                    <th colspan="2" style="text-align: center; background-color: #f8f9fa;">Transporte</th>
                </tr>
                <tr>
                    <th style="width: 50%; text-align: center;">Tipo</th>
                    <th style="width: 50%; text-align: center;">Descripción</th>
                </tr>
                <tr>
                    <td style="text-align: center;">${particion.tipo_transporte?.nombre || '—'}</td>
                    <td style="text-align: center;">${particion.tipo_transporte?.descripcion || '—'}</td>
                </tr>
            </table>

            <table class="document-table">
                <tr>
                    <th colspan="5" style="text-align: center; background-color: #f8f9fa;">Detalles de cargamento</th>
                </tr>
                <tr>
                    <th style="width: 20%; text-align: center;">Tipo</th>
                    <th style="width: 20%; text-align: center;">Variedad</th>
                    <th style="width: 20%; text-align: center;">Empaquetado</th>
                    <th style="width: 20%; text-align: center;">Cantidad</th>
                    <th style="width: 20%; text-align: center;">Peso Kg</th>
                </tr>
                    ${particion.cargas?.map(c => `
                        <tr>
                            <td style="text-align: center;">${c.tipo || '—'}</td>
                            <td style="text-align: center;">${c.variedad || '—'}</td>
                            <td style="text-align: center;">${c.empaquetado || '—'}</td>
                            <td style="text-align: center;">${c.cantidad || 0}</td>
                            <td style="text-align: center;">${c.peso || 0}</td>
                        </tr>
                    `).join('') || '<tr><td colspan="5" class="text-center">Sin cargas registradas</td></tr>'}
            </table>

            ${particion.checklistCondiciones && particion.checklistCondiciones.length > 0 ? `
                <div style="margin-top: 15px;"></div>
                <table class="document-table">
                    <tr>
                        <th colspan="3" style="text-align: center; background-color: #f8f9fa;">Registro de condiciones de transporte</th>
                    </tr>
                    <tr>
                        <th style="width: 60%;">Condiciones de Transporte</th>
                        <th style="width: 20%; text-align: center;">Sí</th>
                        <th style="width: 20%; text-align: center;">No</th>
                    </tr>
                    ${particion.checklistCondiciones.map((c, index) => `
                        <tr>
                            <td>${index + 1}. ${c.condicion?.titulo || '—'}</td>
                            <td style="text-align: center;">${c.valor ? 'Sí' : ''}</td>
                            <td style="text-align: center;">${!c.valor ? 'No' : ''}</td>
                        </tr>
                    `).join('')}
                    ${particion.observaciones_condiciones ? `
                        <tr>
                            <td colspan="3"><strong>Observación:</strong> ${particion.observaciones_condiciones}</td>
                        </tr>
                    ` : ''}
                </table>
            ` : ''}

            ${particion.checklistIncidentes && particion.checklistIncidentes.length > 0 ? `
                <div style="margin-top: 15px;"></div>
                <table class="document-table">
                    <tr>
                        <th colspan="3" style="text-align: center; background-color: #f8f9fa;">Registro de Incidentes de transporte</th>
                    </tr>
                    <tr>
                        <th style="width: 60%;">Incidentes de transporte</th>
                        <th style="width: 20%; text-align: center;">Sí</th>
                        <th style="width: 20%; text-align: center;">No</th>
                    </tr>
                    ${particion.checklistIncidentes.map((inc, index) => `
                        <tr>
                            <td>${(index + 1)}. ${inc.tipo_incidente?.titulo || 'Incidente'}</td>
                            <td style="text-align: center;">${inc.ocurrio ? 'Sí' : ''}</td>
                            <td style="text-align: center;">${!inc.ocurrio ? 'No' : ''}</td>
                        </tr>
                    `).join('')}
                    ${particion.observaciones_incidentes ? `
                        <tr>
                            <td colspan="3"><strong>Observación:</strong> ${particion.observaciones_incidentes}</td>
                        </tr>
                    ` : ''}
                </table>
            ` : ''}

            <div class="signature-container">
                <div class="signature-item">
                    ${mostrarFirmaCliente ? `
                        <img src="${firmaCliente}" alt="Firma Cliente" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display:none; color: #999;">Firma no disponible</div>
                    ` : '<div style="color: #999;">Firma no disponible</div>'}
                    <div class="signature-line">Firma del Cliente</div>
                </div>
                <div class="signature-item">
                    ${mostrarFirmaTransportista ? `
                        <img src="${firmaTransportista}" alt="Firma Transportista"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display:none; color: #999;">Firma no disponible</div>
                    ` : '<div style="color: #999;">Firma no disponible</div>'}
                    <div class="signature-line">Firma del Transportista</div>
                </div>
            </div>
        </div>
    `;
}

function volverAHistorial() {
    envioSeleccionado = null;
    particionSeleccionada = null;
    
    tabParticiones.classList.add('disabled');
    tabDocumento.classList.add('disabled');
    $(tabHistorial).tab('show');
}

// Búsqueda
searchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    if (!query) {
        renderEnvios(enviosData);
        return;
    }
    
    const filtrados = enviosData.filter(e => 
        String(e.id).includes(query) ||
        (e.nombre_origen || '').toLowerCase().includes(query) ||
        (e.nombre_destino || '').toLowerCase().includes(query)
    );
    
    renderEnvios(filtrados);
});

// Inicializar
cargarInfoCliente();
cargarEnvios();
</script>
@endpush