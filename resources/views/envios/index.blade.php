@extends('layouts.app')

@section('title', 'Envíos - OrgTrack')
@section('page-title', 'Envíos')

@section('breadcrumb')
    <li class="breadcrumb-item active">Envíos</li>
@endsection

@section('content')
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

@section('scripts')
<script>
    // Auth
    const _rawToken = localStorage.getItem('authToken');
    const token = _rawToken ? _rawToken.replace(/^"+|"+$/g, '') : null;
    if (!token) { window.location.href = '/login'; }

    const tabCurso = document.getElementById('tab-curso');
    const tabAnteriores = document.getElementById('tab-anteriores');
    const tabPendientes = document.getElementById('tab-pendientes');

    function badgeFor(estado){
        const map = { 'En curso':'badge-info', 'Pendiente':'badge-warning', 'Asignado':'badge-primary', 'Entregado':'badge-success', 'Finalizado':'badge-secondary' };
        const cls = map[estado] || 'badge-light';
        return `<span class="badge ${cls} ml-2">${estado}</span>`;
    }

    function renderLista(container, envios){
        if (!envios || envios.length === 0){
            container.innerHTML = '<div class="text-muted">Sin envíos</div>';
            return;
        }
        const ul = document.createElement('ul');
        ul.className = 'list-group';
        envios.forEach(e => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action envio-card';
            li.tabIndex = 0;
            li.dataset.href = `{{ url('/envios') }}/${e.id}`;
            li.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>ID: ${e.id}</strong>
                        ${badgeFor(e.estado)}
                        <div class="mt-2 small"><strong>Origen:</strong> ${e.nombre_origen || '—'}</div>
                        <div class="small"><strong>Destino:</strong> ${e.nombre_destino || '—'}</div>
                    </div>
                    <div class="text-right small text-muted">
                        <div><strong>Creado:</strong> ${e.fecha_creacion || '—'}</div>
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
        const card = e.target.closest('.envio-card');
        if (card && card.dataset.href) {
            window.location.href = card.dataset.href;
        }
    });

    document.addEventListener('keydown', (e) => {
        if ((e.key === 'Enter' || e.key === ' ') && e.target.classList.contains('envio-card')) {
            e.preventDefault();
            const href = e.target.dataset.href;
            if (href) window.location.href = href;
        }
    });
</script>
@endsection
