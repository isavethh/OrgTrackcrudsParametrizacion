@extends('layouts.adminlte')

@section('page-title', 'Gestión de Tipos de Vehículo')

@section('page-content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Tipos de Vehículo</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrear">
                    <i class="fas fa-plus"></i> Nuevo Tipo
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tiposTableBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center p-3">
                <div id="paginationInfo" class="text-muted">
                    Mostrando 0 a 0 de 0 registros
                </div>
                <nav>
                    <ul class="pagination mb-0" id="paginationControls">
                        <!-- Controles generados dinámicamente -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Crear -->
    <div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-labelledby="modalCrearLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearLabel">Nuevo Tipo de Vehículo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formCrear">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="crear_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="crear_nombre" name="nombre" required maxlength="50"
                                placeholder="Ej: Camión Articulado">
                        </div>
                        <div class="form-group">
                            <label for="crear_descripcion">Descripción</label>
                            <textarea class="form-control" id="crear_descripcion" name="descripcion" rows="3"
                                maxlength="150"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Tipo de Vehículo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEditar">
                    <input type="hidden" id="editar_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editar_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_nombre" name="nombre" required
                                maxlength="50">
                        </div>
                        <div class="form-group">
                            <label for="editar_descripcion">Descripción</label>
                            <textarea class="form-control" id="editar_descripcion" name="descripcion" rows="3"
                                maxlength="150"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const API_URL = '/api/tipos-vehiculo';
    const _rawToken = localStorage.getItem('authToken');
    const TOKEN = _rawToken ? _rawToken.replace(/^"+|"+$/g, '') : null;

    if (!TOKEN) {
        window.location.href = '/login';
    }

    let tiposCache = [];
    var currentPage = 1;
    const itemsPerPage = 10;
    var filteredData = []; 

    const tablaBody = document.getElementById('tiposTableBody');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');

    function renderTipos(data) {
        filteredData = data; 
        const totalItems = data.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        // Ajustar página actual
        if (currentPage > totalPages) currentPage = totalPages || 1;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, totalItems);

        // Renderizar Info
        if (totalItems === 0) {
            tablaBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No hay registros coincidentes</td></tr>';
            paginationInfo.textContent = 'Mostrando 0 a 0 de 0 registros';
            paginationControls.innerHTML = '';
            return;
        }

        paginationInfo.textContent = `Mostrando ${startIndex + 1} a ${endIndex} de ${totalItems} registros`;

        // Renderizar Filas
        const itemsToShow = data.slice(startIndex, endIndex);
        tablaBody.innerHTML = itemsToShow.map(row => `
            <tr>
                <td>${row.id}</td>
                <td>${row.nombre}</td>
                <td>${row.descripcion || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-info btn-editar" data-id="${row.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        // Renderizar Controles
        let controlsHtml = '';
        controlsHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); changePage(${currentPage - 1})">Anterior</a>
            </li>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                controlsHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="event.preventDefault(); changePage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                    controlsHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        controlsHtml += `
            <li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); changePage(${currentPage + 1})">Siguiente</a>
            </li>
        `;
        paginationControls.innerHTML = controlsHtml;
    }

    function changePage(page) {
        const totalItems = tiposCache.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTipos(tiposCache);
    }

    async function cargarDatos() {
        tablaBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Cargando...</td></tr>';
        try {
            const res = await fetch(API_URL, {
                headers: { 'Authorization': `Bearer ${TOKEN}` }
            });
            
            if (!res.ok) {
                if (res.status === 401) {
                    localStorage.removeItem('authToken');
                    window.location.href = '/login';
                    return;
                }
                throw new Error('No se pudieron cargar los datos');
            }

            const response = await res.json();
            // La API puede devolver array directo o {data: []}
            tiposCache = Array.isArray(response) ? response : (response.data || []);
            
            renderTipos(tiposCache);

        } catch (e) {
            tablaBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">${e.message}</td></tr>`;
            console.error(e);
        }
    }

    async function crearRegistro() {
        const nombre = document.getElementById('crear_nombre').value.trim();
        const descripcion = document.getElementById('crear_descripcion').value.trim();
        
        try {
            const res = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ nombre, descripcion })
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                let mensaje = 'Error al crear el registro';
                if (err && err.errors) mensaje = Object.values(err.errors).join('<br>');
                throw new Error(mensaje);
            }

            $('#modalCrear').modal('hide');
            document.getElementById('formCrear').reset();
            Swal.fire('Éxito', 'Registro creado correctamente', 'success');
            cargarDatos();

        } catch (e) {
            Swal.fire('Error', e.message, 'error');
        }
    }

    async function actualizarRegistro() {
        const id = document.getElementById('editar_id').value;
        const nombre = document.getElementById('editar_nombre').value.trim();
        const descripcion = document.getElementById('editar_descripcion').value.trim();

        try {
            const res = await fetch(`${API_URL}/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ nombre, descripcion })
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                let mensaje = 'Error al actualizar el registro';
                if (err && err.errors) mensaje = Object.values(err.errors).join('<br>');
                throw new Error(mensaje);
            }

            $('#modalEditar').modal('hide');
            Swal.fire('Éxito', 'Registro actualizado correctamente', 'success');
            cargarDatos();

        } catch (e) {
            Swal.fire('Error', e.message, 'error');
        }
    }

    async function eliminarRegistro(id) {
        const result = await Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                const res = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${TOKEN}` }
                });

                if (!res.ok) throw new Error('Error al eliminar el registro');

                Swal.fire('Eliminado', 'Registro eliminado correctamente', 'success');
                cargarDatos();

            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
    }

    function editarRegistro(id) {
        const tipo = tiposCache.find(t => t.id == id);
        if (tipo) {
            $('#editar_id').val(tipo.id);
            $('#editar_nombre').val(tipo.nombre);
            $('#editar_descripcion').val(tipo.descripcion);
            $('#modalEditar').modal('show');
        } else {
            Swal.fire('Error', 'No se pudo cargar el registro', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        cargarDatos();

        document.getElementById('formCrear').addEventListener('submit', function(e) {
            e.preventDefault();
            crearRegistro();
        });

        document.getElementById('formEditar').addEventListener('submit', function(e) {
            e.preventDefault();
            actualizarRegistro();
        });

        tablaBody.addEventListener('click', (e) => {
            const btnEdit = e.target.closest('.btn-editar');
            if (btnEdit) {
                editarRegistro(btnEdit.dataset.id);
                return;
            }
            const btnDel = e.target.closest('.btn-eliminar');
            if (btnDel) {
                eliminarRegistro(btnDel.dataset.id);
            }
        });
    });
</script>
@endpush