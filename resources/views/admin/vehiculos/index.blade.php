@extends('layouts.adminlte')

@section('page-title', 'Vehículos')

@section('page-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Listado de Vehículos</h3>
        <button class="btn btn-primary btn-sm" id="btnNuevoVehiculo">
            <i class="fas fa-plus"></i> Nuevo Vehículo
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Placa</th>
                        <th>Tipo Vehículo</th>
                        <th>Tipo Transporte</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th style="width: 160px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="vehiculosTableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Cargando vehículos...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal crear/editar -->
<div class="modal fade" id="vehiculoModal" tabindex="-1" role="dialog" aria-labelledby="vehiculoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="vehiculoForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="vehiculoModalLabel">Nuevo vehículo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vehiculoIdField">
                    <div class="form-group">
                        <label for="vehiculoPlaca">Placa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vehiculoPlaca" required maxlength="20">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="vehiculoTipoVehiculo">Tipo de Vehículo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="vehiculoTipoVehiculo" required>
                                    <option value="">Cargando...</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnAddTipoVehiculo" title="Crear tipo de vehículo">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Si no existe el tipo, créalo con el botón +.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="vehiculoTipoTransporte">Tipo de Transporte <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="vehiculoTipoTransporte" required>
                                    <option value="">Cargando...</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnAddTipoTransporte" title="Crear tipo de transporte">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Puedes registrar un tipo nuevo con el botón +.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehiculoCapacidad">Capacidad (kg) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="vehiculoCapacidad" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="vehiculoEstado">Estado</label>
                        <input type="text" class="form-control" id="vehiculoEstado" value="Disponible" disabled>
                        <small class="form-text text-muted">El estado se gestiona automáticamente.</small>
                    </div>
                    <div class="alert alert-danger d-none" id="vehiculoError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarVehiculo">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal eliminar -->
<div class="modal fade" id="vehiculoDeleteModal" tabindex="-1" role="dialog" aria-labelledby="vehiculoDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehiculoDeleteLabel">Eliminar vehículo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Seguro que deseas eliminar el vehículo <strong id="vehiculoDeletePlaca"></strong>?
                <div class="alert alert-danger d-none mt-3" id="vehiculoDeleteError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteVehiculo">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal nuevo tipo de vehículo -->
<div class="modal fade" id="tipoVehiculoModal" tabindex="-1" role="dialog" aria-labelledby="tipoVehiculoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formTipoVehiculo">
                <div class="modal-header">
                    <h5 class="modal-title" id="tipoVehiculoModalLabel">Nuevo tipo de vehículo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nuevoTipoVehiculoNombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nuevoTipoVehiculoNombre" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="nuevoTipoVehiculoDescripcion">Descripción</label>
                        <textarea class="form-control" id="nuevoTipoVehiculoDescripcion" rows="2" maxlength="150"></textarea>
                    </div>
                    <div class="alert alert-danger d-none" id="tipoVehiculoError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarTipoVehiculo">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal nuevo tipo de transporte -->
<div class="modal fade" id="tipoTransporteModal" tabindex="-1" role="dialog" aria-labelledby="tipoTransporteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formTipoTransporte">
                <div class="modal-header">
                    <h5 class="modal-title" id="tipoTransporteModalLabel">Nuevo tipo de transporte</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nuevoTipoTransporteNombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nuevoTipoTransporteNombre" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="nuevoTipoTransporteDescripcion">Descripción</label>
                        <textarea class="form-control" id="nuevoTipoTransporteDescripcion" rows="2" maxlength="255"></textarea>
                    </div>
                    <div class="alert alert-danger d-none" id="tipoTransporteError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarTipoTransporte">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const _rawTokenVehiculo = localStorage.getItem('authToken');
    const authToken = _rawTokenVehiculo ? _rawTokenVehiculo.replace(/^"+|"+$/g, '') : null;
    if (!authToken) {
        window.location.href = '/login';
    }

    const tablaBody = document.getElementById('vehiculosTableBody');
    const btnNuevoVehiculo = document.getElementById('btnNuevoVehiculo');
    const formVehiculo = document.getElementById('vehiculoForm');
    const modalVehiculo = $('#vehiculoModal');
    const modalDelete = $('#vehiculoDeleteModal');
    const errorForm = document.getElementById('vehiculoError');
    const deleteError = document.getElementById('vehiculoDeleteError');
    const deletePlaca = document.getElementById('vehiculoDeletePlaca');
    const btnDeleteConfirm = document.getElementById('btnConfirmDeleteVehiculo');

    const fieldId = document.getElementById('vehiculoIdField');
    const fieldPlaca = document.getElementById('vehiculoPlaca');
    const fieldTipoVehiculo = document.getElementById('vehiculoTipoVehiculo');
    const fieldTipoTransporte = document.getElementById('vehiculoTipoTransporte');
    const fieldCapacidad = document.getElementById('vehiculoCapacidad');
    const fieldEstado = document.getElementById('vehiculoEstado');
    const btnGuardarVehiculo = document.getElementById('btnGuardarVehiculo');
    const btnAddTipoVehiculo = document.getElementById('btnAddTipoVehiculo');
    const btnAddTipoTransporte = document.getElementById('btnAddTipoTransporte');

    const modalTipoVehiculo = $('#tipoVehiculoModal');
    const modalTipoTransporte = $('#tipoTransporteModal');
    const formTipoVehiculo = document.getElementById('formTipoVehiculo');
    const formTipoTransporte = document.getElementById('formTipoTransporte');
    const tipoVehiculoNombre = document.getElementById('nuevoTipoVehiculoNombre');
    const tipoVehiculoDescripcion = document.getElementById('nuevoTipoVehiculoDescripcion');
    const tipoTransporteNombre = document.getElementById('nuevoTipoTransporteNombre');
    const tipoTransporteDescripcion = document.getElementById('nuevoTipoTransporteDescripcion');
    const tipoVehiculoError = document.getElementById('tipoVehiculoError');
    const tipoTransporteError = document.getElementById('tipoTransporteError');
    const btnGuardarTipoVehiculo = document.getElementById('btnGuardarTipoVehiculo');
    const btnGuardarTipoTransporte = document.getElementById('btnGuardarTipoTransporte');

    let idVehiculoDelete = null;
    let tiposVehiculoCache = [];
    let tiposTransporteCache = [];

    // Cargar tipos de vehículo desde API
    async function cargarTiposVehiculo() {
        try {
            const res = await fetch(`${window.location.origin}/api/tipos-vehiculo`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
            if (res.status === 401) {
                localStorage.removeItem('authToken');
                window.location.href = '/login';
                return;
            }
            if (!res.ok) throw new Error('No se pudieron cargar los tipos de vehículo');
            tiposVehiculoCache = await res.json();
            if (!Array.isArray(tiposVehiculoCache) || tiposVehiculoCache.length === 0) {
                fieldTipoVehiculo.innerHTML = '<option value="">No hay tipos registrados. Usa el botón +</option>';
                return;
            }
            fieldTipoVehiculo.innerHTML = '<option value="">Selecciona...</option>';
            tiposVehiculoCache.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.nombre;
                fieldTipoVehiculo.appendChild(opt);
            });
        } catch (e) {
            fieldTipoVehiculo.innerHTML = `<option value="">Error: ${e.message}</option>`;
        }
    }

    // Cargar tipos de transporte desde API
    async function cargarTiposTransporte() {
        try {
            const res = await fetch(`${window.location.origin}/api/tipotransporte`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
            if (res.status === 401) {
                localStorage.removeItem('authToken');
                window.location.href = '/login';
                return;
            }
            if (!res.ok) throw new Error('No se pudieron cargar los tipos de transporte');
            tiposTransporteCache = await res.json();
            if (!Array.isArray(tiposTransporteCache) || tiposTransporteCache.length === 0) {
                fieldTipoTransporte.innerHTML = '<option value="">No hay tipos registrados. Usa el botón +</option>';
                return;
            }
            fieldTipoTransporte.innerHTML = '<option value="">Selecciona...</option>';
            tiposTransporteCache.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.nombre;
                fieldTipoTransporte.appendChild(opt);
            });
        } catch (e) {
            fieldTipoTransporte.innerHTML = `<option value="">Error: ${e.message}</option>`;
        }
    }

    // Cargar ambos al iniciar
    cargarTiposVehiculo();
    cargarTiposTransporte();

    function badgeEstado(estado){
        const clases = {
            'Disponible':'badge-success',
            'No Disponible':'badge-secondary',
            'En ruta':'badge-info',
            'Mantenimiento':'badge-warning'
        };
        const cls = clases[estado] || 'badge-light';
        return `<span class="badge ${cls}">${estado || '—'}</span>`;
    }

    async function cargarVehiculos(){
        tablaBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Cargando vehículos...</td></tr>`;
        try{
            const res = await fetch(`${window.location.origin}/api/vehiculos`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
            if (!res.ok){
                if (res.status === 401){ localStorage.removeItem('authToken'); window.location.href = '/login'; return; }
                throw new Error('No se pudieron cargar los vehículos');
            }
            const vehiculos = await res.json();
            if (!Array.isArray(vehiculos) || vehiculos.length === 0){
                tablaBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">No hay vehículos registrados.</td></tr>`;
                return;
            }
            tablaBody.innerHTML = vehiculos.map(v => {
                const disabled = v.estado === 'En ruta' ? 'disabled' : '';
                return `
                <tr>
                    <td>${v.id}</td>
                    <td>${v.placa}</td>
                    <td>${v.tipo}</td>
                    <td>${v.tipo_transporte?.nombre || '—'}</td>
                    <td>${v.capacidad ?? '—'}</td>
                    <td>${badgeEstado(v.estado)}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-warning btn-editar" data-id="${v.id}" ${disabled}><i class="fas fa-edit"></i></button>
                            <button class="btn btn-outline-danger btn-eliminar" data-id="${v.id}" data-placa="${v.placa}" ${disabled}><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');
        } catch (e){
            tablaBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${e.message}</td></tr>`;
        }
    }

    async function abrirModalCrear(){
        formVehiculo.reset();
        fieldId.value = '';
        fieldEstado.value = 'Disponible';
        fieldTipoVehiculo.value = '';
        fieldTipoTransporte.value = '';
        errorForm.classList.add('d-none');
        // Asegurar que los tipos estén cargados
        if (tiposVehiculoCache.length === 0) await cargarTiposVehiculo();
        if (tiposTransporteCache.length === 0) await cargarTiposTransporte();
        document.getElementById('vehiculoModalLabel').innerText = 'Nuevo vehículo';
        modalVehiculo.modal('show');
    }

    async function guardarVehiculo(e){
        e.preventDefault();
        errorForm.classList.add('d-none');
        const id = fieldId.value;
        
        const placa = fieldPlaca.value.trim();
        const idTipoVehiculo = fieldTipoVehiculo.value;
        const idTipoTransporte = fieldTipoTransporte.value;
        const capacidad = fieldCapacidad.value ? Number(fieldCapacidad.value) : null;
        
        if (!placa || !idTipoVehiculo || !idTipoTransporte || capacidad === null){
            errorForm.textContent = 'Completa todos los campos requeridos.';
            errorForm.classList.remove('d-none');
            return;
        }
        
        const payload = {
            placa: placa,
            id_tipo_vehiculo: Number(idTipoVehiculo),
            id_tipo_transporte: Number(idTipoTransporte),
            capacidad: capacidad
        };
        
        btnGuardarVehiculo.disabled = true;
        btnGuardarVehiculo.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';
        try{
            const res = await fetch(`${window.location.origin}/api/vehiculos${id ? '/' + id : ''}`, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            if (!res.ok){
                const err = await res.json().catch(()=>({}));
                throw new Error(err.error || (err.mensaje ?? (err.message ? JSON.stringify(err.message) : 'No se pudo guardar el vehículo')));
            }
            modalVehiculo.modal('hide');
            await cargarVehiculos();
        } catch(e){
            errorForm.textContent = e.message;
            errorForm.classList.remove('d-none');
        } finally {
            btnGuardarVehiculo.disabled = false;
            btnGuardarVehiculo.innerHTML = 'Guardar';
        }
    }

    async function abrirModalEditar(id, fila){
        try {
            const res = await fetch(`${window.location.origin}/api/vehiculos/${id}`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
            if (!res.ok) throw new Error('No se pudo cargar el vehículo');
            const vehiculo = await res.json();
            
            fieldId.value = id;
            fieldPlaca.value = vehiculo.placa || '';
            fieldCapacidad.value = vehiculo.capacidad || '';
            fieldEstado.value = vehiculo.estado || 'Disponible';
            
            // Buscar el tipo de vehículo por nombre
            if (vehiculo.tipo) {
                const tipoVehiculo = tiposVehiculoCache.find(t => t.nombre === vehiculo.tipo);
                if (tipoVehiculo) fieldTipoVehiculo.value = tipoVehiculo.id;
            }
            
            // Buscar el tipo de transporte
            if (vehiculo.tipo_transporte && vehiculo.tipo_transporte.id) {
                fieldTipoTransporte.value = vehiculo.tipo_transporte.id;
            }
            
            errorForm.classList.add('d-none');
            document.getElementById('vehiculoModalLabel').innerText = 'Editar vehículo';
            modalVehiculo.modal('show');
        } catch (e) {
            alert('Error al cargar el vehículo: ' + e.message);
        }
    }

    function abrirModalEliminar(id, placa){
        idVehiculoDelete = id;
        deletePlaca.textContent = placa;
        deleteError.classList.add('d-none');
        modalDelete.modal('show');
    }

    async function eliminarVehiculo(){
        if (!idVehiculoDelete) return;
        btnDeleteConfirm.disabled = true;
        btnDeleteConfirm.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Eliminando...';
        try{
            const res = await fetch(`${window.location.origin}/api/vehiculos/${idVehiculoDelete}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
            if (!res.ok){
                const err = await res.json().catch(()=>({}));
                throw new Error(err.error || 'No se pudo eliminar el vehículo');
            }
            modalDelete.modal('hide');
            await cargarVehiculos();
        } catch(e){
            deleteError.textContent = e.message;
            deleteError.classList.remove('d-none');
        } finally {
            btnDeleteConfirm.disabled = false;
            btnDeleteConfirm.innerHTML = 'Eliminar';
        }
    }

    btnNuevoVehiculo.addEventListener('click', abrirModalCrear);
    formVehiculo.addEventListener('submit', guardarVehiculo);
    btnDeleteConfirm.addEventListener('click', eliminarVehiculo);

    function abrirModalTipoVehiculo(){
        formTipoVehiculo.reset();
        tipoVehiculoError.classList.add('d-none');
        btnGuardarTipoVehiculo.disabled = false;
        btnGuardarTipoVehiculo.innerHTML = 'Guardar';
        modalTipoVehiculo.modal('show');
    }

    function abrirModalTipoTransporte(){
        formTipoTransporte.reset();
        tipoTransporteError.classList.add('d-none');
        btnGuardarTipoTransporte.disabled = false;
        btnGuardarTipoTransporte.innerHTML = 'Guardar';
        modalTipoTransporte.modal('show');
    }

    async function guardarTipoVehiculo(e){
        e.preventDefault();
        const nombre = tipoVehiculoNombre.value.trim();
        if (!nombre){
            tipoVehiculoError.textContent = 'Ingresa un nombre.';
            tipoVehiculoError.classList.remove('d-none');
            return;
        }
        tipoVehiculoError.classList.add('d-none');
        btnGuardarTipoVehiculo.disabled = true;
        btnGuardarTipoVehiculo.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';
        try{
            const res = await fetch(`${window.location.origin}/api/tipos-vehiculo`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nombre,
                    descripcion: tipoVehiculoDescripcion.value.trim() || null
                })
            });
            if (!res.ok){
                const err = await res.json().catch(()=>({}));
                throw new Error(err.error || 'No se pudo crear el tipo de vehículo');
            }
            const data = await res.json();
            modalTipoVehiculo.modal('hide');
            await cargarTiposVehiculo();
            if (data?.data?.id){
                fieldTipoVehiculo.value = data.data.id;
            }
        } catch (e){
            tipoVehiculoError.textContent = e.message;
            tipoVehiculoError.classList.remove('d-none');
        } finally {
            btnGuardarTipoVehiculo.disabled = false;
            btnGuardarTipoVehiculo.innerHTML = 'Guardar';
        }
    }

    async function guardarTipoTransporte(e){
        e.preventDefault();
        const nombre = tipoTransporteNombre.value.trim();
        if (!nombre){
            tipoTransporteError.textContent = 'Ingresa un nombre.';
            tipoTransporteError.classList.remove('d-none');
            return;
        }
        tipoTransporteError.classList.add('d-none');
        btnGuardarTipoTransporte.disabled = true;
        btnGuardarTipoTransporte.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';
        try{
            const res = await fetch(`${window.location.origin}/api/tipotransporte`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nombre,
                    descripcion: tipoTransporteDescripcion.value.trim() || null
                })
            });
            if (!res.ok){
                const err = await res.json().catch(()=>({}));
                throw new Error(err.error || 'No se pudo crear el tipo de transporte');
            }
            const data = await res.json();
            modalTipoTransporte.modal('hide');
            await cargarTiposTransporte();
            if (data?.data?.id){
                fieldTipoTransporte.value = data.data.id;
            }
        } catch (e){
            tipoTransporteError.textContent = e.message;
            tipoTransporteError.classList.remove('d-none');
        } finally {
            btnGuardarTipoTransporte.disabled = false;
            btnGuardarTipoTransporte.innerHTML = 'Guardar';
        }
    }

    btnAddTipoVehiculo.addEventListener('click', abrirModalTipoVehiculo);
    btnAddTipoTransporte.addEventListener('click', abrirModalTipoTransporte);
    formTipoVehiculo.addEventListener('submit', guardarTipoVehiculo);
    formTipoTransporte.addEventListener('submit', guardarTipoTransporte);

    tablaBody.addEventListener('click', (e)=>{
        const btnEdit = e.target.closest('.btn-editar');
        if (btnEdit){
            const id = btnEdit.dataset.id;
            const fila = btnEdit.closest('tr');
            abrirModalEditar(id, fila);
            return;
        }
        const btnDel = e.target.closest('.btn-eliminar');
        if (btnDel){
            abrirModalEliminar(btnDel.dataset.id, btnDel.dataset.placa);
        }
    });

    cargarVehiculos();
</script>
@endpush