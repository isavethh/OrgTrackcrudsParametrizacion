@extends('layouts.adminlte')

@section('page-title', 'Transportistas')

@section('page-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Listado de Transportistas</h3>
        <button class="btn btn-primary btn-sm" id="btnNuevoTransportista">
            <i class="fas fa-plus"></i> Nuevo Transportista
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Nombre</th>
                        <th>CI</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th style="width: 160px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="transportistasTableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Cargando transportistas...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear / Editar -->
<div class="modal fade" id="transportistaModal" tabindex="-1" role="dialog" aria-labelledby="transportistaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="transportistaForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="transportistaModalLabel">Nuevo transportista</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="transportistaIdField">

                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="chkCrearUsuarioNuevo">
                        <label class="form-check-label" for="chkCrearUsuarioNuevo">Crear nuevo usuario</label>
                    </div>

                    <div id="usuarioExistenteWrapper" class="form-group">
                        <label for="selectUsuarioExistente">Seleccionar usuario registrado</label>
                        <select class="form-control" id="selectUsuarioExistente"></select>
                        <small class="form-text text-muted">Solo se muestran usuarios que aún no son transportistas.</small>
                    </div>

                    <div id="usuarioNuevoWrapper" class="border rounded p-3 mb-3 d-none">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="usuarioNombre">Nombre</label>
                                <input type="text" class="form-control" id="usuarioNombre">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="usuarioApellido">Apellido</label>
                                <input type="text" class="form-control" id="usuarioApellido">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="usuarioCorreo">Correo</label>
                            <input type="email" class="form-control" id="usuarioCorreo">
                        </div>
                        <div class="form-group">
                            <label for="usuarioContrasena">Contraseña</label>
                            <input type="password" class="form-control" id="usuarioContrasena">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="transportistaNombreResumen">Nombre completo</label>
                        <input type="text" class="form-control" id="transportistaNombreResumen" disabled>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="transportistaCI">CI</label>
                            <input type="text" class="form-control" id="transportistaCI" required maxlength="20">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="transportistaTelefono">Teléfono</label>
                            <input type="text" class="form-control" id="transportistaTelefono" required maxlength="20">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="transportistaEstado">Estado</label>
                        <input type="text" class="form-control" id="transportistaEstado" value="Disponible" disabled>
                        <small class="form-text text-muted">El estado se gestiona automáticamente.</small>
                    </div>

                    <div class="alert alert-danger d-none" id="transportistaError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarTransportista">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal eliminar -->
<div class="modal fade" id="transportistaDeleteModal" tabindex="-1" role="dialog" aria-labelledby="transportistaDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transportistaDeleteLabel">Eliminar transportista</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Seguro que deseas eliminar al transportista <strong id="transportistaDeleteNombre"></strong>?
                <div class="alert alert-danger d-none mt-3" id="transportistaDeleteError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteTransportista">Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const tokenRaw = localStorage.getItem('authToken');
    const authToken = tokenRaw ? tokenRaw.replace(/^"+|"+$/g, '') : null;
    if (!authToken) { window.location.href = '/login'; }

    const tablaBody = document.getElementById('transportistasTableBody');
    const btnNuevo = document.getElementById('btnNuevoTransportista');
    const formTransportista = document.getElementById('transportistaForm');
    const modalTransportista = $('#transportistaModal');
    const modalEliminar = $('#transportistaDeleteModal');

    const fieldId = document.getElementById('transportistaIdField');
    const chkNuevoUsuario = document.getElementById('chkCrearUsuarioNuevo');
    const wrapperUsuarioExistente = document.getElementById('usuarioExistenteWrapper');
    const wrapperUsuarioNuevo = document.getElementById('usuarioNuevoWrapper');
    const selectUsuario = document.getElementById('selectUsuarioExistente');
    const fieldNombre = document.getElementById('usuarioNombre');
    const fieldApellido = document.getElementById('usuarioApellido');
    const fieldCorreo = document.getElementById('usuarioCorreo');
    const fieldContrasena = document.getElementById('usuarioContrasena');

    const fieldResumenNombre = document.getElementById('transportistaNombreResumen');
    const fieldCI = document.getElementById('transportistaCI');
    const fieldTelefono = document.getElementById('transportistaTelefono');
    const fieldEstado = document.getElementById('transportistaEstado');
    const errorForm = document.getElementById('transportistaError');
    const btnGuardar = document.getElementById('btnGuardarTransportista');

    const deleteNombre = document.getElementById('transportistaDeleteNombre');
    const deleteError = document.getElementById('transportistaDeleteError');
    const btnDeleteConfirm = document.getElementById('btnConfirmDeleteTransportista');

    let idTransportistaDelete = null;
    let transportistasCache = [];
    let clientesDisponibles = [];

    const badgeEstado = (estado) => {
        const clases = {
            'Disponible': 'badge-success',
            'No Disponible': 'badge-secondary',
            'En ruta': 'badge-info',
            'Inactivo': 'badge-warning'
        };
        return `<span class="badge ${clases[estado] || 'badge-light'}">${estado}</span>`;
    };

    function actualizarResumenNombre() {
        if (chkNuevoUsuario.checked) {
            fieldResumenNombre.value = `${fieldNombre.value.trim()} ${fieldApellido.value.trim()}`.trim();
        } else {
            const selected = selectUsuario.options[selectUsuario.selectedIndex];
            fieldResumenNombre.value = selected ? selected.textContent : '';
        }
    }

    chkNuevoUsuario.addEventListener('change', () => {
        if (chkNuevoUsuario.checked) {
            wrapperUsuarioNuevo.classList.remove('d-none');
            wrapperUsuarioExistente.classList.add('d-none');
            selectUsuario.value = '';
        } else {
            wrapperUsuarioNuevo.classList.add('d-none');
            wrapperUsuarioExistente.classList.remove('d-none');
        }
        actualizarResumenNombre();
    });

    [selectUsuario, fieldNombre, fieldApellido].forEach(el => {
        el.addEventListener('input', actualizarResumenNombre);
        el.addEventListener('change', actualizarResumenNombre);
    });

    async function cargarClientesDisponibles(){
        try{
            const res = await fetch(`${window.location.origin}/api/usuarios/rol/cliente`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
            if (!res.ok) throw new Error('No se pudieron cargar los usuarios disponibles');
            const usuarios = await res.json();
            clientesDisponibles = usuarios.filter(u => !transportistasCache.some(t => t.id_usuario === u.id));
            selectUsuario.innerHTML = '<option value="">Selecciona un usuario...</option>';
            clientesDisponibles.forEach(u => {
                const opt = document.createElement('option');
                opt.value = u.id;
                opt.textContent = `${u.nombre} ${u.apellido} (${u.correo || ''})`;
                selectUsuario.appendChild(opt);
            });
        } catch(e){
            selectUsuario.innerHTML = `<option value="">${e.message}</option>`;
        }
    }

    async function cargarTransportistas(){
        tablaBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Cargando transportistas...</td></tr>`;
        try{
            const res = await fetch(`${window.location.origin}/api/transportistas`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
            if (!res.ok){
                if (res.status === 401){ localStorage.removeItem('authToken'); window.location.href = '/login'; return; }
                throw new Error('No se pudieron cargar los transportistas');
            }
            const datos = await res.json();
            transportistasCache = Array.isArray(datos) ? datos : [];
            if (transportistasCache.length === 0){
                tablaBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No hay transportistas registrados.</td></tr>`;
                return;
            }
            tablaBody.innerHTML = transportistasCache.map(t => {
                const nombreCompleto = `${t.nombre || ''} ${t.apellido || ''}`.trim() || '—';
                const disabled = t.estado === 'En ruta' ? 'disabled' : '';
                return `
                    <tr data-id="${t.id}">
                        <td>${t.id}</td>
                        <td>${nombreCompleto}</td>
                        <td>${t.ci || '—'}</td>
                        <td>${t.telefono || '—'}</td>
                        <td>${badgeEstado(t.estado)}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-warning btn-editar" data-id="${t.id}" ${disabled}><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger btn-eliminar" data-id="${t.id}" data-nombre="${nombreCompleto}" ${disabled}><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        } catch(e){
            tablaBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${e.message}</td></tr>`;
        }
    }

    function limpiarModal(){
        formTransportista.reset();
        fieldId.value = '';
        fieldEstado.value = 'Disponible';
        chkNuevoUsuario.checked = false;
        wrapperUsuarioNuevo.classList.add('d-none');
        wrapperUsuarioExistente.classList.remove('d-none');
        errorForm.classList.add('d-none');
        fieldResumenNombre.value = '';
        fieldContrasena.value = '';
    }

    async function abrirModalCrear(){
        limpiarModal();
        await cargarClientesDisponibles();
        actualizarResumenNombre();
        document.getElementById('transportistaModalLabel').innerText = 'Nuevo transportista';
        modalTransportista.modal('show');
    }

    function abrirModalEditar(id){
        const t = transportistasCache.find(item => item.id === Number(id));
        if (!t) return;
        limpiarModal();
        fieldId.value = t.id;
        chkNuevoUsuario.checked = false;
        wrapperUsuarioNuevo.classList.add('d-none');
        wrapperUsuarioExistente.classList.add('d-none');
        fieldResumenNombre.value = `${t.nombre || ''} ${t.apellido || ''}`.trim();
        fieldCI.value = t.ci || '';
        fieldTelefono.value = t.telefono || '';
        fieldEstado.value = t.estado || 'Disponible';
        document.getElementById('transportistaModalLabel').innerText = 'Editar transportista';
        modalTransportista.modal('show');
    }

    async function guardarTransportista(e){
        e.preventDefault();
        errorForm.classList.add('d-none');

        const id = fieldId.value;
        const ciValue = fieldCI.value.trim();
        const telefonoValue = fieldTelefono.value.trim();

        let url = `${window.location.origin}/api/transportistas`;
        let method = 'POST';
        let payload = {};

        if (id){
            if (!ciValue || !telefonoValue){
                errorForm.textContent = 'Completa los campos de CI y teléfono.';
                errorForm.classList.remove('d-none');
                return;
            }
            payload = { ci: ciValue, telefono: telefonoValue };
            url += `/${id}`;
            method = 'PUT';
        } else if (chkNuevoUsuario.checked){
            if (!fieldNombre.value.trim() || !fieldApellido.value.trim() || !fieldCorreo.value.trim() || !fieldContrasena.value.trim() || !ciValue){
                errorForm.textContent = 'Completa los datos del nuevo usuario (nombre, apellido, correo, contraseña, CI).';
                errorForm.classList.remove('d-none');
                return;
            }
            payload = {
                usuario: {
                    nombre: fieldNombre.value.trim(),
                    apellido: fieldApellido.value.trim(),
                    correo: fieldCorreo.value.trim(),
                    contrasena: fieldContrasena.value.trim(),
                    ci: ciValue,
                    telefono: telefonoValue || null
                }
            };
            url += '/completo';
        } else {
            if (!selectUsuario.value){
                errorForm.textContent = 'Selecciona un usuario existente.';
                errorForm.classList.remove('d-none');
                return;
            }
            payload = { id_usuario: Number(selectUsuario.value) };
        }

        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

        try{
            const res = await fetch(url, {
                method,
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            if (!res.ok){
                const err = await res.json().catch(()=>({}));
                throw new Error(err.error || (err.mensaje ?? 'No se pudo guardar el transportista'));
            }
            modalTransportista.modal('hide');
            await cargarTransportistas();
        } catch(e){
            errorForm.textContent = e.message;
            errorForm.classList.remove('d-none');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = 'Guardar';
        }
    }

    function abrirModalEliminar(id, nombre){
        idTransportistaDelete = id;
        deleteNombre.textContent = nombre;
        deleteError.classList.add('d-none');
        modalEliminar.modal('show');
    }

    async function eliminarTransportista(){
        if (!idTransportistaDelete) return;
        btnDeleteConfirm.disabled = true;
        btnDeleteConfirm.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Eliminando...';
        try{
            const res = await fetch(`${window.location.origin}/api/transportistas/${idTransportistaDelete}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
            if (!res.ok){
                const err = await res.json().catch(()=>({}));
                throw new Error(err.error || 'No se pudo eliminar el transportista');
            }
            modalEliminar.modal('hide');
            await cargarTransportistas();
        } catch(e){
            deleteError.textContent = e.message;
            deleteError.classList.remove('d-none');
        } finally {
            btnDeleteConfirm.disabled = false;
            btnDeleteConfirm.innerHTML = 'Eliminar';
        }
    }

    tablaBody.addEventListener('click', (e)=>{
        const btnEdit = e.target.closest('.btn-editar');
        if (btnEdit){
            abrirModalEditar(btnEdit.dataset.id);
            return;
        }
        const btnDel = e.target.closest('.btn-eliminar');
        if (btnDel){
            abrirModalEliminar(btnDel.dataset.id, btnDel.dataset.nombre);
        }
    });

    btnNuevo.addEventListener('click', abrirModalCrear);
    formTransportista.addEventListener('submit', guardarTransportista);
    btnDeleteConfirm.addEventListener('click', eliminarTransportista);

    cargarTransportistas();
</script>
@endpush