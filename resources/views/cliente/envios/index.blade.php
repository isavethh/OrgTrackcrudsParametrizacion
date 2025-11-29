@extends('layouts.cliente')

@section('page-title', 'Mis Envíos')

@section('page-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-pills p-2">
                    <li class="nav-item"><a class="nav-link active" href="#tab-curso" data-toggle="tab">Envíos en Curso</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab-anteriores" data-toggle="tab">Envíos anteriores</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab-pendientes" data-toggle="tab">Envíos pendientes</a></li>
                    <li class="nav-item ml-auto pr-2"><a href="{{ route('envios.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo Envío</a></li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-curso"></div>
                    <div class="tab-pane" id="tab-anteriores"></div>
                    <div class="tab-pane" id="tab-pendientes"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
if (!window.__envioIndexClienteInitialized) {
    window.__envioIndexClienteInitialized = true;
    
    // Auth
    const _rawToken = localStorage.getItem('authToken');
    const token = _rawToken ? _rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) { window.location.href = '/login'; }

    const tabCurso = document.getElementById('tab-curso');
    const tabAnteriores = document.getElementById('tab-anteriores');
    const tabPendientes = document.getElementById('tab-pendientes');

    function renderLista(container, envios){
        if (!envios || envios.length === 0){
            container.innerHTML = '<div class="text-muted text-center py-4"><i class="fas fa-inbox fa-2x mb-2"></i><br>Sin envíos</div>';
            return;
        }
        const ul = document.createElement('ul');
        ul.className = 'list-group list-group-flush';
        envios.forEach(e => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action';
            li.style.cursor = 'pointer';
            li.tabIndex = 0;
            li.dataset.href = `{{ url('/envios') }}/${e.id}`;
            
            const badgeMap = {
                'En curso': 'badge-primary',
                'Pendiente': 'badge-warning',
                'Asignado': 'badge-info',
                'Entregado': 'badge-success',
                'Finalizado': 'badge-secondary'
            };
            const badgeClass = badgeMap[e.estado] || 'badge-light';
            
            li.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            <strong>Envío #${e.id}</strong>
                            <span class="badge ${badgeClass} ml-2">${e.estado || 'Sin estado'}</span>
                        </h6>
                        <div class="text-muted small">
                            <div class="mb-1"><i class="fas fa-map-marker-alt text-success mr-1"></i><strong>Origen:</strong> ${e.nombre_origen || '—'}</div>
                            <div><i class="fas fa-map-marker-alt text-danger mr-1"></i><strong>Destino:</strong> ${e.nombre_destino || '—'}</div>
                        </div>
                    </div>
                    <div class="text-right text-muted small">
                        <div><i class="far fa-calendar mr-1"></i>${e.fecha_creacion || '—'}</div>
                    </div>
                </div>`;
            ul.appendChild(li);
        });
        container.innerHTML = '';
        container.appendChild(ul);
    }

    async function cargarEnvios(){
        try{
            const res = await fetch(`${window.location.origin}/api/envios`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (!res.ok){
                if (res.status === 401){ localStorage.removeItem('authToken'); window.location.href = '/login'; return; }
                throw new Error('No se pudieron cargar los envíos');
            }
            const todos = await res.json();
            const enCurso = todos.filter(e => e.estado === 'En curso');
            const pendientes = todos.filter(e => e.estado === 'Pendiente' || e.estado === 'Asignado');
            const anteriores = todos.filter(e => e.estado === 'Entregado' || e.estado === 'Finalizado');
            renderLista(tabCurso, enCurso);
            renderLista(tabPendientes, pendientes);
            renderLista(tabAnteriores, anteriores);
        } catch(e){
            tabCurso.innerHTML = `<div class="text-danger">${e.message}</div>`;
        }
    }
    cargarEnvios();

    document.addEventListener('click', (e) => {
        const item = e.target.closest('.list-group-item[data-href]');
        if (item && item.dataset.href) {
            window.location.href = item.dataset.href;
        }
    });

    document.addEventListener('keydown', (e) => {
        if ((e.key === 'Enter' || e.key === ' ') && e.target.dataset.href) {
            e.preventDefault();
            const href = e.target.dataset.href;
            if (href) window.location.href = href;
        }
    });
    
} // Fin de window.__envioIndexClienteInitialized
</script>
@endpush