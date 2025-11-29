@extends('layouts.adminlte')

@section('page-title', 'Catálogo de Condiciones')

@section('page-content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1"><i class="fas fa-clipboard-check mr-2"></i>Condiciones de Transporte</h3>
                        <p class="text-muted mb-0 small">Gestiona las condiciones que se verifican antes de iniciar un viaje</p>
                    </div>
                    <button class="btn btn-success" id="btnNuevaCondicion">
                        <i class="fas fa-plus"></i> Nueva Condición
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="condicionesAlert" class="alert d-none"></div>
                
                {{-- Información de ayuda --}}
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading"><i class="fas fa-info-circle mr-1"></i>¿Qué son las condiciones de transporte?</h6>
                    <p class="mb-0 small">
                        Las condiciones son puntos de verificación que el transportista debe completar antes de iniciar el viaje.
                        Cada condición representa un aspecto de seguridad o preparación del vehículo y carga.
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
                        <tbody id="tablaCondiciones">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando condiciones...
                                </td>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="condicionForm">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="condicionModalLabel">
                        <i class="fas fa-plus-circle mr-2"></i>Nueva Condición
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="condicionId">
                    
                    {{-- Ayuda contextual --}}
                    <div class="alert alert-light border mb-3">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-lightbulb text-warning mr-1"></i>Guía de llenado:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Código:</strong> Identificador único (ej: <code>C001</code>, <code>VEH-LUCES</code>, <code>COND-01</code>)</li>
                            <li><strong>Título:</strong> Descripción corta y clara (ej: "Luces delanteras funcionando", "Neumáticos en buen estado")</li>
                            <li><strong>Descripción:</strong> Detalles adicionales o instrucciones (ej: "Verificar ambas luces altas y bajas")</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="condicionCodigo">
                            Código <span class="text-danger">*</span>
                            <small class="text-muted">(Identificador único)</small>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="condicionCodigo" 
                            maxlength="50" 
                            placeholder="Ej: C001, VEH-LUCES, COND-FRENOS"
                            required>
                        <small class="form-text text-muted">
                            <i class="fas fa-lightbulb mr-1"></i>Usa códigos cortos y descriptivos para identificar rápidamente la condición
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="condicionTitulo">
                            Título <span class="text-danger">*</span>
                            <small class="text-muted">(Nombre de la condición)</small>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="condicionTitulo" 
                            maxlength="100"
                            placeholder="Ej: Luces delanteras funcionando, Neumáticos en buen estado"
                            required>
                        <small class="form-text text-muted">
                            <i class="fas fa-lightbulb mr-1"></i>Describe qué debe verificar el transportista de forma clara y concisa
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="condicionDescripcion">
                            Descripción <small class="text-muted">(Opcional)</small>
                        </label>
                        <textarea 
                            class="form-control" 
                            id="condicionDescripcion" 
                            rows="3" 
                            maxlength="255"
                            placeholder="Ej: Verificar que ambas luces (altas y bajas) funcionen correctamente. Revisar conexiones eléctricas."></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-lightbulb mr-1"></i>Agrega instrucciones adicionales o detalles importantes sobre cómo verificar esta condición
                        </small>
                    </div>

                    <div class="alert alert-danger d-none" id="condicionError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal eliminar condición --}}
<div class="modal fade" id="condicionDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt mr-2"></i>Eliminar Condición
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2">¿Estás seguro de eliminar la siguiente condición?</p>
                <div class="alert alert-warning">
                    <strong id="condicionDeleteTitulo"></strong>
                </div>
                <p class="text-muted small mb-0">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Esta acción no se puede deshacer. La condición ya no estará disponible en los checklists.
                </p>
                <div class="alert alert-danger d-none mt-3" id="condicionDeleteError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteCondicion">
                    <i class="fas fa-trash mr-1"></i>Eliminar
                </button>
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

    const state = { condiciones: [], condicionSeleccionada: null };
    const tablaCondiciones = document.getElementById('tablaCondiciones');
    const condicionModalEl = $('#condicionModal');
    const condicionDeleteModalEl = $('#condicionDeleteModal');
    const condicionForm = document.getElementById('condicionForm');
    const condicionIdInput = document.getElementById('condicionId');
    const condicionCodigoInput = document.getElementById('condicionCodigo');
    const condicionTituloInput = document.getElementById('condicionTitulo');
    const condicionDescripcionInput = document.getElementById('condicionDescripcion');
    const condicionError = document.getElementById('condicionError');
    const condicionDeleteError = document.getElementById('condicionDeleteError');
    const condicionAlert = document.getElementById('condicionesAlert');

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

    function renderCondiciones() {
        if (!state.condiciones.length) {
            tablaCondiciones.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <p class="mb-0">No hay condiciones registradas. Haz clic en "Nueva Condición" para agregar una.</p>
                    </td>
                </tr>`;
            return;
        }
        tablaCondiciones.innerHTML = state.condiciones.map(cond => `
            <tr>
                <td><span class="badge badge-primary">${cond.codigo}</span></td>
                <td class="font-weight-bold">${cond.titulo}</td>
                <td>${cond.descripcion || '<span class="text-muted">—</span>'}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary mr-1" data-action="edit-condicion" data-id="${cond.id}" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" data-action="delete-condicion" data-id="${cond.id}" title="Eliminar">
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

    function openCondicionModal(condicion = null) {
        state.condicionSeleccionada = condicion;
        condicionIdInput.value = condicion ? condicion.id : '';
        condicionCodigoInput.value = condicion ? condicion.codigo : '';
        condicionTituloInput.value = condicion ? condicion.titulo : '';
        condicionDescripcionInput.value = condicion ? (condicion.descripcion || '') : '';
        document.getElementById('condicionModalLabel').innerHTML = condicion 
            ? '<i class="fas fa-edit mr-2"></i>Editar Condición' 
            : '<i class="fas fa-plus-circle mr-2"></i>Nueva Condición';
        showError(condicionError);
        condicionModalEl.modal('show');
    }

    document.getElementById('btnNuevaCondicion').addEventListener('click', () => openCondicionModal());

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
                showError(condicionError, 'El código y el título son obligatorios.');
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
            setAlert(condicionAlert, data.mensaje || 'Condición guardada correctamente', 'success');
            await fetchCondiciones();
        } catch (error) {
            showError(condicionError, error.message);
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
            setAlert(condicionAlert, data.mensaje || 'Condición eliminada correctamente', 'success');
            await fetchCondiciones();
        } catch (error) {
            showError(condicionDeleteError, error.message);
        }
    });

    fetchCondiciones();
})();
</script>
@endpush