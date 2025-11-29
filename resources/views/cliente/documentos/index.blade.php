@extends('layouts.cliente')

@section('page-title', 'Mis Documentos')

@section('page-content')
<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
    .document-card { background: white; padding: 40px; max-width: 900px; margin: 0 auto; }
    .document-header { text-align: center; margin-bottom: 30px; padding-bottom: 15px; }
    .document-header h2 { font-size: 32px; margin-bottom: 5px; color: #333; }
    .document-header p { font-size: 14px; color: #666; font-style: italic; }
    .document-table { width: 100%; border-collapse: collapse; margin-bottom: 0; font-size: 13px; }
    .document-table th, .document-table td { border: 1px solid #000; padding: 10px; }
    .document-table th { background-color: #f8f9fa; font-weight: bold; text-align: left; }
    .document-table td { text-align: left; }
    .table-section { margin-bottom: 15px; }
    .section-title { font-weight: bold; font-size: 14px; margin-top: 20px; margin-bottom: 10px; text-align: center; background-color: #f8f9fa; padding: 8px; border: 1px solid #000; }
    .checklist-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
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
                            <i class="fas fa-file-pdf"></i> Documentos completos
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
                            <p>No tienes envíos entregados disponibles.</p>
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
                        <div class="no-print mb-3">
                            <button class="btn btn-secondary mr-2" onclick="volverDesdeDocumento()">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                        <div id="printableArea">
                            <div id="documentoContainer"></div>
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
    const _rawToken = localStorage.getItem('authToken');
    const token = _rawToken ? _rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) { window.location.href = '/login'; }

    const enviosContainer = document.getElementById('enviosContainer');
    const particionesContainer = document.getElementById('particionesContainer');
    const documentoContainer = document.getElementById('documentoContainer');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noEnvios = document.getElementById('noEnvios');
    const searchInput = document.getElementById('searchInput');

    let enviosData = [];
    let envioSeleccionado = null;
    let origenTab = 'historial'; // Para saber desde dónde volver

    function formatDate(dateString) {
        if (!dateString) return '—';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    function formatTime(timeString) {
        if (!timeString) return '—';
        return timeString.substring(0, 5);
    }

    function badgeFor(estado) {
        const map = {
            'En curso': 'badge-info',
            'Pendiente': 'badge-warning',
            'Asignado': 'badge-primary',
            'Entregado': 'badge-success',
            'Finalizado': 'badge-secondary'
        };
        const cls = map[estado] || 'badge-light';
        return `<span class="badge ${cls}">${estado}</span>`;
    }

    function renderEnvio(envio) {
        const cantParticiones = envio.particiones?.length || 0;
        return `
            <div class="card mb-3 envio-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <h5 class="mb-2">
                                <strong>ID: ${envio.id}</strong>
                                ${badgeFor(envio.estado)}
                            </h5>
                            <p class="mb-1"><strong>Recogida:</strong> ${formatDate(envio.fecha_creacion)}</p>
                            <p class="mb-1"><strong>Entrega:</strong> ${envio.fecha_entrega ? formatDate(envio.fecha_entrega) : '—'}</p>
                            <p class="mb-1"><strong>Origen:</strong> ${envio.nombre_origen || '—'}</p>
                            <p class="mb-0"><strong>Destino:</strong> ${envio.nombre_destino || '—'}</p>
                        </div>
                        <div class="col-md-3 text-center d-flex flex-column justify-content-center">
                            <i class="far fa-file-pdf fa-4x text-danger mb-3"></i>
                            <button class="btn btn-primary btn-block mb-2" onclick="verDocumentoCompleto(${envio.id})">
                                <i class="fas fa-file-alt"></i> Documento completo
                            </button>
                            ${cantParticiones > 0 ? `
                                <button class="btn btn-outline-primary btn-block" onclick="verParticiones(${envio.id})">
                                    <i class="fas fa-th-list"></i> Ver particiones (${cantParticiones})
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderEnvios(envios) {
        if (envios.length === 0) {
            enviosContainer.style.display = 'none';
            noEnvios.style.display = 'block';
            return;
        }

        enviosContainer.innerHTML = envios.map(envio => renderEnvio(envio)).join('');
        enviosContainer.style.display = 'block';
        noEnvios.style.display = 'none';
    }

    function renderParticiones(envio) {
        if (!envio.particiones || envio.particiones.length === 0) {
            return '<p class="text-muted">No hay particiones disponibles.</p>';
        }

        return `
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>ID PARTICIÓN</th>
                        <th>ESTADO</th>
                        <th>TRANSPORTISTA</th>
                        <th>VEHÍCULO</th>
                        <th>ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    ${envio.particiones.map(p => `
                        <tr>
                            <td><strong>#${p.id_asignacion}</strong></td>
                            <td>${badgeFor(p.estado)}</td>
                            <td>${p.transportista?.nombre || '—'} ${p.transportista?.apellido || ''}</td>
                            <td>${p.vehiculo?.placa || '—'}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="verDocumentoParticion(${p.id_asignacion})">
                                    <i class="fas fa-file-alt"></i> Ver Documento
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    }

    function verParticiones(envioId) {
        envioSeleccionado = enviosData.find(e => e.id === envioId);
        if (!envioSeleccionado) return;

        origenTab = 'historial';
        $('#tab-particiones').removeClass('disabled').tab('show');
        particionesContainer.innerHTML = renderParticiones(envioSeleccionado);
    }

    async function verDocumentoCompleto(envioId) {
        try {
            origenTab = 'historial';
            documentoContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Cargando documento...</p></div>';
            $('#tab-documento').removeClass('disabled').tab('show');

            const res = await fetch(`${window.location.origin}/api/envios/${envioId}/documento`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!res.ok) {
                const errorData = await res.json();
                throw new Error(errorData.error || 'Error al cargar el documento');
            }

            const data = await res.json();
            renderDocumentoCompleto(data);

        } catch (e) {
            documentoContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>${e.message}</div>`;
        }
    }

    async function verDocumentoParticion(asignacionId) {
        try {
            origenTab = 'particiones';
            documentoContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Cargando documento...</p></div>';
            $('#tab-documento').removeClass('disabled').tab('show');

            const res = await fetch(`${window.location.origin}/api/envios/asignacion/${asignacionId}/documento`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!res.ok) throw new Error('Error al cargar el documento');

            const data = await res.json();
            renderDocumentoParticion(data);

        } catch (e) {
            documentoContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>${e.message}</div>`;
        }
    }

    function renderDocumentoCompleto(data) {
        let html = '';
        
        console.log('Datos recibidos para documento completo:', data);
        
        data.particiones.forEach((particion, index) => {
            console.log(`Partición ${index + 1}:`, particion);
            console.log('Firma:', particion.firma);
            console.log('Firma Transportista:', particion.firmaTransportista);
            
            html += `
                ${index > 0 ? '<div class="page-break" style="page-break-before: always; margin-top: 40px; padding-top: 20px;"></div>' : ''}
                <div class="document-card">
                    <div class="document-header">
                        <h2><strong>Ortrack</strong></h2>
                        <p>Documento de Envío</p>
                    </div>

                    <div class="table-section">
                        <table class="document-table">
                            <tr>
                                <th style="width: 50%;">Nombre de cliente</th>
                                <th style="width: 50%;">Fecha</th>
                            </tr>
                            <tr>
                                <td>${data.nombre_cliente || '—'}</td>
                                <td>${formatDate(data.fecha_creacion)}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="table-section">
                        <table class="document-table">
                            <tr>
                                <th style="width: 50%;">Punto de recogida</th>
                                <th style="width: 50%;">Punto de Entrega</th>
                            </tr>
                            <tr>
                                <td>${data.nombre_origen || '—'}</td>
                                <td>${data.nombre_destino || '—'}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="section-title">Detalles de Bloque de Envío</div>
                    <div class="table-section">
                        <table class="document-table">
                            <tr>
                                <th style="width: 33.33%;">Día</th>
                                <th style="width: 33.33%;">Hora de Recogida</th>
                                <th style="width: 33.33%;">Hora de Entrega</th>
                            </tr>
                            <tr>
                                <td>${formatDate(particion.recogidaEntrega?.fecha_recogida)}</td>
                                <td>${formatTime(particion.recogidaEntrega?.hora_recogida)}</td>
                                <td>${formatTime(particion.recogidaEntrega?.hora_entrega)}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="table-section">
                        <table class="document-table">
                            <tr>
                                <th style="width: 50%;">Instrucciones en punto de recogida</th>
                                <th style="width: 50%;">Instrucciones en punto de entrega</th>
                            </tr>
                            <tr>
                                <td>${particion.recogidaEntrega?.instrucciones_recogida || 'Sin instrucciones'}</td>
                                <td>${particion.recogidaEntrega?.instrucciones_entrega || 'Sin instrucciones'}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="section-title">Transportista</div>
                    <div class="table-section">
                        <table class="document-table">
                            <tr>
                                <th style="width: 50%;">Nombre y Apellido</th>
                                <th style="width: 25%;">Teléfono</th>
                                <th style="width: 25%;">CI</th>
                            </tr>
                            <tr>
                                <td>${particion.transportista?.nombre || '—'} ${particion.transportista?.apellido || ''}</td>
                                <td>${particion.transportista?.telefono || '—'}</td>
                                <td>${particion.transportista?.ci || '—'}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="section-title">Vehículo</div>
                    <div class="table-section">
                        <table class="document-table">
                            <tr>
                                <th style="width: 50%;">Tipo</th>
                                <th style="width: 50%;">Placa</th>
                            </tr>
                            <tr>
                                <td>${particion.vehiculo?.tipo || '—'}</td>
                                <td>${particion.vehiculo?.placa || '—'}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="section-title">Transporte</div>
                    <div class="table-section">
                        <table class="document-table">
                            <tr>
                                <th style="width: 30%;">Tipo</th>
                                <th style="width: 70%;">Descripción</th>
                            </tr>
                            <tr>
                                <td>${particion.tipo_transporte?.nombre || '—'}</td>
                                <td>${particion.tipo_transporte?.descripcion || '—'}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="section-title">Detalles de cargamento</div>
                    <div class="table-section">
                        <table class="document-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Tipo</th>
                                    <th style="width: 20%;">Variedad</th>
                                    <th style="width: 20%;">Empaquetado</th>
                                    <th style="width: 20%;">Cantidad</th>
                                    <th style="width: 20%;">Peso Kg</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${particion.cargas && particion.cargas.length > 0 ? particion.cargas.map(c => `
                                    <tr>
                                        <td>${c.tipo || '—'}</td>
                                        <td>${c.variedad || '—'}</td>
                                        <td>${c.empaquetado || '—'}</td>
                                        <td>${c.cantidad || 0}</td>
                                        <td>${c.peso || 0} kg</td>
                                    </tr>
                                `).join('') : '<tr><td colspan="5" class="text-center text-muted">Sin cargas registradas</td></tr>'}
                            </tbody>
                        </table>
                    </div>

                    ${(() => {
                        const hasFirmaTransportista = particion.firmaTransportista && particion.firmaTransportista.trim() && particion.firmaTransportista.length > 100;
                        const hasFirma = particion.firma && particion.firma.trim() && particion.firma.length > 100;
                        
                        if (!hasFirmaTransportista && !hasFirma) return '';
                        
                        return `
                            <div class="signature-container">
                                ${hasFirmaTransportista ? `
                                    <div class="signature-item">
                                        <img src="${particion.firmaTransportista.startsWith('data:') ? particion.firmaTransportista : 'data:image/png;base64,' + particion.firmaTransportista}" 
                                             alt="Firma Transportista" 
                                             onerror="console.error('Error cargando firma transportista'); this.parentElement.style.display='none';"
                                             onload="console.log('Firma transportista cargada correctamente')">
                                        <div class="signature-line">Firma del Transportista</div>
                                    </div>
                                ` : ''}
                                ${hasFirma ? `
                                    <div class="signature-item">
                                        <img src="${particion.firma.startsWith('data:') ? particion.firma : 'data:image/png;base64,' + particion.firma}" 
                                             alt="Firma Cliente" 
                                             onerror="console.error('Error cargando firma cliente'); this.parentElement.style.display='none';"
                                             onload="console.log('Firma cliente cargada correctamente')">
                                        <div class="signature-line">Firma del Cliente</div>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    })()}

                    ${renderChecklistCondiciones(particion.checklistCondiciones)}
                    ${renderChecklistIncidentes(particion.checklistIncidentes)}
                </div>
            `;
        });

        documentoContainer.innerHTML = html;
    }

    function renderDocumentoParticion(data) {
        const particion = data.particion;
        
        console.log('Datos recibidos para partición:', data);
        console.log('Partición:', particion);
        console.log('Firma:', particion.firma);
        console.log('Firma Transportista:', particion.firma_transportista);
        
        documentoContainer.innerHTML = `
            <div class="document-card">
                <div class="document-header">
                    <h2><strong>Ortrack</strong></h2>
                    <p>Documento de Envío</p>
                </div>

                <div class="table-section">
                    <table class="document-table">
                        <tr>
                            <th style="width: 50%;">Nombre de cliente</th>
                            <th style="width: 50%;">Fecha</th>
                        </tr>
                        <tr>
                            <td>${data.nombre_cliente || '—'}</td>
                            <td>${formatDate(data.fecha_creacion)}</td>
                        </tr>
                    </table>
                </div>

                <div class="table-section">
                    <table class="document-table">
                        <tr>
                            <th style="width: 50%;">Punto de recogida</th>
                            <th style="width: 50%;">Punto de Entrega</th>
                        </tr>
                        <tr>
                            <td>${data.nombre_origen || '—'}</td>
                            <td>${data.nombre_destino || '—'}</td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Detalles de Bloque de Envío</div>
                <div class="table-section">
                    <table class="document-table">
                        <tr>
                            <th style="width: 33.33%;">Día</th>
                            <th style="width: 33.33%;">Hora de Recogida</th>
                            <th style="width: 33.33%;">Hora de Entrega</th>
                        </tr>
                        <tr>
                            <td>${formatDate(particion.recogidaEntrega?.fecha_recogida)}</td>
                            <td>${formatTime(particion.recogidaEntrega?.hora_recogida)}</td>
                            <td>${formatTime(particion.recogidaEntrega?.hora_entrega)}</td>
                        </tr>
                    </table>
                </div>

                <div class="table-section">
                    <table class="document-table">
                        <tr>
                            <th style="width: 50%;">Instrucciones en punto de recogida</th>
                            <th style="width: 50%;">Instrucciones en punto de entrega</th>
                        </tr>
                        <tr>
                            <td>${particion.recogidaEntrega?.instrucciones_recogida || 'Sin instrucciones'}</td>
                            <td>${particion.recogidaEntrega?.instrucciones_entrega || 'Sin instrucciones'}</td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Transportista</div>
                <div class="table-section">
                    <table class="document-table">
                        <tr>
                            <th style="width: 50%;">Nombre y Apellido</th>
                            <th style="width: 25%;">Teléfono</th>
                            <th style="width: 25%;">CI</th>
                        </tr>
                        <tr>
                            <td>${particion.transportista?.nombre || '—'} ${particion.transportista?.apellido || ''}</td>
                            <td>${particion.transportista?.telefono || '—'}</td>
                            <td>${particion.transportista?.ci || '—'}</td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Vehículo</div>
                <div class="table-section">
                    <table class="document-table">
                        <tr>
                            <th style="width: 50%;">Tipo</th>
                            <th style="width: 50%;">Placa</th>
                        </tr>
                        <tr>
                            <td>${particion.vehiculo?.tipo || '—'}</td>
                            <td>${particion.vehiculo?.placa || '—'}</td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Transporte</div>
                <div class="table-section">
                    <table class="document-table">
                        <tr>
                            <th style="width: 30%;">Tipo</th>
                            <th style="width: 70%;">Descripción</th>
                        </tr>
                        <tr>
                            <td>${particion.tipo_transporte?.nombre || '—'}</td>
                            <td>${particion.tipo_transporte?.descripcion || '—'}</td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Detalles de cargamento</div>
                <div class="table-section">
                    <table class="document-table">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Tipo</th>
                                <th style="width: 20%;">Variedad</th>
                                <th style="width: 20%;">Empaquetado</th>
                                <th style="width: 20%;">Cantidad</th>
                                <th style="width: 20%;">Peso Kg</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${particion.cargas && particion.cargas.length > 0 ? particion.cargas.map(c => `
                                <tr>
                                    <td>${c.tipo || '—'}</td>
                                    <td>${c.variedad || '—'}</td>
                                    <td>${c.empaquetado || '—'}</td>
                                    <td>${c.cantidad || 0}</td>
                                    <td>${c.peso || 0} kg</td>
                                </tr>
                            `).join('') : '<tr><td colspan="5" class="text-center text-muted">Sin cargas registradas</td></tr>'}
                        </tbody>
                    </table>
                </div>

                ${(() => {
                    const hasFirmaTransportista = particion.firma_transportista && particion.firma_transportista.trim() && particion.firma_transportista.length > 100;
                    const hasFirma = particion.firma && particion.firma.trim() && particion.firma.length > 100;
                    
                    if (!hasFirmaTransportista && !hasFirma) return '';
                    
                    return `
                        <div class="signature-container">
                            ${hasFirmaTransportista ? `
                                <div class="signature-item">
                                    <img src="${particion.firma_transportista.startsWith('data:') ? particion.firma_transportista : 'data:image/png;base64,' + particion.firma_transportista}" 
                                         alt="Firma Transportista" 
                                         onerror="console.error('Error cargando firma transportista'); this.parentElement.style.display='none';"
                                         onload="console.log('Firma transportista cargada correctamente')">
                                    <div class="signature-line">Firma del Transportista</div>
                                </div>
                            ` : ''}
                            ${hasFirma ? `
                                <div class="signature-item">
                                    <img src="${particion.firma.startsWith('data:') ? particion.firma : 'data:image/png;base64,' + particion.firma}" 
                                         alt="Firma Cliente" 
                                         onerror="console.error('Error cargando firma cliente'); this.parentElement.style.display='none';"
                                         onload="console.log('Firma cliente cargada correctamente')">
                                    <div class="signature-line">Firma del Cliente</div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                })()}

                ${renderChecklistCondiciones(particion.checklistCondiciones)}
                ${renderChecklistIncidentes(particion.checklistIncidentes)}
            </div>
        `;
    }

    function renderChecklistCondiciones(condiciones) {
        if (!condiciones || condiciones.length === 0) return '';

        return `
            <h5 class="mt-4">Registro de condiciones de transporte</h5>
            <table class="checklist-table">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 70%;">Condiciones de Transporte</th>
                        <th style="width: 15%;">Sí</th>
                        <th style="width: 15%;">No</th>
                    </tr>
                </thead>
                <tbody>
                    ${condiciones.map(c => `
                        <tr>
                            <td>${c.condicion?.titulo || c.titulo || '—'}</td>
                            <td style="text-align: center;">${c.cumple || c.estado ? '✓' : ''}</td>
                            <td style="text-align: center;">${!c.cumple && !c.estado ? '✗' : ''}</td>
                        </tr>
                    `).join('')}
                    ${condiciones.some(c => c.observaciones) ? `
                        <tr>
                            <td colspan="3"><strong>Observaciones:</strong><br>${condiciones.find(c => c.observaciones)?.observaciones || ''}</td>
                        </tr>
                    ` : ''}
                </tbody>
            </table>
        `;
    }

    function renderChecklistIncidentes(incidentes) {
        if (!incidentes || incidentes.length === 0) return '';

        return `
            <h5 class="mt-4">Registro de incidentes de transporte</h5>
            <table class="checklist-table">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 70%;">Incidentes de Transporte</th>
                        <th style="width: 15%;">Sí</th>
                        <th style="width: 15%;">No</th>
                    </tr>
                </thead>
                <tbody>
                    ${incidentes.map(i => `
                        <tr>
                            <td>${i.tipoIncidente?.titulo || i.titulo || '—'}</td>
                            <td style="text-align: center;">${i.ocurrido ? '✓' : ''}</td>
                            <td style="text-align: center;">${!i.ocurrido ? '✗' : ''}</td>
                        </tr>
                    `).join('')}
                    ${incidentes.some(i => i.observaciones) ? `
                        <tr>
                            <td colspan="3"><strong>Observaciones:</strong><br>${incidentes.find(i => i.observaciones)?.observaciones || ''}</td>
                        </tr>
                    ` : ''}
                </tbody>
            </table>
        `;
    }

    function volverAHistorial() {
        $('#tab-historial').tab('show');
        $('#tab-particiones').addClass('disabled');
        $('#tab-documento').addClass('disabled');
    }

    function volverDesdeDocumento() {
        if (origenTab === 'particiones') {
            $('#tab-particiones').tab('show');
        } else {
            $('#tab-historial').tab('show');
        }
    }

    async function cargarEnvios() {
        try {
            loadingSpinner.style.display = 'block';
            enviosContainer.style.display = 'none';
            noEnvios.style.display = 'none';

            const res = await fetch(`${window.location.origin}/api/envios/mis-envios`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!res.ok) {
                if (res.status === 401) {
                    localStorage.removeItem('authToken');
                    window.location.href = '/login';
                    return;
                }
                throw new Error('No se pudieron cargar los envíos');
            }

            const envios = await res.json();
            
            // Filtrar SOLO envíos entregados
            enviosData = envios.filter(e => e.estado === 'Entregado');
            
            loadingSpinner.style.display = 'none';
            renderEnvios(enviosData);

        } catch (e) {
            loadingSpinner.style.display = 'none';
            enviosContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>${e.message}</div>`;
            enviosContainer.style.display = 'block';
        }
    }

    // Búsqueda en tiempo real
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredEnvios = enviosData.filter(envio => {
            return envio.id.toString().includes(searchTerm);
        });
        renderEnvios(filteredEnvios);
    });

    cargarEnvios();
</script>
@endpush