@extends('layouts.app')

@section('page-title', 'Vehículos')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
<li class="breadcrumb-item active">Vehículos</li>
@endsection

@section('content')
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
                        <th>Tipo</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th style="width: 160px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="vehiculosTableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
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
                        <label for="vehiculoPlaca">Placa</label>
                        <input type="text" class="form-control" id="vehiculoPlaca" required maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="vehiculoTipo">Tipo</label>
                        <select class="form-control" id="vehiculoTipo" required></select>
                    </div>
                    <div class="form-group">
                        <label for="vehiculoCapacidad">Capacidad</label>
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
@endsection

@section('scripts')
<script>
    const _rawTokenVehiculo = localStorage.getItem('authToken');
    const authToken = _rawTokenVehiculo ? _rawTokenVehiculo.replace(/^"+|"+$/g, '') : null;
    if (!authToken) {
        window.location.href = '/login';
    }

    const VEHICULO_TIPOS = [
        'Pesado - Ventilado','Pesado - Aislado','Pesado - Refrigerado',
        'Mediano - Ventilado','Mediano - Aislado','Mediano - Refrigerado',
        'Ligero - Ventilado','Ligero - Aislado','Ligero - Refrigerado',
    ];

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
    const fieldTipo = document.getElementById('vehiculoTipo');
    const fieldCapacidad = document.getElementById('vehiculoCapacidad');
    const fieldEstado = document.getElementById('vehiculoEstado');
    const btnGuardarVehiculo = document.getElementById('btnGuardarVehiculo');

    let idVehiculoDelete = null;

    function rellenarSelect(select, opciones) {
        select.innerHTML = '<option value="">Selecciona...</option>';
        opciones.forEach(op => {
            const opt = document.createElement('option');
            opt.value = op;
            opt.textContent = op;
            select.appendChild(opt);
        });
    }

    rellenarSelect(fieldTipo, VEHICULO_TIPOS);

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
                tablaBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No hay vehículos registrados.</td></tr>`;
                return;
            }
            tablaBody.innerHTML = vehiculos.map(v => {
                const disabled = v.estado === 'En ruta' ? 'disabled' : '';
                return `
                <tr>
                    <td>${v.id}</td>
                    <td>${v.placa}</td>
                    <td>${v.tipo}</td>
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
            tablaBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${e.message}</td></tr>`;
        }
    }

    function abrirModalCrear(){
        formVehiculo.reset();
        fieldId.value = '';
        fieldEstado.value = 'Disponible';
        errorForm.classList.add('d-none');
        document.getElementById('vehiculoModalLabel').innerText = 'Nuevo vehículo';
        modalVehiculo.modal('show');
    }

    async function guardarVehiculo(e){
        e.preventDefault();
        errorForm.classList.add('d-none');
        const id = fieldId.value;
        const payload = {
            placa: fieldPlaca.value.trim(),
            tipo: fieldTipo.value,
            capacidad: fieldCapacidad.value ? Number(fieldCapacidad.value) : null
        };
        if (!payload.placa || !payload.tipo || payload.capacidad === null){
            errorForm.textContent = 'Completa todos los campos.';
            errorForm.classList.remove('d-none');
            return;
        }
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

    function abrirModalEditar(id, fila){
        const celdas = fila.children;
        fieldId.value = id;
        fieldPlaca.value = celdas[1].textContent.trim();
        fieldTipo.value = celdas[2].textContent.trim();
        fieldCapacidad.value = celdas[3].textContent.trim();
        fieldEstado.value = fila.querySelector('.badge')?.textContent.trim() || '';
        errorForm.classList.add('d-none');
        document.getElementById('vehiculoModalLabel').innerText = 'Editar vehículo';
        modalVehiculo.modal('show');
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
@endsection

