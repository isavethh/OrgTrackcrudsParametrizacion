<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-header">MIS ENVÍOS</li>
        <li class="nav-item">
            <a href="{{ route('envios.create') }}" class="nav-link {{ request()->routeIs('envios.create') ? 'active' : '' }}">
                <i class="nav-icon fas fa-plus"></i>
                <p>Nuevo Envío</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('envios.index') }}" class="nav-link {{ request()->routeIs('envios.*') && !request()->routeIs('envios.create') ? 'active' : '' }}">
                <i class="nav-icon fas fa-shipping-fast"></i>
                <p>Mis Envíos</p>
            </a>
        </li>
        <li class="nav-header">MIS RECURSOS</li>
        <li class="nav-item">
            <a href="{{ route('direcciones.index') }}" class="nav-link {{ request()->routeIs('direcciones.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-marker-alt"></i>
                <p>Direcciones Guardadas</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('documentos.index') }}" class="nav-link {{ request()->routeIs('documentos.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Documentos</p>
            </a>
        </li>
    </ul>
</nav>

