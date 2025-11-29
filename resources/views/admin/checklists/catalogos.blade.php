@extends('layouts.adminlte')

@section('page-title', 'Catálogos de Checklist')

@section('page-content')
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0">Condiciones de transporte</h3>
                    <small class="text-muted">Definen los puntos del checklist previo al viaje</small>
                </div>
                <button class="btn btn-success btn-sm" id="btnNuevaCondicion">
                    <i class="fas fa-plus"></i> Nueva condición
                </button>
            </div>
            <div class="card-body p-0">
                <div id="condicionesAlert" class="alert d-none m-3"></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 80px;">Código</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th style="width: 110px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCondiciones">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Cargando condiciones...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0">Tipos de incidentes</h3>
                    <small class="text-muted">Catálogo usado en el checklist durante el viaje</small>
                </div>
                <button class="btn btn-primary btn-sm" id="btnNuevoIncidente">
                    <i class="fas fa-plus"></i> Nuevo tipo
                </button>
            </div>
            <div class="card-body p-0">
                <div id="incidentesAlert" class="alert d-none m-3"></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 80px;">Código</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th style="width: 110px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaIncidentes">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Cargando tipos de incidente...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Condición --}}
<div class="modal fade" id="condicionModal" tabindex="-1" role="dialog" aria-labelledby="condicionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="condicionForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="condicionModalLabel">Nueva condición</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="condicionId">
                    <div class="form-group">
                        <label for="condicionCodigo">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="condicionCodigo" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label for="condicionTitulo">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="condicionTitulo" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="condicionDescripcion">Descripción</label>
                        <textarea class="form-control" id="condicionDescripcion" rows="2" maxlength="255"></textarea>
                    </div>
                    <div class="alert alert-danger d-none" id="condicionError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal eliminar condición --}}
<div class="modal fade" id="condicionDeleteModal" tabindex="-1" role="dialog" aria-labelledby="condicionDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="condicionDeleteLabel">Eliminar condición</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Deseas eliminar la condición <strong id="condicionDeleteTitulo"></strong>?
                <div class="alert alert-danger d-none mt-2" id="condicionDeleteError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteCondicion">Eliminar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tipo Incidente --}}
<div class="modal fade" id="incidenteModal" tabindex="-1" role="dialog" aria-labelledby="incidenteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="incidenteForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="incidenteModalLabel">Nuevo tipo de incidente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="incidenteId">
                    <div class="form-group">
                        <label for="incidenteCodigo">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="incidenteCodigo" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label for="incidenteTitulo">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="incidenteTitulo" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="incidenteDescripcion">Descripción</label>
                        <textarea class="form-control" id="incidenteDescripcion" rows="2" maxlength="255"></textarea>
                    </div>
                    <div class="alert alert-danger d-none" id="incidenteError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal eliminar incidente --}}
<div class="modal fade" id="incidenteDeleteModal" tabindex="-1" role="dialog" aria-labelledby="incidenteDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incidenteDeleteLabel">Eliminar tipo de incidente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Deseas eliminar el tipo <strong id="incidenteDeleteTitulo"></strong>?
                <div class="alert alert-danger d-none mt-2" id="incidenteDeleteError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteIncidente">Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    const rawToken = localStorage.getItem('authToken');
    const token = rawToken ? rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) { window.location.href = '/login'; return; }

    const headers = (isJson = true) => {
        const base = { 'Authorization': `Bearer ${token}` };
        return isJson ? { ...base, 'Content-Type': 'application/json' } : base;
    };

    const state = {
        condiciones: [],
        incidentes: [],
        condicionSeleccionada: null,
        incidenteSeleccionado: null,
    };

    const tablaCondiciones = document.getElementById('tablaCondiciones');
    const tablaIncidentes = document.getElementById('tablaIncidentes');
    const condicionModalEl = $('#condicionModal');
    const condicionDeleteModalEl = $('#condicionDeleteModal');
    const incidenteModalEl = $('#incidenteModal');
    const incidenteDeleteModalEl = $('#incidenteDeleteModal');

    const condicionForm = document.getElementById('condicionForm');
    const incidenteForm = document.getElementById('incidenteForm');

    const condicionIdInput = document.getElementById('condicionId');
    const condicionCodigoInput = document.getElementById('condicionCodigo');
    const condicionTituloInput = document.getElementById('condicionTitulo');
    const condicionDescripcionInput = document.getElementById('condicionDescripcion');

    const incidenteIdInput = document.getElementById('incidenteId');
    const incidenteCodigoInput = document.getElementById('incidenteCodigo');
    const incidenteTituloInput = document.getElementById('incidenteTitulo');
    const incidenteDescripcionInput = document.getElementById('incidenteDescripcion');

    const condicionError = document.getElementById('condicionError');
    const incidenteError = document.getElementById('incidenteError');
    const condicionDeleteError = document.getElementById('condicionDeleteError');
    const incidenteDeleteError = document.getElementById('incidenteDeleteError');

    const condicionAlert = document.getElementById('condicionesAlert');
    const incidenteAlert = document.getElementById('incidentesAlert');

    function setAlert(el, message = '', type = 'success') {
        if (!message) {
            el.classList.add('d-none');
            el.textContent = '';
            return;
        }
        el.className = `alert alert-${type}`;
        el.textContent = message;
    }

    function showError(el, message) {
        if (!el) return;
        if (message) {
            el.textContent = message;
            el.classList.remove('d-none');
        } else {
            el.textContent = '';
            el.classList.add('d-none');
        }
    }

    function renderCondiciones() {
        if (!state.condiciones.length) {
            tablaCondiciones.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No hay condiciones registradas.</td>
                </tr>`;
            return;
        }
        tablaCondiciones.innerHTML = state.condiciones.map(cond => `
            <tr>
                <td><span class="badge badge-light">${cond.codigo}</span></td>
                <td class="font-weight-semibold">${cond.titulo}</td>
                <td>${cond.descripcion || '—'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary mr-1" data-action="edit-condicion" data-id="${cond.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" data-action="delete-condicion" data-id="${cond.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function renderIncidentes() {
        if (!state.incidentes.length) {
            tablaIncidentes.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No hay tipos de incidente registrados.</td>
                </tr>`;
            return;
        }
        tablaIncidentes.innerHTML = state.incidentes.map(tipo => `
            <tr>
                <td><span class="badge badge-light">${tipo.codigo}</span></td>
                <td class="font-weight-semibold">${tipo.titulo}</td>
                <td>${tipo.descripcion || '—'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary mr-1" data-action="edit-incidente" data-id="${tipo.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" data-action="delete-incidente" data-id="${tipo.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    async function fetchCondiciones() {
        try {
            setAlert(condicionAlert);
            const res = await fetch(`${window.location.origin}/api/condiciones-transporte`, { headers: headers(false) });
            if (!res.ok) throw new Error('No se pudieron cargar las condiciones.');
            state.condiciones = await res.json();
            renderCondiciones();
        } catch (error) {
            setAlert(condicionAlert, error.message, 'danger');
            tablaCondiciones.innerHTML = `<tr><td colspan="4" class="text-danger text-center py-4">${error.message}</td></tr>`;
        }
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

    function openCondicionModal(condicion = null) {
        state.condicionSeleccionada = condicion;
        condicionIdInput.value = condicion ? condicion.id : '';
        condicionCodigoInput.value = condicion ? condicion.codigo : '';
        condicionTituloInput.value = condicion ? condicion.titulo : '';
        condicionDescripcionInput.value = condicion ? (condicion.descripcion || '') : '';
        document.getElementById('condicionModalLabel').textContent = condicion ? 'Editar condición' : 'Nueva condición';
        showError(condicionError);
        condicionModalEl.modal('show');
    }

    function openIncidenteModal(tipo = null) {
        state.incidenteSeleccionado = tipo;
        incidenteIdInput.value = tipo ? tipo.id : '';
        incidenteCodigoInput.value = tipo ? tipo.codigo : '';
        incidenteTituloInput.value = tipo ? tipo.titulo : '';
        incidenteDescripcionInput.value = tipo ? (tipo.descripcion || '') : '';
        document.getElementById('incidenteModalLabel').textContent = tipo ? 'Editar tipo de incidente' : 'Nuevo tipo de incidente';
        showError(incidenteError);
        incidenteModalEl.modal('show');
    }

    document.getElementById('btnNuevaCondicion').addEventListener('click', () => openCondicionModal());
    document.getElementById('btnNuevoIncidente').addEventListener('click', () => openIncidenteModal());

    condicionForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError(condicionError);
        try {
            const payload = {
                codigo: condicionCodigoInput.value.trim(),
                titulo: condicionTituloInput.value.trim(),
                descripcion: condicionDescripcionInput.value.trim() || null,
            };

            if (!payload.codigo || !payload.titulo) {
                showError(condicionError, 'Completa los campos obligatorios.');
                return;
            }

            const isEdit = Boolean(condicionIdInput.value);
            const url = isEdit
                ? `${window.location.origin}/api/condiciones-transporte/${condicionIdInput.value}`
                : `${window.location.origin}/api/condiciones-transporte`;
            const method = isEdit ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method,
                headers: headers(),
                body: JSON.stringify(payload),
            });

            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.error || 'No se pudo guardar la condición');
            }

            condicionModalEl.modal('hide');
            setAlert(condicionAlert, data.mensaje || 'Cambios guardados correctamente');
            await fetchCondiciones();
        } catch (error) {
            showError(condicionError, error.message);
        }
    });

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
                showError(incidenteError, 'Completa los campos obligatorios.');
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
            setAlert(incidenteAlert, data.mensaje || 'Cambios guardados correctamente');
            await fetchIncidentes();
        } catch (error) {
            showError(incidenteError, error.message);
        }
    });

    tablaCondiciones.addEventListener('click', (event) => {
        const btn = event.target.closest('[data-action]');
        if (!btn) return;
        const id = btn.dataset.id;
        const condicion = state.condiciones.find(c => Number(c.id) === Number(id));
        if (!condicion) return;

        if (btn.dataset.action === 'edit-condicion') {
            openCondicionModal(condicion);
        } else if (btn.dataset.action === 'delete-condicion') {
            state.condicionSeleccionada = condicion;
            document.getElementById('condicionDeleteTitulo').textContent = condicion.titulo;
            showError(condicionDeleteError);
            condicionDeleteModalEl.modal('show');
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

    document.getElementById('btnConfirmDeleteCondicion').addEventListener('click', async () => {
        if (!state.condicionSeleccionada) return;
        showError(condicionDeleteError);
        try {
            const res = await fetch(`${window.location.origin}/api/condiciones-transporte/${state.condicionSeleccionada.id}`, {
                method: 'DELETE',
                headers: headers(false),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'No se pudo eliminar la condición');
            condicionDeleteModalEl.modal('hide');
            setAlert(condicionAlert, data.mensaje || 'Condición eliminada');
            await fetchCondiciones();
        } catch (error) {
            showError(condicionDeleteError, error.message);
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
            setAlert(incidenteAlert, data.mensaje || 'Tipo de incidente eliminado');
            await fetchIncidentes();
        } catch (error) {
            showError(incidenteDeleteError, error.message);
        }
    });

    function init() {
        fetchCondiciones();
        fetchIncidentes();
    }

    init();
})();
</script>
@endpush