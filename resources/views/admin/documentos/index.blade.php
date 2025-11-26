@extends('layouts.admin')

@section('title', 'Documentos de Clientes - OrgTrack')
@section('page-title', 'Documentos de Clientes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Documentos</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1"><i class="fas fa-file-alt mr-2"></i>Lista de Clientes</h3>
                        <p class="text-muted mb-0 small">Aquí podrás ver los documentos generados por cada cliente</p>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div id="clientesAlert" class="alert d-none"></div>
                
                <!-- Buscador -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input 
                                type="text" 
                                id="searchCliente" 
                                class="form-control" 
                                placeholder="Buscar cliente por nombre o correo...">
                            <div class="input-group-append">
                                <button class="btn btn-default" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de clientes -->
                <div id="loadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando clientes...</p>
                </div>
                
                <div id="clientesContainer" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>CI</th>
                                    <th class="text-center" style="width: 180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaClientes">
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div id="noClientes" style="display: none;" class="text-center py-5 text-muted">
                    <i class="fas fa-users-slash fa-3x mb-3"></i>
                    <p>No hay clientes registrados en el sistema.</p>
                </div>
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

    const headers = { 
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    };

    const state = { clientes: [], clientesFiltrados: [] };
    const loadingSpinner = document.getElementById('loadingSpinner');
    const clientesContainer = document.getElementById('clientesContainer');
    const noClientes = document.getElementById('noClientes');
    const tablaClientes = document.getElementById('tablaClientes');
    const searchCliente = document.getElementById('searchCliente');
    const clientesAlert = document.getElementById('clientesAlert');

    function setAlert(message = '', type = 'success') {
        if (!message) {
            clientesAlert.classList.add('d-none');
            clientesAlert.textContent = '';
            return;
        }
        clientesAlert.className = `alert alert-${type}`;
        clientesAlert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
        setTimeout(() => setAlert(), 5000);
    }

    function renderClientes(clientes) {
        if (!clientes || clientes.length === 0) {
            loadingSpinner.style.display = 'none';
            noClientes.style.display = 'block';
            clientesContainer.style.display = 'none';
            return;
        }

        tablaClientes.innerHTML = clientes.map(cliente => `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" 
                             style="width: 40px; height: 40px; font-size: 18px;">
                            ${(cliente.nombre || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="font-weight-bold">${cliente.nombre || '—'} ${cliente.apellido || ''}</div>
                            <small class="text-muted">ID: ${cliente.id}</small>
                        </div>
                    </div>
                </td>
                <td>${cliente.correo || '—'}</td>
                <td>${cliente.telefono || '—'}</td>
                <td>${cliente.ci || '—'}</td>
                <td class="text-center">
                    <a href="/admin/documentos/cliente/${cliente.id}" 
                       class="btn btn-sm btn-primary" 
                       title="Ver documentos de este cliente">
                        <i class="fas fa-file-alt mr-1"></i>Ver Documentos
                    </a>
                </td>
            </tr>
        `).join('');

        loadingSpinner.style.display = 'none';
        noClientes.style.display = 'none';
        clientesContainer.style.display = 'block';
    }

    function filtrarClientes() {
        const query = searchCliente.value.toLowerCase().trim();
        if (!query) {
            state.clientesFiltrados = state.clientes;
        } else {
            state.clientesFiltrados = state.clientes.filter(c => {
                const nombre = `${c.nombre || ''} ${c.apellido || ''}`.toLowerCase();
                const correo = (c.correo || '').toLowerCase();
                return nombre.includes(query) || correo.includes(query);
            });
        }
        renderClientes(state.clientesFiltrados);
    }

    async function cargarClientes() {
        try {
            setAlert();
            const res = await fetch(`${window.location.origin}/api/usuarios/clientes`, { headers });
            
            if (res.status === 401) {
                localStorage.removeItem('authToken');
                localStorage.removeItem('usuario');
                window.location.href = '/login';
                return;
            }

            if (!res.ok) {
                throw new Error('No se pudieron cargar los clientes');
            }

            state.clientes = await res.json();
            state.clientesFiltrados = state.clientes;
            renderClientes(state.clientesFiltrados);
        } catch (error) {
            console.error('Error:', error);
            setAlert(error.message, 'danger');
            loadingSpinner.style.display = 'none';
            noClientes.style.display = 'block';
        }
    }

    // Event listeners
    searchCliente.addEventListener('input', filtrarClientes);

    // Inicializar
    cargarClientes();
})();
</script>
@endsection

