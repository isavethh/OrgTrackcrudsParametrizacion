@extends('layouts.adminlte')

@section('page-title', 'Direcciones Guardadas')

@section('page-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de direcciones</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="searchInput" class="form-control float-right" placeholder="Buscar dirección...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default" id="searchBtn"><i class="fas fa-search"></i></button>
                            <a href="{{ route('admin.direcciones.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nueva dirección
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->

            <div class="card-body p-0">
                <div id="loading" class="text-center p-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando direcciones...</p>
                </div>
                <div id="error-message" class="alert alert-danger d-none m-3"></div>
                <ul id="direcciones-list" class="list-group list-group-flush" style="display: none;">
                    <!-- Las direcciones se cargarán aquí dinámicamente -->
                </ul>
                <div id="empty-message" class="text-center p-4" style="display: none;">
                    <p class="text-muted">No hay direcciones guardadas. <a href="{{ route('admin.direcciones.create') }}">Crear una nueva dirección</a></p>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('authToken');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const direccionesList = document.getElementById('direcciones-list');
    const loadingDiv = document.getElementById('loading');
    const errorMessage = document.getElementById('error-message');
    const emptyMessage = document.getElementById('empty-message');
    const searchInput = document.getElementById('searchInput');
    let allDirecciones = [];

    // Cargar direcciones
    async function loadDirecciones() {
        loadingDiv.style.display = 'block';
        direccionesList.style.display = 'none';
        errorMessage.classList.add('d-none');
        emptyMessage.style.display = 'none';

        try {
            const response = await fetch(`${window.location.origin}/api/ubicaciones`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    localStorage.removeItem('authToken');
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Error al cargar direcciones');
            }

            allDirecciones = await response.json();
            displayDirecciones(allDirecciones);
        } catch (error) {
            errorMessage.textContent = 'Error al cargar las direcciones: ' + error.message;
            errorMessage.classList.remove('d-none');
        } finally {
            loadingDiv.style.display = 'none';
        }
    }

    // Mostrar direcciones
    function displayDirecciones(direcciones) {
        direccionesList.innerHTML = '';

        if (direcciones.length === 0) {
            emptyMessage.style.display = 'block';
            return;
        }

        direccionesList.style.display = 'block';
        direcciones.forEach(dir => {
            const origen = dir.nombreorigen || 'Sin nombre';
            const destino = dir.nombredestino || 'Sin nombre';
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex align-items-center justify-content-between';
            li.innerHTML = `
                <div class="mr-2 flex-grow-1">
                    <div class="font-weight-bold">${origen} → ${destino}</div>
                    <small class="text-muted">
                        ${dir.origen_lat && dir.origen_lng ? `Origen: ${parseFloat(dir.origen_lat).toFixed(4)}, ${parseFloat(dir.origen_lng).toFixed(4)}` : 'Sin coordenadas'} | 
                        ${dir.destino_lat && dir.destino_lng ? `Destino: ${parseFloat(dir.destino_lat).toFixed(4)}, ${parseFloat(dir.destino_lng).toFixed(4)}` : 'Sin coordenadas'}
                    </small>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${dir.id}" data-origen="${origen}" data-destino="${destino}">
                        <i class="fas fa-pen mr-1"></i>Editar
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${dir.id}">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </div>
            `;
            direccionesList.appendChild(li);
        });

        // Event listeners para botones
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                window.location.href = `/admin/direcciones/${id}/edit`;
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                if (confirm('¿Estás seguro de que deseas eliminar esta dirección?')) {
                    await deleteDireccion(id);
                }
            });
        });
    }

    // Eliminar dirección
    async function deleteDireccion(id) {
        try {
            const response = await fetch(`${window.location.origin}/api/ubicaciones/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                alert(data.error || 'Error al eliminar la dirección');
                return;
            }

            await loadDirecciones();
        } catch (error) {
            alert('Error al eliminar la dirección: ' + error.message);
        }
    }


    // Búsqueda
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filtered = allDirecciones.filter(dir => {
            const origen = (dir.nombreorigen || '').toLowerCase();
            const destino = (dir.nombredestino || '').toLowerCase();
            return origen.includes(searchTerm) || destino.includes(searchTerm);
        });
        displayDirecciones(filtered);
    });

    document.getElementById('searchBtn').addEventListener('click', () => {
        searchInput.dispatchEvent(new Event('input'));
    });

    // Cargar direcciones al iniciar
    loadDirecciones();
});
</script>
@endpush