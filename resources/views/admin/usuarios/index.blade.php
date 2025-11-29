@extends('layouts.adminlte')

@section('page-title', 'Gestión de Usuarios')

@section('page-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Usuarios</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevoUsuario">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuario...">
                        </div>
                        <div class="col-md-3">
                            <select id="filterRol" class="form-control">
                                <option value="">Todos los roles</option>
                                <option value="cliente">Cliente</option>
                                <option value="transportista">Transportista</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="loadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>

                <div id="usuariosContainer" style="display: none;">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>CI</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usuariosTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalUsuarioTitle">Nuevo Usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formUsuario">
                    <input type="hidden" id="usuarioId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="apellido" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>CI <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ci" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" id="telefono">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contraseña <span class="text-danger" id="passwordRequired">*</span></label>
                                <input type="password" class="form-control" id="password">
                                <small class="form-text text-muted" id="passwordHelp">
                                    Dejar en blanco para mantener la contraseña actual
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rol <span class="text-danger">*</span></label>
                                <select class="form-control" id="rol" required>
                                    <option value="">Seleccionar rol</option>
                                    <option value="cliente">Cliente</option>
                                    <option value="transportista">Transportista</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Rol -->
<div class="modal fade" id="modalCambiarRol" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">Cambiar Rol de Usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cambiarRolUsuarioId">
                <p>Usuario: <strong id="cambiarRolUsuarioNombre"></strong></p>
                <div class="form-group">
                    <label>Nuevo Rol</label>
                    <select class="form-control" id="nuevoRol">
                        <option value="cliente">Cliente</option>
                        <option value="transportista">Transportista</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnConfirmarCambioRol">
                    Cambiar Rol
                </button>
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

    let usuarios = [];
    let editingUserId = null;

    function badgeForRol(rol) {
        const map = {
            'admin': 'badge-danger',
            'cliente': 'badge-primary',
            'transportista': 'badge-success'
        };
        return `<span class="badge ${map[rol] || 'badge-secondary'}">${rol || 'Sin rol'}</span>`;
    }

    function renderUsuarios(data) {
        const tbody = document.getElementById('usuariosTableBody');
        
        console.log('Usuarios recibidos:', data);
        
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No hay usuarios registrados</td></tr>';
            return;
        }

        tbody.innerHTML = data.map(user => `
            <tr>
                <td>${user.id}</td>
                <td>${user.nombre || '—'} ${user.apellido || ''}</td>
                <td>${user.correo || '—'}</td>
                <td>${badgeForRol(user.rol)}</td>
                <td>${user.ci || '—'}</td>
                <td>${user.telefono || '—'}</td>
                <td>
                    <span class="badge badge-success">Activo</span>
                </td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="editarUsuario(${user.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="abrirCambiarRol(${user.id}, '${user.nombre} ${user.apellido}', '${user.rol}')" title="Cambiar Rol">
                        <i class="fas fa-user-tag"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${user.id})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    async function cargarUsuarios() {
        try {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('usuariosContainer').style.display = 'none';

            const res = await fetch(`${window.location.origin}/api/usuarios`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!res.ok) throw new Error('Error al cargar usuarios');

            usuarios = await res.json();
            
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('usuariosContainer').style.display = 'block';
            
            renderUsuarios(usuarios);

        } catch (e) {
            document.getElementById('loadingSpinner').style.display = 'none';
            alert('Error al cargar usuarios: ' + e.message);
        }
    }

    function filtrarUsuarios() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rolFilter = document.getElementById('filterRol').value;

        const filtrados = usuarios.filter(user => {
            const matchSearch = (user.nombre || '').toLowerCase().includes(searchTerm) ||
                              (user.apellido || '').toLowerCase().includes(searchTerm) ||
                              (user.correo || '').toLowerCase().includes(searchTerm) ||
                              (user.ci || '').toLowerCase().includes(searchTerm);
            
            const matchRol = !rolFilter || user.rol === rolFilter;

            return matchSearch && matchRol;
        });

        renderUsuarios(filtrados);
    }

    document.getElementById('searchInput').addEventListener('input', filtrarUsuarios);
    document.getElementById('filterRol').addEventListener('change', filtrarUsuarios);

    document.getElementById('btnNuevoUsuario').addEventListener('click', function() {
        editingUserId = null;
        document.getElementById('modalUsuarioTitle').textContent = 'Nuevo Usuario';
        document.getElementById('formUsuario').reset();
        document.getElementById('usuarioId').value = '';
        document.getElementById('passwordRequired').style.display = 'inline';
        document.getElementById('passwordHelp').style.display = 'none';
        document.getElementById('password').required = true;
        $('#modalUsuario').modal('show');
    });

    async function editarUsuario(id) {
        try {
            const res = await fetch(`${window.location.origin}/api/usuarios/${id}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!res.ok) throw new Error('Error al cargar usuario');

            const user = await res.json();
            
            console.log('Usuario a editar:', user);
            
            editingUserId = id;
            document.getElementById('modalUsuarioTitle').textContent = 'Editar Usuario';
            document.getElementById('usuarioId').value = id;
            document.getElementById('nombre').value = user.nombre || '';
            document.getElementById('apellido').value = user.apellido || '';
            document.getElementById('ci').value = user.ci || '';
            document.getElementById('telefono').value = user.telefono || '';
            document.getElementById('email').value = user.correo || '';
            document.getElementById('rol').value = user.rol || '';
            document.getElementById('password').value = '';
            document.getElementById('passwordRequired').style.display = 'none';
            document.getElementById('passwordHelp').style.display = 'block';
            document.getElementById('password').required = false;
            
            $('#modalUsuario').modal('show');

        } catch (e) {
            alert('Error al cargar usuario: ' + e.message);
        }
    }

    document.getElementById('btnGuardarUsuario').addEventListener('click', async function() {
        const form = document.getElementById('formUsuario');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const data = {
            nombre: document.getElementById('nombre').value,
            apellido: document.getElementById('apellido').value,
            ci: document.getElementById('ci').value,
            telefono: document.getElementById('telefono').value,
            correo: document.getElementById('email').value,
            rol: document.getElementById('rol').value
        };

        const password = document.getElementById('password').value;
        if (password) {
            data.contrasena = password;
        }

        try {
            let res;
            if (editingUserId) {
                res = await fetch(`${window.location.origin}/api/usuarios/${editingUserId}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
            } else {
                if (!password) {
                    alert('La contraseña es requerida para nuevos usuarios');
                    return;
                }
                res = await fetch(`${window.location.origin}/api/usuarios`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
            }

            if (!res.ok) {
                const error = await res.json();
                throw new Error(error.error || 'Error al guardar usuario');
            }

            $('#modalUsuario').modal('hide');
            alert(editingUserId ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente');
            cargarUsuarios();

        } catch (e) {
            alert('Error: ' + e.message);
        }
    });

    function abrirCambiarRol(id, nombre, rolActual) {
        document.getElementById('cambiarRolUsuarioId').value = id;
        document.getElementById('cambiarRolUsuarioNombre').textContent = nombre;
        document.getElementById('nuevoRol').value = rolActual;
        $('#modalCambiarRol').modal('show');
    }

    document.getElementById('btnConfirmarCambioRol').addEventListener('click', async function() {
        const id = document.getElementById('cambiarRolUsuarioId').value;
        const nuevoRol = document.getElementById('nuevoRol').value;

        try {
            const res = await fetch(`${window.location.origin}/api/usuarios/${id}/cambiar-rol`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ rol: nuevoRol })
            });

            if (!res.ok) {
                const error = await res.json();
                throw new Error(error.error || 'Error al cambiar rol');
            }

            $('#modalCambiarRol').modal('hide');
            alert('Rol cambiado correctamente');
            cargarUsuarios();

        } catch (e) {
            alert('Error: ' + e.message);
        }
    });

    async function eliminarUsuario(id) {
        if (!confirm('¿Está seguro de eliminar este usuario?')) return;

        try {
            const res = await fetch(`${window.location.origin}/api/usuarios/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!res.ok) {
                const error = await res.json();
                throw new Error(error.error || 'Error al eliminar usuario');
            }

            alert('Usuario eliminado correctamente');
            cargarUsuarios();

        } catch (e) {
            alert('Error: ' + e.message);
        }
    }

    cargarUsuarios();
</script>
@endpush