@extends('layouts.admin')

@section('title', 'Catálogo de Incidentes - OrgTrack')
@section('page-title', 'Catálogo de Incidentes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Incidentes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Tipos de Incidentes</h3>
                        <p class="text-muted mb-0 small">Gestiona los tipos de incidentes que pueden reportarse durante el transporte</p>
                    </div>
                    <button class="btn btn-primary" id="btnNuevoIncidente">
                        <i class="fas fa-plus"></i> Nuevo Incidente
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="incidentesAlert" class="alert d-none"></div>
                
                {{-- Información de ayuda --}}
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading"><i class="fas fa-info-circle mr-1"></i>¿Qué son los tipos de incidentes?</h6>
                    <p class="mb-0 small">
                        Los tipos de incidentes definen las categorías de problemas que pueden ocurrir durante el transporte.
                        Los transportistas usarán estas categorías para reportar eventos durante el viaje.
                    </p>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 100px;">Código</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th style="width: 120px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaIncidentes">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando tipos de incidentes...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tipo Incidente --}}
<div class="modal fade" id="incidenteModal" tabindex="-1" role="dialog" aria-labelledby="incidenteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="incidenteForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="incidenteModalLabel">
                        <i class="fas fa-plus-circle mr-2"></i>Nuevo Tipo de Incidente
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="incidenteId">
                    
                    {{-- Ayuda contextual --}}
                    <div class="alert alert-light border mb-3">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-lightbulb text-warning mr-1"></i>Guía de llenado:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Código:</strong> Identificador único corto (ej: <code>INC001</code>, <code>ACC-TRAFICO</code>, <code>DEMORA-01</code>)</li>
                            <li><strong>Título:</strong> Nombre del incidente (ej: "Accidente de tráfico", "Falla mecánica", "Condiciones climáticas adversas")</li>
                            <li><strong>Descripción:</strong> Detalles sobre cuándo usar este tipo (ej: "Usar cuando hay retrasos por tráfico pesado o accidentes en ruta")</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="incidenteCodigo">
                            Código <span class="text-danger">*</span>
                            <small class="text-muted">(Identificador único)</small>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="incidenteCodigo" 
                            maxlength="50" 
                            placeholder="Ej: INC001, ACC-TRAFICO, DEMORA-CLIMA"
                            required>
                        <small class="form-text text-muted">
                            <i class="fas fa-lightbulb mr-1"></i>Usa códigos descriptivos y únicos para identificar el tipo de incidente
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="incidenteTitulo">
                            Título <span class="text-danger">*</span>
                            <small class="text-muted">(Nombre del incidente)</small>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="incidenteTitulo" 
                            maxlength="100"
                            placeholder="Ej: Accidente de tráfico, Falla mecánica del vehículo, Demora por clima"
                            required>
                        <small class="form-text text-muted">
                            <i class="fas fa-lightbulb mr-1"></i>Describe el tipo de incidente de forma clara para que sea fácil de seleccionar
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="incidenteDescripcion">
                            Descripción <small class="text-muted">(Opcional)</small>
                        </label>
                        <textarea 
                            class="form-control" 
                            id="incidenteDescripcion" 
                            rows="3" 
                            maxlength="255"
                            placeholder="Ej: Usar cuando hay retrasos por condiciones de tráfico pesado, accidentes en la ruta o cierres viales. Incluir detalles de ubicación y tiempo estimado de demora."></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-lightbulb mr-1"></i>Explica cuándo usar este tipo de incidente y qué información adicional debe incluirse en el reporte
                        </small>
                    </div>

                    <div class="alert alert-danger d-none" id="incidenteError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal eliminar incidente --}}
<div class="modal fade" id="incidenteDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt mr-2"></i>Eliminar Tipo de Incidente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2">¿Estás seguro de eliminar el siguiente tipo de incidente?</p>
                <div class="alert alert-warning">
                    <strong id="incidenteDeleteTitulo"></strong>
                </div>
                <p class="text-muted small mb-0">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Esta acción no se puede deshacer. Este tipo ya no estará disponible para reportar incidentes.
                </p>
                <div class="alert alert-danger d-none mt-3" id="incidenteDeleteError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteIncidente">
                    <i class="fas fa-trash mr-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const rawToken = localStorage.getItem('authToken');
    const token = rawToken ? rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) { window.location.href = '/login'; return; }

    const headers = (isJson = true) => {
        const base = { 'Authorization': `Bearer ${token}` };
        return isJson ? { ...base, 'Content-Type': 'application/json' } : base;
    };

    const state = { incidentes: [], incidenteSeleccionado: null };
    const tablaIncidentes = document.getElementById('tablaIncidentes');
    const incidenteModalEl = $('#incidenteModal');
    const incidenteDeleteModalEl = $('#incidenteDeleteModal');
    const incidenteForm = document.getElementById('incidenteForm');
    const incidenteIdInput = document.getElementById('incidenteId');
    const incidenteCodigoInput = document.getElementById('incidenteCodigo');
    const incidenteTituloInput = document.getElementById('incidenteTitulo');
    const incidenteDescripcionInput = document.getElementById('incidenteDescripcion');
    const incidenteError = document.getElementById('incidenteError');
    const incidenteDeleteError = document.getElementById('incidenteDeleteError');
    const incidenteAlert = document.getElementById('incidentesAlert');

    function setAlert(el, message = '', type = 'success') {
        if (!message) {
            el.classList.add('d-none');
            el.textContent = '';
            return;
        }
        el.className = `alert alert-${type}`;
        el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
        setTimeout(() => setAlert(el), 5000);
    }

    function showError(el, message) {
        if (!el) return;
        if (message) {
            el.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i>${message}`;
            el.classList.remove('d-none');
        } else {
            el.textContent = '';
            el.classList.add('d-none');
        }
    }

    function renderIncidentes() {
        if (!state.incidentes.length) {
            tablaIncidentes.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <p class="mb-0">No hay tipos de incidentes registrados. Haz clic en "Nuevo Incidente" para agregar uno.</p>
                    </td>
                </tr>`;
            return;
        }
        tablaIncidentes.innerHTML = state.incidentes.map(tipo => `
            <tr>
                <td><span class="badge badge-warning">${tipo.codigo}</span></td>
                <td class="font-weight-bold">${tipo.titulo}</td>
                <td>${tipo.descripcion || '<span class="text-muted">—</span>'}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary mr-1" data-action="edit-incidente" data-id="${tipo.id}" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" data-action="delete-incidente" data-id="${tipo.id}" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    async function fetchIncidentes() {
        try {
            setAlert(incidenteAlert);
            const res = await fetch(`${window.location.origin}/api/tipos-incidente-transporte`, { headers: headers(false) });
            if (!res.ok) throw new Error('No se pudieron cargar los tipos de incidente.');
            state.incidentes = await res.json();
            renderIncidentes();
        } catch (error) {
            setAlert(incidenteAlert, error.message, 'danger');
            tablaIncidentes.innerHTML = `<tr><td colspan="4" class="text-danger text-center py-4">${error.message}</td></tr>`;
        }
    }

    function openIncidenteModal(tipo = null) {
        state.incidenteSeleccionado = tipo;
        incidenteIdInput.value = tipo ? tipo.id : '';
        incidenteCodigoInput.value = tipo ? tipo.codigo : '';
        incidenteTituloInput.value = tipo ? tipo.titulo : '';
        incidenteDescripcionInput.value = tipo ? (tipo.descripcion || '') : '';
        document.getElementById('incidenteModalLabel').innerHTML = tipo 
            ? '<i class="fas fa-edit mr-2"></i>Editar Tipo de Incidente' 
            : '<i class="fas fa-plus-circle mr-2"></i>Nuevo Tipo de Incidente';
        showError(incidenteError);
        incidenteModalEl.modal('show');
    }

    document.getElementById('btnNuevoIncidente').addEventListener('click', () => openIncidenteModal());

    incidenteForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError(incidenteError);
        try {
            const payload = {
                codigo: incidenteCodigoInput.value.trim(),
                titulo: incidenteTituloInput.value.trim(),
                descripcion: incidenteDescripcionInput.value.trim() || null,
            };

            if (!payload.codigo || !payload.titulo) {
                showError(incidenteError, 'El código y el título son obligatorios.');
                return;
            }

            const isEdit = Boolean(incidenteIdInput.value);
            const url = isEdit
                ? `${window.location.origin}/api/tipos-incidente-transporte/${incidenteIdInput.value}`
                : `${window.location.origin}/api/tipos-incidente-transporte`;
            const method = isEdit ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method,
                headers: headers(),
                body: JSON.stringify(payload),
            });

            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.error || 'No se pudo guardar el tipo de incidente');
            }

            incidenteModalEl.modal('hide');
            setAlert(incidenteAlert, data.mensaje || 'Tipo de incidente guardado correctamente', 'success');
            await fetchIncidentes();
        } catch (error) {
            showError(incidenteError, error.message);
        }
    });

    tablaIncidentes.addEventListener('click', (event) => {
        const btn = event.target.closest('[data-action]');
        if (!btn) return;
        const id = btn.dataset.id;
        const tipo = state.incidentes.find(c => Number(c.id) === Number(id));
        if (!tipo) return;

        if (btn.dataset.action === 'edit-incidente') {
            openIncidenteModal(tipo);
        } else if (btn.dataset.action === 'delete-incidente') {
            state.incidenteSeleccionado = tipo;
            document.getElementById('incidenteDeleteTitulo').textContent = tipo.titulo;
            showError(incidenteDeleteError);
            incidenteDeleteModalEl.modal('show');
        }
    });

    document.getElementById('btnConfirmDeleteIncidente').addEventListener('click', async () => {
        if (!state.incidenteSeleccionado) return;
        showError(incidenteDeleteError);
        try {
            const res = await fetch(`${window.location.origin}/api/tipos-incidente-transporte/${state.incidenteSeleccionado.id}`, {
                method: 'DELETE',
                headers: headers(false),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'No se pudo eliminar el tipo de incidente');
            incidenteDeleteModalEl.modal('hide');
            setAlert(incidenteAlert, data.mensaje || 'Tipo de incidente eliminado correctamente', 'success');
            await fetchIncidentes();
        } catch (error) {
            showError(incidenteDeleteError, error.message);
        }
    });

    fetchIncidentes();
})();
</script>
@endsection
