@extends('layouts.adminlte')

@section('page-title', 'Documentos de Envío - Productor')

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
        <!-- Información del productor -->
        <div class="card card-primary card-outline mb-3 no-print">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" id="productorNombre">
                            <i class="fas fa-seedling mr-2"></i><span id="nombreProductor">Cargando información del envío...</span>
                        </h5>
                        <p class="text-muted mb-0 small">
                            <i class="fas fa-envelope mr-1"></i><span id="emailProductor"></span> | 
                            <i class="fas fa-phone ml-2 mr-1"></i><span id="telefonoProductor"></span>
                        </p>
                    </div>
                    <a href="{{ route('admin.documentos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Volver a Documentos
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabs de navegación -->
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-particiones" data-toggle="pill" href="#particiones" role="tab">
                            <i class="fas fa-th-list"></i> Particiones del Envío
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" id="tab-documento" data-toggle="pill" href="#documento" role="tab">
                            <i class="fas fa-file-pdf"></i> Documento de Partición
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-content">
                    <!-- TAB 1: Particiones -->
                    <div class="tab-pane fade show active" id="particiones" role="tabpanel">
                        <div id="loadingSpinner" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando particiones del envío...</p>
                        </div>
                        
                        <div id="particionesContainer" style="display: none;"></div>
                        
                        <div id="noParticiones" style="display: none;" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Este envío no tiene particiones disponibles.</p>
                        </div>
                    </div>

                    <!-- TAB 2: Documento Completo -->
                    <div class="tab-pane fade" id="documento" role="tabpanel">
                        <div class="mb-3 no-print">
                            <button class="btn btn-secondary mr-2" onclick="volverAParticiones()">
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
(function() {
    const idEnvio = {{ $id_envio }};

    let envioData = null;
    let particionSeleccionada = null;

// Elementos del DOM
const loadingSpinner = document.getElementById('loadingSpinner');
const particionesContainer = document.getElementById('particionesContainer');
const noParticiones = document.getElementById('noParticiones');
const documentoCompletoContainer = document.getElementById('documentoCompletoContainer');

const tabParticiones = document.getElementById('tab-particiones');
const tabDocumento = document.getElementById('tab-documento');

// Cargar datos del envío
async function cargarEnvio() {
    try {
        const res = await fetch(`${window.location.origin}/api/public/envios/${idEnvio}/documento`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        
        if (!res.ok) throw new Error('No se pudo cargar el envío');

        envioData = await res.json();
        
        // Actualizar información del productor
        document.getElementById('nombreProductor').textContent = envioData.nombre_cliente || 'Productor';
        document.getElementById('emailProductor').textContent = 'Email no disponible';
        document.getElementById('telefonoProductor').textContent = 'Teléfono no disponible';

        renderParticiones(envioData.particiones);
    } catch (error) {
        console.error('Error:', error);
        loadingSpinner.style.display = 'none';
        noParticiones.style.display = 'block';
    }
}

function renderParticiones(particiones) {
    loadingSpinner.style.display = 'none';
    
    if (!particiones || particiones.length === 0) {
        noParticiones.style.display = 'block';
        particionesContainer.style.display = 'none';
        return;
    }

    particionesContainer.innerHTML = particiones.map((p, idx) => {
        const esEntregado = p.estado === 'Entregado';
        const estadoBadge = esEntregado ? 'success' : (p.estado === 'En curso' ? 'info' : 'secondary');
        const estadoTexto = p.estado || 'Pendiente';
        
        return `
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    Partición ${idx + 1} - Asignación #${p.id_asignacion}
                    <span class="badge badge-${estadoBadge} ml-2">${estadoTexto}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tipo de transporte:</strong> ${p.tipo_transporte?.nombre || '—'}</p>
                        <p><strong>Transportista:</strong> ${p.transportista?.nombre || '—'} ${p.transportista?.apellido || ''}</p>
                        <p><strong>Vehículo:</strong> ${p.vehiculo?.placa || '—'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha recogida:</strong> ${p.recogidaEntrega?.fecha_recogida || '—'}</p>
                        <p><strong>Hora recogida:</strong> ${p.recogidaEntrega?.hora_recogida || '—'}</p>
                        <p><strong>Hora entrega:</strong> ${p.recogidaEntrega?.hora_entrega || '—'}</p>
                        <p><strong>Cargas:</strong> ${p.cargas?.length || 0} item(s)</p>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" onclick="verDocumentoParticion(${idx})" ${!esEntregado ? 'disabled title="El documento solo está disponible para particiones completadas"' : ''}>
                        <i class="fas fa-file-pdf mr-1"></i>Ver Documento de Envío
                    </button>
                    ${!esEntregado ? '<small class="text-muted ml-2"><i class="fas fa-info-circle"></i> Documento disponible al completar la entrega</small>' : ''}
                </div>
            </div>
        </div>
    `;
    }).join('');

    noParticiones.style.display = 'none';
    particionesContainer.style.display = 'block';
}

window.verDocumentoParticion = function(idxParticion) {
    const particion = envioData.particiones[idxParticion];
    if (!particion) return;

    particionSeleccionada = particion;
    renderDocumentoCompleto();
    
    // Activar tab de documento
    tabDocumento.classList.remove('disabled');
    $(tabDocumento).tab('show');
}

function renderDocumentoCompleto() {
    if (!envioData || !particionSeleccionada) return;

    const particion = particionSeleccionada;
    
    const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-BO') : '—';
    const formatTime = (t) => t || '—';

    const firmaCliente = particion.firma || '';
    const firmaTransportista = particion.firmaTransportista || '';

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
                    <td style="text-align: center;">${envioData.id_envio || '—'}</td>
                    <td style="text-align: center;">${particion.id_asignacion || '—'}</td>
                </tr>
                <tr>
                    <th style="text-align: center;">Nombre del Cliente</th>
                    <th style="text-align: center;">Estado del Envío</th>
                </tr>
                <tr>
                    <td style="text-align: center;">${envioData.nombre_cliente || '—'}</td>
                    <td style="text-align: center;">${envioData.estado || '—'}</td>
                </tr>
                <tr>
                    <th style="width: 50%; text-align: center;">Punto de recogida</th>
                    <th style="width: 50%; text-align: center;">Punto de Entrega</th>
                </tr>
                <tr>
                    <td style="text-align: center;">${envioData.nombre_origen || '—'}</td>
                    <td style="text-align: center;">${envioData.nombre_destino || '—'}</td>
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
                    <td style="text-align: center;">${particion.tipo_transporte?.nombre || '—'}</td>
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
                            <td style="text-align: center;">${c.cumple ? 'Sí' : ''}</td>
                            <td style="text-align: center;">${!c.cumple ? 'No' : ''}</td>
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
                        <img src="${firmaCliente}" alt="Firma Productor" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display:none; color: #999;">Firma no disponible</div>
                    ` : '<div style="color: #999;">Firma no disponible</div>'}
                    <div class="signature-line">Firma de Planta</div>
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

window.volverAParticiones = function() {
    $(tabParticiones).tab('show');
    tabDocumento.classList.add('disabled');
}

// Inicializar
cargarEnvio();
})();
</script>
@endpush
