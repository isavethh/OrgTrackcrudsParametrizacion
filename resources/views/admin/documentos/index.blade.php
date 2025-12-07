@extends('layouts.adminlte')

@section('page-title', 'Documentos')

@section('page-content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="fas fa-file-alt mr-2"></i>Gestión de Documentos</h3>
            </div>
            
            <div class="card-body">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="documentosTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="clientes-tab" data-toggle="tab" href="#clientes" role="tab">
                            <i class="fas fa-users mr-1"></i> Documentos de Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="productores-tab" data-toggle="tab" href="#productores" role="tab">
                            <i class="fas fa-seedling mr-1"></i> Documentos de Productores
                        </a>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="documentosTabContent">
                    <!-- TAB CLIENTES -->
                    <div class="tab-pane fade show active" id="clientes" role="tabpanel">
                        <div id="clientesAlert" class="alert d-none"></div>
                        
                        <!-- Buscador Clientes -->
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
                        <div id="loadingSpinnerClientes" class="text-center py-5">
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

                    <!-- TAB PRODUCTORES -->
                    <div class="tab-pane fade" id="productores" role="tabpanel">
                        <div id="productoresAlert" class="alert d-none"></div>
                        
                        <!-- Buscador Productores -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input 
                                        type="text" 
                                        id="searchProductor" 
                                        class="form-control" 
                                        placeholder="Buscar por nombre de productor o teléfono...">
                                    <div class="input-group-append">
                                        <button class="btn btn-default" type="button">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de envíos productores -->
                        <div id="loadingSpinnerProductores" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando envíos de productores...</p>
                        </div>
                        
                        <div id="productoresContainer" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Productor</th>
                                            <th>Teléfono</th>
                                            <th>Ruta</th>
                                            <th>Estado</th>
                                            <th>Fecha Entrega</th>
                                            <th class="text-center" style="width: 180px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProductores">
                                        <!-- Se llena dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div id="noProductores" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                            <h5 class="mt-3">No hay documentos disponibles</h5>
                            <p class="mb-0">En estos momentos no hay envíos de productores completados para ver sus documentos.</p>
                            <small class="text-muted">Los documentos aparecerán automáticamente cuando un envío de productor sea marcado como "Entregado".</small>
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
(function () {
    const rawToken = localStorage.getItem('authToken');
    const token = rawToken ? rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) { window.location.href = '/login'; return; }

    const headers = { 
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    };

    // ==================== CLIENTES ====================
    const stateClientes = { clientes: [], clientesFiltrados: [] };
    const loadingSpinnerClientes = document.getElementById('loadingSpinnerClientes');
    const clientesContainer = document.getElementById('clientesContainer');
    const noClientes = document.getElementById('noClientes');
    const tablaClientes = document.getElementById('tablaClientes');
    const searchCliente = document.getElementById('searchCliente');
    const clientesAlert = document.getElementById('clientesAlert');

    function setAlertClientes(message = '', type = 'success') {
        if (!message) {
            clientesAlert.classList.add('d-none');
            clientesAlert.textContent = '';
            return;
        }
        clientesAlert.className = `alert alert-${type}`;
        clientesAlert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
        setTimeout(() => setAlertClientes(), 5000);
    }

    function renderClientes(clientes) {
        if (!clientes || clientes.length === 0) {
            loadingSpinnerClientes.style.display = 'none';
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

        loadingSpinnerClientes.style.display = 'none';
        noClientes.style.display = 'none';
        clientesContainer.style.display = 'block';
    }

    function filtrarClientes() {
        const query = searchCliente.value.toLowerCase().trim();
        if (!query) {
            stateClientes.clientesFiltrados = stateClientes.clientes;
        } else {
            stateClientes.clientesFiltrados = stateClientes.clientes.filter(c => {
                const nombre = `${c.nombre || ''} ${c.apellido || ''}`.toLowerCase();
                const correo = (c.correo || '').toLowerCase();
                return nombre.includes(query) || correo.includes(query);
            });
        }
        renderClientes(stateClientes.clientesFiltrados);
    }

    async function cargarClientes() {
        try {
            setAlertClientes();
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

            stateClientes.clientes = await res.json();
            stateClientes.clientesFiltrados = stateClientes.clientes;
            renderClientes(stateClientes.clientesFiltrados);
        } catch (error) {
            console.error('Error:', error);
            setAlertClientes(error.message, 'danger');
            loadingSpinnerClientes.style.display = 'none';
            noClientes.style.display = 'block';
        }
    }

    searchCliente.addEventListener('input', filtrarClientes);

    // ==================== PRODUCTORES ====================
    const stateProductores = { envios: [], enviosFiltrados: [] };
    const loadingSpinnerProductores = document.getElementById('loadingSpinnerProductores');
    const productoresContainer = document.getElementById('productoresContainer');
    const noProductores = document.getElementById('noProductores');
    const tablaProductores = document.getElementById('tablaProductores');
    const searchProductor = document.getElementById('searchProductor');
    const productoresAlert = document.getElementById('productoresAlert');

    function setAlertProductores(message = '', type = 'success') {
        if (!message) {
            productoresAlert.classList.add('d-none');
            productoresAlert.textContent = '';
            return;
        }
        productoresAlert.className = `alert alert-${type}`;
        productoresAlert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
        setTimeout(() => setAlertProductores(), 5000);
    }

    function formatearFecha(fecha) {
        if (!fecha) return '-';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-BO', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function renderProductores(envios) {
        if (!envios || envios.length === 0) {
            loadingSpinnerProductores.style.display = 'none';
            noProductores.style.display = 'block';
            productoresContainer.style.display = 'none';
            return;
        }

        tablaProductores.innerHTML = envios.map(envio => `
            <tr>
                <td><span class="badge badge-secondary">#${envio.id}</span></td>
                <td>
                    <strong>${envio.nombre_remitente || 'Sin nombre'}</strong>
                    ${envio.email_remitente ? `<br><small class="text-muted">${envio.email_remitente}</small>` : ''}
                </td>
                <td>${envio.telefono_remitente || '-'}</td>
                <td>
                    <small>
                        <i class="fas fa-map-marker-alt text-success"></i> ${envio.nombre_origen}<br>
                        <i class="fas fa-map-marker-alt text-danger"></i> ${envio.nombre_destino}
                    </small>
                </td>
                <td><span class="badge badge-success">${envio.estado}</span></td>
                <td><small>${envio.fecha_entrega ? formatearFecha(envio.fecha_entrega) : 'Sin fecha'}</small></td>
                <td class="text-center">
                    <a href="/admin/documentos/productor/${envio.id}" class="btn btn-sm btn-info" title="Ver documentos del envío">
                        <i class="fas fa-file-alt mr-1"></i>Ver Documentos
                    </a>
                </td>
            </tr>
        `).join('');

        loadingSpinnerProductores.style.display = 'none';
        noProductores.style.display = 'none';
        productoresContainer.style.display = 'block';
    }

    function filtrarProductores() {
        const query = searchProductor.value.toLowerCase().trim();
        if (!query) {
            stateProductores.enviosFiltrados = stateProductores.envios;
        } else {
            stateProductores.enviosFiltrados = stateProductores.envios.filter(e => {
                return (
                    e.nombre_remitente?.toLowerCase().includes(query) ||
                    e.telefono_remitente?.includes(query) ||
                    e.email_remitente?.toLowerCase().includes(query) ||
                    e.id.toString().includes(query)
                );
            });
        }
        renderProductores(stateProductores.enviosFiltrados);
    }

    async function cargarProductores() {
        try {
            setAlertProductores();
            loadingSpinnerProductores.style.display = 'block';
            noProductores.style.display = 'none';
            productoresContainer.style.display = 'none';
            
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos timeout
            
            const res = await fetch(`${window.location.origin}/api/public/envios`, { 
                headers: { 'Accept': 'application/json' },
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!res.ok) {
                const errorData = await res.text();
                console.error('Error response:', errorData);
                throw new Error(`Error ${res.status}: No se pudieron cargar los envíos`);
            }

            const data = await res.json();
            console.log('Envíos productores cargados:', data);
            
            stateProductores.envios = Array.isArray(data) ? data : [];
            stateProductores.enviosFiltrados = stateProductores.envios;
            renderProductores(stateProductores.enviosFiltrados);
        } catch (error) {
            console.error('Error completo:', error);
            if (error.name === 'AbortError') {
                setAlertProductores('La petición tardó demasiado. Por favor, intenta de nuevo.', 'warning');
            } else {
                setAlertProductores(error.message || 'Error al cargar envíos', 'danger');
            }
            loadingSpinnerProductores.style.display = 'none';
            noProductores.style.display = 'block';
        }
    }

    window.verDetallesProductor = function(idEnvio) {
        window.location.href = `/admin/envios/${idEnvio}`;
    };

    searchProductor.addEventListener('input', filtrarProductores);

    // Manejar cambio de tabs
    const productoresTab = document.getElementById('productores-tab');
    if (productoresTab) {
        productoresTab.addEventListener('shown.bs.tab', function (e) {
            console.log('Tab productores activado');
            // Cargar solo si no se ha cargado antes
            if (stateProductores.envios.length === 0 && loadingSpinnerProductores.style.display === 'none') {
                cargarProductores();
            }
        });
        
        // También escuchar el click para asegurar que se carga
        productoresTab.addEventListener('click', function (e) {
            console.log('Click en tab productores');
            setTimeout(() => {
                if (stateProductores.envios.length === 0 && loadingSpinnerProductores.style.display === 'none') {
                    cargarProductores();
                }
            }, 100);
        });
    }

    // Inicializar (cargar solo clientes al inicio)
    cargarClientes();
})();
</script>
@endpush